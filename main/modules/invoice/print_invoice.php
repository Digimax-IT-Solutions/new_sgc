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
        left: 35px;
        top: 150px;
        font-size: 15px;
    }
    .address {
        position: absolute;
        left: 35px;
        top: 170px;
        font-size: 15px;  
        text-overflow: ellipsis;
        overflow: hidden;
        width: 350px;
    }
    .terms {
        position: absolute;
        right: 250px;
        top: 180px;
        font-size: 15px;
        text-align: left;
    }
    .bstyle {
        position: absolute;
        left: 318px;
        top: 217px;
        font-size: 15px;
        text-align: left;
        width: 350px;
        text-overflow: ellipsis;
        overflow: hidden;
    }
    .tin {
        position: absolute;
        left: 65px;
        top: 217px;
        font-size: 15px;
    }
    .po {
        position: absolute;
        right: 190px;
        top: 145px;
        font-size: 15px;  
    }
    .date {
        position: absolute;
        right: 40px;
        top: 145px;
        font-size: 15px;
    }
    #printInvoice {
        position: absolute;
        top: 270px;
        left: 35px;
        width: 96%;
        font-size: 15px;
        table-layout: fixed;
    }
    #printInvoice td {
        overflow: hidden;
        padding-bottom: 5px;
        
    }
    #printInvoice td:first-child {
        width: 50%;
    }
    #printInvoice td:nth-child(4), #printInvoice td:nth-child(5) {
        text-align: right !important;
    }
    .totalamount {
        position: absolute;
        right: 40px;
        bottom: 355px;
        font-size: 15px;
        text-align: right;
    }
    .vat1{
        position: absolute;
        right: 40px;
        bottom: 332px;
        font-size: 15px; 
        text-align: right;
    }
    .netofvat {
        position: absolute;
        right: 40px;
        bottom: 309px;
        font-size: 15px;
    }
    .wvat{
        position: absolute;
        right: 40px;
        bottom: 263px;
        font-size: 15px; 
    }
    .nad {
        position: absolute;
        right: 40px;
        bottom: 242px;
        font-size: 15px;   
    }
    .netofvat2 {
        position: absolute;
        right: 40px;
        bottom: 219px;
        font-size: 15px;   
    }
    .netamount {
        position: absolute;
        right: 40px;
        bottom: 196px;
        font-size: 15px;   
    }
    .ves {
        position: absolute;
        left: 317px;
        bottom: 286px;
        font-size: 15px; 
    }
    .vat {
        position: absolute;
        left: 317px;
        bottom: 219px;
        font-size: 15px; 
    }
    .zrsale {
        position: absolute;
        left: 317px;
        bottom: 239px;
        font-size: 15px;  
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
    <table id="printInvoice">
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
                $prefix = '';
                $uom = $row["uom"];
                if (preg_match('/\((.*?)\)/', $uom, $matches)) {
                    $prefix = $matches[1];
                    $uom = trim(str_replace('(' . $prefix . ')', '', $uom));
                }
                $amountFormatted = number_format($row['amount'], 2, '.', ',');
                $rateFormatted = number_format($row['rate'], 2, '.', ',');
                echo "<tr>
                    <td style='text-align: left;'>" . $row["description"] . "</td>
                    <td style='text-align: left; width: 40px; padding-left: 5px;'>" . $row["quantity"] . "</td>
                    <td style='text-align: left; padding-left: 30px;'>" . $uom . "</td>
                    <td style='padding-right: 45px;'>" . $rateFormatted . "</td>
                    <td style='padding-right: 40px;'>" . $amountFormatted . "</td>
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
<?php if ($vatAmount <= 0): ?>
    <div class="zrsale"><?php echo $totalAmountDue; ?></div>
<?php endif; ?>

<div class="netamount"><?php echo $totalAmountDue; ?></div>

<?php if ($vatAmount != 0): ?>
    <div class="totalamount"><?php echo $grossAmountFormatted; ?></div>
    <div class="netofvat"><?php echo $netOfVatFormatted ?></div>
    <div class="netofvat2"><?php echo $vatAmount ?></div>
    <div class="ves"><?php echo $netOfVatFormatted ?></div>  
    <div class="wvat"><?php echo $wvatAmount; ?></div>
    <div class="vat1"><?php echo $vatAmount; ?></div>
    <div class="vat"><?php echo $vatAmount; ?></div>
    <div class="nad"><?php echo $grossAmountFormatted; ?></div>
<?php endif; ?>       
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