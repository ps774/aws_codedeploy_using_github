<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header('Content-Type: application/json');
include('php/db.php');

// $referrer = $_SERVER['HTTP_REFERER'];
// $referrer_chunks = explode('/', $referrer);
// $domain = $referrer_chunks[2];

// $domain = str_ireplace('www.', '', $domain);
$data = array();
$conn = new mysqli($var_h, $var_u, $var_p, $var_n, $port);
if ($conn->connect_error) {
		die("MySQL Database Connection Failed: " . $conn->connect_error);
} 
/*
$id = preg_replace("/[^0-9]/", "", $_POST['s_id'] );

*/

$t1 = preg_replace("/[^A-Za-z0-9]/", "", $_POST['t1'] );
$t2 = preg_replace("/[^A-Za-z0-9]/", "", $_POST['t2'] );
$t3 = preg_replace("/[^A-Za-z0-9]/", "", $_POST['t3'] );
$t4 = preg_replace("/[^A-Za-z0-9]/", "", $_POST['t4'] );
if (!empty($_POST['event_type'])){
	$event_type = preg_replace("/[^A-Za-z\-\_]/", "", $_POST['event_type'] );
} else {
	$event_type = "";
}
if (!empty($_POST['event_value'])){
	$event_value = preg_replace("/[^0-9.]/", "", $_POST['event_value'] );
} else {
	$event_value = "";
}
if (!empty($_POST['event_id'])){
	$event_id = preg_replace("/[^A-Za-z0-9.\-\_]/", "", $_POST['event_id'] );
} else {
	$event_id = "";
}
if (!empty($_POST['currency'])){
	$currency = strtoupper( preg_replace("/[^A-Za-z]/", "", $_POST['currency'] ) );
} else {
	$currency = "";
}
if (!empty($_POST['shop'])){
	$shop = preg_replace("/[^A-Za-z0-9.\-\_]/", "", $_POST['shop'] );
} else {
	$shop = "";
}
if (!empty($_POST['shop_id'])){
	$account_id = preg_replace("/[^0-9]/", "", $_POST['shop_id'] );
} else {
	$account_id = "";
}
if (!empty($_POST['s_id'])){
	$session_id = preg_replace("/[^0-9]/", "", $_POST['s_id'] );
} else {
	$session_id = "";
}
if (!empty($_POST['em'])){
	$em = preg_replace("/[^A-Za-z0-9]/", "", $_POST['em'] );
} else {
	$em = "";
}
if (!empty($_POST['ph'])){
	$ph = preg_replace("/[^A-Za-z0-9]/", "", $_POST['ph'] );
} else {
	$ph = "";
}
if (!empty($_POST['fn'])){
	$fn = preg_replace("/[^A-Za-z0-9]/", "", $_POST['fn'] );
} else {
	$fn = "";
}
if (!empty($_POST['ln'])){
	$ln = preg_replace("/[^A-Za-z0-9]/", "", $_POST['ln'] );
} else {
	$ln = "";
}
if (!empty($_POST['fbp'])){
	$fbp = preg_replace("/[^A-Za-z0-9.\-\_]/", "", $_POST['fbp'] );
} else {
	$fbp = "";
}
if (!empty($_POST['fbc'])){
	$fbc = preg_replace("/[^A-Za-z0-9.\-\_]/", "", $_POST['fbc'] );
} else {
	$fbc = "";
}
if (!empty($_POST['client_ip_address'])){
	$ip = preg_replace("/[^0-9.]/", "", $_POST['client_ip_address'] );
} else {
	$ip = "";
}
if (!empty($_POST['client_user_agent'])){
	$user_agent = $_POST['client_user_agent'];
} else {
	$user_agent = "";
}
if (!empty($_POST['client_user_agent_full'])){
	$user_agent_full = $_POST['client_user_agent_full'];
} else {
	$user_agent_full = "";
}
if (!empty($_POST['content_ids'])){
	$content_ids = $_POST['content_ids'];
} else {
	$content_ids = "";
};
if (!empty($_POST['content_name'])){
	$content_name = $_POST['content_name'];
} else {
	$content_name = "";
};
if (!empty($_POST['content_group'])){
	$content_group = $_POST['content_group'];
} else {
	$content_group = "";
};
if (!empty($_POST['content_category'])){
	$content_category = $_POST['content_category'];
} else {
	$content_category = "";
};
if (!empty($_POST['num_items'])){
	$num_items = $_POST['num_items'];
} else {
	$num_items = "";
};
if (!empty($_POST['content_value'])){
	$content_value = $_POST['content_value'];
} else {
	$content_value = "";
};

