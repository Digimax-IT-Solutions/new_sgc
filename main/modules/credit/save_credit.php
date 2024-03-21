<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    try {
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
            echo "success";
        } else {
            echo "Error saving credit.";
        }
    } catch (PDOException $e) {
        // Handle MySQL PDOException
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>
