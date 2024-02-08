<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input data
    $paymentID = $_POST['editPaymentMethodID'];
    $paymentCode = $_POST['editPaymentMethodCode'];
    $paymentName = $_POST['editPaymentMethodName'];
    $paymentDescription = $_POST['editPaymentMethodDescription'];
    $activeStatus = isset($_POST['editActiveStatus']) ? 1 : 0;

    // Update category details in the database
    $query = "UPDATE payment_methods 
                SET payment_code = :paymentCode,
                payment_name = :paymentName, 
                payment_description = :paymentDescription,
                active_status = :activeStatus 
                WHERE payment_id = :paymentID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':paymentID', $paymentID);
    $stmt->bindParam(':paymentCode', $paymentCode);
    $stmt->bindParam(':paymentName', $paymentName);
    $stmt->bindParam(':paymentDescription', $paymentDescription);
    $stmt->bindParam(':activeStatus', $activeStatus);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error updating payment method: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
?>
