<?php

require_once __DIR__ . '/../_init.php';

if (post('action') === 'add') {
    try {
        $itemData = json_decode($_POST['item_data'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }

        $data = [
            'mis_no' => post('mis_no'),
            'location' => post('location'),
            'purpose' => post('purpose'),
            'date' => post('date'),
            'item_data' => $itemData
        ];

        $new_mis_id = MaterialIssuance::add($data);

        flashMessage('add_material_issuance', 'Material Issuance added successfully!', FLASH_SUCCESS);

        $response = ['success' => true, 'id' => $new_mis_id];
    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error in material issuance submission: ' . $ex->getMessage());
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
            throw new Exception("Material Issuance ID is required.");
        }

        if (!in_array($printStatus, [1, 2])) {
            throw new Exception("Invalid print status.");
        }

        // Update the print_status in the database
        $stmt = $connection->prepare("UPDATE material_issuance SET print_status = :status WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $printStatus, PDO::PARAM_INT);
        $result = $stmt->execute();

        if ($result) {
            $response = ['success' => true, 'id' => $id];
        } else {
            throw new Exception("Failed to update print status.");
        }
    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error in material issuance print status update: ' . $ex->getMessage());
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}


