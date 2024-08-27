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
            $stmt = $connection->prepare("INSERT INTO discount (discount_name, discount_rate, discount_description, discount_account_id) VALUES (?, ? ,? ,?)");

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

// Delete discount
if (get('action') === 'delete') {
    $id = get('id');
    $discount = Discount::find($id);

    if ($discount) {
        $discount->delete();
        flashMessage('delete_discount', 'Discount deleted successfully.', FLASH_SUCCESS);
    } else {
        flashMessage('delete_discount', 'Failed to delete discount.', FLASH_ERROR);
    }
    redirect('../../discount');
}

// Add discount
if (post('action') === 'add') {
    $discount_name = $_POST['discount_name'];
    $discount_rate = $_POST['discount_rate'];
    $discount_description = $_POST['discount_description'];
    $discount_account_id = $_POST['discount_account_id'];

    try {
        Discount::add($discount_name, $discount_rate, $discount_description, $discount_account_id);
        flashMessage('add_discount', 'Discount added successfully.', FLASH_SUCCESS);
    } catch (Exception $ex) {
        flashMessage('add_discount', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../../discount');
}

// Update discount
if (post('action') === 'update') {
    $id = $_POST['id'];
    $discount_name = $_POST['discount_name'];
    $discount_rate = $_POST['discount_rate'];
    $discount_description = $_POST['discount_description'];
    $discount_account_id = $_POST['discount_account_id'];

    try {
        $discount = Discount::find($id);
        if ($discount) {
            // Update properties
            $discount->discount_name = $discount_name;
            $discount->discount_rate = $discount_rate;
            $discount->discount_description = $discount_description;
            $discount->discount_account_id = $discount_account_id;

            // Save changes
            $discount->update();
            flashMessage('update_payment_method', 'Discount updated successfully.', FLASH_SUCCESS);
        } else {
            throw new Exception('Discount not found.');
        }
    } catch (Exception $ex) {
        flashMessage('update_payment_method', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../../discount');
}

// Redirect to the discount page if action is not recognized or implemented
redirect('../../discount');
