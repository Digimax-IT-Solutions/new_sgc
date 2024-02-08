<?php
// Include your database connection file
include('../connect.php');

// Fetch location data from the database
$query = "SELECT * FROM payment_methods";
$result = $db->query($query);

if ($result) {
    $payment_methods = $result->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($payment_methods);
} else {
    echo "Error fetching term data.";
}
