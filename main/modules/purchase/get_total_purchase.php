<?php
// Include your database connection file
include('../../connect.php');

try {
    // Fetch Purchase from the Purchase table
    $query = "SELECT * FROM purchase_order";
    $stmt = $db->query($query);
    $Purchase = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch the total count of Purchase
    $totalQuery = "SELECT COUNT(*) as totalPurchase FROM purchase_order";
    $totalStmt = $db->query($totalQuery);
    $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
    $totalPurchase = $totalResult['totalPurchase'];

    // Output Purchase and total count as JSON
    echo json_encode(['Purchase' => $Purchase, 'totalPurchase' => $totalPurchase]);
} catch (PDOException $e) {
    // Log the exception message to the error log
    error_log("PDOException: " . $e->getMessage());
    echo json_encode(['poID' => [], 'totalPurchase' => 0]); // Return an empty array in case of an error
}
?>