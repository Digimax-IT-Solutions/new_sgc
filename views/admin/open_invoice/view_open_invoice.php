<?php
//Guard
require_once '_guards.php';

$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('invoice');

$accounts = ChartOfAccount::all();
$customers = Customer::all();
$products = Product::all();
$terms = Term::all();
$locations = Location::all();
$payment_methods = PaymentMethod::all();
$wtaxes = WithholdingTax::all();
$discounts = Discount::all();
$input_vats = InputVat::all();
$sales_taxes = SalesTax::all();

$newInvoiceNo = Invoice::getLastInvoiceNo();


$page = 'sales_invoice'; // Set the variable corresponding to the current page
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>

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

    #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    #loadingOverlay .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
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

<div class="main">
    <?php require 'views/templates/navbar.php' ?>

    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3"><strong>View Invoice</strong></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="invoice">Invoices</a></li>
                                <li class="breadcrumb-item active" aria-current="page">View Invoice</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="invoice" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Invoices
                        </a>
                    </div>
                </div>
            </div>
            <?php if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $invoice = Invoice::find($id);
                if ($invoice) { ?>
                    <form id="invoiceForm" action="api/invoice_controller.php?action=add" method="POST">
                        <input type="hidden" name="action" id="modalAction" value="add" />
                        <input type="hidden" name="id" id="itemId" value="" />
                        <input type="hidden" name="item_data" id="item_data" />
                        <input type="hidden" id="customer_name_hidden" name="customer_name">
                        <div class="row">
                            <div class="col-12 col-lg-8">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title mb-0">Invoice Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-2">
                                            <!-- Customer Details Section -->
                                            <div class="col-12 mb-3">
                                                <h6 class="border-bottom pb-2">Customer Details</h6>
                                            </div>
                                            <div class="col-md-4 customer-details">
                                                <label for="customer_name" class="form-label">Customer</label>
                                                <select class="form-select form-select-sm select2" id="customer_id"
                                                    name="customer_name"
                                                    <?php echo ($invoice->invoice_status != 4) ? 'disabled' : ''; ?>>
                                                    <?php
                                                    // Array to prevent duplicates
                                                    $used_customers = [];
                                                    $selected_customer_id = $invoice->customer_id ?? ''; // Assuming this holds the selected customer ID

                                                    foreach ($customers as $customer):
                                                        if (!in_array($customer->id, $used_customers)):
                                                            $used_customers[] = $customer->id; // Track used customer IDs
                                                    ?>
                                                            <option value="<?= htmlspecialchars($customer->id) ?>"
                                                                <?= $customer->id == $selected_customer_id ? 'selected' : '' ?>>
                                                                <?= htmlspecialchars($customer->customer_name) ?>
                                                            </option>
                                                    <?php
                                                        endif;
                                                    endforeach;
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4 customer-details">
                                                <label for="customer_tin" class="form-label">TIN</label>
                                                <input type="text" class="form-control form-control-sm" id="customer_tin"
                                                    name="customer_tin" value="<?= htmlspecialchars($invoice->customer_tin) ?>"
                                                    disabled>
                                            </div>

                                            <div class="col-md-4 customer-details">
                                                <label for="customer_email" class="form-label">Email</label>
                                                <input type="text" class="form-control form-control-sm" id="customer_email"
                                                    name="customer_email"
                                                    value="<?= htmlspecialchars($invoice->customer_email) ?>" disabled>
                                            </div>

                                            <div class="col-md-4 customer-details">
                                                <label for="billing_address" class="form-label">Billing Address</label>
                                                <input type="text" class="form-control form-control-sm" id="billing_address"
                                                    name="billing_address"
                                                    value="<?= htmlspecialchars($invoice->billing_address) ?>" disabled>
                                            </div>

                                            <div class="col-md-4 customer-details">
                                                <label for="shipping_address" class="form-label">Shipping Address</label>
                                                <input type="text" class="form-control form-control-sm" id="shipping_address"
                                                    name="shipping_address"
                                                    value="<?= htmlspecialchars($invoice->shipping_address) ?>" disabled>
                                            </div>

                                            <div class="col-md-4 customer-details">
                                                <label for="business_style" class="form-label">Business Style</label>
                                                <input type="text" class="form-control form-control-sm" id="business_style"
                                                    name="business_style"
                                                    value="<?= htmlspecialchars($invoice->business_style) ?>" disabled>
                                            </div>

                                            <div class="col-md-4 customer-details">
                                                <label for="payment_method" class="form-label">Payment Method</label>
                                                <select class="form-select form-select-sm" id="payment_method"
                                                    name="payment_method"
                                                    <?php echo ($invoice->invoice_status != 4) ? 'disabled' : ''; ?>>
                                                    <?php
                                                    // Array to prevent duplicates
                                                    $used_payment_methods = [];
                                                    $selected_payment_method = $invoice->payment_method ?? ''; // Assuming this holds the selected payment method

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
                                                <select class="form-select form-select-sm" id="location" name="location"
                                                    <?php echo ($invoice->invoice_status != 4) ? 'disabled' : ''; ?>>
                                                    <?php
                                                    // Array to prevent duplicates
                                                    $used_locations = [];
                                                    $selected_location = $invoice->location ?? ''; // Assuming this holds the selected location

                                                    // Locations
                                                    foreach ($locations as $location):
                                                        if (!in_array($location->name, $used_locations)):
                                                            $used_locations[] = $location->name; // Track used locations
                                                    ?>
                                                            <option value="<?= htmlspecialchars($location->id) ?>"
                                                                <?= $location->name == $selected_location ? 'selected' : '' ?>>
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
                                                    value="<?= $invoice->customer_po ?>"
                                                    <?php echo ($invoice->invoice_status != 4) ? 'disabled' : ''; ?>>
                                            </div>

                                            <!-- Invoice Details Section -->
                                            <div class="col-12 mt-3 mb-3">
                                                <h6 class="border-bottom pb-2">Invoice Information</h6>
                                            </div>
                                            <div class="col-md-3 invoice-details">
                                                <label for="invoice_number" class="form-label">Invoice Number</label>
                                                <input type="text" class="form-control form-control-sm" id="invoice_number"
                                                    name="invoice_number" placeholder="Enter invoice #" <?php if ($invoice->invoice_status == 4): ?>
                                                    value="<?php echo htmlspecialchars($newInvoiceNo); ?>" readonly>
                                            <?php else: ?>
                                                value="<?php echo htmlspecialchars($invoice->invoice_number); ?>" disabled>
                                            <?php endif; ?>
                                            </div>

                                            <?php if ($invoice->invoice_status == 4): ?>
                                                <!-- Show editable form when invoice_status is 4 -->
                                                <div class="col-md-3 invoice-details">
                                                    <label for="invoice_date" class="form-label">Invoice Date</label>
                                                    <input type="date" class="form-control form-control-sm" id="invoice_date"
                                                        name="invoice_date" value="<?php echo date('Y-m-d'); ?>" required>
                                                </div>

                                                <div class="col-md-3 invoice-details">
                                                    <label for="terms" class="form-label">Terms</label>
                                                    <select class="form-select form-select-sm" id="invoice_terms" name="terms">
                                                        <option value="">Select Terms</option>
                                                        <?php
                                                        // The selected term, based on the term name from the invoice
                                                        $selected_term = $invoice->terms ?? ''; // Assuming this holds the term name

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
                                                    <label for="invoice_due_date" class="form-label">Due Date</label>
                                                    <input type="date" class="form-control form-control-sm" id="invoice_due_date"
                                                        name="invoice_due_date" required>
                                                </div>


                                            <?php else: ?>
                                                <!-- Show read-only form when invoice_status is not 4 -->
                                                <div class="col-md-3 invoice-details">
                                                    <label for="invoice_date" class="form-label">Invoice Date</label>
                                                    <input type="date" class="form-control form-control-sm" id="invoice_date"
                                                        name="invoice_date" value="<?= $invoice->invoice_date ?>" disabled>
                                                </div>

                                                <div class="col-md-3 invoice-details">
                                                    <label for="invoice_due_date" class="form-label">Due Date</label>
                                                    <input type="date" class="form-control form-control-sm" id="invoice_due_date"
                                                        name="invoice_due_date" value="<?= $invoice->invoice_due_date ?>" disabled>
                                                </div>

                                                <div class="col-md-3 invoice-details">
                                                    <label for="terms" class="form-label">Terms</label>
                                                    <select class="form-control form-control-sm" id="invoice_terms" name="terms" disabled>
                                                        <option value="<?= htmlspecialchars($invoice->terms) ?>" selected>
                                                            <?= htmlspecialchars($invoice->terms) ?>
                                                        </option>
                                                    </select>
                                                </div>
                                            <?php endif; ?> <!-- draft end -->

                                            <div class="col-md-3 invoice-details">
                                                <label for="invoice_account_id" class="form-label">Account</label>
                                                <select class="form-select form-select-sm" id="invoice_account_id" name="invoice_account_id"
                                                    <?php echo ($invoice->invoice_status != 4) ? 'disabled' : ''; ?>>
                                                    <option value="">Select Account</option>
                                                    <!-- Populate with account options -->
                                                </select>
                                            </div>
                                            <div class="col-md-3 invoice-details">
                                                <label for="rep" class="form-label">Rep</label>
                                                <input type="text" class="form-control form-control-sm" id="rep" name="rep"
                                                    value="<?= $invoice->rep ?>"
                                                    <?php echo ($invoice->invoice_status != 4) ? 'disabled' : ''; ?>>
                                            </div>

                                            <div class="col-md-3 invoice-details">
                                                <label for="so_no" class="form-label">S.O No.</label>
                                                <input type="text" class="form-control form-control-sm" id="so_no" name="so_no"
                                                    value="<?= $invoice->so_no ?>"
                                                    <?php echo ($invoice->invoice_status != 4) ? 'disabled' : ''; ?>>
                                            </div>

                                            <div class="col-md-3 invoice-details">
                                                <label for="memo" class="form-label">Memo</label>
                                                <input type="text" class="form-control form-control-sm" id="memo" name="memo"
                                                    value="<?= $invoice->memo ?>"
                                                    <?php echo ($invoice->invoice_status != 4) ? 'disabled' : ''; ?>>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-lg-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title">Summary</h5>
                                        <?php if ($invoice->invoice_status == 0): ?>
                                            <span class="badge bg-danger">Unpaid</span>
                                        <?php elseif ($invoice->invoice_status == 1): ?>
                                            <span class="badge bg-success">Paid</span>
                                        <?php elseif ($invoice->invoice_status == 2): ?>
                                            <span class="badge bg-warning">Partially Paid</span>
                                        <?php elseif ($invoice->invoice_status == 3): ?>
                                            <span class="badge bg-secondary">Void</span>
                                        <?php elseif ($invoice->invoice_status == 4): ?>
                                            <span class="badge bg-info text-dark">Draft</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="cash_sales" name="cash_sales"
                                                disabled>
                                            <label class="form-check-label fw-bold" for="cash_sales">Cash Sales</label>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Gross Amount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="gross_amount"
                                                    name="gross_amount"
                                                    value="<?= number_format($invoice->gross_amount, 2, '.', ',') ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Discount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="discount_amount"
                                                    name="discount_amount"
                                                    value="<?= number_format($invoice->discount_amount, 2, '.', ',') ?>"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Net Amount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="net_amount_due"
                                                    name="net_amount_due"
                                                    value="<?= number_format($invoice->net_amount_due, 2, '.', ',') ?>"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">VAT:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="vat_amount"
                                                    name="vat_amount"
                                                    value="<?= number_format($invoice->vat_amount, 2, '.', ',') ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Vatable 12%:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="vatable_amount"
                                                    name="vatable_amount"
                                                    value="<?= number_format($invoice->vatable_amount, 2, '.', ',') ?>"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Zero-rated:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end"
                                                    id="zero_rated_amount" name="zero_rated_amount"
                                                    value="<?= number_format($invoice->zero_rated_amount, 2, '.', ',') ?>"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Vat-Exempt:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end"
                                                    id="vat_exempt_amount" name="vat_exempt_amount"
                                                    value="<?= number_format($invoice->vat_exempt_amount, 2, '.', ',') ?>"
                                                    readonly>
                                            </div>
                                        </div>
                                        <?php if ($invoice->invoice_status == 4): ?>
                                            <!-- Show editable Tax Withheld dropdown when invoice_status is 4 -->
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
                                                                    <?= $wtax->id == $invoice->tax_withheld_percentage ? 'selected' : '' ?>>
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
                                            <!-- Show disabled Tax Withheld dropdown when invoice_status is not 4 -->
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
                                                                    <?= $wtax->id == $invoice->tax_withheld_percentage ? 'selected' : '' ?>>
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
                                                    id="tax_withheld_amount" name="tax_withheld_amount"
                                                    value="<?= number_format($invoice->tax_withheld_amount, 2, '.', ',') ?>"
                                                    readonly>
                                                <input type="hidden" name="tax_withheld_account_id"
                                                    id="tax_withheld_account_id">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label fw-bold">Total Amount Due:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end fw-bold"
                                                    id="total_amount_due" name="total_amount_due"
                                                    value="<?= number_format($invoice->total_amount_due, 2, '.', ',') ?>"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex justify-content-center">
                                        <?php if ($invoice->invoice_status == 4): ?>
                                            <!-- Buttons to show when invoice_status is 4 -->
                                            <button type="button" id="saveDraftBtn" class="btn btn-secondary me-2">Update Draft</button>
                                            <button type="submit" class="btn btn-info me-2">Save as Final</button>
                                        <?php elseif ($invoice->invoice_status == 3): ?>
                                            <!-- Button to show when invoice_status is 3 -->
                                            <a class="btn btn-primary" href="#" id="reprintButton">
                                                <i class="fas fa-print"></i> Reprint
                                            </a>
                                        <?php else: ?>
                                            <!-- Buttons to show when invoice_status is neither 3 nor 4 -->
                                            <button type="button" class="btn btn-secondary me-2" id="voidButton">Void</button>
                                            <a class="btn btn-primary" href="#" id="reprintButton">
                                                <i class="fas fa-print"></i> Reprint
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Invoice Items</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover" id="itemTable">
                                                <thead class="bg-light" style="font-size: 12px;">
                                                    <tr>
                                                        <th style="width: 15%;">Item</th>
                                                        <th style="width: 9%;">Description</th>
                                                        <th class="text-right" style="width: 3%;">Quantity</th>
                                                        <th class="text-right" style="width: 8%;">Unit</th>
                                                        <th class="text-right" style="width: 8%;">Selling Price</th>
                                                        <th class="text-right" style="width: 8%;">Amount</th>
                                                        <th style="width: 8%;">Discount Type</th>
                                                        <th class="text-right" style="width: 8%;">Discount</th>
                                                        <th class="text-right" style="width: 8%;">Net</th>
                                                        <th class="text-right" style="width: 8%;">Taxable Amount</th>
                                                        <th style="width: 10%;">Tax Type</th>
                                                        <th class="text-right" style="width: 10%;">VAT</th>
                                                        <th style="width: 4%;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="itemTableBody" style="font-size: 14px;">
                                                    <?php
                                                    if ($invoice) {
                                                        foreach ($invoice->details as $detail) {
                                                    ?>
                                                            <tr>
                                                                <td>
                                                                    <select class="form-select form-select-sm account-dropdown select2" id="item_id" name="item_id[]" onchange="populateFields(this)" <?php echo ($invoice->invoice_status != 4) ? 'disabled' : ''; ?>>
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
                                                                    <input type="text" class="form-control form-control-sm description-field" name="description[]" value="<?= htmlspecialchars($detail['item_description']) ?>" readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm quantity" name="quantity[]" value="<?= htmlspecialchars($detail['quantity']) ?>" <?php echo ($invoice->invoice_status != 4) ? 'disabled' : ''; ?>>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm uom" name="uom[]" value="<?= htmlspecialchars($detail['uom_name']) ?>" readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm cost" name="cost[]" value="<?= number_format($detail['cost'], 2, '.', ',') ?>" <?php echo ($invoice->invoice_status != 4) ? 'disabled' : ''; ?>>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm amount" name="amount[]" value="<?= number_format($detail['amount'], 2, '.', ',') ?>" readonly>
                                                                </td>
                                                                <td>
                                                                    <select class="form-control form-control-sm discount_percentage select2" name="discount_percentage[]" <?php echo ($invoice->invoice_status != 4) ? 'disabled' : ''; ?>>
                                                                        <?php foreach ($discounts as $discount): ?>
                                                                            <option value="<?= htmlspecialchars($discount->discount_rate) ?>" data-account-id="<?= htmlspecialchars($discount->discount_account_id) ?>" <?= ($discount->discount_rate == $detail['discount_percentage']) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($discount->discount_name) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm discount_amount" name="discount_amount[]" value="<?= number_format($detail['discount_amount'], 2, '.', ',') ?>" readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm net_amount_before_sales_tax" name="net_amount_before_sales_tax[]" value="<?= number_format($detail['net_amount_before_sales_tax'], 2, '.', ',') ?>" readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm net_amount" name="net_amount[]" value="<?= number_format($detail['net_amount'], 2, '.', ',') ?>" readonly>
                                                                </td>
                                                                <td>
                                                                    <select class="form-control form-control-sm sales_tax_percentage select2" name="sales_tax_percentage[]" <?php echo ($invoice->invoice_status != 4) ? 'disabled' : ''; ?>>
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
                                                                    <input type="text" class="form-control form-control-sm sales_tax_amount" name="sales_tax_amount[]" value="<?= number_format($detail['sales_tax_amount'], 2, '.', ',') ?>" readonly>
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-sm btn-danger removeRow" <?php echo ($invoice->invoice_status != 4) ? 'disabled' : ''; ?>>
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
                                        <button type="button" class="btn btn-primary btn-sm" id="addItemBtn">
                                            <i class="fas fa-plus"></i> Add Item
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
            <?php
                    // Invoice found, you can now display the details
                } else {
                    // Handle the case where the invoice is not found
                    echo "Invoice not found.";
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

    <div id="loadingOverlay" style="display: none;">
        <div class="spinner"></div>
        <div class="message">Processing Invoice</div>
    </div>
</div>

<?php require 'views/templates/footer.php' ?>


<iframe id="printFrame" style="display:none;"></iframe>

<script>
    document.getElementById('invoice_terms').addEventListener('change', function() {
        var selectedTerm = this.options[this.selectedIndex];
        var daysDue = parseInt(selectedTerm.getAttribute('data-days'));

        var invoiceDate = document.getElementById('invoice_date').value;

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
            document.getElementById('invoice_due_date').value = `${year}-${month}-${day}`;

            console.log("Updated Due Date:", `${year}-${month}-${day}`);
        } else {
            console.log("Invalid Invoice Date or Days Due.");
        }
    });

    // Trigger change event on page load if a term is selected
    window.addEventListener('load', function() {
        var selectedTerm = document.getElementById('invoice_terms');
        if (selectedTerm.value) {
            selectedTerm.dispatchEvent(new Event('change'));
        }
    });
</script>
<!-- account -->
<script>
    document.getElementById('cash_sales').addEventListener('change', function() {
        var labelText = this.checked ? "Cash Sales - Sales Receipt" : "Sales Invoice";
        document.getElementById('cash_sales_text').innerHTML = '&nbsp;&nbsp;' + labelText;
    });

    document.getElementById('invoiceForm').addEventListener('submit', function() {
        const selectElement = document.getElementById('customer_id');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const customerName = selectedOption.getAttribute('data-customer-name');

        document.getElementById('customer_name_hidden').value = customerName;
        console.log(customerName);
    });

    document.addEventListener("DOMContentLoaded", function() {
        var cashSalesSwitch = document.getElementById('cash_sales');
        var invoiceAccountSelect = document.getElementById('invoice_account_id');
        var accounts = <?php echo json_encode($accounts); ?>;

        function updateLabelAndOptions() {
            var isCashSales = cashSalesSwitch.checked;

            // Clear existing options
            invoiceAccountSelect.innerHTML = '';

            // Add options based on the state of the cashSalesSwitch
            accounts.forEach(function(account) {
                var option = document.createElement('option');
                option.value = account.id;
                option.setAttribute('data-account-type', account.account_type);
                option.textContent = account.account_description;

                if (isCashSales) {
                    // Show 'Bank' and 'Undeposited Funds' when cash sales is selected
                    if (account.account_type === "Bank" || account.account_description.toLowerCase().includes("undeposited")) {
                        invoiceAccountSelect.appendChild(option);
                    }
                } else {
                    // Show 'Accounts Receivable' when cash sales is not selected
                    if (account.account_type === "Accounts Receivable") {
                        invoiceAccountSelect.appendChild(option);
                    }
                }
            });
        }

        // Initial call to set label and options based on default switch state
        updateLabelAndOptions();

        // Event listener for switch change
        cashSalesSwitch.addEventListener('change', updateLabelAndOptions);

        const taxWithheldSelect = document.getElementById('tax_withheld_percentage');
        const taxWithheldAccountIdInput = document.getElementById('tax_withheld_account_id');

        taxWithheldSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const accountId = selectedOption.getAttribute('data-account-id');
            taxWithheldAccountIdInput.value = accountId;
            console.log(accountId);
        });
    });
