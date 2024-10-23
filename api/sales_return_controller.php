<?php

require_once __DIR__ . '/../_init.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ==========================================================================
// WARNING: DO NOT MODIFY THE CODE BELOW!
// Version: 1.0.0 (First working version)
// Last Updated: [7/26/2024]
//
// This method has been finalized and locked for modifications.
// This section is critical for the functionality of the sales_return processing.
// Any changes might cause unexpected behavior or system failures.
// If changes are absolutely necessary, consult with the lead developer
// and thoroughly test all affected systems before deployment.
//
// Change Log:
// v1.0.0 - Initial working version implemented and tested
// ==========================================================================

if (post('action') === 'add') {
    try {
        // Retrieve form data
        // sales_return information
        $sales_return_number = post('sales_return_number');
        $sales_return_date = post('sales_return_date');
        $sales_return_due_date = post('sales_return_due_date');
        $sales_return_account_id = post('sales_return_account_id');

        $sales_return_account_id = post('sales_return_account_id');
        if (empty($sales_return_account_id)) {
            throw new Exception("sales_return account ID is missing or empty.");
        }
        $sales_return_due_date = post('sales_return_due_date');
        $customer_po = post('customer_po');
        $so_no = post('so_no');
        $rep = post('rep');
        $customer_id = post('customer_id');
        $customer_name = post('customer_name');
        $payment_method = post('payment_method');
        $location = post('location');
        $terms = post('terms');
        $memo = post('memo');

        // Summary details
        $gross_amount = post('gross_amount');
        $discount_amount = post('total_discount_amount');
        $discount_account_ids = post('discount_account_ids');
        $output_vat_ids = post('output_vat_ids');
        $net_amount_due = post('net_amount_due');
        $vat_amount = post('total_vat_amount');
        $vatable_amount = post('vatable_amount');
        $zero_rated_amount = post('zero_rated_amount');
        $vat_exempt_amount = post('vat_exempt_amount');

        // Tax withholding information

        $tax_withheld_percentage = post('tax_withheld_percentage');
        $tax_withheld_amount = post('tax_withheld_amount');
        $tax_withheld_account_id = post('tax_withheld_account_id');

        // Total amount and user information
        $total_amount_due = post('total_amount_due');
        $created_by = $_SESSION['user_name'];



        // Check for existing sales_return to prevent duplicates
        $existingRecord = SalesReturn::findBysales_returnNo($sales_return_number);


        if ($existingRecord !== null) {
            throw new Exception("Record with sales_return #: $sales_return_number already exists.");
        }

        // Get the last transaction ID (if needed for reference)
        $transaction_id = SalesReturn::getLastTransactionId();

        // Decode JSON data for sales_return items
        $items = json_decode($_POST['item_data'], true);


        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }


        // Call the sales_return::add method to insert the sales_return into the database
        SalesReturn::add(
            $sales_return_number,
            $sales_return_date,
            $sales_return_account_id,
            $customer_po,
            $so_no,
            $rep,
            $discount_account_ids,
            $output_vat_ids,
            $sales_return_due_date,
            $customer_id,
            $customer_name,
            $payment_method,
            $location,
            $terms,
            $memo,
            $gross_amount,
            $discount_amount,
            $net_amount_due,
            $vat_amount,
            $vatable_amount,
            $zero_rated_amount,
            $vat_exempt_amount,
            $tax_withheld_percentage,
            $tax_withheld_amount,
            $tax_withheld_account_id,
            $total_amount_due,
            $items,
            $created_by
        );

        // Set success message
        flashMessage('add_sales_return', 'Sales Return Submitted Successfully!', FLASH_SUCCESS);

        // Prepare the response with just the transaction_id
        $response = ['success' => true, 'sales_returnId' => $transaction_id + 1];

    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error in sales_return submission: ' . $ex->getMessage());
    }

    // Send JSON response
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
// ==========================================================================
// WARNING: DO NOT MODIFY THE CODE ABOVE!
// This section is critical for the functionality of the sales_return processing.
// Any changes might cause unexpected behavior or system failures.
// ==========================================================================

