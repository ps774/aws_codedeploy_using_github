<?php
if(!isset($_SESSION)) 
{ 
  session_start(); 
} 
include('php/db_read_replica.php');
include('php/templates.php');
$active_page = $_SERVER['REQUEST_URI'];
if( $_SESSION['user']['status'] == 'logged_in' && !empty($_SESSION['user']['email']) && $_SESSION['user']['type'] == '99' ){
	$mode = 1;
} else {
	//var_dump($_SESSION);
	header('Location: /'.$base.'index.php');
	exit();
}

if(!empty($_GET['graph_purchase_range'])){
	$table_range = $_GET['graph_purchase_range'];
} else {
	$table_range = 12;
}
$_SESSION['graph_purchase_range'] = $graph_purchase_range;

$sql = "SELECT id, account_name FROM popsixle.accounts WHERE expires > NOW() AND type != 99 ORDER BY account_name ASC;";
$accounts = sql_select_to_array_replica($sql);

?>
<script>
  var accounts = <?php echo json_encode($accounts); ?>;
</script>
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
	/* width: 10%; */
	min-width: 400px;
	margin: 10px;
}
#graph_type_change{
	margin-right: 5px;
	height: 31px;
	width: 150px;
}
#graph_purchase_range_change{
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
#graph_purchase_range_change_form{
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
  display: flex;
  flex-wrap: wrap;

}
#full_purchase_graph{
	justify-content: center;
	align-items: center;
	text-align: center;
  display: flex;
  flex-wrap: wrap;
  min-width: 800px;
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
	<nav class="navbar navbar-light bg-light">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
			<span class="fas fa-bars"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbar">
			<ul class="sidenav-menu">
		<?php
			foreach( $db_data_shops as $row ){
				echo '<li class="sidenav-item" >
				<a class="nav-item nav-link" href="/'.$base.'admin_events_manager.php/?shop='.$row["sha_token"].'">
					'.$row['account_name'].'</a>
			</li>';
			}
		?>
		</ul>
	</div>
</nav>
	
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
					<h2 class="my-0  pb-1" style="color:#AC39FD"><b>Purchase Log</b></h2>
				</div>				
			</div>
			<div class="card base-card border p-3 bg-light mb-2" id="graph_block" >
        <div id="graph_header_returned">

        </div>
        <div id="full_purchase_returned">

        </div>
				<div id="graph_returned" >

				</div>

			</div>

