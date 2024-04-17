<?php
include('../../connect.php');

try {
    $query = "SELECT receivedDate, RefNo, paymentType, customerName, SUM(payment_amount) AS payment_amount, ar_account FROM receive_payment WHERE depo = 'active' GROUP BY RefNo";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(array('error' => 'Error fetching payment data.'));
}
?>
