<?php
// Include your database connection file
include('../connect.php');

// Check if the vendorID parameter is provided
if (isset($_GET['otherNameID'])) {
    $otherNameID = $_GET['otherNameID'];

    // Fetch vendor details from the database
    $query = "SELECT * FROM other_names WHERE otherNameID = :otherNameID";
    $statement = $db->prepare($query);
    $statement->bindParam(':otherNameID', $otherNameID, PDO::PARAM_INT);
    $statement->execute();

    // Fetch the vendor details as an associative array
    $vendorDetails = $statement->fetch(PDO::FETCH_ASSOC);

    // Convert the array to JSON and output it
    echo json_encode($vendorDetails);
} else {
    // If vendorID is not provided, return an error message
    echo json_encode(['error' => 'Vendor ID not provided']);
}
