<?php
if(!isset($_SESSION)) 
{ 
  session_start(); 
} 
include('php/db_read_replica.php');
include('php/templates.php');
$active_page = $_SERVER['REQUEST_URI'];
if( $_SESSION['user']['status'] == 'logged_in' && !empty($_SESSION['user']['email'])){
	$mode = 1;
} else {
	//var_dump($_SESSION);
	header('Location: /'.$base.'index.php');
	exit();
}
if(!empty($_GET['graph_range'])){
	$graph_range = $_GET['graph_range'];
} else {
	$graph_range = 7;
}
if(!empty($_GET['table_range'])){
	$table_range = $_GET['table_range'];
} else {
	$table_range = 7;
}
if(!empty($_GET['graph_type'])){
	$graph_type = $_GET['graph_type'];
} else {
	$graph_type = "Purchase";
}
$sql = 'SELECT *, A.sha_token as `token` FROM accounts A LEFT JOIN fb_settings F ON A.sha_token = F.sha_token WHERE A.sha_token = "'.$_SESSION['user']['id'].'";';
$db_data = sql_select_to_array_replica($sql);
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
if(!empty($db_data[0]['local_timezone'])) {
	$local_timezone = $timezones[$db_data[0]['local_timezone']];
} else {
	$local_timezone = $timezones["UTC"];
}
if(!empty($db_data[0]['server_timezone'])) {
	$server_timezone = $timezones[$db_data[0]['server_timezone']];
} else {
	$server_timezone = $timezones["UTC"];
}

