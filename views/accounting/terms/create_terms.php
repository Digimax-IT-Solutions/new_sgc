<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();

//require_once 'api/category_controller.php';

$terms = Term::all();

$term = null;
if (get('action') === 'update') {
    $term = Term::find(get('id'));
}

$page = 'terms'; // Set the variable corresponding to the current page
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <style>
        .table-sm .form-control {
            border: none;
            /* Remove border */
            padding: 0;
            /* Remove default padding */
            background-color: transparent;
            /* Make background transparent */
            box-shadow: none;
            /* Remove box shadow */
            height: auto;
            /* Auto height to fit content */
            line-height: inherit;
            /* Inherit line-height from the table */
            font-size: inherit;
            /* Inherit font-size from the table */
        }

        .select2-no-border .select2-selection {
            border: none !important;
            padding: 0 !important;
            box-shadow: none !important;
        }

        .select2-no-border .select2-selection__rendered {
            padding: 0 !important;
            /* Adjust if necessary */
        }
    </style>
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">

            <div class="row">
                <div class="col-sm-12 d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-3"><strong>Terms</strong> List</h1>

                    <div class="d-flex justify-content-end">
                        <a href="terms" class="btn btn-secondary">
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
                            <form method="POST" action="api/masterlist/term_controller.php" id="termForm">
                                <input type="hidden" name="action" id="modalAction" value="add" />
                                <input type="hidden" name="id" id="customerId" value="" />
                                <!-- TERMS -->
                                <div class="row mb-3">
                                    <label for="term_name" class="col-sm-2 col-form-label">Term name</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="" name="term_name"
                                            placeholder="Enter Term name" required>
                                    </div>
                                    <label for="customer_contact" class="col-sm-2 col-form-label">Number of day
                                        due</label>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" id="" name="term_days_due"
                                            placeholder="Enter Term name" required="true">
                                    </div>
                                </div>
                                <!-- DESCRIPTION -->
                                <div class="row mb-3">
                                    <label for="description" class="col-sm-2 col-form-label">Description</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="" name="description"
                                            placeholder="Enter Description" required>
                                    </div>
                                </div>
                                <!--  -->
                                <div class="row mb-3">
                                </div>
                                <!--  -->
                                <div class="row mb-3">
                                </div>
                                <!--  -->
                                <div class="row mb-3">
                                </div>

                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
    </main>




    <?php require 'views/templates/footer.php' ?>