</script>
<!-- print/void -->
<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('reprintButton').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Reprint Invoice?',
                text: "Are you sure you want to reprint this invoice?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reprint it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    printInvoice(<?= $invoice->id ?>, 2); // Pass 2 for reprint
                }
            });
        });

        // Attach event listener for the void button
        document.getElementById('voidButton').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Void Invoice?',
                text: "Are you sure you want to void this invoice? This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, void it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    voidCheck(<?= $invoice->id ?>);
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

    function printInvoice(id, printStatus) {
        showLoadingOverlay();

        $.ajax({
            url: 'api/invoice_controller.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'update_print_status',
                invoice_id: id,
                print_status: printStatus
            },
            success: function(response) {
                if (response.success) {
                    const printFrame = document.getElementById('printFrame');
                    const printContentUrl = `print_invoice?action=print&id=${id}`;

                    printFrame.src = printContentUrl;

                    printFrame.onload = function() {
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
            error: function(jqXHR, textStatus, errorThrown) {
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
            url: 'api/invoice_controller.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'void_check',
                id: id
            },
            success: function(response) {
                hideLoadingOverlay(); // Hide the loading overlay on success
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Invoice has been voided successfully.'
                    }).then(() => {
                        location.reload(); // Reload the page to reflect changes
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to void invoice: ' + (response.message || 'Unknown error')
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                hideLoadingOverlay(); // Hide the loading overlay on error
                console.error('AJAX error:', textStatus, errorThrown);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while voiding the invoice: ' + textStatus
                });
            }
        });
    }