if (!empty($db_data[0]['install_date'])) {
	$install_date = date_create($db_data[0]["install_date"]);
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
$_SESSION['server_timezone'] = $server_timezone;
$_SESSION['db_data'] = $db_data;
$_SESSION['local_timezone'] = $local_timezone;
$_SESSION['table_range'] = $table_range;
?>
<!DOCTYPE html>
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

canvas{
	max-width:1100px !important;
	max-height:500px !important;
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
.explanation_text{
	font-size: 11px;
	margin: 5px 0 0 5px;
	opacity: 60%;
	text-align: center;
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

.charts_container {
	display: flex;
	flex-wrap: wrap;
	margin-top: 10px;
}
.chart_container {
	width: 45%;
	min-width: 800px;
	margin: 10px;
}
#graph_type_change{
	margin-right: 5px;
	height: 31px;
	width: 150px;
}
#graph_range_change{
	width: 40px;
	text-align: center;
	width: 50px;
	margin-right: 5px;
}
#table_range_change{
	width: 40px;
	text-align: center;
	width: 50px;
	margin-right: 5px;
}
#graph_range_change_form{
	display: flex;

	flex-direction: column;
	justify-content: space-between;
}
#account_block{
	justify-content: center;
	align-items: center;
	text-align: center;
}
#table_returned{
	justify-content: center;
	align-items: center;
	text-align: center;
}
#graph_block{
	justify-content: center;
	align-items: center;
	text-align: center;
}
#graph_returned{
	justify-content: center;
	align-items: center;
	text-align: center;
}
#table_block{
	justify-content: center;
	align-items: center;
	text-align: center;
}
.styled-table {
    border-collapse: collapse;
    margin: 25px 0;
    font-family: sans-serif;
    min-width: 300px;
    box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
}
.styled-table thead tr {
    background-color: #9a20f0;
    color: #ffffff;
    text-align: center;
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

	</style>
	<link rel="apple-touch-icon" sizes="180x180" href="/<?php echo $base;?>favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/<?php echo $base;?>favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/<?php echo $base;?>favicon/favicon-16x16.png">
	<link rel="manifest" href="/<?php echo $base;?>favicon/site.webmanifest">
	<link rel="mask-icon" href="/<?php echo $base;?>favicon/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#ffffff">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Pop6 Admin Events Manager</title>
	<script src="https://kit.fontawesome.com/739f699b00.js" crossorigin="anonymous"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js" integrity="sha512-ElRFoEQdI5Ht6kZvyzXhYG9NqjtkmlkfYk0wr6wHxU9JEHakS7UJZNeml5ALk+8IKlU6jDgMabC3vkumRokgJA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
</head>
<body class="m-3" style="background: #FFFFFF;">
	<a href="/<?php echo $base;?>index.php"><img src="/<?php echo $base;?>img/popsixle_logo2.png" width="205" height="52" style="margin:0 0 10px;" alt="Popsixle Logo" border="0" /></a>

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
			<div class="card base-card border p-3 bg-light mb-2" id="account_block">
				<div class="">
					<h2 class="my-0  pb-1" style="color:#AC39FD"><b><?php echo ''.$db_data[0]['account_name'].'' ?></b></h2>
					<h5 class="my-0  pb-1" style="color:#AC39FD">Popsixle Launch Date: <?php echo date_format($install_date, "m/d/Y") ?></h5>
					<h5 class="my-0  pb-3" style="color:#AC39FD">Days live: <?php echo $shop_install_days ?></h5>
				</div>				
			</div>

			<div id="table_returned">
			</div>
			<div class="card base-card border p-3 bg-light mb-2" id="graph_block" >
				<div id="graph_returned" >

				</div>

			</div>

<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>	
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

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

<script type="text/javascript">
			function table_fetch(){


				
				var args = {};
				args.mode = "one";
				args.passed_data = "stuff"
				if ($('#table_range_change').val()){
					args.table_range = parseInt($('#table_range_change').val());
				} else {
					args.table_range = 1
				}
				$('#table_returned').html('<div>nada</div>');
				$('#table_returned').html(`<div class="card base-card border p-3 bg-light mb-2" id="table_block"><div class=""><h4 class="my-2  pb-0" style="color:#AC39FD"><b>${args.table_range} Day Real-Time Event Data Loading</b></h4<br/><br/><br/><br/><img class="m-3" style="" src="/<?php echo $base ?>img/loading-54.gif" width=800 height=300 /></div></div>`);

				$.ajax({
        type: 'post',
        url: "/<?php echo $base ?>services/event_table.php",
        data: args,
        success: function (data) {
					var table_filling = ""
					data = JSON.parse(data)
					let table_dates = data['date_range']
					data = data['table_data']
					let table_date1, table_date2
					if (table_dates[0]['date_min']){
						table_date1 = table_dates[0]['date_min']
						table_date2 = table_dates[0]['date_max']
						table_date1 = table_date1.split(" ")
						table_date1a = table_date1.shift()
						table_date1a = table_date1a.split('-')
						table_date1a.push(table_date1a.shift())
						table_date1a = table_date1a.join('/')
						table_date1a = [table_date1a]
						table_date1 = table_date1a + ' ' + table_date1
						table_date2 = table_date2.split(" ")
						table_date2a = table_date2.shift()
						table_date2a = table_date2a.split('-')
						table_date2a.push(table_date2a.shift())
						table_date2a = table_date2a.join('/')
						table_date2a = [table_date2a]
						table_date2 = table_date2a + ' ' + table_date2
					}
					if (data.length > 0){
						for (const row in data) {
							if( data[row]['Total Events'] ){
								data[row]['Total Events'] = parseInt(data[row]['Total Events']).toFixed(0);
							} else {
								data[row]['Total Events'] = 0;
							}
							
							if( data[row]['% recorded'] ){
								data[row]['% recorded'] = parseInt(data[row]['% recorded']).toFixed(0);
							} else {
								data[row]['% recorded'] = 0;
							}
							
							if( data[row]['Em'] ){
								data[row]['Em'] = parseInt(data[row]['Em']).toFixed(0);
							} else {
								data[row]['Em'] = 0;
							}
							
							if( data[row]['Ph'] ){
								data[row]['Ph'] = parseInt(data[row]['Ph']).toFixed(0);
							} else {
								data[row]['Ph'] = 0;
							}
							
							if( data[row]['Fn'] ){
								data[row]['Fn'] = parseInt(data[row]['Fn']).toFixed(0);
							} else {
								data[row]['Fn'] = 0;
							}
							
							if( data[row]['Ln'] ){
								data[row]['Ln'] = parseInt(data[row]['Ln']).toFixed(0);
							} else {
								data[row]['Ln'] = 0;
							}
							
							if( data[row]['IP'] ){
								data[row]['IP'] = parseInt(data[row]['IP']).toFixed(0);
							} else {
								data[row]['IP'] = 0;
							}
							
							if( data[row]['User Agent'] ){
								data[row]['User Agent'] = parseInt(data[row]['User Agent']).toFixed(0);
							} else {
								data[row]['User Agent'] = 0;
							}
							
							if( data[row]['fbp'] ){
								data[row]['fbp'] = parseInt(data[row]['fbp']).toFixed(0);
							} else {
								data[row]['fbp'] = 0;
							}
							
							if( data[row]['fbc'] ){
								data[row]['fbc'] = parseInt(data[row]['fbc']).toFixed(0);
							} else {
								data[row]['fbc'] = 0;
							}
							
							if( data[row]['Total Revenue'] ){
								data[row]['Total Revenue'] = parseInt(data[row]['Total Revenue']).toFixed(0);
							} else {
								data[row]['Total Revenue'] = 0;
							}
							table_filling += `<tr class="active-row">`
							table_filling += `<td><b>${data[row]["event_type"]}</b></td>`
							table_filling += `<td>${data[row]["Total Events"]}</td>`
							table_filling += `<td style="background: rgba(50,200,50, ${(parseInt(data[row]['% recorded'])/100.0).toFixed(2)}">${data[row]["% recorded"]}%</td>`
							table_filling += `<td style="background: rgba(50,200,50, ${(parseInt(data[row]['Em'])/100.0).toFixed(2)}">${data[row]["Em"]}%</td>`
							table_filling += `<td style="background: rgba(50,200,50, ${(parseInt(data[row]['Ph'])/100.0).toFixed(2)}">${data[row]["Ph"]}%</td>`
							table_filling += `<td style="background: rgba(50,200,50, ${(parseInt(data[row]['Fn'])/100.0).toFixed(2)}">${data[row]["Fn"]}%</td>`
							table_filling += `<td style="background: rgba(50,200,50, ${(parseInt(data[row]['Ln'])/100.0).toFixed(2)}">${data[row]["Ln"]}%</td>`
							table_filling += `<td style="background: rgba(50,200,50, ${(parseInt(data[row]['IP'])/100.0).toFixed(2)}">${data[row]["IP"]}%</td>`
							table_filling += `<td style="background: rgba(50,200,50, ${(parseInt(data[row]['User Agent'])/100.0).toFixed(2)}">${data[row]["User Agent"]}%</td>`
							table_filling += `<td style="background: rgba(50,200,50, ${(parseInt(data[row]['fbp'])/100.0).toFixed(2)}">${data[row]["fbp"]}%</td>`
							table_filling += `<td style="background: rgba(50,200,50, ${(parseInt(data[row]['fbc'])/100.0).toFixed(2)}">${data[row]["fbc"]}%</td>`
							table_filling += `<td>$${data[row]['Total Revenue']}</td>`
							table_filling += `</tr>`
						}
						$('#table_returned').html( 
							'<div class="card base-card border p-3 bg-light mb-2" id="table_block">'+
								'<div class="">'+
									`<h4 class="my-2  pb-0" style="color:#AC39FD"><b>Up to ${args.table_range} Day Real-Time Event Data</b></h4>`+
									`<h5 class="my-2  pb-0" style="color:#AC39FD">Actual Data shown:</h5>`+
									`<h6 class="my-2  pb-0" style="color:#AC39FD">${table_date1} - ${table_date2} <?php echo ''.$db_data[0]['local_timezone'].'' ?></h6>`+
									'<div>'+
										`<input name="table_range_change" id="table_range_change" type="text" placeholder="Range: Days Back" value="${args.table_range}">`+
										'<button class="button cust-btn" onclick="table_fetch()" return false>GO</button>'+
										'<br>'+
									'</div>'+
									'<div>'+
										'<div>'+
											'<div class="explanation_text">Update the value above to adjust the date range shown.</div>'+
											'</div>'+
										'</div>'+
									'</div>'+
									'<table class="styled-table table-head">'+
									
									'<thead>'+
										'<tr>'+
											'<th>Event Type</th>'+
											'<th>Total Events</th>'+
											'<th>% recorded</th>'+
											'<th>Email %</th>'+
											'<th>Phone %</th>'+
											'<th>F Name %</th>'+
											'<th>L Name %</th>'+
											'<th>IP %</th>'+
											'<th>Browser %</th>'+
											'<th>FB Session %</th>'+
											'<th>FB Click %</th>'+
											'<th>Total Revenue</th>'+
										'</tr>'+
									'</thead>'+
									
									'<tbody>'+
									`${table_filling}`+
									'</tbody>'+
										'</table>'+
									'</div>'+
								'</div>'
							);
					} else {
						if(parseInt(args.table_range) > 1){
							$('#table_returned').html( 
								'<div class="card base-card border p-3 bg-light mb-2" id="table_block">'+
									'<div class="">'+
										`<h4 class="my-2  pb-0" style="color:#AC39FD"><b>No Table Data Found within ${args.table_range} Days</b></h4>`+
										`<h6 class="my-2  pb-0" style="color:#AC39FD"><b>Increase Date range below and try again</b></h6>`+
										'<br>'+
										'<div>'+
											`<input name="table_range_change" id="table_range_change" type="text" placeholder="Range: Days Back" value="${args.table_range}">`+
											'<button class="button cust-btn" onclick="table_fetch()" return false>GO</button>'+
											'<br>'+
										'</div>'+
										'<div>'+
											'<div>'+
												'<div class="explanation_text">Update the value above to adjust the date range shown.</div>'+
												'</div>'+
											'</div>'+
										'</div>'+
									'</div>'
								);
						} else {
							$('#table_returned').html( 
								'<div class="card base-card border p-3 bg-light mb-2" id="table_block">'+
									'<div class="">'+
										`<h4 class="my-2  pb-0" style="color:#AC39FD"><b>No Table Data Found within ${args.table_range} Day</b></h4>`+
										`<h6 class="my-2  pb-0" style="color:#AC39FD"><b>Increase Date range below and try again</b></h6>`+
										'<br>'+
										'<div>'+
											`<input name="table_range_change" id="table_range_change" type="text" placeholder="Range: Days Back" value="${args.table_range}">`+
											'<button class="button cust-btn" onclick="table_fetch()" return false>GO</button>'+
											'<br>'+
										'</div>'+
										'<div>'+
											'<div>'+
												'<div class="explanation_text">Update the value above to adjust the date range shown.</div>'+
												'</div>'+
											'</div>'+
										'</div>'+
									'</div>'
								);
						}
					}

          	//Submit data from your server

             // Ajax request
					

        }
    });
			}
			table_fetch();
			</script>
<script type="text/javascript">
	function graph_fetch(){

		var args = {};
		args.mode = "one";
		args.passed_data = "stuff"
		if ($('#graph_range_change').val()){
			args.graph_range = parseInt($('#graph_range_change').val());
		} else {
			args.graph_range = 7
		}
		if ($('#graph_type_change').val()){
			args.graph_type = $('#graph_type_change').val();
		} else {
			args.graph_type = "Purchase"
		}
		$('#graph_returned').html('<div>nada</div>');
		$('#graph_returned').html(`<div class=""><h4 class="my-2  pb-0" style="color:#AC39FD"><b>${args.graph_range} Day Real-Time Chart Loading</b></h4<br/><br/><br/><br/><img class="m-3" style="" src="/<?php echo $base ?>img/loading-54.gif" width=800 height=400 /></div>`);
		$.ajax({
			type: 'post',
			url: "/<?php echo $base ?>services/graph_fetch.php",
			data: args,
			success: function (data) {
				var graph_filling = ""
				data = JSON.parse(data)
				console.log(data.graph_data.length)
				if (data.graph_data.length > 0){
					console.log("the data", data)
				}
				let graph_data, labels
				if (data.graph_data.length > 0){
					graph_data = data["graph_data"];
					labels = graph_data.map((date) => date.date);
				}
				if (typeof($('#myChart_purchase_total')) != 'undefined'){
					$('#myChart_purchase_total').remove();
				}
				if(typeof($('#myChart_graph_data_events')) != 'undefined'){
					$('#myChart_graph_data_events').remove();

				}
				if(data.graph_data.length > 0){
					args.graph_type = data['graph_data'][0]['event_type']
				} else {
					args.graph_type = ""
				}
				var select_event_types = ""
				if (data['event_type'].length > 0){
					for (const row in data['event_type']) {
						if (args.graph_type == data['event_type'][row]['event_type']){
							select_event_types += `<option value="${data['event_type'][row]['event_type']}" selected>${data['event_type'][row]['event_type']}</option>`
						} else {
							select_event_types += `<option value="${data['event_type'][row]['event_type']}">${data['event_type'][row]['event_type']}</option>`
						}
					}
				} else {
					select_event_types += `<option value="" selected>No Data In Range</option>`
				}
				$('#graph_returned').html("")
				console.log(args.graph_type)
				var graph_container = ""
				var graph_header_container = ""
				if(args.graph_type == "Purchase"){
					graph_header_container += `<h4 class="my-2  pb-0" style="color:#AC39FD"><b>${args.graph_range} Day ${args.graph_type} Charts</b></h4>`
					graph_container += '<div class="chart_container">'
					// graph_container += '<div class="card base-card border p-3 bg-light mb-2">'
					graph_container += '<canvas id="myChart_purchase_total"></canvas>'
					// graph_container += '</div>'
					graph_container += '</div>'
					graph_container += '<div class="chart_container">'
					// graph_container += '<div class="card base-card border p-3 bg-light mb-2">'
					graph_container += '<canvas id="myChart_graph_data_events"></canvas>'
					// graph_container += '</div>'
					graph_container += '</div>'
				} else if (args.graph_type != ""){
					graph_header_container += `<h4 class="my-2  pb-0" style="color:#AC39FD"><b>${args.graph_range} Day ${args.graph_type} Chart</b></h4>`
					graph_container += '<div class="chart_container">'
					// graph_container += '<div class="card base-card border p-3 bg-light mb-2">'
					graph_container += '<canvas id="myChart_graph_data_events"></canvas>'
					// graph_container += '</div>'
					graph_container += '</div>'
				} else {
					graph_container += '<div class="chart_container">'
					// graph_container += '<div class="card base-card border p-3 bg-light mb-2">'
					graph_container += 'Increase Date Range and Try Again'
					// graph_container += '</div>'
					graph_container += '</div>'
				}
				if (data.graph_data.length > 0){

					$('#graph_returned').html( 
						'<div>'+
						'<div name="graph_range_change_form" id="graph_range_change_form">'+
						`${graph_header_container}`+
							'<div>'+
								`<input name="graph_range_change" id="graph_range_change" type="text" placeholder="Range: Days Back" value="${args.graph_range}">`+
								'<select name="graph_type_change" id="graph_type_change" type="text">'+
								`${select_event_types}`+
								'</select>'+
								'<button class="button cust-btn" id="graph_button" onclick="graph_fetch()" return false>'+
									'GO'+
								'</button>'+
								'<div>'+
									'<div class="explanation_text">Update the values above to adjust the date range/event type shown.</div>'+
									'</div>'+
								'</div>'+

							'</div>'+
						'</div>'+
							'<div class="charts_container" id="graph_block">'+
								`${graph_container}`+
						'</div>'+
						'</div>'
					);
					if (graph_data[0]['event_type'] == 'Purchase'){
	
						const data_purchase_total = {
							labels: labels,
							datasets: [{
								label: "Purchase Data Total",
								fill: true,
								lineTension: 0,
								backgroundColor: "rgba(171, 57, 253, .5)",
								borderColor: "rgba(0, 0, 0, 1)",
								borderWidth: 2,
								data: graph_data.map((total) => total["Total Revenue"]),
							}],
						}
	
						const data_graph_data_events = {
							labels: labels,
							datasets: [{
								label: `Total ${graph_data[0]["event_type"]} Events`,
								fill: true,
								hidden: false,
								lineTension: 0,
								backgroundColor: "rgba(171, 57, 253, .5)",
								color: "rgba(192, 60, 132, 0.8)",
								borderColor: "rgba(0, 0, 0, 0.8)",
								borderWidth: 2,
								data: graph_data.map((total) => total["Total Events"]),
							}],
						}
	
						const config_purchase_total = {
						type: 'line',
						data: data_purchase_total,
						options: {
							animation: false,
							scales: {
									y: {
										ticks: {
												// Include a dollar sign in the ticks
											callback: function(value, index, ticks) {
													return '$' + value;
											}
										}
									}
								}
							}
						};
						const config_graph_data_events = {
							type: 'line',
							data: data_graph_data_events,
							options: {
								animation: false,
							}
						};
						const myChart_purchase_total = new Chart(
							document.getElementById('myChart_purchase_total'),
							config_purchase_total
						);
						const myChart_graph_data_events = new Chart(
							document.getElementById('myChart_graph_data_events'),
							config_graph_data_events
						);
					} else {
	
						const data_graph_data_events = {
							labels: labels,
							datasets: [{
								label: `Total ${graph_data[0]["event_type"]} Events`,
								fill: true,
								hidden: false,
								lineTension: 0,
								backgroundColor: "rgba(171, 57, 253, .5)",
								color: "rgba(192, 60, 132, 0.8)",
								borderColor: "rgba(0, 0, 0, 0.8)",
								borderWidth: 2,
								data: graph_data.map((total) => total["Total Events"]),
							}],
						}
						const config_graph_data_events = {
							type: 'line',
							data: data_graph_data_events,
							options: {
								animation: false,
							}
						};
						const myChart_graph_data_events = new Chart(
							document.getElementById('myChart_graph_data_events'),
							config_graph_data_events
						);
					}
				} else {
					if(parseInt(args.graph_range) > 1) {
						$('#graph_returned').html( 
							'<div>'+
								'<div name="graph_range_change_form" id="graph_range_change_form">'+
									`<h4 class=" pb-0" style="color:#AC39FD"><b>No Graph Data Found Within ${args.graph_range} Days</b></h4>`+
									`<h6 class=" pb-0" style="color:#AC39FD">Increase Date range below and try again</h6>`+
									'<div>'+
										'<br>'+
										`<input name="graph_range_change" id="graph_range_change" type="text" placeholder="Range: Days Back" value="${args.graph_range}">`+
										'<button class="button cust-btn" id="graph_button" onclick="graph_fetch()" return false>'+
											'GO'+
										'</button>'+
									'</div>'+
								'</div>'+
								'<div>'+
									'<div class="explanation_text">Update the value above to adjust the date range shown.</div>'+
									'</div>'+
								'</div>'+
							'</div>'
						);
					} else {
						$('#graph_returned').html( 
							'<div>'+
								'<div name="graph_range_change_form" id="graph_range_change_form">'+
									`<h4 class=" pb-0" style="color:#AC39FD"><b>No Graph Data Found Within ${args.graph_range} Day</b></h4>`+
									`<h6 class=" pb-0" style="color:#AC39FD">Increase Date range below and try again</h6>`+
									'<div>'+
										'<br>'+
										`<input name="graph_range_change" id="graph_range_change" type="text" placeholder="Range: Days Back" value="${args.graph_range}">`+
										'<button class="button cust-btn" id="graph_button" onclick="graph_fetch()" return false>'+
											'GO'+
										'</button>'+
									'</div>'+
								'</div>'+
								'<div>'+
									'<div class="explanation_text">Update the value above to adjust the date range shown.</div>'+
									'</div>'+
								'</div>'+
							'</div>'
						);
					}
				}
			}
		});
}
			graph_fetch();
			</script>
	</body>
</html>
 