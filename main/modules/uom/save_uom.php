<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input data
    $uomCode = $_POST['uomCode'];
    $uomName = $_POST['uomName'];
    $uomDescription = $_POST['uomDescription'];
    $activeStatus = isset($_POST['activeStatus']) ? 1 : 0;

    // Insert category details into the database
    $query = "INSERT INTO uom (uomCode, uomName, uomDescription, activeStatus) VALUES (:uomCode, :uomName, :uomDescription, :activeStatus)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':uomCode', $uomCode);
    $stmt->bindParam(':uomName', $uomName);
    $stmt->bindParam(':uomDescription', $uomDescription);
    $stmt->bindParam(':activeStatus', $activeStatus);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error saving sales uom: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
?>
