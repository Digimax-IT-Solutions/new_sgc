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

?>


<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>

<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3">Create New Account</h1>

            <div class="row">
                <div class="col-12 col-xl-8">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Account Details</h5>
                        </div>
                        <div class="card-body">
                            <?php displayFlashMessage('add') ?>
                            <?php displayFlashMessage('delete') ?>
                            <?php displayFlashMessage('update') ?>

                            <form method="POST" action="api/masterlist/chart_of_account_controller.php" id="accountForm">
                                <input type="hidden" name="action" id="modalAction" value="add" />

                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="account_type_id">Account Type</label>
                                            <select class="form-select form-select-sm" id="account_type_id" name="account_type_id" required>
                                                <option value="">Select Account Type</option>
                                                <?php foreach ($account_types as $account_type): ?>
                                                    <option value="<?= $account_type->id ?>"><?= $account_type->name ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="account_code">Account Code</label>
                                            <input type="text" class="form-control form-control-sm" id="account_code" name="account_code" placeholder="Enter Account Code">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="account_name">Account Name</label>
                                            <input type="text" class="form-control form-control-sm" id="account_name" name="account_name" placeholder="Enter Account Name">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="sub_account_id">Sub Account of</label>
                                            <select class="form-select form-select-sm" id="sub_account_id" name="sub_account_id">
                                                <option value="">Select Sub Account</option>
                                                <?php foreach ($chart_of_accounts as $chart_of_account): ?>
                                                    <option value="<?= $chart_of_account->id ?>"><?= $chart_of_account->account_name ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group mb-3">
                                            <label for="account_description">Account Description</label>
                                            <textarea class="form-control form-control-sm" id="account_description" name="account_description" rows="3" placeholder="Enter Description"></textarea>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary btn-sm">Create Account</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require 'views/templates/footer.php' ?>

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