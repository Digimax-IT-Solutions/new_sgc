<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deletewtaxID'])) {
    // Sanitize input data
    $wtaxID = filter_var($_POST['deletewtaxID'], FILTER_SANITIZE_NUMBER_INT);

    // Delete the location from the database
    $query = "DELETE FROM wtax WHERE wtaxID = :wtaxID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':wtaxID', $wtaxID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error deleting term: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
