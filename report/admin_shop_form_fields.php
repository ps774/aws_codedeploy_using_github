<?php
if(!isset($_SESSION)) 
{ 
  session_start(); 
} 
include('php/db.php');
include('php/templates.php');

//var_dump($_POST);


if( $_SESSION['user']['status'] == 'logged_in' && !empty($_SESSION['user']['email']) && $_SESSION['user']['type'] == '99' ){
	$mode = 1;
} else {
	//var_dump($_SESSION);
	header('Location: /'.$base.'index.php');
	exit();
}

$sql = 'SELECT *, A.sha_token as `token` FROM accounts A LEFT JOIN fb_settings F ON A.sha_token = F.sha_token WHERE A.sha_token = "'.$_GET['shop'].'";';
$db_data = sql_select_to_array($sql);

$sql = 'SELECT * FROM accounts WHERE type != "99" AND type != "50" AND "'.date("Y-m-d").'" <= expires ORDER BY account_name ASC;';
$db_data_shops = sql_select_to_array($sql);

$sql = 'SELECT * FROM accounts WHERE sha_token = "'.$db_data[0]['token'].'";';
$db_account_name = sql_select_to_array($sql);


$sql = 'SELECT * FROM form_fields WHERE account_token = "'.$db_data[0]['token'].'";';
$form_fields = sql_select_to_array($sql);

//var_dump($sql);
//var_dump($form_fields);

if( !empty($_POST['field_selector']) ){
	
	$field_selector = $_POST['field_selector'];
	
	$valid = true;
	
	if( !empty($_POST['field_type']) ){
		$field_type = $_POST['field_type'];
	} else {
		$valid = false;
	}

	if( $valid ){
		
		$sql_check_dup = 'SELECT * FROM form_fields WHERE account_token = "'.$db_data[0]['token'].'" AND field_selector = "'.$field_selector.'" AND field_type = "'.$field_type.'"';
		$results_check_dup = sql_select_to_array($sql_check_dup);

		if (empty($results_check_dup[0])){
			$sql = 'INSERT INTO form_fields (account_token, field_selector, field_type,created) VALUES ( "'.$db_data[0]['token'].'","'.$field_selector.'","'.$field_type.'","'.date("Y-m-d").'" );';
			$results = sqli_query($sql);
			
			//var_dump($sql);
			
			if($results){
				$_GET['success'] = 1;
				
				$sql = 'SELECT * FROM form_fields WHERE account_token = "'.$db_data[0]['token'].'";';
				$form_fields = sql_select_to_array($sql);
				
			} else {
				$_GET['error'] = 1;
			}
		} else {
			$_GET['duplicate'] = 1;
		}
	}
}
if( !empty($_GET['shopify']) ){
		$sql_check_dup1 = 'SELECT * FROM form_fields WHERE account_token = "'.$db_data[0]['token'].'" AND field_selector = "email" AND field_type = "em"';
		$results_check_dup1 = sql_select_to_array($sql_check_dup1);
		if (empty($results_check_dup1[0])){
			$sql1 = 'INSERT INTO form_fields (account_token, field_selector, field_type,created) VALUES ( "'.$db_data[0]['token'].'","email","em","'.date("Y-m-d").'" );';
			$results1 = sqli_query($sql1);
		}
		$sql_check_dup2 = 'SELECT * FROM form_fields WHERE account_token = "'.$db_data[0]['token'].'" AND field_selector = "customer[email]" AND field_type = "em"';
		$results_check_dup2 = sql_select_to_array($sql_check_dup2);
		if (empty($results_check_dup2[0])){
			$sql2 = 'INSERT INTO form_fields (account_token, field_selector, field_type,created) VALUES ( "'.$db_data[0]['token'].'","customer[email]","em","'.date("Y-m-d").'" );';
			$results2 = sqli_query($sql2);
		}
		$sql_check_dup3 = 'SELECT * FROM form_fields WHERE account_token = "'.$db_data[0]['token'].'" AND field_selector = "contact[email]" AND field_type = "em"';
		$results_check_dup3 = sql_select_to_array($sql_check_dup3);
		if (empty($results_check_dup3[0])){
			$sql3 = 'INSERT INTO form_fields (account_token, field_selector, field_type,created) VALUES ( "'.$db_data[0]['token'].'","contact[email]","em","'.date("Y-m-d").'" );';
			$results3 = sqli_query($sql3);
		}
		$sql_check_dup4 = 'SELECT * FROM form_fields WHERE account_token = "'.$db_data[0]['token'].'" AND field_selector = "customer[first_name]" AND field_type = "fn"';
		$results_check_dup4 = sql_select_to_array($sql_check_dup4);
		if (empty($results_check_dup4[0])){
			$sql4 = 'INSERT INTO form_fields (account_token, field_selector, field_type,created) VALUES ( "'.$db_data[0]['token'].'","customer[first_name]","fn","'.date("Y-m-d").'" );';
			$results4 = sqli_query($sql4);
		}
		$sql_check_dup5 = 'SELECT * FROM form_fields WHERE account_token = "'.$db_data[0]['token'].'" AND field_selector = "customer[last_name]" AND field_type = "ln"';
		$results_check_dup5 = sql_select_to_array($sql_check_dup5);
		if (empty($results_check_dup5[0])){
			$sql5 = 'INSERT INTO form_fields (account_token, field_selector, field_type,created) VALUES ( "'.$db_data[0]['token'].'","customer[last_name]","ln","'.date("Y-m-d").'" );';
			$results5 = sqli_query($sql5);
		}
		$sql_check_dup6 = 'SELECT * FROM form_fields WHERE account_token = "'.$db_data[0]['token'].'" AND field_selector = "contact[phone]" AND field_type = "ph"';
		$results_check_dup6 = sql_select_to_array($sql_check_dup6);
		if (empty($results_check_dup6[0])){
			$sql6 = 'INSERT INTO form_fields (account_token, field_selector, field_type,created) VALUES ( "'.$db_data[0]['token'].'","contact[phone]","ph","'.date("Y-m-d").'" );';
			$results6 = sqli_query($sql6);
		}

		if($results1 || $results2 || $results3 || $results4 || $results5 || $results6){
			$_GET['success'] = 1;
			
			$sql = 'SELECT * FROM form_fields WHERE account_token = "'.$db_data[0]['token'].'";';
			$form_fields = sql_select_to_array($sql);
			
		} else {
			$_GET['error'] = 1;
			$sql = 'SELECT * FROM form_fields WHERE account_token = "'.$db_data[0]['token'].'";';
			$form_fields = sql_select_to_array($sql);

		}
	}


