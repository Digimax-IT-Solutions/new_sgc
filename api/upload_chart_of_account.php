<?php
require_once __DIR__ . '/../_init.php';

require_once __DIR__ . '/../vendor/autoload.php';

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

            $stmt = $connection->prepare("INSERT INTO chart_of_account (account_code, account_type_id, account_name, sub_account_id, account_description) VALUES (?,?,?,?,?)");
            $checkStmt = $connection->prepare("SELECT COUNT(*) FROM chart_of_account WHERE account_code = ?");

            $connection->beginTransaction();

            $importedCount = 0;
            $existingCodes = [];

            foreach ($rows as $row) {
                // Ensure all values are set, even if empty
                $row = array_pad($row, 5, null);

                // Check if account_code already exists
                $checkStmt->execute([$row[0]]);
                $exists = $checkStmt->fetchColumn();

                if ($exists) {
                    $existingCodes[] = $row[0];
                } else {
                    $stmt->execute($row);
                    $importedCount++;
                }
            }

            $connection->commit();

            $message = "Upload successful. $importedCount records imported.";
            if (!empty($existingCodes)) {
                $message .= " The following account codes already exist and were skipped: " . implode(', ', $existingCodes);
            }

            echo json_encode(['status' => 'success', 'message' => $message]);
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
