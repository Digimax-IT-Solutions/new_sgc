<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input data
    $salesTaxID = $_POST['editsalesTaxID'];
    $salesTaxCode = $_POST['editsalesTaxCode'];
    $salesTaxName = $_POST['editsalesTaxName'];
    $salesTaxDescription = $_POST['editsalesTaxDescription'];
    $salesTaxRate = $_POST['editsalesTaxRate'];
    $activeStatus = isset($_POST['editActiveStatus']) ? 1 : 0;

    // Update salesTax details in the database
$query = "UPDATE sales_tax 
SET salesTaxCode = :salesTaxCode,
salesTaxName = :salesTaxName,
salesTaxRate = :salesTaxRate, 
salesTaxDescription = :salesTaxDescription,
activeStatus = :activeStatus 
WHERE salesTaxID = :salesTaxID";
$stmt = $db->prepare($query);
$stmt->bindParam(':salesTaxID', $salesTaxID);
$stmt->bindParam(':salesTaxCode', $salesTaxCode);
$stmt->bindParam(':salesTaxName', $salesTaxName);
$stmt->bindParam(':salesTaxRate', $salesTaxRate); // Corrected variable name
$stmt->bindParam(':salesTaxDescription', $salesTaxDescription);
$stmt->bindParam(':activeStatus', $activeStatus);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error updating salesTax: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
?>
