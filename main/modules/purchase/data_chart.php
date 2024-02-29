<?php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'digimax2023';
$db_database = 'sgc_db';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define the invoice statuses
$unpaidStatus = 'UNPAID';
$paidStatus = 'PAID';
$voidStatus = 'VOID';
$pastDueStatus = 'PAST DUE';

// Count the total number of UNPAID rows
$sqlUnpaid = "SELECT COUNT(*) as unpaidCount FROM sales_invoice WHERE invoiceStatus = '$unpaidStatus'";
$resultUnpaid = $conn->query($sqlUnpaid);

// Count the total number of PAID rows
$sqlPaid = "SELECT COUNT(*) as paidCount FROM sales_invoice WHERE invoiceStatus = '$paidStatus'";
$resultPaid = $conn->query($sqlPaid);

// Count the total number of VOID rows
$sqlVoid = "SELECT COUNT(*) as voidCount FROM sales_invoice WHERE invoiceStatus = '$voidStatus'";
$resultVoid = $conn->query($sqlVoid);

// Count the total number of PAST DUE rows
$sqlPastDue = "SELECT COUNT(*) as pastDueCount FROM sales_invoice WHERE invoiceStatus = '$pastDueStatus'";
$resultPastDue = $conn->query($sqlPastDue);

$data = [];

if ($resultUnpaid->num_rows > 0) {
    $rowUnpaid = $resultUnpaid->fetch_assoc();
    $unpaidCount = $rowUnpaid['unpaidCount'];
    $data['unpaidCount'] = $unpaidCount;
}

if ($resultPaid->num_rows > 0) {
    $rowPaid = $resultPaid->fetch_assoc();
    $paidCount = $rowPaid['paidCount'];
    $data['paidCount'] = $paidCount;
}

if ($resultVoid->num_rows > 0) {
    $rowVoid = $resultVoid->fetch_assoc();
    $voidCount = $rowVoid['voidCount'];
    $data['voidCount'] = $voidCount;
}

if ($resultPastDue->num_rows > 0) {
    $rowPastDue = $resultPastDue->fetch_assoc();
    $pastDueCount = $rowPastDue['pastDueCount'];
    $data['pastDueCount'] = $pastDueCount;
}

// Close the connection
$conn->close();

// Output data as JSON
echo json_encode($data);
?>