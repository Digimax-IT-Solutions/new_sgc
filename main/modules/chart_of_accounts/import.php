<?php
require '../../connect.php'; // Include your database connection script
require __DIR__ . '../../../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if (isset($_POST['import'])) {
    // Check if file was uploaded without errors
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $inputFileName = $_FILES['file']['tmp_name'];

        // Load the Excel file
        $spreadsheet = IOFactory::load($inputFileName);

        // Get the first worksheet in the Excel file
        $worksheet = $spreadsheet->getActiveSheet();

        // Define the SQL statement for inserting data into the items table
        $sql = "INSERT INTO chart_of_accounts (account_type, account_name, description) 
        VALUES (?, ?, ?)";


        // Prepare the SQL statement
        $stmt = $db->prepare($sql);

        // Iterate through rows and insert data into the database
        foreach ($worksheet->getRowIterator() as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false); // Loop all cells, even if they're empty

            // Extract data from each cell
            $rowData = [];
            foreach ($cellIterator as $cell) {
                $value = $cell->getValue();
                $rowData[] = $value !== null ? $value : ''; // Replace null values with empty strings
            }

            // Prepare and execute the SQL statement
            $stmt->execute($rowData);
        }

        echo "File uploaded successfully and data imported!";
    } else {
        echo "Error uploading file. Please try again.";
    }
}
?>