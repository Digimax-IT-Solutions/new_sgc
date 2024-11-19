<?php

require_once '_init.php';
// Include FPDF library
require_once 'plugins/fpdf186/fpdf.php';


try {

    if (isset($_GET['id']) || isset($_GET['ref_no'])) {
        $id = $_GET['id'];

        $journal = GeneralJournal::find($id);
        if ($journal) {
            // journal found, you can now display the details
        } else {
            // Handle the case where the journal is not found
            echo "Journal not found.";
            exit;
        }
    } else {
        // Handle the case where the ID is not provided
        echo "No ID provided.";
        exit;
    }

    // Get the journal ID from the request
    $journalId = $_GET['id']; // Assuming the ID is passed as a query parameter

    // Fetch journal data based on the provided ID
    $journal = GeneralJournal::find($journalId);

    // journal if the journal exists
    if ($journal) {


        // Create a new PDF instance
        $pdf = new FPDF();
        $pdf->AddPage();

        // Add a watermark based on the journal status
        if ($journal->status == 3) {
            // If journal status is 3, add a "VOID" watermark
            $pdf->SetFont('Arial', 'B', 190);
            $pdf->RotatedText(55, 190, 'VOID', 45, array(192, 192, 192)); // Light gray color
        } elseif ($journal->status == 4) {
            // If journal status is 4, add a "DRAFT" watermark
            $pdf->SetFont('Arial', 'B', 175);
            $pdf->RotatedText(55, 190, 'DRAFT', 45, array(192, 192, 192)); // Light gray color
        }

        // Set margins
        $pdf->SetMargins(10, 10, 10);

        // Move to top right corner for "Original Copy"
        $pdf->SetXY(-40, 10);
        $pdf->SetFont('Arial', '', 8);

        if ($journal->print_status == 1) {
            $statusText = 'Original Copy';
        } else {
            $statusText = 'Reprinted Copy';
        }

        $pdf->Cell(30, 5, $statusText, 0, 0, 'R');

        // Reset position for banner
        $pdf->SetXY(10, 10);

        // Calculate banner position
        $pageWidth = $pdf->GetPageWidth();
        $bannerWidth = 40; // Adjust as needed
        $bannerX = ($pageWidth - $bannerWidth) / 2;

        // Add banner image
        $pdf->Image('photos/banner.png', 10, 15, $bannerWidth, 0, 'PNG');

        // Move below banner
        $pdf->SetY($pdf->GetY() + 8);

        // Company details centered under banner
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 4, 'Pilar Development Compound, Warehouse 3 (Unit 2) Rose Ave, Pilar Village,', 0, 1, 'R');
        $pdf->Cell(0, 4, 'Barangay Almanza, Las Pinas, Philippines', 0, 1, 'R');
        $pdf->Cell(
            0,
            4,
            'VAT Reg. TIN: ',
            0,
            1,
            'R'
        );
        $pdf->Cell(
            0,
            4,
            'Tel. No: ',
            0,
            1,
            'R'
        );

        $pdf->SetY($pdf->GetY() + 15);
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 4, 'GENERAL JOURNAL', 0, 3, 'C');
        $pdf->Ln(5);

        // Add journal data to the PDF
        // Set font and size
        $pdf->SetFont('Arial', '', 9);

        // Set maximum width of the cell
        $maxWidth = 80; // Adjust according to your column width requirement

        // Set line height
        $lineHeight = 5; // Adjust according to your line spacing requirement

        // Set padding for the cells (space between left-aligned and right-aligned text)
        $padding = -100; // Adjust as needed



        // Text to be displayed on the left
        $textLeft = 'Entry No: ' . $journal->entry_no;
        $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textLeft, 0, 0, 'L');

        // Text to be displayed on the right
        $textRight = 'Journal Date: ' . $journal->journal_date;
        $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textRight, 0, 1, 'L');

        // // Text to be displayed on the left
        // $textLeft = 'Address: ' . $journal->payee_address;
        // $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textLeft, 0, 0, 'L');

        // // Text to be displayed on the right
        // $textRight = 'journal Date: ' . $journal->journal_date;
        // $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textRight, 0, 1, 'L');

        // // Text to be displayed on the left
        // $textLeft = '';
        // $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textLeft, 0, 0, 'L');

        // // Text to be displayed on the left
        // $textRight = 'journal No: ' . $journal->journal_no;
        // $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textRight, 0, 1, 'L');

        $pdf->Ln(2);

        // Set column widths
        $colWidth1 = 30; // Account Code column (adjusted width)
        $colWidth2 = 106; // Account Title column
        $colWidth3 = 27; // Debit column
        $colWidth4 = 27; // Credit column

        // Table headers
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(
            $colWidth1,
            10,
            'Account Code',
            'LTR',
            0,
            'C'
        );
        $pdf->Cell(
            $colWidth2,
            10,
            'Account Title',
            'LTR',
            0,
            'C'
        );
        $pdf->Cell($colWidth3, 10, 'Debit', 'LTR', 0, 'C');
        $pdf->Cell($colWidth4, 10, 'Credit', 'LTR', 0, 'C');
        $pdf->Cell($colWidth4, 5, '', 0, 1, 'C');

        // Reset font for data rows
        $pdf->SetFont('Arial', '', 10);

        // Initialize total debit and credit
        $totalDebit = 0;
        $totalCredit = 0;


        $pdf->Ln(4);
        // Fetch transaction entries for the check
        // Fetch transaction entries for the journal
        $transactionEntries = GeneralJournal::getJournalDetails($journalId);
        foreach ($transactionEntries as $entry) {
            // Skip the row if both debit and credit are zero
            if (
                $entry['debit'] == 0 && $entry['credit'] == 0
            ) {
                continue;
            }

            // Increment total debit and credit
            $totalDebit += $entry['debit'];
            $totalCredit += $entry['credit'];

            // Use the full account code
            $accountCode = $entry['account_code'];

            $startY = $pdf->GetY();
            $pdf->Cell(
                $colWidth1,
                7,
                $accountCode,
                'LR',
                0,
                'C'
            );



            // Use MultiCell for Account Title to allow text wrapping
            $pdf->MultiCell($colWidth2, 7, $entry['account_description'], 'LR', 'L');

            $endY = $pdf->GetY();
            $pdf->SetXY($pdf->GetX() + $colWidth1 + $colWidth2, $startY);

            $pdf->Cell($colWidth3, $endY - $startY, ($entry['debit'] != 0 ? number_format($entry['debit'], 2) : ''), 'LR', 0, 'R');
            $pdf->Cell($colWidth4, $endY - $startY, ($entry['credit'] != 0 ? number_format($entry['credit'], 2) : ''), 'LR', 1, 'R');

            $pdf->SetY($endY);
        }

        // Draw a line
        $pdf->Line(
            $pdf->GetX(),
            $pdf->GetY(),
            $pdf->GetX() + 190,
            $pdf->GetY()
        );

        // Total row
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(
            $colWidth1 + $colWidth2,
            7,
            'Total',
            0,
            0,
            'R'
        );
        $pdf->Cell($colWidth3, 7, number_format($totalDebit, 2), 0, 0, 'R');
        $pdf->Cell($colWidth4, 7, number_format($totalCredit, 2), 0, 1, 'R');


        $pdf->Ln(10);

        $pdf->SetFont('Arial', '', 8);
        $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); // Draw a line
        // First row with labels
        $pdf->Cell(50, 10, 'PREPARED BY:', 0, 0, 'L');
        $pdf->Cell(50, 10, 'VERIFIED BY:', 0, 0, 'L');
        $pdf->Cell(50, 10, 'CERTIFIED BY:', 0, 0, 'L');
        $pdf->Cell(40, 10, 'APPROVED BY:', 0, 1, 'L');

        // Second row with names/values
        $pdf->Cell(55, 5, '', 0, 0, 'L');
        $pdf->Cell(50, 5, '', 0, 0, 'L');
        $pdf->Cell(50, 5, '', 0, 0, 'L');
        $pdf->Cell(40, 5, '', 0, 0, 'L');
        $pdf->Ln(2); // Move to the next line
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
        $pdf->MultiCell($cellWidth, $lineHeight, 'Memo: ' . $journal->memo, 0, 'L', false);

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
        // Handle the case where the journal ID is invalid or not found
        echo "journal not found!";
    }
} catch (Exception $ex) {
    // Handle any exceptions
    echo "Error: " . $ex->getMessage();
}
