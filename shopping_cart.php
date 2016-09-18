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

  case "createinvoice":
    //return here if cart is empry
    if(empty($_SESSION["cart_item"])) {
      echo $json_response = json_encode(array("error" => "Cart Is Empty"));
      return;
    }

    $api_key = 'IQbMTIZPgvJN7msxSQtDRcb6onMKxUmttMaPFy1kJg4';
    $url = 'http://localhost:8080/api/new_address';
    $data = '';
    $order_id = uniqid();

    $options = array( 
      'http' => array(
        'header'  => 'Authorization: Bearer '.$api_key,
        'method'  => 'POST',
        'content' => $data
      )   
    );  

    //Generate new address for this invoice
    $context = stream_context_create($options);
    $contents = file_get_contents($url, false, $context);
    $new_address = json_decode($contents);

    $total_cost = 0;
    foreach($_SESSION["cart_item"] as $key => $value) {
      $current_cart[] = $value;
      $total_cost += ($value["price"]*$value["quantity"]);
    }

    $cart_string = json_encode($current_cart);
    $query="INSERT INTO order_table (order_id, addr, txid, status, cart, value) VALUES 
      ('".$order_id."', '".$new_address->address."', '', 0, '".$cart_string."', ".$total_cost.")";
    $result = $db_conn->query($query) or die($db_conn->error.__LINE__);

    //Clear current cart
    unset($_SESSION["cart_item"]);

    //Add new address to blockonomics address watcher
    $url = 'http://localhost:8080/api/address';
    $data = array('addr' => $new_address->address, 'tag' => $order_id);

    $options = array( 
      'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n".
        "Authorization: Bearer ".$api_key."\r\n",
        'method'  => 'POST',
        'content' => json_encode($data)
      )   
    );  

    //Generate new address for this invoice
    $context = stream_context_create($options);
    $contents = file_get_contents($url, false, $context);

    echo json_encode(array("order_id" => $order_id));
    break;

  case "getinvoice":
    if(empty($_GET["order_id"])){
      echo json_encode(array("error"=>"Order id not found."));
    }

    $order_id = $_GET["order_id"];

    $url = 'http://localhost:8080/api/price?currency=USD';
    $options = array( 'http' => array( 'method'  => 'GET') );  
    $context = stream_context_create($options);
    $contents = file_get_contents($url, false, $context);
    $price = json_decode($contents);

    $query="SELECT * FROM order_table WHERE order_id='".$order_id."'";
    $result = $db_conn->query($query) or die($db_conn->error.__LINE__);

    while($row= $result->fetch_assoc()) {
      $resultset[] = $row;
    }

    if(!empty($resultset[0]))
    {
      $bits = intval(100000000.0*$resultset[0]["value"]/$price->price);
      //update price
      $resultset[0]["bits"] = $bits;
      echo json_encode($resultset[0]);
    } 
    else
      echo json_encode([]);
    break;
  }
}
?>
