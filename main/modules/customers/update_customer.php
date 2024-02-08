<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input data
    $customerCode = $_POST['editCustomerCode'];
    $editCustomerName = $_POST['editCustomerName'];
    $editCustomerPaymentMethod = $_POST['editCustomerPaymentMethod'];
    $editCustomerBillingAddress = $_POST['editCustomerBillingAddress'];
    $editCustomerShippingAddress = $_POST['editCustomerShippingAddress'];
    $editCustomerTin = $_POST['editCustomerTin'];
    $editContactNumber = $_POST['editContactNumber'];
    $editCustomerDeliveryType = $_POST['editCustomerDeliveryType'];
    $editCustomerTerms = $_POST['editCustomerTerms'];
    $editCustomerEmail = $_POST['editCustomerEmail'];

    // Assuming you have a customerID coming from the form
    $customerID = $_POST['editCustomerID'];

    try {
        // Update customer details in the database
        $query = "UPDATE customers 
                  SET customerCode = :customerCode,
                      customerName = :customerName,
                      customerPaymentMethod = :customerPaymentMethod,
                      customerBillingAddress = :customerBillingAddress,
                      customerShippingAddress = :customerShippingAddress,
                      customerTin = :customerTin,
                      contactNumber = :contactNumber,
                      customerDeliveryType = :customerDeliveryType,
                      customerTerms = :customerTerms,
                      customerEmail = :customerEmail 
                  WHERE customerID = :customerID";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':customerID', $customerID);
        $stmt->bindParam(':customerCode', $customerCode);
        $stmt->bindParam(':customerName', $editCustomerName);
        $stmt->bindParam(':customerPaymentMethod', $editCustomerPaymentMethod);
        $stmt->bindParam(':customerBillingAddress', $editCustomerBillingAddress);
        $stmt->bindParam(':customerShippingAddress', $editCustomerShippingAddress);
        $stmt->bindParam(':customerTin', $editCustomerTin);
        $stmt->bindParam(':contactNumber', $editContactNumber);
        $stmt->bindParam(':customerDeliveryType', $editCustomerDeliveryType);
        $stmt->bindParam(':customerTerms', $editCustomerTerms);
        $stmt->bindParam(':customerEmail', $editCustomerEmail);

        if ($stmt->execute()) {
            echo "success";        
        } else {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Error updating customer: " . $errorInfo[2]);
        }
    } catch (Exception $e) {
        echo "Exception: " . $e->getMessage();
    }
} else {
    echo "Invalid request.";
}
?>