<?php
// Include your database connection file
include('../connect.php');

// Fetch location data from the database
$query = "SELECT * FROM wtax";
$result = $db->query($query);

if ($result) {
    $wTax = $result->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($wTax);
} else {
    echo "Error fetching sales tax data.";
}
