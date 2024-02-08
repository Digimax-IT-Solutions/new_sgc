<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['deleteChartOfAccountID'])) {
    // Sanitize input data
    $chartOfAccountID = filter_var($_POST['deleteChartOfAccountID'], FILTER_SANITIZE_NUMBER_INT);

    // Delete the location from the database
    $query = "DELETE FROM chart_of_accounts WHERE account_id = :chartOfAccountID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':chartOfAccountID', $chartOfAccountID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error deleting account: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
