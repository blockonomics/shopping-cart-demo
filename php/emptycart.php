<?php
session_start();
unset($_SESSION["cart_item"]);
echo $json_response = json_encode([]);
?>
