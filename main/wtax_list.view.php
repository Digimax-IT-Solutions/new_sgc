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
#wtaxsTable {
    border-collapse: collapse;
    width: 100%;
}

#wtaxsTable th,
#wtaxsTable td {
    padding: 2px;
    /* Adjust the padding as needed */
}
#wtaxsTable tbody tr:hover {
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
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Withholding Tax Rate</h1>
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
                                        <li class="breadcrumb-item active">Sales Tax</li>
                                    </ol>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <button class="btn btn-success" data-toggle="modal" data-target="#addwtaxModal"
                                            style="background-color: rgb(0, 149, 77); color: white;">
                                            Add WTAX Rate
                                        </button>
                                    </ol>
                                </div><!-- /.col -->
                            </div><!-- /.row -->

                            <br><br>
                            <table id="wtaxsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>CODE</th>
                                        <th>WTAX</th>
                                        <th>RATE %</th>
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
    <div class="modal" id="addwtaxModal" tabindex="-1" role="dialog" aria-labelledby="addwtaxModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                    <h5 class="modal-title" id="addwtaxModalLabel"><b>ADD WITHHOLDING TAX</b></h5>
                    <button type="buttonsaveTermButton" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background-color: rgb(0,149,77); color: white;">
                    <form id="addwtaxForm"
                        style="background-color: rgb(0,149,77); padding: 20px; color: white; border-radius: 10px;">
                        <div class="form-group">
                            <label for="wTaxCode">CODE</label>
                            <input type="text" class="form-control" id="wTaxCode" name="wTaxCode" required>
                        </div>

                        <div class="form-group">
                            <label for="wTaxName">WITHHOLDING TAX</label>
                            <input type="text" class="form-control" id="wTaxName" name="wTaxName" required>
                        </div>
                        <div class="form-group">
                            <label for="wTaxRate">RATE</label>
                            <input type="number" class="form-control" id="wTaxRate" name="wTaxRate" required>
                        </div>
                        <div class="form-group">
                            <label for="wTaxDescription">DESCRIPTION</label>
                            <input type="text" class="form-control" id="wTaxDescription" name="wTaxDescription"
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
                        <button type="button" class="btn btn-success" id="saveWtaxButton">Save Sales Tax</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- End Add Modal -->

    <!-- Edit Location Modal -->
    <div class="modal" id="editwtaxModal" tabindex="-1" role="dialog" aria-labelledby="editwtaxModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                    <h5 class="modal-title" id="editwtaxModalLabel"><b>EDIT WITHHOLDING TAX</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background-color: rgb(0,149,77); color: white;">
                    <form id="editwtaxForm"
                        style="background-color: rgb(0,149,77); padding: 20px; color: white; border-radius: 10px;">
                        <input type="text" class="form-control" id="editwtaxID" name="editwtaxID" hidden>
                        <div class="form-group">
                            <label for="editwtaxCode">WTAX CODE</label>
                            <input type="text" class="form-control" id="editwtaxCode" name="editwtaxCode" required>
                        </div>
                        <div class="form-group">
                            <label for="editwtaxName">WTAX</label>
                            <input type="text" class="form-control" id="editwtaxName" name="editwtaxName" required>
                        </div>
                        <div class="form-group">
                            <label for="editwtaxRate">WTAX RATE</label>
                            <input type="number" class="form-control" id="editwtaxRate" name="editwtaxRate" required>
                        </div>
                        <div class="form-group">
                            <label for="editwtaxDescription">DESCRIPTION</label>
                            <input type="text" class="form-control" id="editwtaxDescription" name="editwtaxDescription"
                                required>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="editActiveStatus" name="editActiveStatus"
                                checked>
                            <label class="form-check-label" for="editActiveStatus">Active</label>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" id="saveEditwtaxButton">Save Changes</button>
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
    $("#saveWtaxButton").click(function() {
        var formData = $("#addwtaxForm").serialize();

        $.ajax({
            type: "POST",
            url: "modules/wtax/save_wtax.php", // Adjust the URL to the server-side script
            data: formData,
            success: function(response) {
                if (response === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'New WTAX Added!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        // Reload the browser after SweetAlert2 is closed
                        location.reload();
                    });
                    // Update the table after successfully saving the location
                    populatewtaxsTable();
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
    function populatewtaxsTable() {
        $.ajax({
            type: "GET",
            url: "modules/wtax/get_wtax.php", // Adjust the URL to the server-side script
            success: function(response) {
                var wTaxRates = JSON.parse(response);

                // Clear existing table rows
                $("#wtaxsTable tbody").empty();

                wTaxRates.forEach(function(wTaxRate) {
                    // Use a class based on the active_status value
                    var statusClass = wTaxRate.activeStatus ? 'active' : 'inactive';
                    var row = `<tr>
                            <td>${wTaxRate.wTaxCode}</td>
                            <td>${wTaxRate.wTaxName}</td>
                            <td>${wTaxRate.wTaxRate}</td>
                            <td>${wTaxRate.wTaxDescription}</td>
                            <td class="${statusClass}">${wTaxRate.activeStatus ? 'Active' : 'Inactive'}</td>
                            <td>
                            <button type="button" class="btn btn-primary btn-sm editwtaxButton" style="background-color: rgb(0, 149, 77); color: white; border: 1px rgb(0, 149, 77);" data-id="${wTaxRate.wtaxID}">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm deletewtaxButton" data-id="${wTaxRate.wtaxID}">Delete</button>
                                </td>
                            </tr>`;
                    $("#wtaxsTable tbody").append(row);
                });

                // Initialize DataTables only if it's not already initialized
                if (!dataTableInitialized) {
                    $('#wtaxsTable').DataTable({
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
                    $('#wtaxsTable').DataTable().destroy();
                    $('#wtaxsTable').DataTable({
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
    populatewtaxsTable();

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
$("#wtaxsTable").on("click", ".editwtaxButton", function() {
    var wtaxID = $(this).data("id");
    // Populate the edit modal with category data
    $.ajax({
        type: "GET",
        url: "modules/wtax/get_wtax_details.php", // Replace with your server-side script
        data: {
            wtaxID: wtaxID
        },
        success: function(response) {
            var wtaxDetails = JSON.parse(response);
            $("#editwtaxID").val(wtaxDetails.wtaxID);
            $("#editwtaxCode").val(wtaxDetails.wTaxCode);
            $("#editwtaxName").val(wtaxDetails.wTaxName);
            $("#editwtaxRate").val(wtaxDetails.wTaxRate);
            $("#editwtaxDescription").val(wtaxDetails.wTaxDescription);
            $("#editActiveStatus").prop("checked", wtaxDetails.activeStatus);

            // Show the edit modal
            $("#editwtaxModal").modal("show");
        },
        error: function() {
            console.log("Error fetching category details for edit.");
        }
    });
});

// Save Edit Category Changes functionality
$("#saveEditwtaxButton").click(function() {
    var formData = $("#editwtaxForm").serialize();

    $.ajax({
        type: "POST",
        url: "modules/wtax/update_wtax.php", // Replace with your server-side script
        data: formData,
        success: function(response) {
            if (response === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'wtax Method has been updated!',
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
$("#wtaxsTable").on("click", ".deletewtaxButton", function() {
    var wtaxID = $(this).data("id");

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
                url: "modules/wtax/delete_wtax.php",
                data: {
                    deletewtaxID: wtaxID
                },
                success: function(response) {
                    if (response === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'wtax Method has been deleted!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            // Reload the browser after SweetAlert2 is closed
                            location.reload();
                        });
                        // Update the table after successfully deleting the location
                        populatewtaxsTable();
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