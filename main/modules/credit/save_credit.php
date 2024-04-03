<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
        // Begin a transaction
        $db->beginTransaction();

        // Validate and sanitize input data
        $creditID = $_POST['creditID'];
        $customerName = $_POST['customerName'];
        $creditAmount = $_POST['creditAmount'];
        $creditDate = $_POST['creditDate'];
        $creditBalance = $_POST['creditBalance'];
        $memo = $_POST['memo'];
        $poID = $_POST['poID'];

        // Insert category details into the database
        $query = "INSERT INTO credits (creditID, customerName , creditAmount, creditDate, creditBalance, memo, poID) VALUES (:creditID, :customerName, :creditAmount, :creditDate, :creditBalance, :memo, :poID)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':creditID', $creditID);
        $stmt->bindParam(':customerName', $customerName);
        $stmt->bindParam(':creditAmount', $creditAmount);
        $stmt->bindParam(':creditDate', $creditDate);
        $stmt->bindParam(':creditBalance', $creditBalance);
        $stmt->bindParam(':memo', $memo);
        $stmt->bindParam(':poID', $poID);

        if ($stmt->execute()) {
            $creditID = $db->lastInsertId();

            // Iterate over each item and insert into the database
            foreach ($_POST["item"] as $key => $item) {
                $description = $_POST["description"][$key];
                $quantity = $_POST["quantity"][$key];
                $uom = $_POST["uom"][$key];
                $rate = $_POST["rate"][$key];
                $amount = $_POST["amount"][$key];

                // Insert item data into the database
                $itemQuery = "INSERT INTO credit_items (
                    creditID, 
                    item, 
                    description, 
                    quantity, 
                    uom,
                    rate, 
                    amount
                ) VALUES (
                    :creditID, 
                    :item, 
                    :description, 
                    :quantity, 
                    :uom,
                    :rate, 
                    :amount
                )";

                $itemStmt = $db->prepare($itemQuery);
                $itemStmt->bindParam(":creditID", $creditID);
                $itemStmt->bindParam(":item", $item);
                $itemStmt->bindParam(":description", $description);
                $itemStmt->bindParam(":quantity", $quantity);
                $itemStmt->bindParam(":uom", $uom);
                $itemStmt->bindParam(":rate", $rate);
                $itemStmt->bindParam(":amount", $amount);

                if (!$itemStmt->execute()) {
                    throw new Exception("Error inserting credit item data: " . implode(", ", $itemStmt->errorInfo()));
                }
            }

            // Commit the transaction
            $db->commit();
            echo "Credit Saved!";
            // Return a success response
        } else {
            // Handle the case where the insertion into the credits table failed
            throw new Exception("Error inserting credit data: " . implode(", ", $stmt->errorInfo()));
        }
    } catch (Exception $e) {
        // Rollback the transaction in case of an error
        $db->rollBack();
        // Handle exceptions
        echo "Error: " . $e->getMessage();
    }
}
?>