$url = 'https://graph.facebook.com/v11.0/'.$t2.'/events?access_token='.$t3;

$ch = curl_init( $url );

$payload = array(
	'data' => array(
		array(
			"event_name" =>  $event_type,
            "event_time" =>  time(),
            "event_source_url" =>  'https://'.$_SERVER['HTTP_HOST'].'/index.php',
            "action_source" =>  "website",
            "user_data" =>  array(),
            "custom_data" =>  array()
		)
	)
);

$vars = array(
	'event_type' => $event_type,
	't1' => $t1,
	't2' => $t2,
	't3' => $t3,
	't4' => $t4,
	'shop' => $shop
);


if( !empty($em) ){
	if ($em == "undefined" || $em == NULL || $em == "null" || $em == "") {

	} else {
		$payload['data'][0]['user_data']['em'] = $em;
		$vars['em'] = $em;
	}
}

if( !empty($ph) ){
	if ($ph == "undefined" || $ph == NULL || $ph == "null" || $ph == "") {

	} else {
		$payload['data'][0]['user_data']['ph'] = $ph;
		$vars['ph'] = $ph;
	}
}

if( !empty($fn) ){
	if ($fn == "undefined" || $fn == NULL || $fn == "null" || $fn == "") {

	} else {
		$payload['data'][0]['user_data']['fn'] = $fn;
		$vars['fn'] = $fn;
	}
}

if( !empty($ln) ){
	if ($ln == "undefined" || $ln == NULL || $ln == "null" || $ln == "") {

	} else {
		$payload['data'][0]['user_data']['ln'] = $ln;
		$vars['ln'] = $ln;
	}
}

if( !empty($fbp) ){
	if ($fbp == "undefined" || $fbp == NULL || $fbp == "null" || $fbp == "") {

	} else {
		$payload['data'][0]['user_data']['fbp'] = $fbp;
		$vars['fbp'] = $fbp;
	}
}

if( !empty($fbc) ){
	if ($fbc == "undefined" || $fbc == NULL || $fbc == "null" || $fbc == "") {

	} else {
		$payload['data'][0]['user_data']['fbc'] = $fbc;
		$vars['fbc'] = $fbc;
	}

}
if( !empty( $event_id ) ){
	if ($event_id  == "undefined" || $event_id  == NULL || $event_id  == "null" || $event_id == "") {

	} else {
		$payload['data'][0]['event_id'] = $event_id ;
	}
}

if( !empty($ip) ){
	$payload['data'][0]['user_data']['client_ip_address'] = $ip;
	$vars['ip'] = $ip;
}

if( !empty($user_agent_full) ){
	$payload['data'][0]['user_data']['client_user_agent'] = $user_agent_full;
	$vars['user_agent'] =  preg_replace("/[^A-Za-z0-9]/", "", $user_agent );
	$vars['user_agent_full'] = preg_replace("/[^A-Za-z0-9]/", "", $user_agent_full );
}


if( $event_type == 'ViewContent' || $event_type == 'Purchase' || $event_type == 'AddToCart'){
	if( !empty($content_ids) ){
		$payload['data'][0]['custom_data']['content_ids'] = explode(",", $content_ids) ;
	}
	
	if( !empty($content_name) ){
		$payload['data'][0]['custom_data']['content_name'] = $content_name;
	}
	
	if( !empty($content_group) ){
		$payload['data'][0]['custom_data']['content_group'] = $content_group;
	}
	
	if( !empty($content_category) ){
		$payload['data'][0]['custom_data']['content_category'] = $content_category;
	}
	
	if( !empty($num_items) ){
		$payload['data'][0]['custom_data']['num_items'] = $num_items;
	}
	
	if( !empty($content_value) ){
		$event_value = $content_value;
	}
}


