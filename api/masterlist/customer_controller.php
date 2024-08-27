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
            $stmt = $connection->prepare("INSERT INTO customers (customer_name, customer_code, customer_contact, shipping_address, billing_address, business_style, customer_terms, customer_tin, customer_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

            // Begin transaction
            $connection->beginTransaction();

            $importedCount = 0;
            foreach ($rows as $row) {
                // Trim the row to the first 9 columns
                $row = array_slice($row, 0, 9);
            
                // Ensure all values are set, even if empty
                $row = array_pad($row, 9, null);
            
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

// Delete customer
if (get('action') === 'delete') {
    $id = get('id');
    
    try {
        $customer = Customer::find($id);
        if ($customer) {
            $customer->delete();
            flashMessage('delete_customer', 'Customer deleted successfully.', FLASH_SUCCESS);
        } else {
            throw new Exception('Customer not found.');
        }
    } catch (Exception $ex) {
        flashMessage('delete_customer', $ex->getMessage(), FLASH_ERROR);
    }
    
    redirect('../../customer'); // Adjust the redirect URL as needed

}

// Add customer
if (post('action') === 'add') {
    $customer_name = post('customer_name');
    $customer_code = post('customer_code');
    $customer_contact = post('customer_contact');
    $shipping_address = post('shipping_address');
    $billing_address = post('billing_address');
    $business_style = post('business_style');
    $customer_terms = post('customer_terms');
    $customer_tin = post('customer_tin');
    $customer_email = post('customer_email');

    try {
        Customer::add($customer_name, $customer_code, $customer_contact, $shipping_address, $billing_address, $business_style, $customer_terms, $customer_tin, $customer_email);
        flashMessage('add_customer', 'Customer added successfully.', FLASH_SUCCESS);
    } catch (Exception $ex) {
        flashMessage('add_customer', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../../customer'); // Adjust the redirect URL as needed
}

// Update customer
if (post('action') === 'update') {
    $id = post('id');
    $customer_name = post('customer_name');
    $customer_code = post('customer_code');
    $customer_contact = post('customer_contact');
    $company_name = post('company_name');
    $shipping_address = post('shipping_address');
    $billing_address = post('billing_address');
    $business_style = post('business_style');
    $customer_terms = post('customer_terms');
    $customer_tin = post('customer_tin');
    $customer_email = post('customer_email');

    try {
        $customer = Customer::find($id);

        if ($customer) {
            $customer->customer_name = $customer_name;
            $customer->customer_code = $customer_code;
            $customer->customer_contact = $customer_contact;
            $customer->shipping_address = $shipping_address;
            $customer->billing_address = $billing_address;
            $customer->business_style = $business_style;
            $customer->customer_terms = $customer_terms;
            $customer->customer_tin = $customer_tin;
            $customer->customer_email = $customer_email;

            $customer->update();
            flashMessage('update_customer', 'Customer updated successfully.', FLASH_SUCCESS);
        } else {
            throw new Exception('Customer not found.');
        }
    } catch (Exception $ex) {
        flashMessage('update_customer', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../../customer'); // Adjust the redirect URL as needed
}

?>
