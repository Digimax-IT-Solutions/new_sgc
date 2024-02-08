<?php include __DIR__ . ('/includes/header.php'); ?>

<style>
    .dropdown-toggle:hover,
    .dropdown-toggle:focus {
        background-color: rgb(0, 149, 77);
        color: white;
    }

    #purchaseOrderTable {
        border-collapse: collapse;
        width: 100%;
    }

    #purchaseOrderTable th,
    #purchaseOrderTable td {
        padding: 2px;
        /* Adjust the padding as needed */
    }

    #purchaseOrderTable tbody tr:hover {

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
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Purchase Order Transactions</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a style="color:maroon;" href="index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Purchase Order Transactions</li>
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
                                            <a class="dropdown-item" href="create_purchase_order" style="color: rgb(0, 149, 77); background-color: white;">New Purchase
                                                Order</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br><br>
                            <table id="purchaseOrderTable" class="table table-hover table-bordered table-striped">
                                <thead>

                                    <tr>

                                        <th>#</th>
                                        <th>PO NO</th>
                                        <th>VENDOR NAME</th>
                                        
                                        <th>DATE ORDER</th>
                                        <th>DATE OF DELIVERY</th>
                                        <th>AMOUNT</th>
                                        <th>MEMO</th>
                                        <th>STATUS</th>
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

    <?php include('includes/footer.php'); ?>

</div>

<script>
    // Variable to check if DataTables is already initialized
    var dataTableInitialized = false;
    // Function to fetch and populate purchase order data
    // Function to fetch and populate purchase order data
    // Function to fetch and populate purchase order data
    function populatePurchaseOrderTable() {
        $.ajax({
            type: "GET",
            url: "modules/vendor_center/purchase_order/get_purchase_order.php",
            success: function(response) {
                var purchaseOrders = JSON.parse(response);

                // Clear existing table rows
                $("#purchaseOrderTable tbody").empty();

                purchaseOrders.forEach(function(order) {
                    var statusColorClass = getStatusColorClass(order
                        .poStatus); // New function to get color class based on status

                    // Format the totalAmountDue as Philippine currency
                    var formattedAmount = new Intl.NumberFormat('en-PH', {
                        style: 'currency',
                        currency: 'PHP'
                    }).format(order.totalAmountDue);


                    var row = `<tr>
                    <td>${order.poID}</td>
                    <td>${order.poNo}</td>
                    <td>${order.vendor}</td>
                    <td>${order.poDate}</td>
                    <td>${order.poDueDate}</td>
         
                    <td>${order.memo}</td>
                    <td><strong>${formattedAmount}</strong></td>
                    <td><span class="badge ${statusColorClass}">${order.poStatus}</span></td>
                </tr>`;

                    $("#purchaseOrderTable tbody").append(row);
                });

                // Initialize DataTables only if it's not already initialized
                if (!$.fn.DataTable.isDataTable("#purchaseOrderTable")) {
                    $('#purchaseOrderTable').DataTable({
                        // DataTable options
                    });
                }

                // Attach click event to "View Details" buttons
                $(".viewDetailsButton").click(function() {
                    var purchaseId = $(this).data("id");
                    onViewDetailsClick(purchaseId);
                });
            },
            error: function() {
                console.error("Error fetching purchase order data.");
            }
        });
    }

    // Function to get status color class
    function getStatusColorClass(status) {
        switch (status.toLowerCase()) {
            case 'received':
                return 'bg-success'; // AdminLTE class for green background
            case 'waiting for delivery':
                return 'bg-danger'; // AdminLTE class for red background
            default:
                return 'bg-secondary'; // AdminLTE class for default background
        }
    }

    // ... Rest of your script ...


    // ... Rest of your script ...


    // Attach click event to table rows
    $("#purchaseOrderTable tbody").on("click", "tr", function() {
        // Get the Purchase Order ID (poID) from the first cell of the clicked row
        var poID = $(this).find("td:first").text();
        // Redirect to the view_purchase_order.php with the poID parameter
        window.location.href = "view_purchase_order?poID=" + poID;
    });


    // Initial population when the page loads
    $(document).ready(function() {
        populatePurchaseOrderTable();


    });
</script>