if( !empty($event_value) ){
	$payload['data'][0]['custom_data']['value'] = $event_value;
	$vars['event_value'] = $event_value;
} else {
	$payload['data'][0]['custom_data']['value'] = '0.00';
	$vars['event_value'] = '0.00';
}

if( !empty($currency) ){
	$payload['data'][0]['custom_data']['currency'] = $currency;
	$vars['currency'] = $currency;
} else {
	$payload['data'][0]['custom_data']['currency'] = 'USD';
	$vars['currency'] = 'USD';
}



$payload = json_encode($payload);

curl_setopt( $ch, CURLOPT_POSTFIELDS, $payload );
curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
$fb_result = curl_exec($ch);
curl_close($ch);

$fb_result = json_decode( $fb_result, true);
if ( !empty($fb_result['fbtrace_id'])) {
	$vars['fbtrace_id'] = $fb_result['fbtrace_id'];
} else { 
	$vars['fbtrace_id'] = "";
}

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

$vars_min = array();

$vars_min['event_type'] = $event_type;
if(!empty($account_id)){
	$vars_min['account_id'] = $account_id;
} 

if(!empty($session_id)){
	$vars_min['session_id'] = $session_id;
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


if( !empty($em) ){
	if ($em == "undefined" || $em == NULL || $em == "null") {
		$vars_min['em'] = 0;
	} else {
		$vars_min['em'] = 1;
	}
} else {
	$vars_min['em'] = 0;	
}

if( !empty($ph) ){
	if ($ph == "undefined" || $ph == NULL || $ph == "null") {
		$vars_min['ph'] = 0;
	} else {
		$vars_min['ph'] = 1;
	}
} else {
	$vars_min['ph'] = 0;	
}

if( !empty($fn) ){
	if ($fn == "undefined" || $fn == NULL || $fn == "null") {
		$vars_min['fn'] = 0;
	} else {
		$vars_min['fn'] = 1;
	}
} else {
	$vars_min['fn'] = 0;	
}

if( !empty($ln) ){
	if ($ln == "undefined" || $ln == NULL || $ln == "null") {
		$vars_min['ln'] = 0;
	} else {
		$vars_min['ln'] = 1;
	}
} else {
	$vars_min['ln'] = 0;	
}

if( !empty($fbp) ){
	if ($fbp == "undefined" || $fbp == NULL || $fbp == "null") {
		$vars_min['fbp'] = 0;
	} else {
		$vars_min['fbp'] = 1;
	}
} else {
	$vars_min['fbp'] = 0;	
}

if( !empty($fbc) ){
	if ($fbc == "undefined" || $fbc == NULL || $fbc == "null") {
		$vars_min['fbc'] = 0;
	} else {
		$vars_min['fbc'] = 1;
	}
} else {
	$vars_min['fbc'] = 0;	
}

if( !empty($ip) || $ip == NULL || $ip == "null"){
	$vars_min['ip'] = 1;
} else {
	$vars_min['ip'] = 0;
}

if( !empty( $user_agent ) || $user_agent == NULL || $user_agent == "null" ){
	$vars_min['user_agent'] = 1;
	$vars_min['user_agent_full'] = 1;
} else{
	$vars_min['user_agent'] = 0;
	$vars_min['user_agent_full'] = 0;
}

if( !empty($event_value) ){
	$vars_min['event_value'] = $event_value;
} else {
	$vars_min['event_value'] = '0.00';
}

if( !empty($currency) ){
	$vars_min['currency'] = $currency;
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

$data['fb_result'] = $fb_result;
//$data['sql'] = $sql;
//$data['payload'] = $payload;

echo json_encode($data);