<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>	
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<script type="text/javascript">
	function graph_fetch(){
    var first_loop = 0
    var args = {};

    if ($('#graph_purchase_range_change').val()){
      args.graph_purchase_range = parseInt($('#graph_purchase_range_change').val());
    } else {
      args.graph_purchase_range = 12
    }
    $('#graph_returned').html('<div>nada</div>');
    $('#full_purchase_returned').html('<div></div>');
    
    $('#graph_returned').html(`<div class=""><h4 class="my-2  pb-0" style="color:#AC39FD"><b>${args.graph_purchase_range} Hour Real-Time Chart Loading</b></h4<br/><br/><br/><br/><img class="m-3" style="" src="/<?php echo $base ?>img/loading-54.gif" width=800 height=400 /></div>`);
    if (accounts.length > 0){
      accounts.forEach(account => {
        var graph_container = ""
        var graph_header_container = ""
        args.mode = 1
        if (account['id']){
          args.account_id = account['id']
        }
        $.ajax({
          type: 'post',
          url: "/<?php echo $base ?>services/purchase_log_fetch.php",
          data: args,
          success: function (data) {
            var graph_filling = ""
            data = JSON.parse(data)
            let graph_data, labels
            if (data.length > 0){
              graph_data = data;
              labels = graph_data.map((date) => date['interval']);
            }
            // if (typeof($(`#${account['account_id']}`)) != 'undefined'){
            //   $(`#${account['account_id']}`).remove();
            //   console.log("account id here")
            // }

              graph_header_container += `<h4 class="my-2  pb-0" style="color:#AC39FD"><b>${args.graph_purchase_range} Hour Purchase Charts</b></h4>`
              graph_container += '<div class="chart_container">'
              // graph_container += '<div class="card base-card border p-3 bg-light mb-2">'
              graph_container += `<canvas id="shop${account['id']}"></canvas>`
              // graph_container += '</div>'
              graph_container += '</div>'
            if (data.length > 0){
              if (first_loop == 0){
                $('#graph_header_returned').html( 
                  '<div>'+
                  '<div name="graph_purchase_range_change_form" id="graph_purchase_range_change_form">'+
                  `${graph_header_container}`+
                    '<div>'+
                      `<input name="graph_purchase_range_change" id="graph_purchase_range_change" type="text" placeholder="Range: Days Back" value="${args.graph_purchase_range}">`+
                      '<button class="button cust-btn" id="graph_button" onclick="graph_fetch()" return false>'+
                        'GO'+
                      '</button>'+
                      '<div>'+
                        '<div class="explanation_text">Update the values above to adjust the date range/event type shown.</div>'+
                        '</div>'+
                      '</div>'
                );
                $('#graph_returned').html( 
                    '</div>'+
                  '</div>'+
                    '<div class="charts_container" id="graph_block">'+
                      `${graph_container}`+
                  '</div>'+
                  '</div>'
                );
                first_loop++
              } else {
                $('#graph_returned').append( 
                  '<div>'+
                    '<div class="charts_container" id="graph_block">'+
                      `${graph_container}`+
                  '</div>'
                );
              }
              const data_graph_data_events = {
                labels: labels,
                datasets: [{
                  label: `${account['account_name']}`,
                  fill: true,
                  hidden: false,
                  lineTension: 0,
                  backgroundColor: "rgba(171, 57, 253, .5)",
                  color: "rgba(192, 60, 132, 0.8)",
                  borderColor: "rgba(0, 0, 0, 0.8)",
                  borderWidth: 2,
                  data: graph_data.map((total) => total["purchases"]),
                }],
              }
              const config_graph_data_events = {
                type: 'line',
                data: data_graph_data_events,
                options: {
                  animation: false,
                  // scales: {
                  //   yAxes: [
                  //       {
                  //         ticks:
                  //         {
                  //           min: 1
                  //         }
                  //       }
                  //     ]
                  // }
                }
                
              };
              const myChart_graph_data_events = new Chart(
                document.getElementById(`shop${account['id']}`),
                config_graph_data_events
              );
            }
          }
        })
      });
      var full_graph_container = ""
      args.mode = 2;
      $.ajax({
        type: 'post',
        url: "/<?php echo $base ?>services/purchase_log_fetch.php",
        data: args,
        success: function (data) {
          var graph_filling = ""
          data = JSON.parse(data)
          let graph_data, labels
          if (data){
            graph_data = data.graph_data;
            graph_data2 = data.graph_data2;
            labels = graph_data.map((date) => date['interval']);
          }
          // if (typeof($(`#${account['account_id']}`)) != 'undefined'){
          //   $(`#${account['account_id']}`).remove();
          //   console.log("account id here")
          // }

            full_graph_container += '<div class="chart_container">'
            // full_graph_container += '<div class="card base-card border p-3 bg-light mb-2">'
            full_graph_container += `<canvas id="full_purchase_graph"></canvas>`
            // full_graph_container += '</div>'
            full_graph_container += '</div>'
          if (data.graph_data.length > 0){
            $('#full_purchase_returned').append( 
              '<div>'+
              '<div class="charts_container" id="graph_block">'+
              `${full_graph_container}`+
              '</div>'
              );
              console.log(graph_data)
              console.log(graph_data2)
              let data_graph_data_events2
              if (graph_data.length == graph_data2.length) {
              data_graph_data_events2 = {
                labels: labels,
                datasets: [
                  {
                  label: `Previous ${args.graph_purchase_range} Hours`,
                  fill: true,
                  hidden: false,
                  lineTension: 0,
                  backgroundColor: "rgba(171, 57, 253, .5)",
                  color: "rgba(192, 60, 132, 0.8)",
                  borderColor: "rgba(0, 0, 0, 0.8)",
                  borderWidth: 2,
                  data: graph_data.map((total) => total["purchases"]),
                },
                  {
                  label: `${graph_data2[0]["interval"]} - ${graph_data2[graph_data2.length - 1]["interval"]}`,
                  fill: true,
                  hidden: true,
                  lineTension: 0,
                  backgroundColor: "rgba(247, 143, 40, .5)",
                  color: "rgba(244, 230, 255, 0.8)",
                  borderColor: "rgba(0, 0, 0, 0.8)",
                  borderWidth: 2,
                  data: graph_data2.map((total) => total["purchases"]),
                },
              ]}
              console.log(data_graph_data_events2)
            } else {              
              data_graph_data_events2 = {
                labels: labels,
                datasets: [{
                  label: `All Accounts`,
                  fill: true,
                  hidden: false,
                  lineTension: 0,
                  backgroundColor: "rgba(171, 57, 253, .5)",
                  color: "rgba(192, 60, 132, 0.8)",
                  borderColor: "rgba(0, 0, 0, 0.8)",
                  borderWidth: 2,
                  data: graph_data.map((total) => total["purchases"]),
                }],
              }
            }
            const config_graph_data_events = {
              type: 'line',
              data: data_graph_data_events2,
              options: {
                animation: false,
              }
              
            };
            const myChart_graph_data_events = new Chart(
              document.getElementById(`full_purchase_graph`),
              config_graph_data_events
            );
          }
        }
      })
    }
  }
  graph_fetch();
</script>
</body>
</html>
 