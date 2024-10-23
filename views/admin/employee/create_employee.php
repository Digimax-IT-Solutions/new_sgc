<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('employee');

$uoms = Uom::all();


?>
<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3"><strong>Create</strong> Employee</h1>
            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_uom') ?>
                    <?php displayFlashMessage('delete_uom') ?>
                    <?php displayFlashMessage('update_uom') ?>
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">

                                </div>
                            </div>
                            <br><br>
                            <div class="row">
                                <form method="POST" action="api/masterlist/employee_controller.php"
                                    id="employee_listForm">
                                    <input type="hidden" name="action" id="modalAction" value="add" />
                                    <input type="hidden" name="id" id="employee_listId" value="" />


                                    <!-- EMPLOYEE CODE -->
                                    <div class="row mb-3">
                                        <label for="employee_code" class="col-sm-2 col-form-label">Employee Code</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="employee_code"
                                                name="employee_code" placeholder="Enter Employee Code" required>
                                        </div>
                                        <label for="employment_status" class="col-sm-2 col-form-label">Employement
                                            Status</label>
                                        <div class="col-sm-4">
                                            <select class="form-control" id="employment_status" name="employment_status"
                                                required>
                                                <option value="">Select Employment Status</option>
                                                <option value="KP">Key Personnel (KP)</option>
                                                <option value="RF">Rank & File (RF)</option>
                                                <option value="Probationary & Extra">Probationary</option>
                                                <option value="EXTRA">Extra</option>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- EMPLOYEE NAME -->
                                    <div class="row mb-3">
                                        <label for="first_name" class="col-sm-2 col-form-label">First Name</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="first_name" name="first_name"
                                                placeholder="Enter First Name" required>
                                        </div>
                                        <label for="middle_name" class="col-sm-2 col-form-label">Middle Name</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="middle_name" name="middle_name"
                                                placeholder="Enter Middle Name" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="last_name" class="col-sm-2 col-form-label">Last Name</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="last_name" name="last_name"
                                                placeholder="Enter Last Name" required>
                                        </div>
                                        <label for="ext_name" class="col-sm-2 col-form-label">Extension Name</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="ext_name" name="ext_name"
                                                placeholder="Enter Extension Name" required>
                                        </div>
                                    </div>



                                    <!-- COMPANY NAME -->

                                    <div class="row mb-3">
                                        <label for="co_name" class="col-sm-2 col-form-label">Company Name</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="co_name" name="co_name"
                                                placeholder="Enter Company Name" required>
                                        </div>
                                    </div>

                                    <hr>

                                    <!-- EMPLOYE ADDRESS -->
                                    <div class="row mb-3">
                                        <label for="house_lot_number" class="col-sm-2 col-form-label">House/Lot
                                            Number</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="house_lot_number"
                                                name="house_lot_number" placeholder="Enter House/Lot Number" required>
                                        </div>
                                        <label for="street" class="col-sm-2 col-form-label">Street</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="street" name="street"
                                                placeholder="Enter Street" required>
                                        </div>
                                    </div>


                                    <div class="row mb-3">
                                        <label for="town" class="col-sm-2 col-form-label">Town</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="town" name="town"
                                                placeholder="Enter Town" required>
                                        </div>
                                        <label for="barangay" class="col-sm-2 col-form-label">Barangay</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="barangay" name="barangay"
                                                placeholder="Enter Barangay" required>
                                        </div>

                                    </div>

                                    <div class="row mb-3">
                                        <label for="city" class="col-sm-2 col-form-label">City</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="city" name="city"
                                                placeholder="Enter City" required>
                                        </div>
                                        <label for="zip" class="col-sm-2 col-form-label">Zip Code</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="zip" name="zip"
                                                placeholder="Enter Zip Code" required>
                                        </div>
                                    </div>

                                    <hr>

                                    <!-- EMPLOYEE NUMBER -->
                                    <div class="row mb-3">
                                        <label for="sss" class="col-sm-2 col-form-label">SSS Number</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="sss" name="sss"
                                                placeholder="Enter SSS Number" required>
                                        </div>
                                        <label for="philhealth" class="col-sm-2 col-form-label">Philhealth
                                            Number</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="philhealth" name="philhealth"
                                                placeholder="Enter Philhealth Number" required>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <label for="tin" class="col-sm-2 col-form-label">TIN Number</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="tin" name="tin"
                                                placeholder="Enter TIN Number" required>
                                        </div>
                                        <label for="pagibig" class="col-sm-2 col-form-label">Pagibig Number</label>
                                        <div class="col-sm-4">
                                            <input type="text" class="form-control" id="pagibig" name="pagibig"
                                                placeholder="Enter Pagibig Number" required>
                                        </div>
                                    </div>


                                    <br><br>
                                    <button type="submit" class="btn btn-primary">Submit</button>

                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </main>
</div>

<?php require 'views/templates/footer.php' ?>