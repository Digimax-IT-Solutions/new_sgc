<?php

require_once __DIR__ . '/../../_init.php';

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'upload') {
    header('Content-Type: application/json');

    if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] == 0) {
        $file_tmp = $_FILES['excel_file']['tmp_name'];

        try {
            // Load the spreadsheet and get the active sheet
            $spreadsheet = IOFactory::load($file_tmp);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove header row
            array_shift($rows);

            // Prepare SQL statements
            $checkStmt = $connection->prepare("SELECT COUNT(*) FROM fs_notes_classification WHERE name = ?");
            $insertStmt = $connection->prepare("INSERT INTO fs_notes_classification (name) VALUES (?)");

            // Begin transaction
            $connection->beginTransaction();

            $importedCount = 0;
            $skippedCount = 0;
            foreach ($rows as $row) {
                // Trim the row to the first 1 columns
                $row = array_slice($row, 0, 1);

                // Ensure all values are set, even if empty
                $row = array_pad($row, 1, null);

                // Trim whitespace and replace empty strings with null
                $row = array_map(function ($value) {
                    return trim($value) === '' ? null : trim($value);
                }, $row);

                // Check if the row is not entirely empty
                if (
                    array_filter($row, function ($value) {
                        return $value !== null;
                    })
                ) {
                    // Check if the name already exists
                    $checkStmt->execute([$row[0]]);
                    if ($checkStmt->fetchColumn() > 0) {
                        $skippedCount++;
                        continue; // Skip this row if the name exists
                    }

                    // Insert if the name does not exist
                    if ($insertStmt->execute($row)) {
                        $importedCount++;
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'SQL Error: ' . print_r($insertStmt->errorInfo(), true)]);
                        exit; // Exit to avoid continuing on error
                    }
                }
            }

            // Commit transaction
            $connection->commit();
            echo json_encode(['status' => 'success', 'message' => "Upload successful. $importedCount records imported, $skippedCount records skipped (already exist)."]);
        } catch (Exception $e) {
            $connection->rollBack();
            echo json_encode(['status' => 'error', 'message' => "Error: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => "No file uploaded or an error occurred."]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => "Invalid request method or action."]);
}


// Delete Uom
if (get('action') === 'delete') {
    $id = get('id');
    $uom = FsNotesClassification::find($id);

    if ($uom) {
        $uom->delete();
        flashMessage('delete_uom', 'Record has been deleted successfully', FLASH_SUCCESS);
    } else {
        flashMessage('delete_uom', 'Invalid record', FLASH_ERROR);
    }
    redirect('../../fs_notes_classification');
}

// Add Uom
if (post('action') === 'add') {
    $name = post('name');

    try {
        FsNotesClassification::add($name);
        flashMessage('add_fs_notes_classification', 'FS Note Classification added successfully.', FLASH_SUCCESS);
    } catch (Exception $ex) {
        flashMessage('add_fs_notes_classification', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../../fs_notes_classification');
}

// Update Uom
if (post('action') === 'update') {
    $name = post('name');
    $id = post('id');

    try {
        $uom = Uom::find($id);
        $uom->name = $name;
        $uom->update();
        flashMessage('update_uom', 'Uom updated successfully.', FLASH_SUCCESS);
        redirect('../../uom');
    } catch (Exception $ex) {
        flashMessage('update_uom', $ex->getMessage(), FLASH_ERROR);
        redirect("../../uom");
    }
}

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     if ($_FILES['excelFile']['error'] === UPLOAD_ERR_OK) {
//         $tmpFilePath = $_FILES['excelFile']['tmp_name'];
//         $excelData = readExcelData($tmpFilePath);

//         foreach ($excelData as $row) {
//             if (!Uom::findByName($row['name'])) {
//                 Uom::add($row['name']);
//             }
//         }

//         redirect('../../admin_uom.php');
//     } else {
//         redirect('../../admin_uom.php');
//     }
// }

// function readExcelData($filePath)
// {
//     $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
//     $spreadsheet = $reader->load($filePath);
//     $worksheet = $spreadsheet->getActiveSheet();
//     $highestRow = $worksheet->getHighestRow();
//     $highestColumn = $worksheet->getHighestColumn();
//     $data = [];

//     for ($row = 2; $row <= $highestRow; ++$row) {
//         $rowData = [];
//         for ($col = 'A'; $col <= $highestColumn; ++$col) {
//             $cellValue = $worksheet->getCell($col . $row)->getValue();
//             $rowData[] = $cellValue;
//         }
//         $data[] = [
//             'name' => $rowData[0],
//         ];
//     }

//     return $data;
// }
