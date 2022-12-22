<?php
if(!isset($_SESSION)) 
{ 
  session_start(); 
} 
include('php/db_read_replica.php');
include('php/templates.php');

if( $_SESSION['user']['status'] == 'logged_in' && !empty($_SESSION['user']['email']) ){
	$mode = 1;
} else {
	//var_dump($_SESSION);
	header('Location: /'.$base.'index.php');
	exit();
}

$sql = 'SELECT

A.account_name, A.install_date, A.expires, R.Currency,

SUM(CASE WHEN Day < F.install_date AND DATEDIFF(F.install_date, Day) < 60 THEN `Amount spent` ELSE 0 END) AS `Pre - Total Spend`,
SUM(CASE WHEN Day > F.install_date THEN `Amount spent` ELSE 0 END ) AS `Post - Total Spend`,

SUM(CASE WHEN Day < F.install_date AND DATEDIFF(F.install_date, Day) < 60 THEN `Amount spent` ELSE 0 END) /
(DATEDIFF(
MAX(CASE WHEN Day < F.install_date AND DATEDIFF(F.install_date, Day) < 60 THEN `Day` ELSE NULL END),
MIN(CASE WHEN Day < F.install_date AND DATEDIFF(F.install_date, Day) < 60 THEN `Day` ELSE NULL END)
) + 1 )
AS `Pre - Spend Per Day`,
SUM(CASE WHEN Day > F.install_date THEN `Amount spent` ELSE 0 END ) /
(DATEDIFF(
MAX(CASE WHEN Day > F.install_date THEN `Day` ELSE NULL END),
MIN(CASE WHEN Day > F.install_date THEN `Day` ELSE NULL END)
) + 1 )
AS `Post - Spend Per Day`,

SUM(CASE WHEN Day < F.install_date AND DATEDIFF(F.install_date, Day) < 60 THEN `Purchases` ELSE 0 END) AS `Pre - Purchases`,
SUM(CASE WHEN Day > F.install_date THEN `Purchases` ELSE 0 END ) AS `Post - Purchases`,

SUM(CASE WHEN Day < F.install_date AND DATEDIFF(F.install_date, Day) < 60 THEN `Purchases conversion value` ELSE 0 END) AS `Pre - Revenue`,
SUM(CASE WHEN Day > F.install_date THEN `Purchases conversion value` ELSE 0 END ) AS `Post - Revenue`,

SUM(CASE WHEN Day < F.install_date AND DATEDIFF(F.install_date, Day) < 60 THEN `Amount spent` ELSE 0 END) / SUM(CASE WHEN Day < F.install_date AND DATEDIFF(F.install_date, Day) < 60 THEN `Purchases` ELSE 0 END) AS `Pre - CPA`,
SUM(CASE WHEN Day > F.install_date THEN `Amount spent` ELSE 0 END) / SUM(CASE WHEN Day > F.install_date THEN `Purchases` ELSE 0 END ) AS `Post - CPA`,

SUM(CASE WHEN Day < F.install_date AND DATEDIFF(F.install_date, Day) < 60 THEN `Purchases conversion value` ELSE 0 END) / SUM(CASE WHEN Day < F.install_date AND DATEDIFF(F.install_date, Day) < 60 THEN `Amount spent` ELSE 0 END) AS `Pre - ROAS`,
SUM(CASE WHEN Day > F.install_date THEN `Purchases conversion value` ELSE 0 END) / SUM(CASE WHEN Day > F.install_date THEN `Amount spent` ELSE 0 END) AS `Post - ROAS`,

CONCAT( MIN(CASE WHEN Day < F.install_date AND DATEDIFF(F.install_date, Day) < 60 THEN `Day` ELSE NULL END), " to ", MAX(CASE WHEN Day < F.install_date AND DATEDIFF(F.install_date, Day) < 60 THEN `Day` ELSE NULL END) ) AS `Pre - Date Range`,
CONCAT( MIN(CASE WHEN Day > F.install_date THEN `Day` ELSE NULL END), " to ", MAX(CASE WHEN Day > F.install_date THEN `Day` ELSE NULL END) ) AS `Post - Date Range`

