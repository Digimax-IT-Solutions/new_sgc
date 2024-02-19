<?php
// Include your database connection code here
// ...
include('../connect.php');
// Fetch customers from the database
$query = "SELECT customerID, customerName, customerBalance FROM customers";
$stmt = $db->prepare($query);
$stmt->execute();
$customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return the customers as JSON
echo json_encode($customers);
?>
