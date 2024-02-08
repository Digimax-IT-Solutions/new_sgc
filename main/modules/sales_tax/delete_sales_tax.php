<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deleteSalesTaxID'])) {
    // Sanitize input data
    $salesTaxID = filter_var($_POST['deleteSalesTaxID'], FILTER_SANITIZE_NUMBER_INT);

    // Delete the location from the database
    $query = "DELETE FROM sales_tax WHERE salesTaxID = :salesTaxID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':salesTaxID', $salesTaxID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error deleting term: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
