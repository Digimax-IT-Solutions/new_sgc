<?php
// Include your database connection file
include('../connect.php');

try {
    // Get form data
    $accountType = $_POST['accountType'];
    $accountCode = $_POST['accountCode'];
    $subAccountOf = $_POST['subAccountOf'];
    $accountName = $_POST['accountName'];
    $accountBalance = $_POST['accountBalance'];
    $description = $_POST['description'];

    // Fetch the last ID from the chart_of_accounts table
    $stmt = $db->prepare("SELECT account_id FROM chart_of_accounts ORDER BY account_id DESC LIMIT 1");
    $stmt->execute();
    $lastIDRow = $stmt->fetch(PDO::FETCH_ASSOC);

    // If there are existing records, extract the numeric part and increment
    if ($lastIDRow) {
        $numericPart = intval($lastIDRow['account_id']) + 1;
    } else {
        // If no existing records, start with 1
        $numericPart = 1;
    }

    // Generate the new account_no
    $accountNo = 'AC-' . str_pad($numericPart, 6, '0', STR_PAD_LEFT);

    // Insert data into the chart_of_accounts table
    $query = "INSERT INTO chart_of_accounts (account_no, account_type, account_code, sub_account_of, account_name, account_balance, description) VALUES (:accountNo, :accountType, :accountCode, :subAccountOf, :accountName, :accountBalance, :description)";
    $stmt = $db->prepare($query);

    $stmt->bindParam(':accountNo', $accountNo);
    $stmt->bindParam(':accountType', $accountType);
    $stmt->bindParam(':accountCode', $accountCode);
    $stmt->bindParam(':subAccountOf', $subAccountOf);
    $stmt->bindParam(':accountName', $accountName);
    $stmt->bindParam(':accountBalance', $accountBalance);
    $stmt->bindParam(':description', $description);

    $result = $stmt->execute();

    if ($result) {
        echo "success";
    } else {
        // Retrieve the specific MySQL error message
        $errorMessage = $stmt->errorInfo()[2];

        // Log the error message to the error log
        error_log("MySQL Error: " . $errorMessage);

        // Echo a generic error message to the client
        echo "error: An error occurred while processing your request. Please try again later.";
    }
} catch (PDOException $e) {
    // Retrieve the specific MySQL error message
    $errorMessage = $stmt->errorInfo()[2];
    // Log the exception message to the error log
    error_log("PDOException: " . $e->getMessage());
    // Log the error message to the error log
    error_log("MySQL Error: " . $errorMessage);

    // Echo a generic error message to the client
    echo $errorMessage;
}
?>