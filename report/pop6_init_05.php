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

if( !empty( $_POST['t1'] ) ){
	$t1 = preg_replace("/[^A-Za-z0-9]/", "", $_POST['t1'] );
} else {
	$data['error_msg'] = 'database error: no t1';
}

$keys = array('events','form_fields');
$sql = 'SELECT * FROM `events` WHERE `account_token` = "'.$t1.'";';
$sql .= 'SELECT * FROM `form_fields` WHERE `account_token` = "'.$t1.'";';

$db_data = sql_multi_select_to_array($sql,$keys);
$data['events'] = $db_data['events'];
$data['form_fields'] = $db_data['form_fields'];
//$data['sql'] = $sql;

echo json_encode($data);