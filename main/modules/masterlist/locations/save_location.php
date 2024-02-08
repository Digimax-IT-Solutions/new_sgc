<?php
// Include your database connection file
include('../../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input data
    $locationCode = $_POST['locationCode'];
    $locationName = $_POST['locationName'];
    $activeStatus = isset($_POST['activeStatus']) ? 1 : 0;

    // Insert location details into the database
    $query = "INSERT INTO locations (location_code, location_name, active_status) VALUES (:locationCode, :locationName, :activeStatus)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':locationCode', $locationCode);
    $stmt->bindParam(':locationName', $locationName);
    $stmt->bindParam(':activeStatus', $activeStatus);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error saving location: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
