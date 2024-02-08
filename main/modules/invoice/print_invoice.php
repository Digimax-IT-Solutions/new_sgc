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
    a:link, a:visited {
    background-color: white;
    color: black;
    border: 2px solid green;
    padding: 10px 20px;
    text-align: center;
    text-decoration: none;
    display: inline-block;
    }

    a:hover, a:active {
    background-color: green;
    color: white;
    }

    @media print {
      a:link, a:visited {
        display: none; /* Hide the button group when printing */
      }
    }
    .grid-container {
      display: grid;
      grid-template-columns: 1fr 1fr; /* Two equal-width columns */
      gap: 20px; /* Adjust the gap between columns */
    }

    img {
        width: 250px;
        height: 100px;
        position: absolute;
        top: 30px;
    }
    .m-1 {
        text-align: right;
        padding-bottom: 1em;
        padding-top: 1em;
    }
    label{
        font-size: 20px;
        font-weight: bold;
        font-family: 'Poppins';
    }
    #salesInvoiceForm {
        background-color: white;
    }
    span{
        font-weight: normal;
        font-size: 15px;
    }
</style>
<body>
<!-- Container -->
<div class="content-wrapper">
    <img src="../images/conogas.png">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    
                    <h1 class="m-1">Sales Invoice</h1>
                </div>
            </div>
        </div>
    </div>
    <!-- /.content-header -->
<hr>
   
    <div style="padding-bottom: 1.5em;
        padding-top: 1em;" class="grid-container">
        <div class="right-content">
        <label for="">Date printed:</label> 
        </div>

        <div class="left-content" style="position: absolute; right: 0px;">
        <label for="">Invoice #: <span><?php echo $salesInvoice['invoiceNo']; ?></span> </label> 
        </div>
    </div>   
<hr>

    <!-- Main content -->
    <section class="content">
        <div class="grid-container">
            <div class="right-content">
                <label for="customer">Customer Name: <span><?php echo $salesInvoice['customer']; ?></span></label>
                <br>
                <label for="address">Billing Address: <span><?php echo $salesInvoice['address']; ?></span></label>
                <br>
                <label for="shippingAddress">Shipping Address: <span><?php echo $salesInvoice['shippingAddress']; ?></span></label>
                <br>
                <label for="email">Email: <span><?php echo $salesInvoice['email']; ?></span></label>
                <br>
                <label for="account">Account: <span><?php echo $salesInvoice['account']; ?></span></label>
                <br>
                <label for="paymentMethod">Payment Method: <span><?php echo $salesInvoice['paymentMethod']; ?></span></label>
            </div>
                                
            <div class="left-content">
                <label for="invoiceDate">Invoice Date: <span><?php echo $salesInvoice['invoiceDate']; ?></span></label>
                <br>
                <label for="invoiceDueDate">Invoice Due Date: <span><?php echo $salesInvoice['invoiceDueDate']; ?></span></label>
                <br>
                <label for="terms">Terms: <span><?php echo $salesInvoice['terms']; ?></span></label>
                <br>
                <label for="location">Location: <span><?php echo $salesInvoice['location']; ?></span></label>
            </div>
        </div>
    </section>
        <br>
                <?php
                $servername = "localhost";
                $username = "root";
                $password = "digimax2023";
                $dbname = "sales";
                
                $conn = new mysqli($servername, $username, $password, $dbname);

                                        
                if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
                }

                $invoiceID = $_GET['invoiceID'];

                $sql = "SELECT `itemID`, `item`, `description`, `quantity`, `uom`, `rate`, `amount`, `created_at` FROM `sales_invoice_items`
                        WHERE `salesInvoiceId` = '$invoiceID'";

                $result = $conn->query($sql);

                echo "<html><head><style>
                    table{
                        font-family: 'Poppins';
                        border-collapse: collapse;
                        width: 100%;
                        }
                    th, td {
                        border: 1px solid #dddddd;
                        text-align: left;
                        padding: 8px;
                        }
                    tr:nth-child(even) {
                        background-color: #f2f2f2;
                        }
                    </style></head><body>";

                    if ($result->num_rows > 0) {
                        echo "<table><tr><th>Item ID</th><th>Item</th><th>Description</th><th>Quantity</th><th>UOM</th><th>Rate</th><th>Amount</th><th>Created At</th></tr>";

                        while ($row = $result->fetch_assoc()) {
                        echo "<tr><td>" . $row["itemID"] . "</td><td>" . $row["item"] . "</td><td>" . $row["description"] . "</td><td>" . $row["quantity"] . "</td><td>" . $row["uom"] . "</td><td>" . $row["rate"] . "</td><td>" . $row["amount"] . "</td><td>" . $row["created_at"] . "</td></tr>";
                        }
                                            
                        echo "</table>";
                        } else {
                         echo "0 results";
                        }

                        echo "</body></html>";

                        $conn->close();

                        ?>
                        <br>
                    
                                        <div class="grid-container">
                                            <div class="left-content">
                                                <label>Gross Amount:</label>
                                                <span><?php echo $salesInvoice['grossAmount']; ?></span>
                                                <label for="discountPercentage">Discount %:</label>
                                                <span><?php echo $salesInvoice['discountPercentage']; ?>%</span>
                                                <label>Net Amount Due:</label>
                                                <span><?php echo $salesInvoice['netAmountDue']; ?></span>
                                                <label for="vatPercentage">VAT (%):</label>
                                                <span><?php echo $salesInvoice['vatPercentage']; ?>%</span>
                                                <label>Net of VAT:</label>
                                                <span><?php echo $salesInvoice['netOfVat']; ?></span>
                                                <label>Tax Withheld (%):</label>
                                                <span><?php echo $salesInvoice['taxWithheldPercentage']; ?>%</span>
                                                <label>Total Amount Due:</label>
                                                <span><?php echo $salesInvoice['totalAmountDue']; ?></span>
                                            </div>
                                            <div> <a href="javascript:window.print()" class="btn-group">
                                                <i class="fa fa-print"></i>Print</a> 
                                            </div>
                                        </div>
                            <!-- End Sales Invoice Form -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
</body>

</html>