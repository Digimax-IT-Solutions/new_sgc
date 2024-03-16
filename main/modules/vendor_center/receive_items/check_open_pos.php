<?php
header('Content-Type: application/json');
include '../../../connect.php';

// Check if the vendor value is received
if (isset($_POST['vendor'])) {
    $selectedVendor = $_POST['vendor'];
} else {
    echo json_encode(['hasOpenPOs' => false, 'error' => 'Vendor not received in POST.']);
    exit();
}


$query = "SELECT DISTINCT 
            purchase_order.poID,
            purchase_order.poNo, 
            purchase_order.poDate, 
            purchase_order.poDueDate, 
            purchase_order.vendor,
            purchase_order.grossAmount, 
            purchase_order.discountPercentage, 
            purchase_order.netAmountDue,
            purchase_order.vatPercentage, 
            purchase_order.netOfVat, 
            purchase_order.memo,
            purchase_order.totalAmountDue,
            purchase_order.poStatus,
            purchase_order.status
          FROM purchase_order
          JOIN purchase_order_items ON purchase_order.poID = purchase_order_items.poID
          WHERE purchase_order.vendor = :vendor 
          AND UPPER(purchase_order.poStatus) NOT LIKE 'RECEIVED' 
          AND purchase_order.status = 'active'";


try {
    $stmt = $db->prepare($query);
    $stmt->bindParam(':vendor', $selectedVendor, PDO::PARAM_STR);
    $stmt->execute();

    $openPOs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['hasOpenPOs' => !empty($openPOs), 'openPOs' => $openPOs]);
} catch (PDOException $e) {
    echo json_encode(['hasOpenPOs' => false, 'error' => $e->getMessage()]);
}

$db = null;
?>