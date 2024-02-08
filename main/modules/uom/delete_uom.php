<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deleteuomID'])) {
    // Sanitize input data
    $uomID = filter_var($_POST['deleteuomID'], FILTER_SANITIZE_NUMBER_INT);

    // Delete the location from the database
    $query = "DELETE FROM uom WHERE uomID = :uomID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':uomID', $uomID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error deleting term: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
