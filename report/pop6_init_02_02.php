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

$ip = preg_replace("/[^0-9.]/", "", $_POST['client_ip_address'] );
$user_agent_clean = preg_replace("/[^A-Za-z]/", "", $_POST['client_user_agent'] );
$user_agent_full =  $_POST['client_user_agent_full']; //no need to sanitize - only used within prepared statement INSERT
$shop = preg_replace("/[^A-Za-z0-9.\-\_]/", "", $_POST['shop'] );


$sql = 'SELECT * FROM `session` WHERE `ip` = "'.$ip.'" AND `user_agent` = "'.$user_agent_clean.'" AND `shop` = "'.$shop.'" ORDER BY created DESC;';
$session_data = sql_select_to_array($sql);

if( $session_data === false ){
	// no session found in the db
	
	//create new session, grab and return new session id and source as new
	$sql = 'INSERT INTO `session` (`ip`,`user_agent`,`user_agent_full`,`shop`) VALUES (?,?,?,?);';
	if($stmt = mysqli_prepare($conn, $sql)){
		mysqli_stmt_bind_param($stmt,'ssss', $s1,$s2,$s3,$s4); 
		
		$s1 = $ip;
		$s2 = $user_agent_clean;
		$s3 = $user_agent_full;
		$s4 = $shop;	

		mysqli_stmt_execute($stmt);	
		$session_id = mysqli_stmt_insert_id($stmt);
		
		$data['session_id'] = $session_id;
		$data['session_source'] = 'new';
		
		mysqli_stmt_close($stmt);

	} else {
		$data['session_id'] = null;
		$data['error_msg'] = 'database error: prepared statement failed';
	}

} else {
	// session found
	
	$data['session_id'] = $session_data[0]['id'];
	$data['session_data'] = $session_data[0];
	$data['session_source'] = 'db';
}

$data['sql'] = $sql;

echo json_encode($data);