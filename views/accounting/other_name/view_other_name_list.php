<?php
// Guard
require_once '_guards.php';
Guard::adminOnly();

// Include necessary files and retrieve data
$other_names = OtherNameList::all();

$other_name = null;
$action = 'add'; // Default action is to add a new other name

if (get('action') === 'update') {
    $action = 'update';
    $other_name = OtherNameList::find(get('id'));
}

$page = 'other_name_list';
?>

<?php require 'views/templates/header.php'; ?>
<?php require 'views/templates/sidebar.php'; ?>
<div class="main">
    <style>
        .table-sm .form-control {
            border: none;
            padding: 0;
            background-color: transparent;
            box-shadow: none;
            height: auto;
            line-height: inherit;
            font-size: inherit;
        }

        .select2-no-border .select2-selection {
            border: none !important;
            padding: 0 !important;
            box-shadow: none !important;
        }

        .select2-no-border .select2-selection__rendered {
            padding: 0 !important;
        }
    </style>
    <?php require 'views/templates/navbar.php'; ?>
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-sm-12 d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-3"><strong><?= ucfirst($action) ?></strong> Other Name</h1>
                    <div class="d-flex justify-content-end">
                        <a href="other_name" class="btn btn-secondary">
                            <i class="align-middle" data-feather="arrow-left-circle"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_uom'); ?>
                    <?php displayFlashMessage('delete_uom'); ?>
                    <?php displayFlashMessage('update_uom'); ?>
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="api/masterlist/other_name_list_controller.php" id="othernameForm">
                                <input type="hidden" name="action" id="modalAction" value="<?= $action ?>" />
                                <input type="hidden" name="id" id="other_nameId" value="<?= $other_name ? $other_name->id : '' ?>" />
                                <div class="row mb-3">
                                    <label for="other_name" class="col-sm-2 col-form-label">Other Name</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="other_name" name="other_name"
                                            placeholder="Enter Other Name" required
                                            value="<?= $other_name ? htmlspecialchars($other_name->other_name) : '' ?>">
                                    </div>
                                    <label for="other_name_code" class="col-sm-2 col-form-label">Other Name Code</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="other_name_code" name="other_name_code"
                                            placeholder="Enter Other Name Code"
                                            value="<?= $other_name ? htmlspecialchars($other_name->other_name_code) : '' ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="account_number" class="col-sm-2 col-form-label">Account Number</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="account_number" name="account_number"
                                            placeholder="Enter Account Number"
                                            value="<?= $other_name ? htmlspecialchars($other_name->account_number) : '' ?>">
                                    </div>
                                    <label for="other_name_address" class="col-sm-2 col-form-label">Other Name Address</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="other_name_address"
                                            name="other_name_address" placeholder="Enter Other Name Address"
                                            value="<?= $other_name ? htmlspecialchars($other_name->other_name_address) : '' ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="contact_number" class="col-sm-2 col-form-label">Contact #</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="contact_number" name="contact_number"
                                            placeholder="Enter Contact #"
                                            value="<?= $other_name ? htmlspecialchars($other_name->contact_number) : '' ?>">
                                    </div>
                                    <label for="email" class="col-sm-2 col-form-label">Email</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="email" name="email"
                                            placeholder="Enter Email"
                                            value="<?= $other_name ? htmlspecialchars($other_name->email) : '' ?>">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="terms" class="col-sm-2 col-form-label">Terms</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="terms" name="terms"
                                            value="<?= $other_name ? htmlspecialchars($other_name->terms) : '' ?>">
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
</div>

<!-- Success Toast -->
<div class="toast align-items-center text-white bg-success position-absolute top-0 end-0 m-3" id="toastSuccess"
    role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
        <div class="toast-body">
            <!-- Toast message will be inserted here -->
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
            aria-label="Close"></button>
    </div>
</div>

<!-- Error Toast -->
<div class="toast align-items-center text-white bg-danger position-absolute top-0 end-0 m-3" id="toastError"
    role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
        <div class="toast-body">
            <!-- Toast message will be inserted here -->
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
            aria-label="Close"></button>
    </div>
</div>

<?php require 'views/templates/footer.php'; ?>
