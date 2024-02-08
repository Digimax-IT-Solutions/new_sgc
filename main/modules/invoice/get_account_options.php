<?php
// Include your database connection file here
// Example assuming you have a file named "db_connection.php"
include('../connect.php');

// Get the account type from the AJAX request
$accountType = $_GET['accountType'];

// Perform database query based on account type
try {
    // Replace 'your_table_name' with the actual table name in your database
    $query = "SELECT account_id, account_name FROM chart_of_accounts WHERE account_type = :accountType";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':accountType', $accountType);
    $stmt->execute();

    // Fetch account names and store in an array
    $options = array();
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // Add account_id to the options array
        $options[] = array(
            'account_id' => $row['account_id'],
            'account_name' => $row['account_name']
        );
    }

    // Return options as JSON
    echo json_encode($options);

} catch (PDOException $e) {
    // Handle database connection or query error
    echo json_encode(array('error' => 'Error fetching account options.'));
}
?>