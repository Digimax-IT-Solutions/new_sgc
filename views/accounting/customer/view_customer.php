<?php
// Guard
require_once '_guards.php';
Guard::adminOnly();

$customers = Customer::all();
$terms = Term::all();

$customer = null;
$action = 'add'; // Default action is to add a new customer

if (get('action') === 'update') {
    $action = 'update';
    $customer = Customer::find(get('id'));
}

$page = 'customer_list';
?>

<?php require 'views/templates/header.php'; ?>
<?php require 'views/templates/sidebar.php'; ?>
<div class="main">
    <?php require 'views/templates/navbar.php'; ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-sm-12 d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-3"><strong><?= ucfirst($action) ?></strong> Customer</h1>

                    <div class="d-flex justify-content-end">
                        <a href="customer_list" class="btn btn-secondary">
                            <i class="align-middle" data-feather="arrow-left-circle"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="api/masterlist/customer_controller.php" id="customerForm">
                                <input type="hidden" name="action" id="modalAction" value="<?= $action ?>" />
                                <input type="hidden" name="id" id="customerId" value="<?= $customer ? $customer->id : '' ?>" />
                                <!-- CUSTOMER CODE -->
                                <div class="row mb-3">
                                    <label for="customer_code" class="col-sm-2 col-form-label">Customer Code</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="customer_code" name="customer_code"
                                            placeholder="Enter Customer Code"
                                            value="<?= $customer ? htmlspecialchars($customer->customer_code) : '' ?>"
                                            required>
                                    </div>
                                    <label for="customer_contact" class="col-sm-2 col-form-label">Contact Number</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="customer_contact" name="customer_contact"
                                            placeholder="Enter Contact Number"
                                            value="<?= $customer ? htmlspecialchars($customer->customer_contact) : '' ?>"
                                            required>
                                    </div>
                                </div>
                                <!-- CUSTOMER NAME -->
                                <div class="row mb-3">
                                    <label for="customer_name" class="col-sm-2 col-form-label">Customer Name</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="customer_name" name="customer_name"
                                            placeholder="Enter Customer Name"
                                            value="<?= $customer ? htmlspecialchars($customer->customer_name) : '' ?>"
                                            required>
                                    </div>
                                </div>
                                <!-- SHIPPING ADDRESS -->
                                <div class="row mb-3">
                                    <label for="shipping_address" class="col-sm-2 col-form-label">Shipping Address</label>
                                    <div class="col-sm-4">
                                        <textarea class="form-control" id="shipping_address" name="shipping_address"
                                            rows="3" placeholder="Enter Shipping Address"
                                            required><?= $customer ? htmlspecialchars($customer->shipping_address) : '' ?></textarea>
                                    </div>
                                    <label for="billing_address" class="col-sm-2 col-form-label">Billing Address</label>
                                    <div class="col-sm-4">
                                        <textarea class="form-control" id="billing_address" name="billing_address"
                                            rows="3" placeholder="Enter Billing Address"><?= $customer ? htmlspecialchars($customer->billing_address) : '' ?></textarea>
                                    </div>
                                </div>
                                <!-- BUSINESS STYLE TERMS -->
                                <div class="row mb-3">
                                    <label for="business_style" class="col-sm-2 col-form-label">Business Style</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="business_style" name="business_style"
                                            placeholder="Enter Business Style"
                                            value="<?= $customer ? htmlspecialchars($customer->business_style) : '' ?>"
                                            required>
                                    </div>
                                    <label for="customer_terms" class="col-sm-2 col-form-label">Terms</label>
                                    <div class="col-sm-4">
                                        <select class="form-control form-control-sm" id="customer_terms" name="customer_terms">
                                            <option value="">Select Term</option>
                                            <?php foreach ($terms as $term): ?>
                                                <option value="<?= htmlspecialchars($term->id) ?>" <?= ($customer && $customer->customer_terms == $term->id) ? 'selected' : '' ?>>
                                                    <?= htmlspecialchars($term->term_name) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                </div>
                                <!-- TIN EMAIL -->
                                <div class="row mb-3">
                                    <label for="customer_tin" class="col-sm-2 col-form-label">TIN Number</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="customer_tin" name="customer_tin"
                                            placeholder="Enter TIN Number"
                                            value="<?= $customer ? htmlspecialchars($customer->customer_tin) : '' ?>"
                                            required>
                                    </div>
                                    <label for="customer_email" class="col-sm-2 col-form-label">Email</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="customer_email" name="customer_email"
                                            value="<?= $customer ? htmlspecialchars($customer->customer_email) : '' ?>">
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