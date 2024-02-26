<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input data
    $termID = $_POST['edittermID'];
    $termCode = $_POST['edittermCode'];
    $termName = $_POST['edittermName'];
    $termDescription = $_POST['edittermDescription'];
    $termDaysDue = $_POST['edittermDaysDue'];
    $activeStatus = isset($_POST['editActiveStatus']) ? 1 : 0;

    // Update term details in the database
    $query = "UPDATE terms 
                SET term_code = :termCode,
                term_name = :termName, 
                term_description = :termDescription,
                term_days_due = :termDaysDue,
                active_status = :activeStatus 
                WHERE term_id = :termID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':termID', $termID);
    $stmt->bindParam(':termCode', $termCode);
    $stmt->bindParam(':termName', $termName);
    $stmt->bindParam(':termDescription', $termDescription);
    $stmt->bindParam(':termDaysDue', $termDaysDue);
    $stmt->bindParam(':activeStatus', $activeStatus);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error updating term: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
?>
