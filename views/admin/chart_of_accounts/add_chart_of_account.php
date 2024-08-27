<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();

$chart_of_accounts = ChartOfAccount::all();
$account_types = AccountType::all();

?>



<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">

            <h1 class="h3 mb-3"><strong>Create New Account</strong></h1>

            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add') ?>
                    <?php displayFlashMessage('delete') ?>
                    <?php displayFlashMessage('update') ?>
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="api/masterlist/chart_of_account_controller.php">
                                <input type="hidden" name="action" id="modalAction" value="add" />
                                <!-- Account Type -->
                                <div class="row mb-3">
                                    <label for="account_type_id" class="col-sm-2 col-form-label">Account Type</label>
                                    <div class="col-sm-4">
                                        <select class="form-control" id="account_type_id" name="account_type_id"
                                            required>
                                            <option value="">Account Type</option>
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
                                            placeholder="GL CODE + SL CODE" readonly>
                                    </div>
                                </div>
                                <!-- GL -->
                                <div class="row mb-3">
                                    <label for="gl_code" class="col-sm-2 col-form-label">GL CODE</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="gl_code" name="gl_code"
                                            placeholder="Enter GL Code" required>
                                    </div>
                                    <label for="gl_name" class="col-sm-2 col-form-label">GL NAME</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="gl_name" name="gl_name"
                                            placeholder="Enter GL Name" required>
                                    </div>
                                </div>
                                <!-- SL -->
                                <div class="row mb-3">
                                    <label for="sl_code" class="col-sm-2 col-form-label">SL CODE</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="sl_code" name="sl_code"
                                            placeholder="Enter SL Code">
                                    </div>
                                    <label for="sl_name" class="col-sm-2 col-form-label">SL NAME</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="sl_name" name="sl_name"
                                            placeholder="Enter SL Name">
                                    </div>
                                </div>



                                <!-- Account Description -->
                                <div class="row mb-3">
                                    <label for="account_description" class="col-sm-2 col-form-label">Account
                                        Description</label>
                                    <div class="col-sm-4">
                                        <textarea class="form-control" id="account_description"
                                            name="account_description" rows="3"
                                            placeholder="Enter Description"></textarea>
                                    </div>
                                </div>
                                <br><br>
                                <button type="submit" class="btn btn-primary">Submit</button>
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