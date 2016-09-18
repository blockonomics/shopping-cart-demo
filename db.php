<?php 
include_once("auth.php");
$DB_HOST = '127.0.0.1';
$DB_USER = $DBUSER;
$DB_PASS = $DBPWD;
$DB_NAME = 'shopping_cart';
$db_conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
?>
