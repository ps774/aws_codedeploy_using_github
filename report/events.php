<?php
if(!isset($_SESSION)) 
{ 
  session_start(); 
} 
include('php/db.php');
include('php/templates.php');

if( $_SESSION['user']['status'] == 'logged_in' && !empty($_SESSION['user']['email']) ){
	$mode = 1;
} else {
	//var_dump($_SESSION);
	header('Location: /'.$base.'index.php');
	exit();
}

$sql = 'SELECT *, A.sha_token as `token` FROM accounts A LEFT JOIN fb_settings F ON A.sha_token = F.sha_token WHERE A.sha_token = "'.$_SESSION['user']['id'].'";';
$db_data = sql_select_to_array($sql);


$sql = 'SELECT * FROM events WHERE account_token = "'.$db_data[0]['token'].'";';
$events = sql_select_to_array($sql);

if( !empty($_POST['event_type']) ){
	
	$event_type = $_POST['event_type'];
	
	$valid = true;
	
	if( !empty($_POST['event_trigger']) ){
		$trigger = $_POST['event_trigger'];
	} else {
		$valid = false;
	}
	
	if( !empty($_POST['selector_query']) ){
		$selector_query = $_POST['selector_query'];
	} else {
		$valid = false;
	}
	
	if( !empty($_POST['event_value']) ){
		$event_value = $_POST['event_value'];
	} else {
		$event_value = '0';
	}

	
	if( $valid ){
		$sql = 'INSERT INTO events (account_token, event_trigger, selector_query, event_type, event_value, created) VALUES ( "'.$db_data[0]['token'].'","'.$trigger.'","'.$selector_query.'","'.$event_type.'","'.$event_value.'","'.date("Y-m-d").'" );';
		
		$results = sqli_query($sql);
		
		if($results){
			$_GET['success'] = 1;
			
			$sql = 'SELECT * FROM events WHERE account_token = "'.$db_data[0]['token'].'";';
			$events = sql_select_to_array($sql);
			
		} else {
			$_GET['error'] = 1;
		}
	}
}

$domain = $db_data[0]['domain'];
$pixel_id = $db_data[0]['pixel_id'];
$access_token = $db_data[0]['access_token'];
$test_event_code = $db_data[0]['test_event_code'];


