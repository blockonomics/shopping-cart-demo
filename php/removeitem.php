<?php
session_start();
include_once("include.php");
if(!empty($_SESSION["cart_item"])) {
    foreach($_SESSION["cart_item"] as $k => $v) {
        if($_GET["code"] == $k)
            unset($_SESSION["cart_item"][$k]);
        if(empty($_SESSION["cart_item"]))
            unset($_SESSION["cart_item"]);
    }
}
echo $json_response = json_encode(getCurrentCartItems());
?>
