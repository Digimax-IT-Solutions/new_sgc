<?php
// Include your database connection file
include('../connect.php');

try {
    // Fetch sales transactions from the sales_invoice table with related information
    $query = "SELECT 
        invoiceID, 
        invoiceNo, 
        invoiceDate, 
        invoiceDueDate, 
        customer, 
        address, 
        email, 
        account, 
        terms, 
        location, 
        paymentMethod, 
        grossAmount, 
        discountPercentage, 
        netAmountDue, 
        vatPercentage, 
        netOfVat, 
        taxWithheldPercentage, 
        totalAmountDue, 
        invoiceStatus,
        created_at
    FROM sales_invoice WHERE status = 'active'";

    $stmt = $db->query($query);
    $salesTransactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the monetary columns
    foreach ($salesTransactions as &$transaction) {
        $columnsToFormat = ['grossAmount', 'discountPercentage', 'netAmountDue', 'vatPercentage', 'netOfVat', 'taxWithheldPercentage', 'totalAmountDue'];

        foreach ($columnsToFormat as $column) {
            $transaction["formatted_$column"] = '₱' . number_format($transaction[$column], 2);
        }

        // Check if the invoice is unpaid and due date has passed, then update status to "PAST DUE"
        $invoiceDueDate = $transaction['invoiceDueDate'];
        if ($transaction['invoiceStatus'] == 'UNPAID' && strtotime($invoiceDueDate) < strtotime(date('d-m-Y'))) {
            // Query to update invoice status to PAST DUE
            $updateQuery = "UPDATE sales_invoice SET invoiceStatus = 'PAST DUE' WHERE invoiceID = :invoiceID AND invoiceStatus = 'UNPAID' AND invoiceDueDate < CURDATE()";
            $stmtUpdate = $db->prepare($updateQuery);
            $stmtUpdate->bindParam(':invoiceID', $transaction['invoiceID']);
            $stmtUpdate->execute();
        }
    }

    // Output sales transactions as JSON
    echo json_encode($salesTransactions);
} catch (PDOException $e) {
    // Log the exception message to the error log
    error_log("PDOException: " . $e->getMessage());
    echo json_encode([]); // Return an empty array in case of an error
}
?>
