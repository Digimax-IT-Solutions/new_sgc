<?php
// Include your database connection code here
// ...
include('../connect.php');

// Log the received customerId parameter
error_log('Received customerId: ' . $_POST['customerName']);

$customerName = $_POST['customerName'];

// Fetch unpaid invoices for the selected customer
$query = "SELECT * FROM sales_invoice WHERE customer = :customerName AND (invoiceStatus = 'UNPAID' OR invoiceStatus = 'UNDERPAID' OR invoiceStatus = 'PAST DUE') AND status = 'active'";
$stmt = $db->prepare($query);
$stmt->bindParam(':customerName', $customerName);
$stmt->execute();
$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Return the unpaid invoices as JSON
echo json_encode($invoices);
?>
