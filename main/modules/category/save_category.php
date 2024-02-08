<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input data
    $categoryCode = $_POST['categoryCode'];
    $categoryName = $_POST['categoryName'];
    $categoryDescription = $_POST['categoryDescription'];
    $activeStatus = isset($_POST['activeStatus']) ? 1 : 0;

    // Insert category details into the database
    $query = "INSERT INTO categories (category_code, category_name, category_description, active_status) VALUES (:categoryCode, :categoryName, :categoryDescription, :activeStatus)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':categoryCode', $categoryCode);
    $stmt->bindParam(':categoryName', $categoryName);
    $stmt->bindParam(':categoryDescription', $categoryDescription);
    $stmt->bindParam(':activeStatus', $activeStatus);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error saving category: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
?>
