<?php

include '../../../connect.php';

// Check if the 'poID' parameter is set
if (isset($_POST['poID'])) {
    // Sanitize the input to prevent SQL injection
    $selectedPoID = $_POST['poID'];

    try {
        // Prepare a SQL statement to fetch purchase order items for the selected purchase order
        $sql = "SELECT item, description, quantity, uom, rate, amount, poNo FROM purchase_order_items WHERE poID = :poID";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':poID', $selectedPoID);
        $stmt->execute();

        // Fetch the results as an associative array
        $purchaseOrderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Send the JSON response
        echo json_encode($purchaseOrderItems);
    } catch (PDOException $e) {
        // Handle database errors
        echo json_encode(['error' => 'Database error']);
    }
} else {
    // If 'poID' parameter is not set, return an empty array
    echo json_encode([]);
}

$db = null;
