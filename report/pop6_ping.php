<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header('Content-Type: application/json');
include('php/db.php');


$referrer = $_SERVER['HTTP_REFERER'];
$referrer_chunks = explode('/', $referrer);
$domain = $referrer_chunks[2];

$domain = str_ireplace('www.', '', $domain);
$data = array();
$conn = new mysqli($var_h, $var_u, $var_p, $var_n, $port);
if ($conn->connect_error) {
		die("MySQL Database Connection Failed: " . $conn->connect_error);
} 

if( !empty( $_POST['t1'] ) ){
	$_POST['t1'] = preg_replace("/[^A-Za-z0-9]/", "", $_POST['t1'] );
}

if( !empty( $_POST['passed_value'] ) ){
	$_POST['passed_value'] = preg_replace("/[^A-Za-z0-9]/", "", $_POST['passed_value'] );
}

if( !empty( $_POST['passed_key'] ) ){
	$_POST['passed_key'] = preg_replace("/[^A-Za-z0-9\_]/", "", $_POST['passed_key'] );
}
if (empty($_POST['mode']) || $_POST['mode'] == ""){
	exit();
}
if( $_POST['mode'] == 'init' && $_POST['passed_key'] == 'token'){
	
	//add A-Z0-9 regex replace santization for $_POST['passed_value']
	$sql = 'SELECT *, accounts.sha_token AS `t1` FROM accounts LEFT join fb_settings ON accounts.sha_token = fb_settings.sha_token WHERE accounts.sha_token = "'.$_POST['passed_value'].'" AND "'.date("Y-m-d").'" <= expires;';
	$results = sql_select_to_array($sql);
		
	//is there an active account
	if( !empty($results[0]['t1']) ){
		$account_id = $results[0]['id'];
		if(!empty($_POST['sh_c']) && $_POST['sh_c'] != "undefined" ){
			$sh_cart_id = $_POST['sh_c'];
		} else {
			$sh_cart_id = '';
		}
		
		if(!empty($_POST['fbp']) && $_POST['fbp'] != "undefined" ){
			$fbp = $_POST['fbp'];
		} else {
			$fbp = '';
		}
		if(!empty($_POST['fbc']) && $_POST['fbc'] != "undefined" ){
			$fbc = $_POST['fbc'];
		} else {
			$fbc = '';
		}
		
		$ip = $_SERVER['REMOTE_ADDR'];
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$user_agent_clean = preg_replace("/[^A-Za-z]/", "", $_SERVER['HTTP_USER_AGENT'] );
		
		
		///// DYLAN - again, worth fixing this to be a prepared statement to prevent errors and/or sql injection
		
		if( !empty($fbc) && $fbc != "undefined" ){
			$sql = 'INSERT INTO `session` (`ip`,`user_agent`,`user_agent_full`,`shop`,`fbp`,`fbc`) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `fbp` = ?, `fbc` = ?;';
			if($stmt = mysqli_prepare($conn, $sql)){
				mysqli_stmt_bind_param($stmt,'ssssssss', $s1,$s2,$s3,$s4,$s5,$s6,$s7,$s8); 
				
				$s1 = $ip;
				$s2 = $user_agent_clean;
				$s3 = $user_agent;
				$s4 = $results[0]['shop'];
				$s5 = $fbp;
				$s6 = $fbc;
				$s7 = $fbp;
				$s8 = $fbc;		
		
				mysqli_stmt_execute($stmt);	

				//echo 'true';
				mysqli_stmt_close($stmt);
				// mysqli_close($conn);
			} else {
				//echo 'false';
			}

			// $sql = 'INSERT INTO `session` (`ip`,`user_agent`,`shop`,`fbp`,`fbc`) VALUES ("'.$ip.'","'.$user_agent.'","'.$results[0]['shop'].'","'.$fbp.'","'.$fbc.'") ON DUPLICATE KEY UPDATE `fbp` = "'.$fbp.'", `fbc` = "'.$fbc.'";';
		} else if( !empty($fbp) ){
			$sql = 'INSERT INTO `session` (`ip`,`user_agent`,`user_agent_full`,`shop`,`fbp`,`fbc`) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `fbp` = ?;';
			if($stmt = mysqli_prepare($conn, $sql)){
				mysqli_stmt_bind_param($stmt,'sssssss', $s1,$s2,$s3,$s4,$s5,$s6,$s7); 
				
				$s1 = $ip;
				$s2 = $user_agent_clean;
				$s3 = $user_agent;
				$s4 = $results[0]['shop'];
				$s5 = $fbp;
				$s6 = $fbc;
				$s7 = $fbp;	
		
				mysqli_stmt_execute($stmt);	

				//echo 'true';
				mysqli_stmt_close($stmt);
				// mysqli_close($conn);
			} else {
				//echo 'false';
			}

			// $sql = 'INSERT INTO `session` (`ip`,`user_agent`,`shop`,`fbp`,`fbc`) VALUES ("'.$ip.'","'.$user_agent.'","'.$results[0]['shop'].'","'.$fbp.'","'.$fbc.'") ON DUPLICATE KEY UPDATE `fbp` = "'.$fbp.'";';
		} else {
			$sql = 'INSERT INTO `session` (`ip`,`user_agent`,`user_agent_full`,`shop`,`fbp`,`fbc`) VALUES (?,?,?,?,?,?) ON DUPLICATE KEY UPDATE `created` = NOW();';
			if($stmt = mysqli_prepare($conn, $sql)){
				mysqli_stmt_bind_param($stmt,'ssssss', $s1,$s2,$s3,$s4,$s5,$s6); 
				
				$s1 = $ip;
				$s2 = $user_agent_clean;
				$s3 = $user_agent;
				$s4 = $results[0]['shop'];
				$s5 = $fbp;
				$s6 = $fbc;
		
				mysqli_stmt_execute($stmt);	

				//echo 'true';
				mysqli_stmt_close($stmt);
				// mysqli_close($conn);
			} else {
				//echo 'false';
			}

			// $sql = 'INSERT INTO `session` (`ip`,`user_agent`,`shop`,`fbp`,`fbc`) VALUES ("'.$ip.'","'.$user_agent.'","'.$results[0]['shop'].'","'.$fbp.'","'.$fbc.'") ON DUPLICATE KEY UPDATE `created` = NOW();';
		}

		// sqli_query($sql);
		
		if( !empty($sh_cart_id) ){
			$sql = 'UPDATE `session` SET `sh_cart_id` = ?, `user_agent_full` = ? WHERE `ip` = ? AND `user_agent` = ? AND `shop` = ?;';
			if($stmt = mysqli_prepare($conn, $sql)){
				mysqli_stmt_bind_param($stmt,'sssss', $s1,$s2,$s3,$s4,$s5); 
				
				$s1 = $sh_cart_id;
				$s2 = $user_agent;
				$s3 = $ip;
				$s4 = $user_agent_clean;
				$s5 = $results[0]['shop'];

		
				mysqli_stmt_execute($stmt);	

				//echo 'true';
				mysqli_stmt_close($stmt);
				// mysqli_close($conn);
				// error_log("\n\n There IS a cart in INIT. ".$sh_cart_id."\n\n",0);
			} else {
				//echo 'false';
				
			}
			// $sql = 'UPDATE `session` SET `sh_cart_id` = "'.$sh_cart_id.'" WHERE `ip` = "'.$ip.'" AND `user_agent` = "'.$user_agent.'" AND `shop` = "'.$results[0]['shop'].'";';
			// sqli_query($sql);
		} else  {
			
			// error_log("\n\n There is NO cart in INIT. ".$sh_cart_id."\n\n",0);
		}
		
	
		
		if( stristr($results[0]['domain'], $domain) != false){
			
			$sql = 'SELECT * FROM `session` WHERE `ip` = "'.$ip.'" AND `user_agent` = "'.$user_agent_clean.'" AND `shop` = "'.$results[0]['shop'].'" ORDER BY created DESC;';
			$session_data = sql_select_to_array($sql);
			$data['settings'] = array(
				//'email' => $results[0]['email'],
				't1' => $results[0]['t1'],
				'account_id' => $account_id
			);
			
			if( !empty( $results[0]['pixel_id'] ) && $results[0]['pixel_id'] != "undefined" ){
				$data['settings']['t2'] = $results[0]['pixel_id'];
			}
			
			if( !empty( $results[0]['access_token'] ) && $results[0]['access_token'] != "undefined" ){
				$data['settings']['t3'] = $results[0]['access_token'];
			}
			
			if( !empty( $results[0]['test_event_code'] ) && $results[0]['test_event_code'] != "undefined" ){
				$data['settings']['t4'] = $results[0]['test_event_code'];
			}
			
			if( !empty( $results[0]['account_mode'] ) && $results[0]['account_mode'] != "undefined" ){
				$data['settings']['account_mode'] = $results[0]['account_mode'];
			}
			
			if( !empty( $session_data[0]['created'] ) && $session_data[0]['created'] != 'undefined' ){
				$data['settings']['session_id'] = $session_data[0]['id'];
				$data['pl'] = array();
				$keys = array('em','ph','fn','ln','fbp','fbc', 'shop');
				foreach($keys as $k){
					if( !empty( $session_data[0][$k] ) && $session_data[0][$k] != "undefined" && $session_data[0][$k] != ""){
						$data['pl'][$k] = $session_data[0][$k];
					}
				}
			}
			
			
		} else {
			$data['error'] = '(1) Invalid domain.';
			$data['domain1'] = $domain;
			$data['domain2'] = $results[0]['domain'];
			// $data['bool'] = str_contains($results[0]['domain'], $domain);
			
		}

	} else {
		
		
		$sql = 'SELECT expires, domain FROM accounts WHERE accounts.sha_token = "'.$_POST['passed_value'].'";';
		$results = sql_select_to_array($sql);
		
		if( is_array( $results[0]) ){
			if( $domain == $results[0]['domain'] ){
				if( !empty($results[0]['expires']) ){
					$data['expires'] = $results[0]['expires'];
				} else {
					$data['error'] = '(2) Please activate your license: team@popsixle.com';
				}

			} else {
				$data['error'] = '(3) Invalid domain.';
			}

		} else {
			$data['error'] = '(4) No logged in account.';
		}
				
		unset($data['settings']);

	}
	
	
}
// error_log("\n\n MODE".$_POST['mode']."\n\n",0);

