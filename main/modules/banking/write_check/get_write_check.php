<?php
// Include your database connection file
include('../../connect.php');

try {
    // Fetch sales transactions from the sales_invoice table with related information
    $query = "SELECT * FROM checks";

    $stmt = $db->query($query);
    $checksTransactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output sales transactions as JSON
    echo json_encode($checksTransactions);
} catch (PDOException $e) {
    // Log the exception message to the error log
    error_log("PDOException: " . $e->getMessage());
    echo json_encode([]); // Return an empty array in case of an error
}
?>