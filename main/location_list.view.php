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
#itemsTable {
    border-collapse: collapse;
    width: 100%;
}

#itemsTable th,
#itemsTable td {
    padding: 2px;
    /* Adjust the padding as needed */
}
#itemsTable tbody tr:hover {
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
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Location List</h1>
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
                                        <li class="breadcrumb-item active">Location List</li>
                                    </ol>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <button class="btn btn-success" data-toggle="modal" data-target="#addItemModal"
                                            style="background-color: rgb(0, 149, 77); color: white;">
                                            Add New Location
                                        </button>
                                    </ol>
                                </div><!-- /.col -->
                            </div><!-- /.row -->

                            <br><br>
                            <table id="itemsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>CODE</th>
                                        <th>LOCATION NAME</th>
                                        <th>STATUS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Your location data will go here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Add Location Modal -->
    <div class="modal" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                    <h5 class="modal-title" id="addItemModalLabel"><b>ADD NEW LOCATION</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background-color: rgb(0,149,77); color: white;">
                    <form id="addLocationForm"
                        style="background-color: rgb(0,149,77); padding: 20px; color: white; border-radius: 10px;">


                        <div class="form-group">
                            <label for="locationCode">LOCATION CODE</label>
                            <input type="text" class="form-control" id="locationCode" name="locationCode" required>
                        </div>

                        <div class="form-group">
                            <label for="locationName">LOCATION NAME</label>
                            <input type="text" class="form-control" id="locationName" name="locationName" required>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="activeStatus" name="activeStatus"
                                checked>
                            <label class="form-check-label" for="activeStatus">Active</label>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" id="saveLocationButton">Save Location</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- End Add Modal -->

    <!-- Edit Location Modal -->
    <div class="modal" id="editLocationModal" tabindex="-1" role="dialog" aria-labelledby="editLocationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                    <h5 class="modal-title" id="editLocationModalLabel"><b>EDIT LOCATION</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background-color: rgb(0,149,77); color: white;">
                    <form id="editLocationForm"
                        style="background-color: rgb(0,149,77); padding: 20px; color: white; border-radius: 10px;">
                        <input type="text" class="form-control" id="editLocationID" name="editLocationID" hidden>
                        <div class="form-group">
                            <label for="editLocationCode">LOCATION CODE</label>
                            <input type="text" class="form-control" id="editLocationCode" name="editLocationCode"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="editLocationName">LOCATION NAME</label>
                            <input type="text" class="form-control" id="editLocationName" name="editLocationName"
                                required>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="editActiveStatus"
                                name="editActiveStatus">
                            <label class="form-check-label" for="editActiveStatus">ACTIVE</label>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" id="saveEditLocationButton">Save Changes</button>
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
    $("#saveLocationButton").click(function() {
        var formData = $("#addLocationForm").serialize();

        $.ajax({
            type: "POST",
            url: "modules/masterlist/locations/save_location.php", // Adjust the URL to the server-side script
            data: formData,
            success: function(response) {
                if (response === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Location saved successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        // Reload the browser after SweetAlert2 is closed
                        location.reload();
                    });
                    // Update the table after successfully saving the location
                    populateLocationsTable();
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
    function populateLocationsTable() {
        $.ajax({
            type: "GET",
            url: "modules/masterlist/locations/get_locations.php", // Adjust the URL to the server-side script
            success: function(response) {
                var locations = JSON.parse(response);

                // Clear existing table rows
                $("#itemsTable tbody").empty();

                locations.forEach(function(location) {
                    // Use a class based on the active_status value
                    var statusClass = location.active_status ? 'active' : 'inactive';
                    var row = `<tr>
                            <td>${location.location_code}</td>
                            <td>${location.location_name}</td>
                            <td class="${statusClass}">${location.active_status ? 'Active' : 'Inactive'}</td>
                            <td>
                            <button type="button" class="btn btn-primary btn-sm editLocationButton" style="background-color: rgb(0, 149, 77); color: white; border: 1px rgb(0, 149, 77);" data-id="${location.location_id}">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm deleteLocationButton" data-id="${location.location_id}">Delete</button>
                                </td>
                            </tr>`;
                    $("#itemsTable tbody").append(row);
                });

                // Initialize DataTables only if it's not already initialized
                if (!dataTableInitialized) {
                    $('#itemsTable').DataTable({
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
                    $('#itemsTable').DataTable().destroy();
                    $('#itemsTable').DataTable({
                        // Your DataTables options here
                    });
                }
            },
            error: function() {
                console.log("Error fetching location data.");
            }
        });
    }

    // Initial population when the page loads
    populateLocationsTable();

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
$("#itemsTable").on("click", ".editLocationButton", function() {
    var locationID = $(this).data("id");

    // Populate the edit modal with location data
    $.ajax({
        type: "GET",
        url: "modules/masterlist/locations/get_location_details.php", // Replace with your server-side script
        data: {
            locationID: locationID
        },
        success: function(response) {
            var locationDetails = JSON.parse(response);

            // Populate the edit modal with location details
            $("#editLocationID").val(locationDetails.location_id);
            $("#editLocationCode").val(locationDetails.location_code);
            $("#editLocationName").val(locationDetails.location_name);
            $("#editActiveStatus").prop("checked", locationDetails.active_status);

            // Show the edit modal
            $("#editLocationModal").modal("show");
        },
        error: function() {
            console.log("Error fetching location details for edit.");
        }
    });
});

// Save Edit Location Changes functionality
$("#saveEditLocationButton").click(function() {
    var formData = $("#editLocationForm").serialize();

    $.ajax({
        type: "POST",
        url: "modules/masterlist/locations/update_location.php", // Replace with your server-side script
        data: formData,
        success: function(response) {
            if (response === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'Location updated successfully!',
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
$("#itemsTable").on("click", ".deleteLocationButton", function() {
    var locationID = $(this).data("id");

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
                url: "modules/masterlist/locations/delete_location.php",
                data: {
                    deleteLocationID: locationID
                },
                success: function(response) {
                    if (response === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Location deleted successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            // Reload the browser after SweetAlert2 is closed
                            location.reload();
                        });
                        // Update the table after successfully deleting the location
                        populateLocationsTable();
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