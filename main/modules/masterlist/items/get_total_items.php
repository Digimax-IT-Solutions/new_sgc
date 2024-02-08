<?php
// Include your database connection file
include('../../connect.php');

try {
    // Fetch items from the items table
    $query = "SELECT * FROM items";
    $stmt = $db->query($query);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch the total count of items
    $totalQuery = "SELECT COUNT(*) as total FROM items";
    $totalStmt = $db->query($totalQuery);
    $totalResult = $totalStmt->fetch(PDO::FETCH_ASSOC);
    $totalItems = $totalResult['total'];

    // Output items and total count as JSON
    echo json_encode(['items' => $items, 'totalItems' => $totalItems]);
} catch (PDOException $e) {
    // Log the exception message to the error log
    error_log("PDOException: " . $e->getMessage());
    echo json_encode(['items' => [], 'totalItems' => 0]); // Return an empty array in case of an error
}
?>
