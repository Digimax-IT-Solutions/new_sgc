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
// This section is critical for the functionality of the invoice processing.
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
        // Invoice information
        $invoice_number = post('invoice_number');
        $invoice_date = post('invoice_date');
        $invoice_due_date = post('invoice_due_date');
        $invoice_account_id = post('invoice_account_id');

        $invoice_account_id = post('invoice_account_id');
        if (empty($invoice_account_id)) {
            throw new Exception("Invoice account ID is missing or empty.");
        }
        $invoice_due_date = post('invoice_due_date');
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



        // Check for existing invoice to prevent duplicates
        $existingRecord = Invoice::findByInvoiceNo($invoice_number);


        if ($existingRecord !== null) {
            throw new Exception("Record with Invoice #: $invoice_number already exists.");
        }

        // Get the last transaction ID (if needed for reference)
        $transaction_id = Invoice::getLastTransactionId();

        // Decode JSON data for invoice items
        $items = json_decode($_POST['item_data'], true);


        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }


        // Call the Invoice::add method to insert the invoice into the database
        Invoice::add(
            $invoice_number,
            $invoice_date,
            $invoice_account_id,
            $customer_po,
            $so_no,
            $rep,
            $discount_account_ids,
            $output_vat_ids,
            $invoice_due_date,
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
        flashMessage('add_invoice', 'Sales Invoice Submitted Successfully!', FLASH_SUCCESS);

        // Prepare the response with just the transaction_id
        $response = ['success' => true, 'invoiceId' => $transaction_id + 1];

    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error in invoice submission: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
// ==========================================================================
// WARNING: DO NOT MODIFY THE CODE ABOVE!
// This section is critical for the functionality of the invoice processing.
// Any changes might cause unexpected behavior or system failures.
// ==========================================================================

// update for create
if (post('action') === 'update_print_status') {
    try {
        $invoiceId = post('invoice_id');
        $printStatus = post('print_status');

        if (!$invoiceId) {
            throw new Exception("Invoice ID is required.");
        }

        if (!in_array($printStatus, [1, 2])) {
            throw new Exception("Invalid print status.");
        }

        // Update the print_status in the database
        $stmt = $connection->prepare("UPDATE sales_invoice SET print_status = :status WHERE id = :id");
        $stmt->bindParam(':id', $invoiceId, PDO::PARAM_INT);
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



// Check if action is 'update'
if (post('action') === 'update') {
    try {
        // Retrieve invoice ID for update
        $id = post('id');

        // Retrieve form data for update
        $invoice_number = post('invoice_number');
        $invoice_date = post('invoice_date');
        $invoice_account_id = post('invoice_account_id');
        $invoice_due_date = post('invoice_due_date');
        $customer_id = post('customer_name');
        $payment_method = post('payment_method');
        $location = post('location');
        $terms = post('terms');
        $memo = post('memo');
        $gross_amount = post('gross_amount');
        $total_discount_amount = post('discount_amount');
        $net_amount_due = post('net_amount_due');
        $vat_amount = post('sales_tax_amount');
        $vatable_amount = post('vatable_amount');
        $zero_rated_amount = post('zero_rated_amount');
        $vat_exempt_amount = post('vat_exempt_amount');
        $tax_withheld_percentage = post('tax_withheld_percentage');
        $tax_withheld_amount = post('tax_withheld_amount');
        $total_amount_due = post('total_amount_due');

        // Decode JSON data for items
        $items = json_decode($_POST['item_data'], true);
        // Update invoice using Invoice class method
        Invoice::update(
            $id,
            $invoice_number,
            $invoice_date,
            $invoice_account_id,
            $invoice_due_date,
            $customer_id,
            $payment_method,
            $location,
            $terms,
            $memo,
            $gross_amount,
            $total_discount_amount,
            $net_amount_due,
            $vat_amount,
            $vatable_amount,
            $zero_rated_amount,
            $vat_exempt_amount,
            $tax_withheld_percentage,
            $tax_withheld_amount,
            $total_amount_due
        );

        // Update invoice details using Invoice class method
        Invoice::updateDetails($id, $items);

        flashMessage('update_invoice', 'Invoice updated successfully.', FLASH_SUCCESS);
    } catch (Exception $e) {
        // Handle any exceptions that occur during update
        flashMessage('update_invoice', $e->getMessage(), FLASH_ERROR);
    }

    // Redirect after processing 'update' action
    redirect('../invoice');
}

// Add this to the existing switch statement or if-else block
if (post('action') === 'void_check') {
    try {
        $id = post('id');
        if (!$id) {
            throw new Exception("Invoice ID is required.");
        }
        
        $result = Invoice::voidInvoice($id);
        
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
        $invoice_date = post('invoice_date');
        $invoice_due_date = post('invoice_due_date');
        $invoice_account_id = post('invoice_account_id');
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

        
        // Decode JSON data for invoice items
        $items = json_decode($_POST['item_data'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }

        // Call the Invoice::addDraft method to insert the draft invoice into the database
        $result = Invoice::addDraft(
            $invoice_date,
            $invoice_account_id,
            $customer_po,
            $so_no,
            $rep,
            $invoice_due_date,
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
            ['success' => true, 'message' => 'Invoice saved as draft successfully'] : 
            throw new Exception("Failed to save invoice as draft.");

    } catch (Exception $ex) {
        error_log('Error in saving draft invoice: ' . $ex->getMessage());
        error_log('Stack trace: ' . $ex->getTraceAsString());
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (post('action') === 'update_draft_invoice') {
    $id = post('id');
    $invoice_number = post('invoice_number');
    $items = json_decode(post('item_data'), true);
    $invoice_account_id = post('invoice_account_id');
    $customer_id = post('customer_id');
    $customer = Customer::find($customer_id);
    $customer_name = $customer ? $customer->customer_name : '';
    $total_amount_due = post('total_amount_due');
    $created_by = $_SESSION['user_name'];

    // Extract additional required data from the items or post data
    $invoice_date = post('invoice_date');
    // Summary details
    $gross_amount = post('gross_amount');
    $discount_amount = post('discount_amount');
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


    // Check for existing invoice to prevent duplicates
    $existingRecord = Invoice::findByInvoiceNo($invoice_number);


    if ($existingRecord !== null) {
        throw new Exception("Record with Invoice #: $invoice_number already exists.");
    }


    // Decode JSON data for invoice items
    $items = json_decode($_POST['item_data'], true);


    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error decoding item data: " . json_last_error_msg());
    }

    $response = Invoice::updateDraftInvoice(
        $id, 
        $invoice_number, 
        $invoice_account_id, 
        $customer_name, 
        $customer_id,
        $tax_withheld_account_id, 
        $tax_withheld_amount, 
        $total_amount_due, 
        $gross_amount,
        $invoice_date,
        $output_vat_ids, 
        $vat_amount, 
        $discount_amount, 
        $created_by,
        $items  // Add the $items parameter here
    );

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}