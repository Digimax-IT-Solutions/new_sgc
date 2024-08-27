<?php

require_once __DIR__ . '/../../_init.php';
require_once __DIR__ . '/../../vendor/autoload.php';

// Guard
Guard::adminOnly();

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
            $stmt = $connection->prepare("INSERT INTO other_name (other_name, other_name_code, account_number, other_name_address, contact_number, email, terms) VALUES (?, ? ,? ,?, ?, ?, ?)");

            // Begin transaction
            $connection->beginTransaction();

            $importedCount = 0;
            foreach ($rows as $row) {
                // Trim the row to the first 7 columns
                $row = array_slice($row, 0, 7);
            
                // Ensure all values are set, even if empty
                $row = array_pad($row, 7, null);
            
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



// Delete other name
if (get('action') === 'delete') {
    $id = get('id');
    $other_name = OtherNameList::find($id);

    if ($other_name) {
        $other_name->delete();
        flashMessage('delete_other_name', 'Other Name deleted successfully', FLASH_SUCCESS);
    } else {
        flashMessage('delete_other_name', 'Invalid other name', FLASH_ERROR);
    }
    redirect('../../other_name');
}

// Add other name
if (post('action') === 'add') {
    $other_name = post('other_name');
    $other_name_code = post('other_name_code');
    $account_number = post('account_number');
    $other_name_address = post('other_name_address');
    $contact_number = post('contact_number');
    $email = post('email');
    $terms = post('terms');

    try {
        OtherNameList::add($other_name, $other_name_code, $account_number, $other_name_address, $contact_number, $email, $terms);
        flashMessage('add_other_name', 'Other name added successfully.', FLASH_SUCCESS);
    } catch (Exception $ex) {
        flashMessage('add_other_name', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../../other_name');
}

// Update other name
if (post('action') === 'update') {
    $id = post('id');
    $other_name = OtherNameList::find($id);

    if ($other_name) {
        $other_name->other_name = post('other_name');
        $other_name->other_name_code = post('other_name_code');
        $other_name->account_number = post('account_number');
        $other_name->other_name_address = post('other_name_address');
        $other_name->contact_number = post('contact_number');
        $other_name->email = post('email');
        $other_name->terms = post('terms');

        try {
            $other_name->update();
            flashMessage('update_other_name', 'Other name updated successfully.', FLASH_SUCCESS);
        } catch (Exception $ex) {
            flashMessage('update_other_name', $ex->getMessage(), FLASH_ERROR);
        }
    } else {
        flashMessage('update_other_name', 'Invalid other name.', FLASH_ERROR);
    }

    redirect('../../other_name');
}
?>
