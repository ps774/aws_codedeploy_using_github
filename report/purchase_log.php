<?php
if( stristr( $_SERVER['HTTP_HOST'] , "localhost:8888") == "localhost:8888"){
	$host_rep = "localhost";
	$db_name_rep = "popsixle";
	$db_user_rep = "root";
	$base_rep = 'popsixle/';
	$pass_rep = "root";
	$var_h_rep = "localhost";
	$var_n_rep = "popsixle";
	$var_u_rep = "root";
	$var_p_rep = "root";
	$port_rep = 8888;
	$base_rep_url_rep = "localhost:8888/";
	$host = "localhost";
	$db_name = "popsixle";
	$db_user = "root";
	$base = 'popsixle/';
	$pass = "root";
	$var_h = "localhost";
	$var_n = "popsixle";
	$var_u = "root";
	$var_p = "root";
	$port = 8888;
	$base_url = "localhost:8888/";

} else {

  $base = '';
  $host = "pop6prod2xl2zone.c56m0ch5mqih.us-east-1.rds.amazonaws.com";
  $db_name = "popsixle";
  $db_user = "dbmasteruser";
  $pass = "HunterGr33n!DataB8T";
  $var_h = "pop6prod2xl2zone.c56m0ch5mqih.us-east-1.rds.amazonaws.com";
  $var_u = "dbmasteruser";
  $var_p = "HunterGr33n!DataB8T";
  $port = 3309;
  $base_url = "staging.pop6serve.com/";
  /* query 1 - get all account ids*/
  $base_rep = '';
  $host_rep = "pop6prod2xl2zonereplica.c56m0ch5mqih.us-east-1.rds.amazonaws.com";
  $db_name_rep = "popsixle";
  $db_user_rep = "dbmasteruser";
  $pass_rep = "HunterGr33n!DataB8T";
  $var_h_rep = "pop6prod2xl2zonereplica.c56m0ch5mqih.us-east-1.rds.amazonaws.com";
  $var_u_rep = "dbmasteruser";
  $var_p_rep = "HunterGr33n!DataB8T";
  $port_rep = 3309;
  $base_rep_url_rep = "staging.pop6serve.com/";
}

function sqli_query($sql){
	global $host, $db_name, $db_user, $pass, $port;
	
	$conn = new mysqli($host, $db_user, $pass, $db_name, $port);
	if ($conn->connect_error) {
			return 'error: msqli connection error';	
	    die("Connection failed: " . $conn->connect_error);
	} 
	
	$result = $conn->query($sql);
	
	$conn->close();
	
	return $result;	
}

function sql_select_to_array_replica($sql){
	global $host_rep, $db_name_rep, $db_user_rep, $pass_rep, $port_rep;
	
	$conn = new mysqli($host_rep, $db_user_rep, $pass_rep, $db_name_rep, $port_rep);
	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	} 
	
	$result = $conn->query($sql);
	$data = false;
	
	if ($result->num_rows > 0) {
	   /* $data = array();
	    while($row = $result->fetch_assoc()) {
	        array_push($data, $row);
	    }*/
	    for ($data = array (); $row = $result->fetch_assoc(); $data[] = $row);
	}
	$conn->close();
	
	return $data;	
}

$sql = "SELECT id, account_name FROM popsixle.accounts WHERE expires > NOW() AND type != 99;";
$accounts = sql_select_to_array_replica($sql);
/* query 2 - for each, get puchase count from last hour */
foreach ($accounts as $account){
  $sql = "SELECT E.account_id, A.account_name, DATE_FORMAT(NOW() - INTERVAL 1 HOUR, '%Y-%m-%d %H:00:00') AS `interval`,
  COUNT(*) AS `purchases` FROM popsixle.processed_events_min E LEFT JOIN accounts A ON A.id = E.account_id
  WHERE E.account_id = ".$account['id']."
  AND E.timestamp > NOW() - INTERVAL 1 HOUR
  AND E.event_type = 'Purchase'
  ORDER BY E.id DESC;";

  $account_purchases = sql_select_to_array_replica($sql);
  if (!empty($account_purchases[0])){
    $sql = "INSERT IGNORE INTO popsixle.purchase_log (`account_id`,`account_name`,`interval`,`purchases`) VALUES ( '".$account['id']."', '".$account['account_name']."', '".$account_purchases[0]["interval"]."', '".$account_purchases[0]["purchases"]."' );";
    $success = sqli_query($sql);
    var_dump($success);
  }
}
?>
<div>Running</div>
