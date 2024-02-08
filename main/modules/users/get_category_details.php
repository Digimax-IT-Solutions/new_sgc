<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['categoryID'])) {
    // Sanitize input data
    $categoryID = filter_var($_GET['categoryID'], FILTER_SANITIZE_NUMBER_INT);

    // Fetch location details from the database
    $query = "SELECT * FROM categories WHERE category_id = :categoryID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':categoryID', $categoryID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $categoryDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return location details as JSON
        echo json_encode($categoryDetails);
    } else {
        echo "Error fetching category details: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
