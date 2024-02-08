<?php
// Include your database connection file
include('../connect.php');

if (isset($_GET['invoiceID'])) {
    $invoiceID = $_GET['invoiceID'];

    try {
        // Prepare and execute the delete query for the sales_invoice_items
        $deleteItemsQuery = "DELETE FROM sales_invoice_items WHERE salesInvoiceID = :invoiceID";
        $deleteItemsStmt = $db->prepare($deleteItemsQuery);
        $deleteItemsStmt->bindParam(':invoiceID', $invoiceID);
        $deleteItemsStmt->execute();

        // Prepare and execute the delete query for the sales_invoices
        $deleteInvoiceQuery = "DELETE FROM sales_invoices WHERE invoiceID = :invoiceID";
        $deleteInvoiceStmt = $db->prepare($deleteInvoiceQuery);
        $deleteInvoiceStmt->bindParam(':invoiceID', $invoiceID);
        $deleteInvoiceStmt->execute();

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