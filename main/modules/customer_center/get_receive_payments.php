<?php
require_once('../connect.php');

try {
    // Fetch data from the database
    $query = "SELECT rp.RefNo, rp.invoiceNo, rp.receivedDate, rp.ar_account, rp.customerName, si.totalAmountDue, rp.payment_amount, rp.discCredapplied, rp.paymentType
              FROM receive_payment rp
              JOIN sales_invoice si ON rp.invoiceNo = si.invoiceNo";
              
    $stmt = $db->prepare($query);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return data as JSON
    echo json_encode($data);

} catch (PDOException $e) {
    // Log the exception message to the error log
    error_log("PDOException: " . $e->getMessage());
    // Return an empty array in case of an error
    echo json_encode([]);
}
?>
