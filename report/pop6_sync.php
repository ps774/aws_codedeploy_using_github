<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header('Content-Type: application/json');
include('php/db.php');

$conn = new mysqli($var_h, $var_u, $var_p, $var_n, $port);
if ($conn->connect_error) {
		die("MySQL Database Connection Failed: " . $conn->connect_error);
} 

$id = $_POST['session_id'];
$key =  preg_replace("/[^A-Za-z0-9\-\_]/", "", $_POST['passed_key'] ); // cleanse this
$value = $_POST['passed_value'];
$data = array();

if($value === ''){
	$value = null;
}

$data['debug'] = array(
	'id' => $id,
	'key' => $key,
	'value' => $value,
	'$_POST' => $_POST
);

if( !empty( $id ) && !empty( $key ) ){

	$sql = 'UPDATE `session` SET `'.$key.'` = ? WHERE `id` = ?;';
	if($stmt = mysqli_prepare($conn, $sql)){
		mysqli_stmt_bind_param($stmt,'si', $s1,$s2); 
		
		$s1 = $value;
		$s2 = $id;

		mysqli_stmt_execute($stmt);	
		mysqli_stmt_close($stmt);
		
		$data['response'] = 'session sync success';
	} else {
		$data['response'] = 'db update failed';
	}
	
	$data['sql'] = $sql;
	
}
echo json_encode($data);