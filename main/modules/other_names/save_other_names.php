<?php
// Include your database connection file
include('../../connect.php');

try {
    // Get form data
    $vendorCode = $_POST['vendorCode'];
    $vendorName = $_POST['vendorName'];
    $vendorAccountNumber = $_POST['vendorAccountNumber'];
    $vendorAddress = $_POST['vendorAddress'];
    $vendorContactNumber = $_POST['vendorContactNumber'];
    $vendorEmail = $_POST['vendorEmail'];
    $vendorTerms = $_POST['vendorTerms'];

    // Insert data into the vendors table
    $query = "INSERT INTO other_names (otherCode, otherName, otherAccountNumber, otherAddress, otherContactNumber, otherEmail, otherTerms) VALUES (:vendorCode, :vendorName, :vendorAccountNumber, :vendorAddress, :vendorContactNumber, :vendorEmail, :vendorTerms)";
    $stmt = $db->prepare($query);

    $stmt->bindParam(':vendorCode', $vendorCode);
    $stmt->bindParam(':vendorName', $vendorName);
    $stmt->bindParam(':vendorAccountNumber', $vendorAccountNumber);
    $stmt->bindParam(':vendorAddress', $vendorAddress);
    $stmt->bindParam(':vendorContactNumber', $vendorContactNumber);
    $stmt->bindParam(':vendorEmail', $vendorEmail);
    $stmt->bindParam(':vendorTerms', $vendorTerms);

    $result = $stmt->execute();

    if ($result) {
        echo "success";
    } else {
        // Retrieve the specific MySQL error message
        $errorMessage = $stmt->errorInfo()[2];

        // Echo the error message to the client
        echo "error: " . $errorMessage;
    }
} catch (PDOException $e) {
    // Log the exception message to the error log
    error_log("PDOException: " . $e->getMessage());

    // Echo a generic error message to the client
    echo "error";
}
?>
