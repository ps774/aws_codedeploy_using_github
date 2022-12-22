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

$sql = 'SELECT A.account_name, F.`Account ID`, MIN(F.Day) AS "FB Min Sync", MAX(F.Day) AS "FB Max Sync", A.expires, A.local_timezone, A.server_timezone, A.sha_token FROM accounts A
LEFT JOIN fb_ad_account_id_map M ON M.account_id = A.id
LEFT JOIN popsixle.fb_spend_revenue F ON F.`Account ID` = M.fb_ad_account_id
WHERE type != "99" AND type != "50" AND NOW() - INTERVAL 1 DAY <= expires
GROUP BY account_name ORDER BY expires ASC;';
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
.styled-table {
    border-collapse: collapse;
    margin: 25px 0;
    font-family: sans-serif;
    min-width: 300px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
}


.styled-table th,
.styled-table td {
    padding: 7px 7px;
}
.styled-table tr {
	border-bottom: 1px solid #d5bfdd;
}

.styled-table tr:nth-of-type(even) {
	background-color: white;
}

.styled-table tr:last-of-type {
	border-bottom: 2px solid #9a20f0;
}
.styled-table tr.active-row {
	font-weight: bold;
	color: black;
}
.table-head td, .table-head{
	background-color: #9a20f0;
	color: #ffffff;
	text-align: center;
	padding: 7px 7px;
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
							echo '<table class="styled-table">';
							echo '<tr class="table-head">';
							echo '<th >Shop</th>';
							echo '<th >Expires</th>';
							echo '<th >FB Min Sync</th>';
							echo '<th >FB Max Sync</th>';
							echo '</tr>';	
							
						foreach( $db_data as $row ){
							$now = time();
							if(!empty($row['FB Min Sync'])) {
								$fb_time_min = strtotime($row['FB Min Sync']);
								$fb_time_diff_min = $now - $fb_time_min;
								$fb_time_diff_min = round($fb_time_diff_min / (60 * 60 * 24));
							} else {
								$fb_time_diff_min = 0;
							}
							if(!empty($row['FB Max Sync'])) {
								$fb_time_max = strtotime($row['FB Max Sync']);
								$fb_time_diff_max = $now - $fb_time_max;
								$fb_time_diff_max = round($fb_time_diff_max / (60 * 60 * 24));
							} else {
								$fb_time_diff_max = 0;
							}

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
							
							$date = date("Y-m-d H:i:s", strtotime('-1 day'));

							$time_diff = (new DateTime($row['expires']))->diff(new DateTime($date))->days;
								echo '<tr style="text-align:center;">';
								if (!empty($row['account_name'])){
									echo '<td style="text-align:left;"><a class="nav-link" href="/'.$base.'admin_shop_account.php/?shop='.$row["sha_token"].'">'.$row['account_name'].'</a></td>';
								} else {
									echo '<td style="text-align:left;"><a class="nav-link" href="/'.$base.'admin_shop_account.php/?shop='.$row["sha_token"].'">'.$row['username'].'</a></td>';
								}
								if ($time_diff < 2) {
                  echo '<td style="background: rgba(255,0,0)">'.$row['expires'].'</td>';
                } else if ($time_diff > 1 && $time_diff < 4) {
                  echo '<td style="background: rgba(255, 153, 0)">'.$row['expires'].'</td>';
                } else if ($time_diff > 3 && $time_diff < 8) {
                  echo '<td style="background: rgba(255, 255, 0)">'.$row['expires'].'</td>';
                } else if ($time_diff > 7) {
                  echo '<td style="background: rgba(50, 255, 0)">'.$row['expires'].'</td>';
                } else {
                  echo '<td style="background: rgba(255,0,0)">'.$row['expires'].'</td>';
                }
								if(!empty($fb_time_diff_min)){
									if ($fb_time_diff_min < 30) {
										echo '<td style="background: rgba(255,0,0)">'.$row['FB Min Sync'].'</td>';
									}  else if ($fb_time_diff_min > 29 && $fb_time_diff_min < 60) {
										echo '<td style="background: rgba(255, 255, 0)">'.$row['FB Min Sync'].'</td>';
									} else if ($fb_time_diff_min > 59) {
										echo '<td style="background: rgba(50,255,0)">'.$row['FB Min Sync'].'</td>';
									}
								} else {
									echo '<td style="">'.$row['FB Min Sync'].'</td>';
								}
								if(!empty($fb_time_diff_max)){
									if ($fb_time_diff_max > 7) {
										echo '<td style="background: rgba(255,0,0)">'.$row['FB Max Sync'].'</td>';
									}  else if ($fb_time_diff_max > 4 && $fb_time_diff_max < 8) {
										echo '<td style="background: rgba(255, 255, 0)">'.$row['FB Max Sync'].'</td>';
									} else if ($fb_time_diff_max < 5) {
										echo '<td style="background: rgba(50,255,0)">'.$row['FB Max Sync'].'</td>';
									}
								} else {
									echo '<td style="">'.$row['FB Max Sync'].'</td>';
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