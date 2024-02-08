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
#salesTaxTable {
    border-collapse: collapse;
    width: 100%;
}

#salesTaxTable th,
#salesTaxTable td {

    padding: 2px;
    /* Adjust the padding as needed */
}
#salesTaxTable   tbody tr:hover {
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
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Sales Tax</h1>
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
                                        <button class="btn btn-success" data-toggle="modal" data-target="#addSalesTaxModal"
                                            style="background-color: rgb(0, 149, 77); color: white;">
                                            Add New Sales Tax
                                        </button>
                                    </ol>
                                </div><!-- /.col -->
                            </div><!-- /.row -->

                            <br><br>
                            <table id="salesTaxTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>CODE</th>
                                        <th>SALES TAX</th>
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
    <div class="modal" id="addSalesTaxModal" tabindex="-1" role="dialog" aria-labelledby="addSalesTaxModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                    <h5 class="modal-title" id="addSalesTaxModalLabel"><b>ADD SALES TAX</b></h5>
                    <button type="buttonsaveTermButton" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background-color: rgb(0,149,77); color: white;">
                    <form id="addSalesTaxForm"
                        style="background-color: rgb(0,149,77); padding: 20px; color: white; border-radius: 10px;">
                        <div class="form-group">
                            <label for="salesTaxCode">CODE</label>
                            <input type="text" class="form-control" id="salesTaxCode" name="salesTaxCode" required>
                        </div>

                        <div class="form-group">
                            <label for="salesTaxName">SALES TAX</label>
                            <input type="text" class="form-control" id="salesTaxName" name="salesTaxName" required>
                        </div>
                        <div class="form-group">
                            <label for="salesTaxRate">RATE</label>
                            <input type="number" class="form-control" id="salesTaxRate" name="salesTaxRate" required>
                        </div>
                        <div class="form-group">
                            <label for="salesTaxDescription">DESCRIPTION</label>
                            <input type="text" class="form-control" id="salesTaxDescription" name="salesTaxDescription"
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
                        <button type="button" class="btn btn-success" id="saveSalesTaxButton">Save Sales Tax</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- End Add Modal -->

    <!-- Edit Location Modal -->
    <div class="modal" id="editsalesTaxModal" tabindex="-1" role="dialog" aria-labelledby="editsalesTaxModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                    <h5 class="modal-title" id="editsalesTaxModalLabel"><b>EDIT SALES TAX</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background-color: rgb(0,149,77); color: white;">
                    <form id="editsalesTaxForm"
                        style="background-color: rgb(0,149,77); padding: 20px; color: white; border-radius: 10px;">
                        <input type="text" class="form-control" id="editsalesTaxID" name="editsalesTaxID" hidden>
                        <div class="form-group">
                            <label for="editsalesTaxCode">SALES TAX CODE</label>
                            <input type="text" class="form-control" id="editsalesTaxCode" name="editsalesTaxCode" required>
                        </div>

                        <div class="form-group">
                            <label for="editsalesTaxName">SALES TAX NAME</label>
                            <input type="text" class="form-control" id="editsalesTaxName" name="editsalesTaxName" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="editsalesTaxRate">EDIT RATE</label>
                            <input type="text" class="form-control" id="editsalesTaxRate" name="editsalesTaxRate" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="editsalesTaxDescription">DESCRIPTION</label>
                            <input type="text" class="form-control" id="editsalesTaxDescription" name="editsalesTaxDescription"
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
                        <button type="button" class="btn btn-success" id="saveEditsalesTaxButton">Save Changes</button>
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
    $("#saveSalesTaxButton").click(function() {
        var formData = $("#addSalesTaxForm").serialize();

        $.ajax({
            type: "POST",
            url: "modules/sales_tax/save_sales_tax.php", // Adjust the URL to the server-side script
            data: formData,
            success: function(response) {
                if (response === "success") {
                    Swal.fire({ 
                        icon: 'success',
                        title: 'New Sales Tax Added!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        // Reload the browser after SweetAlert2 is closed
                        location.reload();
                    });
                    // Update the table after successfully saving the location
                    populateSalesTaxTable();
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
    function populateSalesTaxTable() {
        $.ajax({
            type: "GET",
            url: "modules/sales_tax/get_sales_tax.php", // Adjust the URL to the server-side script
            success: function(response) {
                var salesTaxRates = JSON.parse(response);

                // Clear existing table rows
                $("#salesTaxTable tbody").empty();

                salesTaxRates.forEach(function(salesTaxRate) {
                    // Use a class based on the active_status value
                    var statusClass = salesTaxRate.activeStatus ? 'active' : 'inactive';
                    var row = `<tr>
                            <td>${salesTaxRate.salesTaxCode}</td>
                            <td>${salesTaxRate.salesTaxName}</td>
                            <td>${salesTaxRate.salesTaxRate}</td>
                            <td>${salesTaxRate.salesTaxDescription}</td>
                            <td class="${statusClass}">${salesTaxRate.activeStatus ? 'Active' : 'Inactive'}</td>
                            <td>
                            <button type="button" class="btn btn-primary btn-sm editSalesTaxButton" style="background-color: rgb(0, 149, 77); color: white; border: 1px rgb(0, 149, 77);" data-id="${salesTaxRate.salesTaxID}">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm deleteSalesTaxButton" data-id="${salesTaxRate.salesTaxID}">Delete</button>
                                </td>
                            </tr>`;
                    $("#salesTaxTable tbody").append(row);
                });

                // Initialize DataTables only if it's not already initialized
                if (!dataTableInitialized) {
                    $('#salesTaxTable').DataTable({
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
                    $('#salesTaxTable').DataTable().destroy();
                    $('#salesTaxTable').DataTable({
                        // Your DataTables options here
                    });
                }
            },
            error: function() {
                console.log("Error fetching SALES TAX data.");
            }
        });
    }

    // Initial population when the page loads
    populateSalesTaxTable();

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

// Edit SALES TAX functionality
$("#salesTaxTable").on("click", ".editSalesTaxButton", function() {
    var salesTaxID = $(this).data("id");
    // Populate the edit modal with SALES TAX data
    $.ajax({
        type: "GET",
        url: "modules/sales_tax/get_sales_tax_details.php",
        data: {
            salesTaxID: salesTaxID
        },
            success: function(response){
                var stax = JSON.parse(response);
                $("#editsalesTaxID").val(stax.salesTaxID);
                $("#editsalesTaxCode").val(stax.salesTaxCode);
                $("#editsalesTaxName").val(stax.salesTaxName);
                $("#editsalesTaxRate").val(stax.salesTaxRate);
                $("#editsalesTaxDescription").val(stax.salesTaxDescription);
                $("#editActiveStatus").prop("checked", stax.activeStatus);

                // Show the edit modals
                $("#editsalesTaxModal").modal("show");
            },
        error: function() {
            console.log("Error fetching category details for edit.");
        }
    });
});

// Save Edit SALES TAX Changes functionality
$("#saveEditsalesTaxButton").click(function() {
    var formData = $("#editsalesTaxForm").serialize();

    $.ajax({
        type: "POST",
        url: "modules/sales_tax/update_sales_tax.php", // Replace with your server-side script
        data: formData,
        success: function(response) {
            if (response === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'Sales tax has been updated!',
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
$("#salesTaxTable").on("click", ".deleteSalesTaxButton", function() {
    var $salesTaxID = $(this).data("id");

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
                url: "modules/sales_tax/delete_sales_tax.php",
                data: {
                    deleteSalesTaxID: $salesTaxID
                },
                success: function(response) {
                    if (response === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sales tax has been deleted!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            // Reload the browser after SweetAlert2 is closed
                            location.reload();
                        });
                        // Update the table after successfully deleting the location
                        populateTermsTable();
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