if( $_POST['mode'] == 'fb_settings' ){
	
	if( strlen($_POST['fb_pixel_id']) >= 12){
		$fb_pixel_id = $_POST['fb_pixel_id'];
	}
	
	if( strlen($_POST['fb_access_token']) >= 12){
		$fb_access_token = $_POST['fb_access_token'];
	}
	
	if( strlen($_POST['fb_test_event_code']) > 8){
		$fb_test_event_code = $_POST['fb_test_event_code'];
	}
	
	$response = send_to_CAPI_test($fb_pixel_id,$fb_access_token,$fb_test_event_code);
	
	$response = json_decode($response, true);
	

	
	
	if( !empty( $response['fbtrace_id'] ) ){
		
		$data['settings'] = array(
			't1' => $_POST['t1'],
			't2' => $fb_pixel_id,
			't3' => $fb_access_token,
			't4' => $fb_test_event_code
		);
		$sql = 'INSERT INTO fb_settings (sha_token,pixel_id,access_token,test_event_code) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE `pixel_id`=VALUES(`pixel_id`), `access_token`=VALUES(`access_token`), `test_event_code`=VALUES(`test_event_code`);';
			if($stmt = mysqli_prepare($conn, $sql)){
				mysqli_stmt_bind_param($stmt,'ssss', $s1,$s2,$s3,$s4); 
				
				$s1 = $_POST['t1'];
				$s2 = $fb_pixel_id;
				$s3 = $fb_access_token;
				$s4 = $fb_test_event_code;	
		
				mysqli_stmt_execute($stmt);	

				//echo 'true';
				mysqli_stmt_close($stmt);
				// mysqli_close($conn);
			} else {
				//echo 'false';
			}
		// $sql = 'INSERT INTO fb_settings (sha_token,pixel_id,access_token,test_event_code) VALUES ("'.$_POST['t1'].'","'.$fb_pixel_id.'","'.$fb_access_token.'","'.$fb_test_event_code.'") ON DUPLICATE KEY UPDATE `pixel_id`=VALUES(`pixel_id`), `access_token`=VALUES(`access_token`), `test_event_code`=VALUES(`test_event_code`);';
		// sqli_query($sql);
	} else {
		$data['debug'] = $response;
	}
} else if( $_POST['mode'] == 'ping_capi' ){
	$data['response'] = send_to_CAPI_event($_POST);
} else if( $_POST['mode'] == 'delete_event'  ){
	$sql = 'DELETE FROM events WHERE id = ?;';
	if($stmt = mysqli_prepare($conn, $sql)){
		mysqli_stmt_bind_param($stmt,'s', $s1); 
		
		$s1 = $_POST['id'];

		mysqli_stmt_execute($stmt);	

		//echo 'true';
		mysqli_stmt_close($stmt);
		// mysqli_close($conn);
	} else {
		//echo 'false';
	}

	// $sql = 'DELETE FROM events WHERE id = "'.$_POST['id'].'";';
	// $result = sqli_query($sql);
} else if( $_POST['mode'] == 'delete_form_field'  ){
	$sql = 'DELETE FROM form_fields WHERE id = ?;';
	if($stmt = mysqli_prepare($conn, $sql)){
		mysqli_stmt_bind_param($stmt,'s', $s1); 
		
		$s1 = $_POST['id'];

		mysqli_stmt_execute($stmt);	

		//echo 'true';
		mysqli_stmt_close($stmt);
		// mysqli_close($conn);
	} else {
		//echo 'false';
	}
	// $sql = 'DELETE FROM form_fields WHERE id = "'.$_POST['id'].'";';
	// $result = sqli_query($sql);
	//$data['delete_query'] = $sql;
	
} else if( $_POST['mode'] == 'get_events'  ){
	
	if( !empty( $_POST['t1'] ) ){
		$sql = 'SELECT * FROM events WHERE account_token = "'.$_POST['t1'].'";';
		$data['events'] = sql_select_to_array($sql);	
	}	else {
		$data['events'] = 0;
	}
	
} else if( $_POST['mode'] == 'get_form_fields'  ){	
	if( !empty( $_POST['t1'] ) ){
		$sql = 'SELECT * FROM form_fields WHERE account_token = "'.$_POST['t1'].'";';
		$data['form_fields'] = sql_select_to_array($sql);
	} else {
		$data['form_fields'] = 0;
	}
} else if( $_POST['mode'] == 'cart_sync'  ){	
	
	$data['response'] = 'cart sync error';
	
	$sql = 'SELECT *, accounts.sha_token AS `t1` FROM accounts LEFT join fb_settings ON accounts.sha_token = fb_settings.sha_token WHERE accounts.sha_token = "'.$_POST['t1'].'" AND "'.date("Y-m-d").'" <= expires;';
	$results = sql_select_to_array($sql);
		
	//is there an active account
	if( !empty($results[0]['t1']) ){
		
		$ip = $_SERVER['REMOTE_ADDR'];
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$user_agent_clean = preg_replace("/[^A-Za-z]/", "", $_SERVER['HTTP_USER_AGENT'] );
		
		$data['response'] = '1st query worked';
		$data['shop'] = $results[0]['shop'];
		$data['sh_c'] = $_POST['sh_c'];
		
		if( !empty($_POST['sh_c']) ){
			$sh_cart_id = $_POST['sh_c'];
			// error_log("\n\n There IS a cart in CART SYNC. ".$sh_cart_id."\n\n",0);


			$sql = 'UPDATE `session` SET `sh_cart_id` = ?, `user_agent_full` = ? WHERE `ip` = ? AND `user_agent` = ? AND `shop` = ?;';
			if($stmt = mysqli_prepare($conn, $sql)){
				mysqli_stmt_bind_param($stmt,'sssss', $s1,$s2,$s3,$s4,$s5); 
				
				$s1 = $sh_cart_id;
				$s2 = $user_agent;
				$s3 = $ip;
				$s4 = $user_agent_clean;
				$s5 = $results[0]['shop'];


				mysqli_stmt_execute($stmt);	

				//echo 'true';
				mysqli_stmt_close($stmt);
				// mysqli_close($conn);
				$data['response'] = 'cart id sync success';
			} else {
				//echo 'false';
			}

			// $sql = 'UPDATE `session` SET `sh_cart_id` = "'.$sh_cart_id.'" WHERE `ip` = "'.$ip.'" AND `user_agent` = "'.$user_agent.'" AND `shop` = "'.$results[0]['shop'].'";';
			// sqli_query($sql);
			//$data['sql'] = $sql;
		} else {
			// error_log("\n\n There IS NO cart in CART SYNC. ".$sh_cart_id."\n\n",0);

		}
	}
} else if( $_POST['mode'] == 'session_sync' ){
	$data['response'] = 'session sync error';
	
	$sql = 'SELECT *, accounts.sha_token AS `t1` FROM accounts LEFT join fb_settings ON accounts.sha_token = fb_settings.sha_token WHERE accounts.sha_token = "'.$_POST['t1'].'" AND "'.date("Y-m-d").'" <= expires;';
	$results = sql_select_to_array($sql);
		
	//is there an active account
	if( !empty($results[0]['t1']) ){
		
		$ip = $_SERVER['REMOTE_ADDR'];
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		$user_agent_clean = preg_replace("/[^A-Za-z]/", "", $_SERVER['HTTP_USER_AGENT'] );

		
		$data['response'] = '1st query worked';
		$data['key'] = $_POST['passed_key'];
		$data['value'] = $_POST['passed_value'];
		
		if( !empty($data['value']) ){

			$sql = 'UPDATE `session` SET `'.$data['key'].'` = ?, `user_agent_full` = ?  WHERE `ip` = ? AND `user_agent` = ? AND `shop` = ?;';
			if($stmt = mysqli_prepare($conn, $sql)){
				mysqli_stmt_bind_param($stmt,'sssss', $s1,$s2,$s3,$s4,$s5); 
				
				$s1 = $data['value'];
				$s2 = $user_agent;
				$s3 = $ip;
				$s4 = $user_agent_clean;
				$s5 = $results[0]['shop'];


				mysqli_stmt_execute($stmt);	

				//echo 'true';
				mysqli_stmt_close($stmt);
				// mysqli_close($conn);
			} else {
				//echo 'false';
			}

			// $sql = 'UPDATE `session` SET `'.$data['key'].'` = "'.$data['value'].'" WHERE `ip` = "'.$ip.'" AND `user_agent` = "'.$user_agent.'" AND `shop` = "'.$results[0]['shop'].'";';
			// sqli_query($sql);
			$data['response'] = 'session sync success';
			//$data['sql'] = $sql;
		}
	}
}

