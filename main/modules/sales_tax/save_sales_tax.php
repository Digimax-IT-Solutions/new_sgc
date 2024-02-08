<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input data
    $salesTaxCode = $_POST['salesTaxCode'];
    $salesTaxName = $_POST['salesTaxName'];
    $salesTaxRate = $_POST['salesTaxRate'];
    $salesTaxDescription = $_POST['salesTaxDescription'];
    $activeStatus = isset($_POST['activeStatus']) ? 1 : 0;

    // Insert category details into the database
    $query = "INSERT INTO sales_tax (salesTaxCode, salesTaxName , salesTaxRate, salesTaxDescription, activeStatus) VALUES (:salesTaxCode, :salesTaxName, :salesTaxRate, :salesTaxDescription, :activeStatus)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':salesTaxCode', $salesTaxCode);
    $stmt->bindParam(':salesTaxName', $salesTaxName);
    $stmt->bindParam(':salesTaxRate', $salesTaxRate);
    $stmt->bindParam(':salesTaxDescription', $salesTaxDescription);
    $stmt->bindParam(':activeStatus', $activeStatus);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error saving sales tax: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
?>