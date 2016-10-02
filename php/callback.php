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

$query="UPDATE order_table SET status='".$status."',txid='".$txid."',bits_payed=".$value." WHERE addr='".$addr."'";

$result = $db_conn->query($query) or die($db_conn->error.__LINE__);

?>
