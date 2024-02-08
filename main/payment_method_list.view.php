<?php include __DIR__ . ('/includes/header.php'); ?>
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
#paymentMethodsTable {
    border-collapse: collapse;
    width: 100%;
}

#paymentMethodsTable th,
#paymentMethodsTable td {
    padding: 2px;
    /* Adjust the padding as needed */
}
#paymentMethodsTable tbody tr:hover {
    color: white;
    background-color: rgb(0, 149, 77); /* Set your desired background color here */
}
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Payment Methods List</h1>
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
                                        <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Master List</li>
                                        <li class="breadcrumb-item active">Payment Methods List</li>
                                    </ol>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <button class="btn btn-success" data-toggle="modal"
                                            data-target="#addPaymentMethodModal"
                                            style="background-color: rgb(0, 149, 77); color: white;">
                                            Add New Payment
                                        </button>
                                    </ol>
                                </div><!-- /.col -->
                            </div><!-- /.row -->

                            <br><br>
                            <table id="paymentMethodsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>CODE</th>
                                        <th>PAYMENT METHOD</th>
                                        <th>DESCRIPTION</th>
                                        <th>STATUS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Your terms data will go here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Add Location Modal -->
    <div class="modal" id="addPaymentMethodModal" tabindex="-1" role="dialog"
        aria-labelledby="addPaymentMethodModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                    <h5 class="modal-title" id="addPaymentMethodModalLabel"><b>ADD NEW TERM</b></h5>
                    <button type="buttonsaveTermButton" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background-color: rgb(0,149,77); color: white;">
                    <form id="addPaymentMethodForm"
                        style="background-color: rgb(0,149,77); padding: 20px; color: white; border-radius: 10px;">
                        <div class="form-group">
                            <label for="paymentCode">CODE</label>
                            <input type="text" class="form-control" id="paymentCode" name="paymentCode" required>
                        </div>

                        <div class="form-group">
                            <label for="paymentName">PAYMENT TYPE</label>
                            <input type="text" class="form-control" id="paymentName" name="paymentName" required>
                        </div>
                        <div class="form-group">
                            <label for="paymentDescription">DESCRPTION</label>
                            <input type="text" class="form-control" id="paymentDescription" name="paymentDescription"
                                required>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="activeStatus" name="activeStatus"
                                checked>
                            <label class="form-check-label" for="activeStatus">Active</label>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" id="savePaymentMethodButton">Save
                            Category</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- End Add Modal -->

    <!-- Edit Location Modal -->
    <div class="modal" id="editPaymentMethodModal" tabindex="-1" role="dialog"
        aria-labelledby="editPaymentMethodModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                    <h5 class="modal-title" id="editPaymentMethodModalLabel"><b>EDIT CATEGORY</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background-color: rgb(0,149,77); color: white;">
                    <form id="editPaymentMethodForm"
                        style="background-color: rgb(0,149,77); padding: 20px; color: white; border-radius: 10px;">
                        <input type="text" class="form-control" id="editPaymentMethodID" name="editPaymentMethodID"
                            hidden>
                        <div class="form-group">
                            <label for="editPaymentMethodCode">CATEGORY CODE</label>
                            <input type="text" class="form-control" id="editPaymentMethodCode"
                                name="editPaymentMethodCode" required>
                        </div>

                        <div class="form-group">
                            <label for="editPaymentMethodName">CATEGORY NAME</label>
                            <input type="text" class="form-control" id="editPaymentMethodName"
                                name="editPaymentMethodName" required>
                        </div>
                        <div class="form-group">
                            <label for="editPaymentMethodDescription">DESCRPTION</label>
                            <input type="text" class="form-control" id="editPaymentMethodDescription"
                                name="editPaymentMethodDescription" required>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="editActiveStatus"
                                name="editActiveStatus" checked>
                            <label class="form-check-label" for="editActiveStatus">Active</label>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" id="saveEditPaymentMethodButton">Save
                            Changes</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <?php include('includes/footer.php'); ?>
</div>

<!-- Your existing JavaScript code for handling the table and other functionalities -->

