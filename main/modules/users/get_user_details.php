<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id'])) {
    // Sanitize input data
    $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

    // Fetch location details from the database
    $query = "SELECT * FROM user WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $userDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return location details as JSON
        echo json_encode($userDetails);
    } else {
        echo "Error fetching user details: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
