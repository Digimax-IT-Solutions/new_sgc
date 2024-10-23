<?php
require_once __DIR__ . '/../_init.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'POST') {
    $fromDate = $_REQUEST['from_date'] ?? null;
    $toDate = $_REQUEST['to_date'] ?? null;

    // Validate dates
    if ($fromDate && $toDate && strtotime($fromDate) > strtotime($toDate)) {
        echo "Error: 'From' date cannot be later than 'To' date.";
        exit;
    }

    // Display Profit and Loss statement
    ProfitAndLoss::displayProfitAndLoss($fromDate, $toDate);
} else {
    echo "Invalid request method.";
}
