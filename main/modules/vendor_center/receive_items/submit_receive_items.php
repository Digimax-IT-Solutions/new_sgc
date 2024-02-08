    <?php
// Start session for user authentication
session_start();
    // Include your database connection file
    include '../../connect.php';

    // Check if the form is submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Retrieve form data
            $vendor = $_POST['vendorSelect'];
            $chartOfAccount = $_POST['chartOfAccountSelect'];
            $pono = $_POST['poNo'];
            $location = $_POST['location'];
            $terms = $_POST['terms'];
            $receiveItemDueDate = $_POST['receiveItemDueDate'];
    
            $receiveDate = $_POST['receiveItemDate'];
            $refNo = $_POST['refNo'];
            $totalAmount = $_POST['totalAmountDue'];
            $memo = $_POST['memo'];

            // Validate and sanitize your data as needed
            $grossAmount = $_POST["grossAmount"];
            $discountPercentage = isset($_POST["discountPercentage"]) ? $_POST["discountPercentage"] : 0;
            $netAmountDue = $_POST["netAmountDue"];
            $vatPercentage = $_POST["vatPercentageAmount"];
            $netOfVat = $_POST["netOfVat"];
            $discountPercentage = is_numeric($discountPercentage) ? $discountPercentage : 0;
            $status = 'active';
            // Start a database transaction
            $db->beginTransaction();

            // Insert data into the 'received_items' table
            $insertReceiveItems = $db->prepare("INSERT INTO received_items (poNo, location, terms, dueDate, account, vendor, receiveDate, refNo, totalAmount, memo, grossAmount, discountPercentage, netAmountDue, vatPercentage, netOfVat, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $insertReceiveItems->execute([$pono, $location, $terms, $receiveItemDueDate, $chartOfAccount, $vendor, $receiveDate, $refNo, $totalAmount, $memo, $grossAmount, $discountPercentage, $netAmountDue, $vatPercentage, $netOfVat, $status]);

            // Retrieve the last inserted ID
            $receiveID = $db->lastInsertId();

            foreach ($_POST['items'] as $item) {
                // Ensure that each item has the expected keys
                if (isset($item['item'], $item['description'], $item['quantity'], $item['uom'], $item['rate'], $item['amount'], $item['poItemID'])) {
                    $poItemID = $item['poItemID'];
                    $quantity = $item['quantity'];
                    $itemDesc = $item['description'];
                    $uom = $item['uom'];
                    $rate = $item['rate'];
                    $amount = $item['amount'];

                    // Insert data into the 'received_items_details' table
                    $insertReceiveItemsDetails = $db->prepare("INSERT INTO received_items_details (receiveID, poItemID, quantity, item, description, uom, rate, amount, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    $insertReceiveItemsDetails->execute([$receiveID, $poItemID, $quantity, $item['item'], $itemDesc, $uom, $rate, $amount, 'RECEIVED']);

                    // You might want to update the 'purchase_order_items' table here to mark the items as 'RECEIVED'
                    // Update 'purchase_order' table to mark the entire order as 'RECEIVED'
                    $updatePurchaseOrder = $db->prepare("UPDATE purchase_order SET poStatus = 'RECEIVED' WHERE poNo = ?");
                    $updatePurchaseOrder->execute([$pono]);


                    // Update item quantity in the items table
                    $updateQuantityQuery = "UPDATE items SET itemQty = itemQty + :quantity WHERE itemName = :item";
                    $updateQuantityStmt = $db->prepare($updateQuantityQuery);
                    $updateQuantityStmt->bindParam(":quantity", $quantity, PDO::PARAM_INT);  // Assuming quantity is an integer
                    $updateQuantityStmt->bindParam(":item", $item['item'], PDO::PARAM_STR);  // Assuming item is a string

                    if (!$updateQuantityStmt->execute()) {
                        // Handle the error appropriately, you might want to log it or throw an exception
                        echo json_encode(['status' => 'error', 'message' => 'Error updating item quantity in the items table.']);
                        exit;
                    }

                    
                } else {
                    // Log an error or handle the case where item details are not set
                    echo json_encode(['status' => 'error', 'message' => 'Invalid item details']);
                    exit;
                }
            }
            // // Update 'purchase_order' table to mark the entire order as 'RECEIVED'
            // $updatePurchaseOrder = $db->prepare("UPDATE purchase_order SET poStatus = 'RECEIVED' WHERE vendor = ?");
            // $updatePurchaseOrder->execute([$vendor]);

            // Log the receive items action in the audit trail
        $logQuery = "INSERT INTO audit_trail (tableName, recordID, action, userID, details) VALUES (:tableName, :recordID, :action, :userID, :details)";
        $logStmt = $db->prepare($logQuery);

        $tableName = "received_items";
        $recordID = $receiveID; // Assuming $receiveID is available
        $action = "receive_items";
        $userID = $_SESSION['SESS_MEMBER_ID'];
        $details = "Receive items recorded for PO: $receiveID";

        $logStmt->bindParam(":tableName", $tableName);
        $logStmt->bindParam(":recordID", $recordID);
        $logStmt->bindParam(":action", $action);
        $logStmt->bindParam(":userID", $userID);
        $logStmt->bindParam(":details", $details);

        if (!$logStmt->execute()) {
            throw new Exception("Error logging receive items action in the audit trail.");
        }
            // Commit the transaction
            $db->commit();

            // Return a success response
            $response = ['status' => 'success', 'message' => 'Receive items saved successfully'];
            echo json_encode($response);
        } catch (Exception $e) {
            // An error occurred, rollback the transaction
            $db->rollBack();

            // Return an error response
            $response = ['status' => 'error', 'message' => 'Error saving receive items: ' . $e->getMessage()];
            echo json_encode($response);
        }
    } else {
        // If the form is not submitted via POST, return an error response
        $response = ['status' => 'error', 'message' => 'Invalid request'];
        echo json_encode($response);
    }