<?php
// Include your database connection file
include('../../connect.php');

try {
    // Fetch sales transactions from the sales_invoice table with related information
    $query = "SELECT 
        poID, 
        poNo, 
        poDate, 
        poDueDate, 
        vendor, 
        shippingAddress, 
        email, 
        terms, 
        location, 
        paymentMethod, 
        grossAmount, 
        discountPercentage, 
        netAmountDue, 
        vatPercentage, 
        netOfVat, 
        memo,
        poStatus,
        totalAmountDue, 
        poStatus,
        created_at
    FROM purchase_order WHERE status = 'active'";

    $stmt = $db->query($query);
    $salesTransactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format the monetary columns
    foreach ($salesTransactions as &$transaction) {
        $columnsToFormat = ['grossAmount', 'discountPercentage', 'netAmountDue', 'vatPercentage', 'netOfVat','totalAmountDue'];

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