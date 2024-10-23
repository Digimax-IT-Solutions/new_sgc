<?php

// create_user.php


//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('create_user');

//require_once 'api/category_controller.php';

$users = User::all();
$roles = Role::all();

$page = 'user_list';
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>

<style>
    .menu-access-container {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        padding: 1rem;
    }

    .menu-item {
        margin-bottom: 1rem;
    }

    .submenu-items {
        margin-left: 1.5rem;
    }
</style>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">

            <h1 class="h3 mb-3"><strong>User</strong> List</h1>

            <div class="row">
                <div class="col-12">

                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="api/user_controller.php" id="userForm">
                                <input type="hidden" name="action" id="modalAction" value="add">
                                <input type="hidden" name="id" id="userId" value="">

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="role" class="form-label">User Role</label>
                                        <select id="role" class="form-select" name="role" required>
                                            <option value="" selected disabled>Select a role</option>
                                            <?php foreach ($roles as $role): ?>
                                                <option value="<?= $role->id ?>">
                                                    <?= htmlspecialchars($role->role_name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="name" class="form-label">Full Name</label>
                                        <input type="text" id="name" name="name" class="form-control" required>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input type="text" id="username" name="username" class="form-control" required>
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" id="password" name="password" class="form-control"
                                            required>
                                        <small class="form-text text-muted">
                                            This is the default password. The user will be prompted to change it upon
                                            first login.
                                        </small>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label for="modules" class="form-label">Module Access</label>
                                        <div id="modules" class="menu-access-container">
                                            <!-- Modules will be loaded here based on the selected role -->
                                        </div>
                                    </div>
                                </div>



                                <div class="text-end">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->

            </div>
        </div>
</div>
</main>



<?php require 'views/templates/footer.php' ?>

<script>
    document.getElementById('role').addEventListener('change', function () {
        const roleId = this.value;

        if (roleId) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', 'api/get_role_modules.php', true);
            xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    document.getElementById('modules').innerHTML = xhr.responseText;
                }
            };
            xhr.send('role_id=' + roleId);
        } else {
            document.getElementById('modules').innerHTML = '';
        }
    });
</script>