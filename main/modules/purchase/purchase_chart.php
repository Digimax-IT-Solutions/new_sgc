<?php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = 'digimax2023';
$db_database = 'sales';

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define the purchase order statuses
$receivedStatus = 'RECEIVED';
$waitingStatus = 'WAITING FOR DELIVERY';

$data = [];

try {
    // Count the total number of RECEIVED rows
    $sqlReceived = "SELECT COUNT(*) as receivedCount FROM purchase_order WHERE poStatus = ?";
    $stmtReceived = $conn->prepare($sqlReceived);
    $stmtReceived->bind_param("s", $receivedStatus);
    $stmtReceived->execute();
    $resultReceived = $stmtReceived->get_result();

    if ($resultReceived->num_rows > 0) {
        $rowReceived = $resultReceived->fetch_assoc();
        $data['receivedCount'] = $rowReceived['receivedCount'];
    }

    // Count the total number of WAITING FOR DELIVERY rows
    $sqlWaiting = "SELECT COUNT(*) as waitingCount FROM purchase_order WHERE poStatus = ?";
    $stmtWaiting = $conn->prepare($sqlWaiting);
    $stmtWaiting->bind_param("s", $waitingStatus);
    $stmtWaiting->execute();
    $resultWaiting = $stmtWaiting->get_result();

    if ($resultWaiting->num_rows > 0) {
        $rowWaiting = $resultWaiting->fetch_assoc();
        $data['waitingCount'] = $rowWaiting['waitingCount'];
    }

    $stmtReceived->close();
    $stmtWaiting->close();
} catch (Exception $e) {
    // Handle exceptions, log errors, or output appropriate error messages.
    $data['error'] = $e->getMessage();
}

// Close the connection
$conn->close();

// Output data as JSON
echo json_encode($data);
?>
