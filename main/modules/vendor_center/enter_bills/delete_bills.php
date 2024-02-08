<?php
// Include your database connection file
include('../connect.php');

if (isset($_GET['bill_id'])) {
    $bill_id = $_GET['bill_id'];

    try {
        // Prepare and execute the delete query for the sales_invoice_items
        $deleteItemsQuery = "UPDATE bills SET status = 'deactivated' WHERE bill_id = :bill_id";
        $deleteItemsStmt = $db->prepare($deleteItemsQuery);
        $deleteItemsStmt->bindParam(':bill_id', $bill_id);
        $deleteItemsStmt->execute();

        // Prepare and execute the delete query for the sales_invoices

        // Redirect to the sales transactions page after successful deletion
        // Redirect to the sales_invoice page after successful deletion
header('Location: ../../' . $page='sales_invoice');
        exit();
    } catch (PDOException $e) {
        // Log the exception message to the error log
        error_log("PDOException: " . $e->getMessage());
        // You may want to handle the error differently, e.g., display an error message
        echo "Error deleting invoice.";
    }
}
?>