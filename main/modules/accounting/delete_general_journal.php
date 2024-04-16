<?php
include '../../connect.php';

// Check if the form is submitted using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $ID = $_POST['ID']; // Assuming you have this ID parameter in your form

    // Start a transaction
    try {
        $db->beginTransaction();

        // Update status to deactivate
        $stmt = $db->prepare("UPDATE general_journal SET status = 'inactive' WHERE ID = ?");
        $stmt->execute([$ID]);

        // Commit the transaction
        $db->commit();

        echo json_encode(['status' => 'success', 'message' => 'General Journal Deactivated']);
    } catch (PDOException $e) {
        // Rollback the transaction on error
        $db->rollBack();

        // Include MySQL error information in the response
        echo json_encode(['status' => 'error', 'message' => 'Error deactivating data: ' . $e->getMessage(), 'mysql_error' => $db->errorInfo()]);
    }
}
?>