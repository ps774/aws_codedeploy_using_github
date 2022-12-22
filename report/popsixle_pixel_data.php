<?php


include('php/db.php');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header('Content-Type: application/json');
// header("Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS");
// $json = file_get_contents('php://input');
// $data = json_decode($json, true);
// error_log(var_export($data, true),0);

$vars = array();
//hard coded for this specific page, can become logic, pulled from the 



$headers = apache_request_headers();
if (!empty($headers['Shop'])){
  $shop = $headers['Shop'];

}
if (!empty($headers['Low-Range'])){
  $low_range = $headers['Low-Range'];

}
if (!empty($headers['High-Range'])){
  $high_range = $headers['High-Range'];

}
// error_log($headers['Low-Range'],0);
// error_log($headers['Shop'],0);
// error_log($headers['High-Range'],0);

if (!empty($shop) && !empty($low_range) && !empty($high_range)) {
  $sql = 'SELECT event_type, COUNT(*) AS `Total Events`,
	TRUNCATE(100.0 * SUM(fbtrace_id>0) / COUNT(*), 1) AS `% recorded`,
	TRUNCATE(100.0 * SUM(em>0) / COUNT(*), 1) AS `Em`,
	TRUNCATE(100.0 * SUM(ph>0) / COUNT(*), 1) AS `Ph`,
	TRUNCATE(100.0 * SUM(fn>0) / COUNT(*), 1) AS `Fn`,
	TRUNCATE(100.0 * SUM(ln>0) / COUNT(*), 1) AS `Ln`,
	TRUNCATE(100.0 * SUM(ip>0) / COUNT(*), 1) AS `IP`,
	TRUNCATE(100.0 * SUM(user_agent>0) / COUNT(*), 1) AS `User Agent`,
	TRUNCATE(100.0 * SUM(fbp>0) / COUNT(*), 1) AS `fbp`,
	TRUNCATE(100.0 * SUM(fbc>0) / COUNT(*), 1) AS `fbc`,
	CASE WHEN event_type = "Purchase" THEN SUM(event_value) END AS "Total Revenue"
 	FROM `processed_events_min`
 	WHERE timestamp >"'.$low_range.'" AND account_id IN (SELECT id FROM accounts WHERE shop = "'.$shop.'" AND expires IS NOT NULL) GROUP BY event_type ORDER BY `Total Events` DESC;';
  // $sql = 'SELECT event_type, COUNT(*) AS `Total Events`,
	// TRUNCATE( 100.0 * SUM(fbtrace_id LIKE "%A%") / COUNT(*), 1) AS `% recorded`,
	// TRUNCATE( 100.0 * SUM(LENGTH(em)>5) / COUNT(*), 2) AS `Em`,
	// TRUNCATE( 100.0 * SUM(LENGTH(ph)>5) / COUNT(*), 2) AS `Ph`,
	// TRUNCATE( 100.0 * SUM(LENGTH(fn)>5) / COUNT(*), 2) AS `Fn`,
	// TRUNCATE( 100.0 * SUM(LENGTH(ln)>5) / COUNT(*), 2) AS `Ln`,
	// TRUNCATE( 100.0 * SUM(LENGTH(ip)>5) / COUNT(*), 2) AS `IP`,
	// TRUNCATE( 100.0 * SUM(LENGTH(user_agent)>5) / COUNT(*), 2) AS `User Agent`,
	// TRUNCATE( 100.0 * SUM(LENGTH(fbp)>5) / COUNT(*), 2) AS `fbp`,
	// TRUNCATE( 100.0 * SUM(LENGTH(fbc)>5) / COUNT(*), 2) AS `fbc`,
	// CASE WHEN event_type = "Purchase" THEN SUM(event_value) END AS "Total Revenue"
	// FROM `processed_events`
	// WHERE timestamp >"'.$low_range.'" AND
  // shop = "'.$shop.'" GROUP BY event_type ORDER BY `Total Events` DESC;';
	$pixel_data = sql_select_to_array($sql);
  // error_log($sql, 0);
  // error_log(var_export($pixel_data, true),0);
  if (!empty($pixel_data)){
    // error_log(print_r($pixel_data, true));
    echo json_encode($pixel_data, true);
  }
}

?>