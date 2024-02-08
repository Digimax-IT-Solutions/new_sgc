<?php
// Include your database connection file
include('../connect.php');

// Fetch location data from the database
$query = "SELECT * FROM uom";
$result = $db->query($query);

if ($result) {
    $uom = $result->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($uom);
} else {
    echo "Error fetching uom data.";
}