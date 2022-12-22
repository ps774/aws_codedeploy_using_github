<?php
if(!isset($_SESSION)) 
{ 
  session_start(); 
} 
session_destroy();
if(!isset($_SESSION)) 
{ 
  session_start(); 
} 

include_once('php/db.php');
$errorPage = "/".$base."404.php";


$str = $_GET['l'];
$sql = 'SELECT * FROM verified LEFT JOIN accounts ON verified.email = accounts.email WHERE verification_code = "'.$str.'" AND verified.type = 1;';
$results = sql_select_to_array($sql);

$r = $results[0];
if( !empty($r['email']) ){
	$email = $r['email'];
	
	$sql = 'UPDATE accounts SET verified_email = 1 WHERE email = "'.$email.'";';
	$result = sqli_query($sql);
	if($result){

		$url = '/'.$base.'password.php?success=verify';
		
		$_SESSION['user'] = array(
			'status' => 'logged_in',
			'username' => $r['username'],
			'type' => 1,
			'email' => $email
		);
		
		header('Location: '.$url);
		exit();
	} else {
		header('Location: '.$errorPage.'?message='.urlencode('Verify link error (1).') );
		exit();
	}
} 
//var_dump($r);
header('Location: '.$errorPage.'?message='.urlencode('Verify link error (2).') );
exit();


