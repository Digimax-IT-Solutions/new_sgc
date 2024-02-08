<?php
// Include your database connection file
include('../connect.php');

// Fetch location data from the database
$query = "SELECT * FROM categories";
$result = $db->query($query);

if ($result) {
    $categories = $result->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($categories);
} else {
    echo "Error fetching category data.";
}
