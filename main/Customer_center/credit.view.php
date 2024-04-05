<?php include __DIR__ . ('../../includes/header.php'); ?>
<?php include('connect.php'); ?>

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
        background-color: rgba(128,21,20,0.8) !important;
        /* Change the background color on hover */
        color: white !important;
        /* Change the text color on hover */
    }

    #creditTable {
        border-collapse: collapse;
        width: 100%;
        table-layout: fixed;
        text-align: right;
    }

    #creditTable td:nth-child(-n+3) {
        text-align: left;
    }

    #creditTable th {
        font-size: 10px;
        white-space: nowrap;
        border: none;
    }

    #creditTable td {
        font-size: 13px;
        padding: 5px;
        overflow: hidden;
        /* Hides any overflowing content */
        border: none;
        border-bottom: 1px solid red;
    }

    #creditTable tbody tr:hover {
        color: white;
        background-color: rgba(128,21,20,0.8);
        /* Set your desired background color here */
    }
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Create Credit/Refunds Memo</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">Credit/Refunds</li>
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
                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="newTransactionDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: rgb(128,21,20); color: white;">
                                            New Transactions
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="newTransactionDropdown">
                                            <a class="dropdown-item" href="create_credit" style="color: rgb(128,21,20); background-color: white;">Create Credit</a>
                                            <a class="dropdown-item" href="#" style="color: rgb(128,21,20); background-color: white;">Refund</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br><br>
                            <table id="creditTable" class="table table-hover table-bordered">
                                <thead style="background-color: gray; color: white;">
                                    <tr>
                                        <td>Credit No</td>
                                        <td>Po No</td>
                                        <td>Customer name</td>
                                        <td>Date Created</td>
                                        <td>Credit Amount</td>
                                        <td>Credit Balance</td>
                                        <td>Credit Status</td>
                                    </tr>
                                </thead>
                                <!-- Your sales data will go here -->
                                <tbody>

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
    // Fetch and populate purchase order data
    function populatecreditTable() {
        $.ajax({
            type: "GET",
            url: "modules/credit/fetch_all_credits.php",
            success: function(response) {
                var credits = JSON.parse(response);

                // Clear existing table rows
                $("#creditTable tbody").empty();

                credits.forEach(function(credit) {
                    // Format credit amount and balance with comma separators
                    var creditAmountFormatted = '₱' + parseFloat(credit.creditAmount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    var creditBalanceFormatted = '₱' + parseFloat(credit.creditBalance).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                    var poIDCellContent = credit.poID !== null ? credit.poID : '';
                    var statusBadgeClass = credit.status === 'active' ? 'badge-success' : 'badge-danger';
                    var statusBadgeText = credit.status === 'active' ? 'Active' : 'Inactive';

                    var invoiceDateParts = credit.creditDate.split('-'); // Assuming date is in "YYYY-MM-DD" format
                    var formattedInvoiceDate = `${invoiceDateParts[1]}-${invoiceDateParts[2]}-${invoiceDateParts[0]}`;
                    var row = `<tr class="creditRow" data-credit-id="${credit.creditID}">

                    <td>${credit.creditID}</td>
                    <td>${poIDCellContent}</td>
                    <td>${credit.customerName}</td>
                    <td>${formattedInvoiceDate}</td>
                    <td><strong>${creditAmountFormatted}</strong></td>
                    <td><strong>${creditBalanceFormatted}</strong></td>
                    <td><span class="badge ${statusBadgeClass}">${statusBadgeText}</span></td>
                </tr>`;

                    $("#creditTable tbody").append(row);
                });

                // Initialize DataTables only if it's not already initialized
                if (!$.fn.DataTable.isDataTable("#creditTable")) {
                    $('#creditTable').DataTable({
                        // DataTable options
                    });
                }
            },
            error: function() {
                console.error("Error fetching purchase order data.");
            }
        });
    }


    $(document).ready(function() {
        populatecreditTable();
    });
</script>
<script>
    $(document).on('click', '.creditRow', function() {
    var creditID = $(this).data('credit-id');
    window.location.href = 'view_credit?creditID=' + creditID;
});
</script>