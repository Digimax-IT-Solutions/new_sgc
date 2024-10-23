<?php
// Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('employee');
// require_once 'api/category_controller.php';

$employees = Employee::all();

$employee = null;
if (isset($_GET['action']) && $_GET['action'] === 'update') {
    $employee = Employee::find($_GET['id']);
}

$page = 'employee';
?>
<?php require 'templates/header.php'; ?>
<?php require 'templates/navbar.php'; ?>
<?php require 'templates/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>
                        <?php if (isset($_GET['action']) && $_GET['action'] === 'update'): ?>
                            Update Employee
                        <?php else: ?>
                            Employee
                        <?php endif; ?>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Home</a></li>
                        <li class="breadcrumb-item active">Employee List</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <section class="content">
        <?php displayFlashMessage('add_employee'); ?>
        <?php displayFlashMessage('delete_employee'); ?>
        <?php displayFlashMessage('update_employee'); ?>

        <div class="card">
            <div class="card-body">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#employeeModal">
                    Add New Employee
                </button>
                <br /><br /><br />
                <table id="employeeTable" class="table">
                    <thead>
                        <tr>
                            <th>Employee Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($employees as $emp): ?>
                            <tr>
                                <td><?= $emp->first_name . ' ' . $emp->last_name ?></td>
                                <td>
                                    <a class="text-primary" href="?action=update&id=<?= $emp->id ?>">
                                        <i class="fas fa-edit"></i> Update
                                    </a>
                                    <a class="text-danger ml-2"
                                        href="api/masterlist/employee_controller.php?action=delete&id=<?= $emp->id ?>">
                                        <i class="fas fa-trash-alt"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="employeeModal" tabindex="-1" role="dialog" aria-labelledby="employeeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="employeeModalLabel">
                    New Employee Enlistment
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form method="POST" action="api/masterlist/employee_controller.php" id="employee_listForm">
                    <input type="hidden" name="action" id="modalAction" value="add" />
                    <input type="hidden" name="id" id="employee_listId" value="" />
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="employee_code">Employee Code</label>
                                <input type="text" class="form-control" id="employee_code" name="employee_code"
                                    placeholder="Enter employee code" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="employment_status">Employment Status</label>
                                <select class="form-control" id="employment_status" name="employment_status" required>
                                    <option value="">Select Employment Status</option>
                                    <option value="KP">Key Personnel (KP)</option>
                                    <option value="RF">Rank & File (RF)</option>
                                    <option value="Probationary & Extra">Probationary</option>
                                    <option value="EXTRA">Extra</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="first_name">First Name</label>
                                <input type="text" class="form-control" id="first_name" name="first_name"
                                    placeholder="Enter first name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="middle_name">Middle Name</label>
                                <input type="text" class="form-control" id="middle_name" name="middle_name"
                                    placeholder="Enter middle name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="last_name">Last Name</label>
                                <input type="text" class="form-control" id="last_name" name="last_name"
                                    placeholder="Enter last name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ext_name">Extension Name</label>
                                <input type="text" class="form-control" id="ext_name" name="ext_name"
                                    placeholder="Enter extension name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="co_name">Company Name</label>
                                <input type="text" class="form-control" id="co_name" name="co_name"
                                    placeholder="Enter company name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tin">Employee Tin Number</label>
                                <input type="int" class="form-control" id="tin" name="tin"
                                    placeholder="Enter tin number" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="house_lot_number">House/Lot Number</label>
                                <input type="text" class="form-control" id="house_lot_number" name="house_lot_number"
                                    placeholder="Enter house/lot number" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="street">Street</label>
                                <input type="text" class="form-control" id="street" name="street"
                                    placeholder="Enter street" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="barangay">Barangay</label>
                                <input type="text" class="form-control" id="barangay" name="barangay"
                                    placeholder="Enter barangay" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="town">Town</label>
                                <input type="text" class="form-control" id="town" name="town" placeholder="Enter town"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" class="form-control" id="city" name="city" placeholder="Enter city"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="zip">Zip Code</label>
                                <input type="text" class="form-control" id="zip" name="zip" placeholder="Enter zip code"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="sss">SSS Number</label>
                                <input type="text" class="form-control" id="sss" name="sss"
                                    placeholder="Enter SSS Number" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="phil">Philhealth Number</label>
                                <input type="text" class="form-control" id="phil" name="phil"
                                    placeholder="Enter Philhealth Number" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="pagibig">Pagibig Number</label>
                                <input type="text" class="form-control" id="pagibig" name="pagibig"
                                    placeholder="Enter Pagibig Number" required>
                            </div>
                        </div>
                        <!-- More form fields -->
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require 'templates/footer.php'; ?>

<script>
    $(function () {
        $("#employeeTable").DataTable({
            "responsive": true,
            "lengthChange": false,
            "autoWidth": false,
            "buttons": ["csv", "excel", "pdf", "print"]
        }).buttons().container().appendTo('#employeeTable_wrapper .col-md-6:eq(0)');
    });
</script>