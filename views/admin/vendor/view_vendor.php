<?php
// Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('vendor_list');
// Fetch all vendors
$vendors = Vendor::all();
$input_vats = InputVat::all();
$items = Product::all();



// Initialize vendor variable for update action
$vendor = null;
$action = 'add'; // Default action is to add a new vendor

if (get('action') === 'update') {
    $action = 'update';
    $vendor = Vendor::find(get('id'));
}
?>

<?php
// Remove duplicates from the $items array
$uniqueItems = array_unique(array_map(function ($item) {
    return $item->item_type;
}, $items));

// Create an associative array with unique item types
$uniqueItemsArray = [];
foreach ($items as $item) {
    if (!in_array($item->item_type, $uniqueItemsArray)) {
        $uniqueItemsArray[] = $item->item_type;
    }
}
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <style>
        /* CSS styles specific to your page */
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
    <?php require 'views/templates/navbar.php' ?>
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-sm-12 d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-3"><strong><?= ucfirst($action) ?></strong> Vendor</h1>
                    <div class="d-flex justify-content-end">
                        <a href="vendor_list" class="btn btn-secondary">
                            <i class="align-middle" data-feather="arrow-left-circle"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <!-- Flash messages -->
                    <?php displayFlashMessage('add_vendor'); ?>
                    <?php displayFlashMessage('update_vendors'); ?>
                    <?php displayFlashMessage('delete_vendor'); ?>

                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="api/masterlist/vendor_controller.php" id="vendorForm">
                                <div class="row mb-3">
                                    <label for="vendorname" class="col-sm-2 col-form-label">Vendor Name</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="vendorname" name="vendorname"
                                            value="<?php echo $vendor ? htmlspecialchars($vendor->vendor_name) : ''; ?>"
                                            required>
                                    </div>
                                    <label for="vendorcode" class="col-sm-2 col-form-label">Vendor Code</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="vendorcode" name="vendorcode"
                                            value="<?php echo $vendor ? htmlspecialchars($vendor->vendor_code) : ''; ?>"
                                            required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="accountnumber" class="col-sm-2 col-form-label">Account Number</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="accountnumber" name="accountnumber"
                                            value="<?php echo $vendor ? htmlspecialchars($vendor->account_number) : ''; ?>"
                                            required>
                                    </div>
                                    <label for="vendoraddress" class="col-sm-2 col-form-label">Vendor Address</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="vendoraddress" name="vendoraddress"
                                            value="<?php echo $vendor ? htmlspecialchars($vendor->vendor_address) : ''; ?>"
                                            required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="contactnumber" class="col-sm-2 col-form-label">Contact Number</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="contactnumber" name="contactnumber"
                                            value="<?php echo $vendor ? htmlspecialchars($vendor->contact_number) : ''; ?>"
                                            required>
                                    </div>
                                    <label for="email" class="col-sm-2 col-form-label">Email</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="email" name="email"
                                            value="<?php echo $vendor ? htmlspecialchars($vendor->email) : ''; ?>"
                                            required>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="terms" class="col-sm-2 col-form-label">Terms</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="terms" name="terms"
                                            value="<?php echo $vendor ? htmlspecialchars($vendor->terms) : ''; ?>"
                                            required>
                                    </div>
                                    <label for="email" class="col-sm-2 col-form-label">TIN </label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="tin" name="tin"
                                            value="<?php echo $vendor ? htmlspecialchars($vendor->tin) : ''; ?>">
                                    </div>
                                </div>


                                <div class="row mb-3">
                                    <label for="input_vat" class="col-sm-2 col-form-label">Tax Type</label>
                                    <div class="col-sm-4">
                                        <select class="form-control form-control-sm input-vat select2" id="tax_type"
                                            name="tax_type">
                                            <option value="<?= htmlspecialchars($vendor->tax_type) ?>" selected>
                                                <?= htmlspecialchars($vendor->tax_type) ?>
                                            </option>
                                            <?php foreach ($input_vats as $input_vat): ?>

                                                <option value="<?= htmlspecialchars($input_vat->input_vat_name) ?>">
                                                    <?= htmlspecialchars($input_vat->input_vat_name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <label for="tel" class="col-sm-2 col-form-label">Tel NO. </label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="tel_no" name="tel_no"
                                            placeholder="Enter Tel No.">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="fax" class="col-sm-2 col-form-label">Fax NO.</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="fax_no" name="fax_no"
                                            value="<?php echo $vendor ? htmlspecialchars($vendor->fax_no) : ''; ?>">
                                    </div>

                                    <label for="notes" class="col-sm-2 col-form-label">Notes </label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="notes" name="notes"
                                            value="<?php echo $vendor ? htmlspecialchars($vendor->notes) : ''; ?>">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="item_type" class="col-sm-2 col-form-label">Item Type</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="item_type" name="item_type"
                                            value="<?php echo $vendor ? htmlspecialchars($vendor->item_type) : ''; ?>"
                                            readonly>
                                    </div>
                                </div>

                                <input type="hidden" name="id" id="vendorId"
                                    value="<?php echo $vendor ? htmlspecialchars($vendor->id) : ''; ?>" />
                                <input type="hidden" name="action" id="modalAction"
                                    value="<?php echo $vendor ? 'update' : 'add'; ?>" />

                                <div class="row">
                                    <div class="col-sm-12">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
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