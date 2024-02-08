<?php

// Include your database connection file
include '../../connect.php';
header('Content-Type: application/json');


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        // Start a database transaction
        $db->beginTransaction();

        // Retrieve form data
        $bankAccountName = $_POST['bankAccountName'];
        $payeeName = $_POST['payeeName'];
        $address = $_POST['address'];
        $checkDate = $_POST['checkDate'];
        $referenceNo = $_POST['referenceNo']; // Fix the field name
        $memos = $_POST['memos'];
        $total_amount = $_POST['total_amount'];

        // Validate and sanitize your data as needed

        // Insert data into the 'checks' table
        $checkQuery = "INSERT INTO checks (
            bankAccountName, 
            payeeName, 
            address, 
            checkDate, 
            referenceNo, 
            memo,
            total_amount
        ) VALUES (
            :bankAccountName, 
            :payeeName, 
            :address, 
            :checkDate, 
            :referenceNo, 
            :memos,
            :total_amount
        )";

        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':bankAccountName', $bankAccountName);
        $checkStmt->bindParam(':payeeName', $payeeName);
        $checkStmt->bindParam(':address', $address);
        $checkStmt->bindParam(':checkDate', $checkDate);
        $checkStmt->bindParam(':referenceNo', $referenceNo);
        $checkStmt->bindParam(':memos', $memos);
        $checkStmt->bindParam(':total_amount', $total_amount);

        if ($checkStmt->execute()) {
            $checkID = $db->lastInsertId();

            // Iterate over each item and insert into the database
            foreach ($_POST["item"] as $key => $item) {
                $description = $_POST["description"][$key];
                $quantity = $_POST["quantity"][$key];
                $uom = $_POST["uom"][$key];
                $rate = $_POST["rate"][$key];
                $amount = $_POST["amount"][$key];

                // Insert item data into the database
                $itemQuery = "INSERT INTO check_items (
                    checkID, 
                    item, 
                    description, 
                    quantity, 
                    uom,
                    rate, 
                    amount
                ) VALUES (
                    :checkID, 
                    :item, 
                    :description, 
                    :quantity, 
                    :uom,
                    :rate, 
                    :amount
                )";

                $itemStmt = $db->prepare($itemQuery);
                $itemStmt->bindParam(":checkID", $checkID);
                $itemStmt->bindParam(":item", $item);
                $itemStmt->bindParam(":description", $description);
                $itemStmt->bindParam(":quantity", $quantity);
                $itemStmt->bindParam(":uom", $uom);
                $itemStmt->bindParam(":rate", $rate);
                $itemStmt->bindParam(":amount", $amount);

                if (!$itemStmt->execute()) {
                    throw new Exception("Error inserting check item data: " . implode(", ", $itemStmt->errorInfo()));
                }
            }

            // Handle expense data
            $accounts = $_POST["account"];
            $memo = $_POST["memo"];
            $amounts = $_POST["amount"];

            foreach ($accounts as $key => $account) {
                // Insert data into the 'check_expenses' table
                $expensesQuery = "INSERT INTO check_expenses (checkID, accountName, memo, amount) VALUES (:checkID, :account, :memo, :amount)";
                $expensesStmt = $db->prepare($expensesQuery);
                $expensesStmt->bindParam(":checkID", $checkID);
                $expensesStmt->bindParam(":account", $account);
                $expensesStmt->bindParam(":memo", $memo[$key]);
                $expensesStmt->bindParam(":amount", $amounts[$key]);

                if (!$expensesStmt->execute()) {
                    throw new Exception("Error inserting check expenses data: " . implode(", ", $expensesStmt->errorInfo()));
                }
            }

            // Commit the transaction
            $db->commit();
            echo "Check Saved!";
            // Return a success response
            $response = ['status' => 'success', 'message' => 'Check saved successfully', 'checkID' => $checkID];
            echo json_encode($response);
        } else {
            // Handle the case where the insertion into the checks table failed
            throw new Exception("Error inserting check data: " . implode(", ", $checkStmt->errorInfo()));
        }

    } catch (Exception $e) {
        // An error occurred, rollback the transaction
        $db->rollBack();

        // Return an error response
        $response = ['status' => 'error', 'message' => 'Error saving check: ' . $e->getMessage()];
        echo json_encode($response);
    }
} else {
    // If the form is not submitted via POST, return an error response
    $response = ['status' => 'error', 'message' => 'Invalid request'];
    echo json_encode($response);
}
?>
