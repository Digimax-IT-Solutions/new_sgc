<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['uomID'])) {
    // Sanitize input data
    $uomID = filter_var($_GET['uomID'], FILTER_SANITIZE_NUMBER_INT);

    // Fetch location details from the database
    $query = "SELECT * FROM uom WHERE uomID = :uomID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':uomID', $uomID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $uomDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return location details as JSON
        echo json_encode($uomDetails);
    } else {
        echo "Error fetching payment method details: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
