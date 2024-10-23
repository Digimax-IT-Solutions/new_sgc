<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('chart_of_accounts_list');

$chart_of_accounts = ChartOfAccount::all();
$account_types = AccountType::all();
$fs_classifications = FsClassification::all();
$fs_notes_classifications = FsNotesClassification::all();

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
                                                <select class="form-control" id="account_type_id" name="account_type_id"
                                                    required>
                                                    <option value="">Select Account Type</option>
                                                    <?php
                                                    // Array to prevent duplicates
                                                    $used_account_types = [];

                                                    foreach ($account_types as $account_type):
                                                        if (!in_array($account_type->id, $used_account_types)):
                                                            $used_account_types[] = $account_type->id; // Track used account types
                                                            ?>
                                                            <option value="<?= $account_type->id ?>"
                                                                <?= ($chart_of_account->account_type_id == $account_type->id) ? 'selected' : '' ?>>
                                                                <?= $account_type->name ?>
                                                            </option>
                                                        <?php
                                                        endif;
                                                    endforeach;
                                                    ?>
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

                                        <!-- FS Classification -->
                                        <div class="row mb-3">
                                            <label for="fs_classification" class="col-sm-2 col-form-label">FS
                                                Classification</label>
                                            <div class="col-sm-4">
                                                <select class="form-control standout-field" id="fs_classification"
                                                    name="fs_classification" required>
                                                    <option value="">Select FS Classification</option>
                                                    <?php
                                                    // Create an array to store already used classifications to prevent duplicates
                                                    $used_classifications = [];

                                                    foreach ($fs_classifications as $fs_classification):
                                                        // Only add the classification if it hasn't been added before
                                                        if (!in_array($fs_classification->id, $used_classifications)):
                                                            $used_classifications[] = $fs_classification->id; // Track the used classification
                                                            ?>
                                                            <option value="<?= $fs_classification->id ?>"
                                                                <?= ($chart_of_account->fs_classification_name == $fs_classification->name) ? 'selected' : '' ?>>
                                                                <?= $fs_classification->name ?>
                                                            </option>
                                                            <?php
                                                        endif;
                                                    endforeach;
                                                    ?>
                                                </select>
                                            </div>
                                        </div>


                                        <!-- FS Notes Classification -->
                                        <div class="row mb-3">
                                            <label for="fs_notes_classification" class="col-sm-2 col-form-label">FS Notes
                                                Classification</label>
                                            <div class="col-sm-4">
                                                <select class="form-control standout-field" id="fs_notes_classification"
                                                    name="fs_notes_classification" required>
                                                    <option value="">Select FS Notes Classification</option>
                                                    <?php
                                                    // Array to keep track of already used classifications
                                                    $used_notes_classifications = [];

                                                    foreach ($fs_notes_classifications as $fs_notes_classification):
                                                        if (!in_array($fs_notes_classification->id, $used_notes_classifications)):
                                                            $used_notes_classifications[] = $fs_notes_classification->id; // Prevent duplicates
                                                            ?>
                                                            <option value="<?= $fs_notes_classification->id ?>"
                                                                <?= ($chart_of_account->fs_notes_classification_name == $fs_notes_classification->name) ? 'selected' : '' ?>>
                                                                <?= $fs_notes_classification->name ?>
                                                            </option>
                                                            <?php
                                                        endif;
                                                    endforeach;
                                                    ?>
                                                </select>
                                            </div>
                                        </div>


                                        <br><br>
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                        <br><br>
                                    </form>

                                    <style>
                                        .standout-field {
                                            border: 2px solid #007bff;
                                            /* Blue border */
                                            background-color: #f0f8ff;
                                            /* Light blue background */
                                            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
                                            /* Shadow effect */
                                        }
                                    </style>

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