<?php

require_once __DIR__ . '/../_init.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);




// ==========================================================================
// WARNING: DO NOT MODIFY THE CODE BELOW!
// Version: 1.0.0 (First working version)
// Last Updated: [7/26/2024]
//
// This method has been finalized and locked for modifications.
// This section is critical for the functionality of the invoice processing.
// Any changes might cause unexpected behavior or system failures.
// If changes are absolutely necessary, consult with the lead developer
// and thoroughly test all affected systems before deployment.
//
// Change Log:
// v1.0.0 - Initial working version implemented and tested
// ==========================================================================

// if (post('action') === 'add') {
//     try {

//          // Get the last transaction ID (if needed for reference)
//          $transaction_id = OfficialReceipt::getLastTransactionId();


//         // Decode JSON data for invoice items
//         $items = json_decode($_POST['item_data'], true);

//         if (json_last_error() !== JSON_ERROR_NONE) {
//             throw new Exception("Error decoding item data: " . json_last_error_msg());
//         }

//         $data = [
//             'customer_id' => post('customer_id'),
//             'customer_name' => post('customer_name'),
//             'location' => post('location'),
//             'customer_po' => post('customer_po'),
//             'rep' => post('rep'),
//             'so_no' => post('so_no'),
//             'or_number' => post('or_number'),
//             'or_date' => post('or_date'),
//             'or_account_id' => post('or_account_id'),
//             'payment_method' => post('payment_method'),
//             'check_no' => post('check_no'),
//             'memo' => post('memo'),
//             'gross_amount' => post('gross_amount'),
//             'total_discount_amount' => post('total_discount_amount'),
//             'net_amount_due' => post('net_amount_due'),
//             'total_vat_amount' => post('total_vat_amount'),
//             'vatable_amount' => post('vatable_amount'),
//             'zero_rated_amount' => post('zero_rated_amount'),
//             'vat_exempt_amount' => post('vat_exempt_amount'),
//             'output_vat_ids' => post('output_vat_ids'),
//             'tax_withheld_percentage' => post('tax_withheld_percentage'),
//             'tax_withheld_amount' => post('tax_withheld_amount'),
//             'tax_withheld_account_id' => post('tax_withheld_account_id'),
//             'total_amount_due' => post('total_amount_due'),
//             'created_by' => $_SESSION['user_name'],
//             'items' => $items  // Add the items to the data array
//         ];

//         $result = OfficialReceipt::add($data);

//         // Set success message
//         flashMessage('add_invoice', 'Payment Submitted Successfully!', FLASH_SUCCESS);

//         // Prepare the response
//         $response = ['success' => true, 'or_id' => $transaction_id + 1];

//     } catch (Exception $ex) {
//         $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
//         error_log('Error in payment submission: ' . $ex->getMessage());
//     }

//     // Send JSON response
//     header('Content-Type: application/json');
//     echo json_encode($response);
//     exit;
// }


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
        $ci_no = OfficialReceipt::getLastOrNo();

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

// ==========================================================================
// WARNING: DO NOT MODIFY T HE CODE ABOVE!
// This section is critical for the functionality of the invoice processing.
// Any changes might cause unexpected behavior or system failures.
// ==========================================================================

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



// // Check if action is 'update'
// if (post('action') === 'update') {
//     try {
//         // Retrieve invoice ID for update
//         $id = post('id');

//         // Retrieve form data for update
//         $invoice_number = post('invoice_number');
//         $invoice_date = post('invoice_date');
//         $invoice_account_id = post('invoice_account_id');
//         $invoice_due_date = post('invoice_due_date');
//         $customer_id = post('customer_name');
//         $payment_method = post('payment_method');
//         $location = post('location');
//         $terms = post('terms');
//         $memo = post('memo');
//         $gross_amount = post('gross_amount');
//         $total_discount_amount = post('discount_amount');
//         $net_amount_due = post('net_amount_due');
//         $vat_amount = post('sales_tax_amount');
//         $vatable_amount = post('vatable_amount');
//         $zero_rated_amount = post('zero_rated_amount');
//         $vat_exempt_amount = post('vat_exempt_amount');
//         $tax_withheld_percentage = post('tax_withheld_percentage');
//         $tax_withheld_amount = post('tax_withheld_amount');
//         $total_amount_due = post('total_amount_due');

