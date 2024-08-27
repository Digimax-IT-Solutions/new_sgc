<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();
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
// $invoiceDetails = Invoice::find($id);

// // Output JSON encoded invoice details
// echo json_encode($invoiceDetails);

$page = 'sales_invoice'; // Set the variable corresponding to the current page
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <style>
        .table-sm .form-control {
            border: none;
            /* Remove border */
            padding: 0;
            /* Remove default padding */
            background-color: transparent;
            /* Make background transparent */
            box-shadow: none;
            /* Remove box shadow */
            height: auto;
            /* Auto height to fit content */
            line-height: inherit;
            /* Inherit line-height from the table */
            font-size: inherit;
            /* Inherit font-size from the table */
        }

        .select2-no-border .select2-selection {
            border: none !important;
            padding: 0 !important;
            box-shadow: none !important;
        }

        .select2-no-border .select2-selection__rendered {
            padding: 0 !important;
            /* Adjust if necessary */
        }



        .custom-control-input {
            transform: scale(1.4);
        }

        .form-check-label {
            /* Adjust font size and any other styling as needed */
            font-size: .9rem;
            /* Example font size */
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
        }

        .loading-overlay .spinner {
            border: 16px solid #54BD69;
            border-top: 16px solid #fff;
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
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
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="h3"><strong>View Invoice</strong></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="invoice">Invoices</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View Invoice</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <?php if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $invoice = Invoice::find($id);
                if ($invoice) { ?>
                    <form id="writeCheckForm" action="api/invoice_controller.php" method="POST">
                        <input type="hidden" name="action" id="modalAction" value="update">
                        <input type="hidden" id="item_data" name="item_data">
                        <input type="hidden" name="id" id="itemId" value="<?= $invoice->id ?>">
                        <!-- INVOICE DETAILS -->
                        <div class="row">
                            <!-- INVOICE CUSTOMER DETAILS -->
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
                                                <select class="form-control form-control-sm" id="customer_name"
                                                    name="customer_name">
                                                    <?php $selectedCustomerName = '';
                                                    foreach ($customers as $customer) {
                                                        if ($customer->id == $invoice->customer_id) {
                                                            $selectedCustomerName = $customer->customer_name;
                                                            break;
                                                        }
                                                    }
                                                    ?>
                                                    <!-- Display the selected customer name as the default option -->
                                                    <option value="<?= $invoice->customer_id ?>"><?= $selectedCustomerName ?>
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
                                                    name="customer_tin" value="<?= htmlspecialchars($invoice->customer_tin) ?>">
                                            </div>

                                            <div class="col-md-4 customer-details">
                                                <label for="customer_email" class="form-label">Email</label>
                                                <input type="text" class="form-control form-control-sm" id="customer_email"
                                                    name="customer_email"
                                                    value="<?= htmlspecialchars($invoice->customer_email) ?>">
                                            </div>

                                            <div class="col-md-4 customer-details">
                                                <label for="billing_address" class="form-label">Billing Address</label>
                                                <input type="text" class="form-control form-control-sm" id="billing_address"
                                                    name="billing_address"
                                                    value="<?= htmlspecialchars($invoice->billing_address) ?>">
                                            </div>

                                            <div class="col-md-4 customer-details">
                                                <label for="shipping_address" class="form-label">Shipping Address</label>
                                                <input type="text" class="form-control form-control-sm" id="shipping_address"
                                                    name="shipping_address"
                                                    value="<?= htmlspecialchars($invoice->shipping_address) ?>">
                                            </div>

                                            <div class="col-md-4 customer-details">
                                                <label for="business_style" class="form-label">Business Style</label>
                                                <input type="text" class="form-control form-control-sm" id="business_style"
                                                    name="business_style"
                                                    value="<?= htmlspecialchars($invoice->business_style) ?>">
                                            </div>

                                            <div class="col-md-4 customer-details">
                                                <label for="payment_method" class="form-label">Payment Method</label>
                                                <select class="form-control form-control-sm" id="payment_method"
                                                    name="payment_method">
                                                    <option value="<?= $invoice->payment_method ?>">
                                                        <?= $invoice->payment_method ?>
                                                    </option>
                                                    <?php foreach ($payment_methods as $payment_method): ?>
                                                        <option value="<?= $payment_method->payment_method_name ?>">
                                                            <?= $payment_method->payment_method_name ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="col-md-4 customer-details">
                                                <label for="location" class="form-label">Location</label>
                                                <select class="form-control form-control-sm" id="location" name="location">
                                                    <option value="<?= $invoice->location ?>"><?= $invoice->location ?></option>
                                                    <?php foreach ($locations as $location): ?>
                                                        <option value="<?= $location->name ?>"><?= $location->name ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4 customer-details">
                                                <label for="customer_po" class="form-label">Customer PO No.</label>
                                                <input type="text" class="form-control form-control-sm" id="customer_po"
                                                    name="customer_po" value="<?= $invoice->customer_po ?>">
                                            </div>
                                        </div>

                                        <!-- Invoice Details Section -->
                                        <div class="col-12 mt-3 mb-3">
                                            <h6 class="border-bottom pb-2">Invoice Information</h6>
                                        </div>

                                        <div class="row g-2">
                                            <div class="col-md-3 invoice-details">
                                                <label for="invoice_number" class="form-label">Invoice Number</label>
                                                <input type="text" class="form-control form-control-sm" id="invoice_number"
                                                    name="invoice_number" placeholder="Enter invoice #"
                                                    value="<?= $invoice->invoice_number ?>">
                                            </div>

                                            <div class="col-md-3 invoice-details">
                                                <label for="invoice_date" class="form-label">Invoice Date</label>
                                                <input type="date" class="form-control form-control-sm" id="invoice_date"
                                                    name="invoice_date" value="<?php echo date('Y-m-d'); ?>" required>
                                            </div>

                                            <div class="col-md-3 invoice-details">
                                                <label for="invoice_due_date" class="form-label">Due Date</label>
                                                <input type="date" class="form-control form-control-sm" id="invoice_due_date"
                                                    name="invoice_due_date" value="<?= $invoice->invoice_due_date ?>">
                                            </div>

                                            <div class="col-md-3 invoice-details">
                                                <label for="terms" class="form-label">Terms</label>
                                                <select class="form-control form-control-sm" id="invoice_terms" name="terms">
                                                    <option value="">Select Terms</option>
                                                    <option value="<?= htmlspecialchars($invoice->terms) ?>" selected>
                                                        <?= htmlspecialchars($invoice->terms) ?>
                                                    </option>
                                                </select>
                                            </div>

                                            <div class="col-md-3 invoice-details">
                                                <label for="invoice_account_id" class="form-label">Account</label>
                                                <select class="form-select form-select-sm" id="invoice_account_id"
                                                    name="invoice_account_id" required>
                                                    <option value="">Select Account</option>
                                                    <!-- Populate with account options -->
                                                </select>
                                            </div>

                                            <div class="col-md-3 invoice-details">
                                                <label for="rep" class="form-label">Rep</label>
                                                <input type="text" class="form-control form-control-sm" id="rep" name="rep"
                                                    value="<?= $invoice->rep ?>">
                                            </div>

                                            <div class="col-md-3 invoice-details">
                                                <label for="so_no" class="form-label">S.O No.</label>
                                                <input type="text" class="form-control form-control-sm" id="so_no" name="so_no"
                                                    value="<?= $invoice->so_no ?>">
                                            </div>
                                            <div class="col-md-3 invoice-details">
                                                <label for="memo" class="form-label">Memo</label>
                                                <input type="text" class="form-control form-control-sm" id="memo" name="memo"
                                                    value="<?= $invoice->memo ?>">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- INVOICE DETAILS -->
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
                                                    name="gross_amount" value="<?= $invoice->gross_amount ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Discount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="discount_amount"
                                                    name="discount_amount" value="<?= $invoice->discount_amount ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Net Amount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="net_amount_due"
                                                    name="net_amount_due" value="<?= $invoice->net_amount_due ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">VAT:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="vat_amount"
                                                    name="vat_amount" value="<?= $invoice->vat_amount ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Vatable 12%:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="vatable_amount"
                                                    name="vatable_amount" value="<?= $invoice->vatable_amount ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Zero-rated:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end"
                                                    id="zero_rated_amount" name="zero_rated_amount"
                                                    value="<?= $invoice->zero_rated_amount ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Vat-Exempt:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end"
                                                    id="vat_exempt_amount" name="vat_exempt_amount"
                                                    value="<?= $invoice->vat_exempt_amount ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Tax Withheld (%):</label>
                                            <div class="col-sm-6">
                                                <select class="form-control form-control-sm" id="tax_withheld_percentage"
                                                    name="tax_withheld_percentage" required>
                                                    <?php foreach ($wtaxes as $wtax): ?>
                                                        <option value="<?= htmlspecialchars($wtax->wtax_rate) ?>"
                                                            data-account-id="<?= htmlspecialchars($wtax->wtax_account_id) ?>"
                                                            <?= $wtax->wtax_rate == $invoice->tax_withheld_amount ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($wtax->wtax_name) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Tax Withheld Amount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end"
                                                    id="tax_withheld_amount" name="tax_withheld_amount" value="0.00" readonly>
                                                <input type="hidden" class="form-control" name="tax_withheld_account_id"
                                                    id="tax_withheld_account_id" value="<?= $invoice->tax_withheld_amount ?>"
                                                    readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label fw-bold">Total Amount Due:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end fw-bold"
                                                    id="total_amount_due" name="total_amount_due"
                                                    value="<?= $invoice->total_amount_due ?>" readonly>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="card-footer d-flex justify-content-center">

                                        <button type="button" class="btn btn-secondary me-2">Void</button>
                                        <a class="btn btn-primary" href="#" onclick="printInvoice(<?= $invoice->id ?>);">
                                            <i class="fas fa-print"></i> Reprint
                                        </a>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- INVOICE ITEMS -->
                        <div class="row mt-4">
                            <!-- INVOICE ITEMS -->
                            <div class="col-12 col-lg-12">
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
                                                    <!-- Existing rows or dynamically added rows will be appended here -->
                                                    <?php
                                                    if ($invoice) {
                                                        foreach ($invoice->details as $detail) {
                                                            ?>
                                                            <tr>
                                                                <td>
                                                                    <select class="form-control form-control-sm item-dropdown select2"
                                                                        name="item_id[]">

                                                                        <?php foreach ($products as $product): ?>
                                                                            <option value="<?= htmlspecialchars($product->id) ?>"
                                                                                <?= ($product->id == $detail['item_id']) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($product->item_name) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>

                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm item_sales_description"
                                                                        name="item_sales_description[]"
                                                                        value="<?= htmlspecialchars($detail['item_sales_description']) ?>"
                                                                        readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm uom_name"
                                                                        name="uom_name[]"
                                                                        value="<?= htmlspecialchars($detail['uom_name']) ?>" readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm quantity"
                                                                        name="quantity[]"
                                                                        value="<?= htmlspecialchars($detail['quantity']) ?>"
                                                                        placeholder="Qty">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm cost"
                                                                        name="cost[]" value="<?= htmlspecialchars($detail['cost']) ?>"
                                                                        placeholder="Enter Cost">
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm amount"
                                                                        name="amount[]"
                                                                        value="<?= htmlspecialchars($detail['amount']) ?>"
                                                                        placeholder="Amount" readonly>
                                                                </td>
                                                                <td>
                                                                    <select
                                                                        class="form-control form-control-sm discount-dropdown select2"
                                                                        name="discount_percentage[]">
                                                                        <?php foreach ($discounts as $discount): ?>
                                                                            <option
                                                                                value="<?= htmlspecialchars($discount->discount_rate) ?>"
                                                                                <?= ($discount->discount_rate == $detail['discount_percentage']) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($discount->discount_description) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm discount_amount"
                                                                        name="discount_amount[]"
                                                                        value="<?= htmlspecialchars($detail['discount_amount']) ?>"
                                                                        readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm net_amount_before_sales_tax"
                                                                        name="net_amount_before_sales_tax[]"
                                                                        value="<?= htmlspecialchars($detail['net_amount_before_sales_tax']) ?>"
                                                                        readonly>
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm net_amount"
                                                                        name="net_amount[]"
                                                                        value="<?= htmlspecialchars($detail['net_amount']) ?>" readonly>
                                                                </td>
                                                                <td>
                                                                    <select
                                                                        class="form-control form-control-sm sales_tax_percentage select2"
                                                                        name="sales_tax_percentage[]">
                                                                        <?php foreach ($input_vats as $vat): ?>
                                                                            <option value="<?= htmlspecialchars($vat->input_vat_rate) ?>"
                                                                                <?= ($vat->input_vat_rate == $detail['sales_tax_percentage']) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($vat->input_vat_name) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm sales_tax_amount"
                                                                        name="sales_tax_amount[]"
                                                                        value="<?= htmlspecialchars($detail['sales_tax_amount']) ?>"
                                                                        readonly>
                                                                </td>
                                                                <td>
                                                                    <button type="button" class="btn btn-sm btn-danger removeRow">
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
                                        <!-- <button type="button" class="btn btn-primary btn-sm" id="addItemBtn">
                                            <i class="fas fa-plus"></i> Add Item
                                        </button> -->
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
</div>

<iframe id="printFrame" style="display:none;"></iframe>
<div id="loadingOverlay" class="loading-overlay">
    <div class="spinner"></div>
</div>
<?php require 'views/templates/footer.php' ?>

<script>
    function showLoadingOverlay() {
        document.getElementById('loadingOverlay').style.display = 'flex';
    }

    function hideLoadingOverlay() {
        document.getElementById('loadingOverlay').style.display = 'none';
    }

    function printInvoice(id) {
        showLoadingOverlay();

        const printFrame = document.getElementById('printFrame');
        const printContentUrl = `print_invoice?action=print&id=${id}`;

        printFrame.src = printContentUrl;

        printFrame.onload = function () {
            printFrame.contentWindow.focus();
            printFrame.contentWindow.print();
            hideLoadingOverlay();
        };
    }
</script>

<script>
   document.addEventListener("DOMContentLoaded", function () {
    var cashSalesSwitch = document.getElementById('cash_sales');
    var invoiceAccountSelect = document.getElementById('invoice_account_id');
    var cashSalesText = document.getElementById('cash_sales_text');
    var accounts = <?php echo json_encode($accounts); ?>;

    // Store the initial account ID
    var initialAccountId = '<?php echo $invoice->invoice_account_id; ?>';

    // Function to update label text based on checkbox state
    function updateLabel() {
        var isCashSales = cashSalesSwitch.checked;
        var labelText = isCashSales ? "Cash Sales - Sales Receipt" : "Sales Invoice";
        cashSalesText.innerHTML = '&nbsp;&nbsp;' + labelText;
    }

    // Function to populate dropdown based on checkbox state
    function updateDropdown() {
        var isCashSales = cashSalesSwitch.checked;
        var accountNamesToShow = isCashSales
            ? ["Undeposited Fund"]
            : ["Accounts Receivable"];

        // Clear existing options
        invoiceAccountSelect.innerHTML = '';

        // Populate options based on account names
        accounts.forEach(function (account) {
            if (accountNamesToShow.includes(account.account_description)) {
                var option = document.createElement('option');
                option.value = account.id;
                option.setAttribute('data-account-type', account.account_type);
                option.textContent = account.account_description;
                invoiceAccountSelect.appendChild(option);

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
            cashSalesSwitch.checked = initialAccount.account_description === "Undeposited Fund";
            updateLabel();
            updateDropdown();
        }
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
    $(document).ready(function () {

        $(document).ready(function () {
            // Initialize Select2 for existing dropdowns in the table
            $('#itemTableBody').find('.select2').select2({
                width: '100%',
                theme: 'bootstrap-5' // Adjust this if using a different Bootstrap version
            });
        });

        $('#customer_name').select2({
            theme: 'bootstrap-5', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Customer',
            allowClear: true
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


        // Handle invoice terms change
        $('#invoice_terms').change(function () {
            var terms = $(this).val();
            var deliveryDate = calculateDeliveryDate(terms);
            $('#invoice_due_date').val(deliveryDate);
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
                <td><input type="text" class="form-control form-control-sm item_sales_description" name="item_sales_description[]" readonly></td>
                <td><input type="text" class="form-control form-control-sm uom_name" name="uom_name[]" readonly></td>
                <td><input type="text" class="form-control form-control-sm quantity" name="quantity[]" placeholder="Qty"></td>
                <td><input type="text" class="form-control form-control-sm cost" name="cost[]" placeholder="Enter Cost"></td>
                <td><input type="text" class="form-control form-control-sm amount" name="amount[]" placeholder="Amount" readonly></td>
                <td><select class="form-control form-control-sm discount-dropdown select2" name="discount_percentage[]">${discountDropdownOptions}</select></td>
                <td><input type="text" class="form-control form-control-sm discount_amount" name="discount_amount[]" readonly></td>
                <td><input type="text" class="form-control form-control-sm net_amount_before_sales_tax" name="net_amount_before_sales_tax[]" readonly></td>
                <td><input type="text" class="form-control form-control-sm net_amount" name="net_amount[]" readonly></td>
                <td><select class="form-control form-control-sm sales_tax_percentage select2" name="sales_tax_percentage[]">${inputVatDropdownOption}</select></td>
                <td><input type="text" class="form-control form-control-sm sales_tax_amount" name="sales_tax_amount[]" readonly></td>
                <td><button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-trash"></i></button></td>
                </tr>`;
            $('#itemTableBody').append(newRow);

            // Bind events for the new row
            bindRowEvents($('#itemTableBody tr:last-child'));
            calculateRowValues($('#itemTableBody tr:last-child'));
            calculateTotalAmount();

            // Initialize Select2 for the new row
            $('#itemTableBody tr:last-child').find('.select2').select2({
                width: '100%',
                theme: 'bootstrap-5' // Use this if you're using Bootstrap 4
            });
        });
        // Gather table items and submit form
        function gatherTableItems() {
            const items = [];
            $('#itemTableBody tr').each(function () {
                const item = {
                    item_id: $(this).find('select[name="item_id[]"]').val(),
                    quantity: $(this).find('input[name="quantity[]"]').val(),
                    cost: $(this).find('input[name="cost[]"]').val(),
                    amount: $(this).find('input[name="amount[]"]').val(),
                    discount_percentage: $(this).find('select[name="discount_percentage[]"]').val(),
                    discount_amount: $(this).find('input[name="discount_amount[]"]').val(),
                    discount_account_id: $(this).find('select[name="discount_percentage[]"] option:selected').data('account-id'),
                    net_amount_before_sales_tax: $(this).find('input[name="net_amount_before_sales_tax[]"]').val(),
                    net_amount: $(this).find('input[name="net_amount[]"]').val(),
                    sales_tax_percentage: $(this).find('select[name="sales_tax_percentage[]"]').val(),
                    sales_tax_amount: $(this).find('input[name="sales_tax_amount[]"]').val(),
                    input_vat_id: $(this).find('select[name="sales_tax_percentage[]"] option:selected').data('account-id')
                };
                items.push(item);
            });
            return items;
        }

        $('#writeCheckForm').submit(function (event) {
            event.preventDefault();
            if ($('#itemTableBody tr').length === 0) {
                alert("You must add at least one account before submitting the check.");
                return false;
            }
            const items = gatherTableItems();
            $('#item_data').val(JSON.stringify(items));
            this.submit();
        });
    });

    function bindRowEvents(row) {
        row.find('.quantity, .cost, .discount-dropdown, .sales_tax_percentage').on('input change', function () {
            calculateRowValues($(this).closest('tr'));
            calculateTotalAmount();
        });
    }

    // Calculate row values
    function calculateRowValues(row) {
        const quantity = parseFloat(row.find('.quantity').val()) || 0;
        const cost = parseFloat(row.find('.cost').val()) || 0;
        const discountPercentage = parseFloat(row.find('.discount-dropdown').val()) || 0;
        const salesTaxPercentage = parseFloat(row.find('.sales_tax_percentage').val()) || 0;

        const amount = quantity * cost;
        const discountAmount = (amount * discountPercentage) / 100;
        const netAmountBeforeTax = amount - discountAmount;
        const salesTaxAmount = (netAmountBeforeTax * salesTaxPercentage) / 100;
        const netAmount = netAmountBeforeTax + salesTaxAmount;

        row.find('.amount').val(amount.toFixed(2));
        row.find('.discount_amount').val(discountAmount.toFixed(2));
        row.find('.net_amount_before_sales_tax').val(netAmountBeforeTax.toFixed(2));
        row.find('.sales_tax_amount').val(salesTaxAmount.toFixed(2));
        row.find('.net_amount').val(netAmount.toFixed(2));
    }

    // Calculate total amounts
    function calculateTotalAmount() {
        const totals = {
            totalAmount: 0, totalDiscountAmount: 0, totalNetAmountBeforeTax: 0, totalInputVatAmount: 0,
            vatableAmount: 0, zeroRatedAmount: 0, vatExemptAmount: 0
        };

        $('#itemTableBody tr').each(function () {
            const row = $(this);
            totals.totalAmount += parseFloat(row.find('.amount').val()) || 0;
            totals.totalDiscountAmount += parseFloat(row.find('.discount_amount').val()) || 0;
            totals.totalNetAmountBeforeTax += parseFloat(row.find('.net_amount_before_sales_tax').val()) || 0;
            totals.totalInputVatAmount += parseFloat(row.find('.sales_tax_amount').val()) || 0;

            const inputVatName = row.find('.sales_tax_percentage option:selected').text();
            const netAmount = parseFloat(row.find('.net_amount').val()) || 0;

            if (inputVatName === '12%') totals.vatableAmount += netAmount;
            else if (inputVatName === 'E') totals.vatExemptAmount += netAmount;
            else if (inputVatName === 'Z') totals.zeroRatedAmount += netAmount;
        });

        $("#gross_amount").val(totals.totalAmount.toFixed(2));
        $("#discount_amount").val(totals.totalDiscountAmount.toFixed(2));
        $("#net_amount_due").val(totals.totalNetAmountBeforeTax.toFixed(2));
        $("#sales_tax_amount").val(totals.totalInputVatAmount.toFixed(2));
        $("#vatable_amount").val(totals.vatableAmount.toFixed(2));
        $("#zero_rated_amount").val(totals.zeroRatedAmount.toFixed(2));
        $("#vat_exempt_amount").val(totals.vatExemptAmount.toFixed(2));

        updateTaxWithheldAmount();
    }

    // Update tax withheld amount
    function updateTaxWithheldAmount() {
        const taxWithheldPercentage = parseFloat($("#tax_withheld_percentage option:selected").val()) || 0;
        const taxableBase = parseFloat($("#vatable_amount").val()) || 0;
        const taxWithheldAmount = (taxWithheldPercentage / 100) * taxableBase;

        $("#tax_withheld_amount").val(taxWithheldAmount.toFixed(2));

        const totalAmountDue = (parseFloat($("#gross_amount").val()) || 0) - taxWithheldAmount;
        $("#total_amount_due").val(totalAmountDue.toFixed(2));
    }

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