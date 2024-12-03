<?php

header('Content-Type: application/json');
ob_clean(); // Clear any output before this point

require_once __DIR__ . '/../../_init.php';

// Add payment method
if (post('action') === 'direct_add') {
    $paymentMethodName = post('payment_method_name');
    $description = post('description');

    try {
        // Check if the payment method already exists
        $existingPaymentMethod = PaymentMethod::findByName($paymentMethodName);

        if ($existingPaymentMethod) {
            // Payment method already exists, return their details
            echo json_encode([
                "success" => true,
                "payment_method" => [
                    "id" => $existingPaymentMethod->id,
                    "payment_method_name" => $existingPaymentMethod->payment_method_name,
                    "description" => $existingPaymentMethod->description
                ],
                "message" => "Payment method already exists."
            ]);
        } else {
            // Insert new payment method if it does not already exist
            $newPaymentMethodId = PaymentMethod::add($paymentMethodName, $description);

            // Retrieve the newly added payment method details
            $newPaymentMethod = PaymentMethod::find($newPaymentMethodId);

            echo json_encode([
                "success" => true,
                "payment_method" => [
                    "id" => $newPaymentMethodId,
                    "name" => $paymentMethodName,
                    "description" => $description
                ],
                "message" => "Payment method added successfully."
            ]);
        }
    } catch (Exception $ex) {
        echo json_encode([
            "success" => false,
            "message" => $ex->getMessage()
        ]);
    }
    exit;
}
