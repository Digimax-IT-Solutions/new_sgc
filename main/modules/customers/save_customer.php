<?php
// Include your database connection file
include('../connect.php');

try {
    // Get form data
    $customerCode = $_POST['customerCode'];
    $customerName = $_POST['customerName'];
    $customerPaymentMethod = $_POST['customerPaymentMethod'];
    $customerBillingAddress = $_POST['customerBillingAddress'];
    // $customerShippingAddress = $_POST['customerShippingAddress'];
    $customerTin = $_POST['customerTin'];
    $contactNumber = $_POST['contactNumber'];
    $customerDeliveryType = $_POST['customerDeliveryType'];
    $customerTerms = $_POST['customerTerms'];
    // $customerEmail = $_POST['customerEmail'];
    $customerBusinessStyle = $_POST['customerBusinessStyle'];

    // Insert data into the customers table
    $query = "INSERT INTO customers (customerCode, customerName, customerPaymentMethod, customerBillingAddress, customerTin, contactNumber, customerDeliveryType, customerTerms, customerBusinessStyle) VALUES (:customerCode, :customerName, :customerPaymentMethod, :customerBillingAddress, :customerTin, :contactNumber, :customerDeliveryType, :customerTerms, :customerBusinessStyle)";
    
    $stmt = $db->prepare($query);

    $stmt->bindParam(':customerCode', $customerCode);
    $stmt->bindParam(':customerName', $customerName);
    $stmt->bindParam(':customerPaymentMethod', $customerPaymentMethod);
    $stmt->bindParam(':customerBillingAddress', $customerBillingAddress);
    // $stmt->bindParam(':customerShippingAddress', $customerShippingAddress);
    $stmt->bindParam(':customerTin', $customerTin);
    $stmt->bindParam(':contactNumber', $contactNumber);
    $stmt->bindParam(':customerDeliveryType', $customerDeliveryType);
    $stmt->bindParam(':customerTerms', $customerTerms);
    // $stmt->bindParam(':customerEmail', $customerEmail);
    $stmt->bindParam(':customerBusinessStyle', $customerBusinessStyle);

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
