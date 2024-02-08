<?php
require_once('../../connect.php');

// Include your database connection file (You only need to include it once)
// include('../../connect.php');

try {
    // Fetch data from the database
    $query = "SELECT receiveID, poNo, location, terms, dueDate, account, vendor, receiveDate, refNo, totalAmount, memo, status, createdAt, updatedAt FROM received_items";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return data as JSON
    echo json_encode(['data' => $data]);

} catch (PDOException $e) {
    // Log the exception message to the error log
    error_log("PDOException: " . $e->getMessage());
    echo json_encode(['data' => []]); // Return an empty array in case of an error
}
?>
