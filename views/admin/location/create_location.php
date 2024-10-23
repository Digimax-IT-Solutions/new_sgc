<?php
// Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('location');
// Fetch all locations
$locations = Location::all();

// Initialize location object if updating
$location = null;
if (get('action') === 'update') {
    $location = Location::find(get('id'));
}

$page = 'location_list'; // Set the variable corresponding to the current page
?>

<?php require 'views/templates/header.php'; ?>
<?php require 'views/templates/sidebar.php'; ?>
<div class="main">
    <style>
        .table-sm .form-control {
            border: none;
            padding: 0;
            background-color: transparent;
            box-shadow: none;
            height: auto;
            line-height: inherit;
            font-size: inherit;
        }

        .select2-no-border .select2-selection {
            border: none !important;
            padding: 0 !important;
            box-shadow: none !important;
        }

        .select2-no-border .select2-selection__rendered {
            padding: 0 !important;
        }
    </style>
    <?php require 'views/templates/navbar.php'; ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">

            <div class="row">
                <div class="col-sm-12 d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-3"><strong>Location</strong> List</h1>

                    <div class="d-flex justify-content-end">
                        <a href="location" class="btn btn-secondary">
                            <i class="align-middle" data-feather="arrow-left-circle"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_payment_method'); ?>
                    <?php displayFlashMessage('delete_payment_method'); ?>
                    <?php displayFlashMessage('update_payment_method'); ?>
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="api/masterlist/location_controller.php" id="uomForm">
                                <input type="hidden" name="action" id="modalAction" value="add" />
                                <input type="hidden" name="id" id="itemId" />
                                <input type="hidden" name="item_data" id="item_data" />

                                <div class="row mb-3">
                                    <label for="categoryName" class="col-sm-2 col-form-label">Location</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="categoryName" name="name"
                                            placeholder="Enter location here"  required />
                                    </div>
                                </div>

                                <!-- Add additional form fields here if needed -->

                                <br><br>

                                <!-- Submit Button -->
                                <div class="row">
                                    <div class="col-md-10 d-inline-block">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </main>

    <?php require 'views/templates/footer.php'; ?>
</div>
