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
            $stmt = $connection->prepare("INSERT INTO payment_method (paymer_method_name, description) VALUES (?, ?)");

            // Begin transaction
            $connection->beginTransaction();

            $importedCount = 0;
            foreach ($rows as $row) {
                // Trim the row to the first 2 columns
                $row = array_slice($row, 0, 2);
            
                // Ensure all values are set, even if empty
                $row = array_pad($row, 2, null);
            
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

// Delete payment method
if (get('action') === 'delete') {
    $id = get('id');
    $payment_method = PaymentMethod::find($id);

    if ($payment_method) {
        $payment_method->delete();
        flashMessage('delete_payment_method', 'Payment method deleted successfully', FLASH_SUCCESS);
    } else {
        flashMessage('delete_payment_method', 'Invalid payment method', FLASH_ERROR);
    }
    redirect('../../payment_method');
}

// Add payment method
if (post('action') === 'add') {
    $payment_method_name = $_POST['payment_method_name'];
    $description = $_POST['description'];

    try {
        PaymentMethod::add($payment_method_name, $description);
        flashMessage('add_payment_method', 'Payment method added successfully.', FLASH_SUCCESS);
    } catch (Exception $ex) {
        flashMessage('add_payment_method', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../../payment_method');
}

// Update payment method
if (post('action') === 'update') {
    $id = post('id');
    $payment_method_name = post('payment_method_name');
    $description = post('description');

    try {
        $payment_method = PaymentMethod::find($id);
        $payment_method->payment_method_name = $payment_method_name;
        $payment_method->description = $description;
        $payment_method->update();
        flashMessage('update_payment_method', 'Payment method updated successfully.', FLASH_SUCCESS);
        redirect('../../payment_method');
    } catch (Exception $ex) {
        flashMessage('update_payment_method', $ex->getMessage(), FLASH_ERROR);
        redirect("../../payment_method?action=update&id={$id}");
    }
}
