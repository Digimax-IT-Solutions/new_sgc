<?php
// Include your database connection file
include('../connect.php');

try {
    // Fetch vendors from the vendors table
    $query = "SELECT * FROM vendors";
    $stmt = $db->query($query);
    $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output vendors as JSON
    echo json_encode($vendors);
} catch (PDOException $e) {
    // Log the exception message to the error log
    error_log("PDOException: " . $e->getMessage());
    echo json_encode([]); // Return an empty array in case of an error
}
