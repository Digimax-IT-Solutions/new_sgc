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

    // Start a transaction
    try {
        $db->beginTransaction();

        // Update data in general_journal table
        $stmt = $db->prepare("UPDATE general_journal SET entry_no = ?, journal_date = ?, total_debit = ?, total_credit = ? WHERE ID = ?");
        $stmt->execute([$entryNo, $entryDate, array_sum($debits), array_sum($credits), $ID]);

        foreach ($accounts as $key => $account) {
            $debit = $debits[$key];
            $credit = $credits[$key];
            $name = $_POST['name'][$key];
            $memo = $memos[$key];

            // Update the chart_of_accounts if debit is greater than 0
            if ($debit > 0) {
                // Get the previous debit amount for this account
                $previousDebitStmt = $db->prepare("SELECT debit FROM journal_entries WHERE general_journal_id = ? AND account = ?");
                $previousDebitStmt->execute([$ID, $account]);
                $previousDebit = $previousDebitStmt->fetchColumn();
            
                // Update the chart_of_accounts with the difference between the new and previous debit
                $debitDifference = $debit - $previousDebit;
                $updateChartOfAccountsStmt = $db->prepare("UPDATE chart_of_accounts SET account_balance = account_balance + ? WHERE account_name = ?");
                $updateChartOfAccountsStmt->execute([$debitDifference, $account]);
            
                // Update the journal entry with the new debit value
                $updateStmt = $db->prepare("UPDATE journal_entries SET debit = ? WHERE general_journal_id = ? AND account = ?");
                $updateStmt->execute([$debit, $ID, $account]);
            }

            if ($credit > 0) {
                // Get the previous credit amount for this account
                $previousCreditStmt = $db->prepare("SELECT credit FROM journal_entries WHERE general_journal_id = ? AND account = ?");
                $previousCreditStmt->execute([$ID, $account]);
                $previousCredit = $previousCreditStmt->fetchColumn();
                
                // Update the chart_of_accounts with the difference between the new and previous credit
                $creditDifference = $credit - $previousCredit;
                $updateChartOfAccountsStmt = $db->prepare("UPDATE chart_of_accounts SET account_balance = account_balance + ? WHERE account_name = ? AND account_type IN (?, ?, ?)");
                $updateChartOfAccountsStmt->execute([$creditDifference, $account, 'Other Current Liability', 'Equity', 'Revenue']);
                
                // Update the journal entry with the new credit value
                $updateStmt = $db->prepare("UPDATE journal_entries SET credit = ? WHERE general_journal_id = ? AND account = ?");
                $updateStmt->execute([$credit, $ID, $account]);
            }            

            // Insert data into journal_entries table
            $stmt = $db->prepare("UPDATE journal_entries SET debit = ?, credit = ?, name = ?, memo = ? WHERE general_journal_id = ? AND account = ?");
            $stmt->execute([$debit, $credit, $name, $memo, $ID, $account]);
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
