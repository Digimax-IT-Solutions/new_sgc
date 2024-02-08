<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deleteCategoryID'])) {
    // Sanitize input data
    $categoryID = filter_var($_POST['deleteCategoryID'], FILTER_SANITIZE_NUMBER_INT);

    // Delete the location from the database
    $query = "DELETE FROM categories WHERE category_id = :categoryID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':categoryID', $categoryID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error deleting category: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
