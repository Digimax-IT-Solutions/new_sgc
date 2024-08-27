<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();
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
                    <h1 class="h3 mb-3"><strong>Create Input</strong> Vat</h1>
                    <div class="d-flex justify-content-end">
                        <a href="input_vat" class="btn btn-secondary">
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
                                    <form method="POST" action="api/masterlist/input_vat_controller.php"
                                        id="salesTaxForm">
                                        <input type="hidden" name="action" id="modalAction" value="add" />
                                        <!-- INPUT VAT -->
                                        <div class="row mb-3">
                                            <label for="input_vat_name" class="col-sm-2 col-form-label">Input Vat
                                                Name</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" id="input_vat_name"
                                                    name="input_vat_name" placeholder="Enter Input Vat Name" required>
                                            </div>
                                            <!-- INPUT VAT ACCOUNT-->
                                            <label class="col-sm-2 col-form-label" for="input_vat_account_id">Input
                                                Vat Account</label>
                                            <div class="col-sm-4">
                                                <select class="form-control" id="input_vat_account_id"
                                                    name="input_vat_account_id">
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
                                            <label for="input_vat_description"
                                                class="col-sm-2 col-form-label">Description
                                            </label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" id="input_vat_description"
                                                    name="input_vat_description" placeholder="Enter Description"
                                                    required="true" />
                                            </div>
                                            <!-- INPUT VAT RATE -->
                                            <label for="input_vat_rate" class="col-sm-2 col-form-label">Input Vat
                                                rate
                                                (%)</label>
                                            <div class="col-sm-4">
                                                <input type="number" class="form-control" id="input_vat_rate"
                                                    name="input_vat_rate" placeholder="Enter Input Vat Rate" required>
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
            $('#input_vat_account_id').select2({
                theme: 'classic',
                width: '100%',
                placeholder: 'Select Input Vat Account',
                allowClear: false
            });

        });
    </script>