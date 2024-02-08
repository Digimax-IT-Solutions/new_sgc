<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['salesTaxID'])) {
    // Sanitize input data
    $salesTaxID = filter_var($_GET['salesTaxID'], FILTER_SANITIZE_NUMBER_INT);

    // Fetch location details from the database
    $query = "SELECT * FROM sales_tax WHERE salesTaxID = :salesTaxID ";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':salesTaxID', $salesTaxID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $stax = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if a result was found
        if ($stax !== false) {
            // Return location details as JSON
            echo json_encode($stax);
        } else {
            echo "Sales tax not found.";
        }
    } else {
        echo "Error fetching sales tax details: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}

