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

            $stmt = $connection->prepare("INSERT INTO audit_trail (transaction_type, transaction_date, ref_no, location, name, item, qty_sold, qty_purch, ave_cost, cost, sell_price, cogs_sold, amt_sold, account_id, debit, credit, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            $connection->beginTransaction();

            // foreach ($rows as $row) {
            //     // Ensure all values are set, even if empty
            //     $row = array_pad($row, 17, null);

            //     // Convert the date format
            //     if (!empty($row[1])) {
            //         $date = DateTime::createFromFormat('n/j/Y', $row[1]);
            //         if ($date) {
            //             $row[1] = $date->format('Y-m-d');
            //         } else {
            //             throw new Exception("Invalid date format in row: " . implode(', ', $row));
            //         }
            //     }

            //     $stmt->execute($row);
            // }

            // //

            foreach ($rows as $row) {
                // Ensure all values are set, even if empty
                $row = array_pad($row, 17, null);

                // Convert the date format
                if (!empty($row[1])) {
                    $date = DateTime::createFromFormat('n/j/Y', $row[1]);
                    if ($date) {
                        $row[1] = $date->format('Y-m-d');
                    } else {
                        throw new Exception("Invalid date format in row: " . implode(', ', $row));
                    }
                }

                // Bind parameters explicitly
                $stmt->bindParam(1, $row[0]);
                $stmt->bindParam(2, $row[1]);
                $stmt->bindParam(3, $row[2]);
                $stmt->bindParam(4, $row[3]);
                $stmt->bindParam(5, $row[4]);
                $stmt->bindParam(6, $row[5]);
                $stmt->bindParam(7, $row[6]);
                $stmt->bindParam(8, $row[7]);
                $stmt->bindParam(9, $row[8]);
                $stmt->bindParam(10, $row[9]);
                $stmt->bindParam(11, $row[10]);
                $stmt->bindParam(12, $row[11]);
                $stmt->bindParam(13, $row[12]);
                $stmt->bindParam(14, $row[13]);
                $stmt->bindParam(15, $row[14]);
                $stmt->bindParam(16, $row[15]);
                $stmt->bindParam(17, $row[16]);

                $stmt->execute();
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
