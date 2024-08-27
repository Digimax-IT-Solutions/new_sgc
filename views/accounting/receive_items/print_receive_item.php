<?php

require_once '_init.php';
// Include FPDF library
require_once 'plugins/fpdf186/fpdf.php';


try {

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $receive_items = ReceivingReport::find($id);

        if ($receive_items) {

            // Get the check ID from the request
            $receive_id = $_GET['id']; // Assuming the ID is passed as a query parameter

            // Fetch purchase data based on the provided ID
            $receive_items = ReceivingReport::find($receive_id);

            // Check if the purchase exists
            if ($receive_items) {

                // Create a new PDF instance
                $pdf = new FPDF();
                $pdf->AddPage();

                // Add a watermark based on the receive_items status
                if ($receive_items->receive_status == 3) {
                    // If receive_items status is 3, add a "VOID" watermark
                    $pdf->SetFont('Arial', 'B', 190);
                    $pdf->RotatedText(55, 190, 'VOID', 45, array(192, 192, 192)); // Light gray color
                } elseif ($receive_items->receive_status == 4) {
                    // If receive_items status is 4, add a "DRAFT" watermark
                    $pdf->SetFont('Arial', 'B', 175);
                    $pdf->RotatedText(55, 190, 'DRAFT', 45, array(192, 192, 192)); // Light gray color
                }


                // Set margins
                $pdf->SetMargins(10, 10, 10);

                // Move to top right corner for "Original Copy"
                $pdf->SetXY(-40, 10);
                $pdf->SetFont('Arial', '', 8);

                // Reset position for banner
                $pdf->SetXY(10, 10);
        
                // Calculate banner position
                $pageWidth = $pdf->GetPageWidth();
                $bannerWidth = 80; // Adjust as needed
                $bannerX = ($pageWidth - $bannerWidth) / 2;
        
                // Add banner image
                $pdf->Image('photos/banner.png', 2, 15, $bannerWidth, 0, 'PNG');
        
                // Move below banner
                $pdf->SetY($pdf->GetY() + 8);
        
                // Company details centered under banner
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(0, 4, 'Montebello, Kanaga, Leyte, 6531', 0, 1, 'R');
                $pdf->Cell(0, 4, 'VAT Reg. TIN: 000-123-533-00000', 0, 1, 'R');
                $pdf->Cell(0, 4, 'Tel. No: +63 (53) 553 0058', 0, 1, 'R');
        
                $pdf->SetY($pdf->GetY() + 15);
                $pdf->SetFont('Arial', 'B', 14);
                $pdf->Cell(0, 4, 'RECEIVING REPORT', 0, 3, 'C');
        
        
                $pdf->Ln(5);
        
                $pdf->SetFont('Arial', '', 11);
                $pdf->Cell(175.8, 10, 'RR NO: ' . $receive_items->receive_no, 0, 1, 'R');
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
                $textLeft = 'Date: ' . $receive_items->receive_date;
                $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textLeft, 0, 1, 'L');



                // Text to be displayed on the right
                $textRight = 'Vendor: ' . $receive_items->vendor_name;
                $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textRight, 0, 0, 'L');

                $textRight = 'Delivery Date: ' . $receive_items->receive_date;
                $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textRight, 0, 1, 'L');

                // Text to be displayed on the left
                $textLeft = 'Address: ' . $receive_items->location;
                $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textLeft, 0, 0, 'L');
                // Text to be displayed on the left
                $textLeft = 'Terms: ' . $receive_items->terms;
                $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textLeft, 0, 1, 'L');



                $pdf->Ln(2);

                // Table headers

                // Table headers
                $pdf->Ln(); // Move to the next line
                $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); // Draw a line

                // Adjusting the column widths
                $pdf->Cell(40, 10, 'Item Name', 0, 0, 'L');
                $pdf->Cell(60, 10, 'Description', 0, 0, 'L');
                $pdf->Cell(20, 10, 'Quantity', 0, 0, 'C');
                $pdf->Cell(20, 10, 'Unit', 0, 0, 'C');
                $pdf->Cell(25, 10, 'Cost', 0, 0, 'R');
                $pdf->Cell(25, 10, 'Amount', 0, 0, 'R');
                $pdf->Ln(); // Move to the next line
                $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); // Draw a line

                $totalCost = 0;
                $totalAmount = 0;

                if ($receive_items) {
                    foreach ($receive_items->details as $detail) {
                        $totalCost += $detail['cost'];
                        $totalAmount += $detail['amount'];

                        $startY = $pdf->GetY();
                        $currentX = $pdf->GetX();

                        // Item Name
                        $pdf->Cell(40, 5, $detail['item_name'], 0, 0, 'L');

                        // Description (using MultiCell)
                        $pdf->SetXY($currentX + 40, $startY);
                        $pdf->MultiCell(60, 5, $detail['item_purchase_description'], 0, 'L');

                        $endY = $pdf->GetY();

                        // Other details
                        $pdf->SetXY($currentX + 100, $startY);
                        $pdf->Cell(20, 5, $detail['quantity'], 0, 0, 'C');
                        $pdf->Cell(20, 5, $detail['uom_name'], 0, 0, 'C');
                        $pdf->Cell(25, 5, number_format($detail['cost'], 2), 0, 0, 'R');
                        $pdf->Cell(25, 5, number_format($detail['amount'], 2), 0, 0, 'R');

                        // Move to the next line, considering the height of the description
                        $pdf->SetY($endY);
                    }
                }


                $pdf->SetFont('Arial', 'B', 10); // 'B' indicates bold
                $pdf->Cell(165, 10, 'Total:', 0, 0, 'R');
                $pdf->Cell(30, 10, 'P' . number_format($totalAmount, 2), 0, 0, 'L');
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
                $pdf->MultiCell($cellWidth, $lineHeight, 'Memo: ' . $receive_items->memo, 0, 'L', false);

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

