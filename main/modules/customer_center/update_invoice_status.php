<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate input data
    if(isset($_POST['invoiceIDs']) && is_array($_POST['invoiceIDs']) && isset($_POST['payment_amount'])) {
        // Sanitize and prepare the invoice IDs
        $invoiceIDs = array_map('intval', $_POST['invoiceIDs']);
        $payment_amount = floatval($_POST['payment_amount']);
        
        // Begin a transaction
        $db->beginTransaction();

        try {
            // Update invoice statuses and amountReceived
            foreach ($invoiceIDs as $invoice_id) {
                // Get the total amount due for the invoice
                $getTotalAmountDueQuery = "SELECT totalAmountDue FROM sales_invoice WHERE invoiceID = :invoiceID";
                $getTotalAmountDueStmt = $db->prepare($getTotalAmountDueQuery);
                $getTotalAmountDueStmt->bindParam(':invoiceID', $invoice_id, PDO::PARAM_INT);
                $getTotalAmountDueStmt->execute();
                $totalAmountDue = $getTotalAmountDueStmt->fetchColumn();

                // Update invoice status based on payment amount
                if ($payment_amount >= $totalAmountDue) {
                    $updateInvoiceQuery = "UPDATE sales_invoice SET invoiceStatus = 'PAID' WHERE invoiceID = :invoiceID";
                } elseif ($payment_amount < $totalAmountDue) {
                    $updateInvoiceQuery = "UPDATE sales_invoice SET invoiceStatus = 'UNDERPAID' WHERE invoiceID = :invoiceID";
                }

                // Update invoice status
                $updateInvoiceStmt = $db->prepare($updateInvoiceQuery);
                $updateInvoiceStmt->bindParam(':invoiceID', $invoice_id, PDO::PARAM_INT);
                $updateInvoiceStmt->execute();

                // Update amountReceived
                $updateAmountReceivedQuery = "UPDATE sales_invoice SET amountReceived = amountReceived + :payment_amount WHERE invoiceID = :invoiceID";
                $updateAmountReceivedStmt = $db->prepare($updateAmountReceivedQuery);
                $updateAmountReceivedStmt->bindParam(':invoiceID', $invoice_id, PDO::PARAM_INT);
                $updateAmountReceivedStmt->bindParam(':payment_amount', $payment_amount, PDO::PARAM_STR);
                $updateAmountReceivedStmt->execute();

                // Check for errors
                if ($updateAmountReceivedStmt->errorCode() !== '00000') {
                    $errors = $updateAmountReceivedStmt->errorInfo();
                    throw new Exception("Error updating amountReceived: " . $errors[2]); // Throw an exception to trigger rollback
                }
            }

            // Commit the transaction
            $db->commit();
            echo "success";
        } catch (PDOException $e) {
            // Rollback the transaction in case of an error
            $db->rollback();
            error_log("Database error: " . $e->getMessage());
            echo "Error: Database error.";
        } catch (Exception $ex) {
            // Rollback the transaction in case of an error
            $db->rollback();
            error_log("Error: " . $ex->getMessage());
            echo "Error: " . $ex->getMessage();
        }
    } else {
        echo "Invalid invoice IDs or payment amount.";
    }
} else {
    echo "Invalid request.";
}
?>
