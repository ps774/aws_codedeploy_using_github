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

$sql = 'SELECT *, A.sha_token AS `token`, A.install_date AS `live_install_date` FROM accounts A LEFT JOIN fb_settings F ON A.sha_token = F.sha_token WHERE A.sha_token = "'.$_GET['shop'].'" ORDER BY fb_settings_id ASC LIMIT 1;';
$db_data = sql_select_to_array($sql);

$sql = 'SELECT * FROM fb_ad_account_id_map WHERE sha_token = "'.$_GET['shop'].'" ORDER BY account_id ASC LIMIT 1;';
$db_data_meta = sql_select_to_array($sql);
// var_dump($db_data[0]);

if( !empty($_POST['account_mode']) ){
	
	$data_structure = array(
		'account_mode' => '',
		'expires' => '',
	);
	
	$form = array();
	
	foreach( $data_structure as $k => $v ){
		if( !empty($_POST[$k]) ){
			$form[$k] = $_POST[$k];
		}
	}
	
	$keys = array('accounts','fb_settings');
	$sql = 'UPDATE accounts SET expires = "'.$form['expires'].'" WHERE sha_token = "'.$_GET['shop'].'";';
	if (!empty($db_data[0]['fb_settings_id'])){
		$sql .= 'UPDATE fb_settings SET account_mode = "'.$form['account_mode'].'" WHERE fb_settings_id = "'.$db_data[0]["fb_settings_id"].'";';
	} else {
		$sql .= 'INSERT INTO fb_settings (sha_token, account_mode) VALUES ("'.$db_data[0]['token'].'", "'.$form['account_mode'].'")';
	}
	
	sql_multi_query($sql,$keys);
	
	$_GET['success'] = htmlentities('Your settings were saved to the database successfully.');
	$sql = 'SELECT *, A.sha_token AS `token`, A.install_date AS `live_install_date` FROM accounts A LEFT JOIN fb_settings F ON A.sha_token = F.sha_token WHERE A.sha_token = "'.$_GET['shop'].'" ORDER BY fb_settings_id ASC LIMIT 1;';
	$db_data = sql_select_to_array($sql);
	$sql = 'SELECT * FROM fb_ad_account_id_map WHERE sha_token = "'.$_GET['shop'].'" ORDER BY account_id ASC LIMIT 1;';
	$db_data_meta = sql_select_to_array($sql);
}


