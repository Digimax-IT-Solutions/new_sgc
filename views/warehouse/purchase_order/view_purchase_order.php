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

        .table {
            width: 100%;
            margin-bottom: 1rem;
            color: #212529;
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
                <div class="col-12">
                    <h1 class="h3"><strong>View Purchase Order</strong></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="purchase_order">Purchase Order</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View Purchase Order</li>
                        </ol>
                    </nav>
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
                                                                name="vendor_id">
                                                                <option value="">Select Vendor</option>
                                                                <?php foreach ($vendors as $vendor): ?>
                                                                    <option value="<?= $vendor->id ?>"
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
                                                                value="<?= $purchase_order->vendor_address ?>">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 customer-details">
                                                        <!-- TIN -->
                                                        <div class="form-group">
                                                            <label for="tin">TIN</label>
                                                            <input type="text" class="form-control form-control-sm" id="tin"
                                                                name="tin" value="<?= $purchase_order->tin ?>">
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
                                                            <input type="text" class="form-control form-control-sm" id="po_no"
                                                                name="po_no" value="<?= $purchase_order->po_no ?>">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 order-details">
                                                        <!-- TERMS -->
                                                        <div class="form-group">
                                                            <label for="terms">Terms</label>
                                                            <select class="form-control form-control-sm" id="terms"
                                                                name="terms">
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
                                                                value="<?= date('Y-m-d', strtotime($purchase_order->po_date)) ?>">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 order-details">
                                                        <!-- DELIVERY DATE -->
                                                        <div class="form-group">
                                                            <label for="delivery_date">Delivery Date</label>
                                                            <input type="date" class="form-control form-control-sm"
                                                                id="delivery_date" name="delivery_date"
                                                                value="<?= date('Y-m-d', strtotime($purchase_order->delivery_date)) ?>">
                                                        </div>
                                                    </div>

                                                    <div class="col-md-8 order-details">
                                                        <!-- MEMO -->
                                                        <label for="memo" class="form-label">Memo</label>
                                                        <textarea class="form-control" id="memo" name="memo" rows="2"
                                                            placeholder="Enter memo"><?= $purchase_order->memo ?></textarea>

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
                                                <?php if ($purchase_order->status == 0): ?>
                                                    <span class="badge bg-danger">Unpaid</span>
                                                <?php elseif ($purchase_order->status == 1): ?>
                                                    <span class="badge bg-danger">Waiting for Delivery</span>
                                                <?php elseif ($purchase_order->status == 2): ?>
                                                    <span class="badge bg-warning">Partially Paid</span>
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
                                                <div class="row">
                                                    <div class="card-footer">
                                                        <a class="btn btn-primary w-100" href="#"
                                                            onclick="printPurchaseOrder(<?= $purchase_order->po_id ?>); return false;">
                                                            <i class="fas fa-print"></i> Print
                                                        </a>
                                                    </div>
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
                                                    <table class="table table-sm table-hover" id="itemTable">
                                                        <thead class="bg-light" style="font-size: 12px;">
                                                            <tr>
                                                                <th style="width: 10%;">Item</th>
                                                                <th style="width: 10%;">Cost Center</th>
                                                                <th style="width: 18%;">Description</th>
                                                                <th style="width: 6%;">Unit</th>
                                                                <th class="text-right" style="width: 3%;">Quantity</th>
                                                                <th class="text-right" style="width: 8%;">Cost</th>
                                                                <th class="text-right" style="width: 8%;">Amount</th>
                                                                <th style="width: 3%;">Disc Type</th>
                                                                <th class="text-right" style="width: 7%;">Discount</th>
                                                                <th class="text-right" style="width: 7%;">Net</th>
                                                                <th class="text-right" style="width: 7%;">Tax Amount</th>
                                                                <th style="width: 7%;">Tax Type</th>
                                                                <th class="text-right" style="width: 6%;">VAT</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="itemTableBody" style="font-size: 14px;">
                                                            <?php if ($purchase_order): ?>
                                                                <?php foreach ($purchase_order->details as $detail): ?>
                                                                    <tr>
                                                                        <td>
                                                                            <select
                                                                                class="form-control form-control-sm item-dropdown select2"
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
                                                                            <select
                                                                                class="form-control form-control-sm cost-center-dropdown select2"
                                                                                name="cost_center_id[]">
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
                                                                        <td class="text-right">
                                                                            <input type="text"
                                                                                class="form-control form-control-sm quantity text-right"
                                                                                name="quantity[]"
                                                                                value="<?= htmlspecialchars($detail['qty']) ?>"
                                                                                placeholder="Qty">
                                                                        </td>
                                                                        <td class="text-right">
                                                                            <input type="text"
                                                                                class="form-control form-control-sm cost text-right"
                                                                                name="cost[]"
                                                                                value="<?= number_format($detail['cost'], 2, '.', ',') ?>"
                                                                                placeholder="Enter Cost">
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
                                                                                name="input_vat_percentage[]">
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
    function showLoadingOverlay() {
        document.getElementById('loadingOverlay').style.display = 'flex';
    }

    function hideLoadingOverlay() {
        document.getElementById('loadingOverlay').style.display = 'none';
    }

    function printPurchaseOrder(poId) {
        showLoadingOverlay();

        const printFrame = document.getElementById('printFrame');
        const printContentUrl = `print_purchase_order?action=print&id=${poId}`;

        printFrame.src = printContentUrl;

        printFrame.onload = function () {
            printFrame.contentWindow.focus();
            printFrame.contentWindow.print();
            hideLoadingOverlay();
        };
    }
</script>


<script>
    $(document).ready(function () {
        // Handle vendor selection
        $('#vendor_id').change(function () {
            var selectedVendor = $(this).find(':selected');
            var address = selectedVendor.data('address');
            var tin = selectedVendor.data('account');
            $('#vendor_address').val(address);
            $('#tin').val(tin);
        });

        // Handle terms selection and calculate delivery date
        $('#terms').change(function () {
            var terms = $(this).val();
            $('#delivery_date').val(calculateDeliveryDate(terms));
        });

        $('#vendor_id').select2({
            theme: 'bootstrap-5', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            allowClear: false
        });


        $('.select2').select2({
            theme: 'bootstrap-5', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
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
            setTimeout(function () {
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
            row.find('.quantity, .cost, .discount-dropdown, .input_vat_percentage').on('input change', function () {
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
            $('.amount').each(function () {
                totalAmount += parseFloat($(this).val()) || 0;
            });

            $('.discount_amount').each(function () {
                totalDiscountAmount += parseFloat($(this).val()) || 0;
            });

            $('.taxable_amount').each(function () {
                totalNetAmountBeforeVat += parseFloat($(this).val()) || 0;
            });

            $('.input_vat_amount').each(function () {
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



        $('#addItemBtn').click(addRow);

        $(document).on('click', '.removeRow', function () {
            $(this).closest('tr').remove();
            calculateTotalAmount();
        });

        $('#writeCheckForm').submit(function (event) {
            event.preventDefault();
            $('#item_data').val(JSON.stringify(gatherTableItems()));
            this.submit();
        });

        function gatherTableItems() {
            const items = [];
            $('#itemTableBody tr').each(function () {
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

        // Attach event listeners to existing rows
        $('#itemTableBody tr').each(function () {
            attachRowEventListeners($(this));
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