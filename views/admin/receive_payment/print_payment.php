<?php

require_once '_init.php';
// Include FPDF library
require_once 'plugins/fpdf186/fpdf.php';

function numberToWords($number)
{
    $ones = array(
        1 => "One",
        2 => "Two",
        3 => "Three",
        4 => "Four",
        5 => "Five",
        6 => "Six",
        7 => "Seven",
        8 => "Eight",
        9 => "Nine",
        10 => "Ten",
        11 => "Eleven",
        12 => "Twelve",
        13 => "Thirteen",
        14 => "Fourteen",
        15 => "Fifteen",
        16 => "Sixteen",
        17 => "Seventeen",
        18 => "Eighteen",
        19 => "Nineteen"
    );
    $tens = array(
        2 => "Twenty",
        3 => "Thirty",
        4 => "Forty",
        5 => "Fifty",
        6 => "Sixty",
        7 => "Seventy",
        8 => "Eighty",
        9 => "Ninety"
    );
    $scales = array(
        "",
        "Thousand",
        "Million",
        "Billion",
        "Trillion"
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

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $payments = Payment::find($id);
        if ($payments) {

            // Get the check ID from the request
            $payment_id = $_GET['id']; // Assuming the ID is passed as a query parameter

            // Fetch invoice data based on the provided ID
            $payment = Payment::find($payment_id);

            // Invoice if the invoice exists
            if ($payment) {

                // Create a new PDF instance
                $pdf = new FPDF();
                $pdf->AddPage();

                // Add a watermark based on the invoice status
                if ($payment->status == 3) {
                    // If invoice status is 3, add a "VOID" watermark
                    $pdf->SetFont('Arial', 'B', 190);
                    $pdf->RotatedText(55, 190, 'VOID', 45, array(192, 192, 192)); // Light gray color
                } elseif ($payment->status == 4) {
                    // If invoice status is 4, add a "DRAFT" watermark
                    $pdf->SetFont('Arial', 'B', 175);
                    $pdf->RotatedText(55, 190, 'DRAFT', 45, array(192, 192, 192)); // Light gray color
                }


                // Set margins
                $pdf->SetMargins(10, 10, 10);

                // Move to top right corner for "Original Copy"
                $pdf->SetXY(-40, 10);
                $pdf->SetFont('Arial', '', 8);

                if ($payment->print_status == 1) {
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
                $pdf->Cell(
                    0,
                    4,
                    'Pilar Development Compound, Warehouse 3 (Unit 2) Rose Ave, Pilar Village,',
                    0,
                    1,
                    'R'
                );
                $pdf->Cell(0, 4, 'Barangay Almanza, Las Pinas, Philippines', 0, 1, 'R');
                $pdf->Cell(0, 4, 'VAT Reg. TIN: ', 0, 1, 'R');
                $pdf->Cell(0, 4, 'Tel. No: ', 0, 1, 'R');


                $pdf->SetY($pdf->GetY() + 15);
                $pdf->SetFont('Arial', 'B', 14);
                $pdf->Cell(0, 4, 'COLLECTION RECEIPT', 0, 3, 'C');
                $pdf->Cell(0, 10, 'CR No. ' . $payment->cr_no, 0, 1, 'R');
                $pdf->Ln(5);

                // Set font and size for the details section
                $pdf->SetFont('Arial', '', 9);

                $lineHeight = 5; // Adjust according to your line spacing requirement
                $rightColumnX = $pageWidth - 60; // X position for the right column

                // Date and Terms
                // Invoice details
                $date = new DateTime($payment->payment_date);
                $formattedDate = $date->format('m/d/y');
                $pdf->Cell(100, $lineHeight, 'Date: ' . $formattedDate, 0, 0, 'L');
                $pdf->SetX($rightColumnX);
                $pdf->Cell(0, $lineHeight, 'TIN: ' . $payment->customer_tin, 0, 1, 'L');

                // Customer and Rep
                $pdf->Cell(100, $lineHeight, 'Received From: ' . $payment->customer_name, 0, 0, 'L');
                $pdf->SetX($rightColumnX);
                $pdf->Cell(0, $lineHeight, 'Check/Ref No.: ' . $payment->ref_no, 0, 1, 'L');

                // Address
                $pdf->Cell(100, $lineHeight, 'Address: ' . $payments->billing_address, 0, 0, 'L');
                $pdf->SetX($rightColumnX);
                $pdf->Cell(0, $lineHeight, '', 0, 1, 'L');

                // Assuming $payment->summary_applied_amount is a number
                // $amountInWords = numberToWords($payment->summary_applied_amount);

                // Adjust the PDF Cell method
                // $pdf->Cell(100, $lineHeight, 'Amount in Words: ' . $amountInWords, 0, 0, 'L');
                $pdf->SetX($rightColumnX);
                $pdf->Cell(0, $lineHeight, '', 0, 1, 'L');


                // Format the amount without a thousands separator
                $amountInFigures = number_format($payment->summary_applied_amount, 2, '.', '');

                // Split the amount into whole and decimal parts
                list($whole, $decimal) = explode('.', $amountInFigures);

                // Convert the whole part to words
                $amountInWords = numberToWords($whole) . ' Pesos';

                // If there's a decimal part, convert it to words and append
                if ($decimal > 0) {
                    $amountInWords .= ' and ' . numberToWords($decimal) . ' Cents';
                }

                // Output the amount in words and figures
                $pdf->Cell(100, $lineHeight, 'Amount in Words: ' . $amountInWords, 0, 1, 'L');
                $pdf->Cell(100, $lineHeight, 'Amount in Figures: ' . $amountInFigures, 0, 1, 'L');


                $pdf->SetX($rightColumnX);


                $pdf->Ln(2);

                $pdf->Ln();
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell(0, 10, 'PAYMENT DETAILS', 0, 1, 'C');
                $pdf->SetFont('Arial', '', 9);

                // Define the column widths
                $columnWidths = array(
                    'Date' => 19,
                    'SI No.' => 19,
                    'Orig Amt' => 23,
                    'Discount/Credit' => 21,
                    'Net Amt' => 23,
                    'WTax (2307)' => 21,
                    'Amount Due' => 23,
                    'Payment Applied' => 23,
                    'Balance' => 21
                );

                // Set column headers
                foreach ($columnWidths as $header => $width) {
                    $pdf->Cell($width, 6, $header, 'T', 0, 'C'); // Headers remain center-aligned
                }
                $pdf->Ln();

                // Initialize totals
                $totalOrigAmt = $totalDiscount = $totalNetAmt = $totalWTax = $totalAmountDue = $totalPaymentApplied = $totalBalance = 0;

                // Table data with only horizontal borders
                foreach ($payment->details as $detail) {
                    $discount_credit = $detail['discount_amount'] + $detail['credit_amount'];
                    $netAmount = $detail['total_amount_due'] - $discount_credit;
                    $amountDue = $netAmount;

                    $pdf->Cell($columnWidths['Date'], 6, date('m/d/Y', strtotime($detail['invoice_date'])), 'T', 0, 'C');
                    $pdf->Cell($columnWidths['SI No.'], 6, $detail['invoice_number'], 'T', 0, 'C');
                    $pdf->Cell($columnWidths['Orig Amt'], 6, number_format($detail['total_amount_due'], 2), 'T', 0, 'R');
                    $pdf->Cell($columnWidths['Discount/Credit'], 6, number_format($discount_credit, 2), 'T', 0, 'R');
                    $pdf->Cell($columnWidths['Net Amt'], 6, number_format($netAmount, 2), 'T', 0, 'R');
                    $pdf->Cell($columnWidths['WTax (2307)'], 6, '', 'T', 0, 'R');
                    $pdf->Cell($columnWidths['Amount Due'], 6, number_format($amountDue, 2), 'T', 0, 'R');
                    $pdf->Cell($columnWidths['Payment Applied'], 6, number_format($detail['amount_applied'], 2), 'T', 0, 'R');
                    $pdf->Cell($columnWidths['Balance'], 6, number_format($detail['balance_due'], 2), 'T', 1, 'R');

                    // Update totals
                    $totalOrigAmt += $detail['total_amount_due'];
                    $totalDiscount += $discount_credit;
                    $totalNetAmt += $netAmount;
                    $totalAmountDue += $amountDue;
                    $totalPaymentApplied += $detail['amount_applied'];
                    $totalBalance += $detail['balance_due'];
                }

                // Add total row with only horizontal borders
                $pdf->SetFont('Arial', 'B', 9);
                $pdf->Cell($columnWidths['Date'] + $columnWidths['SI No.'], 6, 'TOTAL', 'T', 0, 'C');
                $pdf->Cell($columnWidths['Orig Amt'], 6, number_format($totalOrigAmt, 2), 'T', 0, 'R');
                $pdf->Cell($columnWidths['Discount/Credit'], 6, number_format($totalDiscount, 2), 'T', 0, 'R');
                $pdf->Cell($columnWidths['Net Amt'], 6, number_format($totalNetAmt, 2), 'T', 0, 'R');
                $pdf->Cell($columnWidths['WTax (2307)'], 6, '', 'T', 0, 'R');
                $pdf->Cell($columnWidths['Amount Due'], 6, number_format($totalAmountDue, 2), 'T', 0, 'R');
                $pdf->Cell($columnWidths['Payment Applied'], 6, number_format($totalPaymentApplied, 2), 'T', 0, 'R');
                $pdf->Cell($columnWidths['Balance'], 6, number_format($totalBalance, 2), 'T', 1, 'R');

                $pdf->Ln(2);

                // Set font and size for table content
                // Set font and size for table content
                $pdf->SetFont('Arial', '', 9);
                // Define the width of the cell
                $cellWidth = 150; // Use 0 for auto width
                // Define the height of each line
                $lineHeight = 5; // Adjust as needed
                // Wrap text and add memo to PDF cell

                $pdf->Ln(5);
                // Received by section
                $pdf->Ln(10);
                $pdf->Cell(140, 6, 'Received by:', 0, 1, 'R');
                $pdf->SetY($pdf->GetY() + 5); // Move down 5 units
                $pdf->Cell(130); // Move to the right
                $pdf->Cell(60, 0, '', 'T', 1, 'R'); // Draw a line for signature
                $pdf->Cell(180, 6, 'Signature Over Printed Name', 0, 1, 'R');

                // Acknowledgment Certificate details
                $pdf->Ln(10);
                $pdf->SetFont('Arial', '', 8);
                $pdf->Cell(0, 3, 'Acknowledgment Certificate Control Number:                        Date Issued:' . date('m/d/Y'), 0, 1, 'L');
                $pdf->Cell(0, 5, 'Series No.:000000001-999999999', 0, 1, 'L');
                $pdf->Cell(0, 3, 'Date and Time Printed: ' . date('m/d/Y h:i:sA'), 0, 1, 'L');


                // Not valid for claim of input tax
                $pdf->Ln(5);
                $pdf->SetFont('Arial', 'B', 10);
                $pdf->Cell(0, 6, '"THIS DOCUMENT IS NOT VALID FOR CLAIM OF INPUT TAX"', 0, 1, 'C');

                // Reset Y position for the footer information
                $pdf->SetY(265);


                // Output the PDF
                $pdf->Output();
            } else {
                // Handle the case where the check is not found
                echo "Invoice not found.";
                exit;
            }
        } else {
            // Handle the case where the ID is not provided
            echo "No ID provided.";
            exit;
        }
    } else {
        // Handle the case where the check ID is invalid or not found
        echo "Sales Invoice not found!";
    }
} catch (Exception $ex) {
    // Handle any exceptions
    echo "Error: " . $ex->getMessage();
}
