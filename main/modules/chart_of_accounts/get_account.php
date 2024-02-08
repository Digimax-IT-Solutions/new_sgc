<?php
// Include your database connection file
include('../connect.php');

try {
    // Fetch items from the items table
    $query = "SELECT * FROM chart_of_accounts";
    $stmt = $db->query($query);
    $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output items as JSON
    echo json_encode($accounts);
} catch (PDOException $e) {
    // Log the exception message to the error log
    error_log("PDOException: " . $e->getMessage());
    echo json_encode([]); // Return an empty array in case of an error
}
