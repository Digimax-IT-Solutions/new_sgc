<?php

require_once __DIR__ . '/../_init.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action'])) {
    if ($_GET['action'] === 'get_invoices_and_credits') {
        $customerId = $_GET['customer_id'] ?? '';

        if (empty($customerId)) {
            echo json_encode(['invoices' => [], 'creditMemos' => [], 'creditBalance' => 0]);
            exit;
        }

        try {
            // Fetch customer
            $customer = Customer::find($customerId);
            $creditBalance = $customer ? $customer->credit_balance : 0;

            // Fetch invoices
            $invoices = Invoice::getCashSalesByCustomerId($customerId);
            $formattedInvoices = [];

            foreach ($invoices as $invoice) {
                $formattedInvoices[] = [
                    'id' => $invoice->id,
                    'invoice_account_id' => $invoice->invoice_account_id,
                    'invoice_number' => $invoice->invoice_number,
                    'invoice_date' => $invoice->invoice_date,
                    'invoice_due_date' => $invoice->invoice_due_date,
                    'gross_amount' => $invoice->gross_amount,
                    'total_amount_due' => $invoice->total_amount_due,
                    'memo' => $invoice->memo,
                    'customer_name' => $invoice->customer_name,
                    'credits_applied' => 0, // Initialize credits applied
                    'remaining_amount' => $invoice->total_amount_due, // Initialize remaining amount
                    'balance_due' => $invoice->balance_due // Initialize remaining amount
                ];
            }

            // Fetch credit memos
            $creditMemos = CreditMemo::getAvailableCreditsForCustomer($customerId);

            echo json_encode([
                'invoices' => $formattedInvoices,
                'creditMemos' => $creditMemos,
                'creditBalance' => $creditBalance
            ]);
        } catch (Exception $e) {
            error_log("Error in receive_payment_controller: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
        }
        exit;
    }
}

if (post('action') === 'add') {
    try {
        // Retrieve form data
        $customer_id = post('customer_name');
        $customer = Customer::find($customer_id);
        $customer_name = $customer ? $customer->customer_name : '';
        $payment_date = post('payment_date');
        $payment_method_id = post('payment_method');
        $account_id = post('account_id');
        $ref_no = post('reference_no');
        $cr_no = post('cr_no');
        $memo = post('memo');
        $summary_amount_due = post('summary_amount_due');
        $summary_applied_amount = post('summary_applied_amount');
        $applied_credits_discount = post('applied_credits_discount');
        $created_by = $_SESSION['user_name'];

        // Decode JSON data for selected invoices
        $selected_invoices = json_decode(post('selected_invoices'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding selected invoices data: " . json_last_error_msg());
        }

        // Process credit_nos for each invoice
        foreach ($selected_invoices as &$invoice) {
            if (isset($invoice['credit_no']) && !empty($invoice['credit_no'])) {
                $invoice['credit_no'] = explode(',', $invoice['credit_no']);
            } else {
                $invoice['credit_no'] = [];
            }
        }

        // Call the Payment::add method to insert the payment into the database
        $payment_id = Payment::add(
            $customer_id,
            $payment_date,
            $payment_method_id,
            $account_id,
            $ref_no,
            $cr_no,
            $customer_name,
            $memo,
            $summary_amount_due,
            $summary_applied_amount,
            $selected_invoices,
            $created_by,
            $applied_credits_discount
        );

        // Set success message
        flashMessage('add_payment', 'Payment Recorded Successfully!', FLASH_SUCCESS);

        // Return success response as JSON
        echo json_encode(['success' => true, 'message' => 'Payment Recorded Successfully!', 'payment_id' => $payment_id]);
        exit;
    } catch (Exception $ex) {
        // Handle exceptions and set error message
        flashMessage('add_payment', 'Error: ' . $ex->getMessage(), FLASH_ERROR);
        // Optional: Log the error for debugging
        error_log('Error in payment submission: ' . $ex->getMessage());
        // Return error response as JSON
        echo json_encode(['success' => false, 'message' => 'Error: ' . $ex->getMessage()]);
        exit;
    }
}


// ==========================================================================
// WARNING: DO NOT MODIFY THE CODE ABOVE!
// This section is critical for the functionality of the payment processing.
// Any changes might cause unexpected behavior or system failures.
// ==========================================================================

if (post('action') === 'update_print_status') {
    try {
        $id = post('id');
        $printStatus = post('print_status');

        if (!$id) {
            throw new Exception("Payment ID is required.");
        }

        if (!in_array($printStatus, [1, 2])) {
            throw new Exception("Invalid print status.");
        }

        // Update the print_status in the database
        $stmt = $connection->prepare("UPDATE payments SET print_status = :status WHERE id = :id");
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

if (post('action') === 'save_draft') {
    try {
        // Retrieve form data
        $customer_id = post('customer_name');
        $payment_date = post('payment_date');
        $payment_method_id = post('payment_method');
        $account_id = post('account_id');
        $ref_no = post('reference_no');
        $memo = post('memo');
        $summary_amount_due = post('summary_amount_due');
        $summary_applied_amount = post('summary_applied_amount');
        $applied_credits_discount = post('applied_credits_discount');

        // Decode JSON data for selected invoices
        $selected_invoices = json_decode(post('selected_invoices'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding selected invoices data: " . json_last_error_msg());
        }

        // Call the Payment::addDraft method to insert the draft payment into the database
        $result = Payment::addDraft(
            $customer_id,
            $payment_date,
            $payment_method_id,
            $account_id,
            $ref_no,
            $memo,
            $summary_amount_due,
            $summary_applied_amount,
            $selected_invoices,
            $applied_credits_discount
        );

        echo json_encode(['success' => true, 'message' => 'Payment saved as draft successfully', 'payment_id' => $result]);
    } catch (Exception $ex) {
        error_log('Error in saving draft payment: ' . $ex->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error: ' . $ex->getMessage()]);
    }
    exit;
}

if (post('action') === 'update_draft') {
    try {
        // Retrieve and sanitize input data
        $payment_id = (int)post('payment_id');
        $customer_id = (int)post('customer_id');
        $payment_date = (string)post('payment_date');
        $payment_method_id = (int)post('payment_method_id');
        $account_id = (int)post('account_id');
        $ref_no = (string)post('ref_no');
        $cr_no = (string)post('cr_no');
        $customer_name = (string)post('customer_name');
        $memo = (string)post('memo');
        $summary_amount_due = (float)post('summary_amount_due');
        $summary_applied_amount = (float)post('summary_applied_amount');
        $applied_credits_discount = (float)post('applied_credits_discount');
        $created_by = $_SESSION['user_name'] ?? 'unknown';

        $selected_invoices_json = post('selected_invoices');
        $selected_invoices = json_decode($selected_invoices_json, true);
        
        // Check if JSON decoding failed
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding selected invoices data: " . json_last_error_msg());
        }

        // Check required fields
        if (!$payment_id || !$customer_id || !$selected_invoices) {
            throw new Exception("Payment ID, customer ID, and selected invoices are required.");
        }

        // Update the payment draft status and cr_no
        $stmt = $connection->prepare("
            UPDATE payments 
            SET status = 1,
                cr_no = :cr_no 
            WHERE id = :id
        ");

        $stmt->bindParam(':id', $payment_id, PDO::PARAM_INT);
        $stmt->bindParam(':cr_no', $cr_no, PDO::PARAM_STR);
        $result = $stmt->execute();

        if ($result) {
            // Call the updateDraft function
            Payment::updateDraft(
                $payment_id,
                $customer_id,
                $payment_date,
                $payment_method_id,
                $account_id,
                $ref_no,
                $cr_no,
                $customer_name,
                $memo,
                $summary_amount_due,
                $summary_applied_amount,
                $selected_invoices,
                $created_by,
                $applied_credits_discount
            );

            $response = [
                'success' => true,
                'payment_id' => $payment_id,
                'message' => 'Payment draft updated successfully'
            ];
        } else {
            throw new Exception("Failed to update payment draft.");
        }
    } catch (Exception $ex) {
        $response = [
            'success' => false,
            'message' => 'Error: ' . $ex->getMessage()
        ];
        error_log('Error updating draft payment: ' . $ex->getMessage());
    }

    // Send JSON response
    echo json_encode($response);
    exit;
}


// Add this to the existing switch statement or if-else block
if (post('action') === 'void_check') {
    try {
        $id = post('id');
        if (!$id) {
            throw new Exception("Payment ID is required.");
        }
        
        $result = Payment::void($id);
        
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
