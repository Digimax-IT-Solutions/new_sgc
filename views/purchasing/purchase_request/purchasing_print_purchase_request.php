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
                $pdf = new FPDF();
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
                $pageWidth = $pdf->GetPageWidth();
                $bannerWidth = 80; // Adjust as needed
                $bannerX = ($pageWidth - $bannerWidth) / 2;

                // Add banner image
                $pdf->Image('photos/banner.png', $bannerX, 10, $bannerWidth, 0, 'PNG');

                // Move below banner
                $pdf->SetY($pdf->GetY() + 20);

                // Company details centered under banner
                $pdf->SetFont('Arial', '', 7);
                $pdf->Cell(0, 4, 'Montebello, Kanaga, Leyte, 6531', 0, 1, 'C');
                $pdf->Cell(0, 4, 'VAT Reg. TIN: 000-123-533-00000', 0, 1, 'C');
                $pdf->Cell(0, 4, 'Tel. No: +63 (53) 553 0058', 0, 1, 'C');

                $pdf->Ln(5);

                // Sales Invoice title and number
                $pdf->SetFont('Arial', 'B', 14);
                $pdf->Cell(100, 10, 'Purchase Request', 0, 0, 'L');
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(0, 10, 'PR No. ' . $purchase_request->pr_no, 0, 1, 'R');
                $pdf->Ln(5);




                // Add check data to the PDF
                // Set font and size
                $pdf->SetFont('Arial', '', 9);

                // Set maximum width of the cell
                $maxWidth = 80; // Adjust according to your column width requirement

                // Set line height
                $lineHeight = 5; // Adjust according to your line spacing requirement

                // Set padding for the cells (space between left-aligned and right-aligned text)
                $padding = -100; // Adjust as needed


                // Text to be displayed on the left
                $textLeft = 'Date: ' . $purchase_request->date;
                $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textLeft, 0, 1, 'L');


                $textRight = 'Requesting Section: ' . $purchase_request->location;
                $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textRight, 0, 0, 'L');

                $textRight = 'Date Required: ' . $purchase_request->required_date;
                $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textRight, 0, 1, 'L');


                $pdf->Ln(2);

                // Table headers
                $pdf->Ln();
                $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY());

                // Adjusting the column widths and order
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(20, 10, 'Item Code', 0, 0, 'L');
                $pdf->Cell(60, 10, 'Item Name', 0, 0, 'L');
                $pdf->Cell(60, 10, 'Description', 0, 0, 'L');
                $pdf->Cell(20, 10, 'Quantity', 0, 0, 'C');
                $pdf->Cell(20, 10, 'Unit', 0, 0, 'C');
                $pdf->Ln();
                $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY());

                $pdf->SetFont('Arial', '', 9);
                if ($purchase_request) {
                    foreach ($purchase_request->details as $detail) {
                        $startY = $pdf->GetY();
                        $currentX = $pdf->GetX();

                        // Item Code
                        $pdf->Cell(20, 5, $detail['item_code'], 0, 0, 'L');

                        // Item Name
                        $pdf->Cell(30, 5, $detail['item_name'], 0, 0, 'L');

                        // Description (using MultiCell)
                        $pdf->SetXY($currentX + 80, $startY);
                        $pdf->MultiCell(60, 5, $detail['item_purchase_description'], 0, 'L');

                        $endY = $pdf->GetY();

                        // Quantity, Unit, and Ordered Quantity
                        $pdf->SetXY($currentX + 140, $startY);
                        $pdf->Cell(20, 5, $detail['quantity'], 0, 0, 'C');
                        $pdf->Cell(20, 5, $detail['name'], 0, 0, 'C');

                        // Move to the next line, considering the height of the description
                        $pdf->SetY($endY);
                    }
                }

                $pdf->Ln(15); // Move to the next line after the last row

                $pdf->SetFont('Arial', '', 8);
                $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); // Draw a line
                $pdf->Cell(50, 10, 'PREPARED/POSTED BY:', 0, 0, 'L');
                $pdf->Cell(50, 10, 'CHECKED/CERTIFIED BY:', 0, 0, 'L');
                $pdf->Cell(50, 10, 'VERIFIED FOR PAYMENT BY:', 0, 0, 'L');
                $pdf->Cell(40, 10, 'PAYMENT APPROVED BY:', 0, 0, 'L');
                $pdf->Ln(15); // Move to the next line
                $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); // Draw a line

                $pdf->Ln(2);

                // Set font and size for table content
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


                $pdf->Ln(10);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(0, 10, '"THIS DOCUMENT IS NOT VALID FOR CLAIM OF INPUT TAX"', 0, 1, 'C');
                $pdf->Ln(20);


                // Add the following code after adding content to your PDF document
                $pdf->SetXY(10, 265); // Adjust the X and Y coordinates as needed
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

