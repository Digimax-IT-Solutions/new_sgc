<?php include('includes/header.php'); 
include('connect.php');
?>

<style>
#customerTable {
    border-collapse: collapse;
    width: 100%;
}

#customerTable th,
#customerTable td {
    padding: 2px;
    /* Adjust the padding as needed */
}
#customerTable tbody tr:hover {
    color: white;
    background-color: maroon; /* Set your desired background color here */
}
</style>
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Customer List</h1>
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
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-left">
                                        <li class="breadcrumb-item"><a style="color:maroon;" href="dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Master List</li>
                                        <li class="breadcrumb-item active">Customer List</li>
                                    </ol>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <button class="btn btn-success" data-toggle="modal"
                                            data-target="#addCustomerModal"
                                            style="background-color: rgb(0, 149, 77); color: white;">
                                            Create Customer
                                        </button>
                                    </ol>
                                </div><!-- /.col -->
                            </div><!-- /.row -->


                            <br><br>
                            <table id="customerTable" class="table table-hover table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>CODE</th>
                                        <th>COMPANY NAME</th>
                                        <th>CONTACT NO.</th>
                                        <th>TIN NO.</th>
                                        <th>EMAIL</th>
                                        <th>TERMS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Your customer data will go here -->
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
<!-- Add Customer Modal -->
<div class="modal" id="addCustomerModal" tabindex="-1" role="dialog" aria-labelledby="addCustomerModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                <h5 class="modal-title" id="addCustomerModalLabel"><b>ADD NEW CUSTOMER</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background-color: rgb(0,149,77);">
                <form id="addCustomerForm">
                    <div class="row" style="padding: 20px; color: white; border-radius: 5px;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="customerName">CUSTOMER NAME</label>
                                <input type="text" class="form-control" id="customerName" name="customerName"
                                    placeholder="Enter Customer Name" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="customerCode">CODE</label>
                                <input type="text" class="form-control" id="customerCode" name="customerCode"
                                    placeholder="Enter Customer Code" required>
                            </div>

                            <div class="form-group">
                            <label for="customerPaymentMethod">PAYMENT METHOD</label>
                                        <select class="form-control" id="customerPaymentMethod" name="customerPaymentMethod" required>
                                            <option value="">Select Payment Method</option>
                                            <?php
                                            $query = "SELECT payment_id, payment_name FROM payment_methods";
                                            $result = $db->query($query);

                                            if ($result) {
                                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<option value='{$row['payment_name']}'>{$row['payment_name']}</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="customerBillingAddress">BILLING ADDRESS</label>
                                <input type="text" class="form-control" id="customerBillingAddress"
                                    name="customerBillingAddress" placeholder="Enter Billing Address" required>
                            </div>
                            <div class="form-group">
                                <label for="customerShippingAddress">SHIPPING ADDRESS</label>
                                <input type="text" class="form-control" id="customerShippingAddress"
                                    name="customerShippingAddress" placeholder="Enter Shipping Address" required>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="padding: 20px; color: white; border-radius: 5px;">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="customerTin">TIN #</label>
                                <input type="text" class="form-control" id="customerTin" name="customerTin"
                                    placeholder="Enter TIN #" required>
                            </div>
                            <div class="form-group">
                                <label for="contactNumber">CONTACT #</label>
                                <input type="text" class="form-control" id="contactNumber" placeholder="Enter Contact #"
                                    name="contactNumber" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="customerDeliveryType">DELIVERY TYPE</label>
                                <input type="text" class="form-control" id="customerDeliveryType"
                                    name="customerDeliveryType" required>
                            </div>

                            <div class="form-group">
                            <label for="customerTerms">TERMS </label>
                                        <select class="form-control" id="customerTerms" name="customerTerms" required>
                                            <option value="">Select Term</option>
                                            <?php
                                            $query = "SELECT term_id, term_name, term_days_due FROM terms";
                                            $result = $db->query($query);

                                            if ($result) {
                                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<option value='{$row['term_name']}' data-days-due='{$row['term_days_due']}'>{$row['term_name']}</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="customerEmail">EMAIL</label>
                                <input type="text" class="form-control" id="customerEmail" name="customerEmail"
                                    placeholder="Enter Email" required>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="saveCustomerButton">Save Customer</button>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- Edit Customer Modal -->
<div class="modal" id="editCustomerModal" tabindex="-1" role="dialog" aria-labelledby="editCustomerModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                <h5 class="modal-title" id="editCustomerModalLabel"><b>EDIT CUSTOMER</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background-color: rgb(0,149,77);">
                <form id="editCustomerForm">
                    <input type="text" class="form-control" id="editCustomerID" name="editCustomerID" hidden>
                    <div class="row" style="padding: 20px; color: white; border-radius: 5px;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="editCustomerName">CUSTOMER NAME</label>
                                <input type="text" class="form-control" id="editCustomerName" name="editCustomerName"
                                    placeholder="Enter Customer Name" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="editCustomerCode">CODE</label>
                                <input type="text" class="form-control" id="editCustomerCode" name="editCustomerCode"
                                    placeholder="Enter Customer Code" required>
                            </div>

                            <div class="form-group">
                                <label for="editCustomerPaymentMethod">PAYMENT METHOD</label>
                                <select class="form-control" id="editCustomerPaymentMethod" name="editCustomerPaymentMethod" required>
                                            <option value="">Select Payment Method</option>
                                            <?php
                                            $query = "SELECT payment_id, payment_name FROM payment_methods";
                                            $result = $db->query($query);

                                            if ($result) {
                                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<option value='{$row['payment_name']}'>{$row['payment_name']}</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="editCustomerBillingAddress">BILLING ADDRESS</label>
                                <input type="text" class="form-control" id="editCustomerBillingAddress"
                                    name="editCustomerBillingAddress" placeholder="Enter Billing Address" required>
                            </div>
                            <div class="form-group">
                                <label for="editCustomerShippingAddress">SHIPPING ADDRESS</label>
                                <input type="text" class="form-control" id="editCustomerShippingAddress"
                                    name="editCustomerShippingAddress" placeholder="Enter Shipping Address" required>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="padding: 20px; color: white; border-radius: 5px;">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="editCustomerTin">TIN #</label>
                                <input type="text" class="form-control" id="editCustomerTin" name="editCustomerTin"
                                    placeholder="Enter TIN #" required>
                            </div>
                            <div class="form-group">
                                <label for="editContactNumber">CONTACT #</label>
                                <input type="text" class="form-control" id="editContactNumber"
                                    placeholder="Enter Contact #" name="editContactNumber" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="editCustomerDeliveryType">DELIVERY TYPE</label>
                                <input type="text" class="form-control" id="editCustomerDeliveryType"
                                    name="editCustomerDeliveryType" required>
                            </div>

                            <div class="form-group">
                            <label for="editCustomerTerms">CUSTOMER TERMS </label>
                                        <select class="form-control" id="editCustomerTerms" name="editCustomerTerms" required>
                                            <option value="">Select Term</option>
                                            <?php
                                            $query = "SELECT term_id, term_name, term_days_due FROM terms";
                                            $result = $db->query($query);

                                            if ($result) {
                                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<option value='{$row['term_name']}' data-days-due='{$row['term_days_due']}'>{$row['term_name']}</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="editCustomerEmail">EMAIL</label>
                                <input type="text" class="form-control" id="editCustomerEmail" name="editCustomerEmail"
                                    placeholder="Enter Email" required>
                            </div>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="saveEditCustomerButton">Save Changes</button>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $("#saveCustomerButton").click(function() {
        // Get form data
        var formData = $("#addCustomerForm").serialize();

        // AJAX request to store data
        $.ajax({
            type: "POST",
            url: "modules/customers/save_customer.php",
            data: formData,
            success: function(response) {
                // Use SweetAlert2 for displaying success or error message
                if (response === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Customer saved successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        // Reload the browser after SweetAlert2 is closed
                        location.reload();
                    });
                    $("#addCustomerModal").modal("hide");

                    // Update the table after successfully saving the customer
                    populateCustomerTable();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error saving customer. Please try again.',
                        text: response, // Display the MySQL error message
                        showConfirmButton: false,
                        timer: 5000 // Adjust the timer as needed
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error saving customer. Please try again.',
                    text: response, // Display the MySQL error message
                    showConfirmButton: false,
                    timer: 5000 // Adjust the timer as needed
                });
            }
        });
    });
    // Variable to check if DataTables is already initialized
    var dataTableInitialized = false;

    // Function to fetch and populate customer data
    function populateCustomerTable() {
        $.ajax({
            type: "GET",
            url: "modules/customers/get_customers.php", // Adjust the URL to the server-side script
            processing: true,
            serverSide: true,
            success: function(response) {
                // Parse the JSON response
                var customers = JSON.parse(response);

                // Clear existing table rows
                $("#customerTable tbody").empty();

                // Populate the table with data
                customers.forEach(function(customer) {
                    var row = `<tr>
                    <td>${customer.customerCode || ''}</td>
                            <td>${customer.customerName || ''}</td>
                            <td>${customer.contactNumber || ''}</td>
                            <td>${customer.customerTin || ''}</td>
                            <td>${customer.customerEmail || ''}</td>
                            <td>${customer.customerTerms || ''}</td>
                            <td>
                            <button type="button" class="btn btn-primary btn-sm editCustomerButton" style="background-color: rgb(0, 149, 77); color: white; border: 1px rgb(0, 149, 77);" data-id="${customer.customerID}">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm deleteCustomerButton" data-id="${customer.customerID}">Delete</button></td>
                            </td>
                        </tr>`;
                    $("#customerTable tbody").append(row);
                });

                // Initialize DataTables only if it's not already initialized
                if (!dataTableInitialized) {
                    $('#customerTable').DataTable({
                        "paging": true,
                        "processing": true,
                        "lengthChange": true,
                        "searching": true,
                        "info": true,
                        "autoWidth": true,
                        "lengthMenu": [10, 25, 50, 100],
                        "ordering": false, // Disable sorting for all columns
                        "dom": 'lBfrtip',
                        "buttons": [{
                                extend: 'copy',
                                exportOptions: {
                                    columns: ':not(:last-child)' // Exclude the last column (ACTION)
                                }
                            },
                            {
                                extend: 'csv',
                                exportOptions: {
                                    columns: ':not(:last-child)' // Exclude the last column (ACTION)
                                }
                            },
                            {
                                extend: 'excel',
                                exportOptions: {
                                    columns: ':not(:last-child)' // Exclude the last column (ACTION)
                                }
                            },
                            {
                                extend: 'pdf',
                                exportOptions: {
                                    columns: ':not(:last-child)' // Exclude the last column (ACTION)
                                }
                            },
                            {
                                extend: 'print',
                                exportOptions: {
                                    columns: ':not(:last-child)' // Exclude the last column (ACTION)
                                }
                            },
                        ],
                        "oLanguage": {
                            "sSearch": "Search:",
                            "sLengthMenu": "Show _MENU_ entries",
                            "sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
                            "sInfoEmpty": "Showing 0 to 0 of 0 entries",
                            "sInfoFiltered": "(filtered from _MAX_ total entries)",
                            "oPaginate": {
                                "sFirst": "First",
                                "sLast": "Last",
                                "sNext": "Next",
                                "sPrevious": "Previous"
                            }
                        }
                    });

                    // Set the flag to indicate DataTables is now initialized
                    dataTableInitialized = true;
                } else {
                    // If DataTables is already initialized, destroy and recreate it
                    $('#customerTable').DataTable().destroy();
                    $('#customerTable').DataTable({
                        // Your DataTables options here
                    });
                }
            },
            error: function() {
                console.log("Error fetching data.");
            }
        });
    }
    // Initial population when the page loads
    populateCustomerTable();

    // Function to display an error using SweetAlert2
    function displayError(errorMessage) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: errorMessage,
            showConfirmButton: false,
            timer: 5000
        });
    }
});
// Edit Location functionality
$("#customerTable").on("click", ".editCustomerButton", function() {
    var customerID = $(this).data("id");

    // Populate the edit modal with location data
    $.ajax({
        type: "GET",
        url: "modules/customers/get_customer_details.php", // Replace with your server-side script
        data: {
            customerID: customerID
        },
        success: function(response) {
            var customerDetails = JSON.parse(response);

            // Populate the edit modal with location details
            $("#editCustomerID").val(customerDetails.customerID);
            $("#editCustomerName").val(customerDetails.customerName);
            $("#editCustomerCode").val(customerDetails.customerCode);
            $("#editCustomerPaymentMethod").val(customerDetails.customerPaymentMethod);
            $("#editCustomerBillingAddress").val(customerDetails.customerBillingAddress);
            $("#editCustomerShippingAddress").val(customerDetails.customerShippingAddress);
            $("#editCustomerTin").val(customerDetails.customerTin);
            $("#editContactNumber").val(customerDetails.contactNumber);
            $("#editCustomerDeliveryType").val(customerDetails.customerDeliveryType);
            $("#editCustomerTerms").val(customerDetails.customerTerms);
            $("#editCustomerEmail").val(customerDetails.customerEmail);
            // Show the edit modal
            $("#editCustomerModal").modal("show");
        },
        error: function() {
            console.log("Error fetching Customer details for edit.");
        }
    });
});
// Save Edit Location Changes functionality
$("#saveEditCustomerButton").click(function() {
    // Retrieve the customerID from the form data
    var customerID = $("#editCustomerForm").data("customerID");
    // Add customerID to the form data
    var formData = $("#editCustomerForm").serialize();

    $.ajax({
        type: "POST",
        url: "modules/customers/update_customer.php", // Replace with your server-side script
        data: formData,
        success: function(response) {
            if (response === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'Customer updated successfully!',
                    showConfirmButton: false,
                    timer: 1500
                }).then(function() {
                    // Reload the browser after SweetAlert2 is closed
                    location.reload();
                });
            } else {
                // Display the error message in SweetAlert2
                displayError(response);
            }
        },
        error: function() {
            displayError("An unexpected error occurred. Please try again.");
        }
    });
});
// Delete Location functionality
$("#customerTable").on("click", ".deleteCustomerButton", function() {
    var customerID = $(this).data("id");

    // Display a confirmation dialog before deleting
    Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Delete the location via AJAX
            $.ajax({
                type: "POST",
                url: "modules/customers/delete_customer.php",
                data: {
                    deleteCustomerID: customerID
                },
                success: function(response) {
                    if (response === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Customer deleted successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            // Reload the browser after SweetAlert2 is closed
                            location.reload();
                        });
                        // Update the table after successfully deleting the location
                        populateCustomerTable();
                    } else {
                        // Display the error message in SweetAlert2
                        displayError(response);
                    }
                },
                error: function() {
                    displayError("An unexpected error occurred. Please try again.");
                }
            });
        }
    });
});
// Function to display an error using SweetAlert2
function displayError(errorMessage) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: errorMessage,
        showConfirmButton: false,
        timer: 5000
    });
}
</script>