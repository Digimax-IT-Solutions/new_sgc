<?php

// Start session for user authentication
session_start();
// Include the database connection script
include('../../connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Start a transaction
        $db->beginTransaction();

        // Retrieve and sanitize form data
        $poID = $_POST["poID"];

        // Soft delete purchase order items
        $softDeleteItemsQuery = "UPDATE purchase_order_items SET status = 'deactivated' WHERE poID = :poID";
        $softDeleteItemsStmt = $db->prepare($softDeleteItemsQuery);
        $softDeleteItemsStmt->bindParam(":poID", $poID);

        if (!$softDeleteItemsStmt->execute()) {
            throw new Exception("Error deleting purchase order items.");
        }

        // Soft delete the purchase order
        $softDeletePoQuery = "UPDATE purchase_order SET status = 'deactivated' WHERE poID = :poID";
        $softDeletePoStmt = $db->prepare($softDeletePoQuery);
        $softDeletePoStmt->bindParam(":poID", $poID);

        if (!$softDeletePoStmt->execute()) {
            throw new Exception("Error deleting purchase order.");
        }

                // Log the purchase order soft delete in the audit trail
                $logQuery = "INSERT INTO audit_trail (tableName, recordID, action, userID, details) VALUES (:tableName, :recordID, :action, :userID, :details)";
                $logStmt = $db->prepare($logQuery);
        
                $tableName = "purchase_order";
                $recordID = $poID; // Assuming $poID is available
                $action = "delete";
                $userID = $_SESSION['SESS_MEMBER_ID'];
                $details = "Purchase Order deleted: PO ID $poID";
        
                $logStmt->bindParam(":tableName", $tableName);
                $logStmt->bindParam(":recordID", $recordID);
                $logStmt->bindParam(":action", $action);
                $logStmt->bindParam(":userID", $userID);
                $logStmt->bindParam(":details", $details);
        
                if (!$logStmt->execute()) {
                    throw new Exception("Error logging purchase order soft delete in the audit trail.");
                }
        // After the try-catch block
        if ($db->commit()) {
            // The purchase order and its items are successfully soft-deleted from the database
            echo json_encode(["status" => "success", "message" => "Purchase Order deleted!"]);
        } else {
            throw new Exception("Error committing the transaction.");
        }
    } catch (Exception $e) {
        // Rollback the transaction on exception
        $db->rollBack();

        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
?>
