<?php
// Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('customer');
$customers = Customer::all();
$terms = Term::all();

$customer = null;
if (get('action') === 'update') {
    $customer = Customer::find(get('id'));
}

$page = 'customer_list';
?>

<?php require 'views/templates/header.php'; ?>
<?php require 'views/templates/sidebar.php'; ?>
<div class="main">
    <?php require 'views/templates/navbar.php'; ?>
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-sm-12 d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-3"><strong>Create</strong> New Customer</h1>
                    <div class="d-flex justify-content-end">
                        <a href="customer" class="btn btn-secondary">
                            <i class="align-middle" data-feather="arrow-left-circle"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="api/masterlist/customer_controller.php" id="form">
                                <input type="hidden" name="action" id="modalAction" value="add" />
                                <input type="hidden" name="id" id="customerId"
                                    value="<?= $customer ? $customer->id : ''; ?>" />

                                <!-- CUSTOMER CODE -->
                                <div class="row mb-3">
                                    <label for="customer_code" class="col-sm-2 col-form-label">Customer Code</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="customer_code" name="customer_code"
                                            placeholder="Enter Customer Code">
                                    </div>
                                    <label for="customer_contact" class="col-sm-2 col-form-label">Contact Number</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="customer_contact"
                                            name="customer_contact" placeholder="Enter Contact Number">
                                    </div>
                                </div>

                                <!-- CUSTOMER NAME -->
                                <div class="row mb-3">
                                    <label for="customer_name" class="col-sm-2 col-form-label">Customer Name</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="customer_name" name="customer_name"
                                            placeholder="Enter Customer Name" required>
                                    </div>
                                </div>

                                <!-- SHIPPING ADDRESS -->
                                <div class="row mb-3">
                                    <label for="shipping_address" class="col-sm-2 col-form-label">Shipping
                                        Address</label>
                                    <div class="col-sm-4">
                                        <textarea class="form-control" id="shipping_address" name="shipping_address"
                                            rows="3" placeholder="Enter Shipping Address"></textarea>
                                    </div>
                                    <label for="billing_address" class="col-sm-2 col-form-label">Billing Address</label>
                                    <div class="col-sm-4">
                                        <textarea class="form-control" id="billing_address" name="billing_address"
                                            rows="3" placeholder="Enter Billing Address"></textarea>
                                    </div>
                                </div>

                                <!-- BUSINESS STYLE TERMS -->
                                <div class="row mb-3">
                                    <label for="business_style" class="col-sm-2 col-form-label">Business Style</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="business_style"
                                            name="business_style" placeholder="Enter Business Style">
                                    </div>
                                    <label for="customer_terms" class="col-sm-2 col-form-label">Terms</label>
                                    <div class="col-sm-4">
                                        <select class="form-control form-control-sm" id="customer_terms"
                                            name="customer_terms">
                                            <option value="">Select Term</option>
                                            <?php foreach ($terms as $term): ?>
                                                <option <?= $customer && $customer->customer_terms == $term->id ? 'selected' : ''; ?>><?= $term->term_name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- TIN EMAIL -->
                                <div class="row mb-3">
                                    <label for="customer_tin" class="col-sm-2 col-form-label">TIN Number</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="customer_tin" name="customer_tin"
                                            placeholder="Enter TIN Number">
                                    </div>
                                    <label for="customer_email" class="col-sm-2 col-form-label">Email</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="customer_email"
                                            name="customer_email">
                                    </div>
                                </div>

                                <br><br>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<?php require 'views/templates/footer.php'; ?>