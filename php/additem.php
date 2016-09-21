<?php
session_start();
include_once("include.php");
include_once("config.php");
$db_conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);

if(!empty($_GET["quantity"])) {
    $query = "SELECT * FROM product_table WHERE code='" . $_GET["code"] . "'";
    $result = $db_conn->query($query);

    while($row= $result->fetch_assoc()) {
        $productByCode[] = $row;
    }

    $itemArray = array($productByCode[0]["code"]=>array('name'=>$productByCode[0]["name"], 'code'=>$productByCode[0]["code"], 'quantity'=>$_GET["quantity"], 'price'=>$productByCode[0]["price"]));

    if(!empty($_SESSION["cart_item"])) {
        if(array_key_exists($productByCode[0]["code"],$_SESSION["cart_item"])) {
            foreach($_SESSION["cart_item"] as $k => $v) {
                if($productByCode[0]["code"] == $k)
                    $_SESSION["cart_item"][$k]["quantity"] += $_GET["quantity"];
            }
        } else {
            $_SESSION["cart_item"] = array_merge($_SESSION["cart_item"],$itemArray);
        }
    } else {
        $_SESSION["cart_item"] = $itemArray;
    }

    echo $json_response = json_encode(getCurrentCartItems());
}
?>
