<?php
// Include your database connection file
include('../../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deleteLocationID'])) {
    // Sanitize input data
    $locationID = filter_var($_POST['deleteLocationID'], FILTER_SANITIZE_NUMBER_INT);

    // Delete the location from the database
    $query = "DELETE FROM locations WHERE location_id = :locationID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':locationID', $locationID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error deleting location: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
