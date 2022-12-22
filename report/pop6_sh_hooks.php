<?php
include('php/db.php');
header("Access-Control-Allow-Origin: *");
$json = file_get_contents('php://input');
$data = json_decode($json, true);
//error logging $data... spacing for legibility in apache/bitnami instance
// error_log("\n\n   THIS IS THE DATA ".var_export($data, true)."\n\n",0);

$headers = apache_request_headers();

/* ------------------------ Setting variables ------------------------- */

$vars = array();
$vars_min = array();
$shop = $headers['Shop'];
$vars['shop'] = $shop;
$vars['event_value'] = 0.00;
$webhook = $headers['Webhook'];
$cart_token = '';
$user_agent = '';
$user_agent_full = '';
$ip = '';
$custom_purchase = false;
// $vars['currency'] = "USD";
//error logging webhook 
// error_log("        ".$webhook."\n\n",0);


/* ------------------------ Checking for type of Webhook ------------------------- */

if( $webhook == 'ORDERS_CREATE' ){
  sleep(1);
	$vars['event_type'] = 'Purchase';
  if(!empty($data['cart_token'])){
    $cart_token = $data['cart_token'];
  }
  if( !empty($shop) ){

    $sql = 'SELECT F.*, A.* FROM accounts A LEFT JOIN fb_settings F ON F.sha_token = A.sha_token WHERE A.shop = "'.$shop.'" AND "'.date("Y-m-d").'" <= A.expires;';
    $account_data = sql_select_to_array($sql);
  

    if ( empty($data['client_details']['user_agent']) && empty($data['client_details']['browser_ip'])){
      if ( $shop == "everydaydose.myshopify.com" ){
        $vars['event_type'] = 'CompleteRegistration';
      } else if ( $shop == "betterbody-co.myshopify.com"){
        $vars['event_type'] = 'Subscribe';
      } else if ( $shop == "thexcj.myshopify.com" ){
        $vars['event_type'] = 'Subscribe';
      } else if ( $shop == "curious-elixirs.myshopify.com" ){
        $vars['event_type'] = 'Subscribe';
      } else if ( $shop == "hiya-kids.myshopify.com" ){
        $vars['event_type'] = 'Subscribe';
      } else if ( $shop == "get-klora.myshopify.com" ){
        $vars['event_type'] = 'Subscribe';
      } else if ( $shop == "akua-production.myshopify.com" ){
        $vars['event_type'] = 'Subscribe';
      }  
    }
    
    if ( $shop == "jeff-599.myshopify.com" || $shop == "clinicianschoice.myshopify.com") {
      $custom_purchase = true;
      custom_bestfriends($data, $vars, $vars_min, $shop, $cart_token, $user_agent, $user_agent_full, $ip);
    }
    if ( $shop == "mynewtrend.myshopify.com" || $shop == "pearlmakeup.myshopify.com" || stristr($account_data[0]['metadata_mode'], 'bettercart') == 'bettercart') {
      $custom_purchase = true;
      custom_bettercart($data, $vars, $vars_min, $shop, $cart_token, $user_agent, $user_agent_full, $ip);
    }
    if ($custom_purchase == false){
      initiatecheckout_purchase($data, $vars, $vars_min, $shop, $cart_token, $user_agent, $user_agent_full, $ip);
    }
  }
} else if( $webhook == 'CUSTOMERS_UPDATE' ){
  $vars['event_type'] = 'AddPaymentInfo';
  addpaymentinfo($data, $vars, $vars_min, $shop, $cart_token, $user_agent, $user_agent_full);
} else if( $webhook == 'CUSTOMERS_CREATE' ){
  $vars['event_type'] = 'AddPaymentInfo';
  addpaymentinfo($data, $vars, $vars_min, $shop, $cart_token, $user_agent, $user_agent_full);
}

//Going to likely want to scrap this because Carts Update will always fire
else if( $webhook == 'CARTS_CREATE' ){
  sleep(6);
  $vars['event_type'] = 'AddToCart';
  if (!empty($data['token'])){
    $cart_token = $data['token'];
  } else if (!empty($data['id'])){
    $cart_token = empty($data['id']);
  }
  addtocart($data, $vars, $vars_min, $shop, $cart_token, $user_agent, $user_agent_full);
}
//----------------------------//


else if( $webhook == 'CARTS_UPDATE' ){
  sleep(8);
  $vars['event_type'] = 'AddToCart';
  if (!empty($data['token'])){
    $cart_token = $data['token'];
  } else if (!empty($data['id'])){
    $cart_token = empty($data['id']);
  }
  addtocart($data, $vars, $vars_min, $shop, $cart_token, $user_agent, $user_agent_full);
} else if( $webhook == 'CHECKOUTS_CREATE' ){
  $vars['event_type'] = 'InitiateCheckout';
  if(!empty($data['cart_token'])){
    $cart_token = $data['cart_token'];
  }
  initiatecheckout_purchase($data, $vars, $vars_min, $shop, $cart_token, $user_agent, $user_agent_full, $ip);
} else {
//if there's no webhook type or if it's not one of the expected types, exit
exit();
}
  


/* ------------------------ INITIATE CHECKOUT/PURCHASE ------------------------- */

