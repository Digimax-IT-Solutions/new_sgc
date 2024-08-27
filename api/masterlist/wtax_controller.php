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

            // Prepare SQL statement
            $stmt = $connection->prepare("INSERT INTO wtax (wtax_name, wtax_rate, wtax_description, wtax_account_id) VALUES (?, ? ,? ,?)");

            // Begin transaction
            $connection->beginTransaction();

            $importedCount = 0;
            foreach ($rows as $row) {
                // Trim the row to the first 4 columns
                $row = array_slice($row, 0, 4);
            
                // Ensure all values are set, even if empty
                $row = array_pad($row, 4, null);
            
                // Trim whitespace and replace empty strings with null
                $row = array_map(function ($value) {
                    return trim($value) === '' ? null : trim($value);
                }, $row);
            
                // Check if the row is not entirely empty
                if (array_filter($row, function($value) { return $value !== null; })) {
                    if ($stmt->execute($row)) {
                        $importedCount++;
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'SQL Error: ' . print_r($stmt->errorInfo(), true)]);
                        exit; // Exit to avoid continuing on error
                    }
                }
            }
            
            // Commit transaction
            $connection->commit();
            echo json_encode(['status' => 'success', 'message' => "Upload successful. $importedCount records imported."]);
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

// Delete category
if (get('action') === 'delete') {
    $id = get('id');
    $wtax = WithholdingTax::find($id);

    if ($wtax) {
        $wtax->delete();
        flashMessage('delete', 'Withholding tax deleted successfully', FLASH_SUCCESS);
    } else {
        flashMessage('delete', 'Withholding sales tax not found.', FLASH_ERROR);
    }
    redirect('../../wtax');
}

// Add category
if (post('action') === 'add') {

    $wtax_name = $_POST['wtax_name'];
    $wtax_rate = $_POST['wtax_rate'];
    $wtax_description = $_POST['wtax_description'];
    $wtax_account_id = $_POST['wtax_account_id'];

    try {
        WithholdingTax::add($wtax_name, $wtax_rate, $wtax_description, $wtax_account_id);
        flashMessage('add', 'New Withholding Tax Added!', FLASH_SUCCESS);
    } catch (Exception $ex) {
        flashMessage('add', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../../wtax');
}

// Update category
if (post('action') === 'update') {
    $id = post('id');
    $wtax_name = post('wtax_name');
    $wtax_rate = post('wtax_rate');
    $wtax_description = post('wtax_description');
    $wtax_account_id = post('wtax_account_id');

    try {
        $wtax = WithholdingTax::find($id);
        $wtax->wtax_name = $wtax_name;
        $wtax->wtax_rate = $wtax_rate;
        $wtax->wtax_description = $wtax_description;
        $wtax->wtax_account_id = $wtax_account_id;
        $wtax->update();
        flashMessage('update', 'Withholding Tax updated successfully.', FLASH_SUCCESS);
    } catch (Exception $ex) {
        flashMessage('update', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../../wtax');
}
