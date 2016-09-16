<?php
session_start();
require_once("db.php");

function getCurrentCartItems() {
    if(!empty($_SESSION["cart_item"])) {
      foreach($_SESSION["cart_item"] as $key => $value) {
        $itemsInCart[] = $value;
      }

      return $itemsInCart;
    }
    else
      return [];
}

if(!empty($_GET["action"])) {
  switch($_GET["action"]) {
  case "getitems":
    $query="SELECT * FROM product_table ORDER BY id ASC";
    $result = $db_conn->query($query) or die($db_conn->error.__LINE__);

    while($row= $result->fetch_assoc()) {
      $resultset[] = $row;
    }

    echo $json_response = json_encode($resultset);
    break;

  case "getcart":
      echo $json_response = json_encode(getCurrentCartItems());
    break;

  case "add":
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
    break;

  case "remove":
    if(!empty($_SESSION["cart_item"])) {
      foreach($_SESSION["cart_item"] as $k => $v) {
        if($_GET["code"] == $k)
          unset($_SESSION["cart_item"][$k]);
        if(empty($_SESSION["cart_item"]))
          unset($_SESSION["cart_item"]);
      }
    }
    
    echo $json_response = json_encode(getCurrentCartItems());
    break;
  case "empty":
    unset($_SESSION["cart_item"]);
    echo $json_response = json_encode([]);
    break;
  }
}
?>
