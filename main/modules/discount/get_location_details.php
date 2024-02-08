<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['locationID'])) {
    // Sanitize input data
    $locationID = filter_var($_GET['locationID'], FILTER_SANITIZE_NUMBER_INT);

    // Fetch location details from the database
    $query = "SELECT * FROM locations WHERE location_id = :locationID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':locationID', $locationID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $locationDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return location details as JSON
        echo json_encode($locationDetails);
    } else {
        echo "Error fetching location details: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
