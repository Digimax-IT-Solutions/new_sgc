<?php
require_once __DIR__ . '/../_init.php';

require_once __DIR__ . '/../vendor/autoload.php';

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
            $stmt = $connection->prepare("INSERT INTO categories (name) VALUES (?)");

            // Begin transaction
            $connection->beginTransaction();

            $importedCount = 0;
            foreach ($rows as $row) {
                // Trim the row to the first 1 columns
                $row = array_slice($row, 0, 1);
            
                // Ensure all values are set, even if empty
                $row = array_pad($row, 1, null);
            
                // Trim whitespace and replace empty strings with null
                $row = array_map(function ($value) {
                    return trim($value) === '' ? null : trim($value);
                }, $row);
            
                // Check if the row is not entirely empty
                if (array_filter($row, function($value) { return $value !== null; })) {
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

// Delete category
if (get('action') === 'delete') {
    $id = get('id');
    $category = Category::find($id);

    if ($category) {
        $category->delete();
        flashMessage('delete_category', 'Category deleted successfully', FLASH_SUCCESS);
    } else {
        flashMessage('delete_category', 'Invalid category', FLASH_ERROR);
    }
    redirect('../category');
}

// Add category
if (post('action') === 'add') {
    $name = post('name');

    try {
        Category::add($name);
        flashMessage('add_category', 'Category added successfully.', FLASH_SUCCESS);
    } catch (Exception $ex) {
        flashMessage('add_category', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../category');
}

// Update category
if (post('action') === 'update') {
    $id = post('id');
    $name = post('name');

    try {
        $category = Category::find($id);
        if ($category) {
            $category->name = $name;
            $category->update();
            flashMessage('update_category', 'Category updated successfully.', FLASH_SUCCESS);
        } else {
            throw new Exception('Category not found.');
        }
    } catch (Exception $ex) {
        flashMessage('update_category', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../category');
}
?>
