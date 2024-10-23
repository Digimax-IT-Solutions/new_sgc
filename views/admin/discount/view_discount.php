<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('discount');
$accounts = ChartOfAccount::all();
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
                    <h1 class="h3 mb-3"><strong>Discount</strong> </h1>

                    <div class="d-flex justify-content-end">
                        <a href="discount" class="btn btn-secondary">
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
                                    <?php if (isset($_GET['id'])) {
                                        $id = $_GET['id'];
                                        $discount = Discount::find($id);
                                        if ($discount) { ?>
                                            <form method="POST" action="api/masterlist/discount_controller.php">
                                                <input type="hidden" name="action" value="update" />
                                                <input type="hidden" name="id" value="<?= $discount->id ?>" />

                                                <!-- DISCOUNT NAME -->
                                                <div class="row mb-3">
                                                    <label for="discount_name" class="col-sm-2 col-form-label">Discount
                                                        name</label>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control" id="discount_name"
                                                            name="discount_name" placeholder="Enter discount name"
                                                            value="<?= $discount->discount_name ?>">
                                                    </div>
                                                    <!-- DISCOUNT ACCOUNT -->
                                                    <label class="col-sm-2 col-form-label" for="discount_account_id">Discount
                                                        Account</label>

                                                    <div class="col-sm-4">
                                                        <select class="form-control" id="discount_account_id"
                                                            name="discount_account_id">
                                                            <!-- <option value="">--Select account--</option> -->
                                                            <?php foreach ($accounts as $account): ?>
                                                                <?php //if ($account->gl_code === '8001'): ?>
                                                                <option value="<?= $account->id ?>"
                                                                    <?= ($account->id == $discount->discount_account_id) ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($account->account_description) ?>
                                                                </option>
                                                                <?php //endif; ?>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- DESCRIPTION NAME -->
                                                <div class="row mb-3">
                                                    <label for="discount_description"
                                                        class="col-sm-2 col-form-label">Description
                                                        name</label>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control" id="discount_description"
                                                            name="discount_description" placeholder="Enter description"
                                                            value="<?= $discount->discount_description ?>">
                                                    </div>
                                                    <!-- DISCOUNT TAX RATE -->
                                                    <label for="discount_rate" class="col-sm-2 col-form-label">Discount
                                                        rate
                                                        (%)</label>
                                                    <div class="col-sm-4">
                                                        <input type="number" class="form-control" id="discount_rate"
                                                            name="discount_rate" placeholder="Enter discount rate"
                                                            value="<?= $discount->discount_rate ?>">
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </form>
                                            <div id="loadingOverlay" class="loading-overlay">
                                                <div class="spinner"></div>
                                            </div>
                                            <?php
                                            // Check found, you can now display the details
                                        } else {
                                            // Handle the case where the check is not found
                                            echo "Check not found.";
                                            exit;
                                        }
                                    } else {
                                        // Handle the case where the ID is not provided
                                        echo "No ID provided.";
                                        exit;
                                    }
                                    ?>

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

    <script>
        $(document).ready(function () {
            $('#discount_account_id').select2({
                theme: 'classic',
                width: '100%',
                placeholder: 'Select Discount Account',
                allowClear: false
            });

        });
    </script>