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
#uomTable {
    border-collapse: collapse;
    width: 100%;
}

#uomTable th,
#uomTable td {
    padding: 2px;
    /* Adjust the padding as needed */
}
#uomTable tbody tr:hover {
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
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Unit of Measurement</h1>
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
                                        <li class="breadcrumb-item"><a style="color:maroon"href="dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Master List</li>
                                        <li class="breadcrumb-item active">Unit of Measurement</li>
                                    </ol>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <button class="btn btn-success" data-toggle="modal" data-target="#addUomModal"
                                            style="background-color: rgb(0, 149, 77); color: white;">
                                            Add New UOM
                                        </button>
                                    </ol>
                                </div><!-- /.col -->
                            </div><!-- /.row -->

                            <br><br>
                            <table id="uomTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>CODE</th>
                                        <th>UOM</th>
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
    <div class="modal" id="addUomModal" tabindex="-1" role="dialog" aria-labelledby="addUomModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                    <h5 class="modal-title" id="addUomModalLabel"><b>ADD NEW UOM</b></h5>
                    <button type="buttonsaveTermButton" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background-color: rgb(0,149,77); color: white;">
                    <form id="addUomForm"
                        style="background-color: rgb(0,149,77); padding: 20px; color: white; border-radius: 10px;">
                        <div class="form-group">
                            <label for="uomCode">CODE</label>
                            <input type="text" class="form-control" id="uomCode" name="uomCode" required>
                        </div>

                        <div class="form-group">
                            <label for="uomName">Unit of Measurement</label>
                            <input type="text" class="form-control" id="uomName" name="uomName" required>
                        </div>
                        <div class="form-group">
                            <label for="uomDescription">DESCRPTION</label>
                            <input type="text" class="form-control" id="uomDescription" name="uomDescription"
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
                        <button type="button" class="btn btn-success" id="saveUomButton">Save</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- End Add Modal -->

    <!-- Edit Location Modal -->
    <div class="modal" id="edituomModal" tabindex="-1" role="dialog" aria-labelledby="edituomModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                    <h5 class="modal-title" id="edituomModalLabel"><b>EDIT UOM</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background-color: rgb(0,149,77); color: white;">
                    <form id="edituomForm"
                        style="background-color: rgb(0,149,77); padding: 20px; color: white; border-radius: 10px;">
                        <input type="text" class="form-control" id="edituomID" name="edituomID" hidden>
                        <div class="form-group">
                            <label for="edituomCode">UOM CODE</label>
                            <input type="text" class="form-control" id="edituomCode" name="edituomCode" required>
                        </div>

                        <div class="form-group">
                            <label for="edituomName">UOM NAME</label>
                            <input type="text" class="form-control" id="edituomName" name="edituomName" required>
                        </div>
                        <div class="form-group">
                            <label for="edituomDescription">UOM DESCRPTION</label>
                            <input type="text" class="form-control" id="edituomDescription" name="edituomDescription"
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
                        <button type="button" class="btn btn-success" id="saveEdituomButton">Save Changes</button>
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
    $("#saveUomButton").click(function() {
        var formData = $("#addUomForm").serialize();

        $.ajax({
            type: "POST",
            url: "modules/uom/save_uom.php", // Adjust the URL to the server-side script
            data: formData,
            success: function(response) {
                if (response === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'New UOM Added!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        // Reload the browser after SweetAlert2 is closed
                        location.reload();
                    });
                    // Update the table after successfully saving the location
                    populateuomTable();
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
    function populateuomTable() {
        $.ajax({
            type: "GET",
            url: "modules/uom/get_uom.php", // Adjust the URL to the server-side script
            success: function(response) {
                var uom = JSON.parse(response);

                // Clear existing table rows
                $("#uomTable tbody").empty();

                uom.forEach(function(u) {
                    // Use a class based on the active_status value
                    var statusClass = u.activeStatus ? 'active' : 'inactive';
                    var row = `<tr>
                            <td>${u.uomCode}</td>
                            <td>${u.uomName}</td>
                            <td>${u.uomDescription}</td>
                            <td class="${statusClass}">${u.activeStatus ? 'Active' : 'Inactive'}</td>
                            <td>
                            <button type="button" class="btn btn-primary btn-sm edituomButton" style="background-color: rgb(0, 149, 77); color: white; border: 1px rgb(0, 149, 77);" data-id="${u.uomID}">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm deleteuomButton" data-id="${u.uomID}">Delete</button>
                                </td>
                            </tr>`;
                    $("#uomTable tbody").append(row);
                });

                // Initialize DataTables only if it's not already initialized
                if (!dataTableInitialized) {
                    $('#uomTable').DataTable({
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
                    $('#uomTable').DataTable().destroy();
                    $('#uomTable').DataTable({
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
    populateuomTable();

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
$("#uomTable").on("click", ".edituomButton", function() {
    var uomID = $(this).data("id");
    // Populate the edit modal with category data
    $.ajax({
        type: "GET",
        url: "modules/uom/get_oum_details.php", // Replace with your server-side script
        data: {
           uomID: uomID
        },
        success: function(response) {
            var uomDetails = JSON.parse(response);
            $("#edituomID").val(uomDetails.uomID);
            $("#edituomCode").val(uomDetails.uomCode);
            $("#edituomName").val(uomDetails.uomName);
            $("#edituomRate").val(uomDetails.uomRate);
            $("#edituomDescription").val(uomDetails.uomDescription);
            $("#editActiveStatus").prop("checked", uomDetails.activeStatus);

            // Show the edit modal
            $("#edituomModal").modal("show");
        },
        error: function() {
            console.log("Error fetching category details for edit.");
        }
    });
});

// Save Edit Category Changes functionality
$("#saveEdituomButton").click(function() {
    var formData = $("#edituomForm").serialize();

    $.ajax({
        type: "POST",
        url: "modules/uom/update_uom.php", // Replace with your server-side script
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
$("#uomTable").on("click", ".deleteuomButton", function() {
    var uomID = $(this).data("id");

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
                url: "modules/uom/delete_uom.php",
                data: {
                    deleteuomID: uomID
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
                        populateuomTable();
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