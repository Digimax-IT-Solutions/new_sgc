<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('role_list');

$roles = Role::all();
?>

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

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3"><strong>Role</strong> List</h1>
            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_role') ?>
                    <?php displayFlashMessage('delete_role  ') ?>
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <!-- Button to trigger modal -->
                            <!-- Button to trigger modal -->
                            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
                                data-bs-target="#addRoleModal">
                                <i class="fas fa-plus"></i> New Role
                            </button>
                            <table id="table" class="table">
                                <thead>
                                    <tr>
                                        <th>ROLE</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($roles as $role): ?>
                                        <tr>
                                            <td><?= $role->role_name ?></td>

                                            <td>
                                                <a class="text-primary" href="?action=update&id=<?= $role->id ?>">
                                                    <i class="fas fa-edit"></i> Update
                                                </a>
                                                <a class="text-danger ml-2"
                                                    href="api/user_controller.php?action=deleteRole&id=<?= $role->id ?>">
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

    <!-- Add Role Modal -->
    <div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
        <div class="modal-dialog  modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addRoleModalLabel">Add New Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="api/user_controller.php" id="addRoleForm">
                    <input type="hidden" name="action" id="modalAction" value="addrole">
                    <input type="hidden" name="id" id="userId" value="">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="roleName" class="form-label">Role Name</label>
                            <input type="text" class="form-control" id="roleName" name="roleName" required>
                        </div>
                        <!-- Add more fields here if needed -->
                        <div class="mb-4">
                            <label class="form-label">Module Access</label>
                            <div class="menu-access-container">
                                <?php
                                if (is_array($menuConfig)) {
                                    foreach ($menuConfig as $menuName => $menu) {
                                        echo "<div class='menu-item'>";
                                        if (isset($menu['link'])) {
                                            $isDisabled = ($menuName === 'Dashboard') ? 'disabled' : '';
                                            $isChecked = ($menuName === 'Dashboard') ? 'checked' : '';
                                            echo "<div class='form-check'>";
                                            echo "<input type='checkbox' class='form-check-input' name='menus[]' value='{$menu['link']}' id='$menuName' $isDisabled $isChecked>";
                                            echo "<label class='form-check-label' for='$menuName'>$menuName</label>";
                                            echo "</div>";

                                            // If it's Dashboard, add a hidden input to ensure it's always submitted
                                            if ($menuName === 'Dashboard') {
                                                echo "<input type='hidden' name='menus[]' value='{$menu['link']}'>";
                                            }
                                        } else {
                                            echo "<h6>$menuName</h6>";
                                        }

                                        if (isset($menu['submenu']) && is_array($menu['submenu'])) {
                                            echo "<div class='submenu-items'>";
                                            foreach ($menu['submenu'] as $submenuName => $submenu) {
                                                echo "<div class='form-check'>";
                                                echo "<input type='checkbox' class='form-check-input' name='menus[]' value='{$submenu['link']}' id='$submenuName'>";
                                                echo "<label class='form-check-label' for='$submenuName'>$submenuName</label>";
                                                echo "</div>";
                                            }
                                            echo "</div>";
                                        }
                                        echo "</div>";
                                    }
                                } else {
                                    echo "<p>No menu configuration found.</p>";
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Role</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <?php require 'views/templates/footer.php' ?>

    <script>
        $(function () {

            $('#table').DataTable({
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