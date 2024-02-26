<?php
include('connect.php');
session_start();
$salesInvoice = null;
$salesInvoiceItems = [];

// Check if the 'poID' parameter is set
if (isset($_GET['invoiceID'])) {
    $invoiceID = $_GET['invoiceID'];

    // Query to retrieve purchase order details
    $query = "SELECT * FROM sales_invoice WHERE invoiceID = :invoiceID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':invoiceID', $invoiceID);
    $stmt->execute();

    // Fetch purchase order details
    $salesInvoice = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if purchase order details are found
    if ($salesInvoice) {
        // Fetch purchase order items
        $queryInvoiceItems = "SELECT * FROM sales_invoice_items WHERE salesInvoiceID = :invoiceID";
        $stmtInvoiceItems = $db->prepare($queryInvoiceItems);
        $stmtInvoiceItems->bindParam(':invoiceID', $invoiceID);
        $stmtInvoiceItems->execute();

        $salesInvoiceItems = $stmtInvoiceItems->fetchAll(PDO::FETCH_ASSOC);

        $grossAmount = $salesInvoice['grossAmount'];
        $grossAmountFormatted = number_format($salesInvoice['grossAmount'], 2, '.', ',');
        $vatPercentage = $salesInvoice['vatPercentage'];
        $vatAmount = number_format($grossAmount / (1 + $vatPercentage / 100) * ($vatPercentage / 100), 2);
        $wvatPercentage = $salesInvoice['taxWithheldPercentage'];
        $netOfVat = $salesInvoice['netOfVat'];
        $netOfVatFormatted = number_format($salesInvoice['netOfVat'], 2, '.', ','); // Total amount without VAT
        $netAmountDue = $salesInvoice['netAmountDue']; // Total amount with VAT
        $wvatAmount = number_format(($netOfVat * $wvatPercentage) / 100, 2);
        $totalAmountDue = number_format($salesInvoice['totalAmountDue'], 2, '.', ',');

    } else {
        // Redirect or display an error if purchase order details are not found
        header("Location: index.php"); // Redirect to the main page or display an error message
        exit();
    }
} else {
    // Redirect or display an error if 'poID' parameter is not set
    header("Location: index.php"); // Redirect to the main page or display an error message
    exit();
}




// Fetch product items
$query = "SELECT itemName, itemSalesInfo, itemSrp FROM items";
$result = $db->query($query);

$productItems = array();

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $productItems[] = $row;
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="icon" type="image/x-icon" href="../images/conogas.png">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900' type='text/css'>
<title>Invoice Print</title>
<meta name="author" content="harnishdesign.net">
</head>
<style>
    table {
        border-collapse: collapse;
        width: 100%;
        table-layout: fixed;
    }
   td {
        text-overflow: ellipsis;
    }
    body {
        font-family: calibri;
    }
    .invoiceno {
        position: absolute;
        left: 19px;
        top: 100px;
        font-size: 12px;
    }
    .address {
        position: absolute;
        left: 19px;
        top: 115px;
        font-size: 12px;  
        text-overflow: ellipsis;
        overflow: hidden;
        width: 350px;
    }
    .terms {
        position: absolute;
        right: 220px;
        top: 134px;
        font-size: 12px;
    }
    .bstyle {
        position: absolute;
        left: 310px;
        top: 180px;
        font-size: 12px;
    }
    .tin {
        position: absolute;
        left: 50px;
        top: 180px;
        font-size: 12px;
    }
    .date {
        position: absolute;
        right: 0px;
        top: 100px;
        font-size: 12px;
    }
    table {
        position: absolute;
        top: 235px;
        left: 18px;
        width: 96%;
        font-size: 12px;
    }
    .totalamount {
        position: absolute;
        right: 14px;
        bottom: 310px;
        font-size: 12px;
    }
    .netofvat {
        position: absolute;
        right: 14px;
        bottom: 272px;
        font-size:12px;
    }
    .netamount {
        position: absolute;
        right: 14px;
        bottom: 162px;
        font-size: 12px;   
    }
    .vat {
        position: absolute;
        left: 275px;
        bottom: 177px;
        font-size: 12px; 
    }
    .wvat{
        position: absolute;
        right: 14px;
        bottom: 220px;
        font-size: 12px; 
    }
    .vat1{
        position: absolute;
        right: 14px;
        bottom: 290px;
        font-size: 12px; 
    }
    .nad {
        position: absolute;
        right: 14px;
        bottom: 200px;
        font-size: 12px;   
    }
    .ves {
        position: absolute;
        left: 275px;
        bottom: 217px;
        font-size: 12px; 
    }
    .po {
        position: absolute;
        right: 150px;
        top: 100px;
        font-size: 12px;  
    }
    .netofvat2 {
        position: absolute;
        right: 14px;
        bottom: 180px;
        font-size: 12px;   
    }
