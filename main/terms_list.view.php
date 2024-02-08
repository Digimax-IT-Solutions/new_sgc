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
#termsTable {
    border-collapse: collapse;
    width: 100%;
}

#termsTable th,
#termsTable td {
    padding: 2px;
    /* Adjust the padding as needed */
}
#termsTable tbody tr:hover {
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
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Terms List</h1>
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
                                        <li class="breadcrumb-item active">Term List</li>
                                    </ol>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <button class="btn btn-success" data-toggle="modal" data-target="#addTermModal"
                                            style="background-color: rgb(0, 149, 77); color: white;">
                                            Add New Term
                                        </button>
                                    </ol>
                                </div><!-- /.col -->
                            </div><!-- /.row -->

                            <br><br>
                            <table id="termsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>CODE</th>
                                        <th>TERM NAME</th>
                                        <th>NO. OF DAYS DUE</th>
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
    <div class="modal" id="addTermModal" tabindex="-1" role="dialog" aria-labelledby="addTermModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                    <h5 class="modal-title" id="addTermModalLabel"><b>ADD NEW TERM</b></h5>
                    <button type="buttonsaveTermButton" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background-color: rgb(0,149,77); color: white;">
                    <form id="addTermForm"
                        style="background-color: rgb(0,149,77); padding: 20px; color: white; border-radius: 10px;">
                        <div class="form-group">
                            <label for="termCode">TERM CODE</label>
                            <input type="text" class="form-control" id="termCode" name="termCode" required>
                        </div>

                        <div class="form-group">
                            <label for="termName">TERM NAME</label>
                            <input type="text" class="form-control" id="termName" name="termName" required>
                        </div>
                        <div class="form-group">
                            <label for="termDaysDue">NO. OF DAYS DUE</label>
                            <input type="number" class="form-control" id="termDaysDue" name="termDaysDue" required>
                        </div>
                        <div class="form-group">
                            <label for="termDescription">DESCRPTION</label>
                            <input type="text" class="form-control" id="termDescription" name="termDescription"
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
                        <button type="button" class="btn btn-success" id="saveItemButton">Save term</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- End Add Modal -->

    <!-- Edit Location Modal -->
    <div class="modal" id="editTermModal" tabindex="-1" role="dialog" aria-labelledby="editTermModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                    <h5 class="modal-title" id="editTermModalLabel"><b>EDIT TERM</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background-color: rgb(0,149,77); color: white;">
                    <form id="edittermForm"
                        style="background-color: rgb(0,149,77); padding: 20px; color: white; border-radius: 10px;">
                        <input type="text" class="form-control" id="edittermID" name="edittermID" hidden>
                        <div class="form-group">
                            <label for="edittermCode">TERM CODE</label>
                            <input type="text" class="form-control" id="edittermCode" name="edittermCode" required>
                        </div>

                        <div class="form-group">
                            <label for="edittermName">TERM NAME</label>
                            <input type="text" class="form-control" id="edittermName" name="edittermName" required>
                        </div>
                        <div class="form-group">
                            <label for="edittermDescription">DESCRPTION</label>
                            <input type="text" class="form-control" id="edittermDescription" name="edittermDescription"
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
                        <button type="button" class="btn btn-success" id="saveEdittermButton">Save Changes</button>
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
    $("#saveItemButton").click(function() {
        var formData = $("#addTermForm").serialize();

        $.ajax({
            type: "POST",
            url: "modules/terms/save_term.php", // Adjust the URL to the server-side script
            data: formData,
            success: function(response) {
                if (response === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'New Term Added!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        // Reload the browser after SweetAlert2 is closed
                        location.reload();
                    });
                    // Update the table after successfully saving the location
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
    });
    // Set the flag to indicate DataTables is now initialized
    dataTableInitialized = false;
    // Function to fetch and populate location data
    function populateTermsTable() {
        $.ajax({
            type: "GET",
            url: "modules/terms/get_term.php", // Adjust the URL to the server-side script
            success: function(response) {
                var terms = JSON.parse(response);

                // Clear existing table rows
                $("#termsTable tbody").empty();

                terms.forEach(function(term) {
                    // Use a class based on the active_status value
                    var statusClass = term.active_status ? 'active' : 'inactive';
                    var row = `<tr>
                            <td>${term.term_code}</td>
                            <td>${term.term_name}</td>
                            <td>${term.term_days_due}</td>
                            <td>${term.term_description}</td>
                            <td class="${statusClass}">${term.active_status ? 'Active' : 'Inactive'}</td>
                            <td>
                            <button type="button" class="btn btn-primary btn-sm editTermButton" style="background-color: rgb(0, 149, 77); color: white; border: 1px rgb(0, 149, 77);" data-id="${term.term_id}">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm deleteTermButton" data-id="${term.term_id}">Delete</button>
                                </td>
                            </tr>`;
                    $("#termsTable tbody").append(row);
                });

                // Initialize DataTables only if it's not already initialized
                if (!dataTableInitialized) {
                    $('#termsTable').DataTable({
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
                    $('#termsTable').DataTable().destroy();
                    $('#termsTable').DataTable({
                        // Your DataTables options here
                    });
                }
            },
            error: function() {
                console.log("Error fetching term data.");
            }
        });
    }

    // Initial population when the page loads
    populateTermsTable();

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

// Edit term functionality
$("#termsTable").on("click", ".editTermButton", function() {
    var termID = $(this).data("id");
    console.log("Edit button clicked! Term ID:", termID); // Add this line;

    // Populate the edit modal with term data
    $.ajax({
        type: "GET",
        url: "modules/terms/get_term_details.php", // Corrected the URL
        data: {
            term_id: termID // Corrected parameter name to match your PHP script
        },
        success: function(response) {
            var termDetails = JSON.parse(response);
            $("#edittermID").val(termDetails.term_id);
            $("#edittermCode").val(termDetails.term_code);
            $("#edittermName").val(termDetails.term_name);
            $("#edittermDescription").val(termDetails.term_description);
            $("#editActiveStatus").prop("checked", termDetails.active_status);

            // Show the edit modal
            $("#editTermModal").modal("show");
        },
        error: function() {
            console.log("Error fetching term details for edit.");
        }
    });
});


// Save Edit term Changes functionality
$("#saveEdittermButton").click(function() {
    var formData = $("#edittermForm").serialize();

    $.ajax({
        type: "POST",
        url: "modules/terms/update_term.php", // Replace with your server-side script
        data: formData,
        success: function(response) {
            if (response === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'Terms has been updated!',
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
$("#termsTable").on("click", ".deleteTermButton", function() {
    var termID = $(this).data("id");

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
                url: "modules/terms/delete_term.php",
                data: {
                    deleteTermID: termID
                },
                success: function(response) {
                    if (response === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Term has been deleted!',
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