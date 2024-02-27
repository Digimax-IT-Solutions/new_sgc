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
<link rel="icon" type="image/x-icon" href="../images/sgc.png">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Poppins:100,200,300,400,500,600,700,800,900' type='text/css'>
<title>Invoice Print</title>
<meta name="author" content="harnishdesign.net">
</head>
<style>

body {
    margin: 0; /* Remove default margins */
    padding: 0; /* Remove default padding */
    background-image: url('../images/print.jpg'); /* Set the background image */
    background-size: cover; /* Adjust background size */
    background-repeat: no-repeat;
    font-family: calibri; /* Prevent the image from repeating */
  }
  @media print {
    body {
      -webkit-print-color-adjust: exact; /* For Chrome */
      background-color: white; /* Ensure background color is white for printing */
    }
  }
    .invoiceno {
        position: absolute;
        left: 54px;
        top: 150px;
        font-size: 17px;
    }
    .address {
        position: absolute;
        left: 54px;
        top: 170px;
        font-size: 17px;  
        text-overflow: ellipsis;
        overflow: hidden;
        width: 350px;
    }
    .terms {
        position: absolute;
        right: 250px;
        top: 180px;
        font-size: 17px;
        text-align: left;
    }
    .bstyle {
        position: absolute;
        left: 350px;
        top: 222px;
        font-size: 13px;
        text-align: left;
        width: 350px;
    }
    .tin {
        position: absolute;
        left: 84px;
        top: 220px;
        font-size: 15px;
    }
    .po {
        position: absolute;
        right: 190px;
        top: 145px;
        font-size: 17px;  
    }
    .date {
        position: absolute;
        right: 35px;
        top: 145px;
        font-size: 17px;
    }
    table {
        position: absolute;
        top: 273px;
        left: 50px;
        width: 96%;
        font-size: 15px;
        table-layout: fixed;
    }
    .totalamount {
        position: absolute;
        right: 27px;
        bottom: 355px;
        font-size: 17px;
        text-align: right;
    }
    .vat1{
        position: absolute;
        right: 27px;
        bottom: 332px;
        font-size: 17px; 
        text-align: right;
    }
    .netofvat {
        position: absolute;
        right: 27px;
        bottom: 309px;
        font-size: 17px;
    }
    .wvat{
        position: absolute;
        right: 27px;
        bottom: 263px;
        font-size: 17px; 
    }
    .nad {
        position: absolute;
        right: 27px;
        bottom: 242px;
        font-size: 17px;   
    }
    .netofvat2 {
        position: absolute;
        right: 27px;
        bottom: 219px;
        font-size: 17px;   
    }
    .netamount {
        position: absolute;
        right: 27px;
        bottom: 196px;
        font-size: 17px;   
    }
    .ves {
        position: absolute;
        left: 330px;
        bottom: 219px;
        font-size: 17px; 
    }
    .vat {
        position: absolute;
        left: 330px;
        bottom: 263px;
        font-size: 17px; 
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
                    <td style='text-align: left; padding-bottom: 9px; white-space: nowrap;'>" . $row["description"] . "</td>
                    <td style='text-align: left; padding-left: 255px; padding-bottom: 9px; white-space: nowrap;'>" . $row["quantity"] . "</td>
                    <td style='text-align: left; padding-bottom: 9px; padding-left: 157px; white-space: nowrap;'>" . $row["uom"] . "</td>
                    <td style='text-align: right; width: 100px; padding-left: 40px; padding-bottom: 9px; white-space: nowrap;'>" . $rateFormatted . "</td>
                    <td style='text-align: right; padding-right: 40px; padding-bottom: 9px;'>" . $amountFormatted . "</td>
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