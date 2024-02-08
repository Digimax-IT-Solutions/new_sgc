<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deleteVendorID'])) {
    // Sanitize input data
    $vendorID = filter_var($_POST['deleteVendorID'], FILTER_SANITIZE_NUMBER_INT);

    // Delete the location from the database
    $query = "DELETE FROM other_names WHERE otherNameID = :vendorID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':vendorID', $vendorID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error deleting vendor: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
