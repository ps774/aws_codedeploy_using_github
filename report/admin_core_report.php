<?php
session_start();
include('php/db.php');
include('php/templates.php');

if( $_SESSION['user']['status'] == 'logged_in' && !empty($_SESSION['user']['email']) && $_SESSION['user']['type'] == '99' ){
	$mode = 1;
} else {
	//var_dump($_SESSION);
	header('Location: /'.$base.'index.php');
	exit();
}

$sql = 'SELECT

A.account_name, A.sha_token as `sha_token`, R.Currency,

SUM(CASE WHEN Day < A.install_date AND DATEDIFF(A.install_date, Day) < 60 THEN `Amount spent` ELSE 0 END) AS `Pre - Total Spend`,
SUM(CASE WHEN Day > A.install_date THEN `Amount spent` ELSE 0 END ) AS `Post - Total Spend`,

SUM(CASE WHEN Day < A.install_date AND DATEDIFF(A.install_date, Day) < 60 THEN `Amount spent` ELSE 0 END) /
(DATEDIFF(
MAX(CASE WHEN Day < A.install_date AND DATEDIFF(A.install_date, Day) < 60 THEN `Day` ELSE NULL END),
MIN(CASE WHEN Day < A.install_date AND DATEDIFF(A.install_date, Day) < 60 THEN `Day` ELSE NULL END)
) + 1 )
AS `Pre - Spend Per Day`,
SUM(CASE WHEN Day > A.install_date THEN `Amount spent` ELSE 0 END ) /
(DATEDIFF(
MAX(CASE WHEN Day > A.install_date THEN `Day` ELSE NULL END),
MIN(CASE WHEN Day > A.install_date THEN `Day` ELSE NULL END)
) + 1 )
AS `Post - Spend Per Day`,

SUM(CASE WHEN Day < A.install_date AND DATEDIFF(A.install_date, Day) < 60 THEN `Purchases` ELSE 0 END) AS `Pre - Purchases`,
SUM(CASE WHEN Day > A.install_date THEN `Purchases` ELSE 0 END ) AS `Post - Purchases`,

SUM(CASE WHEN Day < A.install_date AND DATEDIFF(A.install_date, Day) < 60 THEN `Purchases conversion value` ELSE 0 END) AS `Pre - Revenue`,
SUM(CASE WHEN Day > A.install_date THEN `Purchases conversion value` ELSE 0 END ) AS `Post - Revenue`,

SUM(CASE WHEN Day < A.install_date AND DATEDIFF(A.install_date, Day) < 60 THEN `Amount spent` ELSE 0 END) / SUM(CASE WHEN Day < A.install_date AND DATEDIFF(A.install_date, Day) < 60 THEN `Purchases` ELSE 0 END) AS `Pre - CPA`,
SUM(CASE WHEN Day > A.install_date THEN `Amount spent` ELSE 0 END) / SUM(CASE WHEN Day > A.install_date THEN `Purchases` ELSE 0 END ) AS `Post - CPA`,

SUM(CASE WHEN Day < A.install_date AND DATEDIFF(A.install_date, Day) < 60 THEN `Purchases conversion value` ELSE 0 END) / SUM(CASE WHEN Day < A.install_date AND DATEDIFF(A.install_date, Day) < 60 THEN `Amount spent` ELSE 0 END) AS `Pre - ROAS`,
SUM(CASE WHEN Day > A.install_date THEN `Purchases conversion value` ELSE 0 END) / SUM(CASE WHEN Day > A.install_date THEN `Amount spent` ELSE 0 END) AS `Post - ROAS`,

CONCAT( MIN(CASE WHEN Day < A.install_date AND DATEDIFF(A.install_date, Day) < 60 THEN `Day` ELSE NULL END), " to ", MAX(CASE WHEN Day < A.install_date AND DATEDIFF(A.install_date, Day) < 60 THEN `Day` ELSE NULL END) ) AS `Pre - Date Range`,
CONCAT( MIN(CASE WHEN Day > A.install_date THEN `Day` ELSE NULL END), " to ", MAX(CASE WHEN Day > A.install_date THEN `Day` ELSE NULL END) ) AS `Post - Date Range`

