<?php

require_once __DIR__ . '/../../_init.php';

// Add account to chart of accounts
if (post('action') === 'add') {

    $account_code = post('account_code');
    $account_type_id = post('account_type_id');
    $account_name = post('account_name');
    $account_description = post('account_description');
    $fs_classification = post('fs_classification');
    $fs_notes_classification = post('fs_notes_classification');
    $sub_account_id = post('sub_account_id');
    // Check if sub_account_id is empty and set it to 0 if it is
    if (empty($sub_account_id)) {
        $sub_account_id = 0;
    }


    try {
        ChartOfAccount::add($account_code, $account_type_id, $account_name, $account_description, $sub_account_id);
        flashMessage('add_account', 'New account added.', FLASH_SUCCESS);
    } catch (Exception $ex) {
        flashMessage('add_account', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../../chart_of_accounts'); // You may need to adjust the redirect URL
}


//Update account
// Update account
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] === 'update') {
    $id = post('id');
    $account_code = post('account_code');
    $account_type_id = post('account_type_id');
    $account_name = post('account_name');
    $account_description = post('account_description');
    $sub_account_id = post('sub_account_id');

    // Check if sub_account_id is empty and set it to 0 if it is
    if (empty($sub_account_id)) {
        $sub_account_id = 0;
    }

    try {
        // Find the existing ChartOfAccount instance
        $chart_of_account = ChartOfAccount::findById($id);

        // Check if the account exists
        if (!$chart_of_account) {
            throw new Exception('Account not found');
        }

        // Perform the update operation with arguments
        $chart_of_account->update($account_code, $account_type_id, $account_name, $account_description, $sub_account_id);

        // Flash message and redirect upon success
        flashMessage('update_account', 'Account has been updated!', FLASH_SUCCESS);
        redirect("../../edit_chart_of_account?id={$id}");
    } catch (Exception $ex) {
        // Handle errors
        flashMessage('update_account', $ex->getMessage(), FLASH_ERROR);
        redirect("../../edit_chart_of_account?id={$id}");
    }
}

//Delete category
// Delete chart of account
if (get('action') === 'delete') {
    $id = get('id');

    $account = ChartOfAccount::findById($id);

    if ($account) {
        $account->delete();
        flashMessage('delete', 'Chart of account deleted', FLASH_SUCCESS);
    } else {
        flashMessage('delete', 'Invalid chart of account', FLASH_ERROR);
    }
    redirect('../../chart_of_accounts');
}
