<?php

// Include your database connection file
include '../../connect.php';

// Check if the form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Retrieve selected POs from the AJAX request
        $selectedPOs = $_POST['selectedPOs'];

        // Start a database transaction
        $db->beginTransaction();

        // Update 'purchase_order' table to mark the selected orders as 'RECEIVED'
        $updatePurchaseOrders = $db->prepare("UPDATE purchase_order SET poStatus = 'RECEIVED' WHERE poNo IN (" . implode(',', array_fill(0, count($selectedPOs), '?')) . ")");
        $updatePurchaseOrders->execute($selectedPOs);

        // Commit the transaction
        $db->commit();

        // Return a success response
        $response = ['status' => 'success', 'message' => 'Purchase orders updated successfully'];
        echo json_encode($response);
    } catch (Exception $e) {
        // An error occurred, rollback the transaction
        $db->rollBack();

        // Return an error response
        $response = ['status' => 'error', 'message' => 'Error updating purchase orders: ' . $e->getMessage()];
        echo json_encode($response);
    }
} else {
    // If the form is not submitted via POST, return an error response
    $response = ['status' => 'error', 'message' => 'Invalid request'];
    echo json_encode($response);
}
?>