// update for create
if (post('action') === 'update_print_status') {
    try {
        $sales_returnId = post('sales_return_id');
        $printStatus = post('print_status');

        if (!$sales_returnId) {
            throw new Exception("sales_return ID is required.");
        }

        if (!in_array($printStatus, [1, 2])) {
            throw new Exception("Invalid print status.");
        }

        // Update the print_status in the database
        $stmt = $connection->prepare("UPDATE sales_return SET print_status = :status WHERE id = :id");
        $stmt->bindParam(':id', $sales_returnId, PDO::PARAM_INT);
        $stmt->bindParam(':status', $printStatus, PDO::PARAM_INT);
        $result = $stmt->execute();

        if ($result) {
            $response = ['success' => true];
        } else {
            throw new Exception("Failed to update print status.");
        }
    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error updating print status: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Add this to the existing switch statement or if-else block
if (post('action') === 'void_check') {
    try {
        $id = post('id');
        if (!$id) {
            throw new Exception("Credit_memo ID is required.");
        }
        
        $result = SalesReturn::void($id);
        
        if ($result) {
            $response = ['success' => true];
        } else {
            throw new Exception("Failed to void invoice.");
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        error_log('Error voiding invoice: ' . $e->getMessage());
    }
    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (post('action') === 'save_draft') {
    try {
        // Retrieve form data
        $sales_return_date = post('sales_return_date');
        $sales_return_due_date = post('sales_return_due_date');
        $sales_return_account_id = post('sales_return_account_id');
        $customer_po = post('customer_po');
        $so_no = post('so_no');
        $rep = post('rep');
        $customer_id = post('customer_id');
        $customer_name = post('customer_name');
        $payment_method = post('payment_method');
        $location = post('location');
        $terms = post('terms');
        $memo = post('memo');

        // Summary details
        $gross_amount = post('gross_amount');
        $discount_amount = post('total_discount_amount');
        $discount_account_ids = post('discount_account_ids');
        $output_vat_ids = post('output_vat_ids');
        $net_amount_due = post('net_amount_due');
        $vat_amount = post('total_vat_amount');
        $vatable_amount = post('vatable_amount');
        $zero_rated_amount = post('zero_rated_amount');
        $vat_exempt_amount = post('vat_exempt_amount');
        $tax_withheld_percentage = post('tax_withheld_percentage');
        $tax_withheld_amount = post('tax_withheld_amount');
        $tax_withheld_account_id = post('tax_withheld_account_id');
        $total_amount_due = post('total_amount_due');

        // Decode JSON data for sales_return items
        $items = json_decode($_POST['item_data'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }

        // Call the sales_return::addDraft method to insert the draft sales_return into the database
        $result = SalesReturn::addDraft(
            $sales_return_date,
            $sales_return_account_id,
            $customer_po,
            $so_no,
            $rep,
            $sales_return_due_date,
            $customer_id,
            $payment_method,
            $location,
            $terms,
            $memo,
            $gross_amount,
            $discount_amount,
            $net_amount_due,
            $vat_amount,
            $vatable_amount,
            $zero_rated_amount,
            $vat_exempt_amount,
            $tax_withheld_percentage,
            $tax_withheld_amount,
            $total_amount_due,
            $items
        );

        $response = $result ? 
            ['success' => true, 'message' => 'sales_return saved as draft successfully'] : 
            throw new Exception("Failed to save sales_return as draft.");

    } catch (Exception $ex) {
        error_log('Error in saving draft sales_return: ' . $ex->getMessage());
        error_log('Stack trace: ' . $ex->getTraceAsString());
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (post('action') === 'update_draft') {
    $id = post('id');
    $sales_return_number = post('sales_return_number');
    $sales_return_date = post('sales_return_date');
    $sales_return_due_date = post('sales_return_due_date');
    $sales_return_account_id = post('sales_return_account_id');

    $sales_return_account_id = post('sales_return_account_id');
    if (empty($sales_return_account_id)) {
        throw new Exception("sales_return account ID is missing or empty.");
    }
    $sales_return_due_date = post('sales_return_due_date');
    $customer_po = post('customer_po');
    $so_no = post('so_no');
    $rep = post('rep');
    $customer_id = post('customer_id');
    $customer_name = post('customer_name');
    $payment_method = post('payment_method');
    $location = post('location');
    $terms = post('terms');
    $memo = post('memo');

    // Summary details
    $gross_amount = post('gross_amount');
    $discount_amount = post('discount_amount');
    $discount_account_ids = post('discount_account_ids');
    $output_vat_ids = post('output_vat_ids');
    $net_amount_due = post('net_amount_due');
    $vat_amount = post('vat_amount');
    $vatable_amount = post('vatable_amount');
    $zero_rated_amount = post('zero_rated_amount');
    $vat_exempt_amount = post('vat_exempt_amount');

    // Tax withholding information

    $tax_withheld_percentage = post('tax_withheld_percentage');
    $tax_withheld_amount = post('tax_withheld_amount');
    $tax_withheld_account_id = post('tax_withheld_account_id');

    // Total amount and user information
    $total_amount_due = post('total_amount_due');
    $created_by = $_SESSION['user_name'];

    // Decode JSON data for sales return items
    $items = json_decode($_POST['item_data'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error decoding item data: " . json_last_error_msg());
    }


    $response = SalesReturn::updateDraft(
        $id,  $sales_return_number,  $sales_return_account_id,   $customer_name,  $customer_id,  $tax_withheld_account_id,  $tax_withheld_amount,  $total_amount_due,  $gross_amount,  $sales_return_date, $sales_return_due_date, $customer_po, $so_no, $rep, $payment_method, $location, $terms, $memo, $output_vat_ids, $vat_amount, $zero_rated_amount, $vat_exempt_amount, $vatable_amount, $tax_withheld_percentage, $discount_amount, $created_by, $items
    );

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (post('action') === 'save_final') {
    $id = post('id');
    $sales_return_number = post('sales_return_number');
    $sales_return_date = post('sales_return_date');
    $sales_return_due_date = post('sales_return_due_date');
    $sales_return_account_id = post('sales_return_account_id');

    $sales_return_account_id = post('sales_return_account_id');
    if (empty($sales_return_account_id)) {
        throw new Exception("sales_return account ID is missing or empty.");
    }
    $sales_return_due_date = post('sales_return_due_date');
    $customer_po = post('customer_po');
    $so_no = post('so_no');
    $rep = post('rep');
    $customer_id = post('customer_id');
    $customer_name = post('customer_name');
    $payment_method = post('payment_method');
    $location = post('location');
    $terms = post('terms');
    $memo = post('memo');

    // Summary details
    $gross_amount = post('gross_amount');
    $discount_amount = post('discount_amount');
    $discount_account_ids = post('discount_account_ids');
    $output_vat_ids = post('output_vat_ids');
    $net_amount_due = post('net_amount_due');
    $vat_amount = post('vat_amount');
    $vatable_amount = post('vatable_amount');
    $zero_rated_amount = post('zero_rated_amount');
    $vat_exempt_amount = post('vat_exempt_amount');

    // Tax withholding information

    $tax_withheld_percentage = post('tax_withheld_percentage');
    $tax_withheld_amount = post('tax_withheld_amount');
    $tax_withheld_account_id = post('tax_withheld_account_id');

    // Total amount and user information
    $total_amount_due = post('total_amount_due');
    $created_by = $_SESSION['user_name'];

    // Decode JSON data for sales return items
    $items = json_decode($_POST['item_data'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error decoding item data: " . json_last_error_msg());
    }


    $response = SalesReturn::saveFinal(
        $id,  $sales_return_number,  $sales_return_account_id,   $customer_name,  $customer_id,  $tax_withheld_account_id,  $tax_withheld_amount,  $total_amount_due,  $gross_amount,  $sales_return_date, $sales_return_due_date, $customer_po, $so_no, $rep, $payment_method, $location, $terms, $memo, $output_vat_ids, $vat_amount, $zero_rated_amount, $vat_exempt_amount, $vatable_amount, $tax_withheld_percentage, $discount_amount, $created_by, $items
    );

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}