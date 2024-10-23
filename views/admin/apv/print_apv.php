<?php

require_once '_init.php';
// Include FPDF library
require_once 'plugins/fpdf186/fpdf.php';


try {

    if (isset($_GET['id']) || isset($_GET['ref_no'])) {
        $id = $_GET['id'];

        $apv = Apv::find($id);
        if ($apv) {
            // Check found, you can now display the details
        } else {
            // Handle the case where the check is not found
            echo "apv not found.";
            exit;
        }
    } else {
        // Handle the case where the ID is not provided
        echo "No ID provided.";
        exit;
    }

    // Get the check ID from the request
    $apvId = $_GET['id']; // Assuming the ID is passed as a query parameter

    // Fetch check data based on the provided ID
    $apv = Apv::find($apvId);

    // Check if the check exists
    if ($apv) {

        // Create a new PDF instance
        $pdf = new FPDF();
        $pdf->AddPage();

        // Add a watermark based on the apv status
        if ($apv->status == 3) {
            // If apv status is 3, add a "VOID" watermark
            $pdf->SetFont('Arial', 'B', 190);
            $pdf->RotatedText(55, 190, 'VOID', 45, array(192, 192, 192)); // Light gray color
        } elseif ($apv->status == 4) {
            // If apv status is 4, add a "DRAFT" watermark
            $pdf->SetFont('Arial', 'B', 175);
            $pdf->RotatedText(55, 190, 'DRAFT', 45, array(192, 192, 192)); // Light gray color
        }


        // Set margins
        $pdf->SetMargins(10, 10, 10);

        $pdf->SetXY(-40, 10);
        $pdf->SetFont('Arial', '', 8);

        if ($apv->print_status == 1) {
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
        $pdf->Cell(0, 4, 'ACCOUNT PAYABLE VOUCHER', 0, 3, 'C');


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
        $textLeft = 'Vendor Name: ' . $apv->vendor_name;
        $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textLeft, 0, 0, 'L');

        // Text to be displayed on the right
        $textRight = 'Ref No: ' . $apv->ref_no;
        $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textRight, 0, 1, 'L');

        // Text to be displayed on the left
        // $textLeft = 'Address: ' . $apv->payee_address;
        // $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textLeft, 0, 0, 'L');

        // Text to be displayed on the right
        $textRight = 'APV Date: ' . $apv->apv_date;
        $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textRight, 0, 1, 'L');

        // Text to be displayed on the left
        $textLeft = '';
        $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textLeft, 0, 0, 'L');

        // Text to be displayed on the left
        $textRight = 'APV No: ' . $apv->apv_no;
        $pdf->SetFont('Arial', 'B', 12); // Set font to Arial, bold, size 12 (adjust size as needed)
        $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textRight, 0, 1, 'L');

        $pdf->Ln(2);

        // Set column widths
        $colWidth1 = 20; // GL column
        $colWidth2 = 20; // SL column
        $colWidth3 = 90; // Account Title column
        $colWidth4 = 30; // Debit column
        $colWidth5 = 30; // Credit column

        // Table headers
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell($colWidth1 + $colWidth2, 5, 'Account Code', 'LTR', 0, 'C');
        $pdf->Cell($colWidth3, 10, 'Account Title', 'LTR', 0, 'C');
        $pdf->Cell($colWidth4, 10, 'Debit', 'LTR', 0, 'C');
        $pdf->Cell($colWidth5, 10, 'Credit', 'LTR', 0, 'C');
        $pdf->Cell($colWidth5, 5, '', 0, 1, 'C');

        // Subheaders for Account Code
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell($colWidth1, 5, 'GL', 1, 0, 'C');
        $pdf->Cell($colWidth2, 5, 'SL', 1, 0, 'C');
        $pdf->Cell($colWidth3, 5, '', 'LRB', 0);
        $pdf->Cell($colWidth4, 5, '', 'LRB', 0);
        $pdf->Cell($colWidth5, 5, '', 'LRB', 1);


        // Reset font
        $pdf->SetFont(
            'Arial',
            '',
            9
        );

        // Initialize total debit and credit
        $totalDebit = 0;
        $totalCredit = 0;

        // Fetch transaction entries for the check
        $transactionEntries = Apv::findTransactionEntries($apvId);
        foreach ($transactionEntries as $entry) {
            // Skip the row if both debit and credit are zero
            if (
                $entry['debit'] == 0 && $entry['credit'] == 0
            ) {
                continue;
            }

            // Split account code into GL and SL
            $glCode = substr($entry['account_code'], 0, 5);
            $slCode = substr($entry['account_code'], 5);

            $startY = $pdf->GetY();

            // Draw GL and SL cells
            $pdf->Cell($colWidth1, 7, $glCode, 'L', 0, 'C');
            $pdf->Cell($colWidth2, 7, $slCode, 'R', 0, 'C');

            // Use MultiCell for Account Title
            $pdf->MultiCell($colWidth3, 7, $entry['account_description'], 'LR', 'L');

            $endY = $pdf->GetY();

            // Draw the border for GL and SL columns to match the height of the MultiCell
            $pdf->Line($pdf->GetX(), $startY, $pdf->GetX(), $endY);
            $pdf->Line($pdf->GetX() + $colWidth1, $startY, $pdf->GetX() + $colWidth1, $endY);

            $pdf->SetXY($pdf->GetX() + $colWidth1 + $colWidth2 + $colWidth3, $startY);

            // Draw Debit and Credit cells
            $pdf->Cell($colWidth4, $endY - $startY, ($entry['debit'] != 0 ? number_format($entry['debit'], 2) : ''), 'LR', 0, 'R');
            $pdf->Cell($colWidth5, $endY - $startY, ($entry['credit'] != 0 ? number_format($entry['credit'], 2) : ''), 'LR', 1, 'R');

            // Accumulate the total debit and credit
            $totalDebit += $entry['debit'];
            $totalCredit += $entry['credit'];

            $pdf->SetY($endY);
        }

        $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); // Draw a line

        // Total row
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(
            $colWidth1 + $colWidth2 + $colWidth3,
            7,
            'Total',
            0,
            0,
            'R'
        );
        $pdf->Cell($colWidth4, 7, number_format($totalDebit, 2), 0, 0, 'R');
        $pdf->Cell($colWidth5, 7, number_format($totalCredit, 2), 0, 1, 'R');
        // Add signature lines
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
        $lineHeight = 25; // Adjust as needed
        // Wrap text and add memo to PDF cell
        $pdf->MultiCell($cellWidth, $lineHeight, 'Memo: ' . $apv->memo, 0, 'L', false);

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
        echo "Apv not found!";
    }
} catch (Exception $ex) {
    // Handle any exceptions
    echo "Error: " . $ex->getMessage();
}
