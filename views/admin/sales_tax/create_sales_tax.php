<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('sales_tax');
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
                    <h1 class="h3 mb-3"><strong>Create Sales</strong> Tax</h1>

                    <div class="d-flex justify-content-end">
                        <a href="sales_tax" class="btn btn-secondary">
                            <i class="align-middle" data-feather="arrow-left-circle"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <!-- Default box -->
                    <div class="row">
                        <div class="col-12">
                            <!-- Default box -->
                            <div class="card">
                                <div class="card-body">
                                    <form method="POST" action="api/masterlist/sales_tax_controller.php"
                                        id="salesTaxForm">
                                        <input type="hidden" name="action" id="modalAction" value="add" />
                                        <!-- SALES TAX NAME -->
                                        <div class="row mb-3">
                                            <label for="sales_tax_name" class="col-sm-2 col-form-label">Sales Tax
                                                Name</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" id="sales_tax_name"
                                                    name="sales_tax_name" placeholder="Enter tax" required>
                                            </div>
                                            <!-- SALES TAX ACCOUNT -->
                                            <label class="col-sm-2 col-form-label" for="sales_tax_account_id">Sales
                                                Tax
                                                Account</label>
                                            <div class="col-sm-4">
                                                <select class="form-control" id="sales_tax_account_id"
                                                    name="sales_tax_account_id">
                                                    <!-- <option value="">--Select account--</option> -->
                                                    <?php foreach ($accounts as $account): ?>
                                                        <option value="<?= $account->id ?>">
                                                            <?= htmlspecialchars($account->account_description) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <!-- DESCRIPTION -->
                                        <div class="row mb-3">
                                            <label for="sales_tax_description"
                                                class="col-sm-2 col-form-label">Description
                                            </label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" id="sales_tax_description"
                                                    name="sales_tax_description" placeholder="Enter Description"
                                                    required>
                                            </div>
                                            <!-- SALEX TAX RATE -->
                                            <label for="sales_tax_rate" class="col-sm-2 col-form-label">Sales Tax
                                                rate
                                                (%)</label>
                                            <div class="col-sm-4">
                                                <input type="number" class="form-control" id="sales_tax_rate"
                                                    name="sales_tax_rate" placeholder="Enter tax rate" required>
                                            </div>
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

    <script>
        $(document).ready(function () {
            $('#sales_tax_account_id').select2({
                theme: 'classic',
                width: '100%',
                placeholder: 'Select Sales Tax Account',
                allowClear: false
            });

        });
    </script>