<?php
include('../connect.php');

if (isset($_GET['invoiceID'])) {
    $invoiceID = $_GET['invoiceID'];

    try {
        // Fetch sales invoice details with related information
        $query = "SELECT 
                    invoiceID, 
                    invoiceNo, 
                    invoiceDate, 
                    invoiceDueDate, 
                    customer, 
                    address, 
                    shippingAddress,
                    email, 
                    account, 
                    terms, 
                    location, 
                    paymentMethod, 
                    grossAmount, 
                    discountPercentage, 
                    netAmountDue, 
                    vatPercentage, 
                    netOfVat, 
                    taxWithheldPercentage, 
                    totalAmountDue, 
                    invoiceStatus,
                    created_at
                FROM sales_invoice
                WHERE invoiceID = :invoiceID";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':invoiceID', $invoiceID);
        $stmt->execute();
        $invoiceDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        // Fetch items related to the invoice, including itemSrp
        $itemQuery = "SELECT sales_invoice_items.*, items.itemSrp
                      FROM sales_invoice_items
                      JOIN items ON sales_invoice_items.item = items.itemName
                      WHERE sales_invoice_items.salesInvoiceID = :invoiceID";
        $itemStmt = $db->prepare($itemQuery);
        $itemStmt->bindParam(':invoiceID', $invoiceID);
        $itemStmt->execute();
        $invoiceItems = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

        // Your code to display/print the invoice details and items goes here
?>


        <html>

        <head>
            <meta charset="utf-8">
            <script src="script.js"></script>
            <title>ECONOGAS</title>

            <style>
                /* reset */



                * {
                    border: 0;
                    box-sizing: content-box;
                    color: inherit;
                    font-family: inherit;
                    font-size: inherit;
                    font-style: inherit;
                    font-weight: inherit;
                    line-height: inherit;
                    list-style: none;
                    margin: 0;
                    padding: 0;
                    text-decoration: none;
                    vertical-align: top;
                }

                /* content editable */

                *[contenteditable] {
                    border-radius: 0.25em;
                    min-width: 1em;
                    outline: 0;
                }

                *[contenteditable] {
                    cursor: pointer;
                }

                *[contenteditable]:hover,
                *[contenteditable]:focus,
                td:hover *[contenteditable],
                td:focus *[contenteditable],
                img.hover {
                    background: #DEF;
                    box-shadow: 0 0 1em 0.5em #DEF;
                }

                span[contenteditable] {
                    display: inline-block;
                }

                /* heading */

                h1 {
                    font: bold 100% sans-serif;
                    letter-spacing: 0.5em;
                    text-align: center;
                    text-transform: uppercase;
                    color: red;
                }

                /* table */

                table {
                    font-size: 75%;
                    table-layout: fixed;
                    width: 100%;
                }

                table {
                    border-collapse: separate;
                    border-spacing: 2px;
                }

                th,
                td {
                    border-width: 1px;
                    padding: 0.5em;
                    position: relative;
                    text-align: left;
                }

                th,
                td {
                    border-radius: 0.25em;
                    border-style: solid;
                }

                th {
                    background: #EEE;
                    border-color: #BBB;
                }

                td {
                    border-color: #DDD;
                }

                /* page */

                html {
                    font: 16px/1 'Open Sans', sans-serif;
                    overflow: auto;
                    padding: 0.5in;
                }

                html {
                    background: #999;
                    cursor: default;
                }

                body {
                    box-sizing: border-box;
                    height: 11in;
                    margin: 0 auto;
                    overflow: hidden;
                    padding: 0.5in;
                    width: 8.5in;
                }

                body {
                    background: #FFF;
                    border-radius: 1px;
                    box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
                }

                /* header */

                header {
                    margin: 0 0 3em;
                }

                header:after {
                    clear: both;
                    content: "";
                    display: table;
                }

                header h1 {
                    background: rgb(0, 149, 77);
                    border-radius: 0.25em;
                    color: #FFF;
                    margin: 0 0 1em;
                    padding: 0.5em 0;
                }

                header address {
                    float: left;
                    font-size: 75%;
                    font-style: normal;
                    line-height: 1.25;
                    margin: 0 1em 1em 0;
                }

                header address p {
                    margin: 0 0 0.25em;
                }

                header span,
                header img {
                    display: block;
                    float: right;
                }

                header span {
                    margin: 0 0 1em 1em;
                    max-height: 25%;
                    max-width: 60%;
                    position: relative;
                }

                header img {
                    max-height: 100%;
                    max-width: 100%;
                }



                /* article */

                article,
                article address,
                table.meta,
                table.inventory {
                    margin: 0 0 3em;
                }

                article:after {
                    clear: both;
                    content: "";
                    display: table;
                }

                article h1 {
                    clip: rect(0 0 0 0);
                    position: absolute;

                }

                article address {
                    float: left;
                    font-size: 125%;
                    font-weight: bold;
                }

                /* table meta & balance */

                table.meta,
                table.balance {
                    float: right;
                    width: 40%;
                }

                table.meta:after,
                table.balance:after {
                    clear: both;
                    content: "";
                    display: table;
                }

                /* table meta */

                table.meta th {
                    width: 40%;
                }

                table.meta td {
                    width: 60%;
                }

                /* table items */

                table.inventory {
                    clear: both;
                    width: 100%;
                }

                table.inventory th {
                    font-weight: bold;
                    text-align: center;
                }

                table.inventory td:nth-child(1) {
                    width: 26%;
                }

                table.inventory td:nth-child(2) {
                    width: 38%;
                }

                table.inventory td:nth-child(3) {
                    text-align: right;
                    width: 12%;
                }

                table.inventory td:nth-child(4) {
                    text-align: right;
                    width: 12%;
                }

                table.inventory td:nth-child(5) {
                    text-align: right;
                    width: 12%;
                }

                /* table balance */

                table.balance th,
                table.balance td {
                    width: 50%;
                }

                table.balance td {
                    text-align: right;
                }

                /* aside */

                aside h1 {
                    border: none;
                    border-width: 0 0 1px;
                    margin: 0 0 1em;
                }

                aside h1 {
                    border-color: #999;
                    border-bottom-style: solid;
                }

                /* javascript */

                .add,
                .cut {
                    border-width: 1px;
                    display: block;
                    font-size: .8rem;
                    padding: 0.25em 0.5em;
                    float: left;
                    text-align: center;
                    width: 0.6em;
                }

                .add,
                .cut {
                    background: #9AF;
                    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
                    background-image: -moz-linear-gradient(#00ADEE 5%, #0078A5 100%);
                    background-image: -webkit-linear-gradient(#00ADEE 5%, #0078A5 100%);
                    border-radius: 0.5em;
                    border-color: #0076A3;
                    color: #FFF;
                    cursor: pointer;
                    font-weight: bold;
                    text-shadow: 0 -1px 2px rgba(0, 0, 0, 0.333);
                }

                .add {
                    margin: -2.5em 0 0;
                }

                .add:hover {
                    background: #00ADEE;
                }

                .cut {
                    opacity: 0;
                    position: absolute;
                    top: 0;
                    left: -1.5em;
                }

                .cut {
                    -webkit-transition: opacity 100ms ease-in;
                }

                tr:hover .cut {
                    opacity: 1;
                }

                @media print {
                    * {
                        -webkit-print-color-adjust: exact;
                    }

                    html {
                        background: none;
                        padding: 0;
                    }

                    body {
                        box-shadow: none;
                        margin: 0;
                        text-align: center;
                    }

                    span:empty {
                        display: none;
                    }

                    .add,
                    .cut {
                        display: none;
                    }

                    img {
                        display: inline-block;
                    }

                }

                @page {
                    margin: 0;
                }
            </style>
        </head>


        <body>
            <header>
                <h1>Invoice</h1>
                <address>
                    <img src="econo.jpg" alt="" width="100px">
                </address>

            </header>
            <article>
                <h1>Recipient</h1>

                <address>
                    <p><?php echo $invoiceDetails['customer']; ?></p>
                    <p><?php echo $invoiceDetails['address']; ?></p>
                    <p><?php echo $invoiceDetails['shippingAddress']; ?></p>
                    <p><?php echo $invoiceDetails['email']; ?></p>
                </address>
                <table class="meta">
                    <tr>
                        <th>Date Printed</th>
                        <td id="datePrinted"></td>
                    </tr>
                    <tr>
                        <th><span>Invoice #</span></th>
                        <td><span><?php echo $invoiceDetails['invoiceNo']; ?></span></td>
                    </tr>
                    <tr>
                        <th><span>Date</span></th>
                        <td><span><?php echo $invoiceDetails['invoiceDate']; ?></span></td>
                    </tr>
                </table>
                <table class="inventory">
                    <thead>

                        <tr>
                            <th><span>ITEM</span></th>
                            <th><span>DESCRIPTION</span></th>
                            <th><span>RATE</span></th>
                            <th><span>QTY</span></th>
                            <th><span>UOM</span></th>
                            <th><span>AMOUNT</span></th>
                        </tr>


                    </thead>
                    <tbody>
                        <?php foreach ($invoiceItems as $item) { ?>
                            <tr>
                                <td><?php echo $item['item']; ?></td>
                                <td><?php echo $item['description']; ?></td>
                                <td><?php echo $item['itemSrp']; ?></td>
                                <td><?php echo $item['quantity']; ?></td>
                                <td><?php echo $item['uom']; ?></td>
                                <td>₱<?php echo number_format($item['amount']); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <table class="balance">
                    <tr>
                        <th>Gross Amount</span></th>
                        <td>₱<?php echo number_format($invoiceDetails['grossAmount']); ?></td>
                    </tr>
                    <tr>
                        <th>Discount</span></th>
                        <td><?php echo $invoiceDetails['discountPercentage']; ?></td>
                    </tr>
                    <tr>
                        <th>Net Amount Due</span></th>
                        <td>₱<?php echo number_format($invoiceDetails['netAmountDue']); ?></td>
                    </tr>
                    <tr>
                        <th>VAT</span></th>
                        <td><?php echo $invoiceDetails['vatPercentage']; ?></td>
                    </tr>
                    <tr>
                        <th>Net of VAT</span></th>
                        <td>₱<?php echo number_format($invoiceDetails['netOfVat']); ?></td>
                    </tr>
                    <tr>
                        <th>Tax Withheld</span></th>
                        <td><?php echo $invoiceDetails['taxWithheldPercentage']; ?></td>
                    </tr>
                    <tr>

                        <th style="text-align: center; font-size: 14px;">TOTAL AMOUNT DUE</span></th>
                        <td style="background-color: rgb(0, 149, 77); color: white; font-size: 20px; text-align: center;">
                            ₱<?php echo number_format($invoiceDetails['totalAmountDue']); ?></td>

                    </tr>
                </table>
            </article>

            <script>
                // Get the current date
                var currentDate = new Date();

                // Format the date as YYYY-MM-DD
                var formattedDate = currentDate.toISOString().split('T')[0];

                // Display the formatted date in the "datePrinted" element
                document.getElementById("datePrinted").innerHTML = formattedDate;
            </script>
        </body>

        </html>

<?php
    } catch (PDOException $e) {
        // Handle exceptions if needed
        echo "Error: " . $e->getMessage();
    }
}
?>