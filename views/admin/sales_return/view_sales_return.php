<?php
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('sales_return');
$accounts = ChartOfAccount::all();
$customers = Customer::all();
$products = Product::all();
$terms = Term::all();
$locations = Location::all();
$payment_methods = PaymentMethod::all();
$wtaxes = WithholdingTax::all();

$newsales_returnNo = SalesReturn::getLastsales_returnNo();


$discounts = Discount::all();
$input_vats = InputVat::all();
$sales_taxes = SalesTax::all();
// $sales_returnDetails = sales_return::find($id);

// // Output JSON encoded sales_return details
// echo json_encode($sales_returnDetails);

$page = 'sales_sales_return'; // Set the variable corresponding to the current page
?>

<style>
    .form-label {
        font-size: 0.675rem;
        margin-bottom: 0.25rem;
    }

    .card-body {
        font-size: 0.875rem;
    }

    #itemTable th {
        white-space: nowrap;
    }

    #itemTable tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    #itemTable th,
    #itemTable td {
        padding: 0.5rem;
        vertical-align: middle;
    }

    #itemTable .text-right {
        text-align: right;
    }

    .loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(255, 255, 255, 0.8);
        z-index: 9999;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    .loading-overlay .spinner {
        border: 16px solid #54BD69;
        border-top: 16px solid #fff;
        border-radius: 50%;
        width: 120px;
        height: 120px;
        animation: spin 2s linear infinite;
    }

    #loadingOverlay .message {
        color: white;
        margin-top: 10px;
        font-size: 18px;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>

