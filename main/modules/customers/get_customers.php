<?php
// Include your database connection file
include('../connect.php');

try {
    // Fetch items from the items table
    $query = "SELECT * FROM customers";
    $stmt = $db->query($query);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output items as JSON
    echo json_encode($items);
} catch (PDOException $e) {
    // Log the exception message to the error log
    error_log("PDOException: " . $e->getMessage());
    echo json_encode([]); // Return an empty array in case of an error
}
