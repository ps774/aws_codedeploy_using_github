<?php


include('php/db.php');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header('Content-Type: application/json');


$vars = array();

if( !empty($_GET['graphRange']) ){
  $graph_range = $_GET['graphRange'];
}
if( !empty($_GET['shop']) ){
  $shop = $_GET['shop'];
}



$headers = apache_request_headers();
// if (!empty($headers['Shop'])){
//   $shop = $headers['Shop'];
// }


if (!empty($shop) && !empty($graph_range)) {
  $sql = 'SELECT DATE(timestamp) AS date, account_id AS `shop`, event_type, COUNT(*) AS `Total Events`,
  CASE WHEN event_type = "Purchase" THEN SUM(event_value) END AS "Total Revenue"
  FROM `processed_events_min`
  WHERE account_id IN (SELECT id FROM accounts WHERE shop = "'.$shop.'") AND event_type = "Purchase" AND DATEDIFF(NOW(),`timestamp`) < '.$graph_range.'
  GROUP BY DATE(timestamp) 
  ORDER BY DATE(timestamp) ASC';

  // $sql = 'SELECT DATE(timestamp) AS date, shop, event_type, COUNT(*) AS `Total Events`,
  // CASE WHEN event_type = "Purchase" THEN SUM(event_value) END AS "Total Revenue"
  // FROM `processed_events`
  // WHERE shop = "'.$shop.'" && event_type = "Purchase" 
  // GROUP BY DATE(timestamp) 
  // ORDER BY DATE(timestamp) ASC';
	$purchase_data = sql_select_to_array($sql);
  // error_log($sql, 0);
  // error_log(var_export($pixel_data, true),0);
  if (!empty($purchase_data)){
    // error_log(print_r($pixel_data, true));
    echo json_encode($purchase_data, true);
  }
}

?>