</style>
<body>
    <div class="invoiceno"><?php echo $salesInvoice['customer']; ?></div>
    <div class="address"><?php echo $salesInvoice['address']; ?></div>  
    <div class="terms"><?php echo $salesInvoice['terms']; ?></div>
    <div class="tin"><?php echo $salesInvoice['invoiceTin']; ?></div>
    <div class="date"><?php echo date('m/d/Y', strtotime($salesInvoice['invoiceDate'])); ?></div> 
    <div class="bstyle"><?php echo $salesInvoice['invoiceBusinessStyle']; ?></div>  
    <div class="po"><?php echo $salesInvoice['invoicePo']; ?></div>  
    <table class="table">
    <tbody>
        <?php
        // Connect to your database
        $servername = "localhost";
        $username = "root";
        $password = "digimax2023";
        $dbname = "sgc_db";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $invoiceID = $_GET['invoiceID'];

        // Query to fetch rows from the database
        $sql = "SELECT `itemID`, `item`, `description`, `quantity`, `uom`, `rate`, `amount`, `status`, `created_at` FROM `sales_invoice_items`
            WHERE `salesInvoiceId` = '$invoiceID'";

        $result = $conn->query($sql);

        // If rows are found, display them in HTML table rows
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $amountFormatted = number_format($row['amount'], 2, '.', ',');
                $rateFormatted = number_format($row['rate'], 2, '.', ',');
                echo "<tr>
                    <td style='text-align: left; padding-bottom: 13.5px; white-space: nowrap;'>" . $row["description"] . "</td>
                    <td style='padding-left: 325px; padding-bottom: 13.5px; white-space: nowrap;'>" . $row["quantity"] . "</td>
                    <td style='text-align: left; padding-bottom: 13.5px; padding-left: 270px; white-space: nowrap;'>" . $row["uom"] . "</td>
                    <td style='text-align: right; width: 100px; padding-left: 210px; padding-bottom: 13.5px; white-space: nowrap;'>" . $rateFormatted . "</td>
                    <td style='text-align: right; padding-bottom: 13.5px;'>" . $amountFormatted . "</td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='5'>No items found</td></tr>";
        }

        // Close the database connection
        $conn->close();
        ?>
    </tbody>
</table>
<div class="totalamount"><?php echo $grossAmountFormatted; ?></div>
<div class="netofvat"><?php echo $netOfVatFormatted ?></div>
<div class="netamount"><?php echo $totalAmountDue; ?></div>
<div class="vat"><?php echo $vatAmount; ?></div>
<div class="ves"><?php echo $netOfVatFormatted ?></div>  
<div class="wvat"><?php echo $wvatAmount; ?></div>
<div class="vat1"><?php echo $vatAmount; ?></div>
<div class="netofvat2"><?php echo $vatAmount ?></div>
<div class="nad"><?php echo $grossAmountFormatted; ?></div>           
</body>
<script>
        window.onload = function() {
            window.print(); // Automatically print the page when it loads
        };

        // Event listener for the beforeprint event
        window.onbeforeprint = function() {
            // Delay redirect to ensure the print dialog is shown
            setTimeout(function() {
                // Redirect to the specified URL
                window.history.back(); // Replace 'https://example.com' with your desired URL
            }, 0);
        };
    </script>
</html>