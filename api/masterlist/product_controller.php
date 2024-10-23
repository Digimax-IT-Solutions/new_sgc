<?php
require_once __DIR__ . '/../../_init.php';

require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'upload') {
    header('Content-Type: application/json');

    if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] == 0) {
        $file_tmp = $_FILES['excel_file']['tmp_name'];

        try {
            // Load the spreadsheet and get the active sheet
            $spreadsheet = IOFactory::load($file_tmp);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Remove header row
            array_shift($rows);

            // Prepare SQL statement
            $stmt = $connection->prepare("INSERT INTO items (item_name, item_code, item_type, item_vendor_id, item_uom_id, item_reorder_point, item_category_id, item_sales_description, item_purchase_description, item_selling_price, item_cost_price, item_cogs_account_id, item_income_account_id, item_asset_account_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

            // Begin transaction
            $connection->beginTransaction();

            $importedCount = 0;
            foreach ($rows as $row) {
                // Trim the row to the first 13 columns
                $row = array_slice($row, 0, 14);

                // Ensure all values are set, even if empty
                $row = array_pad($row, 14, null);

                // Trim whitespace and replace empty strings with null
                $row = array_map(function ($value) {
                    return trim($value) === '' ? null : trim($value);
                }, $row);

                // Check if the row is not entirely empty
                if (array_filter($row, function ($value) {
                    return $value !== null;
                })) {
                    if ($stmt->execute($row)) {
                        $importedCount++;
                    } else {
                        echo json_encode(['status' => 'error', 'message' => 'SQL Error: ' . print_r($stmt->errorInfo(), true)]);
                        exit; // Exit to avoid continuing on error
                    }
                }
            }

            // Commit transaction
            $connection->commit();
            echo json_encode(['status' => 'success', 'message' => "Upload successful. $importedCount records imported."]);
        } catch (Exception $e) {
            $connection->rollBack();
            echo json_encode(['status' => 'error', 'message' => "Error: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => "No file uploaded or an error occurred."]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => "Invalid request method or action."]);
}


if (get('action') === 'add') {
    $item_name = post('item_name'); // Required, no default
    $item_code = post('item_code'); // Can be NULL, no default needed
    $item_type = post('item_type'); // Can be NULL, no default needed
    $vendor_id = !empty($_POST['item_vendor_id']) ? $_POST['item_vendor_id'] : 0; // Default to 0
    $item_uom_id = !empty(post('item_uom_id')) ? post('item_uom_id') : 0; // Default to 0
    $item_reorder_point = !empty(post('item_reorder_point')) ? post('item_reorder_point') : 0.00; // Default to 0.00
    $item_category_id = !empty(post('item_category_id')) ? post('item_category_id') : 0; // Default to 0
    $item_quantity = !empty(post('item_quantity')) ? post('item_quantity') : 0; // Default to 0
    $item_sales_description = post('item_sales_description'); // Can be NULL, no default needed
    $item_purchase_description = post('item_purchase_description'); // Can be NULL, no default needed
    $item_selling_price = !empty(post('item_selling_price')) ? post('item_selling_price') : 0.00; // Default to 0.00
    $item_cost_price = !empty(post('item_cost_price')) ? post('item_cost_price') : 0.00; // Default to 0.00
    $item_cogs_account_id = !empty(post('item_cogs_account_id')) ? post('item_cogs_account_id') : 0; // Default to 0
    $item_income_account_id = !empty(post('item_income_account_id')) ? post('item_income_account_id') : 0; // Default to 0
    $item_asset_account_id = !empty(post('item_asset_account_id')) ? post('item_asset_account_id') : 0; // Default to 0


    try {
        Product::add(
            $item_name,
            $item_code,
            $item_type,
            $item_vendor_id,
            $item_uom_id,
            $item_reorder_point,
            $item_category_id,
            $item_quantity,
            $item_sales_description,
            $item_purchase_description,
            $item_selling_price,
            $item_cost_price,
            $item_cogs_account_id,
            $item_income_account_id,
            $item_asset_account_id
        );
        flashMessage('add', 'New Product Added!', FLASH_SUCCESS);
    } catch (Exception $ex) {
        flashMessage('add', 'An error occurred: ' . $ex->getMessage(), FLASH_ERROR);
    }
    redirect('../../item_list');
}

if (get('action') === 'delete') {
    $id = get('id');

    Product::find($id)?->delete();

    flashMessage('delete', 'Product deleted successfully.', FLASH_SUCCESS);
    redirect('../../item_list');
}

if (post('action') === 'update') {
    $id = post('id');
    $product = Product::find($id);

    if ($product) {
        $product->item_name = post('item_name'); // Required
        $product->item_code = post('item_code'); // Can be NULL or provided
        $product->item_type = post('item_type'); // Can be NULL or provided
        $product->item_vendor_id = !empty(post('item_vendor_id')) ? (int)post('item_vendor_id') : 0; // Default to 0 if empty
        $product->item_uom_id = !empty(post('item_uom_id')) ? (int)post('item_uom_id') : 0; // Default to 0 if empty
        $product->item_reorder_point = !empty(post('item_reorder_point')) ? post('item_reorder_point') : 0.00; // Default to 0.00 if empty
        $product->item_category_id = !empty(post('item_category_id')) ? (int)post('item_category_id') : 0; // Default to 0 if empty
        $product->item_quantity = !empty(post('item_quantity')) ? (int)post('item_quantity') : 0; // Default to 0 if empty
        $product->item_sales_description = post('item_sales_description'); // Can be NULL or provided
        $product->item_purchase_description = post('item_purchase_description'); // Can be NULL or provided
        $product->item_selling_price = !empty(post('item_selling_price')) ? post('item_selling_price') : 0.00; // Default to 0.00 if empty
        $product->item_cost_price = !empty(post('item_cost_price')) ? post('item_cost_price') : 0.00; // Default to 0.00 if empty

        // Convert empty strings to null for integer fields if they are not provided
        $product->item_cogs_account_id = !empty(post('item_cogs_account_id')) ? (int)post('item_cogs_account_id') : null;
        $product->item_asset_account_id = !empty(post('item_asset_account_id')) ? (int)post('item_asset_account_id') : null;
        $product->item_income_account_id = !empty(post('item_income_account_id')) ? (int)post('item_income_account_id') : null;

        try {
            $product->update(); // Perform the update
            flashMessage('update_product', 'Product updated successfully.', FLASH_SUCCESS);
        } catch (Exception $ex) {
            flashMessage('update_product', $ex->getMessage(), FLASH_ERROR);
        }
    } else {
        flashMessage('update_product', 'Invalid product ID.', FLASH_ERROR);
    }

    redirect('../../item_list');
}
