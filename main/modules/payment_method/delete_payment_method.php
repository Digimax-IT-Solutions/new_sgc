<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deletePaymentMethodID'])) {
    // Sanitize input data
    $paymentMethodID = filter_var($_POST['deletePaymentMethodID'], FILTER_SANITIZE_NUMBER_INT);

    // Delete the location from the database
    $query = "DELETE FROM payment_methods WHERE payment_id = :paymentMethodID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':paymentMethodID', $paymentMethodID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error deleting payment method: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}