if( !empty($pixel_id) && !empty($access_token) && !empty($test_event_code) && !empty($events[0]['event_type'])){
	$capi_ready = true;
} else {
	$capi_ready = false;
}


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
	</style>
	<link rel="apple-touch-icon" sizes="180x180" href="/<?php echo $base;?>favicon/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/<?php echo $base;?>favicon/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/<?php echo $base;?>favicon/favicon-16x16.png">
	<link rel="manifest" href="/<?php echo $base;?>favicon/site.webmanifest">
	<link rel="mask-icon" href="/<?php echo $base;?>favicon/safari-pinned-tab.svg" color="#5bbad5">
	<meta name="msapplication-TileColor" content="#da532c">
	<meta name="theme-color" content="#ffffff">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body class="m-3" style="background: #FFFFFF;">
	<a href="/<?php echo $base;?>index.php"><img src="/<?php echo $base;?>img/popsixle_logo2.png" width="205" height="52" style="margin:0 0 10px;" alt="Popsixle Logo" border="0" /></a>
		<!-- <p class="pt-2" style="margin-left:2px;"><b>Back to the good old days.</b></p> -->

	
	
	<?php
	if( !empty($_SESSION['user']['email']) ){
		echo '<div id="log_out">'.$_SESSION['user']['username'].' | <a href="/'.$base.'php/logout.php">logout</a></div>';
	}	
	?>
			<div class="p-3 mb-2 card base-card border text-white bg-danger" id="error_block" style="display:none;">
			</div>
			
			<div class="p-3 mb-2  card base-card border text-white bg-success" id="success_block" style="display:none;">
			</div>

			<?php echo navbar();?>
			
			
			<?php
			if(!$capi_ready){
				echo '<!--';
			}
			?>
			<div class="card base-card bg-light border p-3 my-2">
				<div class="">
					<h4 class="my-2  pb-2" style="color:#AC39FD"><b>Your Events</b></h4>
						
				</div>
				
				
			<?php
			
			foreach( $events as $id => $e ){
				$uid = $e['event_type'].'_'.$e['event_trigger'].'_'.$e['selector_query'];
				$uid = str_ireplace(' ', '_', $uid);
				$uid = str_ireplace('-', '_', $uid);
				$uid = str_ireplace('#', '', $uid);
				$uid = str_ireplace('.', '', $uid);
				echo '<h5 class="mb-1 mt-4"><b>Event '.($id+1).':</b></h5>';
				echo '<div class="card base-card bg-light bg-dark text-white border p-3 mt-2">';
				echo '<p><small>';
				echo '<b>Event Type:</b> '.$e['event_type'];
				echo '<br/><b>Event Trigger:</b> '.$e['event_trigger'];
				echo '<br/><b>Event Query/Selector:</b> '.$e['selector_query'];
				echo '<br/><b>Event Value:</b> '.$e['event_value'];
				echo '<br/><b>Created:</b> '.$e['created'];
				echo '</small></p>';
				
				echo '
				<div id="'.$uid.'_capi_response" class="py-2"></div>
				<div class="text-center row px-2">
					
					<div class="col px-1">
						<button class="btn btn-block cust-btn text-center" onclick="send_test_event(\''.$e['event_type'].'\',\''.$e['event_value'].'\',\''.$uid.'\');return false;">Send Test Event</button>
					</div>
					<div class="col px-1">
						<button class="btn btn-block btn-danger text-center" onclick="delete_event('.$e['id'].');return false;">Delete</button>
					</div>
					<div class="col px-1 too-small-hide"></div>
					<div class="col px-1 too-small-hide"></div>
				</div>
				';
				
				echo '</div>';
			}	
				
				
			?>

			</div>
			<?php
			if(!$capi_ready){
				echo '-->';
			}
			?>
			
				
			<div class="card base-card bg-light border p-3">
				<div class="">
					<h4 class="my-2  pb-2" style="color:#AC39FD"><b>New Event Creation</b></h4>
				</div>
				<form id="event" action="/<?php echo $base;?>events.php" method="post" autocomplete="off">					
					
					<div class="form-group">
						<label for="form-check" class=""><b>Event Trigger:</b></label>
						<div class="form-check">
							<input class="form-check-input" type="radio" name="event_trigger" id="event_trigger_url" value="url" checked>
							<label class="form-check-label" for="event_trigger_url">URL event</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="radio" name="event_trigger" id="event_trigger_click" value="click">
							<label class="form-check-label" for="event_trigger_click">Click event</label>
						</div>
				  	</div>
					
				  	
				  	<div class="form-group">
				    	<label id="selector_query_label" for="domain" class="blue-text"><b>URL match string:</b></label>
						<input type="text" class="form-control" id="selector_query" name="selector_query" placeholder="Enter the URL match str (example: checkout.php)" minlength="3" required >
				  	</div>
				  	
				  	<div class="form-group">
				    	<label for="event_type" class="blue-text"><b>Event Type:</b></label>
						<select class="form-control" name="event_type" id="event_type" required>
							<option value="">-- Select a Standard FB Event --</option>
							<option value="PageView">Page View</option>
							<option value="ViewContent">View Content</option>
							<option value="AddToCart">Add To Cart</option>
							<option value="InitiateCheckout">Initiate Checkout</option>
							<option value="Purchase">Purchase</option>
							<option value="Lead">Lead</option>
							<option value="Subscribe">Subscribe</option>
							<option value="Search">Search</option>
							<option value="CompleteRegistration">Complete Registration</option>
						</select>
				  	</div>
				  	
				  	<div class="form-group">
				    	<label for="event_value" class="blue-text"><b>Event Value:</b><br/><small class="text-secondary">(optional - required for Purchase events only)</small></label>
						<input type="text" class="form-control" id="event_value" name="event_value" placeholder="Event Value in USD (example: 100.00)" autocomplete="new-password" >
				  	</div>
				  	
				  	<div class="text-center row px-2">
					  	
							<div class="col px-1 too-small-hide"></div>
							<div class="col px-1">
								<button type="submit" class="btn btn-block cust-btn text-center">Create Event</button>
							</div>
							<div class="col px-1 too-small-hide"></div>
				  	</div>
				</form>
			</div>
			
			
			

			
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>	
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script>

			var base = "<?php echo $base;?>";
			
			$(function() {
				<?php
			
				if( !empty($_GET['success']) ) {
					echo '
					show_success( \''.$_GET['success'].'\' );';	
					
				}
				
				if( !empty($_GET['error']) ) {
					echo '
					show_error( \''.$_GET['error'].'\' );';	
				}
					
				?>
			});
			/*
			$('#changePassword').validate({
				rules: {
					password: "required",
					password_again: {
						equalTo: "#password"
					}
				}
			});
			*/
			
			$('input[type=radio][name=event_trigger]').change(function(){
				if($(this).val() == 'url'){
					$('#selector_query_label').html('<b>URL match string:</b>');
					$('#selector_query').attr("placeholder", "Enter the URL match str (example: checkout.php)");
				} else {
					$('#selector_query_label').html('<b>jQuery Object Selector ID/Class:</b>');
					$('#selector_query').attr("placeholder", "Enter the Object Selector (example: #submit_button or .submit-btn)");
				}
			});
			
			function show_success( msg ){
				msg = 'Your event was successfully created.';
				
				$('#success_block').html('<p class="mb-0">'+msg+'</p><div class="x-close rounded">x</div>').show();
				$("html, body").animate({ scrollTop: 0 }, "slow");
				
				$( '.x-close' ).click(function(){
					$('#success_block').hide();
				});

			}
			
			function show_error( msg ){
				msg = 'Something went wrong. Please try again.';
				
				
				$('#error_block').html('<p class="mb-0">'+msg+'</p><div class="x-close rounded">x</div>').show();
				//$("html, body").animate({ scrollTop: 0 }, "slow");
				
				$( '.x-close' ).click(function(){
					$('#error_block').hide();
				});

			}
			
			function send_test_event(event_type, event_value, uid){
				var args = {};
				args.mode = 'ping_capi';
				args.event_type = event_type;
				args.event_value = event_value;
				args.t2 = '<?php echo $db_data[0]['pixel_id'];?>';
				args.t3 = '<?php echo $db_data[0]['access_token'];?>';
				args.t4 = '<?php echo $db_data[0]['test_event_code'];?>';
				
				$.post( "/<?php echo $base;?>pop6_ping.php", args, function( data ) {
					
					data = JSON.parse(data);
					
					if( typeof data.response != 'undefined' ){
						
						if( typeof data.response.fbtrace_id != 'undefined' ){
							var markup = '<p><b>CAPI response:</b><br/>Your test event was received ( '+event_type+', '+event_value+' value).</p>';
						} 
						
						if(typeof data.response.error != 'undefined') {
							var markup = '<p><b>CAPI response:</b><br/><span class="text-red">Your event was not received. Error: '+data.response.error.message +'</span></p>';
						}
						
						$('#'+uid+'_capi_response').html(markup);
						
					} else if( typeof data.debug != 'undefined' ){
						console.log(data.debug);
					}
				});
			}
			
			function delete_event(id){
				var args = {};
				args.mode = 'delete_event';
				args.id = id;
				
				$.post( "/<?php echo $base;?>pop6_ping.php", args, function( data ) {
					
					window.location.href = '/'+base+'events.php';
					
				});
			}
			
		</script>

	</body>
</html>