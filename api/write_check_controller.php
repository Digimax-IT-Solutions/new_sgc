<?php
require_once __DIR__ . '/../_init.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

// ==========================================================================
// WARNING: DO NOT MODIFY THE CODE BELOW!
// Version: 1.0.0 (First working version)
// Last Updated: [8/6/2024]
//
// This method has been finalized and locked for modifications.
// This section is critical for the functionality of the write check processing.
// Any changes might cause unexpected behavior or system failures.
// If changes are absolutely necessary, consult with the lead developer
// and thoroughly test all affected systems before deployment.
//
// Change Log:
// v1.0.0 - Initial working version implemented and tested
// ==========================================================================

if (post('action') === 'add') {
    try {
        // Retrieve the last transaction_id
        $transaction_id = WriteCheck::getLastTransactionId();

        // Retrieve form data
        $cv_no = post('cv_no');
        $check_no = post('check_no');
        $ref_no = post('ref_no');
        $check_date = post('check_date');
        $bank_account_id = post('bank_account_id');
        $payee_type = post('payee_type');
        $payee_id = post('payee_id');
        $payee_name = post('payee_name');
        $memo = post('memo');
        $gross_amount = post('gross_amount');
        $discount_amount = post('discount_amount');
        $net_amount_due = post('net_amount_due');
        $vat_percentage_amount = post('vat_percentage_amount');
        $net_of_vat = post('net_of_vat');
        $tax_withheld_amount = post('tax_withheld_amount');
        $wtax_account_id = post('tax_withheld_account_id');
        $total_amount_due = post('total_amount_due');
        $created_by = $_SESSION['user_name'];

        // Process item_data (assuming it's JSON data)
        $items = json_decode($_POST['item_data'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }

        // Save check using WriteCheck class method
        WriteCheck::add(
            $cv_no, $check_no, $ref_no, $check_date, $bank_account_id, $payee_type, 
            $payee_id, $payee_name, $memo, $gross_amount, $discount_amount, 
            $net_amount_due, $vat_percentage_amount, $net_of_vat, $tax_withheld_amount, 
            $total_amount_due, $created_by, $items, $wtax_account_id
        );

        flashMessage('add_write_check', 'Check added successfully.', FLASH_SUCCESS);

        // Prepare the response with just the transaction_id
        $response = ['success' => true, 'id' => $transaction_id + 1];

    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        error_log('Error in write check submission: ' . $e->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
// ==========================================================================
// WARNING: DO NOT MODIFY THE CODE ABOVE!
// This section is critical for the functionality of the write check processing.
// Any changes might cause unexpected behavior or system failures.
// ==========================================================================

// Update check
if (post('action') === 'update') {
    try {
        $id = post('id');
        $check_no = post('check_no');
        $ref_no = post('ref_no');
        $check_date = post('check_date');
        $bank_account_id = post('bank_account_id');
        $payee_type = post('payee_type');
        $payee_id = post('payee_id');
        $memo = post('memo');
        $gross_amount = post('gross_amount');
        $discount_amount = post('discount_amount');
        $net_amount_due = post('net_amount_due');
        $vat_percentage_amount = post('vat_percentage_amount');
        $net_of_vat = post('net_of_vat');
        $tax_withheld_amount = post('tax_withheld_amount');
        $total_amount_due = post('total_amount_due');
        $updated_by = $_SESSION['user_name'];
        $tax_withheld_account_id = post('tax_withheld_account_id');

        // Process item_data (assuming it's JSON data)
        $items = json_decode($_POST['item_data'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }

        // Update check and its details using WriteCheck class methods
        WriteCheck::update(
            $id, $check_no, $ref_no, $check_date, $bank_account_id, $payee_type,
            $payee_id, $memo, $gross_amount, $discount_amount, $net_amount_due,
            $vat_percentage_amount, $net_of_vat, $tax_withheld_amount, $total_amount_due
        );
        WriteCheck::updateDetails($id, $items);

        flashMessage('update_write_check', 'Check updated successfully.', FLASH_SUCCESS);
        $response = ['success' => true, 'id' => $id];

    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        error_log('Error in write check update: ' . $e->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Update print status
if (post('action') === 'update_print_status') {
    try {
        $id = post('id');
        $printStatus = post('print_status');

        if (!$id) {
            throw new Exception("Write Check ID is required.");
        }

        if (!in_array($printStatus, [1, 2])) {
            throw new Exception("Invalid print status.");
        }

         // Update the print_status in the database
         $stmt = $connection->prepare("UPDATE wchecks SET print_status = :status WHERE id = :id");
         $stmt->bindParam(':id', $id, PDO::PARAM_INT);
         $stmt->bindParam(':status', $printStatus, PDO::PARAM_INT);
         $result = $stmt->execute();

        if ($result) {
            $response = ['success' => true];
        } else {
            throw new Exception("Failed to update print status.");
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        error_log('Error updating print status: ' . $e->getMessage());
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
            throw new Exception("Invoice ID is required.");
        }
        
        $result = WriteCheck::void($id);
        
        if ($result) {
            $response = ['success' => true];
        } else {
            throw new Exception("Failed to void wchecks.");
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        error_log('Error voiding wchecks: ' . $e->getMessage());
    }
    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (post('action') === 'save_draft') {
    try {
        // Retrieve the last transaction_id
        $transaction_id = WriteCheck::getLastTransactionId();

        // Retrieve form data
        $check_date = post('check_date');
        $bank_account_id = post('bank_account_id');
        $payee_type = post('payee_type');
        $payee_id = post('payee_id');
        $payee_name = post('payee_name');
        $memo = post('memo');
        $gross_amount = post('gross_amount');
        $discount_amount = post('discount_amount');
        $net_amount_due = post('net_amount_due');
        $vat_percentage_amount = post('vat_percentage_amount');
        $net_of_vat = post('net_of_vat');
        $tax_withheld_amount = post('tax_withheld_amount');
        $total_amount_due = post('total_amount_due');
        $ref_no = post('ref_no');
        $check_no = post('check_no');
        $created_by = $_SESSION['user_id']; // Assuming you have a user session

      
        
        // Decode JSON data (Write Check Details)
         // Get the account IDs from the first item in the items array
         $items = json_decode(post('item_data'), true);
         $discount_account_id = $items[0]['discount_account_id'] ?? null;
         $input_vat_account_id = $items[0]['input_vat_account_id'] ?? null;
         $tax_withheld_account_id = post('tax_withheld_account_id');
         $tax_withheld_account_id = post('tax_withheld_account_id');
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }

        // Validate that bank_account_id is not null
        if (empty($bank_account_id)) {
            throw new Exception("Bank account cannot be empty");
        }

        // Save draft to database
        $result = WriteCheck::saveDraft(
            $check_no, $check_date, $bank_account_id, $ref_no, $payee_type, $payee_id, $memo, 
            $gross_amount, $discount_amount, $net_amount_due, $vat_percentage_amount, $net_of_vat, 
            $tax_withheld_amount, $total_amount_due, $discount_account_id, $tax_withheld_account_id, 
            $input_vat_account_id, $created_by, $items
        );

        if (!$result['success']) {
            throw new Exception($result['message']);
        }

        $response = ['success' => true, 'message' => 'Write Check saved as draft successfully'];
    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error in saving write check draft: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}



if (post('action') === 'update_draft') {
    try {
        // Retrieve input data
        $id = post('id');
        $cv_no = post('cv_no');
        $check_no = post('check_no');
        $ref_no = post('ref_no');
        $check_date = post('check_date');
        $bank_account_id = post('bank_account_id');
        $payee_type = post('payee_type');
        $payee_id = post('payee_id');
        $payee_name = post('payee_name');
        $memo = post('memo');
        $gross_amount = post('gross_amount');
        $discount_amount = post('discount_amount');
        $net_amount_due = post('net_amount_due');
        $vat_percentage_amount = post('vat_percentage_amount');
        $net_of_vat = post('net_of_vat');
        $tax_withheld_amount = post('tax_withheld_amount');
        $wtax_account_id = post('tax_withheld_account_id');
        $total_amount_due = post('total_amount_due');


        $created_by = $_SESSION['user_name'];
        $items = json_decode(post('item_data'), true); // Assuming item_data is used later

        // Validate input
        if (!$id || !$cv_no) {
            throw new Exception("Write Check ID and number are required.");
        }


        // Prepare and execute the update statement
        $stmt = $connection->prepare("
            UPDATE wchecks 
            SET status = 0, 
                cv_no = :cv_no
            WHERE id = :id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':cv_no', $cv_no, PDO::PARAM_STR);
        $result = $stmt->execute();

        if ($result) {

            // Call the updateDraft function
            WriteCheck::updateDraft(
                $id, $cv_no, $check_no, $ref_no, $check_date, $bank_account_id, 
                $payee_type, $payee_id, $payee_name, $memo, $gross_amount, $discount_amount, 
                $net_amount_due, $vat_percentage_amount, $net_of_vat, $tax_withheld_amount, 
                $total_amount_due, $items, $created_by, $wtax_account_id
            );


            $response = [
                'success' => true,
                'checkId' => $id,
                'message' => 'Write Check updated successfully'
            ];
        } else {
            throw new Exception("Failed to update write check.");
        }
    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error updating draft write check: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Redirect if no actions were performed
redirect('../write_check');