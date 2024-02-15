<?php include('includes/header.php'); ?>
<style>
#vendorTable {
    border-collapse: collapse;
    width: 100%;
}

#vendorTable th,
#vendorTable td {
    padding: 1px;
        width: 100px;
        border: 1px solid maroon;
    /* Adjust the padding as needed */
}
#vendorTable tbody tr:hover {
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
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Vendor List</h1>
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
                                        <li class="breadcrumb-item active">Vendor List</li>
                                    </ol>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <button class="btn btn-success" data-toggle="modal"
                                            data-target="#addVendorModal"
                                            style="background-color: rgb(0, 149, 77); color: white;">
                                            Create New Vendor
                                        </button>
                                    </ol>
                                </div><!-- /.col -->
                                <div class="col-sm-15">
                                        <ol class="breadcrumb float-sm-right">
                                            <form action="modules/vendors/import.php" method="post" enctype="multipart/form-data">
                                                <input type="file" name="file" id="file" accept=".xls,.xlsx">
                                                <button type="submit" name="import">Import </button>
                                            </form>
                                        </ol>
                                    </div>
                            </div><!-- /.row -->


                            <br><br>
                            <table id="vendorTable" class="table table-hover table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>CODE</th>
                                        <th>VENDOR NAME</th>
                                        <th>VENDOR ADDRESS</th>
                                        <th>VENDOR CONTACT NO #</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Your Vendor data will go here -->
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
<!-- Add Vendor Modal -->
<div class="modal" id="addVendorModal" tabindex="-1" role="dialog" aria-labelledby="addVendorModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                <h5 class="modal-title" id="addVendorModalLabel"><b>ADD NEW VENDOR</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background-color: rgb(0,149,77);">
                <form id="addVendorForm">
                    <div class="row"
                        style="background-color: rgb(0,149,77); padding: 20px; color: white; border-radius: 5px;">
                        <!-- Column 1 -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="vendorName">VENDOR NAME</label>
                                <input type="text" class="form-control" id="vendorName" name="vendorName"
                                    placeholder="Enter Vendor Name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vendorCode">VENDOR CODE</label>
                                <input type="text" class="form-control" id="vendorCode" name="vendorCode"
                                    placeholder="Enter Vendor Code" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="vendorAccountNumber">ACCOUNT NUMBER</label>
                                <input type="text" class="form-control" id="vendorAccountNumber"
                                    name="vendorAccountNumber" placeholder="Enter Account Number" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="vendorAddress">VENDOR ADDRESS</label>
                                <input type="text" class="form-control" id="vendorAddress" name="vendorAddress"
                                    placeholder="Enter Vendor Address" required>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="padding: 20px; color: white; border-radius: 5px;">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="vendorContactNumber">CONTACT #</label>
                                <input type="text" class="form-control" id="vendorContactNumber"
                                    name="vendorContactNumber" placeholder="Enter Contact #" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="vendorEmail">EMAIL</label>
                                <input type="text" class="form-control" id="vendorEmail" name="vendorEmail"
                                    placeholder="Enter Email" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="vendorTerms">TERMS</label>
                                <input type="text" class="form-control" id="vendorTerms" name="vendorTerms" required>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="saveVendorButton">Add Vendor</button>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- Edit vendor Modal -->
