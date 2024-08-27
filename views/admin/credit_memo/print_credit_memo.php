<?php

require_once '_init.php';
// Include FPDF library
require_once 'plugins/fpdf186/fpdf.php';


try {

    if (isset($_GET['id']) || isset($_GET['credit_no'])) {
        $id = $_GET['id'];

        $credits = CreditMemo::find($id);
        if ($credits) {
            // Check found, you can now display the details
        } else {
            // Handle the case where the check is not found
            echo "Check not found.";
            exit;
        }
    } else {
        // Handle the case where the ID is not provided
        echo "No ID provided.";
        exit;
    }

    // Get the check ID from the request
    $credits = $_GET['id']; // Assuming the ID is passed as a query parameter

    // Fetch check data based on the provided ID
    $credits = CreditMemo::find($credits);

    // Check if the check exists
    if ($credits) {

        // Create a new PDF instance
        $pdf = new FPDF();
        $pdf->AddPage();

        // Add a watermark based on the credits status
          if ($credits->status == 3) {
            // If invoice status is 3, add a "VOID" watermark
            $pdf->SetFont('Arial', 'B', 190);
            $pdf->RotatedText(55, 190, 'VOID', 45, array(192, 192, 192)); // Light gray color
        } elseif ($credits->status == 4) {
            // If credits status is 4, add a "DRAFT" watermark
            $pdf->SetFont('Arial', 'B', 175);
            $pdf->RotatedText(35, 190, 'DRAFT', 45, array(192, 192, 192)); // Light gray color
        }


        // Set margins
        $pdf->SetMargins(10, 10, 10);

        // Move to top right corner for "Original Copy"
        $pdf->SetXY(-40, 10);
        $pdf->SetFont('Arial', '', 8);

        if ($credits->print_status == 1) {
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
                $pdf->Cell(0, 4, 'CREDIT MEMO', 0, 3, 'C');
        $pdf->Cell(0, 10, 'Credit No. ' . $credits->credit_no, 0, 1, 'R');
        $pdf->Ln(5);




        // Add check data to the PDF
        // Set font and size
        $pdf->SetFont('Arial', '', 9);
        $lineHeight = 6;
        $maxWidth = $pdf->GetPageWidth() - 20; // Assuming 10mm margins on each side
        $colWidth = $maxWidth / 2;
        
        // Customer details (left column)
        $pdf->SetXY(10, $pdf->GetY());
        $pdf->MultiCell($colWidth, $lineHeight, "Customer Name: " . $credits->customer_name, 0, 'L');
        $pdf->SetXY(10, $pdf->GetY());
        $pdf->MultiCell($colWidth, $lineHeight, "Address: " . $credits->billing_address, 0, 'L');
        $pdf->SetXY(10, $pdf->GetY());
        $pdf->MultiCell($colWidth, $lineHeight, "Business Style: " . $credits->business_style, 0, 'L');
        
        // Date and TIN (right column)
        $pdf->SetXY($colWidth + 10, $pdf->GetY() - 3 * $lineHeight);
        $pdf->MultiCell($colWidth, $lineHeight, "Date:       " . $credits->credit_date, 0, 'R');
        $pdf->SetXY($colWidth + 10, $pdf->GetY());
        $pdf->MultiCell($colWidth, $lineHeight, "TIN: " . $credits->customer_tin, 0, 'R');
        
        // Reset Y position to the bottom of the customer details
        $pdf->SetY($pdf->GetY() + $lineHeight);

        // Text to be displayed on the left
        // $textLeft = 'Terms: ' . $credits->terms;
        // $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textLeft, 0, 1, 'L');

        $pdf->Ln(2);

        // Table headers

        // Table headers
        $pdf->Ln(); // Move to the next line
        $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); // Draw a line

        // Adjusting the column widths
        $pdf->Cell(40, 10, 'Account Name', 0, 0, 'L');
        $pdf->Cell(60, 10, 'Description', 0, 0, 'L');
        $pdf->Cell(25, 10, 'Tax', 0, 0, 'R');
        $pdf->Cell(25, 10, 'Amount', 0, 0, 'R');
        $pdf->Ln(); // Move to the next line
        $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); // Draw a line

        $totalCost = 0;
        $totalAmount = 0;

        if ($credits) {
            foreach ($credits->details as $detail) {
                $totalAmount += $detail['amount'];

                $startY = $pdf->GetY();
                $currentX = $pdf->GetX();

                // Item Name
                $pdf->Cell(40, 5, $detail['gl_name'], 0, 0, 'L');

                // Description (using MultiCell)
                $pdf->SetXY($currentX + 40, $startY);
                $pdf->MultiCell(60, 5, $detail['account_description'], 0, 'L');

                $endY = $pdf->GetY();

                // Other details
                $pdf->SetXY($currentX + 100, $startY);
                $pdf->Cell(25, 5, number_format($detail['sales_tax'], 2), 0, 0, 'R');
                $pdf->Cell(25, 5, number_format($detail['amount'], 2), 0, 0, 'R');

                // Move to the next line, considering the height of the description
                $pdf->SetY($endY);
            }
        }


                $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); // Draw a line
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(159, 10, 'Gross Amount:', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($credits->gross_amount, 2), 0, 0, 'R');
                $pdf->Ln(4); // Move to the next line after the last row
                $pdf->Cell(159, 10, 'Net Amount:', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($credits->net_amount_due, 2), 0, 0, 'R');
                $pdf->Ln(4); // Move to the next line after the last row
                $pdf->Cell(159, 10, 'VAT Sales:', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($credits->vat_percentage_amount, 2), 0, 0, 'R');
                $pdf->Ln(4); // Move to the next line after the last row
                $pdf->Cell(159, 10, 'Vatable (12%):', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($credits->net_of_vat, 2), 0, 0, 'R');
                $pdf->Ln(4); // Move to the next line after the last row
                $pdf->Cell(159, 10, ' Withholding Tax:', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($credits->tax_withheld_amount, 2), 0, 0, 'R');
                $pdf->Ln(4); // Move to the next line after the last row

                $pdf->SetFont('Arial', 'B', 10); // 'B' indicates bold
                $pdf->Cell(159, 10, 'Total Amount Due:', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($credits->total_amount_due, 2), 0, 0, 'R');
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
        $pdf->MultiCell($cellWidth, $lineHeight, 'Memo: ' . $credits->memo, 0, 'L', false);

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
        // Handle the case where the check ID is invalid or not found
        echo "Check not found!";
    }
} catch (Exception $ex) {
    // Handle any exceptions
    echo "Error: " . $ex->getMessage();
}