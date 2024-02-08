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
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Category List</h1>
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
                                        <li class="breadcrumb-item active">Category List</li>
                                    </ol>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <button class="btn btn-success" data-toggle="modal" data-target="#addItemModal"
                                            style="background-color: rgb(0, 149, 77); color: white;">
                                            Add New Category
                                        </button>
                                    </ol>
                                </div><!-- /.col -->
                            </div><!-- /.row -->

                            <br><br>
                            <table id="itemsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>CODE</th>
                                        <th>CATEGORY NAME</th>
                                        <th>DESCRIPTION</th>
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
                    <h5 class="modal-title" id="addItemModalLabel"><b>ADD NEW CATEGORY</b></h5>
                    <button type="buttonsaveCategoryButton" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background-color: rgb(0,149,77); color: white;">
                    <form id="addCategoryForm"
                        style="background-color: rgb(0,149,77); padding: 20px; color: white; border-radius: 10px;">
                        <div class="form-group">
                            <label for="categoryCode">CATEGORY CODE</label>
                            <input type="text" class="form-control" id="categoryCode" name="categoryCode" required>
                        </div>

                        <div class="form-group">
                            <label for="categoryName">CATEGORY NAME</label>
                            <input type="text" class="form-control" id="categoryName" name="categoryName" required>
                        </div>
                        <div class="form-group">
                            <label for="categoryDescription">DESCRPTION</label>
                            <input type="text" class="form-control" id="categoryDescription" name="categoryDescription"
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
                        <button type="button" class="btn btn-success" id="saveCategoryButton">Save Category</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- End Add Modal -->

    <!-- Edit Location Modal -->
    <div class="modal" id="editCategoryModal" tabindex="-1" role="dialog" aria-labelledby="editCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                    <h5 class="modal-title" id="editCategoryModalLabel"><b>EDIT CATEGORY</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background-color: rgb(0,149,77); color: white;">
                    <form id="editCategoryForm"
                        style="background-color: rgb(0,149,77); padding: 20px; color: white; border-radius: 10px;">
                        <input type="text" class="form-control" id="editCategoryID" name="editCategoryID" hidden>
                        <div class="form-group">
                            <label for="editCategoryCode">CATEGORY CODE</label>
                            <input type="text" class="form-control" id="editCategoryCode" name="editCategoryCode" required>
                        </div>

                        <div class="form-group">
                            <label for="editCategoryName">CATEGORY NAME</label>
                            <input type="text" class="form-control" id="editCategoryName" name="editCategoryName" required>
                        </div>
                        <div class="form-group">
                            <label for="editCategoryDescription">DESCRPTION</label>
                            <input type="text" class="form-control" id="editCategoryDescription" name="editCategoryDescription"
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
                        <button type="button" class="btn btn-success" id="saveEditCategoryButton">Save Changes</button>
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
    $("#saveCategoryButton").click(function() {
        var formData = $("#addCategoryForm").serialize();

        $.ajax({
            type: "POST",
            url: "modules/category/save_category.php", // Adjust the URL to the server-side script
            data: formData,
            success: function(response) {
                if (response === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Category saved successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        // Reload the browser after SweetAlert2 is closed
                        location.reload();
                    });
                    // Update the table after successfully saving the location
                    populateCategoriesTable();
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
    function populateCategoriesTable() {
        $.ajax({
            type: "GET",
            url: "modules/category/get_category.php", // Adjust the URL to the server-side script
            success: function(response) {
                var categories = JSON.parse(response);

                // Clear existing table rows
                $("#itemsTable tbody").empty();

                categories.forEach(function(category) {
                    // Use a class based on the active_status value
                    var statusClass = category.active_status ? 'active' : 'inactive';
                    var row = `<tr>
                            <td>${category.category_code}</td>
                            <td>${category.category_code}</td>
                            <td>${category.category_description}</td>
                            <td class="${statusClass}">${category.active_status ? 'Active' : 'Inactive'}</td>
                            <td>
                            <button type="button" class="btn btn-primary btn-sm editCategoryButton" style="background-color: rgb(0, 149, 77); color: white; border: 1px rgb(0, 149, 77);" data-id="${category.category_id}">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm deleteCategoryButton" data-id="${category.category_id}">Delete</button>
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
                console.log("Error fetching category data.");
            }
        });
    }

    // Initial population when the page loads
    populateCategoriesTable();

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
$("#itemsTable").on("click", ".editCategoryButton", function() {
    var categoryID = $(this).data("id");

    // Populate the edit modal with category data
    $.ajax({
        type: "GET",
        url: "modules/category/get_category_details.php", // Replace with your server-side script
        data: {
            categoryID: categoryID
        },
        success: function(response) {
            var categoryDetails = JSON.parse(response);
            $("#editCategoryID").val(categoryDetails.category_id);
            $("#editCategoryCode").val(categoryDetails.category_code);
            $("#editCategoryName").val(categoryDetails.category_name);
            $("#editCategoryDescription").val(categoryDetails.category_description);
            $("#editActiveStatus").prop("checked", categoryDetails.active_status);

            // Show the edit modal
            $("#editCategoryModal").modal("show");
        },
        error: function() {
            console.log("Error fetching category details for edit.");
        }
    });
});

// Save Edit Category Changes functionality
$("#saveEditCategoryButton").click(function() {
    var formData = $("#editCategoryForm").serialize();

    $.ajax({
        type: "POST",
        url: "modules/category/update_category.php", // Replace with your server-side script
        data: formData,
        success: function(response) {
            if (response === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'Category has been updated!',
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
$("#itemsTable").on("click", ".deleteCategoryButton", function() {
    var categoryID = $(this).data("id");

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
                url: "modules/category/delete_category.php",
                data: {
                    deleteCategoryID: categoryID
                },
                success: function(response) {
                    if (response === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Category has been deleted!',
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