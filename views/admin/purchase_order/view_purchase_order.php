<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();
$accounts = ChartOfAccount::all();
$vendors = Vendor::all();
$products = Product::all();
$discounts = Discount::all();
$input_vats = InputVat::all();
$purchase_orders = PurchaseOrder::all();
$cost_centers = CostCenter::all();

$newPoNo = PurchaseOrder::getLastPoNo();

?>


<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card-header {
            background-color: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .card-body {
            padding: 20px;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: bold;
        }

        .status-badge.waiting {
            background-color: #ffc107;
            color: #000;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        #itemTable {
            min-width: 3000px;
            /* Adjust this value based on your table's content */
            table-layout: fixed;
        }

        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }

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
    </style>
    <style>
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
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3"><strong>View Purchase Order</strong></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="purchase_order">Purchase Order</a></li>
                                <li class="breadcrumb-item active" aria-current="page">View Purchase Order</li>
                            </ol>
                        </nav>
                    </div>
                    <a href="purchase_order" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Purchases List
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_purchase_order') ?>
                    <?php displayFlashMessage('delete_payment_method') ?>
                    <!-- <?php displayFlashMessage('update_purchase_order') ?> -->
                    <!-- Default box -->


                    <?php
                    if (isset($_GET['id'])) {
                        $id = $_GET['id'];

                        $purchase_order = PurchaseOrder::find($id);

                        if ($purchase_order) { ?>
                            <form id="writeCheckForm" action="api/purchase_order_controller.php" method="POST">
                                <input type="hidden" name="id" id="itemId" value="<?= $purchase_order->po_id ?>">
                                <input type="hidden" name="action" id="modalAction" value="update" />
                                <input type="hidden" name="item_data" id="item_data" />

                                <div class="row">
                                    <div class="col-12 col-lg-8">
                                        <div class="card h-100">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">Purchase Order Details</h5>
                                            </div>

                                            <div class="card-body">
                                                <div class="row g-2">
                                                    <!-- Vendor Details Section -->
                                                    <div class="col-12 mb-3">
                                                        <h6 class="border-bottom pb-2">Vendor Details</h6>
                                                    </div>

                                                    <div class="col-md-4 customer-details">
                                                        <!-- SELECT VENDOR -->
                                                        <div class="form-group">
                                                            <label for="vendor_id">Vendor</label>
                                                            <select class="form-control form-control-sm select2" id="vendor_id"
                                                                name="vendor_id" disabled>
                                                                <option value="">Select Vendor</option>
                                                                <?php foreach ($vendors as $vendor): ?>
                                                                    <option value="<?= $vendor->id ?>"
                                                                        data-tin="<?= $vendor->tin ?>"
                                                                        data-address="<?= $vendor->vendor_address ?>"
                                                                        data-terms="<?= $vendor->terms ?>"
                                                                        <?= $vendor->id == $purchase_order->vendor_id ? 'selected' : '' ?>>
                                                                        <?= $vendor->vendor_name ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 customer-details">
                                                        <!-- VENDOR ADDRESS -->
                                                        <div class="form-group">
                                                            <label for="vendor_address">Address</label>
                                                            <input type="text" class="form-control form-control-sm"
                                                                id="vendor_address" name="vendor_address"
                                                                value="<?= $purchase_order->vendor_address ?>" disabled>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 customer-details">
                                                        <!-- TIN -->
                                                        <div class="form-group">
                                                            <label for="tin">TIN</label>
                                                            <input type="text" class="form-control form-control-sm" id="tin"
                                                                name="tin" value="<?= $purchase_order->tin ?>" disabled>
                                                        </div>
                                                    </div>

                                                    <!-- Order Information Section -->
                                                    <div class="col-12 mt-3 mb-3">
                                                        <h6 class="border-bottom pb-2">Order Information</h6>
                                                    </div>

                                                    <div class="col-md-3 order-details">
                                                        <div class="form-group">
                                                            <!-- PURCHASE ORDER NO -->
                                                            <label for="po_no">PO NO:</label>
                                                            <input type="text" class="form-control form-control-sm" id="po_no" name="po_no"
                                                                <?php if ($purchase_order->po_status == 4): ?>
                                                                    value="<?php echo htmlspecialchars($newPoNo); ?>" readonly
                                                                <?php else: ?>
                                                                    value="<?php echo htmlspecialchars($purchase_order->po_no); ?>" disabled
                                                                <?php endif; ?>
                                                            >
                                                        </div>
                                                    </div>


                                                    <div class="col-md-3 order-details">
                                                        <!-- TERMS -->
                                                        <div class="form-group">
                                                            <label for="terms">Terms</label>
                                                            <select class="form-control form-control-sm" id="terms"
                                                                name="terms" disabled>
                                                                <option value="<?= $purchase_order->terms ?>">
                                                                    <?= $purchase_order->terms ?>
                                                                </option>
                                                                <option value="Due on Receipt">Due on Receipt</option>
                                                                <option value="NET 7">NET 7</option>
                                                                <option value="NET 15">NET 15</option>
                                                                <option value="NET 30">NET 30</option>
                                                            </select>
                                                        </div>
                                                    </div>


                                                    <div class="col-md-3 order-details">
                                                        <!-- DATE -->
                                                        <div class="form-group">
                                                            <label for="po_date">Date</label>
                                                            <input type="date" class="form-control form-control-sm" id="po_date"
                                                                name="po_date"
                                                                value="<?= date('Y-m-d', strtotime($purchase_order->po_date)) ?>" disabled>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 order-details">
                                                        <!-- DELIVERY DATE -->
                                                        <div class="form-group">
                                                            <label for="delivery_date">Delivery Date</label>
                                                            <input type="date" class="form-control form-control-sm"
                                                                id="delivery_date" name="delivery_date"
                                                                value="<?= date('Y-m-d', strtotime($purchase_order->delivery_date)) ?>" disabled>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-8 order-details">
                                                        <!-- MEMO -->
                                                        <label for="memo" class="form-label">Memo</label>
                                                        <textarea class="form-control" id="memo" name="memo" rows="2"
                                                            placeholder="Enter memo" disabled><?= $purchase_order->memo ?></textarea>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Summary Section -->
                                    <div class="col-12 col-lg-4">
                                        <div class="card h-100">
                                            <div class="card-header">
                                                <h5 class="card-title">Summary</h5>
                                                <?php if ($purchase_order->po_status == 0): ?>
                                                    <span class="badge bg-danger">Waiting for Delivery</span>
                                                <?php elseif ($purchase_order->po_status == 1): ?>
                                                    <span class="badge bg-success">Paid</span>
                                                <?php elseif ($purchase_order->po_status == 2): ?>
                                                    <span class="badge bg-warning">Partially Paid</span>
                                                <?php elseif ($purchase_order->po_status == 3): ?>
                                                <span class="badge bg-secondary">Void</span>
                                                <?php elseif ($purchase_order->po_status == 4): ?>
                                                    <span class="badge bg-info text-dark">Draft</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="card-body">
                                                <!-- GROSS AMOUNT -->
                                                <div class="row">
                                                    <label class="col-sm-6 col-form-label">Gross Amount:</label>
                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control-plaintext text-end"
                                                            id="gross_amount" name="gross_amount"
                                                            value="<?= number_format($purchase_order->gross_amount, 2, '.', ',') ?>"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <!-- DISCOUNT -->
                                                <div class="row">
                                                    <label class="col-sm-6 col-form-label">Discount:</label>
                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control-plaintext text-end"
                                                            id="discount_amount" name="discount_amount"
                                                            value="<?= number_format($purchase_order->discount_amount, 2, '.', ',') ?>"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <!-- NET AMOUNT DUE -->
                                                <div class="row">
                                                    <label class="col-sm-6 col-form-label">Net Amount:</label>
                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control-plaintext text-end"
                                                            id="net_amount_due" name="net_amount_due"
                                                            value="<?= number_format($purchase_order->net_amount_due, 2, '.', ',') ?>"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <!-- VAT PERCENTAGE -->
                                                <div class="row">
                                                    <label class="col-sm-6 col-form-label">VAT:</label>
                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control-plaintext text-end"
                                                            id="input_vat_amount" name="input_vat_amount"
                                                            value="<?= number_format($purchase_order->po_input_vat, 2, '.', ',') ?>"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <!-- VATABLE -->
                                                <div class="row">
                                                    <label class="col-sm-6 col-form-label">Vatable 12%:</label>
                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control-plaintext text-end"
                                                            id="vatable_amount" name="vatable_amount"
                                                            value="<?= number_format($purchase_order->vatable_amount, 2, '.', ',') ?>"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <!-- VAT ZERO RATED -->
                                                <div class="row">
                                                    <label class="col-sm-6 col-form-label">Zero-rated:</label>
                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control-plaintext text-end"
                                                            id="zero_rated_amount" name="zero_rated_amount"
                                                            value="<?= number_format($purchase_order->zero_rated_amount, 2, '.', ',') ?>"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <!-- VAT EXEMPT -->
                                                <div class="row">
                                                    <label class="col-sm-6 col-form-label">Vat-Exempt:</label>
                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control-plaintext text-end"
                                                            id="vat_exempt_amount" name="vat_exempt_amount"
                                                            value="<?= number_format($purchase_order->vat_exempt, 2, '.', ',') ?>"
                                                            readonly>
                                                    </div>
                                                </div>

                                                <!-- TOTAL AMOUNT DUE -->
                                                <div class="row">
                                                    <label class="col-sm-6 col-form-label fw-bold">Total Amount Due:</label>
                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control-plaintext text-end fw-bold"
                                                            id="total_amount_due" name="total_amount_due"
                                                            value="<?= number_format($purchase_order->total_amount, 2, '.', ',') ?>"
                                                            readonly>
                                                    </div>
                                                </div>
                                                
                                                <div class="card-footer d-flex justify-content-center">
                                                    <?php if ($purchase_order->po_status == 4): ?>
                                                        <!-- Buttons to show when invoice_status is 4 -->
                                                        <button type="submit" class="btn btn-info me-2">Save and Print</button>
                                                    <?php elseif ($purchase_order->po_status == 3): ?>
                                                        <!-- Button to show when invoice_status is 3 -->
                                                        <a class="btn btn-primary" href="#" id="reprintButton">
                                                            <i class="fas fa-print"></i> Reprint
                                                        </a>
                                                    <?php else: ?>
                                                        <!-- Buttons to show when invoice_status is neither 3 nor 4 -->
                                                        <button type="button" class="btn btn-secondary btn-sm me-2" id="voidButton">Void</button>
                                                        <a class="btn btn-primary btn-sm" href="#" id="reprintButton">
                                                            <i class="fas fa-print"></i> Reprint
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Items Table Section -->
                                <div class="row mt-4">
                                    <div class="col-12">
                                        <div class="card">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h5 class="card-title mb-0">Items</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-hover" id="itemTable">
                                                        <thead class="bg-light" style="font-size: 12px;">
                                                            <tr>
                                                                <th style="width: 150px;">PR No</th>
                                                                <th style="width: 250px;">Item</th>
                                                                <th style="width: 280px;">Cost Center</th>
                                                                <th>Description</th>
                                                                <th>Unit</th>
                                                                <th style="background-color: #e6f3ff;">Pr Quantity</th>
                                                                <th style="background-color: #e6f3ff;">Ordered</th>
                                                                <th style="background-color: #e6f3ff;">Quantity</th>
                                                                <th>Cost</th>
                                                                <th>Amount</th>
                                                                <th>Disc Type</th>
                                                                <th>Discount</th>
                                                                <th>Net</th>
                                                                <th>Tax Amount</th>
                                                                <th>Tax Type</th>
                                                                <th>VAT</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="itemTableBody" style="font-size: 14px;">
                                                            <?php if ($purchase_order): ?>
                                                                <?php foreach ($purchase_order->details as $detail): ?>
                                                                    <tr>
                                                                        <td>
                                                                            <input type="text" class="form-control form-control-sm pr_no" name="pr_no[]"
                                                                                value="<?= htmlspecialchars($detail['pr_no']) ?>" readonly>
                                                                        </td>
                                                                        <td>
                                                                            <select
                                                                                class="form-control form-control-sm item-dropdown select2"
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
                                                                            <select
                                                                                class="form-control form-control-sm cost-center-dropdown select2"
                                                                                name="cost_center_id[]" disabled>
                                                                                <?php foreach ($cost_centers as $cost_center): ?>
                                                                                    <option
                                                                                        value="<?= htmlspecialchars($cost_center->id) ?>"
                                                                                        <?= ($cost_center->id == $detail['cost_center_id']) ? 'selected' : '' ?>>
                                                                                        <?= htmlspecialchars($cost_center->particular) ?>
                                                                                    </option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text"
                                                                                class="form-control form-control-sm item_purchase_description"
                                                                                name="item_purchase_description[]"
                                                                                value="<?= htmlspecialchars($detail['item_purchase_description']) ?>"
                                                                                readonly>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text"
                                                                                class="form-control form-control-sm uom_name"
                                                                                name="uom_name[]"
                                                                                value="<?= htmlspecialchars($detail['uom_name']) ?>"
                                                                                readonly>
                                                                        </td>
                                                                        <td style="background-color: #e6f3ff;">
                                                                            <input type="text"
                                                                                class="form-control form-control-sm pr_quantity text-right"
                                                                                name="pr_quantity[]"
                                                                                value="<?= htmlspecialchars($detail['related_quantity']) ?>"
                                                                                placeholder="Qty" disabled>
                                                                        </td>
                                                                        <td class="text-right" style="background-color: #e6f3ff;">
                                                                            <input type="text"
                                                                                class="form-control form-control-sm quantity text-right"
                                                                                name="quantity[]"
                                                                                value="<?= htmlspecialchars($detail['last_ordered_qty']) ?>"
                                                                                placeholder="Qty" disabled>
                                                                        </td>
                                                                        <td class="text-right" style="background-color: #e6f3ff;">
                                                                            <input type="text"
                                                                                class="form-control form-control-sm quantity text-right"
                                                                                name="quantity[]"
                                                                                value="<?= htmlspecialchars($detail['qty']) ?>"
                                                                                placeholder="Qty" disabled>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <input type="text"
                                                                                class="form-control form-control-sm cost text-right"
                                                                                name="cost[]"
                                                                                value="<?= number_format($detail['cost'], 2, '.', ',') ?>"
                                                                                placeholder="Enter Cost" disabled>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <input type="text"
                                                                                class="form-control form-control-sm amount text-right"
                                                                                name="amount[]"
                                                                                value="<?= number_format($detail['amount'], 2, '.', ',') ?>"
                                                                                placeholder="Amount" readonly>
                                                                        </td>
                                                                        <td>
                                                                            <select
                                                                                class="form-control form-control-sm discount-dropdown select2"
                                                                                name="discount_percentage[]" disabled>
                                                                                <?php foreach ($discounts as $discount): ?>
                                                                                    <option
                                                                                        value="<?= htmlspecialchars($discount->discount_rate) ?>"
                                                                                        <?= ($discount->discount_rate == $detail['discount_percentage']) ? 'selected' : '' ?>>
                                                                                        <?= htmlspecialchars($discount->discount_description) ?>
                                                                                    </option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <input type="text"
                                                                                class="form-control form-control-sm discount_amount text-right"
                                                                                name="discount_amount[]"
                                                                                value="<?= number_format($detail['discount'], 2, '.', ',') ?>"
                                                                                readonly>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <input type="text"
                                                                                class="form-control form-control-sm taxable_amount text-right"
                                                                                name="taxable_amount[]"
                                                                                value="<?= number_format($detail['net_amount'], 2, '.', ',') ?>"
                                                                                readonly>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <input type="text"
                                                                                class="form-control form-control-sm net_amount text-right"
                                                                                name="net_amount[]"
                                                                                value="<?= number_format($detail['taxable_amount'], 2, '.', ',') ?>"
                                                                                readonly>
                                                                        </td>
                                                                        <td>
                                                                            <select
                                                                                class="form-control form-control-sm input_vat_percentage select2"
                                                                                name="input_vat_percentage[]" disabled>
                                                                                <?php foreach ($input_vats as $vat): ?>
                                                                                    <option
                                                                                        value="<?= htmlspecialchars($vat->input_vat_rate) ?>"
                                                                                        <?= ($vat->input_vat_rate == $detail['input_vat_percentage']) ? 'selected' : '' ?>>
                                                                                        <?= htmlspecialchars($vat->input_vat_name) ?>
                                                                                    </option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <input type="text"
                                                                                class="form-control form-control-sm input_vat_amount text-right"
                                                                                name="input_vat_amount[]"
                                                                                value="<?= number_format($detail['input_vat'], 2, '.', ',') ?>"
                                                                                readonly>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>

                    <?php
                            // Check found, you can now display the details
                        } else {
                            // Handle the case where the check is not found
                            echo "PO not found.";
                            exit;
                        }
                    } else {
                        // Handle the case where the ID is not provided
                        echo "No ID provided.";
                        exit;
                    }


                    ?>
                </div>
            </div>
        </div>
    </main>
