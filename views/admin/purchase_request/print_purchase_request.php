<?php

require_once '_init.php';
// Include FPDF library
require_once 'plugins/fpdf186/fpdf.php';

try {

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $purchase_request = PurchaseRequest::find($id);

        if ($purchase_request) {

            // Get the check ID from the request
            $pr_id = $_GET['id']; // Assuming the ID is passed as a query parameter

            // Fetch purchase data based on the provided ID
            $purchase_request = PurchaseRequest::find($pr_id);

            // Check if the purchase exists
            if ($purchase_request) {

                // Create a new PDF instance
                $pdf = new FPDF('L', 'mm', 'Letter'); // 'L' for landscape orientation
                $pdf->AddPage();

                // Add a watermark based on the purchase_order status
                if ($purchase_request->status == 3) {
                    // If purchase_order status is 3, add a "VOID" watermark
                    $pdf->SetFont('Arial', 'B', 190);
                    $pdf->RotatedText(55, 190, 'VOID', 45, array(192, 192, 192)); // Light gray color
                } elseif ($purchase_request->status == 4) {
                    // If purchase_order status is 4, add a "DRAFT" watermark
                    $pdf->SetFont('Arial', 'B', 175);
                    $pdf->RotatedText(55, 190, 'DRAFT', 45, array(192, 192, 192)); // Light gray color
                }

                // Set margins
                $pdf->SetMargins(10, 10, 10);

                // Move to top right corner for "Original Copy"
                $pdf->SetXY(-40, 10);
                $pdf->SetFont('Arial', '', 8);

                if ($purchase_request->print_status == 1) {
                    $statusText = 'Original Copy';
                } else {
                    $statusText = 'Reprinted Copy';
                }

                $pdf->Cell(30, 5, $statusText, 0, 0, 'R');
                // Reset position for banner
                $pdf->SetXY(10, 10);

                // Calculate banner position
                // Calculate banner position
                $pageWidth = $pdf->GetPageWidth();
                $bannerWidth = 40; // Adjust as needed
                $bannerX = ($pageWidth - $bannerWidth) / 2;

                // Add banner image
                $pdf->Image('photos/banner.png', 10, 15, $bannerWidth, 0, 'PNG');

                // Move below banner
                $pdf->SetY($pdf->GetY() + 20);

                // Company details centered under banner
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(0, 4, 'Pilar Development Compound, Warehouse 3 (Unit 2) Rose Ave, Pilar Village,', 0, 1, 'R');
                $pdf->Cell(0, 4, 'Barangay Almanza, Las Pinas, Philippines', 0, 1, 'R');
                $pdf->Cell(0, 4, 'VAT Reg. TIN: ', 0, 1, 'R');
                $pdf->Cell(0, 4, 'Tel. No: ', 0, 1, 'R');


                $pdf->Ln();

                // Sales Invoice title and number
                $pdf->SetFont('Arial', 'B', 14);
                $pdf->Cell(100, 10, 'Purchase Request', 0, 0, 'L');
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(0, 10, 'PR No. ' . $purchase_request->pr_no, 0, 1, 'R');

                // Add check data to the PDF
                // Set font and size
                $pdf->SetFont('Arial', '', 9);

                // Set the left cell width
                $leftCellWidth = 95; // Adjust this value as needed
                $rightCellWidth = 165; // Adjust this value as needed

                // Set the Y position to align the cells vertically
                $pdf->Cell($leftCellWidth, 6, 'Date: ' . $purchase_request->date, 0, 0, 'L');
                $pdf->Cell($rightCellWidth, 6, 'Date Required: ' . $purchase_request->required_date, 0, 1, 'R');

                $pdf->Cell($leftCellWidth, 6, 'Cost Center: ' . $purchase_request->cost_center, 0, 0, 'L');
                $pdf->Cell($rightCellWidth, 6, 'Requesting Section: ' . $purchase_request->location, 0, 1, 'R');

                $pdf->Ln();
                $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 260, $pdf->GetY()); // Draw a line
                $pdf->SetFont('Arial', 'B', 12);
                $pdf->Cell(260, 10, 'Comparison of Quotation ' . '', 1, 0, 'R');

                // Table headers
                $pdf->Ln();
                $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 260, $pdf->GetY()); // Draw a line

                // Set font for headers
                $pdf->SetFont('Arial', 'B', 9);

                // Add header cells with borders
                $pdf->Cell(30, 10, 'Item Code', 1, 0, 'L'); // Border on all sides
                $pdf->Cell(40, 10, 'Item Name', 1, 0, 'L'); // Border on all sides
                $pdf->Cell(45, 10, 'Description', 1, 0, 'L'); // Border on all sides
                $pdf->Cell(25, 10, 'Quantity', 1, 0, 'C'); // Border on all sides
                $pdf->Cell(25, 10, 'U/M', 1, 0, 'C'); // Border on all sides

                // Store the X position for the vertical line after "U/M"
                $verticalLineAfterUM = $pdf->GetX() + 0; // Adjust based on U/M column width

                // Create cells with 'Comparison of Quotation' and add vertical line positions
                for ($i = 0; $i < 5; $i++) {
                    $pdf->Cell(19, 10, '', 1, 0, 'L'); // Border on all sides
                    $currentX = $pdf->GetX();
                    $verticalLinesX[] = $currentX;
                }

                $pdf->Ln();

                // Set initial Y position for the vertical lines
                $initialY = $pdf->GetY();

                // Rows loop for 10 rows
                $pdf->SetFont('Arial', '', 9);
                for ($row = 0; $row < 7; $row++) {

                    $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 260, $pdf->GetY()); // Draw a line

                    if ($purchase_request && isset($purchase_request->details[$row])) {
                        $detail = $purchase_request->details[$row];
                        $startY = $pdf->GetY();
                        $currentX = $pdf->GetX();

                        // Item Code
                        $pdf->Cell(30, 5, $detail['item_code'], 0, 0, 'L');

                        // Capture the starting X and Y positions
                        $startX = $pdf->GetX();

                        // Determine the height of the largest cell
                        $pdf->SetX($startX); // Adjust position for Item Name
                        $pdf->MultiCell(40, 5, $detail['item_name'], 0, 'L');
                        $itemNameHeight = $pdf->GetY() - $startY;

                        // Move to the next column for Description and calculate height
                        $pdf->SetXY($startX + 50, $startY); // Adjust position for Description
                        $pdf->MultiCell(45, 5, $detail['item_purchase_description'], 0, 'L');
                        $descriptionHeight = $pdf->GetY() - $startY;

                        // Get the maximum height needed for the row
                        $maxHeight = max($itemNameHeight, $descriptionHeight);

                        // Reset Y to the top of the row and print Quantity and Unit
                        $pdf->SetXY($startX + 95, $startY); // Adjust X position for Quantity and U/M
                        $pdf->Cell(25, $maxHeight, $detail['quantity'], 0, 0, 'C');
                        $pdf->Cell(25, $maxHeight, $detail['name'], 0, 0, 'C');

                        // Move the Y position to the bottom of the current row
                        $pdf->SetY($startY + $maxHeight);
                    } else {
                        // Add empty cells for missing details
                        $pdf->Cell(30, 5, '', 0, 0, 'L');
                        $pdf->Cell(40, 5, '', 0, 0, 'L');
                        $pdf->Cell(45, 5, '', 0, 0, 'L');
                        $pdf->Cell(25, 5, '', 0, 0, 'C');
                        $pdf->Cell(25, 5, '', 0, 0, 'C');
                        $pdf->Ln();
                    }
                }

                // Draw vertical lines after 10 rows, including after U/M
                foreach ($verticalLinesX as $xPosition) {
                    $pdf->Line($xPosition, $initialY, $xPosition, $pdf->GetY());
                }

                // Draw the vertical line after U/M
                $pdf->Line($verticalLineAfterUM, $initialY, $verticalLineAfterUM, $pdf->GetY());

                // Draw a final line at the bottom
                $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 260, $pdf->GetY());


                $pdf->Ln(); // Move to the next line after the last row

                $pdf->SetFont('Arial', '', 8);
                $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 260, $pdf->GetY()); // Draw a line
                // First row with labels
                $pdf->Cell(50, 10, 'Requisitioned by:', 0, 0, 'L');
                $pdf->Cell(70, 10, 'Certified as to non-availability of stock:', 0, 0, 'L');
                $pdf->Cell(70, 10, 'Verified by:', 0, 0, 'L');
                $pdf->Cell(70, 10, 'Approved by:', 0, 1, 'L'); // Line break after the last cell

                // Second row with names/values
                $pdf->Cell(50, 10, '', 0, 0, 'L'); // Empty cell under 'Requisitioned by'
                $pdf->Cell(70, 5, '', 0, 0, 'L'); // Name under 'Certified as to non-availability of stock'
                $pdf->Cell(70, 5, '', 0, 0, 'L'); // Empty cell under 'Verified by'
                $pdf->Cell(70, 5, '', 0, 1, 'L'); // Empty cell under 'Approved by'

                // Third row with names/values
                $pdf->Cell(50, 5, '', 0, 0, 'L'); // Empty cell under 'Requisitioned by'
                $pdf->Cell(70, 5, '', 0, 0, 'L'); // Name under 'Certified as to non-availability of stock'
                $pdf->Cell(70, 5, '', 0, 0, 'L'); // Empty cell under 'Verified by'
                $pdf->Cell(70, 5, '', 0, 1, 'L'); // Empty cell under 'Approved by'

                $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 260, $pdf->GetY()); // Draw a line

                $pdf->Ln();
                // Set font and size for table content
                $pdf->SetFont('Arial', '', 9);
                // Define the width of the cell
                $cellWidth = 150; // Use 0 for auto width
                // Define the height of each line
                $lineHeight = 5; // Adjust as needed
                // Wrap text and add memo to PDF cell
                $pdf->MultiCell($cellWidth, $lineHeight, 'Memo: ' . $purchase_request->memo, 0, 'L', false);

                // Add additional details in the table footer
                $pdf->SetFont('Arial', '', 8);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(0, 5, '"THIS DOCUMENT IS NOT VALID FOR CLAIM OF INPUT TAX"', 0, 1, 'C');

                // Add the following code after adding content to your PDF document
                $pdf->SetXY(10, 185); // Adjust the X and Y coordinates as needed
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(0, 3, 'Acknowledgment Certificate Control Number:                        Date Issued:' . date('m/d/Y'), 0, 1, 'L');
                $pdf->Cell(0, 3, 'Series No.: 000000001 to 999999999', 0, 1, 'L');
                $pdf->Cell(0, 3, 'Date and Time Printed: ' . date('m/d/Y h:i:sA'), 0, 1, 'L');

                // Output the PDF
                $pdf->Output();
            } else {
                // Handle the case where the check is not found
                echo "PO not found.";
                exit;
            }
        } else {
            // Handle the case where the ID is not provided
            echo "No ID provided.";
            exit;
        }
    } else {
        // Handle the case where the check ID is invalid or not found
        echo "PO not found!";
    }
} catch (Exception $ex) {
    // Handle any exceptions
    echo "Error: " . $ex->getMessage();
}
