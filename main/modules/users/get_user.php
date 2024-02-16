<?php
// Include your database connection file
include('../connect.php');

// Fetch location data from the database
$query = "SELECT * FROM user";
$result = $db->query($query);

if ($result) {
    $users = $result->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
} else {
    echo "Error fetching term data.";
}