FROM fb_settings F LEFT JOIN `fb_spend_revenue` R ON R.`Account ID` IN (F.fb_ad_account_id)  LEFT JOIN accounts A ON A.sha_token = F.sha_token WHERE A.sha_token = "'.$_SESSION['user']['id'].'" AND F.fb_ad_account_id IS NOT NULL

GROUP BY A.account_name;';
$db_data = sql_select_to_array_replica($sql);
$sql = 'SELECT

DATE_ADD(Day, INTERVAL + (6-((WEEKDAY(Day)+1) %7)) DAY) AS `Week Ending`, R.`Account ID` AS `Account ID`,

SUM(`Amount spent`) AS `Total Spend`,

SUM(`Amount spent`) /
(DATEDIFF(
MAX(`Day`),
MIN(`Day`)
) + 1 )
AS `Spend Per Day`,

SUM(`Purchases`) AS `Purchases`,

SUM(`Purchases conversion value`) AS `Revenue`,

SUM(`Amount spent`) / SUM(`Purchases`) AS `CPA`,

SUM(`Purchases conversion value`) / SUM(`Amount spent`) AS `ROAS`,

CONCAT( MIN(`Day`), " to ", MAX(`Day`) ) AS `Date Range`

FROM fb_settings F LEFT JOIN `fb_spend_revenue` R ON R.`Account ID` IN (F.fb_ad_account_id) LEFT JOIN accounts A ON A.sha_token = F.sha_token

WHERE A.sha_token = "'.$_SESSION['user']['id'].'"
GROUP BY DATE_ADD(Day, INTERVAL + (6-((WEEKDAY(Day)+1) %7)) DAY)
ORDER BY DATE_ADD(Day, INTERVAL + (6-((WEEKDAY(Day)+1) %7)) DAY) ASC
;';
$db_data_weekly = sql_select_to_array_replica($sql);

$sql = 'SELECT fb_ad_account_id FROM popsixle.fb_ad_account_id_map WHERE sha_token = "'.$_SESSION['user']['id'].'";';
$account_ids = sql_select_to_array_replica($sql);

$fb_account_ids = '';
$loop_count = 0;
foreach ($account_ids as $fb_account_value) {
	if($loop_count == 0){
		$fb_account_ids .= $fb_account_value["fb_ad_account_id"];
		$loop_count++;
	} else {
		$fb_account_ids .= ', '.$fb_account_value["fb_ad_account_id"];
	}
};

$sql = 'SELECT `Day` FROM `fb_spend_revenue` WHERE `Account ID` IN ('.$fb_account_ids.') AND DATEDIFF(CURDATE(), Day) < 32 GROUP BY Day;';
$db_data_daily_check = sql_select_to_array_replica($sql);

// var_dump($db_data_daily_check);

if (!empty($db_data[0]['install_date'])) {
	if ( strtotime($db_data[0]['expires']) + 86400 < time()){
		$shop_install_days = "Expired";
	} else {
		$shop_install_date = strtotime($db_data[0]['install_date']) + 86400;
		$shop_datediff = time() - $shop_install_date;
		$shop_install_days = round($shop_datediff / (60 * 60 * 24));
		if ($shop_install_days <= 0){
			$shop_install_days = 0;
		}
	}
}

$date_array = array();
for ($x = -30; $x < 0; $x++){
	array_push($date_array, date("Y-m-d",strtotime($x." day")));
};

?>
<html>
<head>

	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
	<style>
.x-close{
	position: absolute;
    right: 0;
    top: 0px;
    padding: 1 7px;
}

.x-close:hover{
	cursor: pointer;
	background: #7a0000;
}

pre.snippet {
    background-color: #eaeded;
    padding: 8px 16px;
    border-radius: 8px;
    overflow-x: auto;
    -webkit-user-select: all;
    -moz-user-select: all;
    -ms-user-select: all;
    user-select: all;
}

#log_out{
	position: fixed;
	right:5px;
	top: 5px;
	padding: 5px 10px;
	font-size:small;
	z-index: 10 !important;
	background:#FFFFFF;
}

.too-small-hide{
	display: none;
}

.order-inactive span {
		visibility:hidden;
}
.order-inactive:hover span {
		visibility:visible;
}
.order-active span {
		visibility: visible;
}
 @media (min-width: 769px) {
	.too-small-hide{
		display: block;
	}	 
}
.getfile1, .getfile1:hover, .getfile1:active, .getfile1:visited, .getfile1:focus, .getfile1:link {
	text-decoration: none;
	color: white;
}
.getfile2, .getfile2:hover, .getfile2:active, .getfile2:visited, .getfile2:focus, .getfile2:link {
	text-decoration: none;
	color: white;
}
a {
	color: #AC39FD;
}

