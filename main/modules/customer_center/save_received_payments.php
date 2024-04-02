<?php
// Include your database connection file
include('../connect.php');

// Set error logging for PHP
ini_set('log_errors', 1);
ini_set('error_log', '/var/log/apache2/php_error.log'); // Specify the path to your PHP error log file

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input data
    $ar_account = $_POST['ar_account'];
    $customerName = $_POST['customerName'];
    $payment_amount = $_POST['payment_amount'];
    $receivedDate = $_POST['receivedDate'];
    $invoiceNo = $_POST['invoiceNo'];
    // Validate and sanitize input data
    $discCredapplied = $_POST ['discCredapplied'];
    $paymentType = $_POST['paymentType'];
    $creditAmount = floatval($_POST['creditAmount']);
    $excessAmount = $_POST['excessAmount'];
    $referenceNumber = $_POST['RefNo']; // Use the same reference number for both cash and check

    // Extract checked invoice numbers
    $checkedInvoices = isset($_POST['invoice']) ? $_POST['invoice'] : array();

    // Begin a transaction
    $db->beginTransaction();

    try {
        // Insert payment details into the receive_payment table
        $query = "INSERT INTO receive_payment (ar_account, customerName, payment_amount, receivedDate, discCredapplied, paymentType, RefNo, invoiceNo) SELECT :ar_account, :customerName, :payment_amount, :receivedDate, :discCredapplied, :paymentType, :referenceNumber, sales_invoice.invoiceNo FROM sales_invoice WHERE sales_invoice.invoiceID = :invoiceNo";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':ar_account', $ar_account);
        $stmt->bindParam(':customerName', $customerName);
        $stmt->bindParam(':payment_amount', $payment_amount);
        $stmt->bindParam(':receivedDate', $receivedDate);
        $stmt->bindParam(':discCredapplied', $discCredapplied);
        $stmt->bindParam(':paymentType', $paymentType);
        $stmt->bindParam(':referenceNumber', $referenceNumber);
        $stmt->bindParam(':invoiceNo', $invoiceNo);

        // Bind invoice numbers
        $stmt->bindParam(':invoiceNo', $invoiceNo);

        // Loop through each checked invoice and execute the query for each invoice
        foreach ($checkedInvoices as $invoice) {
            $invoiceNo = $invoice; // Set the current invoice number
            $stmt->execute();
        }

        // Update customer balance
        $updateCustomerQuery = "UPDATE customers SET customerBalance = customerBalance - :payment_amount WHERE customerName = :customerName";
        $updateCustomerStmt = $db->prepare($updateCustomerQuery);
        $updateCustomerStmt->bindParam(':payment_amount', $payment_amount);
        $updateCustomerStmt->bindParam(':customerName', $customerName);
        $updateCustomerStmt->execute();

        // Check if excess amount is greater than zero before inserting credit details
        if ($excessAmount > 0) {
            // Insert credit details into the credits table
            $insertCreditQuery = "INSERT INTO credits (customerName, creditAmount, creditBalance) VALUES (:customerName, :creditAmount, :creditBalance)";
            
            // Calculate credit balance
            $creditBalance = $excessAmount;
        
            // Prepare and execute the SQL statement
            $insertCreditStmt = $db->prepare($insertCreditQuery);
            $insertCreditStmt->bindParam(':customerName', $customerName);
            $insertCreditStmt->bindParam(':creditAmount', $excessAmount);
            $insertCreditStmt->bindParam(':creditBalance', $creditBalance);
            $insertCreditStmt->execute();
        }

        // Commit the transaction
        $db->commit();
        echo "success";
    } catch (PDOException $e) {
        // Rollback the transaction in case of an error
        $db->rollback();
        error_log("PDOException: " . $e->getMessage()); // Log PDOException to PHP error log
        echo "Error: " . $e->getMessage();
    } catch (Exception $ex) {
        // Rollback the transaction in case of an error
        $db->rollback();
        error_log("Exception: " . $ex->getMessage()); // Log Exception to PHP error log
        echo "Error: " . $ex->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
