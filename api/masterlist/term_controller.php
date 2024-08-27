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
            $stmt = $connection->prepare("INSERT INTO terms (term_name, term_days_due, description) VALUES (?, ?, ?)");

            // Begin transaction
            $connection->beginTransaction();

            $importedCount = 0;
            foreach ($rows as $row) {
                // Trim the row to the first 3 columns
                $row = array_slice($row, 0, 3);
            
                // Ensure all values are set, even if empty
                $row = array_pad($row, 3, null);
            
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

// Delete term
if (get('action') === 'delete') {
    $id = get('id');
    $term = Term::find($id);

    if ($term) {
        $term->delete();
        flashMessage('delete_term', 'Term deleted successfully', FLASH_SUCCESS);
    } else {
        flashMessage('delete_term', 'Invalid term', FLASH_ERROR);
    }
    redirect('../../terms');
}

// Add term
if (post('action') === 'add') {
    $term_name = post('term_name');
    $term_days_due = post('term_days_due');
    $description = post('description');

    try {
        Term::add($term_name, $term_days_due, $description);
        flashMessage('add_term', 'Term added successfully.', FLASH_SUCCESS);
    } catch (Exception $ex) {
        flashMessage('add_term', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../../terms');
}

// Update term
if (post('action') === 'update') {
    $id = post('id');
    $term_name = post('term_name');
    $term_days_due = post('term_days_due');
    $description = post('description');

    try {
        $term = Term::find($id);
        if ($term) {
            $term->term_name = $term_name;
            $term->term_days_due = $term_days_due;
            $term->description = $description;
            $term->update();
            flashMessage('update_term', 'Term updated successfully.', FLASH_SUCCESS);
        } else {
            throw new Exception('Term not found.');
        }
    } catch (Exception $ex) {
        flashMessage('update_term', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../../terms');
}
?>
