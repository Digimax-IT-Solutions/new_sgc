<?php

require_once __DIR__ . '/../../_init.php';

if (post('action') === 'add') {
    try {
        $itemData = json_decode($_POST['item_data'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }

        $data = [
            'pr_no' => post('pr_no'),
            'location' => post('location'),
            'date' => post('date'),
            'required_date' => post('required_date'),
            'memo' => post('memo'),
            'item_data' => $itemData
        ];

        $new_pr_id = PurchaseRequest::add($data);

        flashMessage('add_purchase_request', 'Purchase request added successfully!', FLASH_SUCCESS);

        $response = ['success' => true, 'id' => $new_pr_id];

    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error in purchase request submission: ' . $ex->getMessage());
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

