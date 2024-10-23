<?php

require_once '_init.php';
// Include FPDF library
require_once 'plugins/fpdf186/fpdf.php';

try {
    if (isset($_GET['id']) || isset($_GET['ref_no'])) {
        $id = $_GET['id'];

        $check = WriteCheck::find($id);
        if ($check) {
        } else {
            echo "Check not found.";
            exit;
        }
    } else {
        echo "No ID provided.";
        exit;
    }

    $checkId = $_GET['id'];

    $check = WriteCheck::find($checkId);

    if ($check) {
        // Create a new PDF instance
        $pdf = new FPDF();
        $pdf->AddPage();

        //$backgroundImage = 'photos/cheque.png'; // Replace with your image path
        //$pdf->Image($backgroundImage, 0, 0, $pdf->GetPageWidth(), 80);


        $pdf->SetDrawColor(255, 0, 0);
        $pdf->SetLineWidth(1); // Set line width

        $pdf->Line(40, -10, -7, 40);
        $pdf->Line(50, -10, -7, 50);

        $pdf->SetFont('Arial', 'B', 11);
        $pdf->SetTextColor(255, 0, 0); // Set text color to red
        $pdf->SetXY(23, 7);
        $pdf->Rotate(61); // Rotate text to match diagonal lines
        $pdf->Cell(14, 10, "PAYEE'S ACCOUNT ONLY", 0, 0, 'R');
        $pdf->Rotate(0); // Reset rotation

        $pdf->SetDrawColor(0, 0, 0);
        $pdf->SetTextColor(0, 0, 0);
        
        $maxWidth = 80; // Adjust according to your column width requirement

        // Set line height
        $lineHeight = 5; // Adjust according to your line spacing requirement

        // Set padding for the cells (space between left-aligned and right-aligned text)
        $padding = -100;

        $pdf->Ln(2);
        $pdf->SetFont('Arial', 'B', 11);

        $dateParts = date('mdY', strtotime($check->check_date));

        // Split the date into parts and add spaces manually
        $month = substr($dateParts, 0, 2);
        $day = substr($dateParts, 2, 2);
        $year = substr($dateParts, 4);

        $textDate = $month[0] . '   ' . $month[1] . '    ' . $day[0] . '   ' . $day[1] . '    ' . $year[0] . '   ' . $year[1] . '   ' . $year[2] . '   ' . $year[3];

        $pdf->Cell($pdf->GetPageWidth() - 20, $lineHeight, $textDate, 0, 0, 'R');

        $pdf->Ln(11);

        $pdf->SetFont('Arial', 'B', 11);
        $textLeft = "** " . $check->payee_name . " **";
        $textRight = "** " . number_format($check->gross_amount, 2) . " **";
        $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textLeft, 0, 0, 'C');
        $pdf->Cell(1 - $padding, $lineHeight, $textRight, 0, 0, 'L');

        function convertNumberToWord($num) {
            $words = array(
                0 => '', 1 => 'ONE', 2 => 'TWO', 3 => 'THREE', 4 => 'FOUR', 5 => 'FIVE', 
                6 => 'SIX', 7 => 'SEVEN', 8 => 'EIGHT', 9 => 'NINE', 10 => 'TEN', 
                11 => 'ELEVEN', 12 => 'TWELVE', 13 => 'THIRTEEN', 14 => 'FOURTEEN', 
                15 => 'FIFTEEN', 16 => 'SIXTEEN', 17 => 'SEVENTEEN', 18 => 'EIGHTEEN', 
                19 => 'NINETEEN', 20 => 'TWENTY', 30 => 'THIRTY', 40 => 'FORTY', 
                50 => 'FIFTY', 60 => 'SIXTY', 70 => 'SEVENTY', 80 => 'EIGHTY', 90 => 'NINETY'
            );
        
            if ($num < 21) {
                return $words[$num];
            } elseif ($num < 100) {
                return $words[10 * floor($num / 10)] . ' ' . $words[$num % 10];
            } elseif ($num < 1000) {
                return $words[floor($num / 100)] . ' HUNDRED ' . convertNumberToWord($num % 100);
            } elseif ($num < 1000000) {
                return convertNumberToWord(floor($num / 1000)) . ' THOUSAND ' . convertNumberToWord($num % 1000);
            } elseif ($num < 1000000000) {
                return convertNumberToWord(floor($num / 1000000)) . ' MILLION ' . convertNumberToWord($num % 1000000);
            }
        }
        
        $pdf->Ln(9);
        $pdf->SetFont('Arial', 'B', 10);
        
        $textLeft = "** " . convertNumberToWord($check->gross_amount) . " PESOS ONLY **";
        $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textLeft, 0, 0, 'C');

        $pdf->Output();
    } else {
        // Handle the case where the check ID is invalid or not found
        echo "Check not found!";
    }
} catch (Exception $ex) {
    // Handle any exceptions
    echo "Error: " . $ex->getMessage();
}