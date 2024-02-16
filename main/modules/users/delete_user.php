<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deleteid'])) {
    // Sanitize input data
    $id = filter_var($_POST['deleteid'], FILTER_SANITIZE_NUMBER_INT);

    // Delete the location from the database
    $query = "DELETE FROM user WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error deleting user " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
