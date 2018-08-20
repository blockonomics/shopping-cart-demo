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

function fetchAssocStatement($stmt)
{
    if($stmt->num_rows>0)
    {
        $result = array();
        $md = $stmt->result_metadata();
        $params = array();
        while($field = $md->fetch_field()) {
            $params[] = &$result[$field->name];
        }
        call_user_func_array(array($stmt, 'bind_result'), $params);
        if($stmt->fetch())
            return $result;
    }

    return null;
}
?>
