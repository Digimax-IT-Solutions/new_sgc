<?php
// Include the database connection file
include('../../connect.php');

try {
    // Query to fetch payment data from the database using PDO
    $query = "SELECT * FROM receive_payment WHERE depo = 'active'"; // Include the 'id' column in the query
    $stmt = $db->query($query);
    
    // Fetch all rows as associative array
    $paymentData = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Encode payment data as JSON and return it
    echo json_encode($paymentData);
} catch (PDOException $e) {
    // Handle database query error
    echo json_encode(array('error' => 'Failed to fetch payment data: ' . $e->getMessage()));
}
?>
