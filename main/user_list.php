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
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user) : ?>
                                    <tr>
                                        <td><?= $user['id'] ?></td>
                                        <td><?= $user['name'] ?></td>
                                        <td><?= $user['username'] ?></td>
                                        <td><?= $user['position'] ?></td>
                                        <td>
                                            <!-- Add any action buttons here -->
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
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
                <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                    <h5 class="modal-title" id="addUserModalLabel"><b>ADD NEW USER</b></h5>
                    <button type="buttonsaveTermButton" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="background-color: rgb(0,149,77); color: white;">
                    <form id="addTermForm"
                        style="background-color: rgb(0,149,77); padding: 20px; color: white; border-radius: 10px;">
                        <div class="form-group">
                            <label for="userName">NAME</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>

                        <div class="form-group">
                            <label for="userType">USER TYPE</label>
                            <select class="form-control" id="position" name="position">
                                <option value="" selected disabled>-Select User Type-</option>
                                <option value="admin">Admin</option>
                                <option value="employee">Employee</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="termDescription">DESCRIPTION</label>
                            <input type="text" class="form-control" id="position" name="position" required>
                        </div>
                        <div class="form-group form-check">
                            <input type="checkbox" class="form-check-input" id="activeStatus" name="activeStatus"
                                checked>
                            <label class="form-check-label" for="activeStatus">Active</label>
                        </div>
                    </form>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" id="saveItemButton">Save User</button>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- End Add Modal -->


    <?php include('includes/footer.php'); ?>
</div>