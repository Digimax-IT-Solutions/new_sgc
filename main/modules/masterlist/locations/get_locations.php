<?php
// Include your database connection file
include('../../connect.php');

// Fetch location data from the database
$query = "SELECT * FROM locations";
$result = $db->query($query);

if ($result) {
    $locations = $result->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($locations);
} else {
    echo "Error fetching location data.";
}
