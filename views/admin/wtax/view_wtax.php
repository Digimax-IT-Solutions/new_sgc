<?php

//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('wtax');
$accounts = ChartOfAccount::all();

?>
<!-- 

WITHHOLDING TAX FORM

name
description
rate
account_code

-->
<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-sm-12 d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-3"><strong>Withholding</strong> Tax</h1>
                    <div class="d-flex justify-content-end">
                        <a href="wtax" class="btn btn-secondary">
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
                                    <?php if (isset($_GET['id'])) {
                                        $id = $_GET['id'];
                                        $wtax = WithholdingTax::find($id);
                                        if ($wtax) { ?>
                                            <form method="POST" action="api/masterlist/wtax_controller.php">
                                                <input type="hidden" name="action" value="update" />
                                                <input type="hidden" name="id" value="<?= $wtax->id ?>" />
                                                <!-- WITHHOLDING TAX NAME -->
                                                <div class="row mb-3">
                                                    <label for="wtax_name" class="col-sm-2 col-form-label">Withholding Tax
                                                        Name</label>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control" id="wtax_name" name="wtax_name"
                                                            placeholder="Enter tax" value="<?= $wtax->wtax_name ?>">
                                                    </div>
                                                    <!-- WITHHOLDING TAX RATE -->
                                                    <label for="wtax_rate" class="col-sm-2 col-form-label">Withholding tax rate
                                                        (%)</label>
                                                    <div class="col-sm-4">
                                                        <input type="number" class="form-control" id="wtax_rate"
                                                            name="wtax_rate" placeholder="Enter tax rate"
                                                            value="<?= $wtax->wtax_rate ?>">
                                                    </div>
                                                </div>
                                                <!-- DESCRIPTION -->
                                                <div class="row mb-3">
                                                    <label for="wtax_description"
                                                        class="col-sm-2 col-form-label">Description</label>
                                                    <div class="col-sm-4">
                                                        <input type="text" class="form-control" id="wtax_description"
                                                            name="wtax_description" placeholder="Enter description"
                                                            value="<?= $wtax->wtax_description ?>">
                                                    </div>
                                                </div>
                                                <div class="row mb-3">
                                                    <!-- WITHHOLDING TAX ACCOUNT -->
                                                    <label for="wtax_account_id" class="col-sm-2 col-form-label">Withholding Tax
                                                        Account</label>
                                                    <div class="col-sm-4">
                                                        <select class="form-control" id="wtax_account_id"
                                                            name="wtax_account_id">
                                                            <?php foreach ($accounts as $account): ?>
                                                                <option value="<?= $account->id ?>"
                                                                    <?= ($account->id == $wtax->wtax_account_id) ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($account->account_description) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <button type="submit" class="btn btn-primary">Submit</button>
                                            </form>
                                            <?php
                                        } else {
                                            echo "Withholding tax not found.";
                                        }
                                    } else {
                                        ?>
                                        <form method="POST" action="api/masterlist/wtax_controller.php">
                                            <input type="hidden" name="action" id="modalAction" value="add" />
                                            <!-- WITHHOLDING TAX NAME -->
                                            <div class="row mb-3">
                                                <label for="wtax_name" class="col-sm-2 col-form-label">Withholding Tax
                                                    Name</label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" id="wtax_name" name="wtax_name"
                                                        placeholder="Enter tax" required>
                                                </div>
                                                <!-- WITHHOLDING TAX RATE -->
                                                <label for="wtax_rate" class="col-sm-2 col-form-label">Withholding tax rate
                                                    (%)</label>
                                                <div class="col-sm-4">
                                                    <input type="number" class="form-control" id="wtax_rate"
                                                        name="wtax_rate" placeholder="Enter tax rate" required>
                                                </div>
                                            </div>
                                            <!-- DESCRIPTION -->
                                            <div class="row mb-3">
                                                <label for="wtax_description"
                                                    class="col-sm-2 col-form-label">Description</label>
                                                <div class="col-sm-4">
                                                    <input type="text" class="form-control" id="wtax_description"
                                                        name="wtax_description" placeholder="Enter description" required>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <!-- WITHHOLDING TAX ACCOUNT -->
                                                <label for="wtax_account_id" class="col-sm-2 col-form-label">Withholding Tax
                                                    Account</label>
                                                <div class="col-sm-4">
                                                    <select class="form-control" id="wtax_account_id"
                                                        name="wtax_account_id">
                                                        <?php foreach ($accounts as $account): ?>
                                                            <option value="<?= $account->id ?>">
                                                                <?= htmlspecialchars($account->account_description) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </form>
                                    <?php } ?>
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
</div>

<script>
    $(document).ready(function () {
        $('#wtax_account_id').select2({
            theme: 'classic',
            width: '100%',
            placeholder: 'Select WTax Account',
            allowClear: false
        });

    });
</script>