<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3"><strong>View Sales Return</strong></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="sales_return">Returns</a></li>
                                <li class="breadcrumb-item active" aria-current="page">View Sales Return</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="sales_return" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Sales Return
                        </a>
                    </div>
                </div>
            </div>
            <?php if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $sales_return = SalesReturn::find($id);
                if ($sales_return) { ?>
                    <form id="receiveItemForm" action="api/sales_return_controller.php" method="POST">
                        <input type="hidden" name="action" id="modalAction" value="update">
                        <input type="hidden" id="item_data" name="item_data">
                        <input type="hidden" name="id" id="itemId" value="<?= $sales_return->id ?>">
                        <!-- sales_return DETAILS -->
                        <div class="row">
                            <!-- sales_return CUSTOMER DETAILS -->
                            <div class="col-12 col-lg-8">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Sales Return Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-2">
                                            <!-- Customer Details Section -->
                                            <div class="col-12 mb-3">
                                                <h6 class="border-bottom pb-2">Customer Details</h6>
                                            </div>


                                            <div class="col-md-4 customer-details">
                                                <label for="customer_name" class="form-label">Customer</label>
                                                <select class="form-control form-control-sm" id="customer_name"
                                                    name="customer_name"
                                                    <?php echo ($sales_return->status != 4) ? 'disabled' : ''; ?>>
                                                    <?php $selectedCustomerName = '';
                                                    foreach ($customers as $customer) {
                                                        if ($customer->id == $sales_return->customer_id) {
                                                            $selectedCustomerName = $customer->customer_name;
                                                            break;
                                                        }
                                                    }
                                                    ?>
                                                    <!-- Display the selected customer name as the default option -->
                                                    <option value="<?= $sales_return->customer_id ?>"><?= $selectedCustomerName ?>
                                                    </option>
                                                    <?php foreach ($customers as $customer): ?>
                                                        <option value="<?= $customer->id ?>">
                                                            <?= $customer->customer_name ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="col-md-4 customer-details">
                                                <label for="customer_tin" class="form-label">TIN</label>
                                                <input type="text" class="form-control form-control-sm" id="customer_tin"
                                                    name="customer_tin" value="<?= htmlspecialchars($sales_return->customer_tin) ?>" disabled>
                                            </div>

                                            <div class="col-md-4 customer-details">
                                                <label for="customer_email" class="form-label">Email</label>
                                                <input type="text" class="form-control form-control-sm" id="customer_email"
                                                    name="customer_email"
                                                    value="<?= htmlspecialchars($sales_return->customer_email) ?>" disabled>
                                            </div>

                                            <div class="col-md-4 customer-details">
                                                <label for="billing_address" class="form-label">Billing Address</label>
                                                <input type="text" class="form-control form-control-sm" id="billing_address"
                                                    name="billing_address"
                                                    value="<?= htmlspecialchars($sales_return->billing_address) ?>" disabled>
                                            </div>

                                            <div class="col-md-4 customer-details">
                                                <label for="shipping_address" class="form-label">Shipping Address</label>
                                                <input type="text" class="form-control form-control-sm" id="shipping_address"
                                                    name="shipping_address"
                                                    value="<?= htmlspecialchars($sales_return->shipping_address) ?>" disabled>
                                            </div>

                                            <div class="col-md-4 customer-details">
                                                <label for="business_style" class="form-label">Business Style</label>
                                                <input type="text" class="form-control form-control-sm" id="business_style"
                                                    name="business_style"
                                                    value="<?= htmlspecialchars($sales_return->business_style) ?>" disabled>
                                            </div>

                                            <div class="col-md-4 customer-details">
                                                <label for="payment_method" class="form-label">Payment Method</label>
                                                <select class="form-select form-select-sm" id="payment_method" name="payment_method" 
                                                    <?= ($sales_return->status == 4) ? '' : 'disabled'; ?>>
                                                    <?php
                                                    // Array to prevent duplicates
                                                    $used_payment_methods = [];
                                                    $selected_payment_method = $sales_return->payment_method ?? ''; // Assuming this holds the selected payment method

                                                    // Payment Methods
                                                    foreach ($payment_methods as $payment_method):
                                                        if (!in_array($payment_method->payment_method_name, $used_payment_methods)):
                                                            $used_payment_methods[] = $payment_method->payment_method_name; // Track used payment methods
                                                    ?>
                                                            <option value="<?= htmlspecialchars($payment_method->payment_method_name) ?>"
                                                                <?= $payment_method->payment_method_name == $selected_payment_method ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($payment_method->payment_method_name) ?>
                                                            </option>
                                                    <?php
                                                        endif;
                                                    endforeach;
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-md-4 customer-details">
                                                <label for="location" class="form-label">Location</label>
                                                <select class="form-control form-control-sm" id="location" name="location" 
                                                    <?php if ($sales_return->status != 4) echo 'disabled'; ?>>
                                                    <?php
                                                        // Array to prevent duplicates
                                                        $used_locations = [];
                                                        $selected_location = $sales_return->location ?? ''; // Assuming this holds the selected location

                                                        // Locations
                                                        foreach ($locations as $location):
                                                            if (!in_array($location->name, $used_locations)):
                                                                $used_locations[] = $location->name; // Track used locations
                                                    ?>
                                                                <option value="<?= htmlspecialchars($location->id) ?>" 
                                                                        <?= $location->id == $selected_location ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($location->name) ?>
                                                                </option>
                                                    <?php
                                                            endif;
                                                        endforeach;
                                                    ?>
                                                </select>
                                            </div>

                                            <div class="col-md-4 customer-details">
                                                <label for="customer_po" class="form-label">Customer PO No.</label>
                                                <input type="text" class="form-control form-control-sm" id="customer_po" name="customer_po" 
                                                    value="<?= $sales_return->customer_po ?>" <?= ($sales_return->status == 4) ? '' : 'disabled'; ?>>
                                            </div>
                                        </div>


                                        <!-- sales_return Details Section -->
                                        <div class="col-12 mt-3 mb-3">
                                            <h6 class="border-bottom pb-2">Sales Return Information</h6>
                                        </div>

                                        <div class="row g-2">
                                            <div class="col-md-3 sales_return-details">
                                                <label for="sales_return_number" class="form-label">Sales Return Number</label>
                                                <input type="text" class="form-control form-control-sm" id="sales_return_number"
                                                    name="sales_return_number" placeholder="Enter sales_return #"
                                                    value="<?php echo htmlspecialchars($sales_return->status == 4 ? $newsales_returnNo : $sales_return->sales_return_number); ?>"
                                                    <?= ($sales_return->status == 4) ? 'readonly' : 'disabled'; ?>>
                                            </div>
                                            <?php if ($sales_return->status == 4): ?>
                                                <!-- Show editable form when status is 4 -->
                                                <div class="col-md-3 invoice-details">
                                                    <label for="sales_return_date" class="form-label">Sales Return Date</label>
                                                    <input type="date" class="form-control form-control-sm" id="sales_return_date" name="sales_return_date" value="<?php echo date('Y-m-d'); ?>" required>
                                                </div>

                                                <div class="col-md-3 invoice-details">
                                                    <label for="terms" class="form-label">Terms</label>
                                                    <select class="form-select form-select-sm" id="sales_return_terms" name="terms">
                                                        <option value="">Select Terms</option>
                                                        <?php
                                                        // The selected term, based on the term name from the invoice
                                                        $selected_term = $sales_return->terms ?? ''; // Assuming this holds the term name

                                                        // Array to prevent duplicate term names
                                                        $used_terms = [];
                                                        foreach ($terms as $term):
                                                            if (!in_array($term->id, $used_terms)): // Use term ID to prevent duplicates
                                                                $used_terms[] = $term->id; // Track used term IDs
                                                        ?>
                                                                <option value="<?= htmlspecialchars($term->term_name) ?>"
                                                                    data-days="<?= htmlspecialchars($term->term_days_due) ?>"
                                                                    <?= $term->term_name == $selected_term ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($term->term_name) ?>
                                                                </option>
                                                        <?php
                                                            endif;
                                                        endforeach;
                                                        ?>
                                                    </select>
                                                </div>

                                                <div class="col-md-3 invoice-details">
                                                    <label for="sales_return_due_date" class="form-label">Due Date</label>
                                                    <input type="date" class="form-control form-control-sm" id="sales_return_due_date" name="sales_return_due_date" required>
                                                </div>
                                            <?php else: ?>
                                                <!-- Show read-only form when status is not 4 -->
                                                <div class="col-md-3 invoice-details">
                                                    <label for="invoice_date" class="form-label">Sales Return Date</label>
                                                    <input type="date" class="form-control form-control-sm" id="sales_return_date"
                                                        name="sales_return_date" value="<?= $sales_return->sales_return_date ?>" disabled>
                                                </div>

                                                <div class="col-md-3 invoice-details">
                                                    <label for="invoice_due_date" class="form-label">Due Date</label>
                                                    <input type="date" class="form-control form-control-sm" id="sales_return_due_date"
                                                        name="sales_return_due_date" value="<?= $sales_return->sales_return_due_date ?>" disabled>
                                                </div>

                                                <div class="col-md-3 invoice-details">
                                                    <label for="terms" class="form-label">Terms</label>
                                                    <select class="form-control form-control-sm" id="sales_return_terms" name="terms" disabled>
                                                        <option value="<?= htmlspecialchars($sales_return->terms) ?>" selected>
                                                            <?= htmlspecialchars($sales_return->terms) ?>
                                                        </option>
                                                    </select>
                                                </div>
                                            <?php endif; ?>  <!-- draft end -->




                                            <div class="col-md-3 sales_return-details">
                                                <label for="sales_return_account_id" class="form-label">Account</label>
                                                <select class="form-select form-select-sm" id="sales_return_account_id"
                                                    name="sales_return_account_id" <?= ($sales_return->status == 4) ? '' : 'disabled'; ?>>
                                                    <option value="">Select Account</option>
                                                    <!-- Populate with account options -->
                                                </select>
                                            </div>

                                            <div class="col-md-3 sales_return-details">
                                                <label for="rep" class="form-label">Rep</label>
                                                <input type="text" class="form-control form-control-sm" id="rep" name="rep"
                                                    value="<?= $sales_return->rep ?>" <?= ($sales_return->status == 4) ? '' : 'disabled'; ?>>
                                            </div>

                                            <div class="col-md-3 sales_return-details">
                                                <label for="so_no" class="form-label">S.O No.</label>
                                                <input type="text" class="form-control form-control-sm" id="so_no" name="so_no"
                                                    value="<?= $sales_return->so_no ?>" <?= ($sales_return->status == 4) ? '' : 'disabled'; ?>>
                                            </div>

                                            <div class="col-md-3 sales_return-details">
                                                <label for="memo" class="form-label">Memo</label>
                                                <input type="text" class="form-control form-control-sm" id="memo" name="memo"
                                                    value="<?= $sales_return->memo ?>" <?= ($sales_return->status == 4) ? '' : 'disabled'; ?>>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- sales_return DETAILS -->
                            <div class="col-12 col-lg-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title">Summary</h5>
                                        <?php if ($sales_return->status == 0): ?>
                                            <span class="badge bg-danger">Unpaid</span>
                                        <?php elseif ($sales_return->status == 1): ?>
                                            <span class="badge bg-success">Paid</span>
                                        <?php elseif ($sales_return->status == 2): ?>
                                            <span class="badge bg-warning">Partially Paid</span>
                                        <?php elseif ($sales_return->status == 3): ?>
                                        <span class="badge bg-secondary">Void</span>
                                        <?php elseif ($sales_return->status == 4): ?>
                                            <span class="badge bg-info text-dark">Draft</span>
                                        <?php endif; ?>
                                    </div>

                                    <div class="card-body">

                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="cash_sales" name="cash_sales">
                                            <label class="form-check-label fw-bold" for="cash_sales">Cash Sales</label>
                                            <span id="cash_sales_text"></span> <!-- This span will show the label text -->
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Gross Amount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="gross_amount"
                                                    name="gross_amount" 
                                                    value="<?= number_format($sales_return->gross_amount, 2, '.', ',') ?>" readonly>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Discount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="discount_amount"
                                                    name="discount_amount" 
                                                    value="<?= number_format($sales_return->discount_amount, 2, '.', ',') ?>" readonly>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Net Amount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="net_amount_due"
                                                    value="<?= number_format($sales_return->net_amount_due, 2, '.', ',') ?>" readonly>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">VAT:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="vat_amount"
                                                    value="<?= number_format($sales_return->vat_amount, 2, '.', ',') ?>" readonly>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Vatable 12%:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="vatable_amount"
                                                    value="<?= number_format($sales_return->vatable_amount, 2, '.', ',') ?>" readonly>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Zero-rated:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end"
                                                    id="zero_rated_amount" name="zero_rated_amount"
                                                    value="<?= number_format($sales_return->zero_rated_amount, 2, '.', ',') ?>" readonly>

                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Vat-Exempt:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end"
                                                    id="vat_exempt_amount" name="vat_exempt_amount"
                                                    value="<?= number_format($sales_return->vat_exempt_amount, 2, '.', ',') ?>" readonly>

                                            </div>
                                        </div>


                                        <?php if ($sales_return->status == 4): ?>
                                            <!-- Show editable Tax Withheld dropdown when status is 4 -->
                                            <div class="row">
                                                <label class="col-sm-6 col-form-label">Tax Withheld (%):</label>
                                                <div class="col-sm-6">
                                                    <select class="form-select form-select-sm" id="tax_withheld_percentage" name="tax_withheld_percentage">
                                                    <?php
                                                        // Array to prevent duplicates
                                                        $used_wtaxes = [];
                                                        foreach ($wtaxes as $wtax):
                                                            if (!in_array($wtax->id, $used_wtaxes)):
                                                                $used_wtaxes[] = $wtax->id; // Track used tax rates
                                                        ?>
                                                                <option value="<?= htmlspecialchars($wtax->id) ?>"
                                                                    data-account-id="<?= htmlspecialchars($wtax->wtax_account_id) ?>"
                                                                    data-rate="<?= htmlspecialchars($wtax->wtax_rate) ?>"
                                                                    <?= $wtax->id == $sales_return->tax_withheld_percentage ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($wtax->wtax_name) ?>
                                                                </option>
                                                        <?php
                                                            endif;
                                                        endforeach;
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <!-- Show disabled Tax Withheld dropdown when status is not 4 -->
                                            <div class="row">
                                                <label class="col-sm-6 col-form-label" for="tax_withheld_percentage">Tax Withheld (%):</label>
                                                <div class="col-sm-6">
                                                    <select class="form-control form-control-sm" id="tax_withheld_percentage" name="tax_withheld_percentage" disabled>
                                                        <option value="">Select Tax Withheld</option>
                                                        <?php
                                                        // Array to prevent duplicates
                                                        $used_wtaxes = [];
                                                        foreach ($wtaxes as $wtax):
                                                            if (!in_array($wtax->id, $used_wtaxes)):
                                                                $used_wtaxes[] = $wtax->id; // Track used tax rates
                                                        ?>
                                                            <option value="<?= htmlspecialchars($wtax->id) ?>" 
                                                                    data-account-id="<?= htmlspecialchars($wtax->wtax_account_id) ?>"
                                                                    <?= $wtax->id == $sales_return->tax_withheld_percentage ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($wtax->wtax_name) ?>
                                                            </option>
                                                        <?php
                                                            endif;
                                                        endforeach;
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Tax Withheld Amount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end"
                                                    id="tax_withheld_amount" name="tax_withheld_amount"   value="<?= number_format($sales_return->tax_withheld_amount, 2, '.', ',') ?>" readonly>
                                                <input type="hidden" class="form-control" name="tax_withheld_account_id"
                                                    id="tax_withheld_account_id" >
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label fw-bold">Total Amount Due:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end fw-bold"
                                                    id="total_amount_due" name="total_amount_due"
                                                    value="<?= number_format($sales_return->total_amount_due, 2, '.', ',') ?>" readonly>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="card-footer d-flex justify-content-center">
                                        <?php if ($sales_return->status == 4): ?>
                                            <!-- Buttons to show when sales_return_status is 4 -->
                                            <button type="button" id="saveDraftBtn" class="btn btn-secondary me-2">Update Draft</button>
                                            <button type="submit" class="btn btn-info me-2">Save as Final</button>
                                        <?php elseif ($sales_return->status == 3): ?>
                                            <!-- Button to show when sales_return_status is 3 -->
                                            <a class="btn btn-primary" href="#" id="reprintButton">
                                                <i class="fas fa-print"></i> Reprint
                                            </a>
                                        <?php else: ?>
                                            <!-- Buttons to show when sales_return_status is neither 3 nor 4 -->
                                            <button type="button" class="btn btn-secondary me-2" id="voidButton">Void</button>
                                            <a class="btn btn-primary" href="#" id="reprintButton">
                                                <i class="fas fa-print"></i> Reprint
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- sales_return ITEMS -->
                        <div class="row mt-4">
                            <!-- sales_return ITEMS -->
                            <div class="col-12 col-lg-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Sales Return Items</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover" id="itemTable">
                                                <thead class="bg-light" style="font-size: 12px;">
                                                    <tr>
                                                        <th style="width: 15%;">Item</th>
                                                        <th style="width: 20%;">Description</th>
                                                        <th style="width: 8%;">U/M</th>
                                                        <th class="text-right" style="width: 3%;">Quantity</th>
                                                        <th class="text-right" style="width: 8%;">Cost</th>
                                                        <th class="text-right" style="width: 8%;">Amount</th>
                                                        <th style="width: 5%;">Disc Type</th>
                                                        <th class="text-right" style="width: 8%;">Discount</th>
                                                        <th class="text-right" style="width: 8%;">Net</th>
                                                        <th class="text-right" style="width: 8%;">Tax Amount</th>
                                                        <th style="width: 5%;">Tax Type</th>
                                                        <th class="text-right" style="width: 10%;">VAT</th>
                                                        <th style="width: 4%;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="itemTableBody" style="font-size: 14px;">
                                                <?php
                                                    if ($sales_return) {
                                                        foreach ($sales_return->details as $detail) {
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <select class="form-control form-control-sm item-dropdown select2"
                                                                        name="item_id[]" <?= ($sales_return->status == 4) ? '' : 'disabled'; ?>>
                                                                        <?php foreach ($products as $product): ?>
                                                                            <option value="<?= htmlspecialchars($product->id) ?>"
                                                                            <?= ($product->id == $detail['item_id']) ? 'selected' : '' ?>
                                                                            data-item-name="<?= htmlspecialchars($product->item_name) ?>"
                                                                            data-description="<?= htmlspecialchars($product->item_sales_description) ?>"
                                                                            data-uom="<?= htmlspecialchars($product->uom_name) ?>"
                                                                            data-cost-price="<?= htmlspecialchars($product->item_cost_price) ?>"
                                                                            data-cogs-account-id="<?= htmlspecialchars($product->item_cogs_account_id) ?>"
                                                                            data-income-account-id="<?= htmlspecialchars($product->item_income_account_id) ?>"
                                                                            data-asset-account-id="<?= htmlspecialchars($product->item_asset_account_id) ?>">
                                                                            <?= htmlspecialchars($product->item_name) ?>
                                                                        </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </td>
                                                                <td style="display: none;">
                                                                    <input type="hidden" class="item-cogs-account-id" name="cogs_account_id[]" value="<?= htmlspecialchars($detail['cogs_account_id']) ?>">
                                                                    <input type="hidden" class="item-income-account-id" name="income_account_id[]" value="<?= htmlspecialchars($detail['income_account_id']) ?>">
                                                                    <input type="hidden" class="item-asset-account-id" name="asset_account_id[]" value="<?= htmlspecialchars($detail['asset_account_id']) ?>">
                                                                </td>
                                                                <td>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm item_sales_description"
                                                                        name="item_sales_description[]"
                                                                        value="<?= htmlspecialchars($detail['item_sales_description']) ?>"readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm uom_name"
                                                                        name="uom_name[]"
                                                                        value="<?= htmlspecialchars($detail['uom_name']) ?>"readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm quantity"
                                                                        name="quantity[]"
                                                                        value="<?= htmlspecialchars($detail['quantity']) ?>"
                                                                        placeholder="Qty" <?= ($sales_return->status == 4) ? '' : 'disabled'; ?>>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm cost"
                                                                        name="cost[]" 
                                                                        value="<?= number_format($detail['cost'], 2, '.', ',') ?>" <?= ($sales_return->status == 4) ? '' : 'readonly'; ?>>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm amount"
                                                                        name="amount[]"
                                                                        value="<?= number_format($detail['amount'], 2, '.', ',') ?>" readonly>
                                                                </td>
                                                                <td>
                                                                    <select
                                                                        class="form-control form-control-sm discount-dropdown select2"
                                                                        name="discount_percentage[]" <?= ($sales_return->status == 4) ? '' : 'disabled'; ?>>
                                                                        <?php foreach ($discounts as $discount): ?>
                                                                            <option value="<?= htmlspecialchars($discount->discount_rate) ?>" data-account-id="<?= htmlspecialchars($discount->discount_account_id) ?>" <?= ($discount->discount_rate == $detail['discount_percentage']) ? 'selected' : '' ?>>
                                                                        <?= htmlspecialchars($discount->discount_name) ?>
                                                                    </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm discount_amount"
                                                                        name="discount_amount[]"
                                                                        value="<?= number_format($detail['discount_amount'], 2, '.', ',') ?>"readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm net_amount_before_sales_tax"
                                                                        name="net_amount_before_sales_tax[]"
                                                                        value="<?= number_format($detail['net_amount_before_sales_tax'], 2, '.', ',') ?>"readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm net_amount"
                                                                        name="net_amount[]"
                                                                        value="<?= number_format($detail['net_amount'], 2, '.', ',') ?>"readonly>
                                                                </td>
                                                                <td>
                                                                    <select class="form-control form-control-sm sales_tax_percentage select2" name="sales_tax_percentage[]" <?php echo ($sales_return->status != 4) ? 'disabled' : ''; ?>>
                                                                        <?php
                                                                        // Array to prevent duplicates
                                                                        $used_vat_rates = [];
                                                                        foreach ($sales_taxes as $vat):
                                                                            if (!in_array($vat->id, $used_vat_rates)):
                                                                                $used_vat_rates[] = $vat->id; // Track used VAT rates
                                                                        ?>
                                                                                <option value="<?= htmlspecialchars($vat->sales_tax_rate) ?>"
                                                                                    data-account-id="<?= htmlspecialchars($vat->sales_tax_account_id) ?>"
                                                                                    <?= $vat->sales_tax_rate == $detail['sales_tax_percentage'] ? 'selected' : '' ?>>
                                                                                    <?= htmlspecialchars($vat->sales_tax_name) ?>
                                                                                </option>
                                                                        <?php
                                                                            endif;
                                                                        endforeach;
                                                                        ?>
                                                                    </select>
                                                                </td>
                                                                <td style="display: none;"><input type="hidden" class="item-cogs-output_vat_id-id" name="output_vat_id[]" value="<?= htmlspecialchars($detail['output_vat_id']) ?>"></td>
                                                                <td>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm sales_tax_amount"
                                                                        name="sales_tax_amount[]"
                                                                        value="<?= number_format($detail['sales_tax_amount'], 2, '.', ',') ?>"readonly>
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-sm btn-danger removeRow" <?= ($sales_return->status == 4) ? '' : 'disabled'; ?>>
                                                                        <i class="fas fa-trash"></i>
                                                                    </button>
                                                                </td>
                                                            </tr>
                                                            <?php
                                                        }
                                                    }
                                                ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php if ($sales_return->status == 4): ?>
                                            <button type="button" class="btn btn-primary btn-sm" id="addItemBtn">
                                                <i class="fas fa-plus"></i> Add Item
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <?php
                    // sales_return found, you can now display the details
                } else {
                    // Handle the case where the sales_return is not found
                    echo "sales_return not found.";
                    exit;
                }
            } else {
                // Handle the case where the ID is not provided
                echo "No ID provided.";
                exit;
            }
            ?>
        </div>
    </main>
