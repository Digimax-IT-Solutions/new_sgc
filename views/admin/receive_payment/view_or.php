<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('receive_payment');
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


$page = 'sales_invoice'; // Set the variable corresponding to the current page
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>

<style>
    /* Adjust placeholder color in Select2 */
    .select2-container--classic .select2-selection--single .select2-selection__placeholder {
        color: #333 !important; /* Replace #333 with your desired color */
        opacity: 1; /* Ensure the color is applied correctly */
    }

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
                        <h1 class="h3"><strong>Official Receipt</strong></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="receive_payment">Payments</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Create Official Receipt</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="receive_payment" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                </div>
            </div>

            <?php if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $orPayments = OfficialReceipt::find($id);
                if ($orPayments) { ?>
                    <form id="officialReceiptFrom" action="api/or_payment_controller.php?action=add" method="POST">
                        <input type="hidden" name="action" id="modalAction" value="add" />
                        <input type="hidden" name="id" id="itemId" value="" />
                        <input type="hidden" name="item_data" id="item_data" />
                        <input type="hidden" id="customer_name_hidden" name="customer_name">
                        <div class="row">
                            <div class="col-12 col-lg-8">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="row g-2">
                                            <!-- Customer Details Section -->
                                            <div class="col-12 mb-3">
                                                <h6 class="border-bottom pb-2">Customer Details</h6>
                                            </div>
                                            <!-- customer_id -->
                                            <div class="col-md-4 customer-details">
                                                <label for="customer_name" class="form-label">Customer</label>
                                                <select class="form-control form-control-sm" id="customer_id" name="customer_id" 
                                                    <?php if ($orPayments->status != 4) echo 'disabled'; ?>>
                                                    <?php
                                                    // Array to prevent duplicates
                                                    $used_customers = [];
                                                    foreach ($customers as $customer):
                                                        if (!in_array($customer->id, $used_customers)):
                                                            $used_customers[] = $customer->id; // Track used customers
                                                    ?>
                                                        <option value="<?= htmlspecialchars($customer->id) ?>"
                                                                <?= $customer->id == $orPayments->customer_id ? 'selected' : '' ?>>
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
                                                    name="customer_tin" value="<?= htmlspecialchars($orPayments->customer_tin) ?>" disabled>
                                            </div>
                                            <div class="col-md-4 customer-details">
                                                <label for="customer_email" class="form-label">Email</label>
                                                <input type="email" class="form-control form-control-sm" id="customer_email"
                                                    name="customer_email" value="<?= htmlspecialchars($orPayments->customer_email) ?>" disabled>
                                            </div>
                                            <div class="col-md-4 customer-details">
                                                <label for="billing_address" class="form-label">Billing Address</label>
                                                <input type="text" class="form-control form-control-sm" id="billing_address"
                                                    name="billing_address" value="<?= htmlspecialchars($orPayments->billing_address) ?>" disabled>
                                            </div>
                                            <div class="col-md-4 customer-details">
                                                <label for="business_style" class="form-label">Business Style</label>
                                                <input type="text" class="form-control form-control-sm" id="business_style"
                                                    name="business_style" value="<?= htmlspecialchars($orPayments->business_style) ?>" disabled>
                                            </div>
                                            <!-- location -->
                                            <div class="col-md-4 customer-details">
                                                <label for="location" class="form-label">Location</label>
                                                <select class="form-control form-control-sm" id="location" name="location" 
                                                    <?php if ($orPayments->status != 4) echo 'disabled'; ?>>
                                                    <?php
                                                        // Array to prevent duplicates
                                                        $used_locations = [];
                                                        $selected_location = $orPayments->location ?? ''; // Assuming this holds the selected location

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

                                            <!-- customer_po -->
                                            <div class="col-md-4 customer-details">
                                                <label for="customer_po" class="form-label">Customer PO No.</label>
                                                <input type="text" class="form-control form-control-sm" id="customer_po"
                                                    name="customer_po" value="<?= htmlspecialchars($orPayments->customer_po) ?>"
                                                    <?php if ($orPayments->status != 4) echo 'disabled'; ?>>
                                            </div>

                                            <!-- rep_no -->
                                            <div class="col-md-4 customer-details">
                                                <label for="rep" class="form-label">Rep No</label>
                                                <input type="text" class="form-control form-control-sm" id="rep" name="rep"
                                                    value="<?= htmlspecialchars($orPayments->rep) ?>"
                                                    <?php if ($orPayments->status != 4) echo 'disabled'; ?>>
                                            </div>

                                            <!-- OR Details Section -->
                                            <div class="col-12 mt-3 mb-3">
                                                <h6 class="border-bottom pb-2">Payment Information</h6>
                                            </div>

                                            <!-- or_number -->
                                            <div class="col-md-3 invoice-details">
                                                <label for="or_number" class="form-label">OR Number</label>
                                                <input type="text" class="form-control form-control-sm" id="or_number"
                                                    name="or_number" value="<?= htmlspecialchars($orPayments->or_number) ?>"
                                                    <?php if ($orPayments->status != 4) echo 'disabled'; ?>>
                                            </div>

                                            <!-- or_date -->
                                            <div class="col-md-3 invoice-details">
                                                <label for="or_date" class="form-label">Payment Date</label>
                                                <input type="date" class="form-control form-control-sm" id="or_date"
                                                    name="or_date" value="<?= htmlspecialchars($orPayments->or_date) ?>"
                                                    <?php if ($orPayments->status != 4) echo 'disabled'; ?>>
                                            </div>

                                            <!-- or_account_id -->
                                            <div class="col-md-3 invoice-details">
                                                <label for="or_account_id" class="form-label">Account</label>
                                                <select class="form-select form-select-sm" id="or_account_id" name="or_account_id" required
                                                    <?php if ($orPayments->status != 4) echo 'disabled'; ?>>
                                                    <option value="">Select Account</option>
                                                    <!-- Populate with account options -->
                                                </select>
                                            </div>

                                            <!-- payment_method -->
                                            <div class="col-md-3 invoice-details">
                                                <label for="payment_method" class="form-label">Payment Method</label>
                                                <select class="form-select" id="payment_method" name="payment_method" 
                                                    value="<?= htmlspecialchars($orPayments->payment_method) ?>"
                                                    <?php if ($orPayments->status != 4) echo 'disabled'; ?>>
                                                    <?php
                                                        // Array to prevent duplicates
                                                        $used_payment_methods = [];
                                                        $selected_payment_method = $orPayments->payment_method ?? ''; // Assuming this holds the selected payment method

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


                                           <!-- check_no -->
                                            <div class="col-md-3 invoice-details">
                                                <label for="check_no" class="form-label">Check No</label>
                                                <input type="text" class="form-control form-control-sm" id="check_no"
                                                    name="check_no" value="<?= htmlspecialchars($orPayments->check_no) ?>"
                                                    <?php if ($orPayments->status != 4) echo 'disabled'; ?>>
                                            </div>

                                            <!-- so_no -->
                                            <div class="col-md-3 invoice-details">
                                                <label for="so_no" class="form-label">S.O No.</label>
                                                <input type="text" class="form-control form-control-sm" id="so_no" name="so_no"
                                                    value="<?= htmlspecialchars($orPayments->so_no) ?>"
                                                    <?php if ($orPayments->status != 4) echo 'disabled'; ?>>
                                            </div>

                                            <!-- memo -->
                                            <div class="col-md-6 invoice-details">
                                                <label for="memo" class="form-label">Memo</label>
                                                <input type="text" class="form-control form-control-sm" id="memo" name="memo"
                                                    value="<?= htmlspecialchars($orPayments->memo) ?>"
                                                    <?php if ($orPayments->status != 4) echo 'disabled'; ?>>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-lg-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title">Summary</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check form-switch mb-2">
                                            <input class="form-check-input" type="checkbox" id="cash_sales" name="cash_sales"
                                                checked disabled>
                                            <label class="form-check-label fw-bold" for="cash_sales">Cash Sales</label>
                                        </div>
                                        <!-- gross_amount -->
                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Gross Amount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="gross_amount" name="gross_amount" value="<?= number_format($orPayments->gross_amount, 2, '.', ',') ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Discount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="discount_amount" name="discount_amount" value="<?= number_format($orPayments->discount_amount, 2, '.', ',') ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Net Amount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="net_amount_due" name="net_amount_due" value="<?= number_format($orPayments->net_amount_due, 2, '.', ',') ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">VAT:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="vat_amount" name="vat_amount" value="<?= number_format($orPayments->vat_amount, 2, '.', ',') ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Vatable 12%:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="vatable_amount" name="vatable_amount" value="<?= number_format($orPayments->vatable_amount, 2, '.', ',') ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Zero-rated:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="zero_rated_amount" name="zero_rated_amount" value="<?= number_format($orPayments->zero_rated_amount, 2, '.', ',') ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Vat-Exempt:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="vat_exempt_amount" name="vat_exempt_amount" value="<?= number_format($orPayments->vat_exempt_amount, 2, '.', ',') ?>" readonly>
                                            </div>
                                        </div>

                                        <?php if ($orPayments->status == 4): ?>
                                    <!-- Show editable Tax Withheld dropdown when status is 4 -->
                                        <!-- tax_withheld_percentage -->
                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Tax Withheld (%):</label>
                                            <div class="col-sm-6">
                                                <select class="form-select form-select-sm" id="tax_withheld_percentage"
                                                    name="tax_withheld_percentage">
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
                                                                <?= $wtax->id == $orPayments->tax_withheld_percentage ? 'selected' : '' ?>>
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
                                                            <?= $wtax->id == $orPayments->tax_withheld_percentage ? 'selected' : '' ?>>
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
                                                <input type="text" class="form-control-plaintext text-end" id="tax_withheld_amount" name="tax_withheld_amount" value="<?= number_format($orPayments->tax_withheld_amount, 2, '.', ',') ?>" readonly>
                                                <input type="hidden" name="tax_withheld_account_id" id="tax_withheld_account_id">
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label fw-bold">Total Amount Due:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end fw-bold" id="total_amount_due" name="total_amount_due" value="<?= number_format($orPayments->total_amount_due, 2, '.', ',') ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                        <div class="card-footer d-flex justify-content-center">
                                        <?php if ($orPayments->status == 4): ?>
                                            <button type="button" id="saveDraftBtn" class="btn btn-secondary me-2">Update Draft</button>
                                            <button type="submit" class="btn btn-info me-2">Save as Final</button>
                                        <?php elseif ($orPayments->status == 3): ?>
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
                                        <h5 class="card-title mb-0">Items</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover" id="itemTable">
                                                <thead class="bg-light" style="font-size: 12px;">
                                                    <tr>
                                                        <th style="width: 15%;">Item</th>
                                                        <th style="width: 9%;">Description</th>
                                                        <th style="width: 8%;">Unit</th>
                                                        <th class="text-right" style="width: 3%;">Quantity</th>
                                                        <th class="text-right" style="width: 8%;">Cost</th>
                                                        <th class="text-right" style="width: 8%;">Amount</th>
                                                        <th style="width: 8%;">Discount Type</th>
                                                        <th class="text-right" style="width: 8%;">Discount</th>
                                                        <th class="text-right" style="width: 8%;">Net</th>
                                                        <th class="text-right" style="width: 8%;">Tax Amount</th>
                                                        <th style="width: 10%;">Tax Type</th>
                                                        <th class="text-right" style="width: 10%;">VAT</th>
                                                        <th style="width: 4%;"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="itemTableBody" style="font-size: 14px;">
                                                    <!-- Items will be dynamically added here -->
                                                      <!-- Existing rows or dynamically added rows will be appended here -->
                                                      <?php
                                                        if ($orPayments) {
                                                            $isEditable = $orPayments->status == 4;
                                                            foreach ($orPayments->details as $detail) {
                                                                ?>
                                                                <tr>
                                                                    <td>
                                                                        <select class="form-control form-control-sm item-dropdown select2" id="item_id" name="item_id[]"
                                                                            <?php if (!$isEditable) echo 'disabled'; ?>>
                                                                            <?php foreach ($products as $product): ?>
                                                                                <option value="<?= htmlspecialchars($product->id) ?>"
                                                                                    <?= ($product->id == $detail['item_id']) ? 'selected' : '' ?>
                                                                                    data-item-name="<?= htmlspecialchars($product->item_name) ?>"
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
                                                                        <input type="text" class="form-control form-control-sm item_sales_description" name="item_sales_description[]"
                                                                            value="<?= htmlspecialchars($detail['item_sales_description']) ?>" readonly>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control form-control-sm uom_name" name="uom_name[]"
                                                                            value="<?= htmlspecialchars($detail['uom_name']) ?>" readonly>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control form-control-sm quantity" name="quantity[]"
                                                                            value="<?= htmlspecialchars($detail['quantity']) ?>" placeholder="Qty"
                                                                            <?php if (!$isEditable) echo 'disabled'; ?>>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control form-control-sm cost" name="cost[]"
                                                                            value="<?= number_format($detail['cost'], 2, '.', ',') ?>" placeholder="Enter Cost"
                                                                            <?php if (!$isEditable) echo 'disabled'; ?>>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control form-control-sm amount" name="amount[]"
                                                                            value="<?= number_format($detail['amount'], 2, '.', ',') ?>" placeholder="Amount" readonly>
                                                                    </td>
                                                                    <td>
                                                                        <select class="form-control form-control-sm discount_percentage select2" name="discount_percentage[]" <?php if (!$isEditable) echo 'disabled'; ?>>
                                                                            <?php foreach ($discounts as $discount): ?>
                                                                                <option value="<?= htmlspecialchars($discount->discount_rate) ?>" data-account-id="<?= htmlspecialchars($discount->discount_account_id) ?>" <?= ($discount->discount_rate == $detail['discount_percentage']) ? 'selected' : '' ?>>
                                                                                    <?= htmlspecialchars($discount->discount_name) ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control form-control-sm discount_amount" name="discount_amount[]"
                                                                            value="<?= number_format($detail['discount_amount'], 2, '.', ',') ?>" readonly>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control form-control-sm net_amount_before_sales_tax" name="net_amount_before_sales_tax[]"
                                                                            value="<?= number_format($detail['net_amount_before_sales_tax'], 2, '.', ',') ?>" readonly>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control form-control-sm net_amount" name="net_amount[]"
                                                                            value="<?= number_format($detail['net_amount'], 2, '.', ',') ?>" readonly>
                                                                    </td>

                                                                    <td>
                                                                        <select class="form-control form-control-sm sales_tax_percentage select2" name="sales_tax_percentage[]" <?php echo ($orPayments->status != 4) ? 'disabled' : ''; ?>>
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

                                                                    <td style="display: none;">
                                                                        <input type="hidden" class="output_vat_id" name="output_vat_id[]" value="<?= htmlspecialchars($detail['output_vat_id']) ?>">
                                                                    </td>

                                                                    <td>
                                                                        <input type="text" class="form-control form-control-sm sales_tax_amount" name="sales_tax_amount[]"
                                                                            value="<?= number_format($detail['sales_tax_amount'], 2, '.', ',') ?>" readonly>
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-sm btn-danger removeRow"
                                                                            <?php if (!$isEditable) echo 'disabled'; ?>>
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
        <div class="message">Processing Payment</div>
    </div>
</div>

<?php require 'views/templates/footer.php' ?>


<iframe id="printFrame" style="display:none;"></iframe>


<script>
    document.getElementById('cash_sales').addEventListener('change', function () {
        var labelText = this.checked ? "Cash Sales - Sales Receipt" : "Sales Invoice";
        document.getElementById('cash_sales_text').innerHTML = '&nbsp;&nbsp;' + labelText;
    });

    document.getElementById('officialReceiptFrom').addEventListener('submit', function () {
        const selectElement = document.getElementById('customer_id');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const customerName = selectedOption.getAttribute('data-customer-name');

        document.getElementById('customer_name_hidden').value = customerName;
        console.log(customerName);
    });

    document.addEventListener("DOMContentLoaded", function () {
        var cashSalesSwitch = document.getElementById('cash_sales');
        var invoiceAccountSelect = document.getElementById('or_account_id');
        var accounts = <?php echo json_encode($accounts); ?>;

        function updateLabelAndOptions() {
            var isCashSales = cashSalesSwitch.checked;

            // Clear existing options
            invoiceAccountSelect.innerHTML = '';

            // Add options based on the state of the cashSalesSwitch
            accounts.forEach(function (account) {
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
    });
</script>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('reprintButton').addEventListener('click', function (e) {
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
                    printMethod(<?= $orPayments->id ?>, 2);  // Pass 2 for reprint
                }
            });
        });

        // Attach event listener for the void button
        document.getElementById('voidButton').addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Void This Official Receipt?',
                text: "Are you sure you want to void this OR? This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, void it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    voidCheck(<?= $orPayments->id ?>);
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

    function printMethod(id, printStatus) {
        $.ajax({
            url: 'api/or_payment_controller.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'update_print_status',
                id: id,
                print_status: printStatus
            },
            success: function (response) {
                if (response.success) {
                    console.log('Print status updated, now printing receipt:', id);
                    const printFrame = document.getElementById('printFrame');
                    const printContentUrl = `print_or?action=print&id=${id}`;

                    printFrame.src = printContentUrl;

                    printFrame.onload = function () {
                        printFrame.contentWindow.focus();
                        printFrame.contentWindow.print();
                    };
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update print status: ' + (response.message || 'Unknown error')
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
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
            url: 'api/or_payment_controller.php',
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
                        text: 'OR has been voided successfully.'
                    }).then(() => {
                        location.reload(); // Reload the page to reflect changes
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to void or: ' + (response.message || 'Unknown error')
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
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

<script>

    // Function to format number with commas and two decimal places
    function formatNumber(num) {
        return num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    $(document).ready(function () {

        $('#item_id').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            allowClear: false
        });

        $('#sales_tax_percentage').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            allowClear: false
        });

        $('#discount_percentage').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            allowClear: false
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


        $('#customer_id').change(function () {
            var customerId = $(this).val();
            if (customerId === '') {
                $('#customer_tin').val('');
                // Clear other fields as needed
                return;
            }

            // Find the selected customer object by customerId
            var selectedCustomer = <?= json_encode($customers); ?>.find(function (customer) {
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
                    <td><input type="text" class="form-control form-control-sm uom" name="uom[]" readonly></td>
                    <td><input type="text" class="form-control form-control-sm quantity" name="quantity[]" placeholder="Qty"></td>
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


            $newRow.find('.quantity, .cost, .discount_percentage, .sales_tax_percentage, .sales_tax_amount').on('input', function () {
                calculateRowValues($(this).closest('tr'));
                calculateTotalAmount();
            });

            calculateRowValues($newRow);
            calculateTotalAmount();
        });



        // Function to unformat number (remove commas)
        function unformatNumber(str) {
            return parseFloat(str.replace(/,/g, '')) || 0;
        }

        function calculateRowValues(row) {
            const quantity = unformatNumber(row.find('.quantity').val());
            const cost = unformatNumber(row.find('.cost').val());
            const discountPercentage = parseFloat(row.find('.discount_percentage').val()) || 0;
            const salesTaxPercentage = parseFloat(row.find('.sales_tax_percentage').val()) || 0;

            const amount = quantity * cost;
            const discountAmount = (amount * discountPercentage) / 100;
            const netAmountBeforeTax = amount - discountAmount;
            const salesTaxAmount = (netAmountBeforeTax / (1 + salesTaxPercentage / 100)) * (salesTaxPercentage / 100);
            const netAmount = netAmountBeforeTax - salesTaxAmount;

            // Set raw values as data attributes and formatted values for display
            row.find('.amount').val(formatNumber(amount)).data('raw-value', amount);
            row.find('.discount_amount').val(formatNumber(discountAmount)).data('raw-value', discountAmount);
            row.find('.net_amount_before_sales_tax').val(formatNumber(netAmountBeforeTax)).data('raw-value', netAmountBeforeTax);
            row.find('.sales_tax_amount').val(formatNumber(salesTaxAmount)).data('raw-value', salesTaxAmount);
            row.find('.net_amount').val(formatNumber(netAmount)).data('raw-value', netAmount);
        }

        function calculateTotalAmount() {
            const orStatus = <?php echo json_encode($orPayments->status); ?>;

            if (orStatus == 4) { // Only calculate if it's a draft (status 4)
                const totals = {
                    totalAmount: 0,
                    totalDiscountAmount: 0,
                    totalNetAmountBeforeTax: 0,
                    totalInputVatAmount: 0,
                    vatableAmount: 0,
                    zeroRatedAmount: 0,
                    vatExemptAmount: 0,
                    notApplicableAmount: 0,
                    nonVatableAmount: 0
                };

                $('.amount, .discount_amount, .net_amount_before_sales_tax, .sales_tax_amount, .net_amount').each(function () {
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
                        if (inputVatName.includes('12%')) {
                            totals.vatableAmount += value;
                        } else if (inputVatName.includes('E')) {
                            totals.vatExemptAmount += value;
                        } else if (inputVatName.includes('Z')) {
                            totals.zeroRatedAmount += value;
                        } else if (inputVatName.includes('NA')) {
                            totals.notApplicableAmount += value;
                        } else if (inputVatName.includes('NV')) {
                            totals.nonVatableAmount += value;
                        } else {
                            totals.vatableAmount += value;
                        }
                    }
                });

                // Format and display values
                $("#gross_amount").val(formatNumber(totals.totalAmount));
                $("#discount_amount").val(formatNumber(totals.totalDiscountAmount));
                $("#net_amount_due").val(formatNumber(totals.totalNetAmountBeforeTax));
                $("#vat_amount").val(formatNumber(totals.totalInputVatAmount));
                $("#vatable_amount").val(formatNumber(totals.vatableAmount));
                $("#zero_rated_amount").val(formatNumber(totals.zeroRatedAmount));
                $("#vat_exempt_amount").val(formatNumber(totals.vatExemptAmount));

                // Store raw values in hidden fields or as data attributes
                $("#gross_amount").data('raw-value', totals.totalAmount);
                $("#discount_amount").data('raw-value', totals.totalDiscountAmount);
                $("#net_amount_due").data('raw-value', totals.totalNetAmountBeforeTax);
                $("#vat_amount").data('raw-value', totals.totalInputVatAmount);
                $("#vatable_amount").data('raw-value', totals.vatableAmount);
                $("#zero_rated_amount").data('raw-value', totals.zeroRatedAmount);
                $("#vat_exempt_amount").data('raw-value', totals.vatExemptAmount);

                // Get the selected tax withheld option
                const selectedTaxWithheld = $("#tax_withheld_percentage option:selected");
                const taxWithheldPercentage = parseFloat(selectedTaxWithheld.data('rate')) || 0;
                const taxWithheldAccountId = selectedTaxWithheld.val();

                // Calculate taxable base (including all relevant amounts)
                const taxableBase = totals.vatableAmount + totals.zeroRatedAmount + totals.vatExemptAmount + totals.notApplicableAmount + totals.nonVatableAmount;
                
                // Calculate tax withheld amount
                const taxWithheldAmount = (taxWithheldPercentage / 100) * taxableBase;

                // Format and display tax withheld amount
                $("#tax_withheld_amount").val(formatNumber(taxWithheldAmount));
                $("#tax_withheld_account_id").val(taxWithheldAccountId);

                // Store the tax withheld ID
                $("#tax_withheld_percentage").data('selected-id', taxWithheldAccountId);

                // Calculate total amount due
                const subtotal = totals.totalInputVatAmount + taxableBase;

                const totalAmountDue = subtotal - taxWithheldAmount;

                $("#total_amount_due").val(formatNumber(totalAmountDue));

                // Store raw values if needed for further processing or database insertion
                $("#tax_withheld_amount").data('raw-value', taxWithheldAmount);
                $("#total_amount_due").data('raw-value', totalAmountDue);
                
            } else {
                // For non-Draft statuses, display existing values without recalculation
                const existingTaxWithheldAmount = parseFloat($("#tax_withheld_amount").val().replace(/,/g, '')) || 0;
                const existingTotalAmountDue = parseFloat($("#total_amount_due").val().replace(/,/g, '')) || 0;

                $("#tax_withheld_amount").val(formatNumber(existingTaxWithheldAmount));
                $("#total_amount_due").val(formatNumber(existingTotalAmountDue));
            }
        }

        // Helper function to format numbers with commas and two decimal places
        function formatNumber(value) {
            return parseFloat(value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }


        function updateFieldValues(fieldName, value) {
            $(`#${fieldName}`).val(formatNumber(value));
            $(`#${fieldName}_raw`).val(value);
        }


        // Event listener for tax withheld percentage change
        $('#tax_withheld_percentage').on('change', function () {
            calculateTotalAmount();
        });
        // Function to initialize event listeners for a row
        function initializeRowListeners(row) {
            row.find('.quantity, .cost, .discount_percentage, .sales_tax_percentage').on('input change', function () {
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
        $('#itemTableBody tr').each(function () {
            initializeRowListeners($(this));
            calculateRowValues($(this));
        });
        
        // Calculate total amount after initializing existing rows
        calculateTotalAmount();

        // Add new row (modified to use initializeRowListeners)
        $('#addItemBtn').click(() => {
            // ... (existing code for creating new row)

            const $newRow = $(newRow);
            $('#itemTableBody').append($newRow);

            initializeRowListeners($newRow);

            calculateRowValues($newRow);
            calculateTotalAmount();
        });

        // REMOVE ITEM
        $(document).on('click', '.removeRow', function () {
            $(this).closest('tr').remove();
            calculateRowValues($(this).closest('tr'));
            calculateTotalAmount();
        });

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
        

        // Function to get unique discount account IDs
        function getUniqueDiscountAccountIds() {
            const uniqueIds = new Set();
            $('#itemTableBody tr').each(function () {
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
            $('#itemTableBody tr').each(function () {
                const outputVatId = $(this).find('.sales_tax_percentage option:selected').data('account-id');
                if (outputVatId) {
                    uniqueIds.add(outputVatId);
                }
            });
            return Array.from(uniqueIds);
        }

        // Update the gatherTableItems function to use raw values
        function gatherTableItems() {
            const items = [];
            $('#itemTableBody tr').each(function (index) {
                const item = {
                    item_id: $(this).find('select[name="item_id[]"]').val(),
                    item_name: $(this).find('.item-name').val(),
                    quantity: $(this).find('input[name="quantity[]"]').val(),
                    cost: $(this).find('input[name="cost[]"]').val(),
                    cost_price: $(this).find('input[name="cost_price[]"]').data('raw-value') || 0,
                    amount: $(this).find('input[name="amount[]"]').data('raw-value') || 0,
                    discount_percentage: parseFloat($(this).find('select[name="discount_percentage[]"]').val()) || 0,
                    discount_amount: $(this).find('input[name="discount_amount[]"]').data('raw-value') || 0,
                    net_amount_before_sales_tax: $(this).find('input[name="net_amount_before_sales_tax[]"]').data('raw-value') || 0,
                    net_amount: $(this).find('input[name="net_amount[]"]').data('raw-value') || 0,
                    sales_tax_percentage: parseFloat($(this).find('select[name="sales_tax_percentage[]"]').val()) || 0,
                    sales_tax_amount: $(this).find('input[name="sales_tax_amount[]"]').data('raw-value') || 0,
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
        
        const saveDraftBtn = document.getElementById('saveDraftBtn');

        // Add click event listener
        saveDraftBtn.addEventListener('click', function(event) {
            event.preventDefault();

            // Check if the table has any rows
            if ($('#itemTableBody tr').length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Items',
                    text: 'You must add at least one item before updating the draft.'
                });
                document.getElementById('loadingOverlay').style.display = 'none';
                return false;
            }

            const items = gatherTableItems();
            $('#item_data').val(JSON.stringify(items));

            // Get tax withheld data
            const selectedTaxWithheld = $("#tax_withheld_percentage option:selected");
            const taxWithheldId = selectedTaxWithheld.val(); // Get the selected option's value (ID)
            const taxWithheldPercentage = selectedTaxWithheld.data('rate') || 0;
            const taxWithheldAccountId = selectedTaxWithheld.data('account-id') || '';

            // Show loading overlay
            document.getElementById('loadingOverlay').style.display = 'flex';

            $.ajax({
                url: 'api/or_payment_controller.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'update_draft',
                    id: <?= json_encode($orPayments->id) ?>,
                    or_number: $('#or_number').val(),
                    or_date: $('#or_date').val(),
                    or_account_id: $('#or_account_id').val(),
                    customer_id: $('#customer_id').val(),
                    customer_name: $('#customer_name_hidden').val(),
                    customer_po: $('#customer_po').val(),
                    so_no: $('#so_no').val(),
                    check_no: $('#check_no').val(),
                    rep: $('#rep').val(),
                    payment_method: $('#payment_method').val(),
                    location: $('#location').val(),
                    memo: $('#memo').val(),
                    gross_amount: $("#gross_amount").data('raw-value'),
                    discount_amount: $("#discount_amount").data('raw-value'),
                    net_amount_due: $("#net_amount_due").data('raw-value'),
                    vat_amount: $("#vat_amount").data('raw-value'),
                    vatable_amount: $("#vatable_amount").data('raw-value'),
                    zero_rated_amount: $("#zero_rated_amount").data('raw-value'),
                    vat_exempt_amount: $("#vat_exempt_amount").data('raw-value'),
                    tax_withheld_percentage: taxWithheldId,
                    // tax_withheld_percentage: taxWithheldPercentage,
                    tax_withheld_amount: $("#tax_withheld_amount").data('raw-value'),
                    total_amount_due: $("#total_amount_due").data('raw-value'),
                    item_data: JSON.stringify(items),
                },
                success: function (response) {
                    document.getElementById('loadingOverlay').style.display = 'none';

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Draft updated successfully!',
                            showCancelButton: false,
                            confirmButtonText: 'Close'
                        }).then((result) => {
                            if (result.isConfirmed) {
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
                error: function (jqXHR, textStatus, errorThrown) {
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
        });

        $('#officialReceiptFrom').submit(function (event) {
            event.preventDefault();

            // Check if the table has any rows
            if ($('#itemTableBody tr').length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Items',
                    text: 'You must add at least one item before submitting the official receipt.'
                });
                document.getElementById('loadingOverlay').style.display = 'none';
                return false;
            }

            const items = gatherTableItems();
            $('#item_data').val(JSON.stringify(items));

            // Get tax withheld data
            const selectedTaxWithheld = $("#tax_withheld_percentage option:selected");
            const taxWithheldId = selectedTaxWithheld.val(); // Get the selected option's value (ID)
            const taxWithheldPercentage = selectedTaxWithheld.data('rate') || 0;
            const taxWithheldAccountId = selectedTaxWithheld.data('account-id') || '';

            // Show loading overlay
            document.getElementById('loadingOverlay').style.display = 'flex';

            $.ajax({
                url: 'api/or_payment_controller.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'save_final',
                    id: <?= json_encode($orPayments->id) ?>,
                    or_number: $('#or_number').val(),
                    or_date: $('#or_date').val(),
                    or_account_id: $('#or_account_id').val(),
                    customer_id: $('#customer_id').val(),
                    customer_name: $('#customer_name_hidden').val(),
                    customer_po: $('#customer_po').val(),
                    so_no: $('#so_no').val(),
                    check_no: $('#check_no').val(),
                    rep: $('#rep').val(),
                    payment_method: $('#payment_method').val(),
                    location: $('#location').val(),
                    memo: $('#memo').val(),
                    gross_amount: $("#gross_amount").data('raw-value'),
                    discount_amount: $("#discount_amount").data('raw-value'),
                    net_amount_due: $("#net_amount_due").data('raw-value'),
                    vat_amount: $("#vat_amount").data('raw-value'),
                    vatable_amount: $("#vatable_amount").data('raw-value'),
                    zero_rated_amount: $("#zero_rated_amount").data('raw-value'),
                    vat_exempt_amount: $("#vat_exempt_amount").data('raw-value'),
                    tax_withheld_percentage: taxWithheldId, // Send the ID instead of the rate
                    tax_withheld_amount: $("#tax_withheld_amount").data('raw-value'),
                    total_amount_due: $("#total_amount_due").data('raw-value'),
                    item_data: JSON.stringify(items),
                },
                success: function (response) {
                    document.getElementById('loadingOverlay').style.display = 'none';

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Official Receipt submitted successfully!',
                            showCancelButton: true,
                            confirmButtonText: 'Print',
                            cancelButtonText: 'Close'
                        }).then((result) => {
                            if (result.isConfirmed && response.id) {
                                printMethod(response.id, 1); // Pass 1 for initial print
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error saving official receipt: ' + (response.message || 'Unknown error')
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
                        text: 'An error occurred while saving the official receipt: ' + textStatus
                    });
                }
            });
        });

        
    });

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
            row.find('.cost').val(formatNumber(parseFloat(costPrice)));
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