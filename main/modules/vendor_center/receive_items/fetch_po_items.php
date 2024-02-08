<?php
header('Content-Type: application/json');
include '../../../connect.php';

// Check if the poID value is received
if (isset($_POST['poID'])) {
    $selectedPoID = $_POST['poID'];

    $query = "SELECT item, description, quantity, uom, rate, amount FROM purchase_order_items WHERE poID = :poID";

    try {
        $stmt = $db->prepare($query);
        $stmt->bindParam(':poID', $selectedPoID, PDO::PARAM_INT);
        $stmt->execute();

        $poItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($poItems);
    } catch (PDOException $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'PO ID not received in POST.']);
}

$db = null;
?>