echo json_encode($data);

function send_to_CAPI_test($fb_pixel_id,$fb_access_token,$fb_test_event_code){
	$url = 'https://graph.facebook.com/v11.0/'.$fb_pixel_id.'/events?access_token='.$fb_access_token;
	$ch = curl_init( $url );
	
	$payload = array(
		"test_event_code" => $args['t4'],
		'data' => array(
			array(
				"event_name" =>  $args['event_type'],
	            "event_time" =>  time(),
	            "event_source_url" =>  'https://'.$_SERVER['HTTP_HOST'].'/index.php',
	            "action_source" =>  "website",
	            "user_data" =>  array(/*
	                "em" =>  $args['em'],
	                "ph" =>  $args['ph'],
	                "fn" =>  $args['fn'],
	                "ln" =>  $args['ln'],
	                "fbp" =>  $args['fbp'],
	                "fbc" =>  $args['fbc'],
	                "client_ip_address" =>  $_SERVER['REMOTE_ADDR'],
	                "client_user_agent" =>  $_SERVER['HTTP_USER_AGENT']
	            */),
	            "custom_data" =>  array(
	                "currency" =>  "USD"/*,
	                "value" =>  $args['event_value']*/
	            )
			)
		)
	);
	
	if( $args['account_mode'] == 'debug'){
		$payload['test_event_code'] = $args['t4'];
	}
	
	
	if( !empty($args['em']) ){
		if ($args['em'] == "undefined") {

		} else {
			$payload['data'][0]['user_data']['em'] = $args['em'];
			$vars['em'] = $args['em'];
		}
	}
	
	if( !empty($args['ph']) ){
		if ($args['ph'] == "undefined") {

		} else {
			$payload['data'][0]['user_data']['ph'] = $args['ph'];
			$vars['ph'] = $args['ph'];
		}
	}
	
	if( !empty($args['fn']) ){
		if ($args['fn'] == "undefined") {

		} else {
			$payload['data'][0]['user_data']['fn'] = $args['fn'];
			$vars['fn'] = $args['fn'];
		}
	}
	
	if( !empty($args['ln']) ){
		if ($args['ln'] == "undefined") {

		} else {
			$payload['data'][0]['user_data']['ln'] = $args['ln'];
			$vars['ln'] = $args['ln'];
		}
	}
	
	if( !empty($args['fbp']) ){
		if ($args['fbp'] == "undefined") {

		} else {
			$payload['data'][0]['user_data']['fbp'] = $args['fbp'];
			$vars['fbp'] = $args['fbp'];
		}
	}
	
	if( !empty($args['fbc']) ){
		if ($args['fbc'] == "undefined" || $args['fbc'] == NULL || $args['fbc'] == "null") {

		} else {
			$payload['data'][0]['user_data']['fbc'] = $args['fbc'];
			$vars['fbc'] = $args['fbc'];
		}

	}
	
	if( !empty($_SERVER['REMOTE_ADDR']) ){
		$payload['data'][0]['user_data']['client_ip_address'] = $_SERVER['REMOTE_ADDR'];
	}
	
	if( !empty($_SERVER['HTTP_USER_AGENT']) ){
		$payload['data'][0]['user_data']['client_user_agent'] = $_SERVER['HTTP_USER_AGENT'];
	}
	
	if( !empty($args['event_value']) ){
		$payload['data'][0]['custom_data']['value'] = $args['event_value'];
	}
	
	if( !empty($args['content_ids']) ){
		$payload['data'][0]['custom_data']['content_ids'] = $args['content_ids'];
	}
	
	if( !empty($args['content_name']) ){
		$payload['data'][0]['custom_data']['content_name'] = $args['content_name'];
	}
	
	if( !empty($args['content_group']) ){
		$payload['data'][0]['custom_data']['content_group'] = $args['content_group'];
	}
	
	if( !empty($args['content_category']) ){
		$payload['data'][0]['custom_data']['content_category'] = $args['content_category'];
	}
	
	if( !empty($args['num_items']) ){
		$payload['data'][0]['custom_data']['num_items'] = $args['num_items'];
	}
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$fb_result = curl_exec($ch);
	curl_close($ch);
	// error_log("\n\n   ".$fb_result."\n\n",0);

	return $fb_result;
}