</div>

<iframe id="printFrame" style="display:none;"></iframe>
<div id="loadingOverlay" class="loading-overlay">
    <div class="spinner"></div>
</div>

<?php require 'views/templates/footer.php' ?>

<iframe id="printFrame" style="display:none;"></iframe>

<script>
    document.getElementById('sales_return_terms').addEventListener('change', function() {
        var selectedTerm = this.options[this.selectedIndex];
        var daysDue = parseInt(selectedTerm.getAttribute('data-days'));

        var invoiceDate = document.getElementById('sales_return_date').value;

        if (invoiceDate && !isNaN(daysDue)) {
            var dueDate = new Date(invoiceDate);

            // Add the days due to the invoice date
            if (daysDue > 0) {
                dueDate.setDate(dueDate.getDate() + daysDue);
            }

            // Format the date to 'YYYY-MM-DD'
            var year = dueDate.getFullYear();
            var month = ('0' + (dueDate.getMonth() + 1)).slice(-2);
            var day = ('0' + dueDate.getDate()).slice(-2);

            // Set the value of the invoice due date input field
            document.getElementById('sales_return_due_date').value = `${year}-${month}-${day}`;

            console.log("Updated Due Date:", `${year}-${month}-${day}`);
        } else {
            console.log("Invalid Invoice Date or Days Due.");
        }
    });

    // Trigger change event on page load if a term is selected
    window.addEventListener('load', function() {
        var selectedTerm = document.getElementById('sales_return_terms');
        if (selectedTerm.value) {
            selectedTerm.dispatchEvent(new Event('change'));
        }
    });
