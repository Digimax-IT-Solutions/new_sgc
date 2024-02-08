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
        $poID = $_POST["poID"]; // Assuming you have a hidden input field for poID in your form
          // Retrieve and sanitize form data
          $poNo = $_POST["poNo"];
          $poID = isset($_POST["poID"]) ? $_POST["poID"] : null; // Add this line to define $poID
          // Add a validation check to ensure that the poNo doesn't already exist
          if ($poID !== null) {
              $checkPoNoQuery = "SELECT COUNT(*) FROM purchase_order WHERE poNo = :poNo AND poID != :poID";
              $checkPoNoStmt = $db->prepare($checkPoNoQuery);
              $checkPoNoStmt->bindParam(":poNo", $poNo);
              $checkPoNoStmt->bindParam(":poID", $poID);
              $checkPoNoStmt->execute();
  
              if ($checkPoNoStmt->fetchColumn() > 0) {
                  throw new Exception("Purchase Order with the same poNo already exists.");
              }
          }
        $poDate = $_POST["poDate"];
        $poDueDate = $_POST["poDueDate"];
        $vendor = $_POST["vendor"];
        $shippingAddress = $_POST["shippingAddress"];
        $email = $_POST["email"];
        $termID = $_POST["terms"];
        $locationID = $_POST["location"];
        $memo = $_POST["memo"];
        $paymentMethod = $_POST["paymentMethod"];
        $grossAmount = $_POST["grossAmount"];
        $discountPercentage = isset($_POST["discountPercentage"]) ? $_POST["discountPercentage"] : 0;
        $netAmountDue = $_POST["netAmountDue"];
        $vatPercentage = $_POST["vatPercentageAmount"];
        $netOfVat = $_POST["netOfVat"];
        $totalAmountDue = $_POST["totalAmountDue"];

        // Validate the discountPercentage value (you may add additional validation if needed)
        $discountPercentage = is_numeric($discountPercentage) ? $discountPercentage : 0;

        // Update purchase order data in the database
        $query = "UPDATE purchase_order SET
            poNo = :poNo,
            poDate = :poDate,
            poDueDate = :poDueDate,
            vendor = :vendor,
            shippingAddress = :shippingAddress,
            email = :email,
            terms = :terms,
            location = :location,
            paymentMethod = :paymentMethod,
            grossAmount = :grossAmount,
            discountPercentage = :discountPercentage,
            netAmountDue = :netAmountDue,
            vatPercentage = :vatPercentage,
            netOfVat = :netOfVat,
            memo = :memo,
            totalAmountDue = :totalAmountDue
            WHERE poID = :poID";

        $stmt = $db->prepare($query);
        $stmt->bindParam(":poID", $poID);
        $stmt->bindParam(":poNo", $poNo);
        $stmt->bindParam(":poDate", $poDate);
        $stmt->bindParam(":poDueDate", $poDueDate);
        $stmt->bindParam(":vendor", $vendor);
        $stmt->bindParam(":shippingAddress", $shippingAddress);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":terms", $termID);
        $stmt->bindParam(":location", $locationID);
        $stmt->bindParam(":paymentMethod", $paymentMethod);
        $stmt->bindParam(":grossAmount", $grossAmount);
        $stmt->bindParam(":discountPercentage", $discountPercentage);
        $stmt->bindParam(":netAmountDue", $netAmountDue);
        $stmt->bindParam(":vatPercentage", $vatPercentage);
        $stmt->bindParam(":netOfVat", $netOfVat);
        $stmt->bindParam(":memo", $memo);
        $stmt->bindParam(":totalAmountDue", $totalAmountDue);

        if (!$stmt->execute()) {
            throw new Exception("Error updating purchase order data.");
        }

        // Delete existing purchase order items
        $deleteItemsQuery = "DELETE FROM purchase_order_items WHERE poID = :poID";
        $deleteItemsStmt =   $db->prepare($deleteItemsQuery);
        $deleteItemsStmt->bindParam(":poID", $poID);

        if (!$deleteItemsStmt->execute()) {
            throw new Exception("Error deleting existing purchase order items.");
        }

        // Iterate over each item and insert into the database
        foreach ($_POST["item"] as $key => $item) {
            $description = $_POST["description"][$key];
            $quantity = $_POST["quantity"][$key];
            $uom = $_POST["uom"][$key];
            $rate = $_POST["rate"][$key];
            $amount = $_POST["amount"][$key];

            // Insert item data into the database
            $query = "INSERT INTO purchase_order_items (
                poID, 
                item, 
                description, 
                quantity, 
                uom,
                rate, 
                amount
            ) VALUES (
                :poID, 
                :item, 
                :description, 
                :quantity, 
                :uom,
                :rate, 
                :amount
            )";

            $stmt = $db->prepare($query);
            $stmt->bindParam(":poID", $poID);
            $stmt->bindParam(":item", $item);
            $stmt->bindParam(":description", $description);
            $stmt->bindParam(":quantity", $quantity);
            $stmt->bindParam(":uom", $uom);
            $stmt->bindParam(":rate", $rate);
            $stmt->bindParam(":amount", $amount);

            if (!$stmt->execute()) {
                throw new Exception("Error inserting purchase order item data.");
            }
        }
         // Log the purchase order update in the audit trail
         $logQuery = "INSERT INTO audit_trail (tableName, recordID, action, userID, details) VALUES (:tableName, :recordID, :action, :userID, :details)";
         $logStmt = $db->prepare($logQuery);
 
         $tableName = "purchase_order";
         $recordID = $poID; // Assuming $poID is available
         $action = "update";
         $userID = $_SESSION['SESS_MEMBER_ID'];
         $details = "Purchase Order updated: PO No. $poNo";
 
         $logStmt->bindParam(":tableName", $tableName);
         $logStmt->bindParam(":recordID", $recordID);
         $logStmt->bindParam(":action", $action);
         $logStmt->bindParam(":userID", $userID);
         $logStmt->bindParam(":details", $details);
 
         if (!$logStmt->execute()) {
             throw new Exception("Error logging purchase order update in the audit trail.");
         }

      // After the try-catch block
if ($db->commit()) {
    // The purchase order, its items are successfully updated in the database
    echo json_encode(["status" => "success", "message" => "Purchase Order updated!"]);
} else {
    throw new Exception("Error committing the transaction.");
}
    } catch (Exception $e) {
        // Rollback the transaction on exception
        $db->rollBack();

        echo json_encode(["status" => "error", "message" => "PO NO ALREADY EXIST!"]);
    }
}
?>
