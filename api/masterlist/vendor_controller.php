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
            $stmt = $connection->prepare("INSERT INTO vendors (vendor_name, vendor_code, account_number, vendor_address, contact_number, email, terms, tin, tax_type, tel_no, fax_no, notes, item_type) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            // Begin transaction
            $connection->beginTransaction();

            $importedCount = 0;
            foreach ($rows as $row) {
                // Trim the row to the first 13 columns
                $row = array_slice($row, 0, 13);
            
                // Ensure all values are set, even if empty
                $row = array_pad($row, 13, null);
            
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

// Delete vendor
if (get('action') === 'delete') {
    $id = get('id');
    $vendor = Vendor::find($id);

    if ($vendor) {
        $vendor->delete();
        flashMessage('delete_vendor', 'Vendor deleted successfully', FLASH_SUCCESS);
    } else {
        flashMessage('delete_vendor', 'Invalid vendor', FLASH_ERROR);
    }
    redirect('../../vendor_list'); // Adjust the redirect URL as needed
}

// Add vendor
if (post('action') === 'add') {
    $vendor_name = post('vendorname');
    $vendor_code = post('vendorcode');
    $account_number = post('accountnumber');
    $vendor_address = post('vendoraddress');
    $contact_number = post('contactnumber');
    $email = post('email');
    $terms = post('terms');
    $tin = post('tin');
    $tax_type = post('tax_type');
    $tel_no = post('tel_no');
    $fax_no = post('fax_no');
    $notes = post('notes');
    $item_type = post('item_type');

    try {
        Vendor::add($vendor_name, $vendor_code, $account_number, $vendor_address, $contact_number, $email, $terms, $tin, $tax_type, $tel_no, $fax_no, $notes, $item_type);
        flashMessage('add_vendor', 'Vendor added successfully.', FLASH_SUCCESS);
    } catch (Exception $ex) {
        flashMessage('add_vendor', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../../vendor_list'); // Adjust the redirect URL as needed
}

// Update vendor
if (post('action') === 'update') {
    $id = post('id');
    $vendor_name = post('vendorname');
    $vendor_code = post('vendorcode');
    $account_number = post('accountnumber');
    $vendor_address = post('vendoraddress');
    $contact_number = post('contactnumber');
    $email = post('email');
    $terms = post('terms');
    $tin = post('tin');
    $tax_type = post('tax_type');
    $tel_no = post('tel_no');
    $fax_no = post('fax_no');
    $notes = post('notes');
    $item_type = post('item_type');

    try {
        $vendor = Vendor::find($id);

        if ($vendor) {
            $vendor->vendor_name = $vendor_name;
            $vendor->vendor_code = $vendor_code;
            $vendor->account_number = $account_number;
            $vendor->vendor_address = $vendor_address;
            $vendor->contact_number = $contact_number;
            $vendor->email = $email;
            $vendor->terms = $terms;
            $vendor->tin = $tin;
            $vendor->tax_type = $tax_type;
            $vendor->tel_no = $tel_no;
            $vendor->fax_no = $fax_no;
            $vendor->notes = $notes;
            $vendor->item_type = $item_type;

            $vendor->update();
            flashMessage('update_vendor', 'Vendor updated successfully.', FLASH_SUCCESS);
        } else {
            throw new Exception('Vendor not found.');
        }
    } catch (Exception $ex) {
        flashMessage('update_vendor', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../../vendor_list'); // Adjust the redirect URL as needed
}