<div class="modal" id="editVendorModal" tabindex="-1" role="dialog" aria-labelledby="editVendorModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                <h5 class="modal-title" id="editVendorModalLabel"><b>EDIT VENDOR</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background-color: rgb(0,149,77);">
                <form id="editVendorForm">
                    <div class="row" style="padding: 20px; color: white; border-radius: 5px;">
                        <!-- Column 1 -->
                        <input type="text" class="form-control" id="editVendorID" name="editVendorID" hidden>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="editVendorName">VENDOR NAME</label>
                                <input type="text" class="form-control" id="editVendorName" name="editVendorName"
                                    placeholder="Enter Vendor Name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editVendorCode">VENDOR CODE</label>
                                <input type="text" class="form-control" id="editVendorCode" name="editVendorCode"
                                    placeholder="Enter Vendor Code" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editVendorAccountNumber">ACCOUNT NUMBER</label>
                                <input type="text" class="form-control" id="editVendorAccountNumber"
                                    name="editVendorAccountNumber" placeholder="Enter Account Number" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="editVendorAddress">VENDOR ADDRESS</label>
                                <input type="text" class="form-control" id="editVendorAddress" name="editVendorAddress"
                                    placeholder="Enter Vendor Address" required>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="padding: 20px; color: white; border-radius: 5px;">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="editVendorContactNumber">CONTACT #</label>
                                <input type="text" class="form-control" id="editVendorContactNumber"
                                    name="editVendorContactNumber" placeholder="Enter Contact #" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="editVendorEmail">EMAIL</label>
                                <input type="text" class="form-control" id="editVendorEmail" name="editVendorEmail"
                                    placeholder="Enter Email" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="editVendorTerms">TERMS</label>
                                <input type="text" class="form-control" id="editVendorTerms" name="editVendorTerms" required>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="saveEditVendorButton">Save Changes</button>
                </div>
            </div>

        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $("#saveVendorButton").click(function() {
        // Get form data
        var formData = $("#addVendorForm").serialize();

        // AJAX request to store data
        $.ajax({
            type: "POST",
            url: "modules/vendors/save_vendor.php",
            data: formData,
            success: function(response) {
                // Use SweetAlert2 for displaying success or error message
                if (response === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Vendor Saved Successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        // Reload the browser after SweetAlert2 is closed
                        location.reload();
                    });
                    $("#addVendorModal").modal("hide");

                    // Update the table after successfully saving the vendor
                    populatevendorTable();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error saving vendor. Please try again.',
                        text: response, // Display the MySQL error message
                        showConfirmButton: false,
                        timer: 5000 // Adjust the timer as needed
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error saving vendor. Please try again.',
                    text: response, // Display the MySQL error message
                    showConfirmButton: false,
                    timer: 5000 // Adjust the timer as needed
                });
            }
        });
    });
    // Variable to check if DataTables is already initialized
    var dataTableInitialized = false;

    // Function to fetch and populate vendor data
    function populatevendorTable() {
        $.ajax({
            type: "GET",
            url: "modules/vendors/get_vendors.php", // Adjust the URL to the server-side script
            success: function(response) {
                // Parse the JSON response
                var vendors = JSON.parse(response);

                // Clear existing table rows
                $("#vendorTable tbody").empty();

                // Populate the table with data
                vendors.forEach(function(vendor) {
                    var row = `<tr>
                            <td>${vendor.vendorCode || ''}</td>
                            <td>${vendor.vendorName || ''}</td>
                            <td>${vendor.vendorAddress || ''}</td>
                            <td>${vendor.vendorContactNumber || ''}</td>                         
                            <td>
                            <button type="button" class="btn btn-primary btn-sm editVendorButton" style="background-color: rgb(0, 149, 77); color: white; border: 1px rgb(0, 149, 77);" data-id="${vendor.vendorID}">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm deleteVendorButton" data-id="${vendor.vendorID}">Delete</button></td>
                            </td>
                        </tr>`;
                    $("#vendorTable tbody").append(row);
                });

                // Initialize DataTables only if it's not already initialized
                if (!dataTableInitialized) {
                    $('#vendorTable').DataTable({
                        "paging": true,
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
                    $('#vendorTable').DataTable().destroy();
                    $('#vendorTable').DataTable({
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
    populatevendorTable();

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
$("#vendorTable").on("click", ".editVendorButton", function() {
    var vendorID = $(this).data("id");

    // Populate the edit modal with location data
    $.ajax({
        type: "GET",
        url: "modules/vendors/get_vendor_details.php", // Replace with your server-side script
        data: {
            vendorID: vendorID
        },
        success: function(response) {
            var vendorDetails = JSON.parse(response);

            // Populate the edit modal with location details
            $("#editVendorID").val(vendorDetails.vendorID);
            $("#editVendorName").val(vendorDetails.vendorName);
            $("#editVendorCode").val(vendorDetails.vendorCode);
            $("#editVendorAccountNumber").val(vendorDetails.vendorAccountNumber);
            $("#editVendorAddress").val(vendorDetails.vendorAddress);
            $("#editVendorContactNumber").val(vendorDetails.vendorContactNumber);
            $("#editVendorEmail").val(vendorDetails.vendorEmail);
            $("#editTinNumber").val(vendorDetails.tinNumber);
            $("#editVendorTerms").val(vendorDetails.vendorTerms);
            // Show the edit modal
            $("#editVendorModal").modal("show");
        },
        error: function() {
            console.log("Error fetching vendor details for edit.");
        }
    });
});
// Save Edit Location Changes functionality
$("#saveEditVendorButton").click(function() {
    // Retrieve the vendorID from the form data
    var vendorID = $("#editVendorForm").data("vendorID");
    // Add vendorID to the form data
    var formData = $("#editVendorForm").serialize();

    $.ajax({
        type: "POST",
        url: "modules/vendors/update_vendor.php", // Replace with your server-side script
        data: formData,
        success: function(response) {
            if (response === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'Vendor updated successfully!',
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
$("#vendorTable").on("click", ".deleteVendorButton", function() {
    var vendorID = $(this).data("id");

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
                url: "modules/vendors/delete_vendor.php",
                data: {
                    deleteVendorID: vendorID
                },
                success: function(response) {
                    if (response === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Vendor has been deleted!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            // Reload the browser after SweetAlert2 is closed
                            location.reload();
                        });
                        // Update the table after successfully deleting the location
                        // populateVendorTable();
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