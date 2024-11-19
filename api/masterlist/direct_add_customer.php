<?php

header('Content-Type: application/json');
ob_clean(); // Clear any output before this point

require_once __DIR__ . '/../../_init.php';

if (post('action') === 'direct_add') {
    $customer_name = post('customer_name');
    $customer_code = post('customer_code');
    $customer_contact = post('customer_contact');
    $shipping_address = post('shipping_address');
    $billing_address = post('billing_address');
    $business_style = post('business_style');
    $customer_terms = post('customer_terms');
    $customer_tin = post('customer_tin');
    $customer_email = post('customer_email');

    try {
        // Check if the customer already exists
        $existingCustomer = Customer::findByCodeOrName($customer_name);

        if ($existingCustomer) {
            // Customer already exists, return their details
            $existingCustomerDetails = [
                "id" => $existingCustomer['id'],
                "customer_name" => $existingCustomer['customer_name']
            ];

            echo json_encode(["success" => true, "customer" => $existingCustomerDetails, "message" => "Customer already exists."]);
        } else {
            // Insert new customer if they do not already exist
            $newCustomerId = Customer::add($customer_name, $customer_code, $customer_contact, $shipping_address, $billing_address, $business_style, $customer_terms, $customer_tin, $customer_email);

            // Retrieve and prepare the newly added customer details
            $id = Customer::getLastCustomerId();
            $newCustomer = [
                "id" => $id,
                "customer_name" => $customer_name
            ];

            echo json_encode(["success" => true, "customer" => $newCustomer, "message" => "Customer added successfully."]);
        }
    } catch (Exception $ex) {
        echo json_encode(["success" => false, "message" => $ex->getMessage()]);
    }
    exit;
}
