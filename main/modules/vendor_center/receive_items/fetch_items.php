<?php

include '../../connect.php';
// Fetch product items
$query = "SELECT itemName, itemPurchaseInfo, itemCost, uom, itemQty FROM items";
$result = $db->query($query);

$productItems = array();

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $productItems[] = $row;
}


?>