FROM fb_ad_account_id_map F LEFT JOIN `fb_spend_revenue` R ON R.`Account ID` IN (F.fb_ad_account_id) LEFT JOIN accounts A ON A.sha_token = F.sha_token
WHERE F.fb_ad_account_id IS NOT NULL AND F.active = 1 AND "'.date("Y-m-d").'" <= A.expires

GROUP BY A.account_name;';
$db_data = sql_select_to_array($sql);


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
a {
	color: #AC39FD;
}
a:active {
	color: white;
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
.getfile1 {
	text-decoration: none;
	color: white;
}
.getfile1:active {
	text-decoration: none;
	color: white;
}
th:hover {
	cursor: pointer;
	background-color: purple;
}
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
	background-color: #9a20f0;
	color: #ffffff;
	text-align: center;
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
	<title>Pop6 Admin Core Report</title>
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

      <div class="card bg-light scroll">
			<br>
			<div class="text-center row px-2">
				<div class="col px-1 too-small-hide"></div>
				<div class="col px-1 too-small-hide"></div>
				<div class="col px-1 too-small-hide"></div>
				<div class="col px-1 too-small-hide"></div>
					<div class="col px-1">
					<button class="button-dl btn cust-btn text-center"><a id="getfile1" class="getfile1">Download</a></button>
					</div>
			</div>
				<div class="mx-auto text-center p-4">
          <div class="">
						<?php 

							echo '<table id="thetable1" class="styled-table">';
								echo '<tr  class="table-head">';
								echo '<th class="order" style="min-width: 200px">account_name</th>';
								echo '<th class="order" style="min-width: 120px">Delta ROAS</td>';
								echo '<th class="order" style="min-width: 120px">Delta CPA</td>';
								echo '<th class="order" style="min-width: 120px">Delta Spend</td>';
								echo '<th class="order" style="min-width: 120px"></td>';
								echo '<th class="order" style="min-width: 120px">Pre - Total Spend</th>';
								echo '<th class="order" style="min-width: 120px">Post - Total Spend</td>';
								echo '<th class="order" style="min-width: 120px">Pre - Spend Per Day</td>';
								echo '<th class="order" style="min-width: 120px">Post - Spend Per Day</td>';
								echo '<th class="order" style="min-width: 120px">Pre - Purchases</td>';
								echo '<th class="order" style="min-width: 120px">Post - Purchases</td>';
								echo '<th class="order" style="min-width: 120px">Pre - Revenue</td>';
								echo '<th class="order" style="min-width: 120px">Post - Revenue</td>';
								echo '<th class="order" style="min-width: 120px">Pre - CPA</td>';
								echo '<th class="order" style="min-width: 120px">Post - CPA</td>';
								echo '<th class="order" style="min-width: 120px">Pre - ROAS</td>';
								echo '<th class="order" style="min-width: 120px">Post - ROAS</td>';
								echo '<th class="order" style="min-width: 120px">Pre - Date Range</td>';
								echo '<th class="order" style="min-width: 120px">Post - Date Range</td>';
							echo '</tr>';	
							echo '<tbody >';

						foreach( $db_data as $row ){
							$currency_symbol = '$';
							if ( !empty($row['Currency'])){
								if ($row['Currency'] == 'GBP'){
									$currency_symbol = '£';
								} else if ($row['Currency'] == 'USD'){
									$currency_symbol = '$';
								} else if ($row['Currency'] == 'CAD'){
									$currency_symbol = 'C$';
								} else if ($row['Currency'] == 'SEK'){
									$currency_symbol = 'kr';
								} else if ($row['Currency'] == 'NZD'){
									$currency_symbol = 'NZ$';
								} else if ($row['Currency'] == 'DKK'){
									$currency_symbol = 'Kr.';
								} else if ($row['Currency'] == 'KRW'){
									$currency_symbol = '₩';
								} else if ($row['Currency'] == 'SDG'){
									$currency_symbol = 'ج.س.';
								} else if ($row['Currency'] == 'MAD'){
									$currency_symbol = 'MAD';
								} else if ($row['Currency'] == 'PLN'){
									$currency_symbol = 'zł';
								} else if ($row['Currency'] == 'CHF'){
									$currency_symbol = 'Fr.';
								} else if ($row['Currency'] == 'AED'){
									$currency_symbol = 'د.إ';
								} else if ($row['Currency'] == 'AUS' || $row['Currency'] == 'AUD'){
									$currency_symbol = 'A$';
								} else if ($row['Currency'] == 'JPY'){
									$currency_symbol = '¥';
								} else if ($row['Currency'] == 'MXN'){
									$currency_symbol = '₱';
								} else if ($row['Currency'] == 'HKD'){
									$currency_symbol = '元';
								} else if ($row['Currency'] == 'INR'){
									$currency_symbol = '₹';
								} else if ($row['Currency'] == 'SGD'){
									$currency_symbol = 'S$';
								} else if ($row['Currency'] == 'MYR'){
									$currency_symbol = 'RM';
								}
							}
							

							// var_dump ($row);
							$roas_color_green = 255;
							$roas_color_red = 255;
							$roas_color_blue = 255;
							$cpa_color_green = 255;
							$cpa_color_red = 255;
							$cpa_color_blue = 255;
							$spend_color_green = 255;
							$spend_color_red = 255;
							$spend_color_blue = 255;
							if ($formatter->format( $row['Post - ROAS']/$row['Pre - ROAS'] - 1, 0 ) != "-NaN%" && $formatter->format( $row['Post - ROAS']/$row['Pre - ROAS'] - 1, 0 ) != "∞%"){
								$delta_roas = $formatter->format( ($row['Post - ROAS']/$row['Pre - ROAS']) - 1, 0);
								if (number_format($row['Post - ROAS']/$row['Pre - ROAS'] - 1, 2) < 0) {
									if (number_format($row['Post - ROAS']/$row['Pre - ROAS'] - 1, 2) <= -1.00){
										$roas_color_red = 255;
										$roas_color_blue = 0;
										$roas_color_green = 0;
									} else {
										$roas_color_blue = (number_format($row['Post - ROAS']/$row['Pre - ROAS'] - 1, 2) + 1) * 255;
										$roas_color_green = (number_format($row['Post - ROAS']/$row['Pre - ROAS'] - 1, 2) + 1) * 255;
									}
								} else if (number_format($row['Post - ROAS']/$row['Pre - ROAS'] - 1, 2) > 0) {
									if (number_format($row['Post - ROAS']/$row['Pre - ROAS'] - 1, 2) >= 1.00){
										$roas_color_green = 255;
										$roas_color_red = 0;
										$roas_color_blue = 0;
									} else {
										$roas_color_blue = number_format((1 - ($row['Post - ROAS']/$row['Pre - ROAS'] - 1)) * 255, 0);
										$roas_color_red = number_format((1 - ($row['Post - ROAS']/$row['Pre - ROAS'] - 1)) * 255, 0);
										}
								}
							} else {
								$delta_roas = "";
							}
							if ($formatter->format( $row['Post - CPA']/$row['Pre - CPA'] - 1, 0 ) != "-NaN%"  && $formatter->format( $row['Post - CPA']/$row['Pre - CPA'] - 1, 0 ) != "∞%"){
								$delta_cpa = $formatter->format( $row['Post - CPA']/$row['Pre - CPA'] - 1, 0 );
								if (number_format($row['Post - CPA']/$row['Pre - CPA'] - 1, 2) < 0) {
									if (number_format($row['Post - CPA']/$row['Pre - CPA'] - 1, 2) <= -1.00){
										$cpa_color_red = 0;
										$cpa_color_blue = 0;
										$cpa_color_green = 255;
									} else {
										$cpa_color_blue = (number_format($row['Post - CPA']/$row['Pre - CPA'] - 1, 2) + 1) * 255;
										$cpa_color_red = (number_format($row['Post - CPA']/$row['Pre - CPA'] - 1, 2) + 1) * 255;
									}
								} else if (number_format($row['Post - CPA']/$row['Pre - CPA'] - 1, 2) > 0) {
									if (number_format($row['Post - CPA']/$row['Pre - CPA'] - 1, 2) >= 1.00){
										$cpa_color_green = 0;
										$cpa_color_red = 255;
										$cpa_color_blue = 0;
									} else {
										$cpa_color_blue = number_format((1 - ($row['Post - CPA']/$row['Pre - CPA'] - 1)) * 255, 0);
										$cpa_color_green = number_format((1 - ($row['Post - CPA']/$row['Pre - CPA'] - 1)) * 255, 0);
										}
								}
							} else {
								$delta_cpa = "";
							}
							if ($formatter->format( $row['Post - Spend Per Day']/$row['Pre - Spend Per Day'] - 1, 0 ) != "-NaN%"  && $formatter->format( $row['Post - Spend Per Day']/$row['Pre - Spend Per Day'] - 1, 0 ) != "∞%"){
								$delta_spend = $formatter->format( $row['Post - Spend Per Day']/$row['Pre - Spend Per Day'] - 1, 0 );
								if (number_format($row['Post - Spend Per Day']/$row['Pre - Spend Per Day'] - 1, 2) < 0) {
									if (number_format($row['Post - Spend Per Day']/$row['Pre - Spend Per Day'] - 1, 2) <= -1.00){
										$spend_color_red = 255;
										$spend_color_blue = 0;
										$spend_color_green = 0;
									} else {
										$spend_color_blue = (number_format($row['Post - Spend Per Day']/$row['Pre - Spend Per Day'] - 1, 2) + 1) * 255;
										$spend_color_green = (number_format($row['Post - Spend Per Day']/$row['Pre - Spend Per Day'] - 1, 2) + 1) * 255;
									}
								} else if (number_format($row['Post - Spend Per Day']/$row['Pre - Spend Per Day'] - 1, 2) > 0) {
									if (number_format($row['Post - Spend Per Day']/$row['Pre - Spend Per Day'] - 1, 2) >= 1.00){
										$spend_color_green = 255;
										$spend_color_red = 0;
										$spend_color_blue = 0;
									} else {
										$spend_color_blue = number_format((1 - ($row['Post - Spend Per Day']/$row['Pre - Spend Per Day'] - 1)) * 255, 0);
										$spend_color_red = number_format((1 - ($row['Post - Spend Per Day']/$row['Pre - Spend Per Day'] - 1)) * 255, 0);
										}
								}

							} else {
								$delta_spend = "";
							}

								echo '<tr >';
									echo '<td "><a class="nav-link" href="/'.$base.'admin_shop_account.php/?shop='.$row["sha_token"].'">'.$row['account_name'].'</a></td>';
									echo '<td style="text-align:center;background-color:rgb('.$roas_color_red.', '.$roas_color_green.', '.$roas_color_blue.')">'.$delta_roas.'</td>';
									echo '<td style="text-align:center;background-color:rgb('.$cpa_color_red.', '.$cpa_color_green.', '.$cpa_color_blue.')">'.$delta_cpa.'</td>';
									echo '<td style="text-align:center;background-color:rgb('.$spend_color_red.', '.$spend_color_green.', '.$spend_color_blue.')">'.$delta_spend.'</td>';
									echo '<td style="text-align:left;"></td>';
									echo '<td style="text-align:left;">'.$currency_symbol.''. number_format( $row['Pre - Total Spend'], 0 ).'</td>';
									echo '<td style="text-align:left;">'.$currency_symbol.''. number_format( $row['Post - Total Spend'], 0 ).'</td>';
									echo '<td style="text-align:left;">'.$currency_symbol.''. number_format( $row['Pre - Spend Per Day'], 0 ).'</td>';
									echo '<td style="text-align:left;">'.$currency_symbol.''. number_format( $row['Post - Spend Per Day'], 0 ).'</td>';
									echo '<td style="text-align:left;">'.$currency_symbol.''. number_format( $row['Pre - Purchases'], 0 ).'</td>';
									echo '<td style="text-align:left;">'.$currency_symbol.''. number_format( $row['Post - Purchases'], 0 ).'</td>';
									echo '<td style="text-align:left;">'.$currency_symbol.''. number_format( $row['Pre - Revenue'], 0 ).'</td>';
									echo '<td style="text-align:left;">'.$currency_symbol.''. number_format( $row['Post - Revenue'], 0 ).'</td>';
									echo '<td style="text-align:left;">'.$currency_symbol.''. number_format( $row['Pre - CPA'], 0 ).'</td>';
									echo '<td style="text-align:left;">'.$currency_symbol.''. number_format( $row['Post - CPA'], 0 ).'</td>';
									echo '<td style="text-align:left;">'.number_format( $row['Pre - ROAS'], 2 ).'</td>';
									echo '<td style="text-align:left;">'.number_format( $row['Post - ROAS'], 2 ).'</td>';
									echo '<td style="text-align:left;">'.$row['Pre - Date Range'].'</td>';
									echo '<td style="text-align:left;">'.$row['Post - Date Range'].'</td>';
								echo '</tr>';
								
								?>
						</pre>
					</div>
				</div>
				<?php
					}
					echo '</tbody>';
					echo '</table>';
				?>
			</div>									
			<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>	
			<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
			<script>
				document.querySelectorAll('th.order').forEach(th_elem => {
						let asc = true
						const span_elem = document.createElement('span')
						span_elem.style = "font-size:0.8rem; margin-left:0.5rem"
						span_elem.innerHTML = "▼"
						th_elem.appendChild(span_elem)
						th_elem.classList.add('order-inactive')

						const index = Array.from(th_elem.parentNode.children).indexOf(th_elem)
						th_elem.addEventListener('click', (e) => {
							document.querySelectorAll('th.order').forEach(elem => {
								elem.classList.remove('order-active')
								elem.classList.add('order-inactive')
							})
							th_elem.classList.remove('order-inactive')
							th_elem.classList.add('order-active')

							if (!asc) {
								th_elem.querySelector('span').innerHTML = '▲'
							} else {
								th_elem.querySelector('span').innerHTML = '▼'
							}
							const arr = Array.from(th_elem.closest("table").querySelectorAll('tbody tr')).slice(1)
							arr.sort((a, b) => {
								const a_val = a.children[index].innerText.toString().replace(",", "")
								const b_val = b.children[index].innerText.toString().replace(",", "")
									return (!asc) ? a_val.replace(",", "").match(/\-[^a-zA-Z]/g) && b_val.replace(",", "").match(/\-[^a-zA-Z]/g) ? b_val.replace(",", "").localeCompare(a_val.replace(",", ""), undefined, {
										numeric: true,
										sensitivity: 'base'
									}) : a_val.replace(",", "").localeCompare(b_val.replace(",", ""), undefined, {
										numeric: true,
										sensitivity: 'base'
									}) : b_val.replace(",", "").match(/\-[^a-zA-Z]/g) && a_val.replace(",", "").match(/\-[^a-zA-Z]/g) ? a_val.replace(",", "").localeCompare(b_val.replace(",", ""), undefined, {
										numeric: true,
										sensitivity: 'base'
									}): b_val.replace(",", "").localeCompare(a_val.replace(",", ""), undefined, {
										numeric: true,
										sensitivity: 'base'
									})
							})

							arr.forEach(elem => {
								th_elem.closest("table").querySelector("tbody").appendChild(elem)
							})
							asc = !asc
						})
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
						exportTableToCSV.apply(this, [$('#thetable1'), 'Full_Core_Report.csv']);
					});
			</script>
		</body>
</html>