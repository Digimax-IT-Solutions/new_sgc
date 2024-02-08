<?php
// Include your database connection file
include('../../connect.php');

try {
    // Fetch customers from the customers table
    $query = "SELECT * FROM customers";
    $stmt = $db->query($query);
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch the total count of customers
    $totalQuery = "SELECT COUNT(*) as totalCustomers FROM customers";
    $totalStmt = $db->query($totalQuery);
    $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
    $totalCustomers = $totalResult['totalCustomers'];

    // Output customers and total count as JSON
    echo json_encode(['customers' => $customers, 'totalCustomers' => $totalCustomers]);
} catch (PDOException $e) {
    // Log the exception message to the error log
    error_log("PDOException: " . $e->getMessage());
    echo json_encode(['customers' => [], 'totalCustomers' => 0]); // Return an empty array in case of an error
}
?>
