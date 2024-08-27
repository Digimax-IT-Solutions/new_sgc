<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();

//require_once 'api/category_controller.php';

$users = User::all();

$page = 'user_list';
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">

            <h1 class="h3 mb-3"><strong>User</strong> List</h1>

            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_user') ?>
                    <?php displayFlashMessage('delete_user') ?>
                    <?php displayFlashMessage('update_uom') ?>
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <!-- Button to trigger modal -->
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#uomModal">
                                Add New User
                            </button>

                            <table id="uomTable" class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Username</th>
                                        <th>Role</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?= $user->name ?></td>
                                            <td><?= $user->username ?></td>
                                            <td><?= $user->role ?></td>

                                            <td>
                                                <a class="text-primary" href="?action=update&id=<?= $user->id ?>">
                                                    <i class="fas fa-edit"></i> Update
                                                </a>
                                                <a class="text-danger ml-2"
                                                    href="api/user_controller.php?action=delete&id=<?= $user->id ?>">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </main>



    <!-- Modal -->
    <!-- Modal -->
    <div class="modal fade" id="uomModal" tabindex="-1" role="dialog" aria-labelledby="uomModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="uomModalLabel">
                        New User
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="api/user_controller.php" id="uomForm">
                        <input type="hidden" name="action" id="modalAction" value="add" />
                        <input type="hidden" name="id" id="uomId" value="" />
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">USER ROLE</label>
                                <select id="role" class="form-control form-control-sm" name="role" required>
                                    <option value="ADMIN">ADMIN</option>
                                    <option value="ACCOUNTING">ACCOUNTING</option>
                                    <option value="PURCHASING">PURCHASING</option>
                                    <option value="WAREHOUSE">WAREHOUSE</option>
                                </select>
                            </div>
                        </div>
                        <br><br>
                        <div class="input-group mb-3">
                            <div class="form-outline" data-mdb-input-init>
                                <input type="text" id="name" name="name" class="form-control form-control-lg" />
                                <label class="form-label" for="formControlLg">Full Name</label>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <div class="form-outline" data-mdb-input-init>
                                <input type="text" id="username" name="username" class="form-control form-control-lg" />
                                <label class="form-label" for="username">User Name</label>
                            </div>
                        </div>
                        <div class="input-group mb-3">
                            <div class="form-outline" data-mdb-input-init>
                                <input type="text" id="password" name="password" class="form-control form-control-lg" />
                                <label class="form-label" for="password">Password</label>
                            </div>
                        </div>
                        <span class="form-text text-muted">
                            This is the default password. The user will be prompted to change it upon first login.
                        </span>


                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>



    <?php require 'views/templates/footer.php' ?>

    <script>
        $(function () {
            $("#userTable").DataTable({
                "responsive": true, "lengthChange": false, "autoWidth": false,
                "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
            }).buttons().container().appendTo('#userTable_wrapper .col-md-6:eq(0)');
            $('#userTable').DataTable({
                "paging": true,
                "lengthChange": false,
                "searching": false,
                "ordering": true,
                "info": true,
                "autoWidth": false,
                "responsive": true,
            });
        });
    </script>