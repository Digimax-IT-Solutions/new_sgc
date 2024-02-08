<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input data
    $termCode = $_POST['termCode'];
    $termName = $_POST['termName'];
    $termDaysDue = $_POST['termDaysDue'];
    $termDescription = $_POST['termDescription'];
    $activeStatus = isset($_POST['activeStatus']) ? 1 : 0;

    // Insert category details into the database
    $query = "INSERT INTO terms (term_code, term_name, term_days_due, term_description, active_status) VALUES (:termCode, :termName, :termDaysDue, :termDescription, :activeStatus)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':termCode', $termCode);
    $stmt->bindParam(':termName', $termName);
    $stmt->bindParam(':termDaysDue', $termDaysDue);
    $stmt->bindParam(':termDescription', $termDescription);
    $stmt->bindParam(':activeStatus', $activeStatus);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error saving term: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
?>
