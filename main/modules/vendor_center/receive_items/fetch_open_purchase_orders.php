<?php

include '../../../connect.php';

// Check if the 'vendor' parameter is set
if (isset($_POST['vendor'])) {
    // Sanitize the input to prevent SQL injection
    $selectedVendor = $_POST['vendor'];

    try {
        // Prepare a SQL statement to fetch purchase orders for the selected vendor
        $sql = "SELECT poNo, poDate, memo FROM purchase_order WHERE vendor = :vendor";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':vendor', $selectedVendor);
        $stmt->execute();

        // Fetch the results as an associative array
        $openPOs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Send the JSON response
        header('Content-Type: application/json');
        echo json_encode($openPOs);
    } catch (PDOException $e) {
        // Handle database errors
        echo json_encode(['error' => 'Database error']);
    }
} else {
    // If 'vendor' parameter is not set, return an empty array
    echo json_encode([]);
}