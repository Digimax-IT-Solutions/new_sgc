<?php

require_once __DIR__ . '/../_init.php';

// Add account to chart of accounts
// In your controller

if (post('action') === 'add') {
    $companyData = [
        'company_name' => post('companyName'),
        'logo' => $_FILES['logo']['name'], // Handle the file upload for logo
        'address' => post('address'),
        'zip_code' => post('zipCode'),
        'contact' => post('contact'),
        'tin' => post('tin'),
    ];

    try {
        $companySetting = new CompanySetting($companyData);
        $companySetting->save(); // Call save method to insert or update the data
        flashMessage('add', 'Company Setting Updated.', FLASH_SUCCESS);
    } catch (Exception $ex) {
        flashMessage('add', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../company_settings'); // Adjust the redirect URL as necessary
}
