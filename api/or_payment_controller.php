<?php

require_once __DIR__ . '/../_init.php';


if (post('action') === 'add') {
    try {
        // Retrieve form data
        $or_number = post('or_number');
        $or_date = post('or_date');
        $or_account_id = post('or_account_id');

        if (empty($or_account_id)) {
            throw new Exception("OR account ID is missing or empty.");
        }

        $customer_id = post('customer_id');
        $customer_name = post('customer_name');
        $location = post('location');
        $customer_po = post('customer_po');
        $rep = post('rep');
        $so_no = post('so_no');
        $payment_method = post('payment_method');
        $check_no = post('check_no');
        $memo = post('memo');

        // Summary details
        $gross_amount = post('gross_amount');
        $total_discount_amount = post('total_discount_amount');
        $output_vat_ids = post('output_vat_ids');
        $net_amount_due = post('net_amount_due');
        $vat_amount = post('total_vat_amount');
        $vatable_amount = post('vatable_amount');
        $zero_rated_amount = post('zero_rated_amount');
        $vat_exempt_amount = post('vat_exempt_amount');

        // Tax withholding information
        $tax_withheld_percentage = post('tax_withheld_percentage');
        $tax_withheld_amount = post('tax_withheld_amount');
        $tax_withheld_account_id = post('tax_withheld_account_id');

        // Total amount and user information
        $total_amount_due = post('total_amount_due');
        $created_by = $_SESSION['user_name'];


        // Generate the new CI number by getting the last CI number and incrementing it
        // $ci_no = OfficialReceipt::getLastOrNo();
        $ci_no = post('invoice_no');

        // Check for existing OR to prevent duplicates
        $existingRecord = OfficialReceipt::findByOrNo($or_number);

        if ($existingRecord !== null) {
            throw new Exception("Record with OR #: $or_number already exists.");
        }

        // Get the last transaction ID (if needed for reference)
        $transaction_id = OfficialReceipt::getLastTransactionId();

        // Decode JSON data for items
        $items = json_decode($_POST['item_data'], true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }

        // Call the OfficialReceipt::add method to insert the receipt into the database
        OfficialReceipt::add(
            $ci_no,
            $or_number,
            $or_date,
            $or_account_id,
            $customer_po,
            $so_no,
            $check_no,
            $rep,
            $output_vat_ids,
            $payment_method,
            $location,
            $memo,
            $gross_amount,
            $total_discount_amount,
            $net_amount_due,
            $vat_amount,
            $vatable_amount,
            $zero_rated_amount,
            $vat_exempt_amount,
            $tax_withheld_percentage,
            $tax_withheld_amount,
            $tax_withheld_account_id,
            $total_amount_due,
            $items,
            $customer_id,
            $customer_name,
            $created_by
        );

        // Set success message
        flashMessage('add_invoice', 'Payment Submitted Successfully!', FLASH_SUCCESS);

        // Send a message to Discord with the user info
        $discordMessage = "**" . $_SESSION['name'] . " posted an Official Receipt 💰!**\n"; // Bold username and action
        $discordMessage .= "-----------------------\n"; // Top border
        $discordMessage .= "**OR No:** `" . post('or_number') . "`\n"; // Bold "PR No" and use backticks for code block style
        $discordMessage .= "**Memo:** `" . post('memo') . "`\n"; // Bold "Memo" and use backticks for code block style
        $discordMessage .= "-----------------------\n"; // Bottom border
        sendToDiscord($discordMessage); // Send the message to Discord

        // Prepare the response with just the transaction_id
        $response = ['success' => true, 'id' => $transaction_id + 1];
    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error in payment submission: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// // update for create
if (post('action') === 'update_print_status') {
    try {
        $id = post('id');
        $printStatus = post('print_status');

        if (!$id) {
            throw new Exception("Invoice ID is required.");
        }

        if (!in_array($printStatus, [1, 2])) {
            throw new Exception("Invalid print status.");
        }

        // Update the print_status in the database
        $stmt = $connection->prepare("UPDATE or_payments SET print_status = :status WHERE id = :id");
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
