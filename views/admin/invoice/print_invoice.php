<?php

require_once '_init.php';
// Include FPDF library
require_once 'plugins/fpdf186/fpdf.php';



try {

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $invoice = Invoice::find($id);
        if ($invoice) {

            // Get the check ID from the request
            $invoice_id = $_GET['id']; // Assuming the ID is passed as a query parameter

            // Fetch invoice data based on the provided ID
            $invoice = Invoice::find($invoice_id);

            // Invoice if the invoice exists
            if ($invoice) {

                // Create a new PDF instance
                $pdf = new FPDF();
                $pdf->AddPage();

                // Add a watermark based on the invoice status
                if ($invoice->invoice_status == 3) {
                    // If invoice status is 3, add a "VOID" watermark
                    $pdf->SetFont('Arial', 'B', 190);
                    $pdf->RotatedText(55, 190, 'VOID', 45, array(192, 192, 192)); // Light gray color
                } elseif ($invoice->invoice_status == 4) {
                    // If invoice status is 4, add a "DRAFT" watermark
                    $pdf->SetFont('Arial', 'B', 175);
                    $pdf->RotatedText(55, 190, 'DRAFT', 45, array(192, 192, 192)); // Light gray color
                }


                // Set margins
                $pdf->SetMargins(10, 10, 10);

                // Move to top right corner for "Original Copy"
                $pdf->SetXY(-40, 10);
                $pdf->SetFont('Arial', '', 8);

                if ($invoice->print_status == 1) {
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
                $pdf->Image('photos/banner.png', 10, 15, $bannerWidth, 0, 'PNG');

                // Move below banner
                $pdf->SetY($pdf->GetY() + 8);

                // Company details centered under banner
                $pdf->SetFont('Arial', '', 10);
                $pdf->Cell(0, 4, '19/F Citibank Tower, 8741 Paseo De Roxas,', 0, 1, 'R');
                $pdf->Cell(0, 4, '1Bel-Air, Makati City, Metro Manila, Philippines', 0, 1, 'R');
                $pdf->Cell(0, 4, 'VAT Reg. TIN: 000-123-533-00000', 0, 1, 'R');
                $pdf->Cell(0, 4, 'Tel. No: +63 (53) 553 0058', 0, 1, 'R');

                $pdf->SetY($pdf->GetY() + 15);
                $pdf->SetFont('Arial', 'B', 14);
                $pdf->Cell(0, 4, 'SALES INVOICE', 0, 3, 'C');
                $pdf->Cell(0, 10, 'No. ' . $invoice->invoice_number, 0, 1, 'R');
                $pdf->Ln(5);

                // Set font and size for the details section
                $pdf->SetFont('Arial', '', 9);

                $lineHeight = 5; // Adjust according to your line spacing requirement
                $rightColumnX = $pageWidth - 60; // X position for the right column

                // Date and Terms
                // Invoice details
                $date = new DateTime($invoice->invoice_date);
                $formattedDate = $date->format('m/d/y');
                $pdf->Cell(100, $lineHeight, 'Date: ' . $formattedDate, 0, 0, 'L');
                $pdf->SetX($rightColumnX);
                $pdf->Cell(0, $lineHeight, 'Terms: ' . $invoice->terms, 0, 1, 'L');

                // Customer and Rep
                $pdf->Cell(100, $lineHeight, 'Customer: ' . $invoice->customer_name, 0, 0, 'L');
                $pdf->SetX($rightColumnX);
                $pdf->Cell(0, $lineHeight, 'Rep: ' . $invoice->rep, 0, 1, 'L');

                // Address
                $pdf->Cell(100, $lineHeight, 'Address: ' . $invoice->shipping_address, 0, 0, 'L');
                $pdf->SetX($rightColumnX);
                $pdf->Cell(0, $lineHeight, '', 0, 1, 'L');

                // Business style and Customer P.O No.
                $pdf->Cell(100, $lineHeight, 'Business Style: ' . $invoice->business_style, 0, 0, 'L');
                $pdf->SetX($rightColumnX);
                $pdf->Cell(0, $lineHeight, 'Customer P.O No.: ' . $invoice->customer_po, 0, 1, 'L');

                // TIN and S.O No.
                $pdf->Cell(100, $lineHeight, 'TIN: ' . $invoice->customer_tin, 0, 0, 'L');
                $pdf->SetX($rightColumnX);
                $pdf->Cell(0, $lineHeight, 'S.O No.: ' . $invoice->so_no, 0, 1, 'L');

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

                if ($invoice) {
                    foreach ($invoice->details as $detail) {
                        $totalCost += $detail['cost'];
                        $totalAmount += $detail['amount'];

                        // Get the starting Y position and X position
                        $startY = $pdf->GetY();
                        $currentX = $pdf->GetX();

                        // Define column widths
                        $itemNameWidth = 40; // Width for Item Name
                        $descriptionWidth = 60; // Width for Description

                        // Measure the width of the item name
                        $pdf->SetFont('Arial', '', 10);
                        $itemName = $detail['item_name'];
                        $itemNameWidthActual = $pdf->GetStringWidth($itemName);

                        // Check if the item name exceeds the allowed width
                        if ($itemNameWidthActual > $itemNameWidth) {
                            // Truncate item name with ellipsis
                            $charWidth = $pdf->GetStringWidth('M'); // Width of a single character (approximation)

                            // Ensure that 10 characters are always visible before truncating
                            $extraChars = -10; // Number of characters to keep before truncating
                            $maxChars = floor($itemNameWidth / $charWidth) - 3 - $extraChars; // Reserve space for ellipsis

                            if (strlen($itemName) > $maxChars) {
                                $truncatedItemName = substr($itemName, 0, $maxChars) . '...';
                            } else {
                                $truncatedItemName = $itemName;
                            }

                            $itemName = $truncatedItemName;
                        }

                        // Item Name (using Cell to avoid wrapping)
                        $pdf->SetXY($currentX, $startY);
                        $pdf->Cell($itemNameWidth, 5, $itemName, 0, 0, 'L');
                        $itemNameEndY = $pdf->GetY(); // Capture Y position after writing

                        // Move to the X position for Description
                        $pdf->SetXY($currentX + $itemNameWidth, $startY);

                        // Truncate Description if needed
                        $itemSalesDescription = $detail['item_sales_description'];
                        $descriptionWidthActual = $pdf->GetStringWidth($itemSalesDescription);

                        if ($descriptionWidthActual > $descriptionWidth) {
                            $charWidth = $pdf->GetStringWidth('M'); // Width of a single character (approximation)

                            // Ensure that 10 characters are always visible before truncating
                            $extraChars = 10; // Number of characters to keep before truncating
                            $maxChars = floor($descriptionWidth / $charWidth) - 3 - $extraChars; // Reserve space for ellipsis

                            if (strlen($itemSalesDescription) > $maxChars) {
                                $truncatedDescription = substr($itemSalesDescription, 0, $maxChars) . '...';
                            } else {
                                $truncatedDescription = $itemSalesDescription;
                            }

                            $itemSalesDescription = $truncatedDescription;
                        }

                        // Description (using MultiCell)
                        $pdf->MultiCell($descriptionWidth, 5, $itemSalesDescription, 0, 'L');
                        $descriptionEndY = $pdf->GetY(); // Capture Y position after wrapping

                        // Determine the maximum Y position for the row
                        $maxEndY = max($itemNameEndY, $descriptionEndY);

                        // Move to the X position for other details
                        $pdf->SetXY($currentX + $itemNameWidth + $descriptionWidth, $startY);

                        // Other details
                        $pdf->Cell(20, 5, $detail['quantity'], 0, 0, 'C');
                        $pdf->Cell(20, 5, $detail['uom_name'], 0, 0, 'C');
                        $pdf->Cell(25, 5, number_format($detail['cost'], 2), 0, 0, 'C');
                        $pdf->Cell(25, 5, number_format($detail['amount'], 2), 0, 0, 'R');

                        // Move to the next line, considering the highest content height
                        $pdf->SetY($maxEndY);
                    }
                }








                $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); // Draw a line
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(159, 10, 'Gross Amount:', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($invoice->gross_amount, 2), 0, 0, 'R');
                $pdf->Ln(4); // Move to the next line after the last row
                $pdf->Cell(159, 10, 'Less: Discount:', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($invoice->discount_amount, 2), 0, 0, 'R');
                $pdf->Ln(4); // Move to the next line after the last row
                $pdf->Cell(159, 10, 'Net Amount:', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($invoice->net_amount_due, 2), 0, 0, 'R');
                // $pdf->Ln(4); // Move to the next line after the last row
                // $pdf->Cell(159, 10, 'VAT Sales:', 0, 0, 'R');
                // $pdf->Cell(30, 10, number_format($invoice->vatable_amount, 2), 0, 0, 'R');
                $pdf->Ln(4); // Move to the next line after the last row
                $pdf->Cell(159, 10, 'Vatable (12%):', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($invoice->vat_amount, 2), 0, 0, 'R');
                $pdf->Ln(4); // Move to the next line after the last row
                $pdf->Cell(159, 10, 'Vat-Exempt Sales:', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($invoice->vat_exempt_amount, 2), 0, 0, 'R');
                $pdf->Ln(4); // Move to the next line after the last row
                $pdf->Cell(159, 10, 'Zero-rated Sales:', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($invoice->zero_rated_amount, 2), 0, 0, 'R');
                $pdf->Ln(4); // Move to the next line after the last row
                $pdf->Cell(159, 10, ' Withholding Tax:', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($invoice->tax_withheld_amount, 2), 0, 0, 'R');
                $pdf->Ln(4); // Move to the next line after the last row

                $pdf->SetFont('Arial', 'B', 10); // 'B' indicates bold
                $pdf->Cell(159, 10, 'Total Amount Due:', 0, 0, 'R');
                $pdf->Cell(30, 10, number_format($invoice->total_amount_due, 2), 0, 0, 'R');
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
                $pdf->MultiCell($cellWidth, $lineHeight, 'Memo: ' . $invoice->memo, 0, 'L', false);

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
