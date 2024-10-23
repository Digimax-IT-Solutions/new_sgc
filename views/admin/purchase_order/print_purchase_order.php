<?php

require_once '_init.php';
// Include FPDF library
require_once 'plugins/fpdf186/fpdf.php';


try {

    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        $purchase_order = PurchaseOrder::find($id);

        if ($purchase_order) {

            // Get the check ID from the request
            $po_id = $_GET['id']; // Assuming the ID is passed as a query parameter

            // Fetch purchase data based on the provided ID
            $purchase_order = PurchaseOrder::find($po_id);

            // Check if the purchase exists
            if ($purchase_order) {

                // Create a new PDF instance
                $pdf = new FPDF();
                $pdf->AddPage();

                // Add a watermark based on the purchase_order status
                if ($purchase_order->po_status == 3) {
                    // If purchase_order status is 3, add a "VOID" watermark
                    $pdf->SetFont('Arial', 'B', 190);
                    $pdf->RotatedText(55, 190, 'VOID', 45, array(192, 192, 192)); // Light gray color
                } elseif ($purchase_order->po_status == 4) {
                    // If purchase_order status is 4, add a "DRAFT" watermark
                    $pdf->SetFont('Arial', 'B', 175);
                    $pdf->RotatedText(55, 190, 'DRAFT', 45, array(192, 192, 192)); // Light gray color
                }

                // Set margins
                $pdf->SetMargins(10, 10, 10);

                // Move to top right corner for "Original Copy"
                $pdf->SetXY(-40, 10);
                $pdf->SetFont('Arial', '', 8);

                if ($purchase_order->print_status == 1) {
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
                $pdf->Image('photos/banner.png', $bannerX, 10, $bannerWidth, 0, 'PNG');

                // Move below banner
                $pdf->SetY($pdf->GetY() + 20);

                // Company details centered under banner
                $pdf->SetFont('Arial', '', 7);
                $pdf->Cell(0, 4, 'Montebello, Kanaga, Leyte, 6531', 0, 1, 'C');
                $pdf->Cell(0, 4, 'VAT Reg. TIN: 000-123-533-00000', 0, 1, 'C');
                $pdf->Cell(0, 4, 'Tel. No: +63 (53) 553 0058', 0, 1, 'C');

                $pdf->Ln(5);

                // Sales Invoice title and number
                $pdf->SetFont('Arial', 'B', 14);
                $pdf->Cell(100, 10, 'Purchase Order', 0, 0, 'L');
                $pdf->SetFont('Arial', '', 9);
                $pdf->Cell(0, 10, 'PO No. ' . $purchase_order->po_no, 0, 1, 'R');
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
                $textLeft = 'Date: ' . $purchase_order->po_date;
                $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textLeft, 0, 1, 'L');



                // Text to be displayed on the right
                $textRight = 'Vendor: ' . $purchase_order->vendor_name;
                $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textRight, 0, 0, 'L');

                $textRight = 'Delivery Date: ' . $purchase_order->delivery_date;
                $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textRight, 0, 1, 'L');

                // Text to be displayed on the left
                $textLeft = 'Address: ' . $purchase_order->vendor_address;
                $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textLeft, 0, 0, 'L');
                // Text to be displayed on the left
                $textLeft = 'Terms: ' . $purchase_order->terms;
                $pdf->Cell($maxWidth / 2 - $padding, $lineHeight, $textLeft, 0, 1, 'L');



                $pdf->Ln(2);



                // Table headers
                $pdf->Ln(); // Move to the next line
                $pdf->Cell(0, 10, 'Please furnish us with the following at your qouted prices.', 0, 1, 'C');
                $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); // Draw a line

                // Adjusting the column widths
                $pdf->Cell(30, 10, 'Pr No.', 0, 0, 'L');
                $pdf->Cell(30, 10, 'Item Name', 0, 0, 'L');
                $pdf->Cell(40, 10, 'Description', 0, 0, 'L');
                $pdf->Cell(20, 10, 'Qty', 0, 0, 'C');
                $pdf->Cell(20, 10, 'U/M', 0, 0, 'C');
                $pdf->Cell(25, 10, 'Cost', 0, 0, 'R');
                $pdf->Cell(25, 10, 'Amount', 0, 0, 'R');
                $pdf->Ln(); // Move to the next line
                $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); // Draw a line

                $totalCost = 0;
                $totalAmount = 0;

                if ($purchase_order) {
                    foreach ($purchase_order->details as $detail) {
                        $totalCost += $detail['cost'];
                        $totalAmount += $detail['amount'];
                        
                        $startY = $pdf->GetY();
                        $currentX = $pdf->GetX();
                        
                        // Pr No.
                        $pdf->Cell(30, 5, $detail['pr_no'], 0, 0, 'L');
                        
                        // Item Name
                        $pdf->SetXY($currentX + 30, $startY);
                        $pdf->MultiCell(30, 5, $detail['item_name'], 0, 'L');
                        $itemNameEndY = $pdf->GetY(); // Capture the Y position after Item Name
                        
                        // Ensure enough space for Description
                        $pdf->SetXY($currentX + 70, $startY);
                        
                        // Description (using MultiCell for wrapping text)
                        $pdf->MultiCell(30, 5, $detail['item_purchase_description'], 0, 'L');
                        $descriptionEndY = $pdf->GetY(); // Capture the Y position after Description
                        
                        // Determine the maximum Y position used for the current line
                        $endY = max($itemNameEndY, $descriptionEndY);
                        
                        // Set the X position and Y position for the remaining cells
                        $pdf->SetXY($currentX + 100, $startY);
                        
                        // Other details
                        $pdf->Cell(20, 5, $detail['qty'], 0, 0, 'C');
                        $pdf->Cell(20, 5, $detail['uom_name'], 0, 0, 'C');
                        $pdf->Cell(25, 5, number_format($detail['cost'], 2), 0, 0, 'R');
                        $pdf->Cell(25, 5, number_format($detail['amount'], 2), 0, 0, 'R');
                        
                        // Move to the next line, considering the height of the highest cell content
                        $pdf->SetY($endY);
                    }
                }
                
                


                $pdf->SetFont('Arial', 'B', 10); // 'B' indicates bold
                $pdf->Cell(165, 10, 'Total:', 0, 0, 'R');
                $pdf->Cell(30, 10, 'P' . number_format($totalAmount, 2), 0, 0, 'L');
                $pdf->Ln(15); // Move to the next line after the last row

                $pdf->SetFont('Arial', '', 8);
                $pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 190, $pdf->GetY()); // Draw a line
                
                // First row with labels
                $pdf->Cell(50, 10, 'PREPARED BY:', 0, 0, 'L');
                $pdf->Cell(50, 10, 'CHECKED/CERTIFIED BY:', 0, 0, 'L');
                $pdf->Cell(50, 10, 'VERIFIED BY:', 0, 0, 'L');
                $pdf->Cell(40, 10, 'APPROVED BY:', 0, 1, 'L');
                
                // Second row with names/values
                $pdf->Cell(50, 5, 'Edmundo B. Panta', 0, 0, 'L');
                $pdf->Cell(50, 5, '', 0, 0, 'L');
                $pdf->Cell(50, 5, 'Ana Liza S. Parrilla', 0, 0, 'L');
                $pdf->Cell(40, 5, 'Emmanuel Q. Cadungog', 0, 1, 'L');
                
                // Third row with titles
                $pdf->Cell(50, 5, 'Purchasing Supervisor', 0, 0, 'L');
                $pdf->Cell(50, 5, '', 0, 0, 'L');
                $pdf->Cell(50, 5, 'Audit & Budget Supervisor', 0, 0, 'L');
                $pdf->Cell(40, 5, 'Asst. to Pres/Resident Manager', 0, 1, 'L');
                
                $pdf->Ln(5); // Adjust spacing after the rows
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
                $pdf->MultiCell($cellWidth, $lineHeight, 'Memo: ' . $purchase_order->memo, 0, 'L', false);

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

