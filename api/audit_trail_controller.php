<?php
require_once __DIR__ . '/../_init.php';
require __DIR__ . '/../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] == 0) {
        $file_name = $_FILES['excel_file']['name'];
        $file_tmp = $_FILES['excel_file']['tmp_name'];

        try {
            $spreadsheet = IOFactory::load($file_tmp);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove header row
            array_shift($rows);

            $stmt = $connection->prepare("INSERT INTO audit_trail (transaction_type, transaction_date, ref_no, name, item, qty_sold, qty_purch, ave_cost, cost, sell_price, cogs_sold, amt_sold, account_id, debit, credit, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $connection->beginTransaction();

            foreach ($rows as $row) {
                // Ensure all values are set, even if empty
                $row = array_pad($row, 16, null);
                $stmt->execute($row);
            }

            $connection->commit();
            echo json_encode(['status' => 'success', 'message' => "Upload successful. " . count($rows) . " records imported."]);
        } catch (Exception $e) {
            $connection->rollBack();
            echo json_encode(['status' => 'error', 'message' => "Error: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => "No file uploaded or an error occurred."]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => "Invalid request method."]);
}