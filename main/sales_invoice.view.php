<?php include __DIR__ . ('/includes/header.php'); ?>
<?php include('connect.php'); ?>

<style>
    .dropdown-toggle:hover,
    .dropdown-toggle:focus {
        background-color: rgb(0, 149, 77);
        color: white;
    }
</style>

<style>
    #salesTable {
        border-collapse: collapse;
        width: 100%;
        table-layout: fixed;
    }

    #salesTable th,
    #salesTable td {
        padding: 1px;
        width: 100px;
        border: 1px solid maroon;
        white-space: nowrap;
        overflow: hidden; /* Hides any overflowing content */
        text-overflow: ellipsis;
    }

    #salesTable tbody tr:hover {

        color: white;
        background-color: maroon;
        /* Set your desired background color here */
    }
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Sales Transactions</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a style="color:maroon;" href="dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">Sales Transactions</li>
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
                                            New Transactions
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="newTransactionDropdown">
                                            <a class="dropdown-item" href="create_invoice" style="color: rgb(0, 149, 77); background-color: white;">Invoice</a>
                                            <!-- <a class="dropdown-item" href="#" style="color: rgb(0, 149, 77); background-color: white;">Received
                                                Payment</a>
                                            <a class="dropdown-item" href="#" style="color: rgb(0, 149, 77); background-color: white;">Make
                                                Deposit</a> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br><br>
                            <table id="salesTable" class="table table-hover table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>INVOICE #</th>
                                        <th>CUSTOMER NAME</th>
                                        <th>DATE</th>
                                        <th>BILLING ADDRESS</th>
                                        <th>ACCOUNT</th>
                                        <th>STATUS</th>
                                        <th>AMOUNT</th>
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
    <?php include('includes/footer.php'); ?>
</div>


<script>
    var dataTableInitialized = false;

    function populateSalesTable() {
    $.ajax({
        type: "GET",
        url: "modules/invoice/get_sales_transactions.php", // Adjust the URL to the server-side script
        success: function(response) {
            var salesTransactions = JSON.parse(response);
            $("#salesTable tbody").empty();

            // Sort the salesTransactions array based on invoiceNo
            salesTransactions.sort((a, b) => a.invoiceNo.localeCompare(b.invoiceNo));

            salesTransactions.forEach(function(transaction) {
                var statusColorClass = getStatusColorClass(transaction.invoiceStatus); // New function to get color class based on status
                
                // Format the totalAmountDue as Philippine currency
                var formattedAmount = new Intl.NumberFormat('en-PH', {
                    style: 'currency',
                    currency: 'PHP'
                }).format(transaction.totalAmountDue);

                var row = `<tr class="salesRow" data-invoice-id="${transaction.invoiceID}">
                    <td>${transaction.invoiceNo}</td>
                    <td>${transaction.customer}</td>
                    <td>${transaction.invoiceDate}</td>
                    <td>${transaction.address}</td>
                    <td>${transaction.account}</td>
                    <td><span class="badge ${statusColorClass}">${transaction.invoiceStatus}</span></td>
                    <td style="text-align: right;"><strong>${formattedAmount}</strong></td>
                </tr>`;
                $("#salesTable tbody").append(row);
            });

            if (!dataTableInitialized) {
                $('#salesTable').DataTable({
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "info": true,
                    "autoWidth": true,
                    "lengthMenu": [10, 25, 50, 100],
                    "ordering": false,
                    "dom": 'lBfrtip',
                    "buttons": [],
                    "oLanguage": {
                        // ... (your language settings)
                    }
                });

                dataTableInitialized = true;
            } else {
                $('#salesTable').DataTable().destroy();
                $('#salesTable').DataTable({
                    // Your DataTables options here
                });
            }

            // Handle row click event
        },
        error: function() {
            console.log("Error fetching data.");
        }
    });
}

    // Function to get status color class
    function getStatusColorClass(status) {
        switch (status.toLowerCase()) {
            case 'paid':
                return 'bg-success'; // AdminLTE class for green background
            case 'unpaid':
                return 'bg-danger'; 
            case 'past due':
                return 'bg-danger';// AdminLTE class for red background
            default:
                return 'bg-secondary'; // AdminLTE class for default background
        }
    }


    // Initial population when the page loads
    populateSalesTable();
</script>



<!-- Add this script at the end of your HTML to handle the click event -->
<script>
    $(document).ready(function() {
        // Event listener for delete invoice button click
        $('.deleteInvoiceButton').on('click', function(e) {
            e.preventDefault(); // Prevent the default link behavior

            // Get the invoice ID from the data attribute
            var invoiceID = $(this).data('invoice-id');

            // Show confirmation dialog
            if (confirm('Are you sure you want to delete this invoice?')) {
                // Perform the deletion using AJAX
                $.ajax({
                    type: "GET",
                    url: "modules/invoice/delete_invoice.php?invoiceID=" + invoiceID,
                    success: function(response) {
                        // Reload the page after successful deletion
                        location.reload();
                    },
                    error: function() {
                        console.log("Error deleting invoice.");
                    }
                });
            }
        });
    });
</script>
<script>
    $(document).on('click', '.salesRow', function() {
    var invoiceID = $(this).data('invoice-id');
    window.location.href = 'view_invoice?invoiceID=' + invoiceID;
});
</script>