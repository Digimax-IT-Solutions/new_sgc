<?php

require_once '_init.php';
// Include FPDF library
require_once 'plugins/fpdf186/fpdf.php';

function numberToWords($number) {
    $ones = array(
        1 => "One", 2 => "Two", 3 => "Three", 4 => "Four", 5 => "Five", 
        6 => "Six", 7 => "Seven", 8 => "Eight", 9 => "Nine", 10 => "Ten", 
        11 => "Eleven", 12 => "Twelve", 13 => "Thirteen", 14 => "Fourteen", 
        15 => "Fifteen", 16 => "Sixteen", 17 => "Seventeen", 18 => "Eighteen", 
        19 => "Nineteen"
    );
    $tens = array(
        2 => "Twenty", 3 => "Thirty", 4 => "Forty", 5 => "Fifty", 
        6 => "Sixty", 7 => "Seventy", 8 => "Eighty", 9 => "Ninety"
    );
    $scales = array(
        "", "Thousand", "Million", "Billion", "Trillion"
    );

    if ($number == 0) {
        return "Zero";
    }

    $number = (int)$number;
    $string = "";
    $scaleCount = 0;

    while ($number > 0) {
        if ($number % 1000 != 0) {
            $scaleKey = $scaleCount;
            $groupWords = "";
            $groupNumber = $number % 1000;

            if ($groupNumber >= 100) {
                $groupWords .= $ones[floor($groupNumber / 100)] . " Hundred ";
                $groupNumber %= 100;
            }

            if ($groupNumber >= 20) {
                $groupWords .= $tens[floor($groupNumber / 10)] . " ";
                $groupNumber %= 10;
            }

            if ($groupNumber > 0) {
                $groupWords .= $ones[$groupNumber] . " ";
            }

            $string = trim($groupWords) . " " . $scales[$scaleKey] . " " . $string;
        }
        $number = floor($number / 1000);
        $scaleCount++;
    }

    return trim($string);
}

