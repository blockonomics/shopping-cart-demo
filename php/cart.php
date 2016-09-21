<?php
session_start();
include_once("include.php");
echo $json_response = json_encode(getCurrentCartItems());
?>
