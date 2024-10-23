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
                    'quantity' => $purchase->quantity,
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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    try {
        // Collect form data
        // $receivingData = [
        //     'vendor_id' => $_POST['vendor_id'],
        //     'location' => $_POST['location'],
        //     'receive_number' => $_POST['receive_number'],
        //     'receive_date' => $_POST['receive_date'],
        //     'terms' => $_POST['terms'],
        //     'receive_due_date' => $_POST['receive_due_date'],
        //     'account_id' => $_POST['account_id'], // ACCOUNTS PAYABLE ACCOUNT
        //     'memo' => $_POST['memo'],
        //     // Add any other fields you need
        // ];



        // Start a transaction
        $vendorId = $_POST['vendor_name'];
        $location = $_POST['location'];
        $receiveNumber = $_POST['receive_number'];
        $receiveDate = $_POST['receive_date'];
        $terms = $_POST['terms'];
        $receiveDueDate = $_POST['receive_due_date'];
        $accountId = $_POST['account_id'];
        $memo = $_POST['memo'];

        $gross_amount = post('gross_amount');
        $discount_amount = post('total_discount_amount');
        $net_amount_due = post('net_amount_due');
        $input_vat_amount = post('total_input_vat_amount');
        $vatable_amount = post('vatable_amount');
        $zero_rated_amount = post('zero_rated_amount');
        $vat_exempt_amount = post('vat_exempt_amount');
        $total_amount_due = post('total_amount_due');

        WarehouseReceiveItem::add(
            $vendorId,
            $location,
            $receiveNumber,
            $receiveDate,
            $terms,
            $receiveDueDate,
            $accountId,
            $memo,
            $gross_amount,
            $discount_amount,
            $net_amount_due,
            $input_vat_amount,
            $vatable_amount,
            $zero_rated_amount,
            $vat_exempt_amount,
            $total_amount_due, // Assuming you're linking this to a specific purchase order
        );


        // Retrieve the last transaction_id
        $transaction_id = WarehouseReceiveItem::getLastTransactionId();


        // Decode the JSON string of item data
        $items = json_decode($_POST['item_data'], true);



        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid item data format');
        }

        dd($items);



        // $receive_data = [
        //     'receive_item_id' => $transaction_id,
        //     'vendor_name' => $_POST['vendor_name'],
        //     'location' => $_POST['location'],
        //     'receive_number' => $_POST['receive_number'],
        //     'receive_date' => $_POST['receive_date'],
        //     'terms' => $_POST['terms'],
        //     'receive_due_date' => $_POST['receive_due_date'],
        //     'account_id' => $_POST['account_id'],
        //     'memo' => $_POST['memo'],
        //     'gross_amount' => post('gross_amount'),
        //     'discount_amount' => post('total_discount_amount'),
        //     'net_amount_due' => post('net_amount_due'),
        //     'input_vat_amount' => post('total_input_vat_amount'),
        //     'vatable_amount' => post('vatable_amount'),
        //     'zero_rated_amount' => post('zero_rated_amount'),
        //     'vat_exempt_amount' => post('vat_exempt_amount'),
        //     'total_amount_due' => post('total_amount_due'),
        //     'item_data' => $itemData
        // ];

        // dd($receive_data);




        // Send a success response
        echo json_encode(['success' => true, 'message' => 'Receiving report created successfully', 'id' => $reportId]);

    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $db->rollBack();

        // Send an error response
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }

    redirect('../warehouse_receive_items');
}