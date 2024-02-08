<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deleteTermID'])) {
    // Sanitize input data
    $termID = filter_var($_POST['deleteTermID'], FILTER_SANITIZE_NUMBER_INT);

    // Delete the location from the database
    $query = "DELETE FROM terms WHERE term_id = :termID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':termID', $termID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error deleting term: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