</script>
<!-- sales_invoice -->
<script>
    $(document).ready(function() {

        $('#sales_tax_percentage').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Vat',
            allowClear: true
        });

        $('#discount_percentage').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Discount',
            allowClear: true
        });

        $('#item_id').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Item',
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


        $('#invoice_account_id').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: '',
            allowClear: false
        });

        $('#customer_id').change(function() {
            var customerId = $(this).val();
            if (customerId === '') {
                $('#customer_tin').val('');
                // Clear other fields as needed
                return;
            }

            // Find the selected customer object by customerId
            var selectedCustomer = <?= json_encode($customers); ?>.find(function(customer) {
                return customer.id == customerId;
            });

            if (selectedCustomer) {
                $('#customer_tin').val(selectedCustomer.customer_tin);
                // Populate other fields similarly
                $('#billing_address').val(selectedCustomer.billing_address);
                $('#customer_email').val(selectedCustomer.customer_email);
                $('#shipping_address').val(selectedCustomer.shipping_address);
                $('#business_style').val(selectedCustomer.business_style);
            } else {
                // Clear fields if customer not found
                $('#customer_tin').val('');
                // Clear other fields as needed
            }
        });


        // Initialize dropdowns
        const initDropdowns = (data, template) => data.reduce((acc, item) => acc + template(item), '');

        const itemDropdownOptions = initDropdowns(<?php echo json_encode($products); ?>,
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

        const discountDropdownOptions = initDropdowns(<?php echo json_encode($discounts); ?>,
            discount => `<option value="${discount.discount_rate}" data-account-id="${discount.discount_account_id}">${discount.discount_name}</option>`
        );

        const inputVatDropdownOption = initDropdowns(<?php echo json_encode($sales_taxes); ?>,
            tax => `<option value="${tax.sales_tax_rate}" data-account-id="${tax.sales_tax_account_id}">${tax.sales_tax_name}</option>`
        );

        // Add new row
        $('#addItemBtn').click(() => {
            const newRow = `
                <tr>
                    <td><select class="form-control form-control-sm account-dropdown select2" id="item" name="item_id[]" onchange="populateFields(this)">
                    <option value=""></option>
                    ${itemDropdownOptions}</select></td>
                    <input type="hidden" class="item-name" name="item_name[]" value="">
                    <input type="hidden" class="item-cost-price" name="cost_price[]" value="">
                    <input type="hidden" class="item-cogs-account-id" name="item_cogs_account_id[]" value="">
                    <input type="hidden" class="item-income-account-id" name="item_income_account_id[]" value="">
                    <input type="hidden" class="item-asset-account-id" name="item_asset_account_id[]" value="">
                    <td><input type="text" class="form-control form-control-sm description-field" name="description[]" readonly></td>
                    <td><input type="text" class="form-control form-control-sm quantity" name="quantity[]" placeholder="Qty"></td>
                    <td><input type="text" class="form-control form-control-sm uom" name="uom[]" readonly></td>
       
                    <td><input type="text" class="form-control form-control-sm cost" name="cost[]" placeholder="Enter Cost"></td>
                    <td><input type="text" class="form-control form-control-sm amount" name="amount[]" placeholder="Amount" readonly></td>
                    <td><select class="form-control form-control-sm discount_percentage select2" name="discount_percentage[]">${discountDropdownOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm discount_amount" name="discount_amount[]" placeholder="" readonly></td>
                    <td><input type="text" class="form-control form-control-sm net_amount_before_sales_tax" name="net_amount_before_sales_tax[]" placeholder="" readonly></td>
                    <td><input type="text" class="form-control form-control-sm net_amount" name="net_amount[]" placeholder=""></td>
                    <td><select class="form-control form-control-sm sales_tax_percentage select2" name="sales_tax_percentage[]">${inputVatDropdownOption}</select></td>
                    <td><input type="text" class="form-control form-control-sm sales_tax_amount" name="sales_tax_amount[]" placeholder="" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm removeRow"><i class="fas fa-trash"></i></button></td>
                </tr>`;

            const $newRow = $(newRow);
            $('#itemTableBody').append($newRow);

            // Initialize Select2 on the new dropdowns
            $newRow.find('.select2').select2({
                width: '100%',
                placeholder: '',
                theme: 'classic' // Use this if you're using Bootstrap 4
            });
            // Specifically initialize the item dropdown with a placeholder
            $newRow.find('.account-dropdown').select2({
                width: '100%',
                placeholder: 'Select Item',
                allowClear: true,
                theme: 'classic'
            });


            $newRow.find('.quantity, .cost, .discount_percentage, .sales_tax_percentage, .sales_tax_amount').on('input', function() {
                calculateRowValues($(this).closest('tr'));
                calculateTotalAmount();
            });

            // Initialize listeners for the new row
            initializeRowListeners($newRow);

            calculateRowValues($newRow);
            calculateTotalAmount();
        });

        function calculateRowValues(row) {
            const quantity = parseFloat(row.find('.quantity').val().replace(/,/g, '')) || 0;
            const cost = parseFloat(row.find('.cost').val().replace(/,/g, '')) || 0;
            const discountPercentage = parseFloat(row.find('.discount_percentage').val()) || 0;
            const salesTaxPercentage = parseFloat(row.find('.sales_tax_percentage').val()) || 0;

            const amount = quantity * cost;
            const discountAmount = (amount * discountPercentage) / 100;
            const netAmountBeforeTax = amount - discountAmount;
            const salesTaxAmount = (netAmountBeforeTax / (1 + salesTaxPercentage / 100)) * (salesTaxPercentage / 100);
            const netAmount = netAmountBeforeTax - salesTaxAmount;

            row.find('.amount').val(formatNumber(amount));
            row.find('.discount_amount').val(formatNumber(discountAmount));
            row.find('.net_amount_before_sales_tax').val(formatNumber(netAmountBeforeTax));
            row.find('.sales_tax_amount').val(formatNumber(salesTaxAmount));
            row.find('.net_amount').val(formatNumber(netAmount));
        }

        // Helper function to format numbers with commas and two decimal places
        function formatNumber(num) {
            return num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
        }

        // Update calculateTotalAmount function
        function calculateTotalAmount() {
            const invoiceStatus = <?php echo json_encode($invoice->invoice_status); ?>;

            if (invoiceStatus == 4) { // Recalculate only if status is Draft (4)
                const totals = {
                    totalAmount: 0,
                    totalDiscountAmount: 0,
                    totalNetAmountBeforeTax: 0,
                    totalInputVatAmount: 0,
                    vatableAmount: 0,
                    zeroRatedAmount: 0,
                    vatExemptAmount: 0,
                    nonVatableAmount: 0 // Added for NV amount tracking
                };

                $('.amount, .discount_amount, .net_amount_before_sales_tax, .sales_tax_amount, .net_amount').each(function() {
                    const value = parseFloat($(this).val().replace(/,/g, '')) || 0;
                    const inputVatName = $(this).closest('tr').find('.sales_tax_percentage option:selected').text();

                    if ($(this).hasClass('amount')) {
                        totals.totalAmount += value;
                    } else if ($(this).hasClass('discount_amount')) {
                        totals.totalDiscountAmount += value;
                    } else if ($(this).hasClass('net_amount_before_sales_tax')) {
                        totals.totalNetAmountBeforeTax += value;
                    } else if ($(this).hasClass('sales_tax_amount')) {
                        totals.totalInputVatAmount += value;
                    } else if ($(this).hasClass('net_amount')) {
                        // Calculate amounts based on the selected VAT type
                        if (inputVatName.includes('12%')) {
                            totals.vatableAmount += value;
                        } else if (inputVatName.includes('E')) {
                            totals.vatExemptAmount += value;
                        } else if (inputVatName.includes('Z')) {
                            totals.zeroRatedAmount += value;
                        } else if (inputVatName.includes('NA')) {
                            // NA case: may not add to any totals, handled as per requirement
                        } else if (inputVatName.includes('NV')) {
                            totals.nonVatableAmount += value; // Add NV value to its own category
                        } else {
                            // Fallback: if no valid VAT type is selected, add to vatable by default
                            totals.vatableAmount += value;
                        }
                    }
                });

                // Update form fields with formatted numbers
                $("#gross_amount").val(formatNumber(totals.totalAmount));
                $("#discount_amount").val(formatNumber(totals.totalDiscountAmount));
                $("#net_amount_due").val(formatNumber(totals.totalNetAmountBeforeTax));
                $("#vat_amount").val(formatNumber(totals.totalInputVatAmount));
                $("#vatable_amount").val(formatNumber(totals.vatableAmount));
                $("#zero_rated_amount").val(formatNumber(totals.zeroRatedAmount));
                $("#vat_exempt_amount").val(formatNumber(totals.vatExemptAmount));

                // Get the selected tax withheld option
                const selectedTaxWithheld = $("#tax_withheld_percentage option:selected");
                const taxWithheldPercentage = parseFloat(selectedTaxWithheld.data('rate')) || 0;
                const taxWithheldId = selectedTaxWithheld.val();

                // Calculate tax withheld amount based on the sum of vatable, zero-rated, vat-exempt, and non-vatable amounts
                const taxableBase = totals.vatableAmount + totals.zeroRatedAmount + totals.vatExemptAmount + totals.nonVatableAmount;
                const taxWithheldAmount = (taxWithheldPercentage / 100) * taxableBase;

                $("#tax_withheld_amount").val(formatNumber(taxWithheldAmount));
                $("#tax_withheld_account_id").val(selectedTaxWithheld.data('account-id'));

                // Store the tax withheld ID
                $("#tax_withheld_percentage").data('selected-id', taxWithheldId);

                // Calculate total amount due
                const subtotal = totals.totalInputVatAmount + taxableBase;
                const totalAmountDue = subtotal - taxWithheldAmount;

                $("#total_amount_due").val(formatNumber(totalAmountDue));
            } else {
                // For non-Draft statuses, display existing values without recalculation
                const existingTaxWithheldAmount = parseFloat($("#tax_withheld_amount").val().replace(/,/g, '')) || 0;
                const existingTotalAmountDue = parseFloat($("#total_amount_due").val().replace(/,/g, '')) || 0;

                $("#tax_withheld_amount").val(formatNumber(existingTaxWithheldAmount));
                $("#total_amount_due").val(formatNumber(existingTotalAmountDue));
            }
        }




        // Event listener for tax withheld percentage change
        $('#tax_withheld_percentage').on('change', function() {
            calculateTotalAmount();
        });

        // Function to initialize event listeners for a row
        function initializeRowListeners(row) {
            row.find('.quantity, .cost, .discount_percentage, .sales_tax_percentage').on('input change', function() {
                calculateRowValues(row);
                calculateTotalAmount();
            });

            // Initialize Select2 for dropdowns in this row
            row.find('.select2').select2({
                width: '100%',
                placeholder: '',
                theme: 'classic'
            });

            // Initialize item dropdown specifically
            row.find('.account-dropdown').select2({
                width: '100%',
                placeholder: 'Select Item',
                allowClear: true,
                theme: 'classic'
            });

            // Add change event listener to item dropdown
            row.find('.account-dropdown').on('change', function() {
                populateFields(this);
                calculateRowValues(row);
                calculateTotalAmount();
            });
        }

        // Initialize existing rows
        $('#itemTableBody tr').each(function() {
            initializeRowListeners($(this));
            calculateRowValues($(this));
        });

        // Calculate total amount after initializing existing rows
        calculateTotalAmount();


        // Function to get unique discount account IDs
        function getUniqueDiscountAccountIds() {
            const uniqueIds = new Set();
            $('#itemTableBody tr').each(function() {
                const discountAccountId = $(this).find('.discount_percentage option:selected').data('account-id');
                if (discountAccountId) {
                    uniqueIds.add(discountAccountId);
                }
            });
            return Array.from(uniqueIds);
        }

        // Function to get unique output VAT IDs
        function getUniqueOutputVatIds() {
            const uniqueIds = new Set();
            $('#itemTableBody tr').each(function() {
                const outputVatId = $(this).find('.sales_tax_percentage option:selected').data('account-id');
                if (outputVatId) {
                    uniqueIds.add(outputVatId);
                }
            });
            return Array.from(uniqueIds);
        }

        // Add change event listener to item dropdowns for existing and new rows
        $(document).on('change', '.account-dropdown', function() {
            populateFields(this);
            calculateRowValues($(this).closest('tr'));
            calculateTotalAmount();
        });

        // Gather table items function (unchanged)
        function gatherTableItems() {
            const items = [];
            $('#itemTableBody tr').each(function(index) {
                const item = {
                    item_id: $(this).find('select[name="item_id[]"]').val(),
                    item_name: $(this).find('.item-name').val(),
                    quantity: $(this).find('input[name="quantity[]"]').val(),
                    cost: $(this).find('input[name="cost[]"]').val(),
                    cost_price: $(this).find('input[name="cost_price[]"]').val(),
                    amount: $(this).find('input[name="amount[]"]').val(),
                    discount_percentage: $(this).find('select[name="discount_percentage[]"]').val(),
                    discount_amount: $(this).find('input[name="discount_amount[]"]').val(),
                    net_amount_before_sales_tax: $(this).find('input[name="net_amount_before_sales_tax[]"]').val(),
                    net_amount: $(this).find('input[name="net_amount[]"]').val(),
                    sales_tax_percentage: $(this).find('select[name="sales_tax_percentage[]"]').val(),
                    sales_tax_amount: $(this).find('input[name="sales_tax_amount[]"]').val(),
                    discount_account_id: $(this).find('.discount_percentage option:selected').data('account-id'),
                    output_vat_id: $(this).find('.sales_tax_percentage option:selected').data('account-id'),
                    cogs_account_id: $(this).find('.item-cogs-account-id').val(),
                    income_account_id: $(this).find('.item-income-account-id').val(),
                    asset_account_id: $(this).find('.item-asset-account-id').val()
                };
                items.push(item);
            });
            return items;
        }

        $(document).on('change', '.account-dropdown, .sales_tax_percentage, .discount_percentage', function() {
            populateFields(this);
            calculateRowValues($(this).closest('tr'));
            calculateTotalAmount();
        });

        $(document).on('input', ' .amount, .discount_amount, .net_amount_before_sales_tax, .sales_tax_amount, .net_amount', function() {
            const value = $(this).val().replace(/[^\d.]/g, '');
            const formattedValue = formatNumber(parseFloat(value) || 0);
            $(this).val(formattedValue);
        });

        $('#invoiceForm').submit(function(event) {
            event.preventDefault();

            // Check if the table has any rows
            if ($('#itemTableBody tr').length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Items',
                    text: 'You must add at least one item before submitting the invoice.'
                });
                document.getElementById('loadingOverlay').style.display = 'none';
                return false;
            }

            const items = gatherTableItems();
            $('#item_data').val(JSON.stringify(items));

            const invoiceStatus = <?= json_encode($invoice->invoice_status) ?>;
            const invoiceId = <?= json_encode($invoice->id) ?>;

            if (invoiceStatus == 4) {


                // Show loading overlay
                document.getElementById('loadingOverlay').style.display = 'flex';

                $.ajax({
                    url: 'api/invoice_controller.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'save_final',
                        id: invoiceId,
                        invoice_number: $('#invoice_number').val(),
                        invoice_date: $('#invoice_date').val(),
                        invoice_due_date: $('#invoice_due_date').val(),
                        terms: $('#invoice_terms').val(),
                        invoice_account_id: $('#invoice_account_id').val(),
                        customer_id: $('#customer_id').val(),
                        customer_name: $('#customer_name_hidden').val(),
                        customer_tin: $('#customer_tin').val(),
                        customer_email: $('#customer_email').val(),
                        billing_address: $('#billing_address').val(),
                        shipping_address: $('#shipping_address').val(),
                        business_style: $('#business_style').val(),
                        payment_method: $('#payment_method').val(),
                        location: $('#location').val(),
                        customer_po: $('#customer_po').val(),
                        rep: $('#rep').val(),
                        so_no: $('#so_no').val(),
                        memo: $('#memo').val(),
                        cash_sales: $('#cash_sales').is(':checked'),
                        gross_amount: $('#gross_amount').val(),
                        discount_amount: $('#discount_amount').val(),
                        net_amount_due: $('#net_amount_due').val(),
                        vat_amount: $('#vat_amount').val(),
                        vatable_amount: $('#vatable_amount').val(),
                        zero_rated_amount: $('#zero_rated_amount').val(),
                        vat_exempt_amount: $('#vat_exempt_amount').val(),
                        tax_withheld_percentage: $('#tax_withheld_percentage').val(),
                        tax_withheld_amount: $('#tax_withheld_amount').val(),
                        tax_withheld_account_id: $('#tax_withheld_account_id').val(),
                        total_amount_due: $('#total_amount_due').val(),
                        item_data: JSON.stringify(items)
                    },
                    success: function(response) {
                        document.getElementById('loadingOverlay').style.display = 'none';

                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Invoice submitted successfully!',
                                showCancelButton: true,
                                confirmButtonText: 'Print',
                                cancelButtonText: 'Save as PDF'
                            }).then((result) => {
                                if (result.isConfirmed && response.invoiceId) {
                                    printInvoice(response.invoiceId, 1); // Pass 1 for initial print
                                } else {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error saving invoice: ' + (response.message || 'Unknown error')
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
                            text: 'An error occurred while saving the invoice: ' + textStatus
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

            const invoiceStatus = <?= json_encode($invoice->invoice_status) ?>;
            const invoiceId = <?= json_encode($invoice->id) ?>;

            if (invoiceStatus == 4) {
                // Show loading overlay
                document.getElementById('loadingOverlay').style.display = 'flex';

                $.ajax({
                    url: 'api/invoice_controller.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'update_draft',
                        id: invoiceId,
                        invoice_date: $('#invoice_date').val(),
                        invoice_due_date: $('#invoice_due_date').val(),
                        terms: $('#invoice_terms').val(),
                        invoice_account_id: $('#invoice_account_id').val(),
                        customer_id: $('#customer_id').val(),
                        customer_name: $('#customer_name_hidden').val(),
                        customer_tin: $('#customer_tin').val(),
                        customer_email: $('#customer_email').val(),
                        billing_address: $('#billing_address').val(),
                        shipping_address: $('#shipping_address').val(),
                        business_style: $('#business_style').val(),
                        payment_method: $('#payment_method').val(),
                        location: $('#location').val(),
                        customer_po: $('#customer_po').val(),
                        rep: $('#rep').val(),
                        so_no: $('#so_no').val(),
                        memo: $('#memo').val(),
                        cash_sales: $('#cash_sales').is(':checked'),
                        gross_amount: $('#gross_amount').val(),
                        discount_amount: $('#discount_amount').val(),
                        net_amount_due: $('#net_amount_due').val(),
                        vat_amount: $('#vat_amount').val(),
                        vatable_amount: $('#vatable_amount').val(),
                        zero_rated_amount: $('#zero_rated_amount').val(),
                        vat_exempt_amount: $('#vat_exempt_amount').val(),
                        tax_withheld_percentage: $('#tax_withheld_percentage').val(),
                        tax_withheld_amount: $('#tax_withheld_amount').val(),
                        tax_withheld_account_id: $('#tax_withheld_account_id').val(),
                        total_amount_due: $('#total_amount_due').val(),
                        item_data: JSON.stringify(items)
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
                                if (result.isConfirmed && response.invoiceId) {
                                    saveAsPDF(response.invoiceId); // Assuming you have a saveAsPDF function
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

    // Function to populate multiple fields based on selected option
    function populateFields(select) {
        const selectedOption = $(select).find('option:selected');
        const row = $(select).closest('tr');

        // Only update fields if it's the item dropdown
        if ($(select).hasClass('account-dropdown')) {
            const itemName = selectedOption.data('item-name');
            const description = selectedOption.data('description');
            const uom = selectedOption.data('uom');
            const costPrice = selectedOption.data('cost-price');
            const cogsAccountId = selectedOption.data('cogs-account-id');
            const incomeAccountId = selectedOption.data('income-account-id');
            const assetAccountId = selectedOption.data('asset-account-id');

            row.find('.item-name').val(itemName);
            row.find('.description-field').val(description);
            row.find('.uom').val(uom);
            row.find('.cost').val(costPrice);
            row.find('.item-cost-price').val(costPrice);
            row.find('.item-cogs-account-id').val(cogsAccountId);
            row.find('.item-income-account-id').val(incomeAccountId);
            row.find('.item-asset-account-id').val(assetAccountId);

            console.log("Item Name:" + itemName);
            console.log("Cost Price:" + costPrice);
            console.log("COGS:" + cogsAccountId);
            console.log("INCOME:" + incomeAccountId);
            console.log("ASSET:" + assetAccountId);
        }
    }
</script>