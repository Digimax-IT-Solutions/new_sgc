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
    }

    // Output sales transactions as JSON
    echo json_encode($salesTransactions);
} catch (PDOException $e) {
    // Log the exception message to the error log
    error_log("PDOException: " . $e->getMessage());
    echo json_encode([]); // Return an empty array in case of an error
}
?>