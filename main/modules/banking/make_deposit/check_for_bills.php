<?php
include('../../connect.php');
// Query to count rows in the "sales_invoice" table where invoiceStatus is "PAID"
try {
    $query = "SELECT COUNT(*) AS count FROM sales_invoice WHERE invoiceStatus = 'PAID'";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($result);
} catch (PDOException $e) {
    // Handle the exception, log the error, or return an error message
    echo json_encode(array('error' => 'Error fetching count of paid invoices.'));
}
?>