function initiatecheckout_purchase($data, $vars, $vars_min, $shop, $cart_token, $user_agent, $user_agent_full, $ip){
  if( !empty($shop) ){

    $sql = 'SELECT F.*, A.* FROM accounts A LEFT JOIN fb_settings F ON F.sha_token = A.sha_token WHERE A.shop = "'.$shop.'" AND "'.date("Y-m-d").'" <= A.expires;';
    $account_data = sql_select_to_array($sql);

    if ( empty($data['client_details']['user_agent']) && empty($data['client_details']['browser_ip'])){
      if ($account_data[0]["reroute_opt_in"] == 1 || $account_data[0]["reroute_opt_in"] == "1" ){
        if (!empty($account_data[0]["reroute_setting"])){
          $vars['event_type'] = $account_data[0]["reroute_setting"];
        }
      }
    }
    if( !empty( $data['currency'] ) && $data['currency'] != "" && $data['currency'] != NULL && $data['currency'] != "NULL"){
      $vars['currency'] = $data['currency'];
    } else if( !empty( $data['customer'] ) && $data['customer'] != "" && $data['customer'] != NULL && $data['customer'] != "NULL"){
      $vars['currency'] = $data['customer']['currency'];
    } else if (!empty($account_data[0]["db_currency"]) && strtoupper($account_data[0]["db_currency"]) != "NULL"){
      $vars['currency'] = $account_data[0]["db_currency"];
    } else {
      $vars['currency'] = "USD";
    }
    if( !empty($cart_token) ){
		$keys = array('cart');
		$sql2 = 'SELECT id, em, ph, fn, ln, fbp, fbc, ip, user_agent, user_agent_full, sh_cust_id, created FROM `session` WHERE sh_cart_id = "'.$cart_token.'" ORDER BY created DESC;';
		$session_data = sql_select_to_array($sql2);	
		// Log sql2
    //error_log("     ".$sql2."\n\n",0);
	} 
	if(empty($session_data[0]['created'])){
		if( !empty($data['client_details']) ){
			//second try to load the session
			if (!empty($data['client_details']['browser_ip'] && $data['client_details']['browser_ip'] != "")) {
				$ip = $data['client_details']['browser_ip'];
				if (!empty($data['client_details']['user_agent']) && $data['client_details']['user_agent'] != "") {
					$user_agent_full = $data['client_details']['user_agent'] ;			
					$user_agent = preg_replace("/[^A-Za-z]/", "", $data['client_details']['user_agent'] );			
				}
				$sql2 = 'SELECT em, ph, fn, ln, fbp, fbc, ip, user_agent, user_agent_full, created FROM `session` WHERE (`ip` = "'.$ip.'") AND `shop` = "'.$shop.'" ORDER BY created DESC;';
				$session_data = sql_select_to_array($sql2);
        // Log sql2
				//error_log("     ".$sql2."\n\n",0);
			}
		} 
	}
	if(empty($session_data[0]['created'])){
		if (!empty($data['addresses'][0]['customer_id']) && $data['addresses'][0]['customer_id'] != '') {
			$sh_cust_id = $data['addresses'][0]['customer_id'];
			$sql2 = 'SELECT id, em, ph, fn, ln, fbp, fbc, ip, user_agent, user_agent_full, created FROM `session` WHERE (`sh_cust_id` = "'.$sh_cust_id.'") AND `shop` = "'.$shop.'" ORDER BY created DESC;';
			$session_data = sql_select_to_array($sql2);

      // Log sql2
			//error_log("     ".$sql2."\n\n",0);
		}
  }
	if(empty($session_data[0]['created'])){
		if (!empty($data['customer']['id']) && $data['customer']['id'] != '') {
			$sh_cust_id = $data['customer']['id'];
			$sql2 = 'SELECT id, em, ph, fn, ln, fbp, fbc, ip, user_agent, user_agent_full, created FROM `session` WHERE (`sh_cust_id` = "'.$sh_cust_id.'") AND `shop` = "'.$shop.'" ORDER BY created DESC;';
			$session_data = sql_select_to_array($sql2);

      // Log sql2
			//error_log("     ".$sql2."\n\n",0);
		}
	}
    if(empty($session_data[0]['created'])){
      $session_data = array();
    }
    if( !empty($data['client_details']) ){
      if (!empty($data['client_details']['browser_ip'] && $data['client_details']['browser_ip'] != "")) {
        if (empty($ip) || $ip == '') {
          $ip = $data['client_details']['browser_ip'];
        }
      }
      if (!empty($data['client_details']['user_agent']) && $data['client_details']['user_agent'] != "") {
        if (empty($user_agent) || $user_agent == '') {
          $user_agent_full = $data['client_details']['user_agent'];			
          $user_agent = preg_replace("/[^A-Za-z]/", "", $data['client_details']['user_agent'] );			
        }
      }
    }
    if (!empty($data['addresses']) && $data['addresses'] != ''){
      if (!empty($data['addresses'][0]['customer_id']) && $data['addresses'][0]['customer_id'] != '') {
        if (empty($sh_cust_id) || $sh_cust_id == '') {
          $sh_cust_id = $data['addresses'][0]['customer_id'];
        }
      }
    } else if (!empty($data['customer']) && $data['customer'] != ''){
      if (!empty($data['customer']['id']) && $data['customer']['id'] != '') {
        if (empty($sh_cust_id) || $sh_cust_id == '') {
          $sh_cust_id = $data['customer']['id'];
        }
      }
    }
      
    
    if( !empty( $account_data[0]['sha_token'] ) ){
      $account_id = $account_data[0]['id'];

      //load the account data
      $vars['t1'] = $account_data[0]['sha_token'];
      $vars['t2'] = $account_data[0]['pixel_id'];
      $vars['t3'] = $account_data[0]['access_token'];
      $vars['t4'] = $account_data[0]['test_event_code'];
      $vars['db_currency'] = $account_data[0]['db_currency'];
      
      //if session data is available, try to grab it
      if( !empty($session_data[0]['created']) ){
        if (!empty($session_data[0]['id']) && $session_data[0]['id'] != ""){
          $session_id = $session_data[0]['id'];
        } else {
          $session_id = "";
        }
        if (!empty($session_data[0]['fbp']) && $session_data[0]['fbp'] != ""){
          $vars['fbp'] = $session_data[0]['fbp'];
        } else {
          if (empty($vars['fbp'])) {
            $vars['fbp'] = "";
          }
        }
        if (!empty($session_data[0]['fbc']) && $session_data[0]['fbc'] != ""){
          $vars['fbc'] = $session_data[0]['fbc'];
        } else {
          if (empty($vars['fbc'])) {
            $vars['fbc'] = "";
          }
        }
        if (!empty($session_data[0]['em']) && $session_data[0]['em'] != ""){
          $vars['em'] = $session_data[0]['em'];
        } else {
          if (empty($vars['em'])) {
            $vars['em'] = "";
          }
        }
        if (!empty($session_data[0]['ph']) && $session_data[0]['ph'] != ""){
          $vars['ph'] = $session_data[0]['ph'];
        } else {
          if (empty($vars['ph'])) {
            $vars['ph'] = "";
          }
        }
        if (!empty($session_data[0]['fn']) && $session_data[0]['fn'] != ""){
          $vars['fn'] = $session_data[0]['fn'];
        } else {
          if (empty($vars['fn'])) {
            $vars['fn'] = "";
          }
        }
        if (!empty($session_data[0]['ln']) && $session_data[0]['ln'] != ""){
          $vars['ln'] = $session_data[0]['ln'];
        } else {
          if (empty($vars['ln'])) {
            $vars['ln'] = "";
          }
        }
        if (!empty($session_data[0]['ip']) && $session_data[0]['ip'] != ""){
          $vars['ip'] = $session_data[0]['ip'];
          $ip = $session_data[0]['ip'];
        } else {
          if (empty($vars['ip'])){
            if (!empty($ip)){
              $vars['ip'] = $ip;
            }
            $vars['ip'] = "";
            $ip = "";
          }
        }
        if (!empty($session_data[0]['sh_cart_id']) && $session_data[0]['sh_cart_id'] != ""){
          $sh_cart_id = $session_data[0]['sh_cart_id'];
        } else {
          $sh_cart_id = "";
        }
        if (!empty($session_data[0]['user_agent_full']) && $session_data[0]['user_agent_full'] != ""){
          $vars['user_agent_full'] = $session_data[0]['user_agent_full'] ;
          $user_agent_full = $session_data[0]['user_agent_full'];			
        } else if (!empty($session_data[0]['user_agent']) && $session_data[0]['user_agent'] != ""){
          $vars['user_agent_full'] = $session_data[0]['user_agent'] ;
          $user_agent_full = $session_data[0]['user_agent'];
        }
        if (!empty($session_data[0]['user_agent']) && $session_data[0]['user_agent'] != ""){
          $vars['user_agent'] = preg_replace("/[^A-Za-z]/", "", $session_data[0]['user_agent'] );	
          $user_agent = preg_replace("/[^A-Za-z]/", "", $session_data[0]['user_agent'] );						
        } else if (!empty($session_data[0]['user_agent_full']) && $session_data[0]['user_agent_full'] != ""){
          $vars['user_agent'] = preg_replace("/[^A-Za-z]/", "", $session_data[0]['user_agent_full'] );			
          $user_agent = preg_replace("/[^A-Za-z]/", "", $session_data[0]['user_agent_full'] );			
        }

        // error_log("  ".$shop."    SESSION MATCHED!\n\n",0);
      } else {
        // error_log("  ".$shop."    NO session matched\n\n",0);
      }
    } else {
      error_log("      NO NO NO NO NO NO NO popsixle account matches the passed shop:   ".$shop."\n\n",0);
      exit();
    }
  } else {
    error_log("      NO shop included with request.\n\n",0);
    exit();
  }

  if( !empty($data['total_price']) && round( floatval( $data['total_price']), 2 ) != 0.00 ){
    $vars['event_value'] = round( floatval( $data['total_price']), 2 );
  } else if (!empty($data['subtotal_price']) && round( floatval( $data['subtotal_price']), 2 ) != 0.00 ) {
    $vars['event_value'] = round( floatval( $data['subtotal_price']), 2 );
  } else if (!empty($data['current_subtotal_price']) && round( floatval( $data['current_subtotal_price']), 2 ) != 0.00 ) {
    $vars['event_value'] = round( floatval( $data['current_subtotal_price']), 2 );
  } else if (!empty($data['total_line_items_price']) && round( floatval( $data['total_line_items_price']), 2 ) != 0.00 ) {
    $vars['event_value'] = round( floatval( $data['total_line_items_price']), 2 );
  } else {
    $vars['event_value'] = 0.00;
  }

  if( !empty($user_agent_full) && $user_agent_full != '' && $user_agent_full != NULL){
    $vars['user_agent_full'] = $user_agent_full;
  }
  if( !empty($user_agent) && $user_agent != '' && $user_agent != NULL){
    $vars['user_agent'] = $user_agent;
  }
  
  if( !empty($ip) && $ip != '' && $ip != NULL){
    $vars['ip'] = $ip;
  }
  if (empty($vars['order_id']) && !empty($data['id'])) {
    $vars['order_id'] = $data['id'];
  }
  // if (empty($vars['external_id'])){
  //   if (!empty($data['customer'])){
  //    if (!empty($data['customer']['id'])) {
  //       $vars['external_id'] = $data['customer']['id'];
  //     }
  //   }
  // }

  if (!empty($data['customer']['email']) && $data['customer']['email'] != "" && $data['customer']['email'] != "NULL" && $data['customer']['email'] != NULL) {
    $vars['em'] = hash('sha256', $data['customer']['email']);
  } else if (!empty($data['addresses'][0]['email']) && $data['addresses'][0]['email'] != "") {
    $vars['em'] = hash('sha256', $data['addresses'][0]['email']);
  } else if (!empty($data['billing_address']['email']) && $data['billing_address']['email'] != "") {
    $vars['em'] = hash('sha256', $data['billing_address']['email']);
  } else if (!empty($data['contact_email']) && $data['contact_email'] != "") {
    $vars['em'] = hash('sha256', $data['contact_email']);
  } else if (!empty($data['email']) && $data['email'] != "") {
    $vars['em'] = hash('sha256', $data['email']);
  } else if (!empty($vars['em']) && $vars['em'] != "") {
  } else {
    $vars['em'] = "";
  }
  if (!empty($data['customer']['phone']) && $data['customer']['phone'] != "") {
    $vars['ph'] = hash('sha256', $data['customer']['phone']);
  } else if (!empty($data['addresses'][0]['phone']) && $data['addresses'][0]['phone'] != "") {
    $vars['ph'] = hash('sha256', $data['addresses'][0]['phone']);
  } else if (!empty($data['billing_address']['phone']) && $data['billing_address']['phone'] != "") {
    $vars['ph'] = hash('sha256', $data['billing_address']['phone']);
  } else if (!empty($data['phone']) && $data['phone'] != "") {
    $vars['ph'] = hash('sha256', $data['phone']);
  } else if (!empty($vars['ph']) && $vars['ph'] != "") {
  } else {
    $vars['ph'] = "";
  }
  if (!empty($data['customer']['first_name']) && $data['customer']['first_name'] != "" && $data['customer']['first_name'] != "NULL" && $data['customer']['first_name'] != NULL) {
    $vars['fn'] = hash('sha256', $data['customer']['first_name']);
  } else if (!empty($data['addresses'][0]['first_name']) && $data['addresses'][0]['first_name'] != "") {
    $vars['fn'] = hash('sha256', $data['addresses'][0]['first_name']);
  } else if (!empty($data['billing_address']['first_name']) && $data['billing_address']['first_name'] != "") {
    $vars['fn'] = hash('sha256', $data['billing_address']['first_name']);
  } else if (!empty($vars['fn']) && $vars['fn'] != "") {
  } else {
    $vars['fn'] = "";
  }
  if (!empty($data['customer']['last_name']) && $data['customer']['last_name'] != "" && $data['customer']['last_name'] != "NULL" && $data['customer']['last_name'] != NULL) {
    $vars['ln'] = hash('sha256', $data['customer']['last_name']);
  } else if (!empty($data['addresses'][0]['last_name']) && $data['addresses'][0]['last_name'] != "") {
    $vars['ln'] = hash('sha256', $data['addresses'][0]['last_name']);
  } else if (!empty($data['billing_address']['last_name']) && $data['billing_address']['last_name'] != "") {
    $vars['ln'] = hash('sha256', $data['billing_address']['last_name']);
  } else if (!empty($vars['ln']) && $vars['ln'] != "") {
  } else {
    $vars['ln'] = "";
  }
  if (!empty($data['customer']['id']) && $data['customer']['id'] != "" && $data['customer']['id'] != "NULL" && $data['customer']['id'] != NULL){
    $sh_cust_id = $data['customer']['id'];
  } else if (!empty($data['addresses'][0]['customer_id']) && $data['addresses'][0]['customer_id'] != '') {
      $sh_cust_id = $data['addresses'][0]['customer_id'];
  }
  
  if( empty($sh_cust_id) ){
    $sh_cust_id = '';
  }
  
  $cart_data = grab_cart_details($data);
  
 
  
  
  
  if( !empty($cart_data) ){
	  $vars['cart_data'] = $cart_data;
  }
  

  $fb_result = send_to_CAPI_event($vars);
  $fb_result = json_decode( $fb_result, true);
  
  if( !empty($vars['cart_data']) ){
  	unset($vars['cart_data']); 
  }
  
  if( !empty($vars['sh_cart_id']) ){
  	unset($vars['sh_cart_id']); 
  }
  if( !empty($vars['external_id']) ){
  	unset($vars['external_id']); 
  }
  if( !empty($vars['order_id']) ){
  	unset($vars['order_id']); 
  }
  if( !empty($vars['event_id']) ){
  	unset($vars['event_id']); 
  }
  

  if(!empty($fb_result['fbtrace_id'])){
    $vars['fbtrace_id'] = $fb_result['fbtrace_id'];
  }
  if( empty($vars['fbtrace_id']) ){
    $vars['fbtrace_id'] = substr($fb_result['error']['error_user_msg'],0,32);
  } 

  $vars['sh_cust_id'] = $sh_cust_id;

  $columns_str = '';
  $values_str = '';
  $first = true;

  foreach($vars as $col => $val){
    if ( $col != "db_currency"){
      if( $first ){
        $first = false;
        $columns_str .= "`".$col."`";
        $values_str .= '"'.$val.'"';
      } else {
        $columns_str .= ",`".$col."`";
        $values_str .= ',"'.$val.'"';
      }
    }
  }

  $keys = array('events');
$sql = 'INSERT INTO processed_events ('.$columns_str.') VALUES ('.$values_str.');';

if( !empty($cart_token) ){
	if (!empty($sh_cust_id) && $sh_cust_id != '' || !empty($vars['em']) && $vars['em'] != '' || !empty($vars['ph']) && $vars['ph'] != '' || !empty($vars['fn']) && $vars['fn'] != '' || !empty($vars['ln']) && $vars['ln'] != ''){
		$comma_tracker = false;
		$sql .= 'UPDATE `session` SET';
		if (!empty($sh_cust_id) && $sh_cust_id != ''){
			$sql .= ' `sh_cust_id` = "'.$sh_cust_id.'"';
			$comma_tracker = true;
		}
		if (!empty($vars['em']) && $vars['em'] != ''){
			if($comma_tracker == true){
				$sql .= ',';
			}
			$sql .= ' `em` = "'.$vars['em'].'"';
			$comma_tracker = true;
		}
		if (!empty($vars['ph']) && $vars['ph'] != ''){
			if($comma_tracker == true){
				$sql .= ',';
			}
			$sql .= ' `ph` = "'.$vars['ph'].'"';
			$comma_tracker = true;
		}
		if (!empty($vars['fn']) && $vars['fn'] != ''){
			if($comma_tracker == true){
				$sql .= ',';
			}
			$sql .= ' `fn` = "'.$vars['fn'].'"';
			$comma_tracker = true;
		}
		if (!empty($vars['ln']) && $vars['ln'] != ''){
			if($comma_tracker == true){
				$sql .= ',';
			}
			$sql .= ' `ln` = "'.$vars['ln'].'"';
			$comma_tracker = true;
		}

		if (!empty($vars['user_agent']) && $vars['user_agent'] != ''){
			if($comma_tracker == true){
				$sql .= ',';
			}
			$sql .= ' `user_agent` = "'.$vars['user_agent'].'"';
			$comma_tracker = true;
		}
		if (!empty($vars['user_agent_full']) && $vars['user_agent_full'] != ''){
			if($comma_tracker == true){
				$sql .= ',';
			}
			$sql .= ' `user_agent_full` = "'.$vars['user_agent_full'].'"';
			$comma_tracker = true;
		}
		$sql .= ' WHERE sh_cart_id = "'.$cart_token.'";';
		$keys[1] = 'session';
	}
} else if(!empty($ip) && !empty($user_agent) && !empty($shop)){
	if (!empty($sh_cust_id) && $sh_cust_id != '' || !empty($vars['em']) && $vars['em'] != '' || !empty($vars['ph']) && $vars['ph'] != '' || !empty($vars['fn']) && $vars['fn'] != '' || !empty($vars['ln']) && $vars['ln'] != ''){
		$comma_tracker = false;
		$sql .= 'UPDATE `session` SET';
		if (!empty($sh_cust_id) && $sh_cust_id != ''){
			$sql .= ' `sh_cust_id` = "'.$sh_cust_id.'"';
			$comma_tracker = true;
		}
		if (!empty($vars['em']) && $vars['em'] != ''){
			if($comma_tracker == true){
				$sql .= ',';
			}
			$sql .= ' `em` = "'.$vars['em'].'"';
			$comma_tracker = true;
		}
		if (!empty($vars['ph']) && $vars['ph'] != ''){
			if($comma_tracker == true){
				$sql .= ',';
			}
			$sql .= ' `ph` = "'.$vars['ph'].'"';
			$comma_tracker = true;
		}
		if (!empty($vars['fn']) && $vars['fn'] != ''){
			if($comma_tracker == true){
				$sql .= ',';
			}
			$sql .= ' `fn` = "'.$vars['fn'].'"';
			$comma_tracker = true;
		}
		if (!empty($vars['ln']) && $vars['ln'] != ''){
			if($comma_tracker == true){
				$sql .= ',';
			}
			$sql .= ' `ln` = "'.$vars['ln'].'"';
			$comma_tracker = true;
		}
		if (!empty($user_agent) && $user_agent != '' && $user_agent != NULL && $user_agent != "NULL"){
			$vars['user_agent'] = $user_agent;
		} 
		if (!empty($vars['user_agent']) && $vars['user_agent'] != ''){
			if($comma_tracker == true){
				$sql .= ',';
			}
			$sql .= ' `user_agent` = "'.$vars['user_agent'].'"';
			$comma_tracker = true;
		}
		if (!empty($vars['user_agent_full']) && $vars['user_agent_full'] != ''){
			if($comma_tracker == true){
				$sql .= ',';
			}
			$sql .= ' `user_agent_full` = "'.$vars['user_agent_full'].'"';
			$comma_tracker = true;
		}
		$sql .= ' WHERE `ip` = "'.$ip.'" AND `user_agent` = "'.$user_agent.'" AND `shop` = "'.$shop.'";';	
		$keys[1] = 'session';
	}
}
// error_log("\n\n SQL dump of Purchase event logging ".$sql."\n\n",0);
sql_multi_query($sql,$keys);

  $vars_min['event_type'] = $vars['event_type'];
  $vars_min['account_id'] = (int)$account_id;
  if (!empty($session_id) && $session_id != ""){
    $vars_min['session_id'] = (int)$session_id;
  }
  if ( !empty($fb_result['fbtrace_id'])){
    if(strpos($fb_result['fbtrace_id'], 'parameters') !== false || empty($fb_result['fbtrace_id'])){
      $vars_min['fbtrace_id'] = 0;
    } else {
      $vars_min['fbtrace_id'] = 1;
    }
  } else {
    $vars_min['fbtrace_id'] = 0;
  }
  if( !empty($vars['em']) ){
    if ($vars['em'] == "undefined") {
      $vars_min['em'] = 0;
    } else {
      $vars_min['em'] = 1;
    }
  } else {
    $vars_min['em'] = 0;	
  }
  if( !empty($vars['ph']) ){
    if ($vars['ph'] == "undefined") {
      $vars_min['ph'] = 0;
    } else {
      $vars_min['ph'] = 1;
    }
  } else {
    $vars_min['ph'] = 0;	
  }
  if( !empty($vars['fn']) ){
    if ($vars['fn'] == "undefined") {
      $vars_min['fn'] = 0;
    } else {
      $vars_min['fn'] = 1;
    }
  } else {
    $vars_min['fn'] = 0;	
  }
  if( !empty($vars['ln']) ){
    if ($vars['ln'] == "undefined") {
      $vars_min['ln'] = 0;
    } else {
      $vars_min['ln'] = 1;
    }
  } else {
    $vars_min['ln'] = 0;	
  }
  if( !empty($vars['fbp']) ){
    if ($vars['fbp'] == "undefined") {
      $vars_min['fbp'] = 0;
    } else {
      $vars_min['fbp'] = 1;
    }
  } else {
    $vars_min['fbp'] = 0;	
  }
  if( !empty($vars['fbc']) ){
    if ($vars['fbc'] == "undefined") {
      $vars_min['fbc'] = 0;
    } else {
      $vars_min['fbc'] = 1;
    }
  } else {
    $vars_min['fbc'] = 0;	
  }
  if( !empty($ip) || !empty($vars['ip'])){
    $vars_min['ip'] = 1;
  } else {
    $vars_min['ip'] = 0;
  }
  if( !empty($user_agent) || !empty($vars['user_agent'])){
    $vars_min['user_agent'] = 1;
  } else{
    $vars_min['user_agent'] = 0;
  }
  if( !empty($vars['user_agent_full']) && $vars['user_agent_full'] != ''){
    $vars_min['user_agent_full'] = 1;
  } else{
    $vars_min['user_agent_full'] = 0;
  }
  if( !empty($vars['event_value']) ){
    $vars_min['event_value'] = round( floatval( $vars['event_value']), 2 );
  } else {
    $vars_min['event_value'] = 0.00;
  }
  if( !empty($vars['currency']) ){
    $vars_min['currency'] = $vars['currency'];
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
  sqli_query($sql);
}



/* ------------------------ ADD PAYMENT INFO ------------------------- */

function addpaymentinfo($data, $vars, $vars_min, $shop, $cart_token, $user_agent, $user_agent_full){
  if( !empty($shop) ){
    $sql = 'SELECT F.*, A.* FROM accounts A LEFT JOIN fb_settings F ON F.sha_token = A.sha_token WHERE A.shop = "'.$shop.'" AND "'.date("Y-m-d").'" <= A.expires;';
    $account_data = sql_select_to_array($sql);

    if( !empty( $data['currency'] )&& strtoupper($data['currency']) != "NULL"){
      $vars['currency'] = $data['currency'];
    } else if( !empty( $data['price_set'] ) && strtoupper($data['price_set']) != "NULL"){
      $vars['currency'] = $data['price_set']['currency_code'];
    } else if( !empty( $data['original_line_price_set'] ) && strtoupper($data['original_line_price_set']) != "NULL"){
      $vars['currency'] = $data['original_line_price_set']['currency_code'];
    } else if (!empty($account_data[0]["db_currency"]) && strtoupper($account_data[0]["db_currency"]) != "NULL"){
      $vars['currency'] = $account_data[0]["db_currency"];
    } else {
      $vars['currency'] = "USD";
    }

    if (!empty($data['addresses']) && $data['addresses'] != ''){
      if (!empty($data['addresses'][0]['customer_id']) && $data['addresses'][0]['customer_id'] != '') {
        $sh_cust_id = $data['addresses'][0]['customer_id'];
        $sql2 = 'SELECT id, em, ph, fn, ln, fbp, fbc, ip, user_agent, user_agent_full, created FROM `session` WHERE (`sh_cust_id` = "'.$sh_cust_id.'") AND `shop` = "'.$shop.'" ORDER BY created DESC;';
        $session_data = sql_select_to_array($sql2);
       // error_log("     ".$sql2."\n\n",0);
      }
    }
    if(empty($session_data[0]['created'])){
      if (empty($data['addresses']) || $data['addresses'] == ''){
        if (!empty($data['id']) && $data['id'] != '') {
          $sh_cust_id = $data['id'];
          $sql2 = 'SELECT id, em, ph, fn, ln, fbp, fbc, ip, user_agent, user_agent_full, created FROM `session` WHERE (`sh_cust_id` = "'.$sh_cust_id.'") AND `shop` = "'.$shop.'" ORDER BY created DESC;';
          $session_data = sql_select_to_array($sql2);  
          //error_log("     ".$sql2."\n\n",0);
        }
      }
    }
    if(empty($session_data[0]['created'])){
      $session_data = array();
    }

    if( !empty( $account_data[0]['sha_token'] ) ){
      $account_id = $account_data[0]['id'];

      //load the account data
      $vars['t1'] = $account_data[0]['sha_token'];
      $vars['t2'] = $account_data[0]['pixel_id'];
      $vars['t3'] = $account_data[0]['access_token'];
      $vars['t4'] = $account_data[0]['test_event_code'];
      $vars['db_currency'] = $account_data[0]['db_currency'];

      
      //if session data is available, try to grab it
      if( !empty($session_data[0]['created']) ){
        if (!empty($session_data[0]['id']) && $session_data[0]['id'] != ""){
          $session_id = $session_data[0]['id'];
        } else {
          $session_id = "";
        }
        if (!empty($session_data[0]['fbp']) && $session_data[0]['fbp'] != ""){
          $vars['fbp'] = $session_data[0]['fbp'];
        } else {
          if (empty($vars['fbp'])){
            $vars['fbp'] = "";
          }
        }
        if (!empty($session_data[0]['fbc']) && $session_data[0]['fbc'] != ""){
          $vars['fbc'] = $session_data[0]['fbc'];
        } else {
          if (empty($vars['fbc'])){
            $vars['fbc'] = "";   
          }
        }
        if (!empty($session_data[0]['em']) && $session_data[0]['em'] != ""){
          $vars['em'] = $session_data[0]['em'];
        } else {
          if (empty($vars['em'])){
            $vars['em'] = "";
          }
        }
        if (!empty($session_data[0]['ph']) && $session_data[0]['ph'] != ""){
          $vars['ph'] = $session_data[0]['ph'];
        } else {
          if (empty($vars['ph'])){
            $vars['ph'] = "";
          }
        }
        if (!empty($session_data[0]['fn']) && $session_data[0]['fn'] != ""){
          $vars['fn'] = $session_data[0]['fn'];
        } else {
          if (empty($vars['fn'])){
            $vars['fn'] = "";
          }
        }
        if (!empty($session_data[0]['ln']) && $session_data[0]['ln'] != ""){
          $vars['ln'] = $session_data[0]['ln'];
        } else {
          if (empty($vars['ln'])){
            $vars['ln'] = "";
          }
        }
        if (!empty($session_data[0]['ip']) && $session_data[0]['ip'] != ""){
          $vars['ip'] = $session_data[0]['ip'];
          $ip = $session_data[0]['ip'];
        } else {
          if (empty($vars['ip'])){
            if (!empty($ip)){
              $vars['ip'] = $ip;
            }
            $vars['ip'] = "";
            $ip = "";
          }
        }
        if (!empty($session_data[0]['sh_cart_id']) && $session_data[0]['sh_cart_id'] != ""){
          //$vars['sh_cart_id'] = $session_data[0]['sh_cart_id'];
          $sh_cart_id = $session_data[0]['sh_cart_id'];
        } else {
          //$vars['sh_cart_id'] = "";
          if (empty($sh_cart_id)){
            $sh_cart_id = "";
          }
        }
        if (!empty($session_data[0]['user_agent_full']) && $session_data[0]['user_agent_full'] != ""){
          $vars['user_agent_full'] = $session_data[0]['user_agent_full'] ;
          $user_agent_full = $session_data[0]['user_agent_full'];			
        } else if (!empty($session_data[0]['user_agent']) && $session_data[0]['user_agent'] != ""){
          $vars['user_agent_full'] = $session_data[0]['user_agent'] ;
          $user_agent_full = $session_data[0]['user_agent'];
        }
        if (!empty($session_data[0]['user_agent']) && $session_data[0]['user_agent'] != ""){
          $vars['user_agent'] = preg_replace("/[^A-Za-z]/", "", $session_data[0]['user_agent'] );	
          $user_agent = preg_replace("/[^A-Za-z]/", "", $session_data[0]['user_agent'] );						
        } else if (!empty($session_data[0]['user_agent_full']) && $session_data[0]['user_agent_full'] != ""){
          $vars['user_agent'] = preg_replace("/[^A-Za-z]/", "", $session_data[0]['user_agent_full'] );			
          $user_agent = preg_replace("/[^A-Za-z]/", "", $session_data[0]['user_agent_full'] );			
        }

        // error_log("  ".$shop."    SESSION MATCHED!\n\n",0);
      } else {
        // error_log("   ".$shop."   NO session matched\n\n",0);
        exit();
      }
    } else {
      error_log("      NO NO NO NO NO NO NO popsixle account matches the passed shop:   ".$shop."\n\n",0);
      exit();
    }
  } else {
    error_log("      NO shop included with request.\n\n",0);
    exit();
  }
  if (!empty($data['customer']['email']) && $data['customer']['email'] != "" && $data['customer']['email'] != "NULL" && $data['customer']['email'] != NULL) {
    $vars['em'] = hash('sha256', $data['customer']['email']);
  } else if (!empty($data['addresses'][0]['email']) && $data['addresses'][0]['email'] != "") {
    $vars['em'] = hash('sha256', $data['addresses'][0]['email']);
  } else if (!empty($data['billing_address']['email']) && $data['billing_address']['email'] != "") {
    $vars['em'] = hash('sha256', $data['billing_address']['email']);
  } else if (!empty($data['contact_email']) && $data['contact_email'] != "") {
    $vars['em'] = hash('sha256', $data['contact_email']);
  } else if (!empty($data['email']) && $data['email'] != "") {
    $vars['em'] = hash('sha256', $data['email']);
  } else if (!empty($vars['em']) && $vars['em'] != "") {
  } else {
    $vars['em'] = "";
  }
  if (!empty($data['customer']['phone']) && $data['customer']['phone'] != "") {
    $vars['ph'] = hash('sha256', $data['customer']['phone']);
  } else if (!empty($data['addresses'][0]['phone']) && $data['addresses'][0]['phone'] != "") {
    $vars['ph'] = hash('sha256', $data['addresses'][0]['phone']);
  } else if (!empty($data['billing_address']['phone']) && $data['billing_address']['phone'] != "") {
    $vars['ph'] = hash('sha256', $data['billing_address']['phone']);
  } else if (!empty($data['phone']) && $data['phone'] != "") {
    $vars['ph'] = hash('sha256', $data['phone']);
  } else if (!empty($vars['ph']) && $vars['ph'] != "") {
  } else {
    $vars['ph'] = "";
  }
  if (!empty($data['customer']['first_name']) && $data['customer']['first_name'] != "" && $data['customer']['first_name'] != "NULL" && $data['customer']['first_name'] != NULL) {
    $vars['fn'] = hash('sha256', $data['customer']['first_name']);
  } else if (!empty($data['addresses'][0]['first_name']) && $data['addresses'][0]['first_name'] != "") {
    $vars['fn'] = hash('sha256', $data['addresses'][0]['first_name']);
  } else if (!empty($data['billing_address']['first_name']) && $data['billing_address']['first_name'] != "") {
    $vars['fn'] = hash('sha256', $data['billing_address']['first_name']);
  } else if (!empty($vars['fn']) && $vars['fn'] != "") {
  } else {
    $vars['fn'] = "";
  }
  if (!empty($data['customer']['last_name']) && $data['customer']['last_name'] != "" && $data['customer']['last_name'] != "NULL" && $data['customer']['last_name'] != NULL) {
    $vars['ln'] = hash('sha256', $data['customer']['last_name']);
  } else if (!empty($data['addresses'][0]['last_name']) && $data['addresses'][0]['last_name'] != "") {
    $vars['ln'] = hash('sha256', $data['addresses'][0]['last_name']);
  } else if (!empty($data['billing_address']['last_name']) && $data['billing_address']['last_name'] != "") {
    $vars['ln'] = hash('sha256', $data['billing_address']['last_name']);
  } else if (!empty($vars['ln']) && $vars['ln'] != "") {
  } else {
    $vars['ln'] = "";
  }

  if( empty($sh_cust_id) ){
    $sh_cust_id = '';
  }

  $fb_result = send_to_CAPI_event($vars);
  $fb_result = json_decode( $fb_result, true);

  if(!empty($fb_result['fbtrace_id'])){
    $vars['fbtrace_id'] = $fb_result['fbtrace_id'];
  }
  if( empty($vars['fbtrace_id']) ){
    $vars['fbtrace_id'] = substr($fb_result['error']['error_user_msg'],0,32);
  } 

  $columns_str = '';
  $values_str = '';
  $first = true;

  foreach($vars as $col => $val){
    if ( $col != "db_currency"){
      if( $first ){
        $first = false;
        $columns_str .= "`".$col."`";
        $values_str .= '"'.$val.'"';
      } else {
        $columns_str .= ",`".$col."`";
        $values_str .= ',"'.$val.'"';
      }
    }
  }

  $keys = array('events');
  $sql = 'INSERT INTO processed_events ('.$columns_str.') VALUES ('.$values_str.');';
  if (!empty($session_data[0]['created']) && !empty($sh_cust_id)){
    if ( !empty($vars['em']) && $vars['em'] != '' || !empty($vars['ph']) && $vars['ph'] != '' || !empty($vars['fn']) && $vars['fn'] != '' || !empty($vars['ln']) && $vars['ln'] != ''){
      $comma_tracker = false;
      $sql .= 'UPDATE `session` SET';

      if (!empty($vars['em']) && $vars['em'] != ''){
        if($comma_tracker == true){
          $sql .= ',';
        }
        $sql .= ' `em` = "'.$vars['em'].'"';
        $comma_tracker = true;
      }
      if (!empty($vars['ph']) && $vars['ph'] != ''){
        if($comma_tracker == true){
          $sql .= ',';
        }
        $sql .= ' `ph` = "'.$vars['ph'].'"';
        $comma_tracker = true;
      }
      if (!empty($vars['fn']) && $vars['fn'] != ''){
        if($comma_tracker == true){
          $sql .= ',';
        }
        $sql .= ' `fn` = "'.$vars['fn'].'"';
        $comma_tracker = true;
      }
      if (!empty($vars['ln']) && $vars['ln'] != ''){
        if($comma_tracker == true){
          $sql .= ',';
        }
        $sql .= ' `ln` = "'.$vars['ln'].'"';
        $comma_tracker = true;
      }
      if (!empty($user_agent) && $user_agent != '' && $user_agent != NULL && $user_agent != "NULL"){
        $vars['user_agent'] = $user_agent;
      } 
      if (!empty($vars['user_agent']) && $vars['user_agent'] != ''){
        if($comma_tracker == true){
          $sql .= ',';
        }
        $sql .= ' `user_agent` = "'.$vars['user_agent'].'"';
        $comma_tracker = true;
      }
      if (!empty($vars['user_agent_full']) && $vars['user_agent_full'] != ''){
        if($comma_tracker == true){
          $sql .= ',';
        }
        $sql .= ' `user_agent_full` = "'.$vars['user_agent_full'].'"';
        $comma_tracker = true;
      }
      $sql .= ' WHERE sh_cart_id = "'.$sh_cart_id.'";';	
      $keys[1] = 'session';
    }
  }
  //error_log("\n\n".$sql."\n\n",0);
  sql_multi_query($sql,$keys);

  $vars_min['event_type'] = $vars['event_type'];
  $vars_min['account_id'] = (int)$account_id;
  if (!empty($session_id) && $session_id != ""){
    $vars_min['session_id'] = (int)$session_id;
  }
  if(strpos($fb_result['fbtrace_id'], 'parameters') !== false || empty($fb_result['fbtrace_id'])){
    $vars_min['fbtrace_id'] = 0;
  } else {
    $vars_min['fbtrace_id'] = 1;
  }
  if( !empty($vars['em']) ){
    if ($vars['em'] == "undefined") {
      $vars_min['em'] = 0;
    } else {
      $vars_min['em'] = 1;
    }
  } else {
    $vars_min['em'] = 0;	
  }
  if( !empty($vars['ph']) ){
    if ($vars['ph'] == "undefined") {
      $vars_min['ph'] = 0;
    } else {
      $vars_min['ph'] = 1;
    }
  } else {
    $vars_min['ph'] = 0;	
  }
  if( !empty($vars['fn']) ){
    if ($vars['fn'] == "undefined") {
      $vars_min['fn'] = 0;
    } else {
      $vars_min['fn'] = 1;
    }
  } else {
    $vars_min['fn'] = 0;	
  }
  if( !empty($vars['ln']) ){
    if ($vars['ln'] == "undefined") {
      $vars_min['ln'] = 0;
    } else {
      $vars_min['ln'] = 1;
    }
  } else {
    $vars_min['ln'] = 0;	
  }
  if( !empty($vars['fbp']) ){
    if ($vars['fbp'] == "undefined") {
      $vars_min['fbp'] = 0;
    } else {
      $vars_min['fbp'] = 1;
    }
  } else {
    $vars_min['fbp'] = 0;	
  }
  if( !empty($vars['fbc']) ){
    if ($vars['fbc'] == "undefined") {
      $vars_min['fbc'] = 0;
    } else {
      $vars_min['fbc'] = 1;
    }
  } else {
    $vars_min['fbc'] = 0;	
  }
  if( !empty($ip) || !empty($vars['ip'])){
    $vars_min['ip'] = 1;
  } else {
    $vars_min['ip'] = 0;
  }
  if( !empty($user_agent) || !empty($vars['user_agent'])){
    $vars_min['user_agent'] = 1;
  } else{
    $vars_min['user_agent'] = 0;
  }
  if( !empty($vars['user_agent_full']) && $vars['user_agent_full'] != ''){
    $vars_min['user_agent_full'] = 1;
  } else{
    $vars_min['user_agent_full'] = 0;
  }
  if( !empty($vars['event_value']) ){
    $vars_min['event_value'] = round( floatval( $vars['event_value']), 2 );
  } else {
    $vars_min['event_value'] = 0.00;
  }
  if( !empty($vars['currency']) ){
    $vars_min['currency'] = $vars['currency'];
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
  sqli_query($sql);
}

/* ------------------------ ADD TO CART ------------------------- */


function addtocart($data, $vars, $vars_min, $shop, $cart_token, $user_agent, $user_agent_full){
  if( !empty($shop) ){
      
    $cart_data = grab_cart_details($data, true);
	  
	  if( !empty($cart_data) ){
		  $vars['cart_data'] = $cart_data;
		  if(!empty($vars['cart_data']['value'])){
		      $vars['event_value'] = round( floatval( $vars['cart_data']['value'] ), 2);
		  }
	  }
	
	$sql_queries = array();
	$keys = array( 'account_data','session_data','cart_state_data' );
	
	$sql_queries = 'SELECT F.*, A.* FROM accounts A LEFT JOIN fb_settings F ON F.sha_token = A.sha_token WHERE A.shop = "'.$shop.'" AND "'.date("Y-m-d").'" <= A.expires;';
	$sql_queries .= 'SELECT id, em, ph, fn, ln, fbp, fbc, ip, user_agent, user_agent_full, sh_cust_id, created FROM `session` WHERE sh_cart_id = "'.$cart_token.'" ORDER BY created DESC;';
	$sql_queries .=  'SELECT * FROM `cart_state` WHERE sh_cart_id = "'.$cart_token.'" ORDER BY id DESC;';
	$db_data = sql_multi_select_to_array($sql_queries,$keys);
	
	
   // $sql = 'SELECT F.*, A.* FROM accounts A LEFT JOIN fb_settings F ON F.sha_token = A.sha_token WHERE A.shop = "'.$shop.'" AND "'.date("Y-m-d").'" <= A.expires;';
    $account_data = $db_data['account_data'];

    if( !empty( $data['currency'] )  && strtoupper($data['currency']) != "NULL"){
      $vars['currency'] = $data['currency'];
    } else if( !empty( $data['price_set'] ) && strtoupper($data['price_set']) != "NULL"){
      $vars['currency'] = $data['price_set']['currency_code'];
    } else if( !empty( $data['original_line_price_set'] ) && strtoupper($data['original_line_price_set']) != "NULL"){
      $vars['currency'] = $data['original_line_price_set']['currency_code'];
    } else if (!empty($account_data[0]["db_currency"]) && strtoupper($account_data[0]["db_currency"]) != "NULL"){
      $vars['currency'] = $account_data[0]["db_currency"];
    } else {
      $vars['currency'] = "USD";
    }
    
    if( !empty($cart_token) ){
      $keys = array('cart');
      // $sql2 = 'SELECT id, em, ph, fn, ln, fbp, fbc, ip, user_agent, user_agent_full, sh_cust_id, created FROM `session` WHERE sh_cart_id = "'.$cart_token.'" ORDER BY created DESC;';
      $session_data = $db_data['session_data'];	
      // Log sql2
      //error_log("     ".$sql2."\n\n",0);
    } 
    if(empty($session_data[0]['created'])){
      $session_data = array();
    }
    if( !empty( $account_data[0]['sha_token'] ) ){
      $account_id = $account_data[0]['id'];

      //load the account data
      $vars['t1'] = $account_data[0]['sha_token'];
      $vars['t2'] = $account_data[0]['pixel_id'];
      $vars['t3'] = $account_data[0]['access_token'];
      $vars['t4'] = $account_data[0]['test_event_code'];
      $vars['db_currency'] = $account_data[0]['db_currency'];

      //pull last cart state from db
      
      //evaluate cart vs db
      
      //if same, skip
      
      //if changed, continue
      
      //if new, continue
      
      /*
        id
        account_id
        cart_id
        cart_total
        cart_object_ids
        timestamp
        
      */
      //$sql3 = 'SELECT * FROM `cart_state` WHERE sh_cart_id = "'.$cart_token.'" ORDER BY id DESC;';
      $cart_state_data = $db_data['cart_state_data'];		

      if(!empty($cart_state_data[0]['id'])){
        if (!empty($cart_data)){
          // error_log("   Cart Data 2     ".$cart_data."\n\n",0);

          $cart_data_value = (float)number_format(floatval( $cart_data['value'] ), 2 );
          if ( $cart_data_value <= 0 || empty($cart_data_value)) {
            $cart_data_value = 0.00;
          }
          if ( $cart_state_data[0]['sh_cart_total'] <= 0 || empty($cart_state_data[0]['sh_cart_total'])) {
            $cart_state_data[0]['sh_cart_total'] = 0.00;
          } else {
            $cart_state_data[0]['sh_cart_total'] = (float)number_format(floatval( $cart_state_data[0]['sh_cart_total'] ), 2 );
          }
          
          // error_log("   cart_data_value     ".$cart_data_value."\n\n",0);
          // error_log("   sh_cart_total    ".$cart_state_data[0]['sh_cart_total']."\n\n",0);
          
          $cart_content_ids = json_encode($cart_data['content_ids']);
          if ( empty($cart_content_ids) || $cart_content_ids == 'null' || $cart_content_ids == 'NULL' ) {
            $cart_content_ids = '';
          }

          if ($cart_state_data[0]['account_id'] == $account_id){
            //possibly need (string)
            if ($cart_state_data[0]['sh_cart_object_ids'] == $cart_content_ids && $cart_state_data[0]['sh_cart_total'] == $cart_data_value){
              // error_log("   Cart exit 1   \n\n",0);

              exit();
            } else {
              if ( $cart_data_value <= $cart_state_data[0]['sh_cart_total']) {
                $sql = 'UPDATE cart_state SET `sh_cart_total` = '.$cart_data_value.', `sh_cart_object_ids` = "'.$cart_content_ids.'" WHERE id = '.$cart_state_data[0]['id'].' AND `sh_cart_id` = "'.$cart_token.'" AND `account_id` =  '.$account_id.';';
                // error_log("   Cart SQL update 3    ".$sql."\n\n",0);
                sqli_query($sql); 
                exit();
              } else {
                $sql = 'UPDATE cart_state SET `sh_cart_total` = '.$cart_data_value.', `sh_cart_object_ids` = "'.$cart_content_ids.'" WHERE id = '.$cart_state_data[0]['id'].' AND `sh_cart_id` = "'.$cart_token.'" AND `account_id` =  '.$account_id.';';
                // error_log("   Cart SQL update 2    ".$sql."\n\n",0);
                sqli_query($sql); 
              }
            }
          } else {
            exit();       
          }
        } else {
          // error_log("   Cart data empty   ".$cart_data."\n\n",0);
          $sql = 'UPDATE cart_state SET `sh_cart_total` = 0.00, `sh_cart_object_ids` = "" WHERE id = '.$cart_state_data[0]['id'].' AND `sh_cart_id` = "'.$cart_token.'" AND `account_id` =  '.$account_id.';';
          // error_log("   Cart data empty update    ".$sql."\n\n",0);
          sqli_query($sql); 

          exit();
        }
      } else {
        if (!empty($cart_data)){

          $cart_data_value = $cart_data['value'];
          if ( $cart_data_value <= 0 || empty($cart_data_value)) {
            $cart_data_value = 0.00;
          } else {
            $cart_data_value = (float)number_format(floatval( $cart_data['value'] ), 2 );
          }
          $cart_content_ids = json_encode($cart_data['content_ids']);
          if ( empty($cart_content_ids) || $cart_content_ids == 'null' || $cart_content_ids == 'NULL' ) {
            $cart_content_ids = '';
          }
          $sql = 'INSERT INTO cart_state ( `account_id`, `sh_cart_id`, `sh_cart_total`, `sh_cart_object_ids` ) VALUES ('.$account_id.', "'.$cart_token.'", '.$cart_data_value.', "'.$cart_content_ids.'" );';
          // error_log("   Cart SQL insert 1     ".$sql."\n\n",0);
          sqli_query($sql); 
        } else {
          $sql = 'INSERT INTO cart_state ( `account_id`, `sh_cart_id`, `sh_cart_total`, `sh_cart_object_ids` ) VALUES ('.$account_id.', "'.$cart_token.'", 0.00, "" );';
          // error_log("   Cart SQL insert 2     ".$sql."\n\n",0);
          sqli_query($sql); 
          exit();
        }
      }

      //if session data is available, try to grab it
      if( !empty($session_data[0]['created']) ){
        if (!empty($session_data[0]['id']) && $session_data[0]['id'] != ""){
          $session_id = $session_data[0]['id'];
        } else if (empty($session_id)){
          $session_id = "";
        }
        if (!empty($session_data[0]['fbp']) && $session_data[0]['fbp'] != ""){
          $vars['fbp'] = $session_data[0]['fbp'];
        } else if (empty($vars['fbp'])){
          $vars['fbp'] = "";
        }
        if (!empty($session_data[0]['fbc']) && $session_data[0]['fbc'] != ""){
          $vars['fbc'] = $session_data[0]['fbc'];
        } else if (empty($vars['fbc'])){
          $vars['fbc'] = "";
        }
        if (!empty($session_data[0]['em']) && $session_data[0]['em'] != ""){
          $vars['em'] = $session_data[0]['em'];
        } else if (empty($vars['em'])){
          $vars['em'] = "";
        }
        if (!empty($session_data[0]['ph']) && $session_data[0]['ph'] != ""){
          $vars['ph'] = $session_data[0]['ph'];
        } else if (empty($vars['ph'])){
          $vars['ph'] = "";
        }
        if (!empty($session_data[0]['fn']) && $session_data[0]['fn'] != ""){
          $vars['fn'] = $session_data[0]['fn'];
        } else if (empty($vars['fn'])) {
          $vars['fn'] = "";
        }
        if (!empty($session_data[0]['ln']) && $session_data[0]['ln'] != ""){
          $vars['ln'] = $session_data[0]['ln'];
        } else if (empty($vars['ln'])) {
          $vars['ln'] = "";
        }
        if (!empty($session_data[0]['ip']) && $session_data[0]['ip'] != ""){
          $vars['ip'] = $session_data[0]['ip'];
          $ip = $session_data[0]['ip'];
        } else if (empty($vars['ip'])) {
          $vars['ip'] = "";
          $ip = "";
        }
        if (!empty($session_data[0]['user_agent_full']) && $session_data[0]['user_agent_full'] != "" && empty($vars['user_agent_full'])){
          $vars['user_agent_full'] = $session_data[0]['user_agent_full'] ;
          $user_agent_full = $session_data[0]['user_agent_full'];			
        } else if (!empty($session_data[0]['user_agent']) && $session_data[0]['user_agent'] != "" && empty($vars['user_agent'])){
          $vars['user_agent_full'] = $session_data[0]['user_agent'] ;
          $user_agent_full = $session_data[0]['user_agent'];
        }
        if (!empty($session_data[0]['user_agent']) && $session_data[0]['user_agent'] != ""){
          $vars['user_agent'] = preg_replace("/[^A-Za-z]/", "", $session_data[0]['user_agent'] );	
          $user_agent = preg_replace("/[^A-Za-z]/", "", $session_data[0]['user_agent'] );						
        } else if (!empty($session_data[0]['user_agent_full']) && $session_data[0]['user_agent_full'] != ""){
          $vars['user_agent'] = preg_replace("/[^A-Za-z]/", "", $session_data[0]['user_agent_full'] );			
          $user_agent = preg_replace("/[^A-Za-z]/", "", $session_data[0]['user_agent_full'] );			
        }
        if (!empty($session_data[0]['sh_cust_id']) && $session_data[0]['sh_cust_id'] != ""){
          $vars['sh_cust_id'] = $session_data[0]['sh_cust_id'];
          $sh_cust_id = $session_data[0]['sh_cust_id'];
        } else if (empty($vars['sh_cust_id'])) {
          $vars['sh_cust_id'] = "";
          $sh_cust_id = '';
        }
        // error_log("   ".$shop."   SESSION MATCHED!\n\n",0);
      } else {
        // error_log("   ".$shop."   NO session matched\n\n",0);
      }
    } else {
      error_log("      NO NO NO NO NO NO NO popsixle account matches the passed shop:   ".$shop."\n\n",0);
      exit();
    }
  } else {
    error_log("      NO shop included with request.\n\n",0);
    exit();
  }

  //fb_result = send_to_CAPI_event($vars);
  //$fb_result = json_decode( $fb_result, true);
  
  $fb_result = send_to_CAPI_event($vars);
  $fb_result = json_decode( $fb_result, true);
  
  // error_log("\n\n   FB API response ".var_export($fb_result, true)."\n\n",0);
  
  if( !empty($vars['cart_data']) ){
  	unset($vars['cart_data']); 
  }
  if( !empty($vars['external_id']) ){
  	unset($vars['external_id']); 
  }
  if( !empty($vars['order_id']) ){
  	unset($vars['order_id']); 
  }
  if( !empty($vars['event_id']) ){
  	unset($vars['event_id']); 
  }
  
  

  if(!empty($fb_result['fbtrace_id'])){
    $vars['fbtrace_id'] = $fb_result['fbtrace_id'];
  }
  if( empty($vars['fbtrace_id']) ){
    $vars['fbtrace_id'] = substr($fb_result['error']['error_user_msg'],0,200);
  } 

  $columns_str = '';
  $values_str = '';
  $first = true;

  foreach($vars as $col => $val){
    if ( $col != "db_currency"){
      if( $first ){
        $first = false;
        $columns_str .= "`".$col."`";
        $values_str .= '"'.$val.'"';
      } else {
        $columns_str .= ",`".$col."`";
        $values_str .= ',"'.$val.'"';
      }
    }
  }

  $sql = 'INSERT INTO processed_events ('.$columns_str.') VALUES ('.$values_str.');';
  sqli_query($sql);

  $vars_min['event_type'] = $vars['event_type'];
  $vars_min['account_id'] = (int)$account_id;
  if (!empty($session_id) && $session_id != ""){
    $vars_min['session_id'] = (int)$session_id;
  }
  if (!empty($fb_result['fbtrace_id'])){
    if(strpos($fb_result['fbtrace_id'], 'parameters') !== false || empty($fb_result['fbtrace_id'])){
      $vars_min['fbtrace_id'] = 0;
    } else {
      $vars_min['fbtrace_id'] = 1;
    }
  } else {
    $vars_min['fbtrace_id'] = 0;
  }
  if( !empty($vars['em']) ){
    if ($vars['em'] == "undefined") {
      $vars_min['em'] = 0;
    } else {
      $vars_min['em'] = 1;
    }
  } else {
    $vars_min['em'] = 0;	
  }
  if( !empty($vars['ph']) ){
    if ($vars['ph'] == "undefined") {
      $vars_min['ph'] = 0;
    } else {
      $vars_min['ph'] = 1;
    }
  } else {
    $vars_min['ph'] = 0;	
  }
  if( !empty($vars['fn']) ){
    if ($vars['fn'] == "undefined") {
      $vars_min['fn'] = 0;
    } else {
      $vars_min['fn'] = 1;
    }
  } else {
    $vars_min['fn'] = 0;	
  }
  if( !empty($vars['ln']) ){
    if ($vars['ln'] == "undefined") {
      $vars_min['ln'] = 0;
    } else {
      $vars_min['ln'] = 1;
    }
  } else {
    $vars_min['ln'] = 0;	
  }
  if( !empty($vars['fbp']) ){
    if ($vars['fbp'] == "undefined") {
      $vars_min['fbp'] = 0;
    } else {
      $vars_min['fbp'] = 1;
    }
  } else {
    $vars_min['fbp'] = 0;	
  }
  if( !empty($vars['fbc']) ){
    if ($vars['fbc'] == "undefined") {
      $vars_min['fbc'] = 0;
    } else {
      $vars_min['fbc'] = 1;
    }
  } else {
    $vars_min['fbc'] = 0;	
  }
  if( !empty($ip) || !empty($vars['ip'])){
    $vars_min['ip'] = 1;
  } else {
    $vars_min['ip'] = 0;
  }
  if( !empty($user_agent) || !empty($vars['user_agent'])){
    $vars_min['user_agent'] = 1;
  } else{
    $vars_min['user_agent'] = 0;
  }
  if( !empty($vars['user_agent_full']) && $vars['user_agent_full'] != ''){
    $vars_min['user_agent_full'] = 1;
  } else{
    $vars_min['user_agent_full'] = 0;
  }
  if( !empty($vars['event_value']) ){
    $vars_min['event_value'] = round( floatval( $vars['event_value']), 2 );
  } else {
    $vars_min['event_value'] = 0.00;
  }
  if( !empty($vars['currency']) ){
    $vars_min['currency'] = $vars['currency'];
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
  sqli_query($sql);
}



function grab_cart_details($api_data, $include_price = false){
//default is no passed var, assume its a Purchase event and we already have the total
//pass in 'true' for a cart update and grab the cart amount from "line_price"
	if( !empty( $api_data ) ){
		
		//foreach( $api_data as $k => $v ){
		//	error_log("\n\n   cart data (".$key.") - ".var_export($v, true)."\n\n",0);
		//}
		
		
		if( !empty( $api_data['line_items'] ) ){
			$returned_data = array();
			$returned_data['content_type'] = 'product_group';
			$returned_data['content_ids'] = array();
			//$returned_data['content_name'] = array();
			$returned_data['num_items'] = 0;
			if( $include_price ){
				$returned_data['value'] = 0;
			}
			foreach( $api_data['line_items'] as $item ){
				if( !empty($item['product_id']) ){
					array_push($returned_data['content_ids'], $item['product_id']);
				}
				//if( !empty($item['sku']) ){
				//	array_push($returned_data['content_name'], $item['sku']);
				//}
				if( !empty($item['quantity']) ){
					$returned_data['num_items'] += intval( $item['quantity'] );
				}
				if( !empty($item['line_price']) && $include_price ){
					$returned_data['value'] += round(floatval( $item['line_price'] ), 2 );
				}
			}
			return $returned_data;
		} else {
			return false;
		}
	} else {
		return false;
	}
}



/* ------------------------ CUSTOM SHOP CODE ------------------------- */

function custom_bestfriends($data, $vars, $vars_min, $shop, $cart_token, $user_agent, $user_agent_full, $ip){

  foreach ($data["note_attributes"] as $note) {
    if($note["name"] == "clientOrderId"){
      $vars['event_id'] = $note["value"];
    } else if ($note["name"] == "orderType"){
      if ($note["value"] == "NEW_SALE") {
        $vars['event_type'] = 'Purchase';
      } else {
        exit();
        $vars['event_type'] = 'Subscribe';
      }
    } else if ($note["name"] == "cartId"){
      // $cart_token = $note["value"];
      // $data["cart_token"] = $note["value"];
    } else if ($note["name"] == "custom3"){
      $vars['fbp'] = $note["value"];
    } else if ($note["name"] == "custom4"){
      $vars['fbc'] = $note["value"];
    } else if ($note["name"] == "ipAddress"){
      $vars['ip'] = "".$note["value"];
      $ip = "".$note["value"];
    } else if ($note["name"] == "custom5"){
      $vars['external_id'] = $note["value"];
    } else if ($note["name"] == "userAgent"){
      $vars['user_agent_full'] = "".$note["value"];
      $user_agent_full = "".$note["value"];
      $data["browser_ip"] = $note["value"];
      $user_agent = "".preg_replace("/[^A-Za-z]/", "", $note["value"] );
      $vars['user_agent'] = "".preg_replace("/[^A-Za-z]/", "", $note["value"] );
    }
  }
  initiatecheckout_purchase($data, $vars, $vars_min, $shop, $cart_token, $user_agent, $user_agent_full, $ip);
}
function custom_bettercart($data, $vars, $vars_min, $shop, $cart_token, $user_agent, $user_agent_full, $ip){

  foreach ($data["note_attributes"] as $note) {
    if ($note["name"] == "shopify-cart-token"){
      $cart_token = $note["value"];
      $data["cart_token"] = $note["value"];
    } else if ($note["name"] == "ip-address"){
      $vars['ip'] = "".$note["value"];
      $ip = "".$note["value"];
    } else if ($note["name"] == "user-agent"){
      $vars['user_agent_full'] = "".$note["value"];
      $user_agent_full = "".$note["value"];
      $data["browser_ip"] = $note["value"];
      $user_agent = "".preg_replace("/[^A-Za-z]/", "", $note["value"] );
      $vars['user_agent'] = "".preg_replace("/[^A-Za-z]/", "", $note["value"] );
    }
  }
  initiatecheckout_purchase($data, $vars, $vars_min, $shop, $cart_token, $user_agent, $user_agent_full, $ip);
}






/* ------------------------ SEND TO CAPI EVENT ------------------------- */



function send_to_CAPI_event($args, $test = false){
	global $shop;
	
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
	      "user_data" =>  array(),
        "custom_data" =>  array(
        )
			)
		)
	);
	
	/* ??? */
	if( !empty($args['cart_data']) ){
		/*
			$returned_data['content_ids'] = array();
			$returned_data['content_name'] = array();
			$returned_data['num_items'] = 0;
			if( $include_price ){
				$returned_data['value'] = 0;
			}
		*/
		if(!empty($args['cart_data']['content_ids'])){
			$payload['data'][0]['custom_data']['content_ids'] = $args['cart_data']['content_ids'];
		}
		//if(!empty($args['cart_data']['content_name'])){
		//	$payload['data'][0]['custom_data']['content_name'] = $args['cart_data']['content_name'];
		//}
		if(!empty($args['cart_data']['num_items'])){
			$payload['data'][0]['custom_data']['num_items'] = $args['cart_data']['num_items'];
		}
		if(!empty($args['cart_data']['value'])){
			//$payload['data'][0]['custom_data']['value'] =  number_format( floatval( $args['cart_data']['value'] ), 2);
			$args['event_value'] = round( floatval( $args['cart_data']['value'] ), 2);
		}	
		
		if(!empty($args['cart_data']['content_type'])){
			$payload['data'][0]['custom_data']['content_type'] = $args['cart_data']['content_type'];
		}
		
		
	}
	
	if (!empty($args['account_mode']) && $args['account_mode'] != ''){
		if( $args['account_mode'] == 'debug'){
			$payload['test_event_code'] = $args['t4'];
		}
	}
	
	if( !empty($args['em']) ){
		$payload['data'][0]['user_data']['em'] = $args['em'];
	} else if( !empty($session_data[0]['created']) ){
		$payload['data'][0]['user_data']['em'] = $session_data[0]['em'];
	}

	
	if( !empty($args['ph']) ){
		$payload['data'][0]['user_data']['ph'] = $args['ph'];
	} else if( !empty($session_data[0]['created']) ){
		$payload['data'][0]['user_data']['ph'] = $session_data[0]['ph'];
	}
	
	if( !empty($args['fn']) ){
		$payload['data'][0]['user_data']['fn'] = $args['fn'];
	} else if( !empty($session_data[0]['created']) ){
		$payload['data'][0]['user_data']['fn'] = $session_data[0]['fn'];
	}
	
	if( !empty($args['ln']) ){
		$payload['data'][0]['user_data']['ln'] = $args['ln'];
	} else if( !empty($session_data[0]['created']) ){
		$payload['data'][0]['user_data']['ln'] = $session_data[0]['ln'];
	}
	
	if( !empty($args['fbp']) ){
		$payload['data'][0]['user_data']['fbp'] = $args['fbp'];
	} else if( !empty($session_data[0]['created']) ){
		$payload['data'][0]['user_data']['em'] = $session_data[0]['em'];
	}
	
	if( !empty($args['fbc']) ){
		$payload['data'][0]['user_data']['fbc'] = $args['fbc'];
	} else if( !empty($session_data[0]['created']) ){
		$payload['data'][0]['user_data']['fbc'] = $session_data[0]['fbc'];
	}
	
	if( !empty($args['ip']) ){
		$payload['data'][0]['user_data']['client_ip_address'] = $args['ip'];
	} else if( !empty($session_data[0]['created']) ){
		$payload['data'][0]['user_data']['client_ip_address'] = $session_data[0]['ip'];
	}
	
	if( !empty($args['user_agent_full']) ){
		$payload['data'][0]['user_data']['client_user_agent'] = $args['user_agent_full'] ;
	} else if( !empty($session_data[0]['created']) ){
		$payload['data'][0]['user_data']['client_user_agent'] = $session_data[0]['user_agent_full'];
	}
	
	//if( empty($payload['data'][0]['custom_data']['value']) || intval($payload['data'][0]['custom_data']['value']) == 0 ){
		if( !empty($args['event_value']) ){
			$payload['data'][0]['custom_data']['value'] = round( floatval($args['event_value']), 2);
		} else {
			$payload['data'][0]['custom_data']['value'] = 0.00;
		}
		// if( !empty($args['external_id']) ){
		// 	$payload['data'][0]['user_data']['external_id'] = $args['external_id'];
		// }
	//} else {
	//}
  if( !empty($args['currency']) && strtoupper($args['currency']) != "NULL" && $args['currency']){
		$payload['data'][0]['custom_data']['currency'] = $args['currency'];
	} else if (!empty($args['db_currency']) && strtoupper($args['db_currency']) != "NULL") {
    $payload['data'][0]['custom_data']['currency'] = $args['db_currency'];
  } else {
    $payload['data'][0]['custom_data']['currency'] = "USD";
  }
  
	if( !empty($args['content_ids']) ){
		$payload['data'][0]['custom_data']['content_ids'] = $args['content_ids'];
	}
	
	//if( !empty($args['content_name']) ){
	//	$payload['data'][0]['custom_data']['content_name'] = $args['content_name'];
	//}
	
	if( !empty($args['content_group']) ){
		$payload['data'][0]['custom_data']['content_group'] = $args['content_group'];
	}
	
	if( !empty($args['content_category']) ){
		$payload['data'][0]['custom_data']['content_category'] = $args['content_category'];
	}
	
	if( !empty($args['num_items']) ){
		$payload['data'][0]['custom_data']['num_items'] = $args['num_items'];
	}
	if( !empty($args['order_id']) ){
		$payload['data'][0]['custom_data']['order_id'] = $args['order_id'];
	}
	if( !empty($args['event_id']) ){
		$payload['data'][0]['event_id'] = $args['event_id'];
	}

	// error_log("\n\n   FB PAYLOAD custom data".var_export($payload['data'][0]['custom_data'], true)."\n\n",0);

	$payload = json_encode($payload);
	

	if( ($shop == 'jeff-599.myshopify.com' ) ){
		//  error_log("\n\n  001 \n\n",0);

		 $sql = "INSERT INTO json_log (`shop`,`obj`) VALUES ('".$shop."','".substr($payload, 0, 3000)."');";
		 sqli_query($sql);
		//  $sql = "INSERT INTO json_log (`shop`,`obj`) VALUES ('".$shop."','".substr($data, 0, 3000)."');";
		//  sqli_query($sql);
		//  error_log("\n\n  ".$sql." \n\n",0);
	}
	
	curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
	curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
	$fb_result = curl_exec($ch);
	curl_close($ch);
	
	return $fb_result;
}

?>