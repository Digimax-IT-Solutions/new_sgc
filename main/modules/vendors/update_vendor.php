<?php
// Include your database connection file
include('../../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input data
    $editVendorCode = $_POST['editVendorCode'];
    $editVendorName = $_POST['editVendorName'];
    $editVendorAccountNumber = $_POST['editVendorAccountNumber'];
    $editVendorAddress = $_POST['editVendorAddress'];
    $editVendorContactNumber = $_POST['editVendorContactNumber'];
    $editVendorEmail = $_POST['editVendorEmail'];
    $editVendorTerms = $_POST['editVendorTerms'];

    // Assuming you have a vendorID coming from the form
    $vendorID = $_POST['editVendorID'];

    try {
        // Update vendor details in the database
        $query = "UPDATE vendors 
                  SET vendorCode = :vendorCode,
                      vendorName = :vendorName, 
                      vendorAccountNumber = :vendorAccountNumber, 
                      vendorAddress = :vendorAddress, 
                      vendorContactNumber = :vendorContactNumber, 
                      vendorEmail = :vendorEmail, 
                      vendorTerms = :vendorTerms 
                  WHERE vendorID = :vendorID";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':vendorID', $vendorID);
        $stmt->bindParam(':vendorCode', $editVendorCode);
        $stmt->bindParam(':vendorName', $editVendorName);
        $stmt->bindParam(':vendorAccountNumber', $editVendorAccountNumber);
        $stmt->bindParam(':vendorAddress', $editVendorAddress);
        $stmt->bindParam(':vendorContactNumber', $editVendorContactNumber);
        $stmt->bindParam(':vendorEmail', $editVendorEmail);
        $stmt->bindParam(':vendorTerms', $editVendorTerms);

        if ($stmt->execute()) {
            echo "success";
        } else {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Error updating vendor: " . $errorInfo[2]);
        }
    } catch (Exception $e) {
        echo "Exception: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>