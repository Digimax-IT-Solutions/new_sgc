<?php

require_once '_init.php';
// Include FPDF library
require_once 'plugins/fpdf186/fpdf.php';



try {

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $sales_return = SalesReturn::find($id);
        if ($sales_return) {

            // Get the check ID from the request
            $sales_return_id = $_GET['id']; // Assuming the ID is passed as a query parameter

            // Fetch sales_return data based on the provided ID
            $sales_return = SalesReturn::find($sales_return_id);

            // sales_return if the sales_return exists
            if ($sales_return) {

                // Create a new PDF instance
                $pdf = new FPDF();
                $pdf->AddPage();

                 // Add a watermark based on the sales_return status
                if ($sales_return->sales_return_status == 3) {
                    // If sales_return status is 3, add a "VOID" watermark
                    $pdf->SetFont('Arial', 'B', 190);
                    $pdf->RotatedText(55, 190, 'VOID', 45, array(192, 192, 192)); // Light gray color
                } elseif ($sales_return->sales_return_status == 4) {
                    // If sales_return status is 4, add a "DRAFT" watermark
                    $pdf->SetFont('Arial', 'B', 175);
                    $pdf->RotatedText(55, 190, 'DRAFT', 45, array(192, 192, 192)); // Light gray color
                }

                // Set margins
                $pdf->SetMargins(10, 10, 10);

                // Move to top right corner for "Original Copy"
                $pdf->SetXY(-40, 10);
                $pdf->SetFont('Arial', '', 8);

                if ($sales_return->print_status == 1) {
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
                $pdf->Cell(0, 4, 'SALES RETURN', 0, 3, 'C');
                $pdf->Cell(0, 10, 'SR No. ' . $sales_return->sales_return_number, 0, 1, 'R');
                $pdf->Ln(5);

                // Set font and size for the details section
                $pdf->SetFont('Arial', '', 9);

                $lineHeight = 5; // Adjust according to your line spacing requirement
                $rightColumnX = $pageWidth - 60; // X position for the right column

                // Date and Terms
                // sales_return details
                $date = new DateTime($sales_return->sales_return_date);
                $formattedDate = $date->format('m/d/y');
                $pdf->Cell(100, $lineHeight, 'Date: ' . $formattedDate, 0, 0, 'L');
                $pdf->SetX($rightColumnX);
                $pdf->Cell(0, $lineHeight, 'Terms: ' . $sales_return->terms, 0, 1, 'L');

                // Customer and Rep
                $pdf->Cell(100, $lineHeight, 'Customer: ' . $sales_return->customer_name, 0, 0, 'L');
                $pdf->SetX($rightColumnX);
                $pdf->Cell(0, $lineHeight, 'Rep: ' . $sales_return->rep, 0, 1, 'L');

                // Address
                $pdf->Cell(100, $lineHeight, 'Address: ' . $sales_return->shipping_address, 0, 0, 'L');
                $pdf->SetX($rightColumnX);
                $pdf->Cell(0, $lineHeight, '', 0, 1, 'L');

                // Business style and Customer P.O No.
                $pdf->Cell(100, $lineHeight, 'Business Style: ' . $sales_return->business_style, 0, 0, 'L');
                $pdf->SetX($rightColumnX);
                $pdf->Cell(0, $lineHeight, 'Customer P.O No.: ' . $sales_return->customer_po, 0, 1, 'L');

                // TIN and S.O No.
                $pdf->Cell(100, $lineHeight, 'TIN: ' . $sales_return->customer_tin, 0, 0, 'L');
                $pdf->SetX($rightColumnX);
                $pdf->Cell(0, $lineHeight, 'S.O No.: ' . $sales_return->so_no, 0, 1, 'L');

                $pdf->Ln(2);

                // Table headers
                $pdf->Ln(); // Move to the next line
                $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); // Draw a line

                // Adjusting the column widths
                $pdf->Cell(40, 10, 'Item Name', 0, 0, 'L');
                $pdf->Cell(60, 10, 'Description', 0, 0, 'L');
                $pdf->Cell(20, 10, 'Qty', 0, 0, 'C');
                $pdf->Cell(20, 10, 'U/M', 0, 0, 'C');
                $pdf->Cell(25, 10, 'Selling Price', 0, 0, 'C');
                $pdf->Cell(25, 10, 'Amount', 0, 0, 'R');
                $pdf->Ln(); // Move to the next line
                $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); // Draw a line

                $totalCost = 0;
                $totalAmount = 0;

                if ($sales_return) {
                    foreach ($sales_return->details as $detail) {
                        $totalCost += $detail['cost'];
                        $totalAmount += $detail['amount'];

                        $startY = $pdf->GetY();
                        $currentX = $pdf->GetX();

                        // Item Name
                        $pdf->Cell(40, 5, $detail['item_name'], 0, 0, 'L');

                        // Description (using MultiCell)
                        $pdf->SetXY($currentX + 40, $startY);
                        $pdf->MultiCell(60, 5, $detail['item_sales_description'], 0, 'L');

                        $endY = $pdf->GetY();

                        // Other details
                        $pdf->SetXY($currentX + 100, $startY);
                        $pdf->Cell(20, 5, $detail['quantity'], 0, 0, 'C');
                        $pdf->Cell(20, 5, $detail['uom_name'], 0, 0, 'C');
                        $pdf->Cell(25, 5, number_format($detail['cost'], 2), 0, 0, 'C');
                        $pdf->Cell(25, 5, number_format($detail['amount'], 2), 0, 0, 'R');

                        // Move to the next line, considering the height of the description
                        $pdf->SetY($endY);
                    }
                }


                $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); // Draw a line
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(159, 10, 'Gross Amount:', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($sales_return->gross_amount, 2), 0, 0, 'R');
                $pdf->Ln(4); // Move to the next line after the last row
                $pdf->Cell(159, 10, 'Less: Discount:', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($sales_return->discount_amount, 2), 0, 0, 'R');
                $pdf->Ln(4); // Move to the next line after the last row
                $pdf->Cell(159, 10, 'Net Amount:', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($sales_return->net_amount_due, 2), 0, 0, 'R');
                $pdf->Ln(4); // Move to the next line after the last row
                $pdf->Cell(159, 10, 'VAT Sales:', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($sales_return->vatable_amount, 2), 0, 0, 'R');
                $pdf->Ln(4); // Move to the next line after the last row
                $pdf->Cell(159, 10, 'Vatable (12%):', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($sales_return->vat_amount, 2), 0, 0, 'R');
                $pdf->Ln(4); // Move to the next line after the last row
                $pdf->Cell(159, 10, 'Vat-Exempt Sales:', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($sales_return->vat_exempt_amount, 2), 0, 0, 'R');
                $pdf->Ln(4); // Move to the next line after the last row
                $pdf->Cell(159, 10, 'Zero-rated Sales:', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($sales_return->zero_rated_amount, 2), 0, 0, 'R');
                $pdf->Ln(4); // Move to the next line after the last row
                $pdf->Cell(159, 10, ' Withholding Tax:', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($sales_return->tax_withheld_amount, 2), 0, 0, 'R');
                $pdf->Ln(4); // Move to the next line after the last row

                $pdf->SetFont('Arial', 'B', 10); // 'B' indicates bold
                $pdf->Cell(159, 10, 'Total Amount Due:', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($sales_return->total_amount_due, 2), 0, 0, 'R');
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
                $pdf->MultiCell($cellWidth, $lineHeight, 'Memo: ' . $sales_return->memo, 0, 'L', false);

                // Add additional details in the table footer
                $pdf->SetFont('Arial', '', 8);


                $pdf->Ln(10);

                // Add the following code after adding content to your PDF document
                $pdf->SetXY(10, 265); // Adjust the X and Y coordinates as needed
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(0, 3, 'Acknowledgment Certificate Control Number:                        Date Issued:' . date('m/d/Y'), 0, 1, 'L');
                $pdf->Cell(0, 3, 'Series No.: 000000001 to 999999999', 0, 1, 'L');
                $pdf->Cell(0, 3, 'Date and Time Printed: ' . date('m/d/Y h:i:sA'), 0, 1, 'L');

                // Set position for the receipt confirmation
                $pdf->SetY(-60); // Move up 40 units from the bottom

                // Add receipt confirmation text
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(0, 5, 'Received the above item/s in good order and condition.', 0, 1, 'R');

                // Add signature line
                $pdf->SetY($pdf->GetY() + 5); // Move down 5 units
                $pdf->Cell(130); // Move to the right
                $pdf->Cell(60, 0, '', 'T', 1, 'R'); // Draw a line for signature

                // Add "Signature over printed name" text
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(130);
                $pdf->Cell(60, 5, 'Signature over printed name', 0, 1, 'C');

                // Reset Y position for the footer information
                $pdf->SetY(265);


                // Output the PDF
                $pdf->Output();


            } else {
                // Handle the case where the check is not found
                echo "sales_return not found.";
                exit;
            }
        } else {
            // Handle the case where the ID is not provided
            echo "No ID provided.";
            exit;
        }
    } else {
        // Handle the case where the check ID is invalid or not found
        echo "Sales sales_return not found!";
    }
} catch (Exception $ex) {
    // Handle any exceptions
    echo "Error: " . $ex->getMessage();
}