.cust-btn {
	background: #AC39FD;
	color: white;
	font-weight: 600;
}
.cust-btn:hover {
	background: #761ab8;
	color: white;
}
.scroll {
	overflow-x: auto;
}
.button-dl{
	width: 125px;
	padding: 0;
	margin: 0 0 8px 24px;
}
.button-dl2{
	width: 125px;
	padding: 0;
	margin: 0 0 8px 0px;
}
.cust-btn {
	background: #AC39FD;
	color: white;
	font-weight: 600;
}
.cust-btn:hover {
	background: #761ab8;
	color: white;
}
/* 
.first-column {
	position: absolute;
	background: white;
} */
.styled-table {
    border-collapse: collapse;
    margin: 25px 0;
    font-size: 0.9em;
    font-family: sans-serif;
    min-width: 300px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
}


.styled-table th,
.styled-table td {
    padding: 7px 7px;
}
.styled-table tbody tr {
    border-bottom: 1px solid #d5bfdd;
}

.styled-table tbody tr:nth-of-type(even) {
    background-color: #f3f3f3;
}

.styled-table tbody tr:last-of-type {
    border-bottom: 2px solid #9a20f0;
}
.styled-table tbody tr.active-row {
    font-weight: bold;
    color: black;
}
.table-head td, .table-head{
	background-color: #616161;
	color: #ffffff;
	text-align: center;
}

/* table {
  border-collapse: separate;
  border-spacing: 0;
  border-top: 1px solid grey;
} */
.table2_container {
	overflow-x: auto;
}
.date_true {
	/* border: solid 1px green; */
	border-radius: 50%;
	background: green;
}
.date_false {
	/* border: solid 1px red; */
	border-radius: 50%;
	background: red;
}

	</style>
	<link rel="apple-touch-icon" sizes="180x180" href="/<?php echo $base;?>favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/<?php echo $base;?>favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/<?php echo $base;?>favicon/favicon-16x16.png">
	<link rel="manifest" href="/<?php echo $base;?>favicon/site.webmanifest">
	<link rel="mask-icon" href="/<?php echo $base;?>favicon/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#ffffff">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Pop6 Admin Pre/Post Report</title>