//         // Decode JSON data for items
//         $items = json_decode($_POST['item_data'], true);
//         // Update invoice using Invoice class method
//         Invoice::update(
//             $id,
//             $invoice_number,
//             $invoice_date,
//             $invoice_account_id,
//             $invoice_due_date,
//             $customer_id,
//             $payment_method,
//             $location,
//             $terms,
//             $memo,
//             $gross_amount,
//             $total_discount_amount,
//             $net_amount_due,
//             $vat_amount,
//             $vatable_amount,
//             $zero_rated_amount,
//             $vat_exempt_amount,
//             $tax_withheld_percentage,
//             $tax_withheld_amount,
//             $total_amount_due
//         );

//         // Update invoice details using Invoice class method
//         Invoice::updateDetails($id, $items);

//         flashMessage('update_invoice', 'Invoice updated successfully.', FLASH_SUCCESS);
//     } catch (Exception $e) {
//         // Handle any exceptions that occur during update
//         flashMessage('update_invoice', $e->getMessage(), FLASH_ERROR);
//     }

//     // Redirect after processing 'update' action
//     redirect('../invoice');
// }

// Add this to the existing switch statement or if-else block
if (post('action') === 'void_check') {
    try {
        $id = post('id');

        if (!$id) {
            throw new Exception("Payments ID is required.");
        }

        $result = OfficialReceipt::void($id);

        if ($result) {

            // Send a message to Discord with the user info
            $discordMessage = "**" . $_SESSION['name'] . " just void an Official Receipt!**\n"; // Bold username and action
            $discordMessage .= "-----------------------\n"; // Top border

            $discordMessage .= "-----------------------\n"; // Bottom border
            sendToDiscord($discordMessage); // Send the message to Discord


            $response = ['success' => true];
        } else {
            throw new Exception("Failed to void payments.");
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        error_log('Error voiding payments: ' . $e->getMessage());
    }
    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}


if (post('action') === 'save_draft') {
    try {
        // Retrieve form data
        $or_number = $_POST['or_number'];
        $or_date = $_POST['or_date'];
        $or_account_id = $_POST['or_account_id'];
        $customer_po = $_POST['customer_po'];
        $so_no = $_POST['so_no'];
        $check_no = $_POST['check_no'];
        $rep = $_POST['rep'];
        $customer_id = $_POST['customer_id'];
        $customer_name = $_POST['customer_name'];
        $payment_method = $_POST['payment_method'];
        $location = $_POST['location'];
        $memo = $_POST['memo'];

        // Summary details
        $gross_amount = $_POST['gross_amount'];
        $discount_amount = $_POST['total_discount_amount'];
        $discount_account_ids = $_POST['discount_account_ids'];
        $output_vat_ids = $_POST['output_vat_ids'];
        $net_amount_due = $_POST['net_amount_due'];
        $vat_amount = $_POST['total_vat_amount'];
        $vatable_amount = $_POST['vatable_amount'];
        $zero_rated_amount = $_POST['zero_rated_amount'];
        $vat_exempt_amount = $_POST['vat_exempt_amount'];
        $tax_withheld_percentage = $_POST['tax_withheld_percentage'];
        $tax_withheld_amount = $_POST['tax_withheld_amount'];
        $tax_withheld_account_id = $_POST['tax_withheld_account_id'];
        $total_amount_due = $_POST['total_amount_due'];
        
        // Decode JSON data for receipt items
        $items = json_decode($_POST['item_data'], true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }

        // Call the Payment::addDraft method to insert the draft official receipt into the database
        $result = OfficialReceipt::addDraft(
            $or_number,
            $or_date,
            $or_account_id,
            $customer_po,
            $so_no,
            $check_no,
            $rep,
            $customer_id,
            $payment_method,
            $location,
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
        $discordMessage = "**" . $_SESSION['name'] . " created a draft Official Receipt!**\n";
        $discordMessage .= "-----------------------\n";
        $discordMessage .= "**OR No:** `" . $or_number . "`\n";
        $discordMessage .= "**Memo:** `" . $memo . "`\n";
        $discordMessage .= "-----------------------\n";
        sendToDiscord($discordMessage);

        if ($result) {
            $response = ['success' => true, 'message' => 'Official receipt saved as draft successfully'];
        } else {
            $response = ['success' => false, 'message' => 'Failed to save official receipt as draft.'];
        }

    } catch (Exception $ex) {
        error_log('Error in saving draft official receipt: ' . $ex->getMessage());
        error_log('Stack trace: ' . $ex->getTraceAsString());

        // Send a message to Discord with the error details
        $discordErrorMessage = "**ERROR: Oh no, we have an error drafting Official Receipt 😱 where's the devs!!!**\n";
        $discordErrorMessage .= "-----------------------\n";
        $discordErrorMessage .= "Error Message: `" . $ex->getMessage() . "`\n";
        $discordErrorMessage .= "Stack Trace: `" . $ex->getTraceAsString() . "`\n";
        $discordErrorMessage .= "-----------------------\n";
        sendToDiscord($discordErrorMessage);

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
        $discount_percentage = post('discount_percentage');
        $discount_amount = post('discount_amount');
        $output_vat_ids = post('output_vat_ids');
        $net_amount_due = post('net_amount_due');
        $vat_amount = post('vat_amount');
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

        // Decode JSON data for invoice items
        $items = json_decode(post('item_data'), true);

        // Ensure $items is an array
        if (!is_array($items)) {
            $items = [];
        }

        $created_by = $_SESSION['user_name'] ?? 'unknown';

        if (!$id) {
            throw new Exception("Official Receipt ID is required.");
        }

        // Log received data
        error_log('Received data: ' . print_r($_POST, true));

        // Call the updateDraft function in the model
        $result = OfficialReceipt::updateDraft(
            $id, $or_number, $or_date, $or_account_id, $customer_po, $so_no, $check_no, $rep,
            $payment_method, $location, $memo, $gross_amount, $discount_amount,
            $net_amount_due, $vat_amount, $vatable_amount, $zero_rated_amount, $vat_exempt_amount,
            $tax_withheld_percentage, $tax_withheld_amount, $total_amount_due, $items,
            $customer_id, $customer_name
        );

        if ($result['success']) {
            $response = [
                'success' => true,
                'id' => $result['id'],
                'message' => 'Official receipt updated successfully'
            ];
        } else {
            throw new Exception($result['message']);
        }
    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error updating draft official receipt: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (post('action') === 'save_final') {
    try {
        $id = post('id');
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
        $discount_percentage = post('discount_percentage');
        $discount_amount = post('discount_amount');
        $output_vat_ids = post('output_vat_ids');
        $net_amount_due = post('net_amount_due');
        $vat_amount = post('vat_amount');
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

        // Decode JSON data for invoice items
        $items = json_decode(post('item_data'), true);

        // Ensure $items is an array
        if (!is_array($items)) {
            $items = [];
        }

        $created_by = $_SESSION['user_name'] ?? 'unknown';

        if (!$id) {
            throw new Exception("Official Receipt ID is required.");
        }

        // Log received data
        error_log('Received data: ' . print_r($_POST, true));

        // Fetch customer name
        $customer_name = '';
        if ($customer_id) {
            $customer = Customer::find($customer_id);
            $customer_name = $customer ? $customer->customer_name : '';
        }

        // Generate the new CI number
        $ci_no = OfficialReceipt::getLastOrNo();

        // Call the updateDraft function in the model
        $result = OfficialReceipt::saveFinal(
            $id, $or_number, $or_date, $or_account_id, $customer_po, $so_no, $check_no, $rep,
            $payment_method, $location, $memo, $gross_amount, $discount_amount,
            $net_amount_due, $vat_amount, $vatable_amount, $zero_rated_amount, $vat_exempt_amount,
            $tax_withheld_percentage, $tax_withheld_amount, $total_amount_due, $items,
            $customer_id, $customer_name, $ci_no, $created_by
        );

        if ($result['success']) {
            $response = [
                'success' => true,
                'id' => $result['id'],
                'message' => 'Official receipt updated successfully'
            ];
        } else {
            throw new Exception($result['message']);
        }
    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error updating draft official receipt: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}





// // if (post('action') === 'update_draft_invoice') {
// //     try {
// //         $id = post('id');
// //         $invoice_number = post('invoice_number');
// //         $items = json_decode(post('item_data'), true);
// //         $invoice_account_id = post('invoice_account_id');
// //         $customer_id = post('customer_id');
// //         $total_amount_due = post('total_amount_due'); // Retrieve total_amount_due

// //         if (!$id || !$invoice_number) {
// //             throw new Exception("Invoice ID and number are required.");
// //         }

// //         // Check if the invoice account is an Accounts Receivable or Undeposited Funds type
// //         $stmt = $connection->prepare("
// //             SELECT at.name
// //             FROM chart_of_account coa
// //             JOIN account_types at ON coa.account_type_id = at.id
// //             WHERE coa.id = ?
// //         ");
// //         $stmt->execute([$invoice_account_id]);
// //         $account_type_name = $stmt->fetchColumn();

// //         // Determine the invoice status and balance due based on the account type
// //         $invoice_status = 0;
// //         $balance_due = $total_amount_due; // Set balance_due to total_amount_due
// //         if ($account_type_name == 'Bank') {
// //             $invoice_status = 1;
// //             $balance_due = 0; // If it's paid immediately, balance due is 0
// //         }

// //         // Update the invoice in the database
// //         $stmt = $connection->prepare("
// //             UPDATE sales_invoice 
// //             SET invoice_status = :invoice_status, 
// //                 invoice_number = :invoice_number,
// //                 balance_due = :balance_due,
// //                 total_amount_due = :total_amount_due
// //             WHERE id = :id
// //         ");
// //         $stmt->bindParam(':id', $id, PDO::PARAM_INT);
// //         $stmt->bindParam(':invoice_number', $invoice_number, PDO::PARAM_STR);
// //         $stmt->bindParam(':invoice_status', $invoice_status, PDO::PARAM_INT);
// //         $stmt->bindParam(':balance_due', $balance_due, PDO::PARAM_STR);
// //         $stmt->bindParam(':total_amount_due', $total_amount_due, PDO::PARAM_STR);
// //         $result = $stmt->execute();

// //         if ($result) {
// //             // Update invoice details
// //             Invoice::updateDetails($id, $items);

// //             // If it's an Accounts Receivable, update the customer's balance
// //             if ($account_type_name == 'Accounts Receivable') {
// //                 $stmt = $connection->prepare("UPDATE customers SET credit_balance = credit_balance + ?, total_invoiced = total_invoiced + ? WHERE id = ?");
// //                 $stmt->execute([$total_amount_due, $total_amount_due, $customer_id]);
// //             }

// //             $response = [
// //                 'success' => true,
// //                 'invoiceId' => $id
// //             ];
// //             echo json_encode($response);
// //             exit;
// //         } else {
// //             throw new Exception("Failed to update invoice.");
// //         }
// //     } catch (Exception $ex) {
// //         $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
// //         error_log('Error updating draft invoice: ' . $ex->getMessage());
// //     }

// //     // Send JSON response
// //     header('Content-Type: application/json');
// //     echo json_encode($response);
// //     exit;
// // }


// if (post('action') === 'update_draft_invoice') {
//     $id = post('id');
//     $invoice_number = post('invoice_number');
//     $items = json_decode(post('item_data'), true);
//     $invoice_account_id = post('invoice_account_id');
//     $customer_id = post('customer_id');
//     $customer = Customer::find($customer_id);
//     $customer_name = $customer ? $customer->customer_name : '';
//     $total_amount_due = post('total_amount_due');
//     $created_by = $_SESSION['user_name'];

//     // Extract additional required data from the items or post data
//     $invoice_date = post('invoice_date');
//     // Summary details
//     $gross_amount = post('gross_amount');
//     $discount_amount = post('discount_amount');
//     $discount_account_ids = post('discount_account_ids');
//     $output_vat_ids = post('output_vat_ids');
//     $net_amount_due = post('net_amount_due');
//     $vat_amount = post('total_vat_amount');
//     $vatable_amount = post('vatable_amount');
//     $zero_rated_amount = post('zero_rated_amount');
//     $vat_exempt_amount = post('vat_exempt_amount');

//     // Tax withholding information
//     $tax_withheld_percentage = post('tax_withheld_percentage');
//     $tax_withheld_amount = post('tax_withheld_amount');
//     $tax_withheld_account_id = post('tax_withheld_account_id');


//     // Check for existing invoice to prevent duplicates
//     $existingRecord = Invoice::findByInvoiceNo($invoice_number);


//     if ($existingRecord !== null) {
//         throw new Exception("Record with Invoice #: $invoice_number already exists.");
//     }


//     // Decode JSON data for invoice items
//     $items = json_decode($_POST['item_data'], true);


//     if (json_last_error() !== JSON_ERROR_NONE) {
//         throw new Exception("Error decoding item data: " . json_last_error_msg());
//     }

//     $response = Invoice::updateDraftInvoice(
//         $id, 
//         $invoice_number, 
//         $invoice_account_id, 
//         $customer_name, 
//         $customer_id,
//         $tax_withheld_account_id, 
//         $tax_withheld_amount, 
//         $total_amount_due, 
//         $gross_amount,
//         $invoice_date,
//         $output_vat_ids, 
//         $vat_amount, 
//         $discount_amount, 
//         $created_by,
//         $items  // Add the $items parameter here
//     );

//     header('Content-Type: application/json');
//     echo json_encode($response);
//     exit;
// }