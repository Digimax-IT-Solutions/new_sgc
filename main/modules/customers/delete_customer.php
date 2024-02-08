<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deleteCustomerID'])) {
    // Sanitize input data
    $customerID = filter_var($_POST['deleteCustomerID'], FILTER_SANITIZE_NUMBER_INT);

    // Delete the location from the database
    $query = "DELETE FROM customers WHERE customerID = :customerID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':customerID', $customerID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error deleting location: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
