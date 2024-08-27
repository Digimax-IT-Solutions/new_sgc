<?php

require_once __DIR__ . '/../_init.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    if ($_GET['action'] === 'get_purchase_order') {
        $vendorId = $_GET['vendor_id'] ?? '';

        if (empty($vendorId)) {
            echo json_encode(['purchase_orders' => []]);
            exit;
        }

        try {
            $purchases = PurchaseOrder::getPurchaseOrderByVendorId($vendorId);
            $formattedPurchases = [];

            foreach ($purchases as $purchase) {
                $formattedPurchases[] = [
                    'id' => $purchase->po_id,
                    'purchase_order_detail_id' => $purchase->purchase_order_detail_id,
                    'po_account_id' => $purchase->po_account_id,
                    'po_no' => $purchase->po_no,
                    'date' => $purchase->date,
                    'delivery_date' => $purchase->delivery_date,
                    'cost_center_id' => $purchase->cost_center_id,
                    'cost_center_name' => $purchase->cost_center_name,
                    'item_id' => $purchase->item_id,
                    'item' => $purchase->item,
                    'description' => $purchase->description,
                    'unit' => $purchase->unit,
                    'qty' => $purchase->qty,
                    'quantity' => $purchase->quantity,
                    'received_qty' => $purchase->received_qty,
                    'cost' => $purchase->cost,
                    'amount' => $purchase->amount,
                    'discount_percentage' => $purchase->discount_percentage,
                    'discount' => $purchase->discount,
                    'net' => $purchase->net,
                    'tax_amount' => $purchase->tax_amount,
                    'input_vat_percentage' => $purchase->input_vat_percentage,
                    'vat' => $purchase->vat
                ];
            }

            echo json_encode(['purchase_orders' => $formattedPurchases]);
        } catch (Exception $e) {
            error_log("Error in receiving_report_controller: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
        }
        exit;
    }
}


//////////////////////////////////////////////////////////////////////////////

if (post('action') === 'add') {
    try {
        // Log received data
        error_log("Received POST data: " . print_r($_POST, true));

        $receive_account_id = post('account_id');
        $receive_no = post('receive_number');
        $vendor_id = post('vendor_name');
        $location = post('location');
        $terms = post('terms');
        $receive_date = post('receive_date');
        $receive_due_date = post('receive_due_date');
        $memo = post('memo');
        $gross_amount = post('gross_amount');
        $discount_amount = post('total_discount_amount');
        $net_amount = post('net_amount_due');
        $input_vat = post('total_vat_amount');
        $vatable = post('vatable_amount');
        $zero_rated = post('zero_rated_amount');
        $vat_exempt = post('vat_exempt_amount');
        $total_amount = post('total_amount_due');

        // Log header data
        error_log("Header data: " . print_r(compact('receive_account_id', 'receive_no', 'vendor_id', 'location', 'terms', 'receive_date', 'receive_due_date', 'memo', 'gross_amount', 'discount_amount', 'net_amount', 'input_vat', 'vatable', 'zero_rated', 'vat_exempt', 'total_amount'), true));

        // Insert header information
        ReceivingReport::add(
            $receive_account_id,
            $receive_no,
            $vendor_id,
            $location,
            $terms,
            $receive_date,
            $receive_due_date,
            $memo,
            $gross_amount,
            $discount_amount,
            $net_amount,
            $input_vat,
            $vatable,
            $zero_rated,
            $vat_exempt,
            $total_amount,
            []  // Empty array for details, as we'll insert them separately
        );

        $transaction_id = ReceivingReport::getLastTransactionId();
        $items = json_decode($_POST['item_data'], true);
        error_log("Decoded item data: " . print_r($items, true));

        foreach ($items as $index => $item) {
            $item_id = $item['item_id'] ?? null;
            if (!$item_id) {
                error_log("Missing item_id for item at index $index");
                continue;
            }
            $quantity = floatval($item['quantity']);
            if ($quantity <= 0) {
                error_log("Invalid quantity for item_id $item_id: $quantity");
                continue;
            }

            $insertedId = ReceivingReport::addItem(
                $transaction_id,
                intval($item['po_id']),
                $item_id,
                intval($item['cost_center_id']),
                $quantity,
                floatval($item['cost']),
                floatval($item['amount']),
                floatval($item['discount_percentage']),
                floatval($item['discount_amount']),
                floatval($item['net_amount_before_input_vat']),
                floatval($item['net_amount']),
                floatval($item['input_vat_percentage']),
                floatval($item['input_vat_amount'])
            );

            error_log("Inserted receive item detail with ID: $insertedId");

            // Update item quantity
            ReceivingReport::updateItemQuantity($item_id, $quantity);

            // Update purchase order details, PO status, and receive status
            ReceivingReport::updatePurchaseOrderDetails(intval($item['po_id']), $item_id, $quantity, $transaction_id);
        }

        echo json_encode(['success' => true, 'id' => $transaction_id]);
        flashMessage('add_received_items', 'Received Items Successfully added.', FLASH_SUCCESS);
    } catch (Exception $e) {
        error_log("Error in receiving_report_controller: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

if (post('action') === 'update_print_status') {
    try {
        $id = post('id');
        $printStatus = post('print_status');

        if (!$id) {
            throw new Exception("Received Items ID is required.");
        }

        if (!in_array($printStatus, [1, 2])) {
            throw new Exception("Invalid print status.");
        }

        // Update the print_status in the database
        $stmt = $connection->prepare("UPDATE receive_items SET print_status = :status WHERE id = :id");
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
