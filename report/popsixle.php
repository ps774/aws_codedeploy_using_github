<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header('Content-Type: text/javascript');
include('php/db.php');
if(!isset($_SESSION)) 
{ 
  session_start(); 
} 

$loaded = false;
if ( !empty($_SERVER['HTTP_REFERER'])){
	$referrer = $_SERVER['HTTP_REFERER'];
}

if( !empty($_GET['shop']) && empty($referrer) ){
	$referrer = 'https://'.$_GET['shop'];
}

if( !empty( $referrer ) ){
	
	$token = $_GET['t'];
	$referrer_chunks = explode('/', $referrer);
	$domain = $referrer_chunks[2];
	$domain = str_ireplace('www.', '', $domain);

	$sql = 'SELECT A.*, 
	F.sha_token AS `t1`,
	F.pixel_id AS `t2`,
	F.access_token AS `t3`,
	F.test_event_code AS `t4`,
	F.account_mode AS `account_mode`
	FROM accounts A
	LEFT JOIN fb_settings F ON A.sha_token = F.sha_token
	WHERE A.sha_token = "'.$token.'" AND A.domain LIKE "%'.$domain.'%";';
	
	$results = sql_select_to_array($sql);
	
	if(!empty($results[0]['email'])){
		if( empty($results[0]['expires']) ){
			//expires was never set - set up account
			$color = 'red_dot';
			$msg = 'Popsixle initialization failed. Your account needs to be activated.';
		} else if( date('Y-m-d') > date("Y-m-d",strtotime( $results[0]['expires'] )) ){
			//token has expired
			$color = 'red_dot';
			$msg = 'Popsixle initialization failed. Your token expired on '.$results[0]['expires'].'.';
		} else if( date('Y-m-d') <= date("Y-m-d",strtotime( $results[0]['expires'] )) ){
			//token has expired
			$color = 'green_dot';
			$msg = 'Popsixle loaded. Token active until '.$results[0]['expires'].'.';
			$loaded = true;
		} else {
			$color = 'red_dot';
			//  NOW: '.date('Y-m-d').' vs DB: '.date("Y-m-d",strtotime( $results[0]['expires'] )).'.
			$msg = 'Popsixle initialization failed. Something went wrong - please try again later.';
		}
	} else {
		//no token + domain match
		$color = 'red_dot';
		$msg = 'Popsixle initialization failed. No active account found for this domain.';
	}
	
} else {
	header("HTTP/1.1 401 Unauthorized");
	exit;
}

if($loaded){
	// if ($_SERVER['HTTP_REFERER'] == "https://www.fragrancex.com/" || $_SERVER['HTTP_REFERER'] == "https://www.purfume.com/" ) {
	// 	$script =  'pop6.min.js';
	// } else {
		$script =  'pop6.js';
	// }
	
	if( $_SERVER['HTTP_HOST'] == "localhost:8888" ){
		$version = 'Popsixle STAGING';
		echo "
	var ping_url = 'http://".$base_url."popsixle/pop6_ping.php';
	var ping_base = 'http://".$base_url."popsixle/';
	";
	} else if ($_SERVER['HTTP_HOST'] == "staging.pop6serve.com"){
		$version = 'Popsixle STAGING SERVER';
		echo "
	var ping_url = 'https://".$base_url."pop6_ping.php';
	var ping_base = 'https://".$base_url."';
	";
	} else {
		$version = 'Popsixle';
		echo "
	var ping_url = 'https://".$base_url."pop6_ping.php';
	var ping_base = 'https://".$base_url."';
	";
	}
}

echo '
const pop6 = String.fromCodePoint(0x1F36D);
const red_dot = String.fromCodePoint(0x1F534) + " ";
const green_dot = String.fromCodePoint(0x1F7E2) + " ";
const white_dot = String.fromCodePoint(0x26AA) + " ";
if(console.log == "User denied the request for Geolocation."){

} else {
	console.log('.$color.' + pop6 + " '.$msg.'");
}
';

	
	
if($loaded) {
	include('md5.js');
	
	echo '
';
	include($script);
	echo '
		const a10x_dl = {};
		a10x_dl.t1 = "'.$results[0]['t1'].'";
		a10x_dl.t2 = "'.$results[0]['t2'].'";
		a10x_dl.t3 = "'.$results[0]['t3'].'";
		a10x_dl.t4 = "'.$results[0]['t4'].'";
		a10x_dl.shop = "'.$results[0]['shop'].'";
		a10x_dl.shop_id = "'.$results[0]['id'].'";
		a10x_dl.metadata_mode = "'.$results[0]['metadata_mode'].'";
	    pop6_init_01();
';
}
?>
