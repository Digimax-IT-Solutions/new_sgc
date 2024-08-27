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

        // $backgroundImage = 'photos/cheque.png'; // Replace with your image path
        // $pdf->Image($backgroundImage, 0, 0, $pdf->GetPageWidth(), 80);

        $maxWidth = 80; // Adjust according to your column width requirement

        // Set line height
        $lineHeight = 5; // Adjust according to your line spacing requirement

        // Set padding for the cells (space between left-aligned and right-aligned text)
        $padding = -100;

        $pdf->Ln(2);
        $pdf->SetFont('Courier', '', 12);

        $dateParts = date('mdY', strtotime($check->check_date));

        // Split the date into parts and add spaces manually
        $month = substr($dateParts, 0, 2);
        $day = substr($dateParts, 2, 2);
        $year = substr($dateParts, 4);

        $textDate = $month[0] . ' ' . $month[1] . '  ' . $day[0] . ' ' . $day[1] . '  ' . $year[0] . ' ' . $year[1] . ' ' . $year[2] . ' ' . $year[3];

        $pdf->Cell($pdf->GetPageWidth() - 20, $lineHeight, $textDate, 0, 0, 'R');


        $pdf->Ln(10);

        $pdf->SetFont('Courier', '', 11);
        $textLeft = $check->payee_name;
        $textRight = number_format($check->gross_amount, 2);
        $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textLeft, 0, 0, 'C');
        $pdf->Cell(1 - $padding, $lineHeight, $textRight, 0, 0, 'L');

        function convertNumberToWord($num) {
            $words = array(
                0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five', 
                6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten', 
                11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 
                15 => 'Fifteen', 16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 
                19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty', 40 => 'Forty', 
                50 => 'Fifty', 60 => 'Sixty', 70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
            );
        
            if ($num < 21) {
                return $words[$num];
            } elseif ($num < 100) {
                return $words[10 * floor($num / 10)] . ' ' . $words[$num % 10];
            } elseif ($num < 1000) {
                return $words[floor($num / 100)] . ' Hundred ' . convertNumberToWord($num % 100);
            } elseif ($num < 1000000) {
                return convertNumberToWord(floor($num / 1000)) . ' Thousand ' . convertNumberToWord($num % 1000);
            } elseif ($num < 1000000000) {
                return convertNumberToWord(floor($num / 1000000)) . ' Million ' . convertNumberToWord($num % 1000000);
            }
        }
        
        $pdf->Ln(8);
        $pdf->SetFont('Courier', '', 10);
        
        $textLeft = convertNumberToWord($check->gross_amount) . 'Pesos Only';
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
