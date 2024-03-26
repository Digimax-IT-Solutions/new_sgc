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
        border-bottom: 1px solid rgb(0, 149, 77);

    }

    #receivePaymentsTable tbody tr:hover {

        color: white;
        background-color: rgb(0, 149, 77);
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
    // Variable to check if DataTables is already initialized
    var dataTableInitialized = false;
    // Function to fetch and populate purchase order data
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

    function populateReceivePaymentsTable() {
        $.ajax({
            type: "GET",
            url: "modules/customer_center/get_receive_payments.php",
            success: function(response) {
                var receivePayments = JSON.parse(response);

                // Clear existing table rows
                $("#receivePaymentsTable tbody").empty();

                receivePayments.forEach(function(payments) {
                    // Format currency with commas
                    var totalAmountDueFormatted = formatCurrency(payments.totalAmountDue);
                    var paymentAmountFormatted = formatCurrency(payments.payment_amount);
                    var discCredappliedFormatted = formatCurrency(payments.discCredapplied);

                    var row = `<tr>
                    <td>${payments.RefNo}</td>
                    <td>${payments.invoiceNo}</td>
                    <td>${payments.receivedDate}</td>
                    <td>${payments.ar_account}</td>
                    <td>${payments.customerName}</td>
                    <td><strong>${totalAmountDueFormatted}</strong></td>
                    <td><strong>${paymentAmountFormatted}</strong></td>
                    <td><strong>${discCredappliedFormatted}</strong></td>
                    <td>${payments.paymentType}</td>
                </tr>`;

                    $("#receivePaymentsTable tbody").append(row);
                });

                // Initialize DataTables only if it's not already initialized
                if (!$.fn.DataTable.isDataTable("#receivePaymentsTable")) {
                    $('#receivePaymentsTable').DataTable({
                        // DataTable options
                    });
                }
            },
            error: function() {
                console.error("Error fetching purchase order data.");
            }
        });
    }


    // Attach click event to table rows
    // $("#purchaseOrderTable tbody").on("click", "tr", function() {
    //     // Get the Purchase Order ID (poID) from the first cell of the clicked row
    //     var poID = $(this).find("td:first").text();
    //     // Redirect to the view_purchase_order.php with the poID parameter
    //     window.location.href = "view_purchase_order?poID=" + poID;
    // });


    // Initial population when the page loads
    $(document).ready(function() {
        populateReceivePaymentsTable();
    });
</script>