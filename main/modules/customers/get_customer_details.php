<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['customerID'])) {
    // Sanitize input data
    $customerID = filter_var($_GET['customerID'], FILTER_SANITIZE_NUMBER_INT);

    // Fetch location details from the database
    $query = "SELECT * FROM customers WHERE customerID = :customerID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':customerID', $customerID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $customerDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return location details as JSON
        echo json_encode($customerDetails);
    } else {
        echo "Error fetching customer details: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
