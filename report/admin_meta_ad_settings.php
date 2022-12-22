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

$sql = 'SELECT *, A.sha_token as `token`, A.install_date AS a_install_date FROM accounts A LEFT JOIN fb_settings F ON A.sha_token = F.sha_token WHERE A.sha_token = "'.$_GET['shop'].'";';
$db_data = sql_select_to_array($sql);
$sql = 'SELECT * FROM accounts WHERE type != "99" AND type != "50" AND "'.date("Y-m-d").'" <= expires ORDER BY account_name ASC;';
$db_data_shops = sql_select_to_array($sql);
$sql = 'SELECT * FROM fb_ad_account_id_map WHERE sha_token = "'.$_GET['shop'].'" ORDER BY account_id ASC;';
$db_data_meta_id_map = sql_select_to_array($sql);
$sql = 'SELECT * FROM fb_settings WHERE sha_token = "'.$_GET['shop'].'" ORDER BY fb_settings_id ASC;';
$db_data_meta_settings = sql_select_to_array($sql);
// var_dump($db_data_meta_settings);

if( $_POST['action'] == "delete" ){
	
	if (count($db_data_meta_id_map) == 1){
		$keys = array('fb_ad_account_id_map','fb_settings');
		$sql = 'UPDATE fb_ad_account_id_map SET fb_ad_account_id = "" WHERE fb_ad_account_id = "'.$_POST["fb_ad_account_id"].'" AND sha_token = "'.$_POST["sha_token"].'";';
		$sql .= 'UPDATE fb_settings SET fb_ad_account_id = "" WHERE fb_settings_id = "'.$_POST["fb_settings_id"].'" AND sha_token = "'.$_POST["sha_token"].'" AND fb_ad_account_id = "'.$_POST["fb_ad_account_id"].'";';
		sql_multi_query($sql,$keys);
		$sql = 'SELECT * FROM fb_ad_account_id_map WHERE sha_token = "'.$_GET['shop'].'" ORDER BY account_id ASC;';
		$db_data_meta_id_map = sql_select_to_array($sql);
		$sql = 'SELECT * FROM fb_settings WHERE sha_token = "'.$_GET['shop'].'" ORDER BY fb_settings_id ASC;';
		$db_data_meta_settings = sql_select_to_array($sql);
	} else if (count($db_data_meta_id_map) > 1) {
		$keys = array('fb_ad_account_id_map','fb_settings');
		$sql = 'DELETE FROM fb_ad_account_id_map WHERE fb_ad_account_id = "'.$_POST["fb_ad_account_id"].'" AND sha_token = "'.$_POST["sha_token"].'";';
		$sql .= 'DELETE FROM  fb_settings WHERE fb_settings_id = "'.$_POST["fb_settings_id"].'" AND sha_token = "'.$_POST["sha_token"].'" AND fb_ad_account_id = "'.$_POST["fb_ad_account_id"].'";';
		sql_multi_query($sql,$keys);
		$sql = 'SELECT * FROM fb_ad_account_id_map WHERE sha_token = "'.$_GET['shop'].'" ORDER BY account_id ASC;';
		$db_data_meta_id_map = sql_select_to_array($sql);
		$sql = 'SELECT * FROM fb_settings WHERE sha_token = "'.$_GET['shop'].'" ORDER BY fb_settings_id ASC;';
		$db_data_meta_settings = sql_select_to_array($sql);
	}
} else if ($_POST['action'] == "create"){
	if (empty($db_data_meta_id_map)) {
		$keys = array('fb_ad_account_id_map','fb_settings');
		$sql = 'INSERT INTO fb_ad_account_id_map (account_id, fb_ad_account_id, account_name, sha_token, active) VALUES ("'.$db_data[0]['id'].'","'.$_POST["fb_ad_account_id"].'","'.$db_data[0]['account_name'].'","'.$db_data[0]['token'].'", 1);';
		$sql .= 'UPDATE fb_settings SET fb_ad_account_id = "'.$_POST["fb_ad_account_id"].'", install_date = "'.$db_data[0]['a_install_date'].'" WHERE fb_settings_id = "'.$_POST["fb_settings_id"].'" AND sha_token = "'.$_POST["sha_token"].'";';
		sql_multi_query($sql,$keys);
		$sql = 'SELECT * FROM fb_ad_account_id_map WHERE sha_token = "'.$_GET['shop'].'" ORDER BY account_id ASC;';
		$db_data_meta_id_map = sql_select_to_array($sql);
		$sql = 'SELECT * FROM fb_settings WHERE sha_token = "'.$_GET['shop'].'" ORDER BY fb_settings_id ASC;';
		$db_data_meta_settings = sql_select_to_array($sql);
	} else if (count($db_data_meta_id_map) == 1){
		if (empty($db_data_meta_id_map[0]['fb_ad_account_id'])){
			$keys = array('fb_ad_account_id_map','fb_settings');
			$sql = 'UPDATE fb_ad_account_id_map SET fb_ad_account_id = "'.$_POST["fb_ad_account_id"].'" WHERE sha_token = "'.$_POST["sha_token"].'";';
			$sql .= 'UPDATE fb_settings SET fb_ad_account_id = "'.$_POST["fb_ad_account_id"].'", install_date = "'.$db_data[0]['a_install_date'].'" WHERE fb_settings_id = "'.$_POST["fb_settings_id"].'" AND sha_token = "'.$_POST["sha_token"].'";';
			sql_multi_query($sql,$keys);
			$sql = 'SELECT * FROM fb_ad_account_id_map WHERE sha_token = "'.$_GET['shop'].'" ORDER BY account_id ASC;';
			$db_data_meta_id_map = sql_select_to_array($sql);
			$sql = 'SELECT * FROM fb_settings WHERE sha_token = "'.$_GET['shop'].'" ORDER BY fb_settings_id ASC;';
			$db_data_meta_settings = sql_select_to_array($sql);
		} else {

			$keys = array('fb_ad_account_id_map','fb_settings');
			$sql = 'INSERT INTO fb_ad_account_id_map (account_id, fb_ad_account_id, account_name, sha_token, active) VALUES ("'.$db_data[0]['id'].'","'.$_POST["fb_ad_account_id"].'","'.$db_data[0]['account_name'].'","'.$db_data[0]['token'].'", 1);';
			$sql .= 'INSERT INTO fb_settings (sha_token, pixel_id, access_token, test_event_code, account_mode, fb_ad_account_id, install_date) VALUES ("'.$db_data[0]['token'].'","'.$db_data[0]['pixel_id'].'","'.$db_data[0]['access_token'].'","'.$db_data[0]['test_event_code'].'","'.$form['account_mode'].'","'.$_POST["fb_ad_account_id"].'","'.$db_data[0]['a_install_date'].'")';
			sql_multi_query($sql,$keys);
			$sql = 'SELECT * FROM fb_ad_account_id_map WHERE sha_token = "'.$_GET['shop'].'" ORDER BY account_id ASC;';
			$db_data_meta_id_map = sql_select_to_array($sql);
			$sql = 'SELECT * FROM fb_settings WHERE sha_token = "'.$_GET['shop'].'" ORDER BY fb_settings_id ASC;';
			$db_data_meta_settings = sql_select_to_array($sql);
		}
	} else if (count($db_data_meta_id_map) > 1) {
		$keys = array('fb_ad_account_id_map','fb_settings');
		$sql = 'INSERT INTO fb_ad_account_id_map (account_id, fb_ad_account_id, account_name, sha_token, active) VALUES ("'.$db_data[0]['id'].'","'.$_POST["fb_ad_account_id"].'","'.$db_data[0]['account_name'].'","'.$db_data[0]['token'].'", 1);';
		$sql .= 'INSERT INTO fb_settings (sha_token, pixel_id, access_token, test_event_code, account_mode, fb_ad_account_id, install_date) VALUES ("'.$db_data[0]['token'].'","'.$db_data[0]['pixel_id'].'","'.$db_data[0]['access_token'].'","'.$db_data[0]['test_event_code'].'","'.$db_data[0]['account_mode'].'","'.$_POST["fb_ad_account_id"].'","'.$db_data[0]['a_install_date'].'")';
		// var_dump($db_data[0]);
		// var_dump($sql);
		// exit;
		sql_multi_query($sql,$keys);
		$sql = 'SELECT * FROM fb_ad_account_id_map WHERE sha_token = "'.$_GET['shop'].'" ORDER BY account_id ASC;';
		$db_data_meta_id_map = sql_select_to_array($sql);
		$sql = 'SELECT * FROM fb_settings WHERE sha_token = "'.$_GET['shop'].'" ORDER BY fb_settings_id ASC;';
		$db_data_meta_settings = sql_select_to_array($sql);
	}
}

