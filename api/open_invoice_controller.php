<?php

require_once __DIR__ . '/../_init.php';

// error_reporting(E_ALL);
// ini_set('display_errors', 1);

if (post('action') === 'add') {
    try {
        // Retrieve form data
        // OpenInvoice information
        $open_invoice_number = post('open_invoice_number');
        $open_invoice_date = post('open_invoice_date');
        $open_invoice_due_date = post('open_invoice_due_date');
        $open_invoice_account_id = post('open_invoice_account_id');


        if (empty($open_invoice_account_id)) {
            throw new Exception("OpenInvoice account ID is missing or empty.");
        }

        $customer_po = post('customer_po');
        $so_no = post('so_no');
        $rep = post('rep');
        $customer_id = post('customer_id');
        $customer_name = post('customer_name');
        $payment_method = post('payment_method');
        $location = post('location');
        $terms = post('terms');
        $memo = post('memo');

        // Summary details
        $gross_amount = post('gross_amount');

        $discount_amount = post('total_discount_amount');
        $discount_account_ids = post('discount_account_ids');
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

        // Check for existing open_invoice to prevent duplicates
        $existingRecord = OpenInvoice::findByInvoiceNo($open_invoice_number);


        if ($existingRecord !== null) {
            throw new Exception("Record with OpenInvoice #: $open_invoice_number already exists.");
        }

        // Get the last transaction ID (if needed for reference)
        $transaction_id = OpenInvoice::getLastTransactionId();

        // Decode JSON data for open_invoice items
        $items = json_decode($_POST['item_data'], true);


        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }

        // Call the OpenInvoice::add method to insert the open_invoice into the database
        OpenInvoice::add(
            $open_invoice_number,
            $open_invoice_date,
            $open_invoice_account_id,
            $customer_po,
            $so_no,
            $rep,
            $discount_account_ids,
            $output_vat_ids,
            $open_invoice_due_date,
            $customer_id,
            $customer_name,
            $payment_method,
            $location,
            $terms,
            $memo,
            $gross_amount,
            $discount_amount,
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
            $created_by
        );

        // Set success message
        flashMessage('add_open_invoice', 'Sales OpenInvoice Submitted Successfully!', FLASH_SUCCESS);

        // Send a message to Discord with the user info
        $discordMessage = "**" . $_SESSION['name'] . " posted an OpenInvoice!**\n"; // Bold username and action
        $discordMessage .= "-----------------------\n"; // Top border
        $discordMessage .= "**OpenInvoice No:** `" . post('open_invoice_number') . "`\n"; // Bold "PR No" and use backticks for code block style
        $discordMessage .= "**Memo:** `" . post('memo') . "`\n"; // Bold "Memo" and use backticks for code block style
        $discordMessage .= "-----------------------\n"; // Bottom border
        sendToDiscord($discordMessage); // Send the message to Discord

        // Prepare the response with just the transaction_id
        $response = ['success' => true, 'open_invoiceId' => $transaction_id + 1];
    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error in open_invoice submission: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// update for create
if (post('action') === 'update_print_status') {
    try {
        $open_invoiceId = post('open_invoice_id');
        $printStatus = post('print_status');

        if (!$open_invoiceId) {
            throw new Exception("OpenInvoice ID is required.");
        }

        if (!in_array($printStatus, [1, 2])) {
            throw new Exception("Invalid print status.");
        }

        // Update the print_status in the database
        $stmt = $connection->prepare("UPDATE sales_open_invoice SET print_status = :status WHERE id = :id");
        $stmt->bindParam(':id', $open_invoiceId, PDO::PARAM_INT);
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

// Check if action is 'update'
if (post('action') === 'update') {
    try {
        // Retrieve open_invoice ID for update
        $id = post('id');

        // Retrieve form data for update
        $open_invoice_number = post('open_invoice_number');
        $open_invoice_date = post('open_invoice_date');
        $open_invoice_account_id = post('open_invoice_account_id');
        $open_invoice_due_date = post('open_invoice_due_date');
        $customer_id = post('customer_name');
        $payment_method = post('payment_method');
        $location = post('location');
        $terms = post('terms');
        $memo = post('memo');
        $gross_amount = post('gross_amount');
        $total_discount_amount = post('discount_amount');
        $net_amount_due = post('net_amount_due');
        $vat_amount = post('sales_tax_amount');
        $vatable_amount = post('vatable_amount');
        $zero_rated_amount = post('zero_rated_amount');
        $vat_exempt_amount = post('vat_exempt_amount');
        $tax_withheld_percentage = post('tax_withheld_percentage');
        $tax_withheld_amount = post('tax_withheld_amount');
        $total_amount_due = post('total_amount_due');

        // Decode JSON data for items
        $items = json_decode($_POST['item_data'], true);
        // Update open_invoice using OpenInvoice class method
        OpenInvoice::update(
            $id,
            $open_invoice_number,
            $open_invoice_date,
            $open_invoice_account_id,
            $open_invoice_due_date,
            $customer_id,
            $payment_method,
            $location,
            $terms,
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
            $total_amount_due
        );

        // Update open_invoice details using OpenInvoice class method
        OpenInvoice::updateDetails($id, $items);

        flashMessage('update_open_invoice', 'OpenInvoice updated successfully.', FLASH_SUCCESS);
    } catch (Exception $e) {
        // Handle any exceptions that occur during update
        flashMessage('update_open_invoice', $e->getMessage(), FLASH_ERROR);
    }

    // Redirect after processing 'update' action
    redirect('../open_invoice');
}

// Add this to the existing switch statement or if-else block
if (post('action') === 'void_check') {
    try {
        $id = post('id');
        if (!$id) {
            throw new Exception("OpenInvoice ID is required.");
        }

        $result = OpenInvoice::voidInvoice($id);

        if ($result) {
            $response = ['success' => true];
        } else {
            throw new Exception("Failed to void open_invoice.");
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        error_log('Error voiding open_invoice: ' . $e->getMessage());
    }
    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (post('action') === 'save_draft') {
    try {
        // Retrieve form data
        $open_invoice_date = post('open_invoice_date');
        $open_invoice_due_date = post('open_invoice_due_date');
        $open_invoice_account_id = post('open_invoice_account_id');
        $customer_po = post('customer_po');
        $so_no = post('so_no');
        $rep = post('rep');
        $customer_id = post('customer_id');
        $customer_name = post('customer_name');
        $payment_method = post('payment_method');
        $location = post('location');
        $terms = post('terms');
        $memo = post('memo');

        // Summary details
        $gross_amount = post('gross_amount');
        $gross_amount = str_replace(',', '', $gross_amount); // Remove thousands separator
        $discount_amount = post('total_discount_amount');
        $discount_amount = str_replace(',', '', $discount_amount); // Remove thousands separator
        $discount_account_ids = post('discount_account_ids');
        $output_vat_ids = post('output_vat_ids');
        $net_amount_due = post('net_amount_due');
        $net_amount_due = str_replace(',', '', $net_amount_due); // Remove thousands separator
        $vat_amount = post('total_vat_amount');
        $vat_amount = str_replace(',', '', $vat_amount); // Remove thousands separator
        $vatable_amount = post('vatable_amount');
        $vatable_amount = str_replace(',', '', $vatable_amount); // Remove thousands separator
        $zero_rated_amount = post('zero_rated_amount');
        $zero_rated_amount = str_replace(',', '', $zero_rated_amount); // Remove thousands separator
        $vat_exempt_amount = post('vat_exempt_amount');
        $vat_exempt_amount = str_replace(',', '', $vat_exempt_amount); // Remove thousands separator

        // Tax withholding information
        $tax_withheld_percentage = post('tax_withheld_percentage');
        $tax_withheld_amount = post('tax_withheld_amount');
        $tax_withheld_amount = str_replace(',', '', $tax_withheld_amount); // Remove thousands separator
        $tax_withheld_account_id = post('tax_withheld_account_id');

        // Total amount and user information
        $total_amount_due = post('total_amount_due');
        $total_amount_due = str_replace(',', '', $total_amount_due); // Remove thousands separator


        // Decode JSON data for open_invoice items
        $items = json_decode($_POST['item_data'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }
        // Sanitize the numeric fields in the items array
        foreach ($items as &$item) {
            if (isset($item['quantity'])) {
                $item['quantity'] = str_replace(',', '', $item['quantity']); // Remove commas from quantity
            }
            if (isset($item['cost'])) {
                $item['cost'] = str_replace(',', '', $item['cost']); // Remove commas from cost
            }
            if (isset($item['amount'])) {
                $item['amount'] = str_replace(',', '', $item['amount']); // Remove commas from amount
            }
            if (isset($item['discount_percentage'])) {
                $item['discount_percentage'] = str_replace(',', '', $item['discount_percentage']); // Remove commas from discount percentage
            }
            if (isset($item['discount_amount'])) {
                $item['discount_amount'] = str_replace(',', '', $item['discount_amount']); // Remove commas from discount amount
            }
            if (isset($item['net_amount_before_sales_tax'])) {
                $item['net_amount_before_sales_tax'] = str_replace(',', '', $item['net_amount_before_sales_tax']); // Remove commas from net amount before sales tax
            }
            if (isset($item['net_amount'])) {
                $item['net_amount'] = str_replace(',', '', $item['net_amount']); // Remove commas from net amount
            }
            if (isset($item['sales_tax_percentage'])) {
                $item['sales_tax_percentage'] = str_replace(',', '', $item['sales_tax_percentage']); // Remove commas from sales tax percentage
            }
            if (isset($item['sales_tax_amount'])) {
                $item['sales_tax_amount'] = str_replace(',', '', $item['sales_tax_amount']); // Remove commas from sales tax amount
            }
        }


        // Call the OpenInvoice::addDraft method to insert the draft open_invoice into the database
        $result = OpenInvoice::addDraft(
            $open_invoice_date,
            $open_invoice_account_id,
            $customer_po,
            $so_no,
            $rep,
            $open_invoice_due_date,
            $customer_id,
            $payment_method,
            $location,
            $terms,
            $memo,
            $gross_amount,
            $discount_amount,
            $net_amount_due,
            $vat_amount,
            $vatable_amount,
            $zero_rated_amount,
            $vat_exempt_amount,
            $tax_withheld_percentage,
            $tax_withheld_amount,
            $total_amount_due,
            $items
        );

        // Send a message to Discord with the user info
        $discordMessage = "**" . $_SESSION['name'] . " generated a draft open_invoice!**\n"; // Bold username and action
        $discordMessage .= "-----------------------\n"; // Top border
        $discordMessage .= "**OpenInvoice No:** `" . post('open_invoice_number') . "`\n"; // Bold "PR No" and use backticks for code block style
        $discordMessage .= "**Memo:** `" . post('memo') . "`\n"; // Bold "Memo" and use backticks for code block style
        $discordMessage .= "-----------------------\n"; // Bottom border
        sendToDiscord($discordMessage); // Send the message to Discord

        $response = $result ?
            ['success' => true, 'message' => 'OpenInvoice saved as draft successfully'] :
            throw new Exception("Failed to save open_invoice as draft.");
    } catch (Exception $ex) {
        error_log('Error in saving draft open_invoice: ' . $ex->getMessage());
        error_log('Stack trace: ' . $ex->getTraceAsString());
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (post('action') === 'update_draft') {
    $id = post('id');
    // OpenInvoice information
    $open_invoice_date = post('open_invoice_date');
    $open_invoice_due_date = post('open_invoice_due_date');
    $open_invoice_account_id = post('open_invoice_account_id');

    if (empty($open_invoice_account_id)) {
        throw new Exception("OpenInvoice account ID is missing or empty.");
    }
    $customer_po = post('customer_po');
    $so_no = post('so_no');
    $rep = post('rep');
    $customer_id = post('customer_id');
    $customer = Customer::find($customer_id);
    $customer_name = $customer ? $customer->customer_name : '';
    $payment_method = post('payment_method');
    $location = post('location');
    $terms = post('terms');
    $memo = post('memo');

    // Summary details
    $gross_amount = post('gross_amount');
    $gross_amount = str_replace(',', '', $gross_amount); // Remove thousands separator
    $discount_amount = post('discount_amount');
    $discount_amount = str_replace(',', '', $discount_amount); // Remove thousands separator
    $discount_account_ids = post('discount_account_ids');
    $output_vat_ids = post('output_vat_ids');
    $net_amount_due = post('net_amount_due');
    $net_amount_due = str_replace(',', '', $net_amount_due); // Remove thousands separator
    $vat_amount = post('vat_amount');
    $vat_amount = str_replace(',', '', $vat_amount); // Remove thousands separator
    $vatable_amount = post('vatable_amount');
    $vatable_amount = str_replace(',', '', $vatable_amount); // Remove thousands separator
    $zero_rated_amount = post('zero_rated_amount');
    $zero_rated_amount = str_replace(',', '', $zero_rated_amount); // Remove thousands separator
    $vat_exempt_amount = post('vat_exempt_amount');
    $vat_exempt_amount = str_replace(',', '', $vat_exempt_amount); // Remove thousands separator

    // Tax withholding information
    $tax_withheld_percentage = post('tax_withheld_percentage');
    $tax_withheld_amount = post('tax_withheld_amount');
    $tax_withheld_amount = str_replace(',', '', $tax_withheld_amount); // Remove thousands separator
    $tax_withheld_account_id = post('tax_withheld_account_id');

    // Total amount and user information
    $total_amount_due = post('total_amount_due');
    $total_amount_due = str_replace(',', '', $total_amount_due); // Remove thousands separator
    $created_by = $_SESSION['user_name'];

    // Decode JSON data for open_invoice items
    $items = json_decode($_POST['item_data'], true);


    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error decoding item data: " . json_last_error_msg());
    }

    // Sanitize the numeric fields in the items array
    foreach ($items as &$item) {
        $item['quantity'] = !empty($item['quantity']) ? str_replace(',', '', $item['quantity']) : null;
        $item['cost'] = !empty($item['cost']) ? str_replace(',', '', $item['cost']) : null;
        $item['amount'] = !empty($item['amount']) ? str_replace(',', '', $item['amount']) : null;
        $item['discount_percentage'] = !empty($item['discount_percentage']) ? str_replace(',', '', $item['discount_percentage']) : null;
        $item['discount_amount'] = !empty($item['discount_amount']) ? str_replace(',', '', $item['discount_amount']) : null;
        $item['net_amount_before_sales_tax'] = !empty($item['net_amount_before_sales_tax']) ? str_replace(',', '', $item['net_amount_before_sales_tax']) : null;
        $item['net_amount'] = !empty($item['net_amount']) ? str_replace(',', '', $item['net_amount']) : null;
        $item['sales_tax_percentage'] = !empty($item['sales_tax_percentage']) ? str_replace(',', '', $item['sales_tax_percentage']) : null;
        $item['sales_tax_amount'] = !empty($item['sales_tax_amount']) ? str_replace(',', '', $item['sales_tax_amount']) : null;

        // Handle potentially empty account IDs
        $item['output_vat_id'] = !empty($item['output_vat_id']) ? $item['output_vat_id'] : null;
        $item['discount_account_id'] = !empty($item['discount_account_id']) ? $item['discount_account_id'] : null;
        $item['cogs_account_id'] = !empty($item['cogs_account_id']) ? $item['cogs_account_id'] : null;
        $item['income_account_id'] = !empty($item['income_account_id']) ? $item['income_account_id'] : null;
        $item['asset_account_id'] = !empty($item['asset_account_id']) ? $item['asset_account_id'] : null;
    }

    $response = OpenInvoice::updateDraft(
        $id,
        $open_invoice_date,
        $open_invoice_account_id,
        $customer_po,
        $so_no,
        $rep,
        $discount_account_ids,
        $output_vat_ids,
        $open_invoice_due_date,
        $customer_id,
        $customer_name,
        $payment_method,
        $location,
        $terms,
        $memo,
        $gross_amount,
        $discount_amount,
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
        $created_by
    );

    if ($response['success']) {
        // Send a message to Discord with the user info
        $discordMessage = "**" . $_SESSION['name'] . " updated an OpenInvoice!**\n"; // Bold username and action
        $discordMessage .= "-----------------------\n"; // Top border
        $discordMessage .= "**Memo:** `" . $memo . "`\n"; // Bold "Memo" and use backticks for code block style
        $discordMessage .= "-----------------------\n"; // Bottom border
        sendToDiscord($discordMessage); // Send the message to Discord
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}


if (post('action') === 'save_final') {
    $id = post('id');
    // OpenInvoice information
    $open_invoice_number = post('open_invoice_number');
    $open_invoice_date = post('open_invoice_date');
    $open_invoice_due_date = post('open_invoice_due_date');
    $open_invoice_account_id = post('open_invoice_account_id');

    if (empty($open_invoice_account_id)) {
        throw new Exception("OpenInvoice account ID is missing or empty.");
    }
    $customer_po = post('customer_po');
    $so_no = post('so_no');
    $rep = post('rep');
    $customer_id = post('customer_id');
    $customer = Customer::find($customer_id);
    $customer_name = $customer ? $customer->customer_name : '';
    $payment_method = post('payment_method');
    $location = post('location');
    $terms = post('terms');
    $memo = post('memo');

    // Summary details
    $gross_amount = post('gross_amount');
    $gross_amount = str_replace(',', '', $gross_amount); // Remove thousands separator
    $discount_amount = post('discount_amount');
    $discount_amount = str_replace(',', '', $discount_amount); // Remove thousands separator
    $discount_account_ids = post('discount_account_ids');
    $output_vat_ids = post('output_vat_ids');
    $net_amount_due = post('net_amount_due');
    $net_amount_due = str_replace(',', '', $net_amount_due); // Remove thousands separator
    $vat_amount = post('vat_amount');
    $vat_amount = str_replace(',', '', $vat_amount); // Remove thousands separator
    $vatable_amount = post('vatable_amount');
    $vatable_amount = str_replace(',', '', $vatable_amount); // Remove thousands separator
    $zero_rated_amount = post('zero_rated_amount');
    $zero_rated_amount = str_replace(',', '', $zero_rated_amount); // Remove thousands separator
    $vat_exempt_amount = post('vat_exempt_amount');
    $vat_exempt_amount = str_replace(',', '', $vat_exempt_amount); // Remove thousands separator

    // Tax withholding information
    $tax_withheld_percentage = post('tax_withheld_percentage');
    $tax_withheld_amount = post('tax_withheld_amount');
    $tax_withheld_amount = str_replace(',', '', $tax_withheld_amount); // Remove thousands separator
    $tax_withheld_account_id = post('tax_withheld_account_id');

    // Total amount and user information
    $total_amount_due = post('total_amount_due');
    $total_amount_due = str_replace(',', '', $total_amount_due); // Remove thousands separator
    $created_by = $_SESSION['user_name'];

    // Decode JSON data for open_invoice items
    $items = json_decode($_POST['item_data'], true);


    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error decoding item data: " . json_last_error_msg());
    }

    // Sanitize the numeric fields in the items array
    foreach ($items as &$item) {
        $item['quantity'] = !empty($item['quantity']) ? str_replace(',', '', $item['quantity']) : null;
        $item['cost'] = !empty($item['cost']) ? str_replace(',', '', $item['cost']) : null;
        $item['amount'] = !empty($item['amount']) ? str_replace(',', '', $item['amount']) : null;
        $item['discount_percentage'] = !empty($item['discount_percentage']) ? str_replace(',', '', $item['discount_percentage']) : null;
        $item['discount_amount'] = !empty($item['discount_amount']) ? str_replace(',', '', $item['discount_amount']) : null;
        $item['net_amount_before_sales_tax'] = !empty($item['net_amount_before_sales_tax']) ? str_replace(',', '', $item['net_amount_before_sales_tax']) : null;
        $item['net_amount'] = !empty($item['net_amount']) ? str_replace(',', '', $item['net_amount']) : null;
        $item['sales_tax_percentage'] = !empty($item['sales_tax_percentage']) ? str_replace(',', '', $item['sales_tax_percentage']) : null;
        $item['sales_tax_amount'] = !empty($item['sales_tax_amount']) ? str_replace(',', '', $item['sales_tax_amount']) : null;

        // Handle potentially empty account IDs
        $item['output_vat_id'] = !empty($item['output_vat_id']) ? $item['output_vat_id'] : null;
        $item['discount_account_id'] = !empty($item['discount_account_id']) ? $item['discount_account_id'] : null;
        $item['cogs_account_id'] = !empty($item['cogs_account_id']) ? $item['cogs_account_id'] : null;
        $item['income_account_id'] = !empty($item['income_account_id']) ? $item['income_account_id'] : null;
        $item['asset_account_id'] = !empty($item['asset_account_id']) ? $item['asset_account_id'] : null;
    }

    // Check for existing open_invoice to prevent duplicates
    $existingRecord = OpenInvoice::findByInvoiceNo($open_invoice_number);


    if ($existingRecord !== null) {
        throw new Exception("Record with OpenInvoice #: $open_invoice_number already exists.");
    }

    $response = OpenInvoice::saveFinal(
        $id,
        $open_invoice_number,
        $open_invoice_date,
        $open_invoice_account_id,
        $customer_po,
        $so_no,
        $rep,
        $discount_account_ids,
        $output_vat_ids,
        $open_invoice_due_date,
        $customer_id,
        $customer_name,
        $payment_method,
        $location,
        $terms,
        $memo,
        $gross_amount,
        $discount_amount,
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
        $created_by
    );

    if ($response['success']) {
        // Send a message to Discord with the user info
        $discordMessage = "**" . $_SESSION['name'] . " save an OpenInvoice!**\n"; // Bold username and action
        $discordMessage .= "-----------------------\n"; // Top border
        $discordMessage .= "**OpenInvoice No:** `" . $open_invoice_number . "`\n"; // Bold "OpenInvoice No" and use backticks for code block style
        $discordMessage .= "**Memo:** `" . $memo . "`\n"; // Bold "Memo" and use backticks for code block style
        $discordMessage .= "-----------------------\n"; // Bottom border
        sendToDiscord($discordMessage); // Send the message to Discord
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
