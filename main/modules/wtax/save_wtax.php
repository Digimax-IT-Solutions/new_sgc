<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input data
    $wTaxCode = $_POST['wTaxCode'];
    $wTaxName = $_POST['wTaxName'];
    $wTaxRate = $_POST['wTaxRate'];
    $wTaxDescription = $_POST['wTaxDescription'];
    $activeStatus = isset($_POST['activeStatus']) ? 1 : 0;

    // Insert category details into the database
    $query = "INSERT INTO wtax (wTaxCode, wTaxName , wTaxRate, wTaxDescription, activeStatus) VALUES (:wTaxCode, :wTaxName, :wTaxRate, :wTaxDescription, :activeStatus)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':wTaxCode', $wTaxCode);
    $stmt->bindParam(':wTaxName', $wTaxName);
    $stmt->bindParam(':wTaxRate', $wTaxRate);
    $stmt->bindParam(':wTaxDescription', $wTaxDescription);
    $stmt->bindParam(':activeStatus', $activeStatus);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error saving wtax: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
?>