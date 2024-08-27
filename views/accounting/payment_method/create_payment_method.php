<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();

//require_once 'api/category_controller.php';

$payment_methods = PaymentMethod::all();

$payment_method = null;
if (get('action') === 'update') {
    $payment_method = PaymentMethod::find(get('id'));
}



$page = 'payment_method'; // Set the variable corresponding to the current page
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
                    <h1 class="h3 mb-3"><strong>Payment</strong> Method</h1>

                    <div class="d-flex justify-content-end">
                        <a href="payment_method" class="btn btn-secondary">
                            <i class="align-middle" data-feather="arrow-left-circle"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_payment_method') ?>
                    <?php displayFlashMessage('delete_payment_method') ?>
                    <?php displayFlashMessage('update_payment_method') ?>
                    <!-- Default box -->
                    <div class="row">
                        <div class="col-12">
                            <!-- Default box -->
                            <div class="card">
                                <div class="card-body">
                                    <form method="POST" action="api/masterlist/payment_method_controller.php"
                                        id="paymentMethodForm">
                                        <input type="hidden" name="action" id="modalAction" value="add" />
                                        <input type="hidden" name="id" id="customerId" value="" />
                                        <!-- PAYMENT METHOD -->
                                        <div class="row mb-3">
                                            <label for="payment_method_name" class="col-sm-2 col-form-label">Payment
                                                Method</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" id="" name="payment_method_name"
                                                    placeholder="Enter Term name" required="true" />
                                            </div>
                                            <!-- DESCRIPTION -->
                                            <label for="description" class="col-sm-2 col-form-label">Description</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" id="" name="description"
                                                    placeholder="Enter Description" required="true" />
                                            </div>
                                        </div>

                                        <div class="row mb-3">

                                        </div>

                                        <div class="row mb-3">

                                        </div>

                                        <div class="row mb-3">

                                        </div>

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
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </main>




    <?php require 'views/templates/footer.php' ?>