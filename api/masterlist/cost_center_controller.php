<?php

require_once __DIR__ . '/../../_init.php';
// require __DIR__ . '/../../vendor/autoload.php';

// Delete Cost Center
if (get('action') === 'delete') {
    $id = get('id');
    $cost_center = CostCenter::find($id);

    if ($cost_center) {
        $cost_center->delete();
        flashMessage('delete_cost_center', 'Cost center deleted successfully.', FLASH_SUCCESS);
    } else {
        flashMessage('delete_cost_center', 'Invalid cost center.', FLASH_ERROR);
    }
    redirect('../../cost_center');
}

// Add Cost Center
if (post('action') === 'add') {
    $code = post('code');
    $particular = post('particular');

    try {
        CostCenter::add($code, $particular);
        flashMessage('add_cost_center', 'Cost center added successfully.', FLASH_SUCCESS);
    } catch (Exception $ex) {
        flashMessage('add_cost_center', $ex->getMessage(), FLASH_ERROR);
    }

    redirect('../../cost_center');
}

// Update Cost Center
if (post('action') === 'update') {

    $code = post('code');
    $particular = post('particular');
    $id = post('id');

    try {
        $cost_center = CostCenter::find($id);

        if ($cost_center) {
            $cost_center->code = $code;
            $cost_center->particular = $particular;
            $cost_center->update();

            flashMessage('update_cost_center', 'Cost center updated successfully.', FLASH_SUCCESS);
            redirect('../../cost_center');

        } else {
            flashMessage('update_cost_center', 'Cost center not found.', FLASH_ERROR);
            redirect('../../cost_center');
        }
    } catch (Exception $ex) {
        flashMessage('update_cost_center', $ex->getMessage(), FLASH_ERROR);
        redirect('../../cost_center');
    }
}

// Handle Excel File Upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excelFile'])) {
    if ($_FILES['excelFile']['error'] === UPLOAD_ERR_OK) {
        $tmpFilePath = $_FILES['excelFile']['tmp_name'];
        $excelData = readExcelData($tmpFilePath);

        // Loop through Excel data and add cost centers
        foreach ($excelData as $row) {
            CostCenter::add(
                $row['code'],
                $row['particular']
            );
        }

        flashMessage('import_cost_center', 'Cost centers imported successfully.', FLASH_SUCCESS);
    } else {
        flashMessage('import_cost_center', 'Error uploading file.', FLASH_ERROR);
    }
    redirect('../../cost_center');

}

// function readExcelData($filePath)
// {
//     // Assuming PHPSpreadsheet is used to read Excel data
//     $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);
//     $spreadsheet = $reader->load($filePath);
//     $worksheet = $spreadsheet->getActiveSheet();
//     $highestRow = $worksheet->getHighestRow();
//     $highestColumn = $worksheet->getHighestColumn();
//     $data = [];

//     for ($row = 2; $row <= $highestRow; ++$row) {
//         $rowData = [];
//         for ($col = 'A'; $col <= $highestColumn; ++$col) {
//             $cellValue = $worksheet->getCell($col . $row)->getValue();
//             $rowData[] = $cellValue;
//         }
//         $data[] = [
//             'code' => $rowData[0] ?? '',
//             'particular' => $rowData[1] ?? '',
//         ];
//     }

//     return $data;
// }