</div>


<iframe id="printFrame" style="display:none;"></iframe>
<div id="loadingOverlay" class="loading-overlay">
    <div class="spinner"></div>
</div>

<?php require 'views/templates/footer.php' ?>


<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('reprintButton').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Reprint Purchase Order?',
                text: "Are you sure you want to reprint this purchase order?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reprint it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    printPurchaseOrder(<?= $purchase_order->po_id ?>, 2); // Pass 2 for reprint
                }
            });
        });

        // Attach event listener for the void button
        document.getElementById('voidButton').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Void Purchase Order?',
                text: "Are you sure you want to void this purchase order? This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, void it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    voidCheck(<?= $purchase_order->po_id ?>);
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

    function printPurchaseOrder(id, printStatus) {
        showLoadingOverlay();

        $.ajax({
            url: 'api/purchase_order_controller.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'update_print_status',
                id: id,
                print_status: printStatus
            },
            success: function(response) {
                if (response.success) {
                    const printFrame = document.getElementById('printFrame');
                    const printContentUrl = `print_purchase_order?action=print&id=${id}`;

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
            url: 'api/purchase_order_controller.php',
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
                        text: 'Purchase Order has been voided successfully.'
                    }).then(() => {
                        location.reload(); // Reload the page to reflect changes
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to void purchase: ' + (response.message || 'Unknown error')
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                hideLoadingOverlay(); // Hide the loading overlay on error
                console.error('AJAX error:', textStatus, errorThrown);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while voiding the purchase: ' + textStatus
                });
            }
        });
    }