</script>
<script>
    document.addEventListener("DOMContentLoaded", function () {

        var cashSalesSwitch = document.getElementById('cash_sales');
        var salesReturnAccountSelect = document.getElementById('sales_return_account_id');
        var cashSalesText = document.getElementById('cash_sales_text');
        var accounts = <?php echo json_encode($accounts); ?>;

        // Store the initial account ID
        var initialAccountId = '<?php echo $sales_return->sales_return_account_id; ?>';

        // Function to update label text based on checkbox state
        function updateLabel() {
            var isCashSales = cashSalesSwitch.checked;
            var labelText = isCashSales ? "Cash Sales - Sales Receipt" : "Sales Return";
            cashSalesText.innerHTML = '&nbsp;&nbsp;' + labelText;
        }

        // Function to populate dropdown based on checkbox state
        function updateDropdown() {
            var isCashSales = cashSalesSwitch.checked;

            // Clear existing options
            salesReturnAccountSelect.innerHTML = '';

            // Populate options based on account type and description
            accounts.forEach(function (account) {
                var showOption = false;

                // Determine which options to show based on checkbox state
                if (isCashSales) {
                    if (account.account_description === "Undeposited Funds" || account.account_type === "Bank") {
                        showOption = true;
                    }
                } else {
                    if (account.account_type === "Accounts Receivable") {
                        showOption = true;
                    }
                }

                if (showOption) {
                    var option = document.createElement('option');
                    option.value = account.id;
                    option.setAttribute('data-account-type', account.account_type);
                    option.textContent = account.account_description;
                    salesReturnAccountSelect.appendChild(option);

                    // Select this option if it matches the initial account ID
                    if (account.id == initialAccountId) {
                        option.selected = true;
                    }
                }
            });
        }

        // Function to set initial checkbox state and update dropdown
        function setInitialState() {
            var initialAccount = accounts.find(account => account.id == initialAccountId);
            if (initialAccount) {
                // Determine initial checkbox state based on the initial account
                cashSalesSwitch.checked = initialAccount.account_description === "Undeposited Funds" || initialAccount.account_type === "Bank";
            } else {
                // If no initial account is found, default to unchecked (Sales Return)
                cashSalesSwitch.checked = false;
            }

            updateLabel();
            updateDropdown();
        }

        // Initialize setup
        setInitialState();

        // Event listener for checkbox change
        cashSalesSwitch.addEventListener('change', function () {
            updateLabel();
            updateDropdown();
        });

        // Tax withheld select and account ID input
        const taxWithheldSelect = document.getElementById('tax_withheld_percentage');
        const taxWithheldAccountIdInput = document.getElementById('tax_withheld_account_id');

        taxWithheldSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const accountId = selectedOption.getAttribute('data-account-id');
            taxWithheldAccountIdInput.value = accountId;
            console.log(accountId); // For debugging
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('reprintButton').addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Reprint sales_return?',
                text: "Are you sure you want to reprint this sales_return?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reprint it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    printsales_return(<?= $sales_return->id ?>, 2);  // Pass 2 for reprint
                }
            });
        });

        // Attach event listener for the void button
        document.getElementById('voidButton').addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Void sales_return?',
                text: "Are you sure you want to void this sales_return? This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, void it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    voidCheck(<?= $sales_return->id ?>);
                }
            });
        });
    });

    function showLoadingOverlay() {
        document.getElementById('loadingOverlay').style.display = 'flex';
    }

    function hideLoadingOverlay() {
        document.getElementById('loadingOverlay').style.display = 'none';
    }

    function printsales_return(id, printStatus) {
        showLoadingOverlay();

        $.ajax({
            url: 'api/sales_return_controller.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'update_print_status',
                sales_return_id: id,
                print_status: printStatus
            },
            success: function (response) {
                if (response.success) {
                    const printFrame = document.getElementById('printFrame');
                    const printContentUrl = `print_sales_return?action=print&id=${id}`;

                    printFrame.src = printContentUrl;

                    printFrame.onload = function () {
                        printFrame.contentWindow.focus();
                        printFrame.contentWindow.print();
                        hideLoadingOverlay();
                    };
                } else {
                    hideLoadingOverlay();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update print status: ' + (response.message || 'Unknown error')
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                hideLoadingOverlay();
                console.error('AJAX error:', textStatus, errorThrown);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating print status: ' + textStatus
                });
            }
        });
    }

    function voidCheck(id) {
        showLoadingOverlay(); // Show the loading overlay before making the request

        $.ajax({
            url: 'api/sales_return_controller.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'void_check',
                id: id
            },
            success: function (response) {
                hideLoadingOverlay(); // Hide the loading overlay on success
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'sales_return has been voided successfully.'
                    }).then(() => {
                        location.reload(); // Reload the page to reflect changes
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to void sales_return: ' + (response.message || 'Unknown error')
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                hideLoadingOverlay(); // Hide the loading overlay on error
                console.error('AJAX error:', textStatus, errorThrown);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while voiding the sales_return: ' + textStatus
                });
            }
        });
    }