try {

    if (isset($_GET['id']) || isset($_GET['cv_no'])) {
        $id = $_GET['id'];

        $check = WriteCheck::find($id);
        if ($check) {
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
    $checkId = $_GET['id']; // Assuming the ID is passed as a query parameter

    // Fetch check data based on the provided ID
    $check = WriteCheck::find($checkId);

    // Check if the check exists
    if ($check) {

        // Create a new PDF instance
        $pdf = new FPDF();
        $pdf->AddPage();

        // Add a watermark based on the check status
        if ($check->status == 3) {
            // If check status is 3, add a "VOID" watermark
            $pdf->SetFont('Arial', 'B', 190);
            $pdf->RotatedText(55, 190, 'VOID', 45, array(192, 192, 192)); // Light gray color
        } elseif ($check->status == 4) {
            // If check status is 4, add a "DRAFT" watermark
            $pdf->SetFont('Arial', 'B', 175);
            $pdf->RotatedText(55, 190, 'DRAFT', 45, array(192, 192, 192)); // Light gray color
        }


        // Set margins
        $pdf->SetMargins(10, 10, 10);

        // Move to top right corner for "Original Copy"
        $pdf->SetXY(-40, 10);
        $pdf->SetFont('Arial', '', 8);
        // $pdf->Cell(30, 5, 'Original Copy', 0, 0, 'R');

        if ($check->print_status == 1) {
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
        $pdf->Cell(0, 4, 'CHECK VOUCHER', 0, 3, 'C');


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
        // Retrieve all employees from the database
        $employees = Employee::all();

        // Check if $employees is null or empty
        if ($employees === null || empty($employees)) {
            // Handle the case where no employees were found
            error_log("No employees found in the database.");
            $employees = []; // Initialize as an empty array to avoid the foreach() error
        }

        // Determine if the payee is an employee
        if ($check->payee_type == 'employee') {
            // Retrieve employee details
            $employee = null;
            foreach ($employees as $emp) {
                if ($emp->id == $check->payee_id) {
                    $employee = $emp;
                    break;
                }
            }

            if ($employee) {
                // Construct employee details
                $payeeName = $employee->first_name . ' ' . $employee->middle_name . ' ' . $employee->last_name;

                // Text to be displayed on the left
                $textLeft = "Payee: $payeeName";
            } else {
                $textLeft = 'Payee: Employee not found';
                error_log("Employee with ID {$check->payee_id} not found.");
            }
        } else {
            // Text to be displayed for non-employee payees
            $textLeft = 'Payee: ' . $check->payee_name;
        }

        // Add the text to the PDF
        $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textLeft, 0, 0, 'L');


        // Text to be displayed on the right
        $textRight = 'Ref No: ' . $check->cv_no;
        $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textRight, 0, 1, 'L');

        // Text to be displayed on the left
        $textLeft = '' . "";
        $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textLeft, 0, 0, 'L');

        // Text to be displayed on the right
        $textRight = 'Check Date: ' . $check->check_date;
        $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textRight, 0, 1, 'L');

        // Text to be displayed on the left
        $textLeft = '';
        $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textLeft, 0, 0, 'L');

        // Text to be displayed on the left
        $textRight = 'Check No: ' . $check->check_no;
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textRight, 0, 1, 'L');

        $pdf->Ln(2);

        // Set column widths
        $colWidth1 = 15; // GL column
        $colWidth2 = 15; // SL column
        $colWidth3 = 106; // Account Title column
        $colWidth4 = 27; // Debit column
        $colWidth5 = 27; // Credit column

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
        $pdf->SetFont('Arial', '', 9);

        // Initialize total debit and credit
        $totalDebit = 0;
        $totalCredit = 0;

        // Fetch transaction entries for the check
        $transactionEntries = WriteCheck::findTransactionEntries($checkId);
        foreach ($transactionEntries as $entry) {
            // Skip the row if both debit and credit are zero
            if ($entry['debit'] == 0 && $entry['credit'] == 0) {
                continue;
            }

            // Increment total debit and credit
            $totalDebit += $entry['debit'];
            $totalCredit += $entry['credit'];

            // Split account code into GL and SL
            $glCode = substr($entry['account_code'], 0, 5);
            $slCode = substr($entry['account_code'], 5);

            $startY = $pdf->GetY();
            $pdf->Cell($colWidth1, 7, $glCode, 'LR', 0, 'C');
            $pdf->Cell($colWidth2, 7, $slCode, 'LR', 0, 'C');

            // Use MultiCell for Account Title to allow text wrapping
            $pdf->MultiCell($colWidth3, 7, $entry['account_description'], 'LR', 'L');

            $endY = $pdf->GetY();
            $pdf->SetXY($pdf->GetX() + $colWidth1 + $colWidth2 + $colWidth3, $startY);

            $pdf->Cell($colWidth4, $endY - $startY, ($entry['debit'] != 0 ? number_format($entry['debit'], 2) : ''), 'LR', 0, 'R');
            $pdf->Cell($colWidth5, $endY - $startY, ($entry['credit'] != 0 ? number_format($entry['credit'], 2) : ''), 'LR', 1, 'R');

            $pdf->SetY($endY);
        }

        $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); // Draw a line
        // Total row
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell($colWidth1 + $colWidth2 + $colWidth3, 7, 'Total', 0, 0, 'R');
        $pdf->Cell($colWidth4, 7, number_format($totalDebit, 2), 0, 0, 'R');
        $pdf->Cell($colWidth5, 7, number_format($totalCredit, 2), 0, 1, 'R');

       // Format the amount without a thousands separator
        $amountInFigures = number_format($totalDebit, 2, '.', '');

        // Split the amount into whole and decimal parts
        list($whole, $decimal) = explode('.', $amountInFigures);

        // Convert the whole part to words
        $amountInWords = strtoupper(numberToWords($whole));

        // Add "&" and the decimal part as digits
        $amountInWords .= " & " . $decimal . "/100";

        // // Add "&" and the decimal part in words if needed
        // if ((int)$decimal > 0) {
        //     $amountInWords .= ' & ' . strtoupper(numberToWords($decimal)) . '/100.';
        // } else {
        //     $amountInWords .= ' & 00/100.';
        // }

        // Set font for the regular text
        $pdf->SetFont('Arial', '', 8);

        // Output the first line, left-aligned
        $pdf->Cell(0, 2, 'Received the check in full/partial payment of account with HIDECO SUGAR MILLING CO., INC. the amount of', 0, 1, 'L');

        // Make the amount in words bold and a little bigger, and center it
        $pdf->SetFont('Arial', 'B', 10);
        // $pdf->Cell(0, 4, ' ' . $amountInWords, 0, 1, 'L');
        $pdf->Cell(0, 4, ' ' . $amountInWords, 0, 1, 'L');



        // Reset the font to its previous style
        $pdf->SetFont('Arial', '', 8);

        // Received by section
        $pdf->Ln(5);
        $pdf->SetY($pdf->GetY() + 5); // Move down 5 units
        $pdf->Cell(130); // Move to the right
        $pdf->Cell(65, 0, '', 'T', 1, 'R'); // Draw a line for signature
        $pdf->Cell(197, 6, 'Authorized Signature/ Name in Print / Date Received', 0, 1, 'R');


        // Add signature lines
        $pdf->Ln(5); // Move to the next line after the last row

        $pdf->SetFont('Arial', '', 8);
        $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); // Draw a line
        // First row with labels
        $pdf->Cell(50, 10, 'PREPARED/POSTED BY:', 0, 0, 'L');
        $pdf->Cell(50, 10, 'CHECKED/CERTIFIED BY:', 0, 0, 'L');
        $pdf->Cell(50, 10, 'VERIFIED FOR PAYMENT BY:', 0, 0, 'L');
        $pdf->Cell(40, 10, 'PAYMENT APPROVED BY:', 0, 1, 'L');

        // Second row with names/values
        $pdf->Cell(65, 5, 'ACC/JCM', 0, 0, 'L'); 
        $pdf->Cell(50, 5, 'AVP', 0, 0, 'L'); 
        $pdf->Cell(50, 5, 'ASP', 0, 0, 'L'); 
        $pdf->Cell(40, 5, 'EQC', 0, 0, 'L'); 
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
        $pdf->MultiCell($cellWidth, $lineHeight, 'Memo: ' . $check->memo, 0, 'L', false);

        // Add additional details in the table footer
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