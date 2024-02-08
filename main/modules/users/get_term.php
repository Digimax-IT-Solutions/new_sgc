<?php
// Include your database connection file
include('../connect.php');

// Fetch location data from the database
$query = "SELECT * FROM terms";
$result = $db->query($query);

if ($result) {
    $terms = $result->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($terms);
} else {
    echo "Error fetching term data.";
}