</script>
<!-- sales_tax_return view -->
<script>
    $(document).ready(function () {

        const selectedTaxWithheld = $("#tax_withheld_percentage option:selected");
        const taxWithheldAccountId = selectedTaxWithheld.data('account-id');
        $("#tax_withheld_account_id").val(taxWithheldAccountId);

        $(document).ready(function () {
            // Initialize Select2 for existing dropdowns in the table
            $('#itemTableBody').find('.select2').select2({
                width: '100%',
                theme: 'classic' // Adjust this if using a different Bootstrap version
            });
        });

        $('#customer_name').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Customer',
            allowClear: true
        });

        $('#customer_id').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Customer',
            allowClear: false
        });

        $('#payment_method').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Payment',
            allowClear: false
        });

        $('#location').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Location',
            allowClear: false
        });

        $('#sales_return_terms').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Terms',
            allowClear: false
        });

        $('#sales_return_account_id').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: '',
            allowClear: false
        });

        // Handle customer change
        $('#customer_name').change(function () {
            var customerId = $(this).val();
            var customers = <?= json_encode($customers); ?>;
            var selectedCustomer = customers.find(customer => customer.id == customerId);

            if (selectedCustomer) {
                $('#customer_tin').val(selectedCustomer.customer_tin);
                $('#billing_address').val(selectedCustomer.billing_address);
                $('#customer_email').val(selectedCustomer.customer_email);
                $('#shipping_address').val(selectedCustomer.shipping_address);
                $('#business_style').val(selectedCustomer.business_style);
            } else {
                $('#customer_tin').val('');
                $('#billing_address').val('');
                $('#customer_email').val('');
                $('#shipping_address').val('');
                $('#business_style').val('');
            }
        });


        // Handle sales_return terms change
        $('#sales_return_terms').change(function () {
            var terms = $(this).val();
            var deliveryDate = calculateDeliveryDate(terms);
            $('#sales_return_due_date').val(deliveryDate);
        });

        function calculateDeliveryDate(terms) {
            var currentDate = new Date();
            var deliveryDate = new Date(currentDate);
            if (terms === 'Due on Receipt') {
                return currentDate.toISOString().split('T')[0];
            } else {
                var daysToAdd = parseInt(terms.replace('NET ', ''), 10);
                deliveryDate.setDate(deliveryDate.getDate() + daysToAdd);
                return deliveryDate.toISOString().split('T')[0];
            }
        }

        // Initialize dropdown options
        function initDropdowns(data, template) {
            return data.reduce((acc, item) => acc + template(item), '');
        }

        const itemDropdownOptions = initDropdowns(<?= json_encode($products); ?>,
            product => `<option value="${product.id}"
                data-item-name="${product.item_name}"
                data-description="${product.item_sales_description}"
                data-uom="${product.uom_name}"
                data-cost-price="${product.item_cost_price}"
                data-cogs-account-id="${product.item_cogs_account_id}"
                data-income-account-id="${product.item_income_account_id}"
                data-asset-account-id="${product.item_asset_account_id}">
                ${product.item_name}
            </option>`
        );

        const discountDropdownOptions = initDropdowns(<?= json_encode($discounts); ?>,
            discount => `<option value="${discount.discount_rate}" data-account-id="${discount.discount_account_id}">${discount.discount_name}</option>`
        );

        const inputVatDropdownOption = initDropdowns(<?= json_encode($sales_taxes); ?>,
            tax => `<option value="${tax.sales_tax_rate}" data-account-id="${tax.sales_tax_account_id}">${tax.sales_tax_name}</option>`
        );

        // Add new row
        $('#addItemBtn').click(() => {
            const newRow = `
                <tr>
                    <td>
                        <select class="form-control form-control-sm item-dropdown select2" name="item_id[]">${itemDropdownOptions}</select>
                    </td>
                    <input type="hidden" class="item-name" name="item_name[]" value="">
                    <input type="hidden" class="item-cost-price" name="cost_price[]" value="">
                    <input type="hidden" class="item-cogs-account-id" name="item_cogs_account_id[]" value="">
                    <input type="hidden" class="item-income-account-id" name="item_income_account_id[]" value="">
                    <input type="hidden" class="item-asset-account-id" name="item_asset_account_id[]" value="">
                    <td><input type="text" class="form-control form-control-sm item_sales_description" name="item_sales_description[]" readonly></td>
                    <td><input type="text" class="form-control form-control-sm uom_name" name="uom_name[]" readonly></td>
                    <td><input type="text" class="form-control form-control-sm quantity" name="quantity[]" placeholder="Qty"></td>
                    <td><input type="text" class="form-control form-control-sm cost" name="cost[]" placeholder="Enter Cost"></td>
                    <td><input type="text" class="form-control form-control-sm amount" name="amount[]" placeholder="Amount" readonly></td>
                    <td>
                        <select class="form-control form-control-sm discount-dropdown select2" name="discount_percentage[]">${discountDropdownOptions}</select>
                    </td>
                    <td><input type="text" class="form-control form-control-sm discount_amount" name="discount_amount[]" readonly></td>
                    <td><input type="text" class="form-control form-control-sm net_amount_before_sales_tax" name="net_amount_before_sales_tax[]" readonly></td>
                    <td><input type="text" class="form-control form-control-sm net_amount" name="net_amount[]" readonly></td>
                    <td>
                        <select class="form-control form-control-sm sales_tax_percentage select2" name="sales_tax_percentage[]">${inputVatDropdownOption}</select>
                    </td>
                    <td><input type="text" class="form-control form-control-sm sales_tax_amount" name="sales_tax_amount[]" readonly></td>
                    <td><button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-trash"></i></button></td>
                </tr>`;
            $('#itemTableBody').append(newRow);

            // Initialize Select2 for the new row
            $('#itemTableBody tr:last-child').find('.select2').select2({
                width: '100%',
                theme: 'classic'
            });
            

            // Bind events for the new row
            bindRowEvents($('#itemTableBody tr:last-child'));

            // Initialize the new row with default values
            calculateRowValues($('#itemTableBody tr:last-child'));
            calculateTotalAmount();
        });

        // Gather table items and submit form
        function gatherTableItems() {
            const items = [];
            $('#itemTableBody tr').each(function () {
                const item = {
                    item_id: $(this).find('select[name="item_id[]"]').val(),
                    quantity: parseFloat($(this).find('input[name="quantity[]"]').val().replace(/,/g, '')) || 0,
                    cost: parseFloat($(this).find('input[name="cost[]"]').val().replace(/,/g, '')) || 0,
                    amount: $(this).find('input[name="amount[]"]').data('raw-value') || 0,
                    discount_percentage: parseFloat($(this).find('select[name="discount_percentage[]"]').val()) || 0,
                    discount_amount: $(this).find('input[name="discount_amount[]"]').data('raw-value') || 0,
                    discount_account_id: $(this).find('select[name="discount_percentage[]"] option:selected').data('account-id'),
                    net_amount_before_sales_tax: $(this).find('input[name="net_amount_before_sales_tax[]"]').data('raw-value') || 0,
                    net_amount: $(this).find('input[name="net_amount[]"]').data('raw-value') || 0,
                    sales_tax_percentage: parseFloat($(this).find('select[name="sales_tax_percentage[]"]').val()) || 0,
                    sales_tax_amount: $(this).find('input[name="sales_tax_amount[]"]').data('raw-value') || 0,
                    input_vat_id: $(this).find('select[name="sales_tax_percentage[]"] option:selected').data('account-id'),
                    output_vat_id: $(this).find('.sales_tax_percentage option:selected').data('account-id'),
                    cogs_account_id: $(this).find('.item-cogs-account-id').val(),
                    income_account_id: $(this).find('.item-income-account-id').val(),
                    asset_account_id: $(this).find('.item-asset-account-id').val()
                };
                items.push(item);
            });
            return items;
        }

        $('#receiveItemForm').submit(function (event) {
            event.preventDefault();
            
            // Check if the table has any rows
            if ($('#itemTableBody tr').length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Items',
                    text: 'You must add at least one item before submitting the sales_return.'
                });
                document.getElementById('loadingOverlay').style.display = 'none';
                return false;
            }

            const items = gatherTableItems();
            $('#item_data').val(JSON.stringify(items));

            const status = <?= json_encode($sales_return->status) ?>;
            const sales_returnId = <?= json_encode($sales_return->id) ?>;
            const sales_returnAccountId = <?= json_encode(value: $sales_return->sales_return_account_id) ?>;
            const customerId = <?= json_encode($sales_return->customer_id) ?>;
            const balance_due = <?= json_encode($sales_return->total_amount_due) ?>;
            const tax_withheld_percentage = <?= json_encode($sales_return->tax_withheld_percentage) ?>;
            const tax_withheld_amount = <?= json_encode($sales_return->tax_withheld_amount) ?>;
            const discount_amount = <?= json_encode($sales_return->discount_amount) ?>;
            const gross_amount = <?= json_encode($sales_return->gross_amount) ?>;

            console.log(sales_returnId);

            if (status == 4) {
                // Show loading overlay
                document.getElementById('loadingOverlay').style.display = 'flex';

                $.ajax({
                    url: 'api/sales_return_controller.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'save_final',
                        id: sales_returnId,
                        sales_return_number: $('#sales_return_number').val(),
                        sales_return_account_id: $('#sales_return_account_id').val(),
                        customer_id: $('#customer_name').val(),
                        total_amount_due: $('#total_amount_due').data('raw-value'),
                        gross_amount: $('#gross_amount').data('raw-value'),
                        discount_amount: $('#discount_amount').data('raw-value'),
                        net_amount_due: $('#net_amount_due').data('raw-value'),
                        vat_amount: $('#vat_amount').data('raw-value'),
                        zero_rated_amount: $('#zero_rated_amount').data('raw-value'),
                        vat_exempt_amount: $('#vat_exempt_amount').data('raw-value'),
                        vatable_amount: $('#vatable_amount').data('raw-value'),
                        tax_withheld_percentage: $('#tax_withheld_percentage').val(),
                        tax_withheld_amount: $('#tax_withheld_amount').data('raw-value'),
                        tax_withheld_account_id: $('#tax_withheld_account_id').val(),
                        sales_return_date: $('#sales_return_date').val(),
                        sales_return_due_date: $('#sales_return_due_date').val(),
                        customer_po: $('#customer_po').val(),
                        so_no: $('#so_no').val(),
                        rep: $('#rep').val(),
                        payment_method: $('#payment_method').val(),
                        location: $('#location').val(),
                        terms: $('#sales_return_terms').val(),
                        memo: $('#memo').val(),
                        item_data: JSON.stringify(gatherTableItems())
                    },
                    success: function (response) {
                        document.getElementById('loadingOverlay').style.display = 'none';

                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'sales_return submitted successfully!',
                                showCancelButton: true,
                                confirmButtonText: 'Print',
                                cancelButtonText: 'Save as PDF'
                            }).then((result) => {
                                if (result.isConfirmed && response.sales_returnId) {
                                    printsales_return(response.sales_returnId, 1); // Pass 1 for initial print
                                } else {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error saving sales_return: ' + (response.message || 'Unknown error')
                            });
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        document.getElementById('loadingOverlay').style.display = 'none';
                        console.error('AJAX error:', textStatus, errorThrown);
                        console.log('Response Text:', jqXHR.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while saving the sales_return: ' + textStatus
                        });
                    }
                });
            }
        });

        $('#saveDraftBtn').click(function(event) {
            event.preventDefault();

            // Check if the table has any rows
            if ($('#itemTableBody tr').length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Items',
                    text: 'You must add at least one item before updating the draft.'
                });
                return false;
            }

            const items = gatherTableItems();
            $('#item_data').val(JSON.stringify(items));

            const status = <?= json_encode($sales_return->status) ?>;
            const sales_returnId = <?= json_encode($sales_return->id) ?>;
            const sales_returnAccountId = <?= json_encode(value: $sales_return->sales_return_account_id) ?>;
            const customerId = <?= json_encode($sales_return->customer_id) ?>;
            const balance_due = <?= json_encode($sales_return->total_amount_due) ?>;
            const tax_withheld_percentage = <?= json_encode($sales_return->tax_withheld_percentage) ?>;
            const tax_withheld_amount = <?= json_encode($sales_return->tax_withheld_amount) ?>;
            const discount_amount = <?= json_encode($sales_return->discount_amount) ?>;
            const gross_amount = <?= json_encode($sales_return->gross_amount) ?>;
            if (status == 4) {
                // Show loading overlay
                document.getElementById('loadingOverlay').style.display = 'flex';

                $.ajax({
                    url: 'api/sales_return_controller.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'update_draft',
                        id: sales_returnId,
                        sales_return_account_id: $('#sales_return_account_id').val(),
                        customer_id: $('#customer_name').val(),
                        total_amount_due: $('#total_amount_due').data('raw-value'),
                        gross_amount: $('#gross_amount').data('raw-value'),
                        discount_amount: $('#discount_amount').data('raw-value'),
                        net_amount_due: $('#net_amount_due').data('raw-value'),
                        vat_amount: $('#vat_amount').data('raw-value'),
                        zero_rated_amount: $('#zero_rated_amount').data('raw-value'),
                        vat_exempt_amount: $('#vat_exempt_amount').data('raw-value'),
                        vatable_amount: $('#vatable_amount').data('raw-value'),
                        tax_withheld_percentage: $('#tax_withheld_percentage').val(),
                        tax_withheld_amount: $('#tax_withheld_amount').data('raw-value'),
                        tax_withheld_account_id: $('#tax_withheld_account_id').val(),
                        sales_return_date: $('#sales_return_date').val(),
                        sales_return_due_date: $('#sales_return_due_date').val(),
                        customer_po: $('#customer_po').val(),
                        so_no: $('#so_no').val(),
                        rep: $('#rep').val(),
                        payment_method: $('#payment_method').val(),
                        location: $('#location').val(),
                        terms: $('#sales_return_terms').val(),
                        memo: $('#memo').val(),
                        item_data: JSON.stringify(gatherTableItems())
                    },
                    success: function(response) {
                        document.getElementById('loadingOverlay').style.display = 'none';

                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Draft updated successfully!',
                                showCancelButton: true,
                                cancelButtonText: 'Close'
                            }).then((result) => {
                                if (result.isConfirmed && response.sales_returnId) {
                                    saveAsPDF(response.sales_returnId); // Assuming you have a saveAsPDF function
                                } else {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error updating draft: ' + (response.message || 'Unknown error')
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        document.getElementById('loadingOverlay').style.display = 'none';
                        console.error('AJAX error:', textStatus, errorThrown);
                        console.log('Response Text:', jqXHR.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while updating the draft: ' + textStatus
                        });
                    }
                });
            }
        });
    });


    $(document).ready(function () {
        // Initialize Select2 for existing dropdowns in the table
        $('#itemTableBody').find('.select2').select2({
            width: '100%',
            theme: 'classic'
        });

        // Initialize existing rows
        $('#itemTableBody tr').each(function () {
            bindRowEvents($(this));
            calculateRowValues($(this));
        });

        calculateTotalAmount();
    });

    // Update the populateFields function to set the account IDs
    function populateFields(select) {
        const selectedOption = $(select).find('option:selected');
        const row = $(select).closest('tr');

        const itemName = selectedOption.data('item-name');
        const description = selectedOption.data('description');
        const uom = selectedOption.data('uom');
        const costPrice = selectedOption.data('cost-price');
        const cogsAccountId = selectedOption.data('cogs-account-id');
        const incomeAccountId = selectedOption.data('income-account-id');
        const assetAccountId = selectedOption.data('asset-account-id');

        row.find('.item_sales_description').val(description);
        row.find('.uom_name').val(uom);
        row.find('.cost').val(costPrice);
        row.find('.item-cogs-account-id').val(cogsAccountId);
        row.find('.item-income-account-id').val(incomeAccountId);
        row.find('.item-asset-account-id').val(assetAccountId);

        calculateRowValues(row);
        calculateTotalAmount();
    }
   
    function bindRowEvents(row) {
        row.find('.item-dropdown, .quantity, .cost, .discount-dropdown, .sales_tax_percentage').on('input change', function () {
            calculateRowValues($(this).closest('tr'));
            calculateTotalAmount();
        });

        row.find('.item-dropdown').on('change', function() {
            populateFields(this);
        });
    }
    
    function calculateRowValues(row) {
        const quantity = parseFloat(row.find('.quantity').val().replace(/,/g, '')) || 0;
        const cost = parseFloat(row.find('.cost').val().replace(/,/g, '')) || 0;
        const discountPercentage = parseFloat(row.find('.discount-dropdown').val()) || 0;
        const salesTaxPercentage = parseFloat(row.find('.sales_tax_percentage').val()) || 0;

        const amount = quantity * cost;
        const discountAmount = (amount * discountPercentage) / 100;
        const netAmountBeforeTax = amount - discountAmount;
        const salesTaxAmount = (netAmountBeforeTax / (1 + salesTaxPercentage / 100)) * (salesTaxPercentage / 100);
        const netAmount = netAmountBeforeTax - salesTaxAmount;

        const fields = [
            { selector: '.amount', value: amount },
            { selector: '.discount_amount', value: discountAmount },
            { selector: '.net_amount_before_sales_tax', value: netAmountBeforeTax },
            { selector: '.sales_tax_amount', value: salesTaxAmount },
            { selector: '.net_amount', value: netAmount }
        ];

        fields.forEach(field => {
            const formatted = formatNumber(field.value);
            row.find(field.selector).val(formatted.formatted).data('raw-value', formatted.raw);
        });
    }
    
    
    $(document).on('blur', '.cost, .amount, .discount_amount, .net_amount_before_sales_tax, .sales_tax_amount, .net_amount', function() {
        const rawValue = parseFloat($(this).val().replace(/,/g, '')) || 0;
        const formatted = formatNumber(rawValue);
        $(this).val(formatted.formatted).data('raw-value', formatted.raw);
        calculateRowValues($(this).closest('tr'));
        calculateTotalAmount();
    });

    function calculateTotalAmount() {
        const totals = {
            totalAmount: 0,
            totalDiscountAmount: 0,
            totalNetAmountBeforeTax: 0,
            totalSalesTaxAmount: 0,
            vatableAmount: 0,
            zeroRatedAmount: 0,
            vatExemptAmount: 0,
            nonVatableAmount: 0 // Added to handle NA/NV amounts
        };

        $('#itemTableBody tr').each(function () {
            const row = $(this);
            const amount = parseFloat(row.find('.amount').data('raw-value')) || 0;
            const discountAmount = parseFloat(row.find('.discount_amount').data('raw-value')) || 0;
            const netAmountBeforeTax = parseFloat(row.find('.net_amount_before_sales_tax').data('raw-value')) || 0;
            const salesTaxAmount = parseFloat(row.find('.sales_tax_amount').data('raw-value')) || 0;
            const netAmount = parseFloat(row.find('.net_amount').data('raw-value')) || 0;
            const salesTaxName = row.find('.sales_tax_percentage option:selected').text();

            totals.totalAmount += amount;
            totals.totalDiscountAmount += discountAmount;
            totals.totalNetAmountBeforeTax += netAmountBeforeTax;
            totals.totalSalesTaxAmount += salesTaxAmount;

            if (salesTaxName.includes('12%')) {
            totals.vatableAmount += netAmount;
            } else if (salesTaxName.includes('E')) {
                totals.vatExemptAmount += netAmount;
            } else if (salesTaxName.includes('Z')) {
                totals.zeroRatedAmount += netAmount;
            } else if (salesTaxName.includes('NA') || salesTaxName.includes('NV')) {
                totals.nonVatableAmount += netAmount;
            } else {
                // Fallback: default to vatable if no valid VAT type is selected
                totals.vatableAmount += netAmount;
            }
        });

        // Update form fields
        const fields = [
            { selector: "#gross_amount", value: totals.totalAmount },
            { selector: "#discount_amount", value: totals.totalDiscountAmount },
            { selector: "#net_amount_due", value: totals.totalNetAmountBeforeTax },
            { selector: "#vat_amount", value: totals.totalSalesTaxAmount },
            { selector: "#vatable_amount", value: totals.vatableAmount },
            { selector: "#zero_rated_amount", value: totals.zeroRatedAmount },
            { selector: "#vat_exempt_amount", value: totals.vatExemptAmount }
        ];

        fields.forEach(field => {
            const formatted = formatNumber(field.value);
            $(field.selector).val(formatted.formatted).data('raw-value', formatted.raw);
        });

        updateTaxWithheldAmount();
    }

    // Helper function to format numbers with commas and two decimal places
    function formatNumber(num) {
        const rawValue = parseFloat(num.toFixed(2)) || 0;
        const formattedValue = rawValue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        return { raw: rawValue, formatted: formattedValue };
    }

    // Update the updateTaxWithheldAmount function to use raw values
    function updateTaxWithheldAmount() {
    const selectedTaxWithheld = $("#tax_withheld_percentage option:selected"); // Move this line up to ensure it's defined before use
    const taxWithheldPercentage = parseFloat(selectedTaxWithheld.data('rate')) || 0;
    const taxableBase = parseFloat($("#vatable_amount").data('raw-value')) || 0;
    const taxWithheldAmount = (taxWithheldPercentage / 100) * taxableBase;

    const formattedTaxWithheld = formatNumber(taxWithheldAmount);
    $("#tax_withheld_amount").val(formattedTaxWithheld.formatted).data('raw-value', formattedTaxWithheld.raw);

    const netAmountDue = parseFloat($("#net_amount_due").data('raw-value')) || 0;
    const totalAmountDue = netAmountDue - taxWithheldAmount;

    const formattedTotalAmountDue = formatNumber(totalAmountDue);
    $("#total_amount_due").val(formattedTotalAmountDue.formatted).data('raw-value', formattedTotalAmountDue.raw);

    const taxWithheldAccountId = selectedTaxWithheld.data('account-id');
    $("#tax_withheld_account_id").val(taxWithheldAccountId);
}


    // Initialize existing rows and bind events
    $('#itemTableBody tr').each(function () {
        bindRowEvents($(this));
        calculateRowValues($(this));
    });
    // Handle row removal
    $(document).on('click', '.removeRow', function () {
        $(this).closest('tr').remove();
        calculateTotalAmount();
    });

    // Populate fields based on selected item
    $(document).on('change', '.item-dropdown', function () {
        const row = $(this).closest('tr');
        const selectedOption = $(this).find('option:selected');
        row.find('.item_sales_description').val(selectedOption.data('description'));
        row.find('.uom_name').val(selectedOption.data('uom'));
        row.find('.cost').val(selectedOption.data('cost-price'));
        calculateRowValues(row);
        calculateTotalAmount();
    });

    // Initialize existing rows
    $('#itemTableBody tr').each(function () {
        bindRowEvents($(this));
    });

    // Update tax withheld amount when tax withheld percentage changes
    $('#tax_withheld_percentage').change(function () {
        updateTaxWithheldAmount();
    });



</script>