</head>
<body class="m-3" style="background: #FFFFFF;">
	<a href="/<?php echo $base;?>index.php"><img src="/<?php echo $base;?>img/popsixle_logo2.png" width="205" height="52" style="margin:0 0 10px;" alt="Popsixle Logo" border="0" /></a>
		<!-- <p class="pt-2" style="margin-left:2px;"><b>Back to the good old days.</b></p> -->

	
	
	<?php
	$formatter = new NumberFormatter('en_US', NumberFormatter::PERCENT);
	if( !empty($_SESSION['user']['email']) &&  $_SESSION['user']['type'] == '99'){
		echo '<div id="log_out">'.$_SESSION['user']['username'].' (Admin) | <a href="/'.$base.'php/logout.php">logout</a></div>';
	}	else if( !empty($_SESSION['user']['email']) ){
		echo '<div id="log_out">'.$_SESSION['user']['username'].' | <a href="/'.$base.'php/logout.php">logout</a></div>';
	}	
	?>
			<div class="p-3 mb-2 card base-card border text-white bg-danger" id="error_block" style="display:none;">
			</div>
			
			<div class="p-3 mb-2  card base-card border text-white bg-success" id="success_block" style="display:none;">
			</div>

			<?php echo navbar();?>

      <div class="card bg-light mb-2">
        <div class="p-4">
					<?php
						$install_date = date_create($db_data[0]["install_date"]);
						echo '<h2 style="color:#AC39FD">'.$db_data[0]["account_name"].'</h2>';
						echo '<h5 style="color:#AC39FD">Popsixle Launch Date: '.date_format($install_date, "m/d/Y").'</h5>';
						echo '<h5 class="my-0  pb-3" style="color:#AC39FD">Days live: '.$shop_install_days.'</h5>';
					?>
				</div>
			</div>
      <div class="card bg-light mb-2">
        <div class="p-4">
          <div class="">
						<?php
						if (count($db_data[0]) < 1) {
							echo '<h1>There is No Report Data</h1>';
						} else {
							$pre_date_explode = explode(" ", $db_data[0]["Pre - Date Range"]);
							$pre1 = date_create($pre_date_explode[0]);
							$pre2 = date_create($pre_date_explode[2]);
							$pre_date_diff = strtotime("$pre_date_explode[2]") - strtotime("$pre_date_explode[0]");
							$pre_date_diff = round($pre_date_diff / (60 * 60 * 24));
							$post_date_explode = explode(" ", $db_data[0]["Post - Date Range"]);
							$post1 = date_create($post_date_explode[0]);
							$post2 = date_create($post_date_explode[2]);
							$post_date_diff = strtotime("$post_date_explode[2]") - strtotime("$post_date_explode[0]");
							$post_date_diff = round($post_date_diff / (60 * 60 * 24));

							echo '<h2 class="order"style="color:#AC39FD">Overall Performance</h2>';
							echo '<table class="styled-table" id="thetable1">';
								echo '<tr class="table-head">';
									echo '<th class="order"style="text-align:center;min-width:160px;background-color:#616161;"></td>';
									echo '<th class="order"style="text-align:center;min-width:245px;background-color:#616161;"><span style="font-size: 0.8rem; margin-right: 0.5rem;"> </span>Pre </br><span style="font-size: 0.8rem; margin-right: 0.5rem;">&nbsp&nbsp </span>'.date_format($pre1, "m/d/Y").' to '.date_format($pre2, "m/d/Y").'</td>';
									echo '<th class="order"style="text-align:center;min-width:245px;background-color:#9a20f0;"><span style="font-size: 0.8rem; margin-right: 0.5rem;"> </span>Post </br><span style="font-size: 0.8rem; margin-right: 0.5rem;">&nbsp&nbsp </span>'.date_format($post1, "m/d/Y").' to '.date_format($post2, "m/d/Y").'</td>';
									echo '<th class="order"style="text-align:center;background-color:#616161;">Delta</td>';
								echo '</tr>';	

								$currency_symbol = '$';
								if ( !empty($db_data[0]['Currency'])){
									if ($db_data[0]['Currency'] == 'GBP'){
										$currency_symbol = '£';
									} else if ($db_data[0]['Currency'] == 'USD'){
										$currency_symbol = '$';
									} else if ($db_data[0]['Currency'] == 'CAD'){
										$currency_symbol = 'C$';
									} else if ($db_data[0]['Currency'] == 'SEK'){
										$currency_symbol = 'kr';
									} else if ($db_data[0]['Currency'] == 'NZD'){
										$currency_symbol = 'NZ$';
									} else if ($db_data[0]['Currency'] == 'DKK'){
										$currency_symbol = 'Kr.';
									} else if ($db_data[0]['Currency'] == 'KRW'){
										$currency_symbol = '₩';
									} else if ($db_data[0]['Currency'] == 'SDG'){
										$currency_symbol = 'ج.س.';
									} else if ($db_data[0]['Currency'] == 'MAD'){
										$currency_symbol = 'MAD';
									} else if ($db_data[0]['Currency'] == 'PLN'){
										$currency_symbol = 'zł';
									} else if ($db_data[0]['Currency'] == 'CHF'){
										$currency_symbol = 'Fr.';
									} else if ($db_data[0]['Currency'] == 'AED'){
										$currency_symbol = 'د.إ';
									} else if ($db_data[0]['Currency'] == 'AUS' || $db_data[0]['Currency'] == 'AUD'){
										$currency_symbol = 'A$';
									} else if ($db_data[0]['Currency'] == 'JPY'){
										$currency_symbol = '¥';
									} else if ($db_data[0]['Currency'] == 'MXN'){
										$currency_symbol = '₱';
									} else if ($db_data[0]['Currency'] == 'HKD'){
										$currency_symbol = '元';
									} else if ($db_data[0]['Currency'] == 'INR'){
										$currency_symbol = '₹';
									} else if ($db_data[0]['Currency'] == 'SGD'){
										$currency_symbol = 'S$';
									} else if ($db_data[0]['Currency'] == 'MYR'){
										$currency_symbol = 'RM';
									}
								}
							

							// var_dump ($db_data[0]);
							$roas_color_green = 255;
							$roas_color_red = 255;
							$roas_color_blue = 255;
							$cpa_color_green = 255;
							$cpa_color_red = 255;
							$cpa_color_blue = 255;
							$spend_color_green = 255;
							$spend_color_red = 255;
							$spend_color_blue = 255;
							if ($formatter->format( $db_data[0]['Post - ROAS']/$db_data[0]['Pre - ROAS'] - 1, 0 ) != "-NaN%"  && $formatter->format( $db_data[0]['Post - ROAS']/$db_data[0]['Pre - ROAS'] - 1, 0 ) != "∞%"){
								$delta_roas = $formatter->format( $db_data[0]['Post - ROAS']/$db_data[0]['Pre - ROAS'] - 1, 0);
								if (number_format($db_data[0]['Post - ROAS']/$db_data[0]['Pre - ROAS'] - 1, 2) < 0) {
									if (number_format($db_data[0]['Post - ROAS']/$db_data[0]['Pre - ROAS'] - 1, 2) <= -1.00){
										$roas_color_red = 255;
										$roas_color_blue = 0;
										$roas_color_green = 0;
									} else {
										$roas_color_blue = (number_format($db_data[0]['Post - ROAS']/$db_data[0]['Pre - ROAS'] - 1, 2) + 1) * 255;
										$roas_color_green = (number_format($db_data[0]['Post - ROAS']/$db_data[0]['Pre - ROAS'] - 1, 2) + 1) * 255;
									}
								} else if (number_format($db_data[0]['Post - ROAS']/$db_data[0]['Pre - ROAS'] - 1, 2) > 0) {
									if (number_format($db_data[0]['Post - ROAS']/$db_data[0]['Pre - ROAS'] - 1, 2) >= 1.00){
										$roas_color_green = 255;
										$roas_color_red = 0;
										$roas_color_blue = 0;
									} else {
										$roas_color_blue = number_format((1 - ($db_data[0]['Post - ROAS']/$db_data[0]['Pre - ROAS'] - 1)) * 255, 0);
										$roas_color_red = number_format((1 - ($db_data[0]['Post - ROAS']/$db_data[0]['Pre - ROAS'] - 1)) * 255, 0);
										}
								}
							} else {
								$delta_roas = "";
							}
							if ($formatter->format( $db_data[0]['Post - CPA']/$db_data[0]['Pre - CPA'] - 1, 0 ) != "-NaN%"  && $formatter->format( $db_data[0]['Post - CPA']/$db_data[0]['Pre - CPA'] - 1, 0 ) != "∞%"){
								$delta_cpa = $formatter->format( $db_data[0]['Post - CPA']/$db_data[0]['Pre - CPA'] - 1, 0 );
								if (number_format($db_data[0]['Post - CPA']/$db_data[0]['Pre - CPA'] - 1, 2) < 0) {
									if (number_format($db_data[0]['Post - CPA']/$db_data[0]['Pre - CPA'] - 1, 2) <= -1.00){
										$cpa_color_red = 0;
										$cpa_color_blue = 0;
										$cpa_color_green = 255;
									} else {
										$cpa_color_blue = (number_format($db_data[0]['Post - CPA']/$db_data[0]['Pre - CPA'] - 1, 2) + 1) * 255;
										$cpa_color_red = (number_format($db_data[0]['Post - CPA']/$db_data[0]['Pre - CPA'] - 1, 2) + 1) * 255;
									}
								} else if (number_format($db_data[0]['Post - CPA']/$db_data[0]['Pre - CPA'] - 1, 2) > 0) {
									if (number_format($db_data[0]['Post - CPA']/$db_data[0]['Pre - CPA'] - 1, 2) >= 1.00){
										$cpa_color_green = 0;
										$cpa_color_red = 255;
										$cpa_color_blue = 0;
									} else {
										$cpa_color_blue = number_format((1 - ($db_data[0]['Post - CPA']/$db_data[0]['Pre - CPA'] - 1)) * 255, 0);
										$cpa_color_green = number_format((1 - ($db_data[0]['Post - CPA']/$db_data[0]['Pre - CPA'] - 1)) * 255, 0);
										}
								}
							} else {
								$delta_cpa = "";
							}
							if ($formatter->format( $db_data[0]['Post - Spend Per Day']/$db_data[0]['Pre - Spend Per Day'] - 1, 0 ) != "-NaN%"  && $formatter->format( $db_data[0]['Post - Spend Per Day']/$db_data[0]['Pre - Spend Per Day'] - 1, 0 ) != "∞%"){
								$delta_spend = $formatter->format( $db_data[0]['Post - Spend Per Day']/$db_data[0]['Pre - Spend Per Day'] - 1, 0 );
								if (number_format($db_data[0]['Post - Spend Per Day']/$db_data[0]['Pre - Spend Per Day'] - 1, 2) < 0) {
									if (number_format($db_data[0]['Post - Spend Per Day']/$db_data[0]['Pre - Spend Per Day'] - 1, 2) <= -1.00){
										$spend_color_red = 255;
										$spend_color_blue = 0;
										$spend_color_green = 0;
									} else {
										$spend_color_blue = (number_format($db_data[0]['Post - Spend Per Day']/$db_data[0]['Pre - Spend Per Day'] - 1, 2) + 1) * 255;
										$spend_color_green = (number_format($db_data[0]['Post - Spend Per Day']/$db_data[0]['Pre - Spend Per Day'] - 1, 2) + 1) * 255;
									}
								} else if (number_format($db_data[0]['Post - Spend Per Day']/$db_data[0]['Pre - Spend Per Day'] - 1, 2) > 0) {
									if (number_format($db_data[0]['Post - Spend Per Day']/$db_data[0]['Pre - Spend Per Day'] - 1, 2) >= 1.00){
										$spend_color_green = 255;
										$spend_color_red = 0;
										$spend_color_blue = 0;
									} else {
										$spend_color_blue = number_format((1 - ($db_data[0]['Post - Spend Per Day']/$db_data[0]['Pre - Spend Per Day'] - 1)) * 255, 0);
										$spend_color_red = number_format((1 - ($db_data[0]['Post - Spend Per Day']/$db_data[0]['Pre - Spend Per Day'] - 1)) * 255, 0);
										}
								}

							} else {
								$delta_spend = "";
							}

							echo '<tbody style="text-align:center;">';
								echo '<tr style="text-align:center;">';
									echo '<td style="text-align:center;">Spend</td>';
									echo '<td style="text-align:center;">'.$currency_symbol.''.number_format($db_data[0]["Pre - Total Spend"], 0).'</td>';
									echo '<td style="text-align:center;">'.$currency_symbol.''.number_format($db_data[0]["Post - Total Spend"], 0).'</td>';
									echo '<td style="text-align:center;"></td>';
								echo '</tr>';
								echo '<tr style="text-align:center;">';
									echo '<td style="text-align:center;">Spend Per Day</td>';
									echo '<td style="text-align:center;">'.$currency_symbol.''.number_format($db_data[0]["Pre - Spend Per Day"], 0).'</td>';
									echo '<td style="text-align:center;">'.$currency_symbol.''.number_format($db_data[0]["Post - Spend Per Day"], 0).'</td>';
									echo '<td style="text-align:center;background-color:rgb('.$spend_color_red.', '.$spend_color_green.', '.$spend_color_blue.')">'.$delta_spend.'</td>';
								echo '</tr>';
								echo '<tr style="text-align:center;">';
									echo '<td style="text-align:center;">Purchases</td>';
									echo '<td style="text-align:center;">'.number_format($db_data[0]["Pre - Purchases"], 0).'</td>';
									echo '<td style="text-align:center;">'.number_format($db_data[0]["Post - Purchases"], 0).'</td>';
									echo '<td style="text-align:center;"></td>';
								echo '</tr>';
								echo '<tr style="text-align:center;">';
									echo '<td style="text-align:center;">Revenue</td>';
									echo '<td style="text-align:center;">'.$currency_symbol.''.number_format($db_data[0]["Pre - Revenue"], 0).'</td>';
									echo '<td style="text-align:center;">'.$currency_symbol.''.number_format($db_data[0]["Post - Revenue"], 0).'</td>';
									echo '<td style="text-align:center;"></td>';
								echo '</tr>';
								echo '<tr style="text-align:center;">';
									echo '<td style="text-align:center;">Cost Per Purchase</td>';
									echo '<td style="text-align:center;">'.$currency_symbol.''.number_format($db_data[0]["Pre - CPA"], 0).'</td>';
									echo '<td style="text-align:center;">'.$currency_symbol.''.number_format($db_data[0]["Post - CPA"], 0).'</td>';
									echo '<td style="text-align:center;background-color:rgb('.$cpa_color_red.', '.$cpa_color_green.', '.$cpa_color_blue.')">'.$delta_cpa.'</td>';
								echo '</tr>';
								echo '<tr style="text-align:center;">';
									echo '<td style="text-align:center;">ROAS</td>';
									echo '<td style="text-align:center;">'.number_format($db_data[0]["Pre - ROAS"], 2).'</td>';
									echo '<td style="text-align:center;">'.number_format($db_data[0]["Post - ROAS"], 2).'</td>';
									echo '<td style="text-align:center;background-color:rgb('.$roas_color_red.', '.$roas_color_green.', '.$roas_color_blue.')">'.$delta_roas.'</td>';
								echo '</tr>';

							echo '</tbody>';
							echo '</table>';
							echo '</br>';
							echo '<button class="button-dl2 btn cust-btn text-center"><a class="getfile1 ">Download CSV</a></button>';
						};
						?>
					</div>
				</div>
			</div>	
			<div class="card bg-light scroll mb-2">
        <div class="p-4">
          <div class="">		
					<?php 
						$week_ending = "";
						if (count($db_data_weekly) < 1) {
							echo '<h1>No Data</h1>';
						} else {
							
							echo '<h2 class="order"style="text-align:left;color:#AC39FD">Weekly Performance</h2>';
							echo '<div class="table2_container">';
							echo '<table id="thetable2" class="styled-table">';
								echo '<tr class="table-head">';
								echo '<th class="order"style="text-align:center;min-width:182px">Date</th>';
								$column_count = 0;
								foreach( $db_data_weekly as $column ){
									$column_count = $column_count + 1;
									$week_ending = date_create($column["Week Ending"]);
									if ( round((strtotime("$post_date_explode[0]") - strtotime($column["Week Ending"]))/ (60 * 60 * 24)) > 60 ){
									} else {
										if ($post1 > date_create($column["Week Ending"])){
											echo '<th class="order"style="text-align:center;min-width:150px;padding-left:20px">'.date_format($week_ending, "m/d/Y").'</td>';
										} else {
											if (count($db_data_weekly) == $column_count) {
												echo '<th class="order"style="text-align:center;min-width:150px;padding-left:20px;background-color:#9a20f0;">'.date_format($week_ending, "m/d/Y").' (partial week)</td>';
											} else {
												echo '<th class="order"style="text-align:center;min-width:150px;padding-left:20px;background-color:#9a20f0;">'.date_format($week_ending, "m/d/Y").'</td>';
											}
										}
									};
								}
								echo '</tr>';	
							$roas_color_green = 255;
							$roas_color_red = 255;
							$roas_color_blue = 255;
							$cpa_color_green = 255;
							$cpa_color_red = 255;
							$cpa_color_blue = 255;
							$spend_color_green = 255;
							$spend_color_red = 255;
							$spend_color_blue = 255;

							echo '<tbody style="text-align:center;">';
								echo '<tr style="text-align:center;" >';
									echo '<td style="text-align:center;min-width:182px"class="first-column">Spend</td>';
									foreach( $db_data_weekly as $column ){
										if ( round((strtotime("$post_date_explode[0]") - strtotime($column["Week Ending"]))/ (60 * 60 * 24)) > 60 ){
										} else {
											echo '<td style="text-align:center;">'.$currency_symbol.''.number_format($column["Total Spend"], 0).'</td>';
										}
									};
								echo '</tr>';
								echo '<tr style="text-align:center;">';
									echo '<td style="text-align:center;min-width:182px"class="first-column">Spend Per Day</td>';
									foreach( $db_data_weekly as $column ){
										if ( round((strtotime("$post_date_explode[0]") - strtotime($column["Week Ending"]))/ (60 * 60 * 24)) > 60 ){
										} else {
											echo '<td style="text-align:center;">'.$currency_symbol.''.number_format($column["Spend Per Day"], 0).'</td>';
										}
									};
								echo '</tr>';
								echo '<tr style="text-align:center;">';
									echo '<td style="text-align:center;min-width:182px"class="first-column">Purchases</td>';
									foreach( $db_data_weekly as $column ){
										if ( round((strtotime("$post_date_explode[0]") - strtotime($column["Week Ending"]))/ (60 * 60 * 24)) > 60 ){
										} else {
											echo '<td style="text-align:center;">'.number_format($column["Purchases"], 0).'</td>';
										}
									};
								echo '</tr>';
								echo '<tr style="text-align:center;">';
									echo '<td style="text-align:center;min-width:182px"class="first-column">Revenue</td>';
									foreach( $db_data_weekly as $column ){
										if ( round((strtotime("$post_date_explode[0]") - strtotime($column["Week Ending"]))/ (60 * 60 * 24)) > 60 ){
										} else {
											echo '<td style="text-align:center;">'.$currency_symbol.''.number_format($column["Revenue"], 0).'</td>';
										}
									};
								echo '</tr>';
								echo '<tr style="text-align:center;">';
									echo '<td style="text-align:center;min-width:182px"class="first-column">Cost Per Purchase</td>';
									foreach( $db_data_weekly as $column ){
										if ( round((strtotime("$post_date_explode[0]") - strtotime($column["Week Ending"]))/ (60 * 60 * 24)) > 60 ){
										} else {
											echo '<td style="text-align:center;">'.$currency_symbol.''.number_format($column["CPA"], 0).'</td>';
										}
									};
								echo '</tr>';
								echo '<tr style="text-align:center;">';
									echo '<td style="text-align:center;min-width:182px"class="first-column">ROAS</td>';
									foreach( $db_data_weekly as $column ){
										if ( round((strtotime("$post_date_explode[0]") - strtotime($column["Week Ending"]))/ (60 * 60 * 24)) > 60 ){
										} else {
											echo '<td style="text-align:center;">'.number_format($column["ROAS"], 2).'</td>';
										}
									};
								echo '</tr>';
							echo '</tbody>';
							echo '</table>';
							echo '</div>';
							echo '</br>';
						};
						?>
					</div>
				</div>
				<button class="button-dl btn btn-block cust-btn text-center"><a class="getfile2">Download CSV</a></button>
			</div>
			<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>	
			<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js"></script>	
			<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
			<script>
				$(function () {
					$('[data-toggle="tooltip"]').tooltip()
				})
			</script>
			<script>
				function exportTableToCSV($table, filename) {

					var $rows = $table.find('tr'),
					
					// Temporary delimiter characters unlikely to be typed by keyboard
					// This is to avoid accidentally splitting the actual contents
					tmpColDelim = String.fromCharCode(11), // vertical tab character
					tmpRowDelim = String.fromCharCode(0), // null character
					// actual delimiter characters for CSV format
					colDelim = '","',
					rowDelim = '"\r\n"',
					count = 0,
							// Grab text from table into CSV formatted string
							csv = '"' + $rows.map(function (i, row) {
									var $row = $(row)
									if (count == 0){
										var $cols = $row.find('th')
										count++ 
									} else {
										var $cols = $row.find('td')
									}

									return $cols.map(function (j, col) {
											var $col = $(col),
											text = $col.text().replace(/([^a-zA-Z0-9\_\-\$\s\%\/])/g, "");

											return text.replace('"', '""'); // escape double quotes

									}).get().join(tmpColDelim);

							}).get().join(tmpRowDelim)
									.split(tmpRowDelim).join(rowDelim)
									.split(tmpColDelim).join(colDelim) + '"',

							// Data URI
							csvData = 'data:application/csv;charset=utf-8,' + encodeURIComponent(csv);

					$(this)
							.attr({
							'download': filename,
									'href': csvData,
									'target': '_blank'
					});
				}

			</script>
			<script>				
				$('.getfile1').click(
					function() { 
						exportTableToCSV.apply(this, [$('#thetable1'), '<?php echo $db_data[0]["account_name"].''?>'+'_overall.csv']);
					});
				$('.getfile2').click(
					function() { 
						exportTableToCSV.apply(this, [$('#thetable2'), '<?php echo $db_data[0]["account_name"].''?>'+'_weekly.csv']);
					});
			</script>
		</body>
</html>