<?php
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
?>
