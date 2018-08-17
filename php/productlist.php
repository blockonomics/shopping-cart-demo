<?php
session_start();
include_once("config.php");

$db_conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if ($db_conn->connect_error) {
    die("Connection failed: " . $db_conn->connect_error);
}

$query="SELECT * FROM product_table ORDER BY id ASC";
$result = $db_conn->query($query);

while($row= $result->fetch_assoc()) {
    $resultset[] = $row;
}

echo $json_response = json_encode($resultset);
?>
