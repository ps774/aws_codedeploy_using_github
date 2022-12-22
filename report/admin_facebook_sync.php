<?php
if(!isset($_SESSION)) 
{ 
  session_start(); 
} 
include('php/db.php');
include('php/templates.php');

if( $_SESSION['user']['status'] == 'logged_in' && !empty($_SESSION['user']['email']) && $_SESSION['user']['type'] == '99' ){
	$mode = 1;
} else {
	//var_dump($_SESSION);
	header('Location: /'.$base.'index.php');
	exit();
}

$sql = 'SELECT M.account_name, F.`Account ID`, MIN(F.Day) AS "FB Min Sync", MAX(F.Day) AS "FB Max Sync", A.expires FROM popsixle.fb_spend_revenue F
LEFT JOIN fb_ad_account_id_map M ON F.`Account ID` = M.fb_ad_account_id
LEFT JOIN accounts A ON M.account_id = A.id
WHERE M.account_name IS NOT NULL AND "'.date("Y-m-d").'" <= A.expires
GROUP BY F.`Account ID`';
$db_data = sql_select_to_array($sql);
$timezones = array(
	'GMT' => "+00:00",
	'UTC' => "+00:00",
	'ECT' => "+01:00",
	'EET' => "+02:00",
	'ART' => "+02:00",
	'EAT' => "+03:00",
	'MET' => "+03:30",
	'NET' => "+04:00",
	'PLT' => "+05:00",
	'IST' => "+05:30",
	'BST' => "+06:00",
	'VST' => "+07:00",
	'CTT' => "+08:00",
	'JST' => "+09:00",
	'ACT' => "+09:30",
	'AET' => "+10:00",
	'NST' => "+12:00",
	'MIT' => "-11:00",
	'HST' => "-10:00",
	'AST' => "-09:00",
	'PST' => "-08:00",
	'PNT' => "-07:00",
	'MST' => "-07:00",
	'CST' => "-06:00",
	'EST' => "-05:00",
	'IET' => "-05:00",
	'PRT' => "-04:00",
	'CNT' => "-03:30",
	'AGT' => "-03:00",
	'BET' => "-03:00",
	'CAT' => "-01:00",
);

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

 @media (min-width: 769px) {
	.too-small-hide{
		display: block;
	}	 
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
th:hover {
	cursor: pointer;
	background-color: purple;
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
	<title>Pop6 Admin</title>
</head>
<body class="m-3" style="background: #FFFFFF;">
	<a href="/<?php echo $base;?>index.php"><img src="/<?php echo $base;?>img/popsixle_logo2.png" width="205" height="52" style="margin:0 0 10px;" alt="Popsixle Logo" border="0" /></a>
		<!-- <p class="pt-2" style="margin-left:2px;"><b>Back to the good old days.</b></p> -->

	
	
	<?php
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

      <div class="card bg-light">
        <div class="mx-auto text-center p-4">
          <div class="">
						<?php 
							echo '<table border="1" cellpadding = "5">';
							echo '<tr style="background:#666;color:#FFF; padding: auto 10px;" cellpadding = "8">';
							echo '<th style="min-width: 400px">Shop</th>';
							echo '<th style="min-width: 100px">Expires</th>';
							echo '</tr>';	

						foreach( $db_data as $row ){
							if(!empty($row['local_timezone'])) {
								$local_timezone = $timezones[$row['local_timezone']];
								// date_default_timezone_set($row['local_timezone']);
							} else {
								$local_timezone = $timezones["UTC"];
								// date_default_timezone_set("UTC");
							}
							if(!empty($row['server_timezone'])) {
								$server_timezone = $timezones[$row['server_timezone']];
							} else {
								$server_timezone = $timezones["UTC"];
							}
							
							$date = date("Y-m-d", strtotime('-1 day'));

							// $time_diff_min = ((time() - strtotime($row['FB Min Sync'])) / (60 * 60 * 24));
							// $time_diff_max = ((time() - strtotime($row['FB Max Sync'])) / (60 * 60 * 24));
              // var_dump($time_diff_min);
              // var_dump($time_diff_max);
								echo '<tr style="text-align:center;">';
								if (!empty($row['account_name'])){
									echo '<td style="text-align:left;"><a class="nav-link" href="/'.$base.'admin_shop_account.php/?shop='.$row["sha_token"].'">'.$row['account_name'].'</a></td>';
								}
								// if ($time_diff < 2) {
                //   echo '<td style="background: rgba(255,0,0)">'.$row['expires'].'</td>';
                // } else if ($time_diff > 1 && $time_diff < 4) {
                //   echo '<td style="background: rgba(255, 153, 0)">'.$row['expires'].'</td>';
                // } else if ($time_diff > 3 && $time_diff < 8) {
                //   echo '<td style="background: rgba(255, 255, 0)">'.$row['expires'].'</td>';
                // } else if ($time_diff > 7) {
                //   echo '<td style="background: rgba(50, 255, 0)">'.$row['expires'].'</td>';
                // } else {
                //   echo '<td style="background: rgba(255,0,0)">'.$row['expires'].'</td>';
                // }
								if ($time_diff_min) {
                  echo '<td style="background: rgba(255,0,0)">'.$time_diff_min.'</td>';
                } else if ($time_diff > 1 && $time_diff < 4) {
                  echo '<td style="background: rgba(255, 153, 0)">'.$row['expires'].'</td>';
                } else if ($time_diff > 3 && $time_diff < 8) {
                  echo '<td style="background: rgba(255, 255, 0)">'.$row['expires'].'</td>';
                } else if ($time_diff > 7) {
                  echo '<td style="background: rgba(50, 255, 0)">'.$row['expires'].'</td>';
                } else {
                  echo '<td style="background: rgba(255,0,0)">'.$row['expires'].'</td>';
                }
								echo '</tr>';
						?>
						</pre>
					</div>
				</div>
				<?php
					}
					echo '</table>';
				?>
			</div>									
			<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>	
			<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
		</body>
		<script>
		const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;
		const comparer = (idx, asc) => (a, b) => ((v1, v2) => 
			v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
			)(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));
		document.querySelectorAll('th').forEach(th => th.addEventListener('click', (() => {
			const table = th.closest('table');
			Array.from(table.querySelectorAll('tr:nth-child(n+2)'))
			.sort(comparer(Array.from(th.parentNode.children).indexOf(th), this.asc = !this.asc))
			.forEach(tr => table.appendChild(tr) );
			})));

		</script>
</html>