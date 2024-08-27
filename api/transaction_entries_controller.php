<?php
// Include necessary files and initialize database connection
require_once __DIR__ . '/../_init.php';

// Handle filtering request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['from_date']) && isset($_POST['to_date'])) {
    // Retrieve the date range from the request
    $fromDate = $_POST['from_date'];
    $toDate = $_POST['to_date'];

    // Filter transaction entries based on the date range
    $filteredEntries = TransactionEntry::filterByDateRange($fromDate, $toDate);


    // Return JSON response
    header('Content-Type: application/json');
    echo json_encode($filteredEntries);
    exit;
}