<script>
$(document).ready(function() {
    // ... Your existing code ...

    // Save Location functionality
    $("#savePaymentMethodButton").click(function() {
        var formData = $("#addPaymentMethodForm").serialize();

        $.ajax({
            type: "POST",
            url: "modules/payment_method/save_payment_method.php", // Adjust the URL to the server-side script
            data: formData,
            success: function(response) {
                if (response === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'New Payment Added!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        // Reload the browser after SweetAlert2 is closed
                        location.reload();
                    });
                    // Update the table after successfully saving the location
                    populatePaymentMethodTable();
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
    // Set the flag to indicate DataTables is now initialized
    dataTableInitialized = false;
    // Function to fetch and populate location data
    function populatePaymentMethodTable() {
        $.ajax({
            type: "GET",
            url: "modules/payment_method/get_payment_method.php", // Adjust the URL to the server-side script
            success: function(response) {
                var payment_methods = JSON.parse(response);

                // Clear existing table rows
                $("#paymentMethodsTable tbody").empty();

                payment_methods.forEach(function(payment_method) {
                    // Use a class based on the active_status value
                    var statusClass = payment_method.active_status ? 'active' : 'inactive';
                    var row = `<tr>
                            <td>${payment_method.payment_code}</td>
                            <td>${payment_method.payment_name}</td>
                            <td>${payment_method.payment_description}</td>
                            <td class="${statusClass}">${payment_method.active_status ? 'Active' : 'Inactive'}</td>
                            <td>
                            <button type="button" class="btn btn-primary btn-sm editPaymentMethodButton" style="background-color: rgb(0, 149, 77); color: white; border: 1px rgb(0, 149, 77);" data-id="${payment_method.payment_id}">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm deletePaymentMethodButton" data-id="${payment_method.payment_id}">Delete</button>
                                </td>
                            </tr>`;
                    $("#paymentMethodsTable tbody").append(row);
                });

                // Initialize DataTables only if it's not already initialized
                if (!dataTableInitialized) {
                    $('#paymentMethodsTable').DataTable({
                        "paging": true,
                        "lengthChange": true,
                        "searching": true,
                        "info": true,
                        "autoWidth": false,
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
                    $('#paymentMethodsTable').DataTable().destroy();
                    $('#paymentMethodsTable').DataTable({
                        // Your DataTables options here
                    });
                }
            },
            error: function() {
                console.log("Error fetching category data.");
            }
        });
    }

    // Initial population when the page loads
    populatePaymentMethodTable();

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
// Edit category functionality
$("#paymentMethodsTable").on("click", ".editPaymentMethodButton", function() {
    var paymentMethodID = $(this).data("id");

    // Populate the edit modal with category data
    $.ajax({
        type: "GET",
        url: "modules/payment_method/get_payment_method_details.php", // Replace with your server-side script
        data: {
            paymentMethodID: paymentMethodID
        },
        success: function(response) {
            var paymentMethodDetails = JSON.parse(response);
            $("#editPaymentMethodID").val(paymentMethodDetails.payment_id);
            $("#editPaymentMethodCode").val(paymentMethodDetails.payment_code);
            $("#editPaymentMethodName").val(paymentMethodDetails.payment_name);
            $("#editPaymentMethodDescription").val(paymentMethodDetails.payment_description);
            $("#editActiveStatus").prop("checked", paymentMethodDetails.active_status);

            // Show the edit modal
            $("#editPaymentMethodModal").modal("show");
        },
        error: function() {
            console.log("Error fetching category details for edit.");
        }
    });
});
// Save Edit Category Changes functionality
$("#saveEditPaymentMethodButton").click(function() {
    var formData = $("#editPaymentMethodForm").serialize();

    $.ajax({
        type: "POST",
        url: "modules/payment_method/update_payment_method.php", // Replace with your server-side script
        data: formData,
        success: function(response) {
            if (response === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'Payment Method has been updated!',
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
$("#paymentMethodsTable").on("click", ".deletePaymentMethodButton", function() {
    var paymentMethodID = $(this).data("id");

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
                url: "modules/payment_method/delete_payment_method.php",
                data: {
                    deletePaymentMethodID: paymentMethodID
                },
                success: function(response) {
                    if (response === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Payment Method has been deleted!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            // Reload the browser after SweetAlert2 is closed
                            location.reload();
                        });
                        // Update the table after successfully deleting the location
                        populatepaymentMethodsTable();
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
        title: 'Error deleting location. Please try again.',
        text: errorMessage,
        showConfirmButton: false,
        timer: 5000 // Adjust the timer as needed
    });
}
</script>