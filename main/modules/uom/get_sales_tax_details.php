<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['paymentMethodID'])) {
    // Sanitize input data
    $paymentMethodID = filter_var($_GET['paymentMethodID'], FILTER_SANITIZE_NUMBER_INT);

    // Fetch location details from the database
    $query = "SELECT * FROM payment_methods WHERE payment_id = :paymentMethodID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':paymentMethodID', $paymentMethodID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $paymentMethodDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return location details as JSON
        echo json_encode($paymentMethodDetails);
    } else {
        echo "Error fetching payment method details: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
