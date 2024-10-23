<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('fs_classification');
//require_once 'api/category_controller.php';

$uoms = Uom::all();

$uom = null;
if (get('action') === 'update') {
    $uom = Uom::find(get('id'));
}


$page = 'enter_bills'; // Set the variable corresponding to the current page
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">

    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-sm-12 d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-3"><strong>Create</strong> FS Classification</h1>
                    <div class="d-flex justify-content-end">
                        <a href="fs_classification" class="btn btn-secondary">
                            <i class="align-middle" data-feather="arrow-left-circle"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">

                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="api/masterlist/fs_classification_controller.php" id="uomForm">
                                <input type="hidden" name="action" id="modalAction" value="add" />
                                <input type="hidden" name="id" id="itemId" value="" />
                                <input type="hidden" name="item_data" id="item_data" />

                                <div class="row mb-3">
                                    <label for="name" class="col-sm-1 col-form-label">Name</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="name" name="name"
                                            placeholder="Enter Classification here" required="off">
                                    </div>
                                </div>
                                <div class="col-md-2"></div>
                                <div class="col-md-2"></div>
                                <div class="col-md-2"></div>

                                <!-- Submit Button -->
                                <div class="col-md-10 d-inline-block">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                        </div>

                        <br><br>


                    </div>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
            <!-- /.card -->
        </div>

    </main>




    <?php require 'views/templates/footer.php' ?>