</script>

<script>
    $(document).ready(function() {
        // Handle vendor selection
        $('#vendor_id').change(function() {
            var selectedVendor = $(this).find(':selected');
            var address = selectedVendor.data('address');
            var tin = selectedVendor.data('tin');
            $('#vendor_address').val(address);
            $('#tin').val(tin);
        });

        // Handle terms selection and calculate delivery date
        $('#terms').change(function() {
            var terms = $(this).val();
            $('#delivery_date').val(calculateDeliveryDate(terms));
        });

        // $('#terms').select2({
        //     theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
        //     width: '100%',
        //     allowClear: false
        // });

        $('#vendor_id').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            allowClear: false
        });


        $('.select2').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            allowClear: false
        });

        function calculateDeliveryDate(terms) {
            var currentDate = new Date();
            if (terms === 'Due on Receipt') {
                return currentDate.toISOString().split('T')[0];
            } else {
                var daysToAdd = parseInt(terms.replace('NET ', ''));
                var deliveryDate = new Date(currentDate.setDate(currentDate.getDate() + daysToAdd));
                return deliveryDate.toISOString().split('T')[0];
            }
        }

        // Populate dropdowns with data from PHP
        const products = <?php echo json_encode($products); ?>;
        const cost_centers = <?php echo json_encode($cost_centers); ?>;
        const discounts = <?php echo json_encode($discounts); ?>;
        const inputVats = <?php echo json_encode($input_vats); ?>;

        function generateOptions(data, keyValue, keyName) {
            return data.map(item => `<option value="${item[keyValue]}">${item[keyName]}</option>`).join('');
        }

        const itemDropdownOptions = generateOptions(products, 'id', 'item_name');
        const costCenterDropdownOptions = generateOptions(cost_centers, 'id', 'particular');
        const discountDropdownOptions = generateOptions(discounts, 'discount_rate', 'discount_description');
        const inputVatDropdownOptions = generateOptions(inputVats, 'input_vat_rate', 'input_vat_name');

        function addRow() {
            const newRow = `
                <tr>
                    <td><select class="form-control form-control-sm item-dropdown select2" name="item_id[]">${itemDropdownOptions}</select></td>
                    <td><select class="form-control form-control-sm cost-center-dropdown select2" name="cost_center_id[]">${costCenterDropdownOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm item_purchase_description" name="item_purchase_description[]" readonly></td>
                    <td><input type="text" class="form-control form-control-sm uom_name" name="uom_name[]" readonly></td>
                    <td><input type="text" class="form-control form-control-sm quantity text-right" name="quantity[]" placeholder="Qty"></td>
                    <td><input type="text" class="form-control form-control-sm cost text-right" name="cost[]" placeholder="Enter Cost"></td>
                    <td><input type="text" class="form-control form-control-sm amount text-right" name="amount[]" placeholder="Amount" readonly></td>
                    <td><select class="form-control form-control-sm discount-dropdown select2" name="discount_percentage[]">${discountDropdownOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm discount_amount text-right" name="discount_amount[]" readonly></td>
                    <td><input type="text" class="form-control form-control-sm taxable_amount text-right" name="taxable_amount[]" readonly></td>
                    <td><input type="text" class="form-control form-control-sm net_amount text-right" name="net_amount[]" readonly></td>
                    <td><select class="form-control form-control-sm input_vat_percentage select2" name="input_vat_percentage[]">${inputVatDropdownOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm input_vat_amount text-right" name="input_vat_amount[]" readonly></td>
                
                </tr>`;
            $('#itemTableBody').append(newRow);

            // Use a timeout to ensure Select2 initializes properly
            setTimeout(function() {
                $('#itemTableBody tr:last-child .select2').select2({
                    width: '100%',
                    theme: 'bootstrap-5', // Use this if you're using Bootstrap 4
                    allowClear: false // This disables the clear "X" button
                });
            }, 100); // Adjust timeout as needed

            // Attach event listeners to the new row
            attachRowEventListeners($('#itemTableBody tr:last-child'));
        }


        function attachRowEventListeners(row) {
            row.find('.quantity, .cost, .discount-dropdown, .input_vat_percentage').on('input change', function() {
                calculateRowValues(row);
                calculateTotalAmount();
            });
        }

        function calculateRowValues(row) {
            const quantity = parseFloat(row.find('.quantity').val()) || 0;
            const cost = parseFloat(row.find('.cost').val()) || 0;
            const discountPercentage = parseFloat(row.find('.discount-dropdown').val()) || 0;
            const vatPercentage = parseFloat(row.find('.input_vat_percentage').val()) || 0 || 0;

            const amount = quantity * cost;
            const discountAmount = (amount * discountPercentage) / 100;
            const netAmountBeforeVat = amount - discountAmount;
            const vatAmount = (netAmountBeforeVat / (1 + vatPercentage / 100)) * (vatPercentage / 100);
            const netAmount = netAmountBeforeVat - vatAmount;

            row.find('.amount').val(amount.toFixed(2));
            row.find('.discount_amount').val(discountAmount.toFixed(2));
            row.find('.taxable_amount').val(netAmountBeforeVat.toFixed(2));
            row.find('.input_vat_amount').val(vatAmount.toFixed(2));
            row.find('.net_amount').val(netAmount.toFixed(2));
        }

        function calculateTotalAmount() {
            let totalAmount = 0;
            let totalDiscountAmount = 0;
            let totalNetAmountBeforeVat = 0;
            let totalVatAmount = 0;

            // Calculate totals for each field
            $('.amount').each(function() {
                totalAmount += parseFloat($(this).val()) || 0;
            });

            $('.discount_amount').each(function() {
                totalDiscountAmount += parseFloat($(this).val()) || 0;
            });

            $('.taxable_amount').each(function() {
                totalNetAmountBeforeVat += parseFloat($(this).val()) || 0;
            });

            $('.input_vat_amount').each(function() {
                totalVatAmount += parseFloat($(this).val()) || 0;
            });

            // Total net amount is the net amount before VAT minus the total VAT amount
            const totalNetAmount = totalNetAmountBeforeVat - totalVatAmount;

            // Update totals in the form
            $("#gross_amount").val(totalAmount.toFixed(2));
            $("#discount_amount").val(totalDiscountAmount.toFixed(2));
            $("#net_amount_due").val(totalNetAmountBeforeVat.toFixed(2));
            $("#input_vat_amount").val(totalVatAmount.toFixed(2));
            $("#total_amount_due").val(totalNetAmount.toFixed(2)); // This should be the total net amount after VAT deduction
        }





        function gatherTableItems() {
            const items = [];
            $('#itemTableBody tr').each(function() {
                items.push({
                    item_id: $(this).find('select[name="item_id[]"]').val(),
                    cost_center_id: $(this).find('select[name="cost_center_id[]"]').val(),
                    description: $(this).find('input[name="item_purchase_description[]"]').val(),
                    uom: $(this).find('input[name="uom_name[]"]').val(),
                    quantity: $(this).find('input[name="quantity[]"]').val(),
                    cost: $(this).find('input[name="cost[]"]').val(),
                    amount: $(this).find('input[name="amount[]"]').val(),
                    discount_percentage: $(this).find('select[name="discount_percentage[]"]').val(),
                    discount_amount: $(this).find('input[name="discount_amount[]"]').val(),
                    net_amount_before_input_vat: $(this).find('input[name="taxable_amount[]"]').val(),
                    net_amount: $(this).find('input[name="net_amount[]"]').val(),
                    input_vat_percentage: $(this).find('select[name="input_vat_percentage[]"]').val(),
                    input_vat_amount: $(this).find('input[name="input_vat_amount[]"]').val(),
                });
            });
            return items;
        }

        $('#writeCheckForm').submit(function (event) {
            event.preventDefault(); // Prevent default form submission
            
            if ($('#itemTableBody tr').length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Items',
                    text: 'You must add at least one item before submitting the purchase order.'
                });
                return false;
            }

            const items = gatherTableItems();
            $('#item_data').val(JSON.stringify(items));
            const purchaseStatus = <?= json_encode($purchase_order->po_status) ?>;
            const id = <?= json_encode($purchase_order->po_id) ?>;

            if (purchaseStatus == 4) {
                // Show loading overlay
                document.getElementById('loadingOverlay').style.display = 'flex';
                
                const formData = {
                    action: 'update_draft',
                    id: id,
                    po_no: $('#po_no').val(),
                    item_data: JSON.stringify(items)
                };

                console.log('Sending data:', formData);  // Log the data being sent

                $.ajax({
                    url: 'api/purchase_order_controller.php',
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    success: function (response) {
                        document.getElementById('loadingOverlay').style.display = 'none';
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Purchase order submitted successfully!',
                                showCancelButton: true,
                                confirmButtonText: 'Print',
                                cancelButtonText: 'Close'
                            }).then((result) => {
                                if (result.isConfirmed && response.id) {
                                    printPurchaseOrder(response.id, 1); // Pass 1 for initial print
                                } else {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error saving purchase order: ' + (response.message || 'Unknown error')
                            });
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        document.getElementById('loadingOverlay').style.display = 'none';
                        console.error('AJAX error:', textStatus, errorThrown);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while saving the purchase order: ' + textStatus
                        });
                    }
                });
            }
        });
    });

    function populateFields(select) {
        const selectedOption = $(select).find('option:selected');
        const description = selectedOption.data('description');
        const uom = selectedOption.data('uom');
        const row = $(select).closest('tr');
        row.find('.item_purchase_description').val(description);
        row.find('.uom_name').val(uom);
    }
</script>