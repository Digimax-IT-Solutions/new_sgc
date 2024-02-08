<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input data
    $wtaxID = $_POST['editwtaxID'];
    $wtaxCode = $_POST['editwtaxCode'];
    $wtaxName = $_POST['editwtaxName'];
    $wtaxRate = $_POST['editwtaxRate'];
    $wtaxDescription = $_POST['editwtaxDescription'];
    $activeStatus = isset($_POST['editActiveStatus']) ? 1 : 0;

    // Update category details in the database
    $query = "UPDATE wtax 
                SET wTaxCode = :wtaxCode,
                wTaxName = :wtaxName, 
                wTaxRate = :wtaxRate,
                wTaxDescription = :wtaxDescription,
                activeStatus = :activeStatus 
                WHERE wtaxID = :wtaxID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':wtaxID', $wtaxID);
    $stmt->bindParam(':wtaxCode', $wtaxCode);
    $stmt->bindParam(':wtaxName', $wtaxName);
    $stmt->bindParam(':wtaxRate', $wtaxRate);
    $stmt->bindParam(':wtaxDescription', $wtaxDescription);
    $stmt->bindParam(':activeStatus', $activeStatus);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error updating wtax method: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
?>
