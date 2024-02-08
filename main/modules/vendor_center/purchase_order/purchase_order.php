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
        // Retrieve and sanitize form data
        $poNo = $_POST["poNo"];
        // Add a validation check to ensure that the poNo doesn't already exist
        $checkPoNoQuery = "SELECT COUNT(*) FROM purchase_order WHERE poNo = :poNo";
        $checkPoNoStmt = $db->prepare($checkPoNoQuery);
        $checkPoNoStmt->bindParam(":poNo", $poNo);
        $checkPoNoStmt->execute();

        if ($checkPoNoStmt->fetchColumn() > 0) {
            throw new Exception("Purchase Order with the same poNo already exists.");
        }
        $poDate = $_POST["poDate"];
        $poDueDate = $_POST["poDueDate"];
        $vendor = $_POST["vendor"];
        $vendorID = $_POST["existingVendor"];
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
        $status = 'active';
        // Insert sales invoice data into the database
        $query = "INSERT INTO purchase_order (
            poNo, 
            poDate, 
            poDueDate, 
            vendor, 
            shippingAddress,
            email, 
            terms, 
            location, 
            paymentMethod, 
            grossAmount, 
            discountPercentage, 
            netAmountDue, 
            vatPercentage, 
            netOfVat, 
            memo,
            totalAmountDue, 
            status,
            created_at
        ) VALUES (
            :poNo, 
            :poDate, 
            :poDueDate, 
            :vendor, 
            :shippingAddress,
            :email, 
            :terms, 
            :location, 
            :paymentMethod, 
            :grossAmount, 
            :discountPercentage, 
            :netAmountDue, 
            :vatPercentage, 
            :netOfVat, 
            :memo,
            :totalAmountDue, 
            :status,
            CURRENT_TIMESTAMP
        )";

        $stmt = $db->prepare($query);
        $stmt->bindParam(":poNo", $poNo);
        $stmt->bindParam(":poDate", $poDate);
        $stmt->bindParam(":poDueDate", $poDueDate);
        $stmt->bindParam(":vendor", $vendor);  // <-- Replace :customerID with :customer
        $stmt->bindParam(":shippingAddress", $shippingAddress);
        $stmt->bindParam(":email", $email);

        $stmt->bindParam(":terms", $termID);         // <-- Replace :termID with :term
        $stmt->bindParam(":location", $locationID); // <-- Replace :locationID with :location
        $stmt->bindParam(":paymentMethod", $paymentMethod);
        $stmt->bindParam(":grossAmount", $grossAmount);
        $stmt->bindParam(":discountPercentage", $discountPercentage);
        $stmt->bindParam(":netAmountDue", $netAmountDue);
        $stmt->bindParam(":vatPercentage", $vatPercentage);
        $stmt->bindParam(":netOfVat", $netOfVat);

        $stmt->bindParam(":memo", $memo);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":totalAmountDue", $totalAmountDue);
        $stmt->bindParam(":status", $status);
        if ($stmt->execute()) {
            $poID = $db->lastInsertId(); // Get the ID of the inserted sales invoice

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
                    throw new Exception("Error inserting sales invoice item data.");
                }

                // // Update item quantity in the items table
                // $updateQuantityQuery = "UPDATE items SET itemQty = itemQty + :quantity WHERE itemName = :item";
                // $updateQuantityStmt = $db->prepare($updateQuantityQuery);
                // $updateQuantityStmt->bindParam(":quantity", $quantity);
                // $updateQuantityStmt->bindParam(":item", $item);

                // if (!$updateQuantityStmt->execute()) {
                //     throw new Exception("Error updating item quantity in the items table.");
                // }
            }

              // Log the purchase order creation in the audit trail
              $logQuery = "INSERT INTO audit_trail (tableName, recordID, action, userID, details) VALUES (:tableName, :recordID, :action, :userID, :details)";
              $logStmt = $db->prepare($logQuery);
  
              $tableName = "purchase_order";
              $recordID = $poID; // Assuming $poID is available
              $action = "create";
              $userID = $_SESSION['SESS_MEMBER_ID'];
              $details = "Purchase Order created: PO No. $poNo";
  
              $logStmt->bindParam(":tableName", $tableName);
              $logStmt->bindParam(":recordID", $recordID);
              $logStmt->bindParam(":action", $action);
              $logStmt->bindParam(":userID", $userID);
              $logStmt->bindParam(":details", $details);
  
              if (!$logStmt->execute()) {
                  throw new Exception("Error logging audit trail.");
              }
  
            // Commit the transaction
            $db->commit();

            // The sales invoice, its items, and item quantities are successfully updated in the database
            echo "Purchase Order Saved!";
        } else {
            // Handle the case where the insertion failed
            throw new Exception("Error inserting purchase order data.");
        }
        // Check if the vendor exists in the vendors table
        $checkVendorQuery = "SELECT vendorID FROM vendors WHERE vendorID = :vendorID";
        $checkVendorStmt = $db->prepare($checkVendorQuery);
        $checkVendorStmt->bindParam(":vendorID", $vendorID);

        if (!$checkVendorStmt->execute()) {
            throw new Exception("Error checking vendor existence.");
        }

        if ($checkVendorStmt->rowCount() == 0) {
            // Vendor doesn't exist, so insert vendor information into the vendors table
            $insertVendorQuery = "INSERT INTO vendors (vendorName, vendorAddress, vendorEmail) VALUES (:vendor, :shippingAddress, :email)";
            $insertVendorStmt = $db->prepare($insertVendorQuery);
            $insertVendorStmt->bindParam(":vendor", $vendor);
            $insertVendorStmt->bindParam(":shippingAddress", $shippingAddress);
            $insertVendorStmt->bindParam(":email", $email);

            if (!$insertVendorStmt->execute()) {
                throw new Exception("Error inserting vendor data.");
            }
        }
    } catch (Exception $e) {
        // Rollback the transaction on exception
        $db->rollBack();

        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
