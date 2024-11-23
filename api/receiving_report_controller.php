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
                    'item_asset_account_id' => $purchase->item_asset_account_id,
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
                    'taxable_amount' => $purchase->taxable_amount,
                    'vat' => $purchase->vat,

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

if (post('action') === 'add') {
    try {
        // Log received data
        // error_log("Received POST data: " . print_r($_POST, true));

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
        $qty_sold = 0;
        $discount_account_ids = post('discount_account_ids');

        $input_vat_ids = post('output_vat_ids');
        $created_by = $_SESSION['user_name'];

        // Log header data
        error_log("Header data: " . print_r(compact('receive_account_id', 'receive_no', 'vendor_id', 'location', 'terms', 'receive_date', 'receive_due_date', 'memo', 'gross_amount', 'discount_amount', 'net_amount', 'input_vat', 'vatable', 'zero_rated', 'vat_exempt', 'total_amount'), true));


        $items = json_decode($_POST['item_data'], true);
        $purchase_discount_per_item = 0;
        error_log("Decoded item data: " . print_r($items, true));



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
            $discount_account_ids,
            $net_amount,
            $input_vat,
            $input_vat_ids,
            $vatable,
            $zero_rated,
            $vat_exempt,
            $total_amount,
            $items,
            $created_by
        );

        $transaction_id = ReceivingReport::getLastTransactionId();


        foreach ($items as $index => $item) {
            // Validate required fields
            if (!isset($item['item_id'])) {
                error_log("Missing item_id for item at index $index");
                continue;
            }

            // Use null coalescing operator to provide default value of 0 if quantity is not set
            $quantity = floatval($item['quantity'] ?? 0);
            
            // Validate quantity
            if ($quantity <= 0) {
                error_log("Invalid quantity for item_id {$item['item_id']}: $quantity");
                continue;
            }

            $insertedId = ReceivingReport::addItem(
                $transaction_id,
                intval($item['po_id'] ?? 0),
                $item['item_id'],
                intval($item['cost_center_id'] ?? 0),
                $quantity,
                floatval($item['cost'] ?? 0),
                floatval($item['amount'] ?? 0),
                floatval($item['discount_percentage'] ?? 0),
                floatval($item['discount_amount'] ?? 0),
                floatval($item['net_amount_before_input_vat'] ?? 0),
                floatval($item['net_amount'] ?? 0),
                floatval($item['input_vat_percentage'] ?? 0),
                floatval($item['input_vat_amount'] ?? 0),
                floatval($item['cost_per_unit'] ?? 0)
            );

            // Update item quantity
            ReceivingReport::updateItemQuantity($item['item_id'], $quantity);
            
            // Update purchase order details, PO status, and receive status
            ReceivingReport::updatePurchaseOrderDetails(intval($item['po_id'] ?? 0), $item['item_id'], $quantity, $transaction_id);
            
            ReceivingReport::insert_inventory_valuation(
                'Purchase',
                $transaction_id,
                $receive_no,
                $receive_date,
                $vendor_id,
                $item['item_id'],
                $quantity,
                $qty_sold,
                floatval($item['cost'] ?? 0),
                floatval($item['amount'] ?? 0),
                floatval($item['discount_percentage'] ?? 0),
                $purchase_discount_per_item,
                floatval($item['discount_amount'] ?? 0),
                floatval($item['net_amount_before_input_vat'] ?? 0),
                floatval($item['input_vat_percentage'] ?? 0),
                floatval($item['input_vat_amount'] ?? 0),
                floatval($item['net_amount'] ?? 0),
                floatval($item['cost_per_unit'] ?? 0)
            );
        }

        // Send a message to Discord with the user info
        $discordMessage = "**" . $_SESSION['name'] . " received an item delivery!**\n"; // Bold username and action
        $discordMessage .= "-----------------------\n"; // Top border
        $discordMessage .= "**RR No:** `" . post('receive_number') . "`\n"; // Bold "PR No" and use backticks for code block style
        $discordMessage .= "**Memo:** `" . post('memo') . "`\n"; // Bold "Memo" and use backticks for code block style
        $discordMessage .= "-----------------------\n"; // Bottom border
        sendToDiscord($discordMessage); // Send the message to Discord

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
