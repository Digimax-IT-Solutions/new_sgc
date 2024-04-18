<?php
include('../../connect.php');

// Check if the form data has been submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Extract form data
    $bankAccountName = $_POST['bankAccountName'];
    $depositDate = $_POST['deposit_date'];
    $depositID = $_POST['deposit_id'];
    $memo = $_POST['memos'];
    $receivedFrom = $_POST['received_from'];
    $fromAccount = $_POST['from_account'];
    $total_deposit = $_POST['total_deposit'];
    $memoArray = $_POST['memo'];
    $checkNoArray = $_POST['check_no'];
    $paymentMethodArray = $_POST['payment_method'];
    $amountArray = $_POST['amount'];
    $checkedIds = isset($_POST['checkedIds']) ? $_POST['checkedIds'] : []; // Array of checked IDs from the modal, default to empty array if not set

    // Example SQL statement to insert deposit record into a database table
    $sql = "INSERT INTO make_deposit (bank_account, deposit_date, deposit_id, total_deposit, memo) 
            VALUES (:bankAccountName, :depositDate, :depositID, :total_deposit, :memo)";
    
    // Prepare the SQL statement
    $stmt = $db->prepare($sql);

    // Bind parameters to the prepared statement
    $stmt->bindParam(':bankAccountName', $bankAccountName);
    $stmt->bindParam(':depositDate', $depositDate);
    $stmt->bindParam(':depositID', $depositID);
    $stmt->bindParam(':total_deposit', $total_deposit);
    $stmt->bindParam(':memo', $memo);


    // Execute the prepared statement
    if ($stmt->execute()) {
        // Get the ID of the last inserted record
        $depositID = $db->lastInsertId();

        // Loop through the received data and insert each row into the database
        for ($i = 0; $i < count($receivedFrom); $i++) {
            $receivedFromValue = $receivedFrom[$i];
            $fromAccountValue = $fromAccount[$i];
            $memoValue = $memoArray[$i];
            $checkNoValue = $checkNoArray[$i];
            $paymentMethodValue = $paymentMethodArray[$i];
            $amountValue = $amountArray[$i];

            // Example SQL statement to insert deposit details into a database table
            $sqlDetails = "INSERT INTO make_deposit_details (deposit_id, received_from, from_account, memo, RefNo, payment_method, amount) 
                           VALUES (:depositID, :receivedFrom, :fromAccount, :memo, :checkNo, :paymentMethod, :amount)";

            // Prepare the SQL statement for details
            $stmtDetails = $db->prepare($sqlDetails);

            // Bind parameters to the prepared statement for details
            $stmtDetails->bindParam(':depositID', $depositID);
            $stmtDetails->bindParam(':receivedFrom', $receivedFromValue);
            $stmtDetails->bindParam(':fromAccount', $fromAccountValue);
            $stmtDetails->bindParam(':memo', $memoValue);
            $stmtDetails->bindParam(':checkNo', $checkNoValue);
            $stmtDetails->bindParam(':paymentMethod', $paymentMethodValue);
            $stmtDetails->bindParam(':amount', $amountValue);

            // Execute the prepared statement for details
            $stmtDetails->execute();
        }

        // Update the depo column in the received_payment table
        foreach ($checkedIds as $id) {
            // Example SQL statement to update depo column
            $sqlUpdate = "UPDATE receive_payment SET depo = 'deactivated' WHERE RefNo = :checkNo";
            $stmtUpdate = $db->prepare($sqlUpdate);
            $stmtUpdate->bindParam(':checkNo', $id); // Assuming $id contains the checkNo value
            $stmtUpdate->execute();
        }

        // Update the chart_of_accounts table
        foreach ($amountArray as $index => $amount) {
            // Get the account name
            $accountName = $bankAccountName; // Using bankAccountName as the account name
            
            // Update the account balance
            $sqlUpdateAccount = "UPDATE chart_of_accounts SET account_balance = account_balance + :amount WHERE account_name = :accountName";
            $stmtUpdateAccount = $db->prepare($sqlUpdateAccount);
            $stmtUpdateAccount->bindParam(':amount', $amount);
            $stmtUpdateAccount->bindParam(':accountName', $accountName);
            $stmtUpdateAccount->execute();
        }

        // Return a success response as JSON
        echo json_encode(['success' => true]);
    } else {
        // Return an error response if the execution fails
        echo json_encode(['success' => false, 'message' => 'Failed to save deposit.']);
    }
} else {
    // Return an error response if the request method is not POST
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
