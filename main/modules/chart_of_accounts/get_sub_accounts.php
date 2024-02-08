<?php
// Include your database connection file
include('../connect.php');

try {
    // Fetch sub-accounts from the charts_of_accounts table
    $stmt = $db->prepare('SELECT account_id, account_name FROM charts_of_accounts');
    $stmt->execute();
    $subAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Output as JSON
    echo json_encode($subAccounts);
} catch (PDOException $e) {
    error_log('Error: ' . $e->getMessage()); // Log the error
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['error' => 'Database error']);
    exit;
}
