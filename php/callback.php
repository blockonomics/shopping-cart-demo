<?php
include_once("config.php");
$db_conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($db_conn->connect_error) {
    die("Connection failed: " . $db_conn->connect_error);
}

$txid = $_GET['txid'];
$value = $_GET['value'];
$status = $_GET['status'];
$addr = $_GET['addr'];

//Match secret for security
if ($_GET['secret'] != $CALLBACK_SECRET) {
  echo "Secret is not matching.";
  return;
}

$query=$db_conn->prepare("SELECT status, bits,timestamp FROM order_table WHERE addr=?");
$query->bind_param("s", $addr);

$query->execute();
$result = $query->get_result();

$row= $result->fetch_assoc();
if ($row["status"]<-1){
  //payment already in error/expired, do nothing
  return;
}

$new_status = $status;
if ($status==0 && time() > $row["timestamp"]+600){
  //Payment expired, Paid after 10 minutes
  $new_status = -3;
  print('expired');  
}
if ($status==2 && $value != $row["bits"]){
  //Payment error, amount paid not matching expected
  $new_status = -2;
}

$query=$db_conn->prepare("UPDATE order_table SET status=?,txid=?,bits_payed=? WHERE addr=?");
$query->bind_param("isss", $new_status, $txid, $value, $addr);

$query->execute();

?>
