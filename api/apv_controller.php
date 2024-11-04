<?php
require_once __DIR__ . '/../_init.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (post('action') === 'add') {
    try {
        // Retrieve the last transaction_id
        $transaction_id = Apv::getLastTransactionId();

        // Retrieve form data
        $apv_no = post('apv_no');
        $ref_no = post('ref_no');
        $po_no = post('po_no');
        $rr_no = post('rr_no');
        $apv_date = post('apv_date');
        $apv_due_date = post('apv_due_date');
        $terms_id = post('terms_id');
        $account_id = post('account_id');
        $vendor_id = post('vendor_id');
        $vendor_name = post('vendor_name');
        $vendor_tin = post('vendor_tin');
        $memo = post('memo');
        $location = post('location');
        $gross_amount = post('gross_amount');
        $discount_amount = post('discount_amount');
        $net_amount_due = post('net_amount_due');
        $vat_percentage_amount = post('vat_percentage_amount');
        $net_of_vat = post('net_of_vat');
        $tax_withheld_amount = post('tax_withheld_amount');
        $tax_withheld_percentage = post('tax_withheld_percentage');
        $wtax_account_id = post('tax_withheld_account_id');
        $total_amount_due = post('total_amount_due');
        $created_by = $_SESSION['user_name'];

        // Process item_data (assuming it's JSON data)
        $items = json_decode($_POST['item_data'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }

        // Save check using WriteCheck class method
        Apv::add(
            $apv_no,
            $ref_no,
            $po_no,
            $rr_no,
            $apv_date,
            $apv_due_date,
            $terms_id,
            $account_id,
            $vendor_id,
            $vendor_name,
            $vendor_tin,
            $memo,
            $location,
            $gross_amount,
            $discount_amount,
            $net_amount_due,
            $vat_percentage_amount,
            $net_of_vat,
            $tax_withheld_amount,
            $tax_withheld_percentage,
            $total_amount_due,
            $created_by,
            $items,
            $wtax_account_id
        );

        flashMessage('add_apv', 'Apv added successfully.', FLASH_SUCCESS);

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

if (post('action') === 'fetch_all_receive_items') {
    try {
        $query = "
        SELECT ri.*, v.id as vendor_id, v.vendor_name, po.*, rid.cost_center_id
        FROM receive_items ri
        JOIN receive_item_details rid ON ri.id = rid.receive_id
        JOIN purchase_order po ON rid.po_id = po.id
        JOIN vendors v ON ri.vendor_id = v.id
        WHERE po.po_status = 1
        GROUP BY ri.id, v.id, v.vendor_name, po.id, rid.cost_center_id
        ";

        $stmt = $connection->prepare($query);
        $stmt->execute();

        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response = ['success' => true, 'items' => $items];
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        error_log('Error fetching all receive items: ' . $e->getMessage());
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (post('action') === 'update_print_status') {
    try {
        $id = post('id');
        $printStatus = post('print_status');

        if (!$id) {
            throw new Exception("APV ID is required.");
        }

        if (!in_array($printStatus, [1, 2])) {
            throw new Exception("Invalid print status.");
        }

        // Update the print_status in the database
        $stmt = $connection->prepare("UPDATE apv SET print_status = :status WHERE id = :id");
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
if (post('action') === 'void_apv') {
    try {
        $id = post('id');
        if (!$id) {
            throw new Exception("Invoice ID is required.");
        }

        $result = Apv::void($id);

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
        $ref_no = post('ref_no');
        $po_no = post('po_no');
        $rr_no = post('rr_no');
        $apv_date = post('apv_date');
        $apv_due_date = post('apv_due_date');
        $terms_id = post('terms_id');
        $account_id = post('account_id');
        $vendor_id = post('vendor_id');
        $vendor_name = post('vendor_name');
        $vendor_tin = post('vendor_tin');
        $memo = post('memo');
        $location = post('location');


        // Summary details
        $gross_amount = post('gross_amount');
        $discount_amount = post('discount_amount');
        $net_amount_due = post('net_amount_due');
        $vat_percentage_amount = post('vat_percentage_amount');
        $net_of_vat = post('net_of_vat');
        $tax_withheld_amount = post('tax_withheld_amount');
        $tax_withheld_percentage = post('tax_withheld_percentage');
        $total_amount_due = post('total_amount_due');
        $created_by = post('created_by');
        $wtax_account_id = post('tax_withheld_account_id'); // Add this line

        // Decode JSON data for APV items
        $items = json_decode(post('item_data'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }

        // Call the APV::saveDraft method to insert the draft APV into the database
        $result = APV::saveDraft(
            $ref_no,
            $po_no,
            $rr_no,
            $apv_date,
            $apv_due_date,
            $terms_id,
            $account_id,
            $vendor_id,
            $vendor_name,
            $vendor_tin,
            $memo,
            $location,
            $gross_amount,
            $discount_amount,
            $net_amount_due,
            $vat_percentage_amount,
            $net_of_vat,
            $tax_withheld_amount,
            $tax_withheld_percentage,
            $total_amount_due,
            $created_by,
            $items,
            $wtax_account_id
        );

        $response = $result ?
            ['success' => true, 'message' => 'APV saved as draft successfully'] :
            throw new Exception("Failed to save APV as draft.");
    } catch (Exception $ex) {
        error_log('Error in saving draft APV: ' . $ex->getMessage());
        error_log('Stack trace: ' . $ex->getTraceAsString());
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (post('action') === 'update_draft') {
    try {
        // Retrieve form data
        $id = (int)post('id');
        $ref_no = (string)post('ref_no');
        $po_no = (string)post('po_no');
        $rr_no = (string)post('rr_no');
        $apv_date = (string)post('apv_date');
        $apv_due_date = (string)post('apv_due_date');
        $terms_id = (int)post('terms_id');
        $account_id = (int)post('account_id');
        $vendor_id = (int)post('vendor_id');
        $vendor_name = (string)post('vendor_name');
        $vendor_tin = (string)post('vendor_tin');
        $memo = (string)post('memo');
        $location = (int) post('location');

        $gross_amount = (float)post('gross_amount');
        $discount_amount = (float)post('discount_amount');
        $net_amount_due = (float)post('net_amount_due');
        $vat_percentage_amount = (float)post('vat_percentage_amount');
        $net_of_vat = (float)post('net_of_vat');
        $tax_withheld_amount = (float)post('tax_withheld_amount');
        $tax_withheld_percentage = (float)post('tax_withheld_percentage');
        $total_amount_due = (float)post('total_amount_due');
        $wtax_account_id = (int)post('wtax_account_id');

        // Validate required fields
        if (empty($id)) {
            throw new Exception("APV ID is required.");
        }

        // Decode item data
        $items = json_decode(post('item_data'), true);
        if (!$items || !is_array($items)) {
            throw new Exception("Invalid item data.");
        }

        // Extract account IDs from the first item
        $discount_account_id = $items[0]['discount_account_id'] ?? null;
        $input_vat_account_id = $items[0]['input_vat_account_id'] ?? null;

        // Get the session user name
        $created_by = $_SESSION['user_name'] ?? 'system';

        // Update the draft
        $updateResult = APV::updateDraft(
            $id,
            $ref_no,  // This is passed but not used in the SQL query, so we will not bind it
            $po_no,
            $rr_no,
            $apv_date,
            $apv_due_date,
            $terms_id,
            $account_id,
            $vendor_id,
            $vendor_tin,
            $memo,
            $location,
            $gross_amount,
            $discount_amount,
            $net_amount_due,
            $vat_percentage_amount,
            $net_of_vat,
            $tax_withheld_amount,
            $tax_withheld_percentage,
            $total_amount_due,
            $items,
            $created_by,
            $wtax_account_id,
            $discount_account_id,
            $input_vat_account_id
        );

        if (!$updateResult['success']) {
            throw new Exception($updateResult['message'] ?? "Failed to update APV draft.");
        }

        // Prepare the success response
        $response = [
            'success' => true,
            'id' => $id,
            'message' => 'APV draft updated successfully'
        ];
    } catch (Exception $ex) {
        // Prepare the error response
        $response = [
            'success' => false,
            'message' => 'Error: ' . $ex->getMessage()
        ];
        error_log('Error updating draft APV: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}


if (post('action') === 'save_final') {
    try {
        $id = (int)post('id');
        $apv_no = (string)post('apv_no');
        $ref_no = (string)post('ref_no');
        $po_no = (string)post('po_no');
        $rr_no = (string)post('rr_no');
        $apv_date = (string)post('apv_date');
        $apv_due_date = (string)post('apv_due_date');
        $terms_id = (int)post('terms_id');
        $account_id = (int)post('account_id');
        $vendor_id = (int)post('vendor_id');
        $vendor = Vendor::find($vendor_id);
        $vendor_name = $vendor ? $vendor->vendor_name : '';
        $vendor_tin = (string)post('vendor_tin');
        $memo = (string)post('memo');
        $memo = (int) post('memo');

        $gross_amount = (float)post('gross_amount');
        $discount_amount = (float)post('discount_amount');
        $net_amount_due = (float)post('net_amount_due');
        $vat_percentage_amount = (float)post('vat_percentage_amount');
        $net_of_vat = (float)post('net_of_vat');
        $tax_withheld_amount = (float)post('tax_withheld_amount');
        $tax_withheld_percentage = (float)post('tax_withheld_percentage');
        $total_amount_due = (float)post('total_amount_due');
        $items = json_decode(post('item_data'), true);
        $created_by = $_SESSION['user_name'];
        $wtax_account_id = (int)post('wtax_account_id');
        $discount_account_id = (int)post('discount_account_id');
        $input_vat_account_id = (int)post('input_vat_account_id');


        if (!$id || !$apv_no || !$items) {
            throw new Exception("APV ID, number, and items are required.");
        }

        // Log received data
        error_log('Received data: ' . print_r($_POST, true));

        // Update the APV record
        $stmt = $connection->prepare("
            UPDATE apv 
            SET status = 1, 
                apv_no = :apv_no
            WHERE id = :id
        ");

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':apv_no', $apv_no, PDO::PARAM_STR);

        $result = $stmt->execute();

        if ($result) {
            // Call the updateDraft function
            APV::saveFinal(
                $id,
                $apv_no,
                $ref_no,
                $po_no,
                $rr_no,
                $apv_date,
                $apv_due_date,
                $terms_id,
                $account_id,
                $vendor_id,
                $vendor_name,
                $vendor_tin,
                $memo,
                $location,
                $gross_amount,
                $discount_amount,
                $net_amount_due,
                $vat_percentage_amount,
                $net_of_vat,
                $tax_withheld_amount,
                $tax_withheld_percentage,
                $total_amount_due,
                $items,
                $created_by,
                $wtax_account_id,
                $discount_account_id,
                $input_vat_account_id
            );

            $response = [
                'success' => true,
                'id' => $id,
                'message' => 'APV updated successfully'
            ];
        } else {
            throw new Exception("Failed to update APV.");
        }
    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error updating draft APV: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
// Redirect if no actions were performed
redirect('../accounts_payable_voucher');
