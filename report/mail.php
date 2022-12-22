<?php
  require_once "Mail.php";
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
  
  $sql = 'SELECT COUNT(id) AS `purchases` FROM popsixle.processed_events_min WHERE account_id != 0 AND `timestamp` > NOW() - INTERVAL "15" MINUTE AND event_type = "Purchase";';
  $purchases = sql_select_to_array_replica($sql);
  /* query 2 - for each, get puchase count from last hour */
  $from = "dylan@popsixle.com";
  $to = 'dylan@popsixle.com, noah@popsixle.com, zach@popsixle.com';

  $host = "ssl://smtp.gmail.com";
  $port = "465";
  $username = 'dylan@popsixle.com';
  $password = 'xnuziwmyjzjyrfgd';
  if (intval($purchases[0]['purchases']) < 100) {
    
    $subject = "ALERT!! PURCHASES LOW";
    $body = "Purchases were under 100 in the last 15 Minutes! They Were ".$purchases[0]['purchases']."";
  
    $headers = array ('From' => $from, 'To' => $to,'Subject' => $subject);
    $smtp = Mail::factory('smtp',
      array ('host' => $host,
        'port' => $port,
        'auth' => true,
        'username' => $username,
        'password' => $password));
  
    $mail = $smtp->send($to, $headers, $body);
  
    if (PEAR::isError($mail)) {
      echo($mail->getMessage());
    } else {
      // echo("Message successfully sent!\n");
    }
  } else if (empty($purchases)) {
    $subject = "ALERT!! PURCHASE EMPTY";
    $body = "Purchases were Empty in the last 15 Minutes. (Could be a cron job code issue)";
  
    $headers = array ('From' => $from, 'To' => $to,'Subject' => $subject);
    $smtp = Mail::factory('smtp',
      array ('host' => $host,
        'port' => $port,
        'auth' => true,
        'username' => $username,
        'password' => $password));
  
    $mail = $smtp->send($to, $headers, $body);
  
    if (PEAR::isError($mail)) {
      echo($mail->getMessage());
    } else {
      // echo("Message successfully sent!\n");
    }
  }


  ?>