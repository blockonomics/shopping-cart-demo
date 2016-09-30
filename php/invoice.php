<?php
include_once("config.php");

if(empty($_GET["order_id"])){
  echo json_encode(array("error"=>"Order id not found."));
}

$order_id = $_GET["order_id"];

$db_conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
$query="SELECT * FROM order_table WHERE order_id='".$order_id."'";
$result = $db_conn->query($query) or die($db_conn->error.__LINE__);

$resultset = [];
while($row= $result->fetch_assoc()) {
  $resultset[] = $row;
}

echo json_encode($resultset[0]);
?>
