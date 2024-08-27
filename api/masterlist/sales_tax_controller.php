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
            $stmt = $connection->prepare("INSERT INTO sales_tax (sales_tax_name, sales_tax_rate, sales_tax_description, sales_tax_account_id) VALUES (?, ?, ?, ?)");

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
    try {
        $sales_tax = SalesTax::find($id);

        if ($sales_tax) {
            $sales_tax->delete();
            flashMessage('delete', 'Sales tax deleted successfully', FLASH_SUCCESS);
        } else {
            flashMessage('delete', 'Sales tax not found', FLASH_ERROR);
        }
    } catch (Exception $e) {
        flashMessage('delete', 'Error deleting sales tax: ' . $e->getMessage(), FLASH_ERROR);
    }
    redirect('../../sales_tax');
}

//Add category
if (post('action') === 'add') {

    $sales_tax_name = post('sales_tax_name');
    $sales_tax_rate = post('sales_tax_rate');
    $sales_tax_description = post('sales_tax_description');
    $sales_tax_account_id = post('sales_tax_account_id');

    try {
        SalesTax::add($sales_tax_name, $sales_tax_rate, $sales_tax_description, $sales_tax_account_id);
        flashMessage('add', 'New Sales Tax Added!.', FLASH_SUCCESS);
    } catch (Exception $ex) {
        flashMessage('add', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../../sales_tax');
}

    // Update sales_tax
    if (post('action') === 'update') {
        $id = $_POST['id'];
        $sales_tax_name = $_POST['sales_tax_name'];
        $sales_tax_rate = $_POST['sales_tax_rate'];
        $sales_tax_description = $_POST['sales_tax_description'];
        $sales_tax_account_id = $_POST['sales_tax_account_id'];

        try {
            $sales_tax = SalesTax::find($id);
            if ($sales_tax) {
                // Update properties
                $sales_tax->sales_tax_name = $sales_tax_name;
                $sales_tax->sales_tax_rate = $sales_tax_rate;
                $sales_tax->sales_tax_description = $sales_tax_description;
                $sales_tax->sales_tax_account_id = $sales_tax_account_id;

                // Save changes
                $sales_tax->update();
                flashMessage('update', 'Sales tax updated successfully.', FLASH_SUCCESS);
            } else {
                throw new Exception('Sales tax not found.');
            }
        } catch (Exception $ex) {
            flashMessage('update', $ex->getMessage(), FLASH_ERROR);
        }

        redirect('../../sales_tax');
    }

    // Redirect to the sales tax page if action is not recognized or implemented
    redirect('../../sales_tax');