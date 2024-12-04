<?php

header('Content-Type: application/json');
ob_clean(); // Clear any output before this point

require_once __DIR__ . '/../../_init.php';

// Add vendor with direct JSON response
if (post('action') === 'direct_add') {
    // Sanitize and validate input
    $vendor_name = trim(post('vendor_name'));
    $vendor_code = trim(post('vendor_code') ?? '');
    $account_number = trim(post('account_number') ?? '');
    $vendor_address = trim(post('vendor_address') ?? '');
    $contact_number = trim(post('contact_number') ?? '');
    $email = trim(post('email') ?? '');
    $terms = trim(post('terms') ?? '');
    $tin = trim(post('tin') ?? '');
    $tax_type = trim(post('tax_type') ?? '');
    $tel_no = trim(post('tel_no') ?? '');
    $fax_no = trim(post('fax_no') ?? '');
    $notes = trim(post('notes') ?? '');
    $item_type = trim(post('item_type') ?? '');

    try {
        // Validate vendor name
        if (empty($vendor_name)) {
            throw new Exception('Vendor name is required');
        }

        // Check if the vendor already exists
        $existingVendor = Vendor::findByCodeOrName($vendor_name);

        if ($existingVendor) {
            // Vendor already exists, return their details
            echo json_encode([
                "success" => true, 
                "vendor" => [
                    "id" => $existingVendor['id'],
                    "vendor_name" => $existingVendor['vendor_name']
                ], 
                "message" => "Vendor already exists."
            ]);
            exit;
        }

        // Insert new vendor
        $newVendorId = Vendor::add(
            $vendor_name, 
            $vendor_code, 
            $account_number, 
            $vendor_address, 
            $contact_number, 
            $email, 
            $terms, 
            $tin, 
            $tax_type, 
            $tel_no, 
            $fax_no, 
            $notes, 
            $item_type
        );

        // Retrieve the newly added vendor details
        $newVendor = Vendor::find($newVendorId);

        echo json_encode([
            "success" => true, 
            "vendor" => [
                "id" => $newVendorId,
                "vendor_name" => $vendor_name
            ], 
            "message" => "Vendor added successfully."
        ]);
        exit;
    } catch (Exception $ex) {
        echo json_encode([
            "success" => false, 
            "message" => $ex->getMessage()
        ]);
        exit;
    }
}