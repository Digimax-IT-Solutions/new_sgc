<?php

header('Content-Type: application/json');
ob_clean(); // Clear any output before this point

require_once __DIR__ . '/../../_init.php';

// Add vendor with direct JSON response
if (post('action') === 'direct_add') {
    $vendor_name = post('vendor_name');
    $vendor_code = post('vendor_code');
    $account_number = post('account_number');
    $vendor_address = post('vendor_address');
    $contact_number = post('contact_number');
    $email = post('email');
    $terms = post('terms');
    $tin = post('tin');
    $tax_type = post('tax_type');
    $tel_no = post('tel_no');
    $fax_no = post('fax_no');
    $notes = post('notes');
    $item_type = post('item_type');

    try {
        // Check if the vendor already exists
        $existingVendor = Vendor::findByCodeOrName($vendor_name);

        if ($existingVendor) {
            // Vendor already exists, return their details
            $existingVendorDetails = [
                "id" => $existingVendor['id'],
                "vendor_name" => $existingVendor['vendor_name']
            ];

            echo json_encode(["success" => true, "vendor" => $existingVendorDetails, "message" => "Vendor already exists."]);
        } else {
            // Insert new vendor if they do not already exist
            $newVendorId = Vendor::add($vendor_name, $vendor_code, $account_number, $vendor_address, $contact_number, $email, $terms, $tin, $tax_type, $tel_no, $fax_no, $notes, $item_type);

            // Retrieve and prepare the newly added vendor details
            $id = Vendor::getLastId();
            $newVendor = [
                "id" => $id,
                "vendor_name" => $vendor_name
            ];

            echo json_encode(["success" => true, "vendor" => $newVendor, "message" => "Vendor added successfully."]);
        }
    } catch (Exception $ex) {
        echo json_encode(["success" => false, "message" => $ex->getMessage()]);
    }
    exit;
}
