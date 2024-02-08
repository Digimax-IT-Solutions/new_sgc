<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input data
    $uomID = $_POST['edituomID'];
    $uomCode = $_POST['edituomCode'];
    $uomName = $_POST['edituomName'];
    $uomDescription = $_POST['edituomDescription'];
    $activeStatus = isset($_POST['editActiveStatus']) ? 1 : 0;

    // Update category details in the database
    $query = "UPDATE uom 
                SET uomCode = :uomCode,
                uomName = :uomName, 
                uomDescription = :uomDescription,
                activeStatus = :activeStatus 
                WHERE uomID = :uomID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':uomID', $uomID);
    $stmt->bindParam(':uomCode', $uomCode);
    $stmt->bindParam(':uomName', $uomName);
    $stmt->bindParam(':uomDescription', $uomDescription);
    $stmt->bindParam(':activeStatus', $activeStatus);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error updating uom method: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
?>
