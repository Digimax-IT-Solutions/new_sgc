<?php
// Include your database connection file
include('../connect.php');

// Fetch location data from the database
$query = "SELECT * FROM sales_tax";
$result = $db->query($query);

if ($result) {
    $salesTax = $result->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($salesTax);
} else {
    echo "Error fetching sales tax data.";
}