if( !empty($_POST['domain']) ){
	
	$data_structure = array(
		'domain' => '',
		'pixel_id' => '',
		'access_token' => '',
		'test_event_code' => '',
		'account_name' => '',
		'local_timezone' => '',
		'install_date' => '',
	);
	
	$form = array();
	
	foreach( $data_structure as $k => $v ){
		if( !empty($_POST[$k]) ){
			$form[$k] = $_POST[$k];
		}
	}
	$pilot_expiration_date = date("Y-m-d", strtotime("+15 days"));
	$keys = array('accounts','fb_settings');
	
	if ($db_data[0]["trial_init"] == 0) {
		$sql = 'UPDATE accounts SET domain = "'.$form['domain'].'", shop = "'.$_POST['sh_store'].'", account_name="'.$form['account_name'].'", expires="'.$pilot_expiration_date.'", local_timezone = "'.$form['local_timezone'].'", install_date = "'.$form['install_date'].'", trial_init="1" WHERE sha_token = "'.$_GET['shop'].'";';
	} else {
		$sql = 'UPDATE accounts SET domain = "'.$form['domain'].'", shop = "'.$_POST['sh_store'].'", account_name="'.$form['account_name'].'", local_timezone = "'.$form['local_timezone'].'", install_date = "'.$form['install_date'].'" WHERE sha_token = "'.$_GET['shop'].'";';
	}
	if (!empty($db_data[0]['fb_settings_id'])){
		$sql .= 'UPDATE fb_settings SET pixel_id = "'.$form['pixel_id'].'", access_token = "'.$form['access_token'].'", test_event_code = "'.$form['test_event_code'].'", install_date = "'.$form['install_date'].'" WHERE sha_token = "'.$_GET['shop'].'";';
	} else {
		$sql .= 'INSERT INTO fb_settings (sha_token, pixel_id, access_token, test_event_code, install_date) VALUES ("'.$db_data[0]['token'].'","'.$form['pixel_id'].'","'.$form['access_token'].'","'.$form['test_event_code'].'","'.$form['install_date'].'") ON DUPLICATE KEY UPDATE pixel_id = "'.$form['pixel_id'].'", access_token = "'.$form['access_token'].'", test_event_code = "'.$form['test_event_code'].'", install_date = "'.$form['install_date'].'";';
	}

	
	sql_multi_query($sql,$keys);
	
	$_GET['success'] = htmlentities('Your settings were saved to the database successfully.');
	//var_dump($sql);
	
	$sql = 'SELECT *, A.sha_token AS `token`, A.install_date AS `live_install_date` FROM accounts A LEFT JOIN fb_settings F ON A.sha_token = F.sha_token WHERE A.sha_token = "'.$_GET['shop'].'" ORDER BY fb_settings_id ASC LIMIT 1;';
	$db_data = sql_select_to_array($sql);
	$sql = 'SELECT * FROM fb_ad_account_id_map WHERE sha_token = "'.$_GET['shop'].'" ORDER BY account_id ASC LIMIT 1;';
	$db_data_meta = sql_select_to_array($sql);
	
	

}
if( !empty($_POST['reroute_opt_in']) ){
	$_POST['reroute_opt_in'] = intval($_POST['reroute_opt_in']);
	
	if ($_POST['reroute_opt_in'] == 1 || $_POST['reroute_opt_in'] == "1"){ 
		if (!empty($_POST['reroute_setting'])){
			$_POST['reroute_setting'] = preg_replace("/[^A-Za-z0-9\-\_ ]/", "", $_POST['reroute_setting'] );
			$data_structure = array(
				'reroute_opt_out' => '',
				'reroute_opt_in' => '',
				'reroute_setting' => '',
			);
			
			$form = array();
			
			foreach( $data_structure as $k => $v ){
				if( !empty($_POST[$k]) ){
					$form[$k] = $_POST[$k];
				}
			}		
			$sql = 'UPDATE accounts SET reroute_opt_in = "1", reroute_setting = "'.$_POST['reroute_setting'].'" WHERE sha_token = "'.$_GET['shop'].'";';
			
			sql_select_to_array($sql);
			
			$_GET['success'] = htmlentities('Your settings were saved to the database successfully.');
			//var_dump($sql);
			
			$sql = 'SELECT *, A.sha_token AS `token`, A.install_date AS `live_install_date` FROM accounts A LEFT JOIN fb_settings F ON A.sha_token = F.sha_token WHERE A.sha_token = "'.$_GET['shop'].'" ORDER BY fb_settings_id ASC LIMIT 1;';
			$db_data = sql_select_to_array($sql);
			$sql = 'SELECT * FROM fb_ad_account_id_map WHERE sha_token = "'.$_GET['shop'].'" ORDER BY account_id ASC LIMIT 1;';
			$db_data_meta = sql_select_to_array($sql);
		} else {
			$sql = 'UPDATE accounts SET reroute_opt_in = "0", reroute_setting = "" WHERE sha_token = "'.$_GET['shop'].'";';
			sql_select_to_array($sql);
			$_GET['success'] = htmlentities('Your settings were saved to the database successfully.');
			//var_dump($sql);
			
			$sql = 'SELECT *, A.sha_token AS `token`, A.install_date AS `live_install_date` FROM accounts A LEFT JOIN fb_settings F ON A.sha_token = F.sha_token WHERE A.sha_token = "'.$_GET['shop'].'" ORDER BY fb_settings_id ASC LIMIT 1;';
			$db_data = sql_select_to_array($sql);
			$sql = 'SELECT * FROM fb_ad_account_id_map WHERE sha_token = "'.$_GET['shop'].'" ORDER BY account_id ASC LIMIT 1;';
			$db_data_meta = sql_select_to_array($sql);	
		}
	} else {
		$sql = 'UPDATE accounts SET reroute_opt_in = "0", reroute_setting = "" WHERE sha_token = "'.$_GET['shop'].'";';
		sql_select_to_array($sql);
		$_GET['success'] = htmlentities('Your settings were saved to the database successfully.');
		//var_dump($sql);
		
		$sql = 'SELECT *, A.sha_token AS `token`, A.install_date AS `live_install_date` FROM accounts A LEFT JOIN fb_settings F ON A.sha_token = F.sha_token WHERE A.sha_token = "'.$_GET['shop'].'" ORDER BY fb_settings_id ASC LIMIT 1;';
		$db_data = sql_select_to_array($sql);
		$sql = 'SELECT * FROM fb_ad_account_id_map WHERE sha_token = "'.$_GET['shop'].'" ORDER BY account_id ASC LIMIT 1;';
		$db_data_meta = sql_select_to_array($sql);

	}
} else if (!empty($_POST['reroute_opt_out']) && $_POST['reroute_opt_out'] == 0  || $_POST['reroute_opt_out'] == "0" && !empty($_POST['reroute_opt_out'])) {
	$sql = 'UPDATE accounts SET reroute_opt_in = "0", reroute_setting = "" WHERE sha_token = "'.$_GET['shop'].'";';
	sql_select_to_array($sql);
	$_GET['success'] = htmlentities('Your settings were saved to the database successfully.');
	//var_dump($sql);
	
	$sql = 'SELECT *, A.sha_token AS `token`, A.install_date AS `live_install_date` FROM accounts A LEFT JOIN fb_settings F ON A.sha_token = F.sha_token WHERE A.sha_token = "'.$_GET['shop'].'" ORDER BY fb_settings_id ASC LIMIT 1;';
	$db_data = sql_select_to_array($sql);
	$sql = 'SELECT * FROM fb_ad_account_id_map WHERE sha_token = "'.$_GET['shop'].'" ORDER BY account_id ASC LIMIT 1;';
	$db_data_meta = sql_select_to_array($sql);

}

