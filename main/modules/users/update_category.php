<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input data
    $categoryID = $_POST['editCategoryID'];
    $categoryCode = $_POST['editCategoryCode'];
    $categoryName = $_POST['editCategoryName'];
    $categoryDescription = $_POST['editCategoryDescription'];
    $activeStatus = isset($_POST['editActiveStatus']) ? 1 : 0;

    // Update category details in the database
    $query = "UPDATE categories 
                SET category_code = :categoryCode,
                category_name = :categoryName, 
                category_description = :categoryDescription,
                active_status = :activeStatus 
                WHERE category_id = :categoryID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':categoryID', $categoryID);
    $stmt->bindParam(':categoryCode', $categoryCode);
    $stmt->bindParam(':categoryName', $categoryName);
    $stmt->bindParam(':categoryDescription', $categoryDescription);
    $stmt->bindParam(':activeStatus', $activeStatus);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error updating category: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
?>
