<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input data
    $locationID = $_POST['editLocationID'];
    $locationCode = $_POST['editLocationCode'];
    $locationName = $_POST['editLocationName'];
    $activeStatus = isset($_POST['editActiveStatus']) ? 1 : 0;

    // Update location details in the database
    $query = "UPDATE locations 
                SET location_code = :locationCode,
                location_name = :locationName, 
                active_status = :activeStatus 
                WHERE location_id = :locationID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':locationID', $locationID);
    $stmt->bindParam(':locationCode', $locationCode);
    $stmt->bindParam(':locationName', $locationName);
    $stmt->bindParam(':activeStatus', $activeStatus);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error updating location: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
