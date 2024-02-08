<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input data
    $accountID = $_POST['editAccountID'];
    $accountType = $_POST['editAccountType'];
    $accountCode = $_POST['editAccountCode'];
    $subAccountOf = $_POST['editSubAccountOf'];
    $accountBalance = $_POST['editAccountBalance'];
    $accountName = $_POST['editAccountName'];

    $description = $_POST['editDescription'];

    // Update account details in the database
    $query = "UPDATE chart_of_accounts 
              SET account_type = :accountType, 
                  account_code = :accountCode, 
                  sub_account_of = :subAccountOf, 
                  account_name = :accountName, 
                  account_balance = :accountBalance,
                  description = :description 
              WHERE account_id = :accountID";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':accountID', $accountID);
    $stmt->bindParam(':accountType', $accountType);
    $stmt->bindParam(':accountCode', $accountCode);
    $stmt->bindParam(':subAccountOf', $subAccountOf);
    $stmt->bindParam(':accountName', $accountName);
    $stmt->bindParam(':accountBalance', $accountBalance);
    $stmt->bindParam(':description', $description);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error updating account: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}