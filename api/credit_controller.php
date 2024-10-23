<?php

require_once __DIR__ . '/../_init.php';
// Include FPDF library
require_once 'fpdf.php';

//Add check
if (post('action') === 'add') {
    try {

        // Retrieve the last transaction_id
        $transaction_id = CreditMemo::getLastTransactionId();

        $credit_no = post('credit_no');
        $credit_date = post('credit_date');
        $credit_account_id = post('credit_account_id');
        $customer_id = post('customer_id');
        $customer_name = post('customer_name');

        $memo = post('credit_memo');
        $location = post('location');


        $gross_amount = post('gross_amount');
        $net_amount_due = post('net_amount_due');
        $vat_percentage_amount = post('vat_percentage_amount');
        $net_of_vat = post('net_of_vat');
        $tax_withheld_percentage = post('tax_withheld_percentage');
        $tax_withheld_account_id = post('tax_withheld_account_id');

        $tax_withheld_amount = post('tax_withheld_amount');
        $total_amount_due = post('total_amount_due');


        // Add check to prevent double insertion
        $existingRecord = CreditMemo::findByCreditNo($credit_no);

        // if ($existingRecord !== null) {
        //     throw new Exception("Record with Credits No: $credit_no already exists.");
        // }

        $created_by = $_SESSION['user_name'];

        // Decode JSON data (CREDIT MEMO DETAILS ACCOUNT)
        $items = json_decode($_POST['item_data'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }

        // dd($items);


        // Add main form data to database (assuming WriteCheck::add() does this)
        CreditMemo::add($credit_no, $credit_date,  $location, $customer_id, $customer_name, $credit_account_id, $memo, $gross_amount, $net_amount_due, $vat_percentage_amount, $net_of_vat, $tax_withheld_amount, $tax_withheld_percentage, $tax_withheld_account_id, $total_amount_due, $items, $created_by);


        CreditMemo::addCreditBalance($total_amount_due, $customer_id);

        // Send a message to Discord with the user info
        $discordMessage = "**" . $_SESSION['name'] . " created a Credit Memo 💳, credit has been added to customer" . post('customer_name') . "!**\n"; // Bold username and action
        $discordMessage .= "-----------------------\n"; // Top border
        $discordMessage .= "**CR No:** `" . post('credit_no') . "`\n"; // Bold "PR No" and use backticks for code block style
        $discordMessage .= "**Memo:** `" . post('credit_memo') . "`\n"; // Bold "Memo" and use backticks for code block style
        $discordMessage .= "-----------------------\n"; // Bottom border
        sendToDiscord($discordMessage); // Send the message to Discord


        flashMessage('add_credit', 'Credit added successfully.', FLASH_SUCCESS);

        // Prepare the response with just the transaction_id
        $response = ['success' => true, 'id' => $transaction_id + 1];

    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error in credit submission: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// update for create
if (post('action') === 'update_print_status') {
    try {
        $id = post('id');
        $printStatus = post('print_status');

        if (!$id) {
            throw new Exception("Credit Memo ID is required.");
        }

        if (!in_array($printStatus, [1, 2])) {
            throw new Exception("Invalid print status.");
        }

        // Update only the print_status in the database
        $stmt = $connection->prepare("UPDATE credit_memo SET print_status = :status WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':status', $printStatus, PDO::PARAM_INT);
        $result = $stmt->execute();

        if ($result) {
            $response = ['success' => true];
        } else {
            throw new Exception("Failed to update print status.");
        }
    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error updating print status: ' . $ex->getMessage());
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
            throw new Exception("Credit_memo ID is required.");
        }

        $result = CreditMemo::void($id);

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
        $credit_date = post('credit_date');
        $credit_account_id = post('credit_account_id');
        $customer_id = post('customer_id');
        $memo = post('credit_memo');
        $location = post('location');
        $gross_amount = post('gross_amount');
        $net_amount_due = post('net_amount_due');
        $vat_percentage_amount = post('vat_percentage_amount');
        $net_of_vat = post('net_of_vat');
        $tax_withheld_amount = post('tax_withheld_amount');
        $tax_withheld_percentage = post('tax_withheld_percentage');
        $tax_withheld_account_id = post('tax_withheld_account_id');
        $total_amount_due = post('total_amount_due');

        // Decode JSON data (CREDIT MEMO DETAILS ACCOUNT)
        $items = json_decode(post('item_data'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }

        // Validate that credit_account_id is not null
        if (empty($credit_account_id)) {
            throw new Exception("Credit account cannot be empty");
        }

        // Save draft to database
        CreditMemo::saveDraft(
            $credit_date, $customer_id,  $location, $credit_account_id, $memo, $gross_amount, $net_amount_due,
            $vat_percentage_amount, $net_of_vat, $tax_withheld_amount, $tax_withheld_percentage, $tax_withheld_account_id,
            $total_amount_due, $items
        );

        // Send a message to Discord with the user info
        $discordMessage = "**" . $_SESSION['name'] . " made a Credit Memo draft💳!!**\n"; // Bold username and action
        $discordMessage .= "-----------------------\n"; // Top border
        $discordMessage .= "**Date:** `" . post('credit_date') . "`\n"; // Bold "PR No" and use backticks for code block style
        $discordMessage .= "**Memo:** `" . post('credit_memo') . "`\n"; // Bold "Memo" and use backticks for code block style
        $discordMessage .= "-----------------------\n"; // Bottom border
        sendToDiscord($discordMessage); // Send the message to Discord

        $response = ['success' => true];
    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error in saving credit memo draft: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (post('action') === 'update_draft') {
    try {
        $id = (int) post('id');
        $credit_date = (string) post('credit_date');
        $customer_id = (int) post('customer_id');
        $customer_name = (string) post('customer_name');
        $credit_account = (string) post('credit_account_id');
        $memo = (string) post('memo');
        $location =(int) post('location');

        $gross_amount = (float) post('gross_amount');
        $net_amount_due = (float) post('net_amount_due');
        $vat_percentage_amount = (float) post('vat_percentage_amount');
        $net_of_vat = (float) post('net_of_vat');
        $tax_withheld_amount = (float) post('tax_withheld_amount');
        $tax_withheld_percentage = (float) post('tax_withheld_percentage');
        $total_amount_due = (float) post('total_amount_due');
        $items = json_decode(post('item_data'), true);
        $created_by = $_SESSION['user_name'] ?? 'unknown';

        // Validate required fields
        if (!$id || !$items) {
            throw new Exception("Credit ID and items are required.");
        }

        // Log received data for debugging
        error_log('Received data for update_draft: ' . print_r($_POST, true));

        // Call the updateDraft function
        $result = CreditMemo::updateDraft(
            $id, $credit_date, $credit_account,  $location, $customer_id, $customer_name, 
            $memo, $gross_amount, $net_amount_due, $vat_percentage_amount, $net_of_vat, 
            $tax_withheld_percentage, $tax_withheld_amount, $total_amount_due, $items, $created_by
        );

        if ($result['success']) {
            // Send a message to Discord with the user info
            $discordMessage = "**" . $_SESSION['name'] . " updated a Credit Memo Draft 💳!!**\n";
            $discordMessage .= "-----------------------\n";
            $discordMessage .= "**CM No:** `" . post('credit_no') . "`\n";
            $discordMessage .= "**Memo:** `" . $memo . "`\n";
            $discordMessage .= "-----------------------\n";
            sendToDiscord($discordMessage);

            // Success response
            $response = [
                'success' => true,
                'id' => $id,
                'message' => 'Credit memo draft updated successfully'
            ];
        } else {
            throw new Exception($result['message'] ?? "Failed to update credit memo draft.");
        }
    } catch (Exception $ex) {
        // Error handling
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error updating draft credit memo: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}


if (post('action') === 'save_final') {
    try {
        $id = (int) post('id');
        $credit_no = (string) post('credit_no');
        $credit_date = (string) post('credit_date');
        $customer_id = (int) post('customer_id');
        $customer_name = (string) post('customer_name');
        $credit_account_id = (string) post('credit_account_id');
        $memo = (string) post('memo');
        $location =(int) post('location');
        $gross_amount = (float) post('gross_amount');
        $net_amount_due = (float) post('net_amount_due');
        $vat_percentage_amount = (float) post('vat_percentage_amount');
        $net_of_vat = (float) post('net_of_vat');
        $tax_withheld_amount = (float) post('tax_withheld_amount');
        // $tax_withheld_account_id = (int) post('tax_withheld_account_id');
        $tax_withheld_percentage = (int) post('tax_withheld_percentage');

        $total_amount_due = (float) post('total_amount_due');
        $items = json_decode(post('item_data'), true);
        $created_by = $_SESSION['user_name'] ?? 'unknown';

        // Add check to prevent double insertion
        $existingRecord = CreditMemo::findByCreditNo($credit_no);

        if (!$id || !$credit_no || !$items) {
            throw new Exception("Credit ID, number, and items are required.");
        }

        // Log received data
        error_log('Received data: ' . print_r($_POST, true));

        // Update the credit memo record
        $stmt = $connection->prepare("
            UPDATE credit_memo 
            SET status = 0, 
                credit_no = :credit_no
            WHERE id = :id
        ");

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':credit_no', $credit_no, PDO::PARAM_STR);

        $result = $stmt->execute();

        if ($result) {
            // Call the updateDraft function
            CreditMemo::updateDraft(
                $id, $credit_no, $credit_date,  $location, $customer_id, $customer_name, 
                $credit_account_id, $memo, $gross_amount, $net_amount_due, 
                $vat_percentage_amount, $net_of_vat, $tax_withheld_amount, 
                $tax_withheld_percentage, $total_amount_due, $items, $created_by
            );

            CreditMemo::addCreditBalance($total_amount_due, $customer_id);

            // Send a message to Discord with the user info
            $discordMessage = "**" . $_SESSION['name'] . " posted a Credit Memo 💳!!**\n"; // Bold username and action
            $discordMessage .= "-----------------------\n"; // Top border
            $discordMessage .= "**CM No:** `" . post('credit_no') . "`\n"; // Bold "PR No" and use backticks for code block style
            $discordMessage .= "**Memo:** `" . post('memo') . "`\n"; // Bold "Memo" and use backticks for code block style
            $discordMessage .= "-----------------------\n"; // Bottom border
            sendToDiscord($discordMessage); // Send the message to Discord

            $response = [
                'success' => true,
                'id' => $id,
                'message' => 'Credit memo updated successfully'
            ];
        } else {
            throw new Exception("Failed to update credit memo.");
        }
    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error updating draft credit memo: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}