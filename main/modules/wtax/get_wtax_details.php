<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['wtaxID'])) {
    // Sanitize input data
    $wtaxID = filter_var($_GET['wtaxID'], FILTER_SANITIZE_NUMBER_INT);

    // Fetch location details from the database
    $query = "SELECT * FROM wtax WHERE wtaxID = :wtaxID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':wtaxID', $wtaxID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $wtaxDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return location details as JSON
        echo json_encode($wtaxDetails);
    } else {
        echo "Error fetching payment method details: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
