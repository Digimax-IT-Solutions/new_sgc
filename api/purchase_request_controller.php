<?php

require_once __DIR__ . '/../_init.php';



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

        // Send a message to Discord with the user info
        $discordMessage = "**" . $_SESSION['name'] . " created a purchase request!**\n"; // Bold username and action
        $discordMessage .= "-----------------------\n"; // Top border
        $discordMessage .= "**PR No:** `" . post('pr_no') . "`\n"; // Bold "PR No" and use backticks for code block style
        $discordMessage .= "**Memo:** `" . post('memo') . "`\n"; // Bold "Memo" and use backticks for code block style
        $discordMessage .= "-----------------------\n"; // Bottom border
        sendToDiscord($discordMessage); // Send the message to Discord


        $response = ['success' => true, 'id' => $new_pr_id];

    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error in purchase request submission: ' . $ex->getMessage());
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
            throw new Exception("Purchase Request ID is required.");
        }

        if (!in_array($printStatus, [1, 2])) {
            throw new Exception("Invalid print status.");
        }

        // Update the print_status in the database
        $stmt = $connection->prepare("UPDATE purchase_request SET print_status = :status WHERE id = :id");
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

if (post('action') === 'void_check') {
    try {
        $id = post('id');
        if (!$id) {
            throw new Exception("Purchase Request ID is required.");
        }

        $result = PurchaseRequest::void($id);

        if ($result) {
            $response = ['success' => true];
        } else {
            throw new Exception("Failed to void purchase request.");
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        error_log('Error voiding purchase request: ' . $e->getMessage());
    }
    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (post('action') === 'save_draft') {
    try {
        // Retrieve form data
        $location = post('location'); // Ensure this is retrieved if needed for the request
        $date = post('date'); // Ensure this is retrieved if needed for the request
        $required_date = post('required_date'); // Ensure this is retrieved if needed for the request
        $memo = post('memo');
        $items = json_decode(post('item_data'), true);

        // Validate JSON data
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }


        // Save draft to database
        PurchaseRequest::saveDraft(
            $location,
            $date,
            $required_date,
            $memo,
            $items
        );

        // Send a message to Discord with the user info
        $discordMessage = "**" . $_SESSION['name'] . " drafted a purchase request!**\n"; // Bold username and action
        $discordMessage .= "-----------------------\n"; // Top border
        $discordMessage .= "**PR Draft Date:** `" . post('date') . "`\n"; // Bold "PR No" and use backticks for code block style
        $discordMessage .= "**Memo:** `" . post('memo') . "`\n"; // Bold "Memo" and use backticks for code block style
        $discordMessage .= "-----------------------\n"; // Bottom border
        sendToDiscord($discordMessage); // Send the message to Discord

        $response = ['success' => true];
    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error in saving purchase request draft: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (post('action') === 'update_draft') {
    try {
        $id = (int) post('id');
        $location = post('location');
        $date = post('date');
        $required_date = post('required_date');
        $memo = post('memo');

        // Decode JSON data for invoice items
        $items = json_decode(post('item_data'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }

        $response = PurchaseRequest::updateDraft(
            $id, $location, $date, $required_date, $memo, $items
        );

        if ($response['success']) {
            // Send a message to Discord with the user info
            $discordMessage = "**" . $_SESSION['name'] . " posted a purchase request!**\n";
            $discordMessage .= "-----------------------\n";
            $discordMessage .= "**PR No:** `" . post('pr_no') . "`\n";
            $discordMessage .= "**Memo:** `" . post('memo') . "`\n";
            $discordMessage .= "-----------------------\n";
            sendToDiscord($discordMessage);

            $response = [
                'success' => true,
                'id' => $id,
                'message' => 'Purchase request updated successfully'
            ];
        } else {
            throw new Exception("Failed to update purchase request.");
        }
    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error updating draft purchase request: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}


if (post('action') === 'save_final') {
    try {
        $id = (int) post('id');
        $pr_no = post('pr_no');
        $location = post('location');
        $date = post('date');
        $required_date = post('required_date');
        $memo = post('memo');

        // Decode JSON data for invoice items
        $items = json_decode(post('item_data'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }

        $response = PurchaseRequest::saveFinal(
            $id, $pr_no, $location, $date, $required_date, $memo, $items
        );

        if ($response['success']) {
            // Send a message to Discord with the user info
            $discordMessage = "**" . $_SESSION['name'] . " posted a purchase request!**\n";
            $discordMessage .= "-----------------------\n";
            $discordMessage .= "**PR No:** `" . post('pr_no') . "`\n";
            $discordMessage .= "**Memo:** `" . post('memo') . "`\n";
            $discordMessage .= "-----------------------\n";
            sendToDiscord($discordMessage);

            $response = [
                'success' => true,
                'id' => $id,
                'message' => 'Purchase request updated successfully'
            ];
        } else {
            throw new Exception("Failed to update purchase request.");
        }
    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error updating draft purchase request: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}


?>