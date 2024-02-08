<?php
// Include your database connection file
include('../../connect.php');

// Check if itemID is set and is a valid number
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deleteItemID'])) {

    // Sanitize input data
    $itemID = filter_var($_POST['deleteItemID'], FILTER_SANITIZE_NUMBER_INT);

    // Perform the deletion
    $query = "DELETE FROM items WHERE itemID = :itemID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':itemID', $itemID, PDO::PARAM_INT);
    // Execute the query
    if ($stmt->execute()) {
        // Return success message
        echo "success";
    } else {
        // Return error message
        echo "Error deleting item. Please try again.";
    }
} else {
    // Return error message if itemID is not set or invalid
    echo "Invalid itemID";
}
