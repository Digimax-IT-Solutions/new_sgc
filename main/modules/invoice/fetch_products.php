<?php
include('../connect.php');

$query = "SELECT itemID, itemName FROM items";
$result = $db->query($query);

$products = array();

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $products[] = array(
        'itemID' => $row['itemID'],
        'itemName' => $row['itemName']
    );
}

echo json_encode($products);
?>