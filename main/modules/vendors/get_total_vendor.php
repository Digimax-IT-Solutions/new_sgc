<?php
// Include your database connection file
include('../connect.php');

try {
    // Fetch vendors from the vendors table
    $query = "SELECT * FROM vendors";
    $stmt = $db->query($query);
    $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch the total count of vendors
    $totalQuery = "SELECT COUNT(*) as totalVendors FROM vendors";
    $totalStmt = $db->query($totalQuery);
    $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
    $totalVendors = $totalResult['totalVendors'];

    // Output vendors and total count as JSON
    echo json_encode(['vendors' => $vendors, 'totalVendors' => $totalVendors]);
} catch (PDOException $e) {
    // Log the exception message to the error log
    error_log("PDOException: " . $e->getMessage());
    echo json_encode(['vendors' => [], 'totalVendors' => 0]); // Return an empty array in case of an error
}
?>
