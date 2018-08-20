<?php
include_once("include.php");
include_once("config.php");

if(empty($_GET["order_id"])){
  echo json_encode(array("error"=>"Order id not found."));
}

$order_id = $_GET["order_id"];

$db_conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($db_conn->connect_error) {
    die("Connection failed: " . $db_conn->connect_error);
}

$query=$db_conn->prepare("SELECT * FROM order_table WHERE order_id=?");
$query->bind_param("s", $order_id);

$query->execute();
$query->store_result();

$resultset = [];
while($row=fetchAssocStatement($query)) {
  $resultset[] = $row;
}

$result = $resultset[0];
function objectify(& $v, $k) {
  $v_decoded = json_decode($v, true);
  if ($v_decoded) { $v = $v_decoded; }
}

if(empty($resultset[0])){
  echo json_encode("{}");
} else {
  array_walk_recursive($result, 'objectify');
  echo json_encode($result);
}
?>
