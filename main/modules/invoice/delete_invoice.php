<?php

// Start session for user authentication
session_start();
// Include the database connection script
include('../../connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Start a transaction
        $db->beginTransaction();

        // Retrieve and sanitize form data
        $invoiceID = $_POST["invoiceID"];

        // Retrieve the total amount due and customer name from the sales invoice
        $invoiceQuery = "SELECT totalAmountDue, customer FROM sales_invoice WHERE invoiceID = :invoiceID";
        $invoiceStmt = $db->prepare($invoiceQuery);
        $invoiceStmt->bindParam(":invoiceID", $invoiceID);
        $invoiceStmt->execute();
        $invoice = $invoiceStmt->fetch(PDO::FETCH_ASSOC);

        if (!$invoice) {
            throw new Exception("Sales invoice not found.");
        }

        $totalAmountDue = $invoice["totalAmountDue"];
        $customer = $invoice["customer"];

        // Soft delete sales invoice items
        $softDeleteItemsQuery = "UPDATE sales_invoice_items SET status = 'deactivated' WHERE salesInvoiceID = :invoiceID";
        $softDeleteItemsStmt = $db->prepare($softDeleteItemsQuery);
        $softDeleteItemsStmt->bindParam(":invoiceID", $invoiceID);

        if (!$softDeleteItemsStmt->execute()) {
            throw new Exception("Error soft deleting sales invoice items.");
        }

        // Soft delete the sales invoice
        $softDeleteInvoiceQuery = "UPDATE sales_invoice SET status = 'deactivated' WHERE invoiceID = :invoiceID";
        $softDeleteInvoiceStmt = $db->prepare($softDeleteInvoiceQuery);
        $softDeleteInvoiceStmt->bindParam(":invoiceID", $invoiceID);

        if (!$softDeleteInvoiceStmt->execute()) {
            throw new Exception("Error deleting sales invoice.");
        }

        // Update customer balance
        $updateCustomerBalanceQuery = "UPDATE customers SET customerBalance = customerBalance - :totalAmountDue WHERE customerName = :customer";
        $updateCustomerBalanceStmt = $db->prepare($updateCustomerBalanceQuery);
        $updateCustomerBalanceStmt->bindParam(":totalAmountDue", $totalAmountDue);
        $updateCustomerBalanceStmt->bindParam(":customer", $customer);

        if (!$updateCustomerBalanceStmt->execute()) {
            throw new Exception("Error updating customer balance.");
        }

        // Log the soft delete in the audit trail
        $logQuery = "INSERT INTO audit_trail (tableName, recordID, action, userID, details) VALUES (:tableName, :recordID, :action, :userID, :details)";
        $logStmt = $db->prepare($logQuery);

        $tableName = "sales_invoice";
        $recordID = $invoiceID;
        $action = "delete";
        $userID = $_SESSION['SESS_MEMBER_ID'];
        $details = "Sales Invoice deleted with ID: $invoiceID";

        $logStmt->bindParam(":tableName", $tableName);
        $logStmt->bindParam(":recordID", $recordID);
        $logStmt->bindParam(":action", $action);
        $logStmt->bindParam(":userID", $userID);
        $logStmt->bindParam(":details", $details);

        if (!$logStmt->execute()) {
            throw new Exception("Error logging audit trail.");
        }

        // After the try-catch block
        if ($db->commit()) {
            // The sales invoice and its items are successfully soft-deleted from the database
            echo json_encode(["status" => "success", "message" => "Sales Invoice deleted!"]);
        } else {
            throw new Exception("Error committing the transaction.");
        }
    } catch (Exception $e) {
        // Rollback the transaction on exception
        $db->rollBack();

        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
?>