$sql = 'SELECT * FROM accounts WHERE type != "99" AND type != "50" AND "'.date("Y-m-d").'" <= expires ORDER BY account_name ASC;';
$db_data_shops = sql_select_to_array($sql);

$domain = $db_data[0]['domain'];
$pixel_id = $db_data[0]['pixel_id'];
$access_token = $db_data[0]['access_token'];
$test_event_code = $db_data[0]['test_event_code'];


if( !empty($pixel_id) && !empty($access_token) && !empty($test_event_code) ){
	$capi_ready = true;
} else {
	$capi_ready = false;
}

if( $capi_ready && !empty($domain) ){
	$script_ready = true;
	//$j_script = '&lt;script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"&gt;&lt;/script&gt;
//';
	

	$server_url = $base_url;

	
	$script = '&lt;script type="text/javascript" src="https://'.$server_url.'popsixle.php?t='.$db_data[0]['token'].'"&gt;&lt;/script&gt;';
} else {
	$script_ready = false;
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
.explanation_text{
	font-size: 11px;
	margin: 5px 0 0 5px;
	opacity: 60%;
}
th:hover {
	cursor: pointer;
	background-color: purple;
}
.switch input
{
  display: none;
}

.switch 
{
  display: inline-block;
  width: 60px; /*=w*/
  height: 30px; /*=h*/
  margin: 4px;
  transform: translateY(40%);
  position: relative;
}

.slider
{
  position: absolute;
  top: 0;
  bottom: 0;
  left: 0;
  right: 0;
  border-radius: 30px;
  box-shadow: 0 0 0 2px #777, 0 0 4px #777;
  cursor: pointer;
  border: 4px solid transparent;
  overflow: hidden;
  transition: 0.2s;
}

.slider:before
{
  position: absolute;
  content: "";
  width: 100%;
  height: 100%;
  background-color: #777;
  border-radius: 30px;
  transform: translateX(-30px); /*translateX(-(w-h))*/
  transition: 0.2s;
}

input:checked + .slider:before
{
  transform: translateX(30px); /*translateX(w-h)*/
  background-color: limeGreen;
}

input:checked + .slider
{
  box-shadow: 0 0 0 2px limeGreen, 0 0 8px limeGreen;
}

.switch200 .slider:before
{
  width: 200%;
  transform: translateX(-82px); /*translateX(-(w-h))*/
}

.switch200 input:checked + .slider:before
{
  background-color: red;
}

.switch200 input:checked + .slider
{
  box-shadow: 0 0 0 2px red, 0 0 8px red;
}
.flex-events-sub {
	display: flex;
}
.flex-events-sub2 {
	flex-direction: column;
	min-width: 300px;
}
.flex-events-sub3 {
	flex-direction: column;
	max-width: 500px;
	margin-top: 10px;
	font-size: 12px;

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
	<title>Pop6 Admin Account Settings</title>
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

			<div class="card base-card bg-light border p-3 mt-2">
				<div class="">
					<h4 class="my-2  pb-2" style="color:#AC39FD"><b>Self-Onboarding Instructions</b></h4>
				</div>
				<p class="pt-2" style="margin-left:2px;">Please complete the steps listed here: <a href="https://docs.google.com/document/d/1fsGpS2jeCP15IX729wwO57ziwoqjGeUOS4rMCJM3xro/">Readme</a></p>
				<p class="pt-2" style="margin-left:2px;">If you are installing with Google Tag Manager complete these steps as well: <a href="https://docs.google.com/document/d/1N8GCmuPSymcdU3bjAsm_ylL8F7XX4aazS-v7lhk5GEk">GTM Readme</a></p>
			</div>

			<div class="card base-card bg-light border p-3">
				<div class="">
					<h4 class="my-2  pb-2" style="color:#AC39FD"><b>Account Settings</b></h4>
				</div>
				<form id="settings" action='/<?php echo $base;?>admin_shop_account.php/?shop=<?php echo$_GET["shop"];?>' method="post" autocomplete="off">
					<input autocomplete="false" name="hidden" type="text" style="display:none;">
				  	
				  	<input type="hidden" id="mode" name="mode" value="1">
					
					
					<div class="form-group" hidden>
				    	<label for="password" class="blue-text"><b>Your Email:</b></label>
						<input class="form-control" id="email" name="email" value="<?php echo $_SESSION['user']['email'];?>" readonly>
				  	</div>
					
				  	<div class="form-group">
				    	<label for="account_name" class="blue-text"><b>Account Name:</b></label>
						<input type="text" class="form-control" id="account_name" name="account_name" placeholder="Enter the Name of the Account" maxlength="99" required autocomplete="new-password" <?php 
							if( !empty($db_data[0]['account_name']) ){
								echo 'value="'.$db_data[0]['account_name'].'"';
							}
						?>>
						<div class="explanation_text">Enter the Name of the Account</div>
				  	</div>
				  	<div class="form-group">
				    	<label for="domain" class="blue-text"><b>Domain:</b></label>
						<input type="text" class="form-control" id="domain" name="domain" placeholder="Enter the domain of the target website (ie. 'domain.com')" minlength="5" required autocomplete="new-password" <?php 
							if( !empty($db_data[0]['domain']) ){
								echo 'value="'.$db_data[0]['domain'].'"';
							}
						?>>
						<div class="explanation_text">Enter the domain of the target website (ie. 'domain.com')</div>
						<div class="explanation_text">If there are multiple domains, separate each domain with a single space (ie. 'domain.com seconddomain.com')</div>
						<div class="explanation_text">Do Not include 'https://' or anything After the top level domain (ie. '.com', '.co', '.io', etc...) </div>
				  	</div>
				  	
				  	<div class="form-group">
				    	<label for="pixel_id" class="blue-text"><b>Meta Pixel ID:</b></label>
						<input type="text" class="form-control" id="pixel_id" name="pixel_id" placeholder="Enter your Meta Pixel ID (Event Manager)" autocomplete="new-password" required <?php 
							if( !empty($db_data[0]['pixel_id']) ){
								echo 'value="'.$db_data[0]['pixel_id'].'"';
							}
						?>>
						<div class="explanation_text">Enter your Meta Pixel ID (Event Manager)</div>
				  	</div>
				  	
				  	<div class="form-group">
				    	<label for="access_token" class="blue-text"><b>CAPI Access Token:</b></label>
						<input type="text" class="form-control" id="access_token" name="access_token" placeholder="Enter your CAPI Access Token (Create in Event Manager Settings)" autocomplete="new-password" required <?php 
							if( !empty($db_data[0]['access_token']) ){
								echo 'value="'.$db_data[0]['access_token'].'"';
							}
						?>>
						<div class="explanation_text">Enter your CAPI Access Token (Create in Event Manager Settings)</div>
				  	</div>
				  	
				  	<div class="form-group">
				    	<label for="test_event_code" class="blue-text"><b>CAPI Test Event Code:</b></label>
						<input type="text" class="form-control" id="test_event_code" name="test_event_code" placeholder="Enter your CAPI Test Event Code (Event Manager Settings)" autocomplete="new-password" required <?php 
							if( !empty($db_data[0]['test_event_code']) ){
								echo 'value="'.$db_data[0]['test_event_code'].'"';
							}
						?>>
						<div class="explanation_text">Enter your CAPI Test Event Code (Event Manager Settings)</div>
				  	</div>
				  	
				  	<div class="form-group">
				    	<label for="sh_store" class="blue-text"><b>Shopify Store URL (optional):</b></label>
						<input type="text" class="form-control" id="sh_store" name="sh_store" placeholder="Enter your Shopify Store URL (Optional)" <?php 
							if( !empty($db_data[0]['shop']) ){
								echo 'value="'.$db_data[0]['shop'].'"';
							}
						?>>
						<div class="explanation_text">Enter your Shopify Store URL (Only for Shopify Stores) (ie. domain.myshopify.com)</div>
						<div class="explanation_text">Please double check that this is entered correctly, and in the format listed above.</div>
						<div class="explanation_text">Do Not include 'https://' or anything After the '.com' </div>
				  	</div>
				  	<div class="form-group">
				    	<label for="local_timezone" class="blue-text"><b>Store Time Zone:</b></label>
						<select type="text" class="form-control" id="local_timezone" name="local_timezone">
							<option value="GMT" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "GMT"){
										echo 'selected';
									}
								} else {
									echo 'selected';
								}
							?>>GMT &rarr; +00:00</option>
							<option value="ECT" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "ECT"){
										echo 'selected';
									}
								}
							?>>ECT &rarr; +01:00</option>
							<option value="EET" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "EET"){
										echo 'selected';
									}
								}
							?>>EET &rarr; +02:00</option>
							<option value="EAT" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "EAT"){
										echo 'selected';
									}
								}
							?>>EAT &rarr; +03:00</option>
							<option value="MET" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "MET"){
										echo 'selected';
									}
								}
							?>>MET &rarr; +03:30</option>
							<option value="NET" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "NET"){
										echo 'selected';
									}
								}
							?>>NET &rarr; +04:00</option>
							<option value="PLT" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "PLT"){
										echo 'selected';
									}
								}
							?>>PLT &rarr; +05:00</option>
							<option value="IST" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "IST"){
										echo 'selected';
									}
								}
							?>>IST &rarr; +05:30</option>
							<option value="BST" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "BST"){
										echo 'selected';
									}
								}
							?>>BST &rarr; +06:00</option>
							<option value="VST" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "VST"){
										echo 'selected';
									}
								}
							?>>VST &rarr; +07:00</option>
							<option value="CTT" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "CTT"){
										echo 'selected';
									}
								}
							?>>CTT &rarr; +08:00</option>
							<option value="JST" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "JST"){
										echo 'selected';
									}
								}
							?>>JST &rarr; +09:00</option>
							<option value="ACT" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "ACT"){
										echo 'selected';
									}
								}
							?>>ACT &rarr; +09:30</option>
							<option value="AET" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "AET"){
										echo 'selected';
									}
								}
							?>>AET &rarr; +10:00</option>
							<option value="NST" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "NST"){
										echo 'selected';
									}
								}
							?>>NST &rarr; +12:00</option>
							<option value="MIT" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "MIT"){
										echo 'selected';
									}
								}
							?>>MIT &rarr; -11:00</option>
							<option value="HST" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "HST"){
										echo 'selected';
									}
								}
							?>>HST &rarr; -10:00</option>
							<option value="AST" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "AST"){
										echo 'selected';
									}
								}
							?>>AST &rarr; -09:00</option>
							<option value="PST" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "PST"){
										echo 'selected';
									}
								}
							?>>PST &rarr; -08:00</option>
							<option value="MST" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "MST"){
										echo 'selected';
									}
								}
							?>>MST &rarr; -07:00</option>
							<option value="CST" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "CST"){
										echo 'selected';
									}
								}
							?>>CST &rarr; -06:00</option>
							<option value="EST" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "EST"){
										echo 'selected';
									}
								}
							?>>EST &rarr; -05:00</option>
							<option value="PRT" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "PRT"){
										echo 'selected';
									}
								}
							?>>PRT &rarr; -04:00</option>
							<option value="CNT" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "CNT"){
										echo 'selected';
									}
								}
							?>>CNT &rarr; -03:30</option>
							<option value="AGT" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "AGT"){
										echo 'selected';
									}
								}
							?>>AGT &rarr; -03:00</option>
							<option value="CAT" <?php 
								if( !empty($db_data[0]['local_timezone']) ){
									if ($db_data[0]['local_timezone'] == "CAT"){
										echo 'selected';
									}
								}
							?>>CAT &rarr; -01:00</option>
						</select>
						<div class="explanation_text">Select Your Store Time Zone</div>
				  	</div>
						<div class="form-group my-2  pb-2">
							<label for="install_date" class="blue-text"><b>Install Date</b></label>
							<input require type="date" class="form-control" id="install_date" name="install_date" placeholder="Install Date ie: YYYY-mm-dd" <?php 
							if( !empty($db_data[0]['live_install_date']) ){
								echo 'value="'.$db_data[0]['live_install_date'].'"';
							} else {
								echo 'value="'.date("Y-m-d", time()).'"';
							}
							?>>
						</div>
						<div class="explanation_text">Date Popsixle went/will go live on site. (This assists with reporting data)</div>

				  	
				  	<div class="text-center row px-2">
					  	
							<div class="col px-1 too-small-hide"></div>
							<div class="col px-1">
								<button type="submit" class="btn btn-block cust-btn text-center">Update Settings</button>
							</div>
							<div class="col px-1 too-small-hide"></div>
				  	</div>
				</form>
			</div>
			<div class="card base-card bg-light border p-3">
				<div class="">
					<h4 class="my-2  pb-2" style="color:#AC39FD"><b>Admin Settings</b></h4>
				</div>
				<form id="settings" action='/<?php echo $base;?>admin_shop_account.php?shop=<?php echo$_GET["shop"];?>' method="post" autocomplete="off">
					<input autocomplete="false" name="hidden" type="text" style="display:none;">
				  	
				  	<input type="hidden" id="mode" name="mode" value="1">
										
					<?php 
						if( !empty($db_data[0]['account_mode']) ){
							$mode = $db_data[0]['account_mode'];
							
							if( $mode == 'live' ){
								$mode1 = '';
								$mode2 = ' checked';
							} else {
								$mode1 = ' checked';
								$mode2 = '';
							}
						} else {
							$mode1 = ' checked';
						}
					?>
					
				  	<div class="form-group">
						<label for="form-check" class=""><b>Account Mode:</b></label>
						<div class="form-check">
							<input class="form-check-input" type="radio" name="account_mode" id="debug" value="debug" <?php echo $mode1;?>>
							<label class="form-check-label" for="debug">Debug (Test Events)</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="radio" name="account_mode" id="live" value="live" <?php echo $mode2;?>>
							<label class="form-check-label" for="live">Live</label>
						</div>
				  	</div>
						<div class="form-group">
				    	<label for="expires" class="blue-text"><b>Account Expires</b></label>
						<input type="date" class="form-control" id="expires" name="expires" placeholder="Enter Token Expiration Date ie: YYYY-mm-dd" <?php 
							if( !empty($db_data[0]['expires']) ){
								echo 'value="'.$db_data[0]['expires'].'"';
							}
						?>>
				  	</div>
			  	
				  	<div class="text-center row px-2">
					  	
							<div class="col px-1 too-small-hide"></div>
							<div class="col px-1">
								<button type="submit" class="btn btn-block cust-btn text-center">Update Admin Settings</button>
							</div>
							<div class="col px-1 too-small-hide"></div>
				  	</div>
				</form>
			</div>

			<div class="card base-card bg-light border p-3">
				<div class="">
					<h4 class="my-2  pb-2" style="color:#AC39FD"><b>Event Processing Settings</b></h4>
				</div>
				<form id="settings" action='/<?php echo $base;?>admin_shop_account.php?shop=<?php echo$_GET["shop"];?>' method="post" autocomplete="off">
					<input autocomplete="false" name="hidden" type="text" style="display:none;">
				  	
										
					<?php 
						if( empty($db_data[0]['reroute_opt_in']) || $db_data[0]['reroute_opt_in'] == 0 || $db_data[0]['reroute_opt_in'] == "0" || $db_data[0]['reroute_opt_in'] == "NULL" || $db_data[0]['reroute_opt_in'] == NULL ){							
							$reroute_opt = '';
						} else if ($db_data[0]['reroute_opt_in'] == 1 || $db_data[0]['reroute_opt_in'] == "1") {
							$reroute_opt = ' checked';
						}
					?>
						<div class="flex-events-sub">
							<div class="form-group flex-events-sub2">
								<label for="form-check" class="mb-0 px-0"><b>Reroute Subscription Purchases</b></label>
								<input class="" type="text" name="reroute_opt_out" value="0" hidden>
								<div class="px-2">
									OFF &nbsp;
									<label class="switch">
										<input class="" type="checkbox" name="reroute_opt_in" value="1" <?php echo $reroute_opt?>>
										<span class="slider"></span>
									</label>
									&nbsp; ON
								</div>
								<label for="form-check" class="mt-3 px-1"><b></b><b>Default: OFF</b></label>
							</div>
							<div class="flex-events-sub3">
								<div>
									Popsixle sends all subscription purchases back to Meta as "Purchase" events. If you would prefer to have Popsixle send subscription purchases back as a separate event, turn on Reroute Subscription Purchases and enter the event you would like to send the data to.
								</div>
								<br>
								<div>
									Read our Knowledge Base article for more information on the pros and cons of remapping subscription purchases.
								</div>
							</div>
						</div>
						<br>
						<div class="form-group">
				    	<label class="mb-3" for="reroute_setting" class="blue-text"><b>Reroute Event:</b></label>
						<input type="text" class="form-control" id="reroute_setting" name="reroute_setting" minlength="4" placeholder="Enter Event to Route Subscriptions to" <?php 
							if( !empty($db_data[0]['reroute_setting']) ){
								echo 'value="'.$db_data[0]['reroute_setting'].'"';
							}
						?>>
						<div class="explanation_text"></div> 
				  	</div>
			  	
				  	<div class="text-center row px-2">
					  	
							<div class="col px-1 too-small-hide"></div>
							<div class="col px-1">
								<button type="submit" class="btn btn-block cust-btn text-center">Update Admin Settings</button>
							</div>
							<div class="col px-1 too-small-hide"></div>
				  	</div>
				</form>
			</div>

			
			
			<?php
			if(!$capi_ready){
				echo '<!--';
			}
			?>
			<div class="card base-card bg-light border p-3 mt-2">
				<div class="">
					<h4 class="my-2  pb-2" style="color:#AC39FD"><b>CAPI Test Events</b></h4>
						
				</div>
				<p class="pt-2" style="margin-left:2px;">Your Meta CAPI settings are ready to test. Send a test event to validate your settings.</p>
				<div id="capi_response" class="py-2"></div>
				<div class="text-center row px-2">
					<div class="col px-1 too-small-hide"></div>
					<div class="col px-1">
						<button class="btn btn-block cust-btn text-center" onclick="send_test_event();return false;">Send Test Event</button>
					</div>
					<div class="col px-1 too-small-hide"></div>
				</div>
			</div>
			<?php
			if(!$capi_ready){
				echo '-->';
			}
			?>
			
			<?php
			if(!$script_ready){
				echo '<!--';
			}
			?>
			<div class="card base-card bg-light border p-3 mt-2">
				<div class="">
					<h4 class="my-2  pb-2" style="color:#AC39FD"><b>Plugin Code</b></h4>
						
				</div>
				<p class="pt-2" style="margin-left:2px; margin-bottom:-10px; font-weight:600;">FOR NON-SHOPIFY ACCOUNTS ONLY:</p>
				<p class="pt-2" style="margin-left:2px;">Copy and paste this code snippet into your website body, just above the closing body tag (&lt;/body&gt;).</p>
				
				<br/><b>Popsixle Code Snippet</b></br/>
				<pre class="snippet"><?php echo $script;?></pre>
				
			</div>
			<?php
			if(!$script_ready){
				echo '-->';
			}
			?>
			
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
			
			function show_success( msg ){
				
				if(msg == 'beta'){
					msg = 'Welcome! Please check your email for your account verification link.';
				} else if(msg == 'waitlist'){
					msg = 'Your request for a closed beta invite was received.';
				} else if(msg == 'premium'){
					msg = 'Congratulations! You successfully unlocked Premium Campaigns. Start earning more rewards now.';
				} else if(msg == 'saved'){
					msg = 'Your account information was saved successfully.';
				} else if(msg == 'password'){
					msg = 'Your password was saved successfully.';
				} else if(msg == 'verify'){
					msg = 'Thank you for verifying your email.';
				} else if(msg == 'mobile'){
					msg = 'Thank you for verifying your mobile number.';
				}
				
				$('#success_block').html('<p class="mb-0">'+msg+'</p><div class="x-close rounded">x</div>').show();
				$("html, body").animate({ scrollTop: 0 }, "slow");
				
				$( '.x-close' ).click(function(){
					$('#success_block').hide();
				});

			}
			
			function show_error( msg ){
				if(msg == 'poa'){
					msg = 'Oh no! You selected too many wrong answers and need to retry that campaign later. Remember to pay close attention to the content.';
				} else {
					msg = 'Something went wrong. Please try again later.';
				}
				
				$('#error_block').html('<p class="mb-0">'+msg+'</p><div class="x-close rounded">x</div>').show();
				//$("html, body").animate({ scrollTop: 0 }, "slow");
				
				$( '.x-close' ).click(function(){
					$('#error_block').hide();
				});

			}
			
			function send_test_event(){
				var args = {};
				args.mode = 'ping_capi';
				args.event_type = 'Purchase';
				args.event_value = '1.00';
				args.t2 = $('#pixel_id').val();
				args.t3 = $('#access_token').val();
				args.t4 = $('#test_event_code').val();
				
				$.post( "/<?php echo $base;?>pop6_ping.php", args, function( data ) {
					
					// data = JSON.parse(data);
					
					if( typeof data.response != 'undefined' ){
						
						if( typeof data.response.fbtrace_id != 'undefined' ){
							var markup = '<p><b>CAPI response:</b><br/>Your test event was received.</p>';
						} 
						
						if(typeof data.response.error != 'undefined') {
							var markup = '<p><b>CAPI response:</b><br/><span class="text-red">Your event was not received. Error: '+data.response.error.message +'</span></p>';
						}
						
						$('#capi_response').html(markup);
						
					} else if( typeof data.debug != 'undefined' ){
						console.log(data.debug);
					}
				});
			}
			
		</script>



	</body>
</html>