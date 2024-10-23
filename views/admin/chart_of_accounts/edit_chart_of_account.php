<?php
// Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('chart_of_accounts_list');

$chart_of_accounts = ChartOfAccount::all();
$account_types = AccountType::all();

?>

<?php require 'views/templates/header.php'; ?>
<?php require 'views/templates/sidebar.php'; ?>

<div class="main">
    <?php require 'views/templates/navbar.php'; ?>
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3">Edit Account Details</h1>

            <div class="row">

                <div class="col-12 col-xl-8">
                    <?php displayFlashMessage('update_account') ?>
                    <a href="chart_of_accounts" class="btn btn-secondary btn-sm">Back to List</a>
                    <br><br>
                    <div class="card">
                        <div class="card-body">
                            <?php // Check if ID parameter is set in the URL
                            if (isset($_GET['id'])) {

                                $chart_of_account = ChartOfAccount::findById($_GET['id']);

                                if ($chart_of_account) { ?>

                                    <form method="POST" action="api/masterlist/chart_of_account_controller.php">
                                        <input type="hidden" name="action" value="update" />
                                        <input type="hidden" name="id" value="<?= $chart_of_account->id ?>" />

                                        <div class="row g-2">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="account_type_id">Account Type</label>
                                                    <select class="form-control" id="account_type_id" name="account_type_id" required>
                                                        <option value="">Select Account Type</option>
                                                        <?php foreach ($account_types as $account_type): ?>
                                                            <option value="<?= $account_type->id ?>"
                                                                <?= ($chart_of_account->account_type_id == $account_type->id) ? 'selected' : '' ?>>
                                                                <?= $account_type->name ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="account_code">Account Code</label>
                                                    <input type="text" class="form-control form-control-sm" id="account_code" name="account_code" placeholder="Enter Account Code" value="<?= $chart_of_account->account_code ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="account_name">Account Name</label>
                                                    <input type="text" class="form-control form-control-sm" id="account_name" name="account_name" placeholder="Enter Account Name" value="<?= $chart_of_account->account_name ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="sub_account_id">Sub Account of</label>
                                                    <select class="form-select form-select-sm" id="sub_account_id" name="sub_account_id">
                                                        <option value="">Select Sub Account</option>
                                                        <?php foreach ($chart_of_accounts as $sub_account): ?>
                                                            <option value="<?= $sub_account->id ?>"
                                                                <?= ($chart_of_account->sub_account_id == $sub_account->id) ? 'selected' : '' ?>>
                                                                <?= $sub_account->account_name ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="form-group mb-3">
                                                    <label for="account_description">Account Description</label>
                                                    <input class="form-control form-control-sm" id="account_description" name="account_description" placeholder="Enter Description" value="<?= $chart_of_account->account_description ?>" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mt-3">
                                            <button type="submit" class="btn btn-primary btn-sm">Update Account</button>


                                        </div>
                                    </form>

                                    <br><br>


                            <?php } else {
                                    // Handle the case where the account is not found
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
                    </div>
                </div>
            </div>
        </div>
    </main>


    <?php require 'views/templates/footer.php'; ?>

    <style>
        body {
            background-color: #f8f9fa;
        }

        .card {
            border: none;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
            border-radius: 0.375rem;
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            padding: 0.75rem 1rem;
        }

        .form-group label {
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
        }

        .form-control-sm,
        .form-select-sm {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
            height: calc(1.8125rem + 2px);
        }

        textarea.form-control-sm {
            height: auto;
        }

        .btn-sm {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('accountForm');
            form.addEventListener('submit', function(event) {
                let isValid = true;
                const requiredFields = form.querySelectorAll('[required]');

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('is-invalid');
                    } else {
                        field.classList.remove('is-invalid');
                    }
                });

                if (!isValid) {
                    event.preventDefault();
                    alert('Please fill in all required fields.');
                }
            });
        });
    </script>