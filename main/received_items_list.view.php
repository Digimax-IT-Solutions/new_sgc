<?php include __DIR__ . ('/includes/header.php'); ?>

<style>
    .dropdown-toggle:hover,
    .dropdown-toggle:focus {
        background-color: rgb(0, 149, 77);
        color: white;
    }

    #receiveItemsTable {
        border-collapse: collapse;
        width: 100%;
    }

    #receiveItemsTable th,
    #receiveItemsTable td {
        font-size: 15px;
        padding: 2px;
        /* Adjust the padding as needed */
    }

    #receiveItemsTable tbody tr:hover {

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
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Receive Items List</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
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
                                            Receive Items
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="newTransactionDropdown">
                                            <a class="dropdown-item" href="received_items" style="color: rgb(0, 149, 77); background-color: white;">Receive Items With Bill</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br><br>
                            <center>
                            <table id="receiveItemsTable" class="table table-hover table-bordered table-striped">
                                <thead>

                                    <tr>

                                        <th>REFERENCE NO</th>
                                        <th>VENDOR NAME</th>
                                        
                                        <th>ACCOUNT</th>
                                        <th>RECEIVE DATE</th>
                                        <th>MEMO</th>
                                        <th>AMOUNT</th>
                                        <th>STATUS</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Your purchase order data will go here -->
                                </tbody>
                            </table>
                            </center>
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
    var receiveItemsDataTableInitialized = false;
    // Function to fetch and populate receive items data
    function populateReceiveItemsTable() {
        $.ajax({
            type: "GET",
            url: "modules/vendor_center/receive_items/get_receive_items.php",
            success: function(response) {
                console.log("Raw response:", response); // Log the raw response

                try {
                    var result = JSON.parse(response);

                    // Check if the data is wrapped in a property (e.g., "data")
                    var receiveItems = Array.isArray(result.data) ? result.data : [];

                    // Clear existing table rows
                    $("#receiveItemsTable tbody").empty();

                    // Iterate over the receive items and append rows
                    receiveItems.forEach(function(item) {
                        var statusColorClass = getStatusColorClass(item.status); // New function to get color class based on status
                        // Adjust this part based on your actual column names
                        // Format the totalAmountDue as Philippine currency
                        var formattedAmount = new Intl.NumberFormat('en-PH', {
                            style: 'currency',
                            currency: 'PHP'
                        }).format(item.totalAmount);
                        var row = `<tr>
                        <td>${item.receiveID}</td>
                        <td>${item.vendor}</td>
            
                    
                        <td>${item.account}</td>
                        <td>${item.receiveDate}</td>
                        <td>${item.memo}</td>
                        <td><strong>${formattedAmount}</strong></td>
                        <td><span class="badge ${statusColorClass}">${item.status}</span></td>
                    </tr>`;

                        $("#receiveItemsTable tbody").append(row);
                    });

                    // Initialize DataTables only if it's not already initialized
                    if (!receiveItemsDataTableInitialized) {
                        $('#receiveItemsTable').DataTable({
                            // DataTable options
                        });
                        receiveItemsDataTableInitialized = true; // Set the flag to true after initialization
                    }

                } catch (error) {
                    console.error("Error parsing JSON:", error);
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching receive items data:", error);
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


    // Initial population when the page loads
    $(document).ready(function() {
        // Call the function to populate the table
        populateReceiveItemsTable();
    });
</script>