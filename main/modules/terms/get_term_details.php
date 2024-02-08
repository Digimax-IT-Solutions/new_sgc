<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['term_id'])) {
    // Sanitize input data
    $termID = filter_var($_GET['term_id'], FILTER_SANITIZE_NUMBER_INT);

    // Fetch location details from the database
    $query = "SELECT * FROM terms WHERE term_id = :termID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':termID', $termID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $termDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return location details as JSON
        echo json_encode($termDetails);
    } else {
        echo "Error fetching term details: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
