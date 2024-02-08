<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input data
    $paymentCode = $_POST['paymentCode'];
    $paymentName = $_POST['paymentName'];
    $paymentDescription = $_POST['paymentDescription'];
    $activeStatus = isset($_POST['activeStatus']) ? 1 : 0;

    // Insert category details into the database
    $query = "INSERT INTO payment_methods (payment_code, payment_name, payment_description, active_status) VALUES (:paymentCode, :paymentName, :paymentDescription, :activeStatus)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':paymentCode', $paymentCode);
    $stmt->bindParam(':paymentName', $paymentName);
    $stmt->bindParam(':paymentDescription', $paymentDescription);
    $stmt->bindParam(':activeStatus', $activeStatus);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error saving method: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
?>