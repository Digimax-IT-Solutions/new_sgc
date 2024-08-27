<?php

require_once __DIR__ . '/../../_init.php';

require __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'upload') {
    header('Content-Type: application/json');
    
    if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] == 0) {
        $file_tmp = $_FILES['excel_file']['tmp_name'];

        try {
            $spreadsheet = IOFactory::load($file_tmp);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove header row
            array_shift($rows);

            $stmt = $connection->prepare("INSERT INTO chart_of_account (account_code, account_type_id, gl_code, gl_name, sl_code, sl_name, account_description) VALUES (?,?,?,?,?,?,?)");
            
            // $checkStmt = $connection->prepare("SELECT COUNT(*) FROM chart_of_account WHERE account_code = ?");

            $connection->beginTransaction();

            // $importedCount = 0;
            // $existingCodes = [];

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

//Delete category
// Delete chart of account
if (get('action') === 'delete') {
    $id = get('id');
    $account = ChartOfAccount::findById($id);

    if ($account) {
        $account->delete();
        flashMessage('delete', 'Chart of account deleted', FLASH_SUCCESS);
    } else {
        flashMessage('delete', 'Invalid chart of account', FLASH_ERROR);
    }
    redirect('../../chart_of_accounts');
}

// Add account to chart of accounts
if (post('action') === 'add') {

    $account_code = post('account_code');
    $account_type_id = post('account_type_id');
    $gl_code = post('gl_code');
    $gl_name = post('gl_name');
    $sl_code = post('sl_code');
    $sl_name = post('sl_name');
    $account_description = post('account_description');

    try {
        ChartOfAccount::add($account_code, $account_type_id, $gl_code, $gl_name, $sl_code, $sl_name, $account_description);
        flashMessage('add', 'Account added successfully.', FLASH_SUCCESS);
    } catch (Exception $ex) {
        flashMessage('add', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../../chart_of_accounts'); // You may need to adjust the redirect URL
}


//Update account
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = post('id');
    $account_code = post('account_code');
    $account_type_id = post('account_type_id');
    $gl_code = post('gl_code');
    $gl_name = post('gl_name');
    $sl_code = post('sl_code');
    $sl_name = post('sl_name');
    $account_description = post('account_description');

    try {
        // Find the existing ChartOfAccount instance
        $chart_of_account = ChartOfAccount::findById($id);

        // Check if the account exists
        if (!$chart_of_account) {
            throw new Exception('Account not found');
        }

        // Perform the update operation with arguments
        $chart_of_account->update(
            $account_code,
            $account_type_id,
            $gl_code,
            $gl_name,
            $sl_code,
            $sl_name,
            $account_description
        );

        // Flash message and redirect upon success
        flashMessage('update', 'Account has been updated!', FLASH_SUCCESS);
        redirect("../../edit_chart_of_account?id={$id}");

    } catch (Exception $ex) {
        // Handle errors
        flashMessage('update', $ex->getMessage(), FLASH_ERROR);
        redirect("../../edit_chart_of_account?id={$id}");
    }
} else {
    // Handle invalid requests or redirect appropriately
    // For example, redirect to an error page or homepage
    redirect("../error.php");
}





