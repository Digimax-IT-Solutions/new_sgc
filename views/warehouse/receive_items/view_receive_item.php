<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('warehouse_receive_items');
$accounts = ChartOfAccount::all();
$vendors = Vendor::all();
$products = Product::all();
$terms = Term::all();
$locations = Location::all();
$wtaxes = WithholdingTax::all();
$discounts = Discount::all();
$input_vats = InputVat::all();
$sales_taxes = SalesTax::all();
$cost_centers = CostCenter::all();


?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>

<style>
    .card {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .card-header {
        background-color: #fff;
        color: white;
        border-radius: 8px 8px 0 0;
    }

    .form-control,
    .form-select {
        border-radius: 4px;
        border: 1px solid #ced4da;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    .btn-primary {
        background-color: #3498db;
        border-color: #3498db;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #2980b9;
        border-color: #2980b9;
        transform: translateY(-2px);
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    #itemTable {
        min-width: 1500px;
        /* Adjust this value based on your table's content */
        table-layout: fixed;
    }

    #itemTable th {
        white-space: nowrap;
    }

    #itemTable tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #itemTable tbody tr:hover {
        background-color: #e6f3ff;
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
                <div class="col-12">
                    <h1 class="h3"><i class="fas fa-truck-loading me-1"></i>&nbsp;<strong>Warehouse Receive
                            Items</strong></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="receive_items">Receive</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Receiving Items</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <?php if (isset($_GET['id'])) {
                $id = $_GET['id'];

                $receive_items = ReceivingReport::find($id);

                if ($receive_items) { ?>
                    <form id="receiveItemForm" action="api/receiving_report_controller.php?action=add" method="POST">
                        <input type="hidden" name="action" id="modalAction" value="add" />
                        <input type="hidden" name="id" id="itemId" value="" />
                        <input type="hidden" name="item_data" id="item_data" />
                        <input type="hidden" id="vendor_name_hidden" name="vendor_name">
                        <div id="hiddenInputContainer"></div>

                        <div class="row">
                            <div class="col-12 col-lg-12">
                                <div class="card h-100 shadow-sm">
                                    <div class="card-header text-white">
                                        <h5 class="card-title mb-0"><i class="fas fa-info-circle me-2"></i>Receive Item Details
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <!-- Vendor Details Section -->
                                            <div class="col-12 mb-3">
                                                <h6 class="border-bottom pb-2"><i class="fas fa-building me-2"></i>Vendor
                                                    Details</h6>
                                            </div>

                                            <div class="col-md-4 vendor-details">
                                                <label for="vendor_name" class="form-label"><i
                                                        class="fas fa-user-tie me-1"></i>Vendor</label>
                                                <select class="form-control form-control-sm" id="vendor_name" name="vendor_name"
                                                    disabled>
                                                    <option value="">Select Vendor</option>
                                                    <?php foreach ($vendors as $vendor): ?>
                                                        <option value="<?= $vendor->id ?>" <?= $vendor->id == $receive_items->vendor_id ? 'selected' : '' ?>>
                                                            <?= $vendor->vendor_name ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="col-md-4 vendor-details">
                                                <label for="location" class="form-label"><i
                                                        class="fas fa-map-marker-alt me-1"></i>Location</label>
                                                <select class="form-control form-control-sm" id="location" name="location"
                                                    disabled>
                                                    <option value="<?= $receive_items->location ?>">
                                                        <?= $receive_items->location ?>
                                                    </option>
                                                    <?php foreach ($locations as $location): ?>
                                                        <option value="<?= $location->name ?>"><?= $location->name ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <!-- Receive Item Details Section -->
                                            <div class="col-12 mt-4 mb-3">
                                                <h6 class="border-bottom pb-2"><i class="fas fa-boxes me-2"></i>Receive Items
                                                    Information</h6>
                                            </div>

                                            <div class="col-md-3 receive-item-details">
                                                <label for="receive_number" class="form-label"><i
                                                        class="fas fa-hashtag me-1"></i>RR No</label>
                                                <input type="text" class="form-control form-control-sm" id="receive_number"
                                                    name="receive_number" value="<?= $receive_items->receive_no ?>" disabled>
                                            </div>

                                            <div class="col-md-3 receive-item-details">
                                                <label for="receive_date" class="form-label"><i
                                                        class="far fa-calendar-alt me-1"></i>Receive Item Date</label>
                                                <input type="date" class="form-control form-control-sm" id="receive_date"
                                                    name="receive_date" value="<?= $receive_items->receive_date ?>" disabled>
                                            </div>
                                            <div class="col-md-3 receive-item-details">
                                                <label for="terms" class="form-label"><i
                                                        class="fas fa-file-contract me-1"></i>Terms</label>
                                                <select class="form-control form-control-sm" id="terms" name="terms" disabled>
                                                    <option value="">Select Terms</option>
                                                    <option value="<?= htmlspecialchars($receive_items->terms) ?>" selected>
                                                        <?= htmlspecialchars($receive_items->terms) ?>
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 receive-item-details">
                                                <label for="receive_due_date" class="form-label"><i
                                                        class="far fa-clock me-1"></i>Due Date</label>
                                                <input type="date" class="form-control form-control-sm"
                                                    id="purchase-order_due_date" name="purchase-order_due_date"
                                                    value="<?= $receive_items->receive_due_date ?>" disabled>
                                            </div>
                                            <div class="col-md-4 receive-item-details" style="display: none;">
                                                <label for="account_id" class="form-label"><i
                                                        class="fas fa-book me-1"></i>Account</label>
                                                <select class="form-select form-select-sm" id="account_id" name="account_id">
                                                    <?php foreach ($accounts as $account): ?>
                                                        <?php if ($account->account_type == 'Accounts Payable'): ?>
                                                            <option value="<?= $account->id ?>"><?= $account->id ?> -
                                                                <?= $account->account_description ?>
                                                            </option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-9 receive-item-details">
                                                <label for="memo" class="form-label"><i
                                                        class="fas fa-sticky-note me-1"></i>Remarks</label>
                                                <input type="text" class="form-control form-control-sm" id="memo" name="memo"
                                                    value="<?= $receive_items->memo ?>" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 col-lg-4" style="display: none;">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="card-title">Summary</h5>
                                </div>
                                <div class="card-body">

                                    <div class="row">
                                        <label class="col-sm-6 col-form-label">Gross Amount:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control-plaintext text-end" id="gross_amount"
                                                name="gross_amount" value="0.00" readonly>
                                        </div>
                                    </div>

                                    <!-- Repeat for other summary fields -->

                                    <div class="row">
                                        <label class="col-sm-6 col-form-label">Discount:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control-plaintext text-end"
                                                id="total_discount_amount" name="total_discount_amount" value="0.00" readonly>
                                            <input type="text" class="form-control" name="discount_account_id"
                                                id="discount_account_id" hidden>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-sm-6 col-form-label">Net Amount:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control-plaintext text-end" id="net_amount_due"
                                                name="net_amount_due" value="0.00" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-sm-6 col-form-label">VAT:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control-plaintext text-end" id="total_vat_amount"
                                                name="total_vat_amount" value="0.00" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-sm-6 col-form-label">Vatable 12%:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control-plaintext text-end" id="vatable_amount"
                                                name="vatable_amount" value="0.00" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-sm-6 col-form-label">Zero-rated:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control-plaintext text-end" id="zero_rated_amount"
                                                name="zero_rated_amount" value="0.00" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-sm-6 col-form-label">Vat-Exempt:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control-plaintext text-end" id="vat_exempt_amount"
                                                name="vat_exempt_amount" value="0.00" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-sm-6 col-form-label fw-bold">Total Amount Due:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control-plaintext text-end fw-bold"
                                                id="total_amount_due" name="total_amount_due" value="0.00" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0"><i class="fas fa-list me-2"></i>Receive Items</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                        <table class="table table-sm table-hover" id="itemTable">
                                                <thead class="bg-light" style="font-size: 12px;">
                                                    <tr>
                                                        <th>PO #</th>
                                                        <th>Item</th>
                                                        <th>Cost Center</th>
                                                        <th>PO Quantity</th>
                                                        <th>Delivered</th>
                                                        <th>Receive Quantity</th>
                                                        <th>Cost</th>
                                                        <th>Amount</th>
                                                        <th>Discount Type</th>
                                                        <th>Discount</th>
                                                        <th>Net</th>
                                                        <th>Tax Amount</th>
                                                        <th>Tax Type</th>
                                                        <th>VAT</th>
                                                        <th style="width: 80px; text-align: center">Closed</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="itemTableBody" style="font-size: 14px;">
                                                    <!-- Existing rows or dynamically added rows will be appended here -->
                                                    <?php
                                                    if ($receive_items) {
                                                        foreach ($receive_items->details as $detail) {
                                                    ?>
                                                            <tr>
                                                                <td hidden>
                                                                    <input type="text" class="form-control form-control-sm po_id"
                                                                        name="po_id[]"
                                                                        value="<?= htmlspecialchars($detail['po_id']) ?>" disabled>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm po_no"
                                                                        name="po_no[]"
                                                                        value="<?= htmlspecialchars($detail['po_no']) ?>" disabled>
                                                                </td>

                                                                <td>
                                                                    <select class="form-control form-control-sm item-dropdown select2"
                                                                        name="item_id[]" disabled>

                                                                        <?php foreach ($products as $product): ?>
                                                                            <option value="<?= htmlspecialchars($product->id) ?>"
                                                                                <?= ($product->id == $detail['item_id']) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($product->item_name) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>

                                                                    </select>
                                                                </td>


                                                                <td>
                                                                    <select class="form-control form-control-sm cost-center-dropdown select2"
                                                                        name="cost_center[]">

                                                                        <?php foreach ($cost_centers as $cost_center): ?>
                                                                            <option value="<?= htmlspecialchars($cost_center->id) ?>"
                                                                                <?= ($cost_center->id == $detail['cost_center_id']) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($cost_center->particular) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>

                                                                    </select>
                                                                </td>


                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm memo"
                                                                        name="quantity[]"
                                                                        value="<?= htmlspecialchars($detail['qty']) ?>" disabled>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm memo"
                                                                        value="<?= htmlspecialchars($detail['last_received_qty']) ?>" disabled>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm memo"
                                                                        name="quantity[]"
                                                                        value="<?= htmlspecialchars($detail['quantity']) ?>" disabled>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm amount"
                                                                        name="amount[]" placeholder="Enter amount"
                                                                        value="<?= htmlspecialchars($detail['cost']) ?>" disabled>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm amount"
                                                                        name="amount[]" placeholder="Enter amount"
                                                                        value="<?= htmlspecialchars($detail['amount']) ?>" disabled>
                                                                </td>

                                                                <td>
                                                                    <select class="form-control form-control-sm discount-dropdown select2" name="discount_percentage[]" disabled>
                                                                        <?php foreach ($discounts as $discount) : ?>
                                                                            <option value="<?= htmlspecialchars($discount->discount_rate) ?>" <?= ($discount->discount_rate == $detail['discount_percentage']) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($discount->discount_description) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </td>

                                                                <td>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm discount-amount"
                                                                        name="discount[]" placeholder=""
                                                                        value="<?= htmlspecialchars($detail['discount']) ?>"
                                                                        disabled>
                                                                </td>

                                                                <td>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm net-amount-before-vat"
                                                                        name="net_amount_before_vat[]" placeholder=""
                                                                        value="<?= htmlspecialchars($detail['net_amount_before_input_vat']) ?>"
                                                                        disabled>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm net-amount"
                                                                        name="net_amount[]" placeholder=""
                                                                        value="<?= htmlspecialchars($detail['net_amount']) ?>" disabled>
                                                                </td>

                                                                <td>
                                                                    <select class="form-control form-control-sm tax-dropdown select2"
                                                                        name="tax_amount[]" disabled>
                                                                        <?php foreach ($input_vats as $input_vat) : ?>
                                                                            <option value="<?= htmlspecialchars($input_vat->input_vat_rate) ?>" <?= ($input_vat->input_vat_rate == $detail['input_vat_percentage']) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($input_vat->input_vat_description) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm input-vat"
                                                                        name="vat_amount[]" placeholder=""
                                                                        value="<?= htmlspecialchars($detail['input_vat_amount']) ?>" disabled>
                                                                </td>

                                                                <td style="text-align: center">
                                                                    <input type="checkbox"
                                                                        value="<?= htmlspecialchars($detail['po_status']) ?>"
                                                                        <?= ($detail['po_status'] == 1) ? 'checked' : '' ?>
                                                                        class="green-checkbox" onclick="return false;">
                                                                    <style>
                                                                        .green-checkbox {
                                                                            accent-color: green;
                                                                            pointer-events: none;
                                                                        }
                                                                    </style>
                                                                </td>
                                                            </tr>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <?php
                    // Invoice found, you can now display the details
                } else {
                    // Handle the case where the invoice is not found
                    echo "Receive_items not found.";
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
        <div class="message">Processing Receiving Items</div>
    </div>
</div>

<?php require 'views/templates/footer.php' ?>

<script>
    $(document).ready(function () {
        initializeSelect2();
    });

    function initializeSelect2() {
        $('#vendor_name').select2({
            theme: 'classic',
            allowClear: false
        });
        $('#account_id').select2({
            theme: 'classic',
            allowClear: false
        });
        $('#location').select2({
            theme: 'classic',
            allowClear: false
        });
        $('#terms').select2({
            theme: 'classic',
            allowClear: false
        });
    }
</script>