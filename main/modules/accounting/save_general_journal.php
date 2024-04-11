<?php
include '../../connect.php';

// Check if the form is submitted using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve data from the form
    $entryDate = $_POST['entry_date'];
    $entryNo = $_POST['entry_no'];
    $accounts = $_POST['account'];
    $debits = array_map('floatval', $_POST['debit']);
    $credits = array_map('floatval', $_POST['credit']);
    $memos = $_POST['memo'];

    // Validate data
    if (empty($entryDate) || empty($entryNo) || empty($accounts) || empty($debits) || empty($credits)) {
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

        // Insert data into general_journal table
        $stmt = $db->prepare("INSERT INTO general_journal (entry_no, journal_date, total_debit, total_credit) VALUES (?, ?, ?, ?)");
        $stmt->execute([$entryNo, $entryDate, array_sum($debits), array_sum($credits)]);
        $generalJournalId = $db->lastInsertId();

        // Insert data into journal_entries table
        $stmt = $db->prepare("INSERT INTO journal_entries (general_journal_id, account, debit, credit, name, memo) VALUES (?, ?, ?, ?, ?, ?)");

        foreach ($accounts as $key => $account) {
            $debit = $debits[$key];
            $credit = $credits[$key];
            $name = $_POST['name'][$key];
            $memo = $memos[$key];
            $stmt->execute([$generalJournalId, $account, $debit, $credit, $name, $memo]);

            if ($debit < 0) {
                $updateStmt = $db->prepare("UPDATE chart_of_accounts SET account_balance = account_balance + ? WHERE account_name = ? AND account_type IN (?, ?, ?)");
                $updateStmt->execute([$debit, $account, 'Other Current Liability', 'Equity', 'Revenue']);
            }

            if ($credit > 0) {
                $updateStmt = $db->prepare("UPDATE chart_of_accounts SET account_balance = account_balance + ? WHERE account_name = ? AND account_type IN (?, ?, ?)");
                $updateStmt->execute([$credit, $account, 'Other Current Liability', 'Equity', 'Revenue']);
            }

            if ($debit > 0) {
                $updateStmt = $db->prepare("UPDATE chart_of_accounts SET account_balance = account_balance + ? WHERE account_name = ?");
                $updateStmt->execute([$debit, $account]);
            }

            if ($credit > 0) {
                $updateStmt = $db->prepare("UPDATE chart_of_accounts SET account_balance = account_balance - ? WHERE account_name = ?");
                $updateStmt->execute([$credit, $account]);
            }

            if ($debit > 0 && $account == 'Undeposited Fund') {
                $updateStmt = $db->prepare("UPDATE customers SET customerBalance = customerBalance - ? WHERE customerName = ?");
                $updateStmt->execute([$debit, $name]);
            }
            
            if ($debit > 0 && $account == 'Accounts Receivable') {
                $updateStmt = $db->prepare("UPDATE customers SET customerBalance = customerBalance + ? WHERE customerName = ?");
                $updateStmt->execute([$debit, $name]);
            }
        }

        // Commit the transaction
        $db->commit();

        echo json_encode(['status' => 'success', 'message' => 'General Journal Saved']);
    } catch (PDOException $e) {
        // Rollback the transaction on error
        $db->rollBack();

        // Include MySQL error information in the response
        echo json_encode(['status' => 'error', 'message' => 'Error saving data: ' . $e->getMessage(), 'mysql_error' => $db->errorInfo()]);
    }
}
?>
