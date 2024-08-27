<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();

$chart_of_accounts = ChartOfAccount::all();
$account_types = AccountType::all();

// echo "Received ID: " . $_GET['id'] . "<br>";
// $chart_of_account = ChartOfAccount::findById($_GET['id']);
// var_dump($chart_of_account);
// die();
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
                    <h1 class="h3 mb-3"><strong>View</strong> Account</h1>
                    <div class="d-flex justify-content-end">
                        <a href="chart_of_accounts" class="btn btn-secondary">
                            <i class="align-middle" data-feather="arrow-left-circle"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add') ?>
                    <?php displayFlashMessage('delete') ?>
                    <?php displayFlashMessage('update') ?>
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">

                            <?php // Check if ID parameter is set in the URL
                            if (isset($_GET['id'])) {

                                $chart_of_account = ChartOfAccount::findById($_GET['id']);

                                if ($chart_of_account) { ?>

                                    <form method="POST" action="api/masterlist/chart_of_account_controller.php">
                                        <input type="hidden" name="action" value="update" />
                                        <input type="hidden" name="id" value="<?= $chart_of_account->id ?>" />
                                        <!-- Account Type -->
                                        <div class="row mb-3">
                                            <label for="account_type_id" class="col-sm-2 col-form-label">Account Type</label>
                                            <div class="col-sm-4">
                                                <select class="form-control" id="account_type_id" name="account_type_id">
                                                    <option value="<?= $chart_of_account->account_type_id ?>">
                                                        <?= $chart_of_account->account_type ?>
                                                    </option>
                                                    <?php foreach ($account_types as $account_type): ?>
                                                        <option value="<?= $account_type->id ?>">
                                                            <?= $account_type->name ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <label for="customer_contact" class="col-sm-2 col-form-label">Account Code</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" id="account_code" name="account_code"
                                                    placeholder="Acount Code" value="<?= $chart_of_account->account_code ?>"
                                                    readonly>
                                            </div>
                                        </div>


                                        <!-- GL -->
                                        <div class="row mb-3">
                                            <label for="gl_code" class="col-sm-2 col-form-label">GL CODE</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" id="gl_code" name="gl_code"
                                                    placeholder="Enter GL Code" value="<?= $chart_of_account->gl_code ?>"
                                                    required>
                                            </div>
                                            <label for="gl_name" class="col-sm-2 col-form-label">GL NAME</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" id="gl_name" name="gl_name"
                                                    placeholder="Enter GL Name" value="<?= $chart_of_account->gl_name ?>"
                                                    required>
                                            </div>
                                        </div>


                                        <!-- SL -->
                                        <div class="row mb-3">
                                            <label for="sl_code" class="col-sm-2 col-form-label">SL CODE</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" id="sl_code" name="sl_code"
                                                    placeholder="Enter SL Code" value="<?= $chart_of_account->sl_code ?>">
                                            </div>
                                            <label for="sl_name" class="col-sm-2 col-form-label">SL NAME</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" id="sl_name" name="sl_name"
                                                    placeholder="Enter SL Name" value="<?= $chart_of_account->sl_name ?>">
                                            </div>
                                        </div>


                                        <!-- Account Description -->
                                        <div class="row mb-3">
                                            <label for="account_description" class="col-sm-2 col-form-label">Account
                                                Description</label>
                                            <div class="col-sm-4">
                                                <textarea class="form-control" id="account_description"
                                                    name="account_description" rows="3"
                                                    placeholder="Enter Description"><?= $chart_of_account->account_description ?></textarea>
                                            </div>
                                        </div>
                                        <br><br>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <br><br>
                                    </form>
                                <?php } else {
                                    // Handle the case where the service is not found
                                    echo "Account not found.";
                                }
                            } else {
                                // Handle the case where the ID is not provided
                                echo "No ID provided.";
                            } ?>
                            <form action="api/masterlist/chart_of_account_controller.php" method="GET"
                                onsubmit="return confirm('Are you sure you want to delete this account?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $chart_of_account->id ?>">

                                <button type="submit" class="btn btn-danger">
                                    <i class="fas fa-trash-alt"></i> Delete Account
                                </button>
                            </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </main>


    <?php require 'views/templates/footer.php' ?>

    <script>
        // Wait for the DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function () {
            // Select the input fields
            const slCodeInput = document.getElementById('sl_code');
            const glCodeInput = document.getElementById('gl_code');
            const accountCodeInput = document.getElementById('account_code');

            // Function to update the Account Code based on SL code and GL code
            function updateAccountCode() {
                // Get the values of SL code and GL code
                const slCode = slCodeInput.value.trim();
                const glCode = glCodeInput.value.trim();

                // Concatenate SL code and GL code to form the Account Code
                const accountCode = glCode + slCode;

                // Update the value of Account Code input field
                accountCodeInput.value = accountCode;
            }

            // Add event listeners to SL code and GL code inputs
            slCodeInput.addEventListener('input', updateAccountCode);
            glCodeInput.addEventListener('input', updateAccountCode);
        });
    </script>