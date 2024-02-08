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

    #enterBillsTable {
        border-collapse: collapse;
        width: 100%;
    }

    #enterBillsTable th,
    #enterBillsTable td {
        padding: 2px;
        /* Adjust the padding as needed */
    }

    #enterBillsTable tbody tr:hover {

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
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Enter Bills</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item">Vendor Center</li>
                        <li class="breadcrumb-item active">Enter Bills</li>
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
                                            Enter Bills
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="newTransactionDropdown">
                                            <a class="dropdown-item" href="bill" style="color: rgb(0, 149, 77); background-color: white;">Bill</a>
                                            <a class="dropdown-item" href="credit" style="color: rgb(0, 149, 77); background-color: white;">Credit</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br><br>
                            <table id="enterBillsTable" class="table table-hover table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>REF #</th>
                                        <th>ACCOUNT</th>
                                        <th>VENDOR</th>
                                        <th>ADDRESS</th>
                                   
                                        <th>BILL DATE</th>
                                        <th>BILL DUE DATE</th>
                                        
                                        <th>MEMO</th>
                                        <th>TOTAL AMOUNT</th>
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
    function populateWriteCheckTable() {
        $.ajax({
            type: "GET",
            url: "modules/vendor_center/enter_bills/get_enter_bills.php",
            success: function(response) {
                var purchaseOrders = JSON.parse(response);

                // Clear existing table rows
                $("#enterBillsTable tbody").empty();

                purchaseOrders.forEach(function(order) {
          // Format the totalAmountDue as Philippine currency
          var formattedAmount = new Intl.NumberFormat('en-PH', {
                            style: 'currency',
                            currency: 'PHP'
                        }).format(order.total_amount);
                    var row = `<tr>
                    <td hidden>${order.bill_id}</td>
                    <td>${order.reference_no}</td>
                    <td>${order.bankAccountName}</td>
                    <td>${order.vendor}</td>
                    <td>${order.address}</td>
                    <td>${order.bill_date}</td>
                    <td>${order.bill_due}</td>
                    
                    <td>${order.memo}</td>
                    <td><strong>${formattedAmount}</strong></td>
                    </tr>`;

                    $("#enterBillsTable tbody").append(row);
                });

                // Initialize DataTables only if it's not already initialized
                if (!$.fn.DataTable.isDataTable("#enterBillsTable")) {
                    $('#enterBillsTable').DataTable({
                        // DataTable options
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
        populateWriteCheckTable();

    });

    $("#enterBillsTable tbody").on("click", "tr", function() {
        // Get the Purchase Order ID (bill_id) from the first cell of the clicked row
        var bill_id = $(this).find("td:first").text();
        // Redirect to the view_purchase_order.php with the bill_id parameter
        window.location.href = "bill_details_view?bill_id=" + bill_id;
    });
</script>