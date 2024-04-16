<?php
include '../../connect.php';

// Check if the form is submitted using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $ID = $_POST['ID']; // Assuming you have this ID parameter in your form
    $entryDate = $_POST['entry_date'];
    $entryNo = $_POST['entry_no'];
    $accounts = $_POST['account'];
    $debits = array_map('floatval', $_POST['debit']);
    $credits = array_map('floatval', $_POST['credit']);
    $memos = $_POST['memo'];

    // Validate data
    if (empty($ID) || empty($entryDate) || empty($entryNo) || empty($accounts) || empty($debits) || empty($credits)) {
        echo json_encode(['status' => 'error', 'message' => 'Please complete all required fields.']);
        exit();
    }

    // Validate that either debit or credit is provided for each entry
    foreach ($accounts as $key => $account) {
        if (empty($debits[$key]) && empty($credits[$key])) {
            echo json_encode(['status' => 'error', 'message' => 'Please provide either Debit or Credit for each account.']);
            exit();
        }
    }

    // Start a transaction
    try {
        $db->beginTransaction();

        // Update data in general_journal table
        $stmt = $db->prepare("UPDATE general_journal SET entry_no = ?, journal_date = ?, total_debit = ?, total_credit = ? WHERE ID = ?");
        $stmt->execute([$entryNo, $entryDate, array_sum($debits), array_sum($credits), $ID]);

        // Delete existing journal entries for this general journal ID
        $deleteStmt = $db->prepare("DELETE FROM journal_entries WHERE general_journal_id = ?");
        $deleteStmt->execute([$ID]);

        // Insert updated data into journal_entries table
        $stmt = $db->prepare("INSERT INTO journal_entries (general_journal_id, account, debit, credit, name, memo) VALUES (?, ?, ?, ?, ?, ?)");

        foreach ($accounts as $key => $account) {
            $debit = $debits[$key];
            $credit = $credits[$key];
            $name = $_POST['name'][$key];
            $memo = $memos[$key];
            $stmt->execute([$ID, $account, $debit, $credit, $name, $memo]);
        }

        // Commit the transaction
        $db->commit();

        echo json_encode(['status' => 'success', 'message' => 'General Journal Updated']);
    } catch (PDOException $e) {
        // Rollback the transaction on error
        $db->rollBack();

        // Include MySQL error information in the response
        echo json_encode(['status' => 'error', 'message' => 'Error updating data: ' . $e->getMessage(), 'mysql_error' => $db->errorInfo()]);
    }
}
?>
