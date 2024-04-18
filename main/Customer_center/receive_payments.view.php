<?php
include __DIR__ . ('../../includes/header.php');

?>

<style>
    /* Add styles for active status */
    .active {
        color: green;
        /* Change the text color for active status */
    }

    /* Add styles for inactive status */
    .inactive {
        color: red;
        /* Change the text color for inactive status */
    }

    /* Add a hover effect to the dropdown items */
    .dropdown-item:hover {
        background-color: rgb(0, 149, 77) !important;
        /* Change the background color on hover */
        color: white;
        /* Change the text color on hover */
    }

    #receivePaymentsTable {
        border-collapse: collapse;
        width: 100%;
        table-layout: fixed;
        text-align: right;

    }

    #receivePaymentsTable td:first-child,
    #receivePaymentsTable td:nth-child(2),
    #receivePaymentsTable td:nth-child(3) {
        text-align: center;
    }

    #receivePaymentsTable td:nth-child(4),
    #receivePaymentsTable td:nth-child(5) {
        text-align: left;
    }

    #receivePaymentsTable th {
        font-size: 10px;
        white-space: nowrap;
        border: none;

    }

    #receivePaymentsTable td {
        font-size: 13px;
        padding: 5px;
        overflow: hidden;
        /* Hides any overflowing content */
        border: none;
        border-bottom: 1px solid red;

    }

    #receivePaymentsTable tbody tr:hover {

        color: white;
        background-color: rgba(128, 21, 20, 0.8);
        /* Set your desired background color here */
    }
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Received Payments</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Received Payments</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="newTransactionDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: rgb(0, 149, 77); color: white;">
                                            Receive Payment
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="newTransactionDropdown">
                                            <a class="dropdown-item" href="create_receive_payments" style="background-color: white;">Receive Payment</a>
                                            <a class="dropdown-item" href="credit" style="background-color: white;">Credit And Refunds</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br><br>
                            <table id="receivePaymentsTable" class="table table-hover table-bordered">
                                <thead>

                                    <tr>
                                        <th>REF NO</th>
                                        <th>INVOICE NO</th>
                                        <th>RECEIVE DATE</th>
                                        <th>AR ACCOUNT</th>
                                        <th>CUSTOMER NAME</th>
                                        <th>TOTAL AMOUNT DUE</th>
                                        <th>PAYMENT AMOUNT</th>
                                        <th>DISC & CRED APPLIED</th>
                                        <th>PAYMENT TYPE</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Your purchase order data will go here -->
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <?php include __DIR__ . ('../../includes/footer.php'); ?>
</div>
<script>
    // Function to format currency with commas
    function formatCurrency(amount) {
        // Convert amount to number
        var num = parseFloat(amount);
        // Check if the number is NaN
        if (isNaN(num)) {
            return amount; // Return the original value if it's not a valid number
        }
        // Use toLocaleString() to format the number with commas
        return num.toLocaleString('en-PH', {
            style: 'currency',
            currency: 'PHP'
        });
    }

    // Function to fetch and populate purchase order data
    function populateReceivePaymentsTable() {
        $.ajax({
            type: "GET",
            url: "modules/customer_center/get_receive_payments.php",
            success: function(response) {
                var receivePayments = JSON.parse(response);

                // Clear existing table rows
                $("#receivePaymentsTable tbody").empty();

                // Define an object to store merged rows by RefNo
                var mergedRows = {};

                receivePayments.forEach(function(payments) {
                    // Assuming payments.receivedDate is in the format yyyy-mm-dd
                    var receivedDate = new Date(payments.receivedDate);

                    // Get the individual components of the date
                    var month = receivedDate.getMonth() + 1; // Months are zero-based, so add 1
                    var day = receivedDate.getDate();
                    var year = receivedDate.getFullYear();

                    // Format the date as 00-00-00 m/y/d
                    var formattedDate = (month < 10 ? '0' : '') + month + '-' + (day < 10 ? '0' : '') + day + '-' + year;

                    // Format currency with commas
                    var totalAmountDueFormatted = formatCurrency(payments.totalAmountDue);
                    var paymentAmount = parseFloat(payments.payment_amount);
                    var discCredapplied = parseFloat(payments.discCredapplied);
                    var paymentAmountFormatted = paymentAmount === 0 ? '0.00' : formatCurrency(paymentAmount);
                    var discCredappliedFormatted = discCredapplied === 0 ? '0.00' : formatCurrency(discCredapplied);

                    // Check if a row with the same RefNo already exists
                    if (mergedRows.hasOwnProperty(payments.RefNo)) {
                        // Append invoiceNo to the existing row's invoiceNo
                        mergedRows[payments.RefNo].invoiceNo += ',' + payments.invoiceNo;
                        // Update payment amount
                        mergedRows[payments.RefNo].paymentAmount = paymentAmount;
                        // Update discCredapplied
                        mergedRows[payments.RefNo].discCredapplied = discCredapplied;
                        // Update totalAmountDue
                        mergedRows[payments.RefNo].totalAmountDue += parseFloat(payments.totalAmountDue); // Accumulate totalAmountDue
                    } else {
                        // Create a new merged row object
                        mergedRows[payments.RefNo] = {
                            RefNo: payments.RefNo,
                            invoiceNo: payments.invoiceNo,
                            receivedDate: formattedDate,
                            ar_account: payments.ar_account,
                            customerName: payments.customerName,
                            totalAmountDue: parseFloat(payments.totalAmountDue), // Convert to number
                            paymentAmount: paymentAmount,
                            discCredapplied: discCredapplied,
                            paymentType: payments.paymentType
                        };
                    }
                });

                // Populate the table with merged rows
                Object.values(mergedRows).forEach(function(rowData) {
                    var paymentAmountFormatted = rowData.paymentAmount === 0 ? '0.00' : formatCurrency(rowData.paymentAmount);
                    var discCredappliedFormatted = rowData.discCredapplied === 0 ? '0.00' : formatCurrency(rowData.discCredapplied);
                    var totalAmountDueFormatted = formatCurrency(rowData.totalAmountDue); // Format the totalAmountDue
                    var row = `<tr class='revRow' data-rev-id='${rowData.RefNo}'>
                        <td>${rowData.RefNo}</td>
                        <td>${rowData.invoiceNo}</td>
                        <td>${rowData.receivedDate}</td>
                        <td>${rowData.ar_account}</td>
                        <td>${rowData.customerName}</td>
                        <td><strong>${totalAmountDueFormatted}</strong></td>
                        <td><strong>${paymentAmountFormatted}</strong></td>
                        <td><strong>${discCredappliedFormatted}</strong></td>
                        <td>${rowData.paymentType}</td>
                    </tr>`;
                    $("#receivePaymentsTable tbody").append(row);
                });

                // Initialize DataTables only if it's not already initialized
                if (!$.fn.DataTable.isDataTable("#receivePaymentsTable")) {
                    $('#receivePaymentsTable').DataTable({
                        // DataTable options
                        "order": [
                            [1, "desc"]
                        ]
                    });
                }
            },
            error: function() {
                console.error("Error fetching purchase order data.");
            }
        });
    }

    // Initial population when the page loads
    $(document).ready(function() {
        populateReceivePaymentsTable();
    });
</script>

<script>
    $(document).on('click', '.revRow', function() {
        var RefNo = $(this).data('rev-id');
        window.location.href = 'view_receive_payment?RefNo=' + RefNo;
    });
</script>