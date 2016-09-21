<?php
include_once("config.php");

if(empty($_GET["order_id"])){
  echo json_encode(array("error"=>"Order id not found."));
}

$order_id = $_GET["order_id"];

$options = array( 'http' => array( 'method'  => 'GET') );  
$context = stream_context_create($options);
$contents = file_get_contents($PRICE_URL, false, $context);
$price = json_decode($contents);

$db_conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
$query="SELECT * FROM order_table WHERE order_id='".$order_id."'";
$result = $db_conn->query($query) or die($db_conn->error.__LINE__);

while($row= $result->fetch_assoc()) {
  $resultset[] = $row;
}

if(!empty($resultset[0]))
{
  $bits = intval(100000000.0*$resultset[0]["value"]/$price->price);
  //update price
  $resultset[0]["bits"] = $bits;
  echo json_encode($resultset[0]);
} 
else
{
  echo json_encode([]);
}

?>
