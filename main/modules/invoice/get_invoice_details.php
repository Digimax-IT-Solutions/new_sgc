<?php
// Include the database connection script
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["invoiceID"])) {
    $invoiceID = $_GET["invoiceID"];

    // Your SQL query to retrieve invoice details based on $invoiceID

    // Example query:
    $query = "SELECT * FROM sales_invoice WHERE salesInvoiceID = :invoiceID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(":invoiceID", $invoiceID);
    $stmt->execute();

    $invoiceDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    // Return the details as JSON
    echo json_encode($invoiceDetails);
}
?>
