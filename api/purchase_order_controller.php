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
        $location = post('location');


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
            $po_no, $po_date, $delivery_date, $vendor_id, $terms, $gross_amount, $discount_amount, $net_amount_due, $input_vat_amount, $vatable_amount, $zero_rated_amount, $vat_exempt_amount, $total_amount_due, $memo, $location, $items
        );

        // You can output a success message or redirect the user to another page after successful processing.
        flashMessage('add_purchase_order', 'Purchase order added!.', FLASH_SUCCESS);

        // Send a message to Discord with the user info
        // Send a message to Discord with the user info
        $discordMessage = "**" . $_SESSION['name'] . " created a purchase order!**\n"; // Bold username and action
        $discordMessage .= "-----------------------\n"; // Top border
        $discordMessage .= "**PO No:** `" . post('po_no') . "`\n"; // Bold "PR No" and use backticks for code block style
        $discordMessage .= "**Memo:** `" . post('memo') . "`\n"; // Bold "Memo" and use backticks for code block style
        $discordMessage .= "-----------------------\n"; // Bottom border
        sendToDiscord($discordMessage); // Send the message to Discord

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
        $location = post('location');

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
            $location,
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
        $location = post('location');
        $memo = post('memo');
        $items = json_decode(post('item_data'), true);

        // Call the updateDraft function
        $response = PurchaseOrder::updateDraft(
            $id, $po_date, $delivery_date, $vendor_id, $terms, $gross_amount,
            $discount_amount, $net_amount_due, $input_vat_amount, $vatable_amount, 
            $zero_rated_amount, $vat_exempt_amount, $total_amount_due, $memo, $location, $items
        );
    
        // Check if the response indicates success
        if ($response['success']) {
            // Success response
            $response = [
                'success' => true,
                'id' => $id,
                'message' => 'Purchase order updated successfully'
            ];
        } else {
            // Handle failure
            throw new Exception($response['message']);
        }
    } catch (Exception $ex) {
        // Error handling
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error updating draft purchase order: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}


if (post('action') === 'save_final') {
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
// Fetch items by PR No.
if (post('action') === 'get_items_by_pr_no') {
    try {
        $pr_no = post('pr_no');
        $items = PurchaseOrder::getItemsByPRNo($pr_no);

        if (!empty($items)) {
            $response = ['success' => true, 'items' => $items];
        } else {
            $response = ['success' => false, 'message' => 'No items found for this Purchase Request.'];
        }
    } catch (Exception $ex) {
        error_log("Exception: " . $ex->getMessage());
        $response = ['success' => false, 'message' => 'An error occurred while processing your request. Please try again later.'];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Check if item is in the selected PR
if (post('action') === 'check_item_in_pr') {
    try {
        $pr_no = post('pr_no');
        $item_id = post('item_id');

        $isItemInPR = PurchaseOrder::isItemInPR($pr_no, $item_id);

        if ($isItemInPR) {
            $response = ['success' => true];
        } else {
            $response = ['success' => false, 'message' => 'Item not found in the selected Purchase Request.'];
        }
    } catch (Exception $ex) {
        error_log("Exception: " . $ex->getMessage());
        $response = ['success' => false, 'message' => 'An error occurred while processing your request. Please try again later.'];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (post('action') === 'get_pr_quantity') {
    try {
        $pr_no = post('pr_no');
        $item_id = post('item_id');

        $quantity = PurchaseOrder::getPRQuantity($pr_no, $item_id);

        if ($quantity !== null) {
            $response = ['success' => true, 'quantity' => $quantity];
        } else {
            $response = ['success' => false, 'message' => 'No quantity found for this PR.'];
        }
    } catch (Exception $ex) {
        error_log("Exception: " . $ex->getMessage());
        error_log("File: " . $ex->getFile());
        error_log(message: "Line: " . $ex->getLine());

        $response = ['success' => false, 'message' => 'An error occurred while processing your request. Please try again later.'];
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
