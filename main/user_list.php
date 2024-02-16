<?php
include __DIR__ . ('/includes/header.php');

// Include your connect.php file
include 'connect.php';

// Fetch user data from the database
try {
    $stmt = $db->prepare("SELECT id, name, username, position FROM user");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

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
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">User List</h1>
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
                                        <li class="breadcrumb-item active">Maintenance</li>
                                        <li class="breadcrumb-item active">User List</li>
                                    </ol>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <button class="btn btn-success" data-toggle="modal" data-target="#addUserModal"
                                            style="background-color: rgb(0, 149, 77); color: white;">
                                            Add New User
                                        </button>
                                    </ol>
                                </div><!-- /.col -->
                            </div><!-- /.row -->
                            <br><br>
                            <table id="usersTable" class="table table-hover table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>NAME</th>
                                        <th>USERNAME</th>
                                        <th>USER TYPE</th>
                                        <th>EMAIL</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

      <!-- Add Location Modal -->
    <div class="modal" id="addUserModal" tabindex="-1" role="dialog" aria-labelledby="addUserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: rgb(153, 68, 67); color: white;">
                    <h5 class="modal-title" id="addUserModalLabel"><b>ADD NEW USER</b></h5>
                    <button type="buttonsaveUserButton" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background-color: rgb(153, 68, 67); color: white;">
                    <form id="addUserForm"
                        style="background-color: rgb(153, 68, 67); padding: 20px; color: white; border-radius: 10px;">
                        <div class="form-group">
                            <label for="name">NAME</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>

                        <div class="form-group">
                            <label for="position">USER TYPE</label>
                            <select class="form-control" id="position" name="position">
                                <option value="" selected disabled>-Select User Type-</option>
                                <option value="admin">Admin</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="username">USERNAME</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">PASSWORD</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="email">EMAIL</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" id="saveUserButton">Save User</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- End Add Modal -->

    <!-- Edit User Modal -->
    <div class="modal" id="edituserModal" tabindex="-1" role="dialog" aria-labelledby="edituserModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                    <h5 class="modal-title" id="edituserModalLabel"><b>EDIT USER</b></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background-color: rgb(0,149,77); color: white;">
                    <form id="edituserForm"
                        style="background-color: rgb(0,149,77); padding: 20px; color: white; border-radius: 10px;">
                        <input type="text" class="form-control" id="id" name="id" hidden>
                        <div class="form-group">
                            <label for="name">NAME</label>
                            <input type="text" class="form-control" id="editname" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="position">USER TYPE</label>
                            <select class="form-control" id="editposition" name="position">
                                <option value="" selected disabled>-Select User Type-</option>
                                <option value="admin">Admin</option>
                                <option value="staff">Staff</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="username">USERNAME</label>
                            <input type="text" class="form-control" id="editusername" name="username" required>
                        </div>
                        <div class="form-group">
                            <label for="password">PASSWORD</label>
                            <input type="password" class="form-control" id="editpassword" name="password"
                                required>
                        </div>
                        <div class="form-group">
                            <label for="email">EMAIL</label>
                            <input type="email" class="form-control" id="editemail" name="email" required>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" id="saveEdituserButton">Save Changes</button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php include('includes/footer.php'); ?>
</div>
<script>
    $(document).ready(function() {
    $("#saveUserButton").click(function() {
        var formData = $("#addUserForm").serialize();

        $.ajax({
            type: "POST",
            url: "modules/users/save_user.php", // Adjust the URL to the server-side script
            data: formData,
            success: function(response) {
                if (response === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'New User Added!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        // Reload the browser after SweetAlert2 is closed
                        location.reload();
                    });
                    // Update the table after successfully saving the location
                    populateusersTable();
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
    dataTableInitialized = false;
    // Function to fetch and populate location data
    function populateusersTable() {
        $.ajax({
            type: "GET",
            url: "modules/users/get_user.php", // Adjust the URL to the server-side script
            success: function(response) {
                var users = JSON.parse(response);

                // Clear existing table rows
                $("#usersTable tbody").empty();

                users.forEach(function(u) {
                    // Use a class based on the active_status value
                    var row = `<tr>
                            <td>${u.id}</td>
                            <td>${u.name}</td>
                            <td>${u.username}</td>
                            <td>${u.position}</td>v
                            <td>${u.email}</td>
                            <td>
                            <button type="button" class="btn btn-primary btn-sm edituserButton" style="background-color: rgb(0, 149, 77); color: white; border: 1px rgb(0, 149, 77);" data-id="${u.id}">Edit</button>
                            <button type="button" class="btn btn-danger btn-sm deleteuserButton" data-id="${u.id}">Delete</button>
                                </td>
                            </tr>`;
                    $("#usersTable tbody").append(row);
                });

                // Initialize DataTables only if it's not already initialized
                if (!dataTableInitialized) {
                    $('#usersTable').DataTable({
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
                    $('#usersTable').DataTable().destroy();
                    $('#usersTable').DataTable({
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
    populateusersTable();

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

$("#usersTable").on("click", ".deleteuserButton", function() {
    var id = $(this).data("id");

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
                url: "modules/users/delete_user.php",
                data: {
                    deleteid: id
                },
                success: function(response) {
                    if (response === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'User has been deleted!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            // Reload the browser after SweetAlert2 is closed
                            location.reload();
                        });
                        // Update the table after successfully deleting the location
                        populateusersTable();
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
function displayError(errorMessage) {
    Swal.fire({
        icon: 'error',
        title: 'Error deleting user. Please try again.',
        text: errorMessage,
        showConfirmButton: false,
        timer: 5000 // Adjust the timer as needed
    });
}

$("#usersTable").on("click", ".edituserButton", function() {
    var id = $(this).data("id");
    // Populate the edit modal with category data
    $.ajax({
        type: "GET",
        url: "modules/users/get_user_details.php", // Replace with your server-side script
        data: {
           id: id
        },
        success: function(response) {
            var userDetails = JSON.parse(response);
            $("#id").val(userDetails.id);
            $("#editname").val(userDetails.name);
            $("#editposition").val(userDetails.position);
            $("#editusername").val(userDetails.username);
            $("#editpassword").val(userDetails.password);
            $("#editemail").val(userDetails.email);
            // Show the edit modal
            $("#edituserModal").modal("show");
        },
        error: function() {
            console.log("Error fetching user details for edit.");
        }
    });
});


$("#saveEdituserButton").click(function() {
    var formData = $("#edituserForm").serialize();

    $.ajax({
        type: "POST",
        url: "modules/users/update_user.php", // Replace with your server-side script
        data: formData,
        success: function(response) {
            if (response === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'User has been updated!',
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

});
</script>
