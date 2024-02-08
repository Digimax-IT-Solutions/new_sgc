<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['chartOfAccountID'])) {
    // Sanitize input data
    $chartOfAccountID = filter_var($_GET['chartOfAccountID'], FILTER_SANITIZE_NUMBER_INT);

    // Fetch location details from the database
    $query = "SELECT * FROM chart_of_accounts WHERE account_id = :chartOfAccountID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':chartOfAccountID', $chartOfAccountID, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $chartOfAccountDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        // Return location details as JSON
        echo json_encode($chartOfAccountDetails);
    } else {
        echo "Error fetching location details: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