$domain = $db_data[0]['domain'];
$pixel_id = $db_data[0]['pixel_id'];
$access_token = $db_data[0]['access_token'];
$test_event_code = $db_data[0]['test_event_code'];


if( !empty($pixel_id) && !empty($access_token) && !empty($test_event_code) && !empty($form_fields[0]['field_selector'])){
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
	<title>Pop6 Admin Form Fields</title>
	<script src="https://kit.fontawesome.com/739f699b00.js" crossorigin="anonymous"></script>

</head>
<body class="m-3" style="background: #FFFFFF;">
	<a href="/<?php echo $base;?>index.php"><img src="/<?php echo $base;?>img/popsixle_logo2.png" width="205" height="52" style="margin:0 0 10px;" alt="Popsixle Logo" border="0" /></a>
		<!-- <p class="pt-2" style="margin-left:2px;"><b>Back to the good old days.</b></p> -->
		<nav class="navbar navbar-light bg-light">
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
			<span class="fas fa-bars"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbar">
			<ul class="sidenav-menu">
		<?php
			foreach( $db_data_shops as $row ){
				if (!empty($row['account_name'])){
					echo '<li class="sidenav-item" >
					<a class="nav-item nav-link" href="/'.$base.'admin_shop_account.php/?shop='.$row["sha_token"].'">
						'.$row['account_name'].'</a>
				</li>';
				} else {
					echo '<li class="sidenav-item" >
					<a class="nav-item nav-link" href="/'.$base.'admin_shop_account.php/?shop='.$row["sha_token"].'">
						'.$row['username'].'</a>
				</li>';;
				}
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
			
			
			<?php
			if(!$capi_ready){
				echo '<!--';
			}
			?>
			<div class="card base-card bg-light border p-3 my-2 mb-2 text-center">
				<div class="text-center">
					<h3 class="my-2  pb-2 text-center" style="color:#AC39FD"><b><?php echo $db_account_name[0]["account_name"]; ?></b></h3>
						
				</div>
			</div>
			<div class="card base-card bg-light border p-3 my-2 mb-2">
				<div class="">
					<h4 class="my-2  pb-2" style="color:#AC39FD"><b>Your Form Fields</b></h4>
						
				</div>
				
				
			<?php
			
			foreach( $form_fields as $id => $e ){
				$uid = $e['field_selector'].'_'.$e['field_type'];
				$uid = str_ireplace(' ', '_', $uid);
				$uid = str_ireplace('-', '_', $uid);
				$uid = str_ireplace('#', '', $uid);
				$uid = str_ireplace('.', '', $uid);
				echo '<h5 class="mb-1 mt-4"><b>Form Field '.($id+1).':</b></h5>';
				echo '<div class="card base-card bg-light bg-dark text-white border p-3 mt-2">';
				echo '<p><small>';
				echo '<b>field_selector:</b> '.$e['field_selector'];
				echo '<br/><b>field_type:</b> '.$e['field_type'];
				echo '<br/><b>Created:</b> '.$e['created'];
				echo '</small></p>';
				
				echo '
				<div id="'.$uid.'_capi_response" class="py-2"></div>
				<div class="text-center row px-2">
					

					<div class="col px-1">
						<button class="btn btn-block btn-danger text-center" onclick="delete_form_field('.$e['id'].');return false;">Delete</button>
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
			
				
			<div class="card base-card bg-light border p-3 mb-2">
				<div class="">
					<h4 class="my-2  pb-2" style="color:#AC39FD"><b>New Form Field</b></h4>
				</div>
				<form id="event" action="/<?php echo $base;?>admin_shop_form_fields.php?shop=<?php echo $_GET['shop'];?>" method="post" autocomplete="off">					
					
				  	
				  	<div class="form-group">
				    	<label id="selector_query_label" for="domain" class="blue-text"><b>Form Input Object Selector:</b></label>
						<input type="text" class="form-control" id="field_selector" name="field_selector" placeholder="Enter the field selector (example: .first-name or #first_name)" minlength="3" required >
				  	</div>
				  	
				  	<div class="form-group">
				    	<label for="field_type" class="blue-text"><b>Field Type:</b></label>
						<select class="form-control" name="field_type" id="field_type" required>
							<option value="">-- Select a Field Type --</option>
							<option value="fn">First Name</option>
							<option value="ln">Last Name</option>
							<option value="ph">Phone Number</option>
							<option value="em">Email</option>
						</select>
				  	</div>
				  	
				  	<div class="text-center row px-2">
					  	
							<div class="col px-1 too-small-hide"></div>
							<div class="col px-1">
								<button type="submit" class="btn btn-block cust-btn text-center">Create Form Field</button>
							</div>
							
							<div class="col px-1 too-small-hide"></div>
				  	</div>
				</form>
			</div>
			<div class="card base-card bg-light border p-3 mb-2">
				<div class="">
					<h4 class="my-2  pb-2" style="color:#AC39FD"><b>Shopify?</b></h4>
				</div>
				<form id="event" action="/<?php echo $base;?>admin_shop_form_fields.php?shop=<?php echo $_GET['shop'];?>&shopify=true" method="post" autocomplete="off">					
				  	<div class="form-group">
				    	<label id="selector_query_label" for="domain" class="blue-text"><b>Click Below to add common Shopify form field selectors</b></label>						
							<div class="text-center row px-2">
								<div class="col px-1">
									<button type="submit" class="btn btn-block cust-btn text-center">Create Shopify Custom Fields</button>
								</div>
								<div class="col px-1 too-small-hide"></div>
								<div class="col px-1 too-small-hide"></div>
							</div>
				  	</div>
				</form>
			</div>
			
			
			

			
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>	
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js" integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ" crossorigin="anonymous"></script>
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
				if( !empty($_GET['duplicate']) ) {
					echo '
					show_error_dup( \''.$_GET['duplicate'].'\' );';	
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
			function show_error_dup( msg ){
				msg = 'You have already created that!';
				
				
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
			
			function delete_form_field(id){
				var args = {};
				args.mode = 'delete_form_field';
				args.id = id;
				
				$.post( "/<?php echo $base;?>pop6_ping.php", args, function( data ) {
					
					window.location.href = '/'+base+'admin_shop_form_fields.php/?shop=<?php echo $_GET["shop"];?>';
					
				});
			}
			
		</script>

	</body>
</html>