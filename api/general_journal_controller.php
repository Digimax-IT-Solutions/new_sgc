<?php

require_once __DIR__ . '/../_init.php';
// Include FPDF library
require_once 'fpdf.php';

// Add check
if (post('action') === 'add') {
    try {
        $id = post('id');
        $entry_no = post('entry_no');
        $journal_date = post('journal_date');
        $total_debit = post('total_debit');
        $total_credit = post('total_credit');
        $memo = post('memo');
        $location = post('location');
        $created_by = $_SESSION['user_name'];

        // Add check to prevent double insertion
        $existingRecord = GeneralJournal::findByEntryNo($entry_no);
        if ($existingRecord !== null) {
            throw new Exception("Record with Entry No: $entry_no already exists.");
        }

        // Get the last transaction ID (if needed for reference)
        $transaction_id = GeneralJournal::getLastTransactionId();

        // Decode JSON data (Journal Details Account)
        $details = json_decode($_POST['item_data'], true);

        // Add main form data to database
        GeneralJournal::add(
            $entry_no,
            $journal_date,
            $total_debit,
            $total_credit,
            $created_by,
            $memo,
            $location,
            $details
        );

        flashMessage('add_general_journal', 'Journal added successfully.', FLASH_SUCCESS);

        // Send a message to Discord with the user info
        $discordMessage = "**" . $_SESSION['name'] . " created a General Journal 📰!**\n"; // Bold username and action
        $discordMessage .= "-----------------------\n"; // Top border
        $discordMessage .= "**GJ No:** `" . post('entry_no') . "`\n"; // Bold "PR No" and use backticks for code block style
        $discordMessage .= "**Memo:** `" . post('memo') . "`\n"; // Bold "Memo" and use backticks for code block style
        $discordMessage .= "-----------------------\n"; // Bottom border
        sendToDiscord($discordMessage); // Send the message to Discord

        // Prepare the response with just the transaction_id
        $response = ['success' => true, 'id' => $transaction_id + 1];
    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error in Journal submission: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Update print status
if (post('action') === 'update_print_status') {
    try {
        $id = post('id');
        $printStatus = post('print_status');

        if (!$id) {
            throw new Exception("general_journal ID is required.");
        }

        if (!in_array($printStatus, [1, 2])) {
            throw new Exception("Invalid print status.");
        }

        // Update the print_status in the database
        $stmt = $connection->prepare("UPDATE general_journal SET print_status = :status WHERE id = :id");
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

// Update action
// Update action
if (post('action') === 'update') {
    try {
        // Retrieve GeneralJournal ID for update
        $general_journal_id = post('id');

        // Retrieve form data for update
        $entry_no = post('entry_no');
        $journal_date = post('journal_date');
        $total_debit = post('total_debit');
        $total_credit = post('total_credit');
        $memo = post('memo');

        // Decode JSON data for items
        $items = json_decode($_POST['item_data'], true);

        // Update GeneralJournal using GeneralJournal class method
        // Add status parameter to update method
        GeneralJournal::update($general_journal_id, $entry_no, $journal_date, $total_debit, $total_credit, $memo, 0);

        // Update GeneralJournal details using GeneralJournal class method
        GeneralJournal::updateDetails($general_journal_id, $items);

        flashMessage('update_general_journal', 'General Journal updated successfully.', FLASH_SUCCESS);
        $response = ['success' => true, 'id' => $general_journal_id];
    } catch (Exception $e) {
        // Handle any exceptions that occur during update
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        error_log('Error in general journal update: ' . $e->getMessage());
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
            throw new Exception("General Journal ID is required.");
        }

        $result = GeneralJournal::void($id);

        if ($result) {
            $response = ['success' => true];
        } else {
            throw new Exception("Failed to void journal.");
        }
    } catch (Exception $e) {
        $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        error_log('Error voiding journal: ' . $e->getMessage());
    }
    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (post('action') === 'save_draft') {
    try {
        // Retrieve form data
        $journal_date = post('journal_date');
        $total_debit = !empty(post('total_debit')) ? post('total_debit') : '0.00';
        $total_credit = !empty(post('total_credit')) ? post('total_credit') : '0.00';
        $memo = post('memo');
        $location = post('location');

        // Decode JSON data (general journal items)
        $items = json_decode(post('item_data'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }

        // Validate required fields
        if (empty($journal_date)) {
            throw new Exception("Journal date cannot be empty");
        }

        // Validate that items are provided
        if (empty($items)) {
            throw new Exception("No items provided for the journal entry");
        }

        // Ensure numeric values for debit and credit in items
        foreach ($items as &$item) {
            $item['debit'] = !empty($item['debit']) ? $item['debit'] : '0.00';
            $item['credit'] = !empty($item['credit']) ? $item['credit'] : '0.00';
        }

        // Save draft to database
        GeneralJournal::saveDraft(
            $journal_date, $total_debit, $total_credit, $memo, $location, $items
        );

        $response = ['success' => true];
    } catch (Exception $ex) {
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error in saving general journal draft: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}


if (post('action') === 'update_draft') {
    try {
        $id = post('id');
        $journal_date = post('journal_date');
        $total_debit = post('total_debit');
        $total_credit = post('total_credit');
        $memo = post('memo');
        $location = post('location');
        $items = json_decode(post('item_data'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }

        // Log received data
        error_log('Received data: ' . print_r($_POST, true));

        // Call the updateDraft function to update the journal details
        $response = GeneralJournal::updateDraft(
            $id, $journal_date, $total_debit, $total_credit, 
            $memo, $location, $items
        );

        // If updateDraft executes successfully, return success response
        $response = [
            'success' => true,
            'id' => $id,
            'message' => 'General journal updated successfully'
        ];

    } catch (Exception $ex) {
        // If an exception occurs, log the error and return failure response
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error updating draft general journal: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

if (post('action') === 'save_final') {
    try {
        $id = post('id');
        $entry_no = post('entry_no');
        $journal_date = post('journal_date');
        $total_debit = post('total_debit');
        $total_credit = post('total_credit');
        $memo = post('memo');
        $location = post('location');
        $items = json_decode(post('item_data'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error decoding item data: " . json_last_error_msg());
        }

        // Log received data
        error_log('Received data: ' . print_r($_POST, true));

        // Call the saveFinal function to update the journal
        $response = GeneralJournal::saveFinal(
            $id, $entry_no, $journal_date, $total_debit, $total_credit, 
            $memo, $location, $items, $_SESSION['user_id'] // Assume user_id is stored in session
        );

        // If saveFinal executes successfully, return success response
        $response = [
            'success' => true,
            'id' => $id,
            'message' => 'General journal updated successfully'
        ];

    } catch (Exception $ex) {
        // If an exception occurs, log the error and return failure response
        $response = ['success' => false, 'message' => 'Error: ' . $ex->getMessage()];
        error_log('Error updating general journal: ' . $ex->getMessage());
    }

    // Send JSON response
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}



// Redirect if no actions were performed
redirect('../general_journal');
