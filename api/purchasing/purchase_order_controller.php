<?php

require_once __DIR__ . '/../_init.php';
// Include FPDF library
require_once 'fpdf.php';

if (post('action') === 'add') {
    try {
        // Process your form data here
        // You can access form fields like $_POST['po_no'], $_POST['po_date'], etc.
        // For example:
        $po_no = post('po_no');
        $po_date = post('po_date');
        $delivery_date = post('delivery_date');
        $vendor_id = post('vendor_id');
        $vendor_address = post('vendor_address');
        $terms = post('terms');
        $gross_amount = post('gross_amount');
        $discount_amount = post('total_discount_amount');
        $net_amount_due = post('net_amount_due');
        $input_vat_amount = post('total_input_vat_amount');
        $vatable_amount = post('vatable_amount');
        $zero_rated_amount = post('zero_rated_amount');
        $vat_exempt_amount = post('vat_exempt_amount');
        $total_amount_due = post('total_amount_due');
        $memo = post('memo');

        // Check for existing invoice to prevent duplicates
        $existingRecord = PurchaseOrder::findByPoNo($po_no);


        if ($existingRecord !== null) {
            throw new Exception("Record with PO #: $po_no already exists.");
        }

        // Retrieve the last transaction_id
        $transaction_id = PurchaseOrder::getLastTransactionId();

        // Decode JSON data
        $items = json_decode($_POST['item_data'], true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }

        PurchaseOrder::add(
            $po_no,
            $po_date,
            $delivery_date,
            $vendor_id,
            $terms,
            $gross_amount,
            $discount_amount,
            $net_amount_due,
            $input_vat_amount,
            $vatable_amount,
            $zero_rated_amount,
            $vat_exempt_amount,
            $total_amount_due,
            $memo,
            $items
        );

        // You can output a success message or redirect the user to another page after successful processing.
        flashMessage('add_purchase_order', 'Purchase order added!.', FLASH_SUCCESS);

        $response = ['success' => true, 'id' => $transaction_id + 1];

    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error in purchase order submission: ' . $ex->getMessage());
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
            throw new Exception("Purchase Order ID is required.");
        }

        if (!in_array($printStatus, [1, 2])) {
            throw new Exception("Invalid print status.");
        }

        // Update the print_status in the database
        $stmt = $connection->prepare("UPDATE purchase_order SET print_status = :status WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $printStatus, PDO::PARAM_INT);
        $result = $stmt->execute();

        if ($result) {
            $response = ['success' => true, 'id' => $id];
        } else {
            throw new Exception("Failed to update print status.");
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        error_log('Error updating print status: ' . $e->getMessage());
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Add this to the existing switch statement or if-else block
if (post('action') === 'void_check') {
    try {
        $id = post('id');

        if (!$id) {
            throw new Exception("Purchase Order ID is required.");
        }

        // Update the status to 3 (void) in the database
        $stmt = $connection->prepare("UPDATE purchase_order SET po_status = 3 WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $result = $stmt->execute();

        if ($result) {
            $response = ['success' => true];
        } else {
            throw new Exception("Failed to void purchase.");
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        error_log('Error voiding purchase: ' . $e->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (post('action') === 'save_draft') {
    try {
        // Retrieve form data
        $po_date = post('po_date');
        $delivery_date = post('delivery_date');
        $vendor_id = post('vendor_id');
        $terms = post('terms');
        $memo = post('memo');

        // Summary details
        $gross_amount = post('gross_amount');
        $discount_amount = post('total_discount_amount');
        $net_amount_due = post('net_amount_due');
        $input_vat_amount = post('total_input_vat_amount');
        $vatable_amount = post('vatable_amount');
        $zero_rated_amount = post('zero_rated_amount');
        $vat_exempt_amount = post('vat_exempt_amount');
        $total_amount_due = post('total_amount_due');

        // Decode JSON data for purchase order items
        $items = json_decode($_POST['item_data'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }

        // Call the PurchaseOrder::saveDraft method to insert the draft purchase order into the database
        $result = PurchaseOrder::saveDraft(
            $po_date,
            $delivery_date,
            $vendor_id,
            $terms,
            $gross_amount,
            $discount_amount,
            $net_amount_due,
            $input_vat_amount,
            $vatable_amount,
            $zero_rated_amount,
            $vat_exempt_amount,
            $total_amount_due,
            $memo,
            $items
        );

        if ($result) {
            $response = ['success' => true, 'message' => 'Purchase order saved as draft successfully'];
        } else {
            throw new Exception("Failed to save purchase order as draft.");
        }

    } catch (Exception $ex) {
        error_log('Error in saving draft purchase order: ' . $ex->getMessage());
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
        $id = post('id');
        $po_no = post('po_no');
        $items = json_decode(post('item_data'), true);

        if (!$id || !$po_no) {
            throw new Exception("Purchase Order ID and number are required.");
        }

        // Log received data
        error_log('Received data: ' . print_r($_POST, true));

        // Update the purchase order in the database
        $stmt = $connection->prepare("
            UPDATE purchase_order 
            SET po_status = 0,
                po_no = :po_no
            WHERE id = :id
        ");
        
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':po_no', $po_no, PDO::PARAM_STR);

        $result = $stmt->execute();

        if ($result) {
            // Update purchase order details
            // PurchaseOrder::updateDetails($id, $items);

            $response = [
                'success' => true,
                'id' => $id,
                'message' => 'Purchase order updated successfully'
            ];
        } else {
            throw new Exception("Failed to update purchase order.");
        }
    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error updating draft purchase order: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (post('action') === 'get_pr_info') {
    try {
        $item_id = post('item_id');
        $pr_info = PurchaseOrder::getPurchaseRequestInfo($item_id);
        
        if (!empty($pr_info)) {
            $response = ['success' => true, 'pr_numbers' => $pr_info];
        } else {
            $response = ['success' => false, 'message' => 'No Purchase Request found for this item.'];
        }
    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