function send_to_CAPI_event($args, $test = false){
	$url = 'https://graph.facebook.com/v11.0/'.$args['t2'].'/events?access_token='.$args['t3'];

	$ch = curl_init( $url );
	
	$payload = array(
		//"test_event_code" => $args['t4'],
		'data' => array(
			array(
				"event_name" =>  $args['event_type'],
	            "event_time" =>  time(),
	            "event_source_url" =>  'https://'.$_SERVER['HTTP_HOST'].'/index.php',
	            "action_source" =>  "website",
	            "user_data" =>  array(/*
	                "em" =>  $args['em'],
	                "ph" =>  $args['ph'],
	                "fn" =>  $args['fn'],
	                "ln" =>  $args['ln'],
	                "fbp" =>  $args['fbp'],
	                "fbc" =>  $args['fbc'],
	                "client_ip_address" =>  $_SERVER['REMOTE_ADDR'],
	                "client_user_agent" =>  $_SERVER['HTTP_USER_AGENT']
	            */),
	            "custom_data" =>  array(
	               // "currency" =>  "USD"/*,
	              //  "value" =>  $args['event_value']*/
	            )
			)
		)
	);
	
	$vars = array(
		'event_type' => $args['event_type'],
		't1' => $args['t1'],
		't2' => $args['t2'],
		't3' => $args['t3'],
		't4' => $args['t4'],
		'shop' => $args['shop']
	);
	
	if( $args['account_mode'] == 'debug'){
		$payload['test_event_code'] = $args['t4'];
	}
	
	if( !empty($args['event_id']) ){
		if ($args['event_id'] == "undefined") {

		} else {
			$payload['data'][0]['event_id'] = $args['event_id'];
			//$vars['em'] = $args['em'];  <-- this can be added, but needs a column in the database
		}
	}
	
	if( !empty($args['em']) ){
		if ($args['em'] == "undefined") {

		} else {
			$payload['data'][0]['user_data']['em'] = $args['em'];
			$vars['em'] = $args['em'];
		}
	}
	
	if( !empty($args['ph']) ){
		if ($args['ph'] == "undefined") {

		} else {
			$payload['data'][0]['user_data']['ph'] = $args['ph'];
			$vars['ph'] = $args['ph'];
		}
	}
	
	if( !empty($args['fn']) ){
		if ($args['fn'] == "undefined") {

		} else {
			$payload['data'][0]['user_data']['fn'] = $args['fn'];
			$vars['fn'] = $args['fn'];
		}
	}
	
	if( !empty($args['ln']) ){
		if ($args['ln'] == "undefined") {

		} else {
			$payload['data'][0]['user_data']['ln'] = $args['ln'];
			$vars['ln'] = $args['ln'];
		}
	}
	
	if( !empty($args['fbp']) ){
		if ($args['fbp'] == "undefined") {

		} else {
			$payload['data'][0]['user_data']['fbp'] = $args['fbp'];
			$vars['fbp'] = $args['fbp'];
		}
	}
	
	if( !empty($args['fbc']) ){
		if ($args['fbc'] == "undefined" || $args['fbc'] == NULL || $args['fbc'] == "null") {

		} else {
			$payload['data'][0]['user_data']['fbc'] = $args['fbc'];
			$vars['fbc'] = $args['fbc'];
		}

	}
	
	if( !empty($_SERVER['REMOTE_ADDR']) ){
		$payload['data'][0]['user_data']['client_ip_address'] = $_SERVER['REMOTE_ADDR'];
		$vars['ip'] = $_SERVER['REMOTE_ADDR'];
	}
	
	if( !empty($_SERVER['HTTP_USER_AGENT']) ){
		$payload['data'][0]['user_data']['client_user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		$vars['user_agent'] = preg_replace("/[^A-Za-z]/", "", $_SERVER['HTTP_USER_AGENT'] );
		$vars['user_agent_full'] = $_SERVER['HTTP_USER_AGENT'];
	}
	
	if( !empty($args['event_value']) ){
		$payload['data'][0]['custom_data']['value'] = $args['event_value'];
		$vars['event_value'] = $args['event_value'];
	} else {
		$payload['data'][0]['custom_data']['value'] = '0.00';
		$vars['event_value'] = '0.00';
	}
	
	if( !empty($args['currency']) ){
		$payload['data'][0]['custom_data']['currency'] = $args['currency'];
		$vars['currency'] = $args['currency'];
	} else {
		$payload['data'][0]['custom_data']['currency'] = 'USD';
		$vars['currency'] = 'USD';
	}
	
	if( !empty($args['content_ids']) ){
		$payload['data'][0]['custom_data']['content_ids'] = $args['content_ids'];
	}
	
	if( !empty($args['content_name']) ){
		$payload['data'][0]['custom_data']['content_name'] = $args['content_name'];
	}
	
	if( !empty($args['content_group']) ){
		$payload['data'][0]['custom_data']['content_group'] = $args['content_group'];
	}
	
	if( !empty($args['content_category']) ){
		$payload['data'][0]['custom_data']['content_category'] = $args['content_category'];
	}
	
	if( !empty($args['num_items']) ){
		$payload['data'][0]['custom_data']['num_items'] = $args['num_items'];
	}
	
	$payload = json_encode($payload);
	
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$fb_result = curl_exec($ch);
	curl_close($ch);
	
	$fb_result = json_decode( $fb_result, true);
	$vars['fbtrace_id'] = $fb_result['fbtrace_id'];
	
	//unset( $vars['currency'] );
	
	$columns_str = '';
	$values_str = '';
	$first = true;
	
	foreach($vars as $col => $val){
		if( $first ){
			$first = false;
			$columns_str .= "`".$col."`";
			$values_str .= '"'.$val.'"';
		} else {
			$columns_str .= ",`".$col."`";
			$values_str .= ',"'.$val.'"';
		}
	}
	
	$sql = 'INSERT INTO processed_events ('.$columns_str.') VALUES ('.$values_str.');';
	// error_log("\n\n   ".$sql."\n\n",0);

	sqli_query($sql);
	$sql = 'SELECT *, accounts.sha_token AS `t1` FROM accounts LEFT join fb_settings ON accounts.sha_token = fb_settings.sha_token WHERE accounts.sha_token = "'.$args['t1'].'" AND "'.date("Y-m-d").'" <= expires;';
	$results = sql_select_to_array($sql);
	if( !empty($results[0]['t1']) ){
		$account_id = $results[0]['id'];
	}
	$sql = 'SELECT * FROM `session` WHERE `ip` = "'.$_SERVER['REMOTE_ADDR'].'" AND `user_agent` = "'.preg_replace("/[^A-Za-z]/", "", $_SERVER['HTTP_USER_AGENT']).'" AND `shop` = "'.$results[0]['shop'].'" ORDER BY created DESC;';
	$session_data = sql_select_to_array($sql);
	if( !empty( $session_data[0]['created'] ) && $session_data[0]['created'] != 'undefined' ){
		$session_id = $session_data[0]['id'];
	}
	// //$fb_result['sql'] = $sql;
	$vars_min = array();
	
	$vars_min['event_type'] = $args['event_type'];
	if(!empty($args['account_id'])){
		$vars_min['account_id'] = $args['account_id'];
	} else if (!empty($account_id)){
		$vars_min['account_id'] = $account_id;
	}
	if(!empty($args['session_id'])){
		$vars_min['session_id'] = $args['session_id'];
	} else if (!empty($session_id)){
		$vars_min['session_id'] = $session_id;
	}
	if(strpos($fb_result['fbtrace_id'], 'parameters') !== false || empty($fb_result['fbtrace_id'])){
		$vars_min['fbtrace_id'] = 0;
	} else {
		$vars_min['fbtrace_id'] = 1;
	}


	if( !empty($args['em']) ){
		if ($args['em'] == "undefined") {
			$vars_min['em'] = 0;
		} else {
			$vars_min['em'] = 1;
		}
	} else {
		$vars_min['em'] = 0;	
	}
	
	if( !empty($args['ph']) ){
		if ($args['ph'] == "undefined") {
			$vars_min['ph'] = 0;
		} else {
			$vars_min['ph'] = 1;
		}
	} else {
		$vars_min['ph'] = 0;	
	}
	
	if( !empty($args['fn']) ){
		if ($args['fn'] == "undefined") {
			$vars_min['fn'] = 0;
		} else {
			$vars_min['fn'] = 1;
		}
	} else {
		$vars_min['fn'] = 0;	
	}
	
	if( !empty($args['ln']) ){
		if ($args['ln'] == "undefined") {
			$vars_min['ln'] = 0;
		} else {
			$vars_min['ln'] = 1;
		}
	} else {
		$vars_min['ln'] = 0;	
	}
	
	if( !empty($args['fbp']) ){
		if ($args['fbp'] == "undefined") {
			$vars_min['fbp'] = 0;
		} else {
			$vars_min['fbp'] = 1;
		}
	} else {
		$vars_min['fbp'] = 0;	
	}
	
	if( !empty($args['fbc']) ){
		if ($args['fbc'] == "undefined") {
			$vars_min['fbc'] = 0;
		} else {
			$vars_min['fbc'] = 1;
		}
	} else {
		$vars_min['fbc'] = 0;	
	}
	
	if( !empty($_SERVER['REMOTE_ADDR']) ){
		$vars_min['ip'] = 1;
	} else {
		$vars_min['ip'] = 0;
	}
	
	if( !empty($_SERVER['HTTP_USER_AGENT']) ){
		$vars_min['user_agent'] = 1;
		$vars_min['user_agent_full'] = 1;
	} else{
		$vars_min['user_agent'] = 0;
		$vars_min['user_agent_full'] = 0;
	}
	
	if( !empty($args['event_value']) ){
		$vars_min['event_value'] = $args['event_value'];
	} else {
		$vars_min['event_value'] = '0.00';
	}
	
	if( !empty($args['currency']) ){
		$vars_min['currency'] = $args['currency'];
	} else {
		$vars_min['currency'] = 'USD';
	}


	$columns_str = '';
	$values_str = '';
	$first = true;
	
	foreach($vars_min as $col => $val){
		if( $first ){
			$first = false;
			$columns_str .= "`".$col."`";
			$values_str .= '"'.$val.'"';
		} else {
			$columns_str .= ",`".$col."`";
			$values_str .= ',"'.$val.'"';
		}
	}
	
	$sql = 'INSERT INTO processed_events_min ('.$columns_str.') VALUES ('.$values_str.');';
	// error_log("\n\n   ".$sql."\n\n",0);

	sqli_query($sql);

	return $fb_result;
}

?>