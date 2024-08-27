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
            $stmt = $connection->prepare("INSERT INTO input_vat (input_vat_name, input_vat_rate, input_vat_description, input_vat_account_id) VALUES (?, ? ,? ,?)");

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

//Delete category
if (get('action') === 'delete') {
    $id = get('id');
    $payment_method = InputVat::find($id);

    if ($payment_method) {
        $payment_method->delete();
        flashMessage('delete', 'Input Vat deleted successfully', FLASH_SUCCESS);
    } else {
        flashMessage('delete', 'Invalid sales tax', FLASH_ERROR);
    }
    redirect('../../input_vat');
}


//Add category
if (post('action') === 'add') {

    $input_vat_name = $_POST['input_vat_name'];
    $input_vat_rate = $_POST['input_vat_rate'];
    $input_vat_description = $_POST['input_vat_description'];
    $input_vat_account_id = $_POST['input_vat_account_id'];

    try {
        InputVat::add($input_vat_name, $input_vat_rate, $input_vat_description, $input_vat_account_id);
        flashMessage('add', 'New Input Vat Added.', FLASH_SUCCESS);
    } catch (Exception $ex) {
        flashMessage('add', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../../input_vat');
}


// Update inputVat
if (post('action') === 'update') {
    $id = $_POST['id'];
    $input_vat_name = $_POST['input_vat_name'];
    $input_vat_rate = $_POST['input_vat_rate'];
    $input_vat_description = $_POST['input_vat_description'];
    $input_vat_account_id = $_POST['input_vat_account_id'];

    try {
        $input_vat = InputVat::find($id);
        if ($input_vat) {
            // Update properties
            $input_vat->input_vat_name = $input_vat_name;
            $input_vat->input_vat_rate = $input_vat_rate;
            $input_vat->input_vat_description = $input_vat_description;
            $input_vat->input_vat_account_id = $input_vat_account_id;

            // Save changes
            $input_vat->update();
            flashMessage('update', 'Input Vat updated successfully.', FLASH_SUCCESS);
        } else {
            throw new Exception('Input Vat not found.');
        }
    } catch (Exception $ex) {
        flashMessage('update', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../../input_vat');
}