$domain = $db_data[0]['domain'];
$pixel_id = $db_data[0]['pixel_id'];
$access_token = $db_data[0]['access_token'];
$test_event_code = $db_data[0]['test_event_code'];

if( !empty($pixel_id) && !empty($access_token) && !empty($test_event_code) && !empty($db_data_meta_settings[0]['fb_ad_account_id'])){
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
p {
	margin: 0;
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
	<title>Pop6 Admin Events</title>
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
<div class="card base-card bg-light border p-3 my-2 mb-2 text-center">
	<div class="text-center">
		<h3 class="my-2  pb-2 text-center" style="color:#AC39FD"><b><?php echo $db_data[0]["account_name"]; ?></b></h3>
	</div>
</div>

<?php
if(!$capi_ready){
	echo '<!--';
}
?>

<div class="card base-card bg-light border p-3 my-2">
	<div class="">
		<h4 class="my-2  pb-2" style="color:#AC39FD"><b>Your Ad IDs</b></h4>	
</div>
	
	
<?php

foreach( $db_data_meta_settings as $id => $e ){
	echo '<h6 class="mb-1 mt-4"><b>Meta Ad ID '.($id+1).':</b></h6>';
	echo '<div class="card base-card bg-light bg-dark text-white border p-3 mt-2">';
	echo '<p>';
	echo '<b>ID:</b> '.$e['fb_ad_account_id'];
	echo '</p>';
	echo '</div>';
	echo '<div class="text-center row px-2">
	
	<div class="col mt-1 px-2">
		<form action="/'.$base.'admin_meta_ad_settings.php?shop='.$_GET["shop"].'" method="post" autocomplete="off">
			<input type="text" id="fb_ad_account_id" name="fb_ad_account_id" value="'.$e['fb_ad_account_id'].'" hidden>
			<input type="text" id="fb_settings_id" name="fb_settings_id" value="'.$e['fb_settings_id'].'" hidden>
			<input type="text" id="sha_token" name="sha_token" value="'.$e['sha_token'].'" hidden>
			<input type="text" id="action" name="action" value="delete" hidden>

			<button type="submit" class="btn btn-block btn-danger text-center">Delete</button>
		</form>
	</div>
	<div class="col px-2 too-small-hide"></div>
	<div class="col px-2 too-small-hide"></div>
	<div class="col px-2 too-small-hide"></div>
	<div class="col px-2 too-small-hide"></div>
	<div class="col px-2 too-small-hide"></div>
</div>';
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
		<h4 class="my-2  pb-2" style="color:#AC39FD"><b>New Meta Ad Account ID</b></h4>
	</div>
	<form id="event" action="/<?php echo $base;?>admin_meta_ad_settings.php?shop=<?php echo $_GET["shop"];?>" method="post" autocomplete="off">					
		<div class="form-group">
			<label for="event_value" class="blue-text"><b>Meta Ad Account ID</b></label>
			<input type="text" class="form-control" id="fb_ad_account_id2" name="fb_ad_account_id" placeholder="Meta Ad Account ID" autocomplete="off" >
			<input type="text" id="fb_settings_id2" name="fb_settings_id" value="<?php echo $e['fb_settings_id'] ?>" hidden>
			<input type="text" id="sha_token2" name="sha_token" value="<?php echo $e['sha_token'] ?>" hidden>

			<input type="text" id="action2" name="action" value="create" hidden>
		</div>
		<div class="text-center row ">
			<div class="col  too-small-hide"></div>
			<div class="col ">
				<button type="submit" class="btn btn-block cust-btn text-center">Add Account ID</button>
			</div>
			<div class="col px-1 too-small-hide"></div>
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
					
				?>
			});

			
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
		

			
		</script>

	</body>
</html>