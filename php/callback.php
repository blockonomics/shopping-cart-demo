<?php
include_once("config.php");
$db_conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

$txid = $_GET['txid'];
$value = $_GET['value'];
$status = $_GET['status'];
$addr = $_GET['addr'];

//Match secret for security
if ($_GET['secret'] != $CALLBACK_SECRET) {
  echo "Secret is not matching.";
  return;
}

$query = "SELECT status, bits,timestamp FROM order_table WHERE addr='" .$addr ."'";
$result = $db_conn->query($query) or die($db_conn->error.__LINE__);

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
  
    
$query="UPDATE order_table SET status='".$new_status."',txid='".$txid."',bits_payed=".$value." WHERE addr='".$addr."'";
$result = $db_conn->query($query) or die($db_conn->error.__LINE__);

?>
