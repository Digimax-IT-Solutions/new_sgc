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

    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="h3"><strong>Create Purchase Order</strong></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="purchase_order">Purchase Order</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Create Purchase Order</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_purchase_order') ?>
                    <?php displayFlashMessage('delete_payment_method') ?>
                    <?php displayFlashMessage('update_payment_method') ?>

                    <!-- Purchase Order Form -->
                    <form id="writeCheckForm" action="api/purchase_order_controller.php?action=add" method="POST">
                        <input type="hidden" name="action" id="modalAction" value="add" />
                        <input type="hidden" name="id" id="itemId" value="" />
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
                                                                data-account="<?= $vendor->account_number ?>">
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
                                                        id="vendor_address" name="vendor_address">
                                                </div>
                                            </div>

                                            <div class="col-md-4 customer-details">
                                                <!-- TIN -->
                                                <div class="form-group">
                                                    <label for="tin">TIN</label>
                                                    <input type="text" class="form-control form-control-sm" id="tin"
                                                        name="tin">
                                                </div>
                                            </div>

                                            <!-- Order Information Section -->
                                            <div class="col-12 mt-3 mb-3">
                                                <h6 class="border-bottom pb-2">Order Information</h6>
                                            </div>

                                            <div class="col-md-3 order-details">
                                                <div class="form-group">
                                                    <!-- PURCHASE ORDER NO -->
                                                    <label for="po_no">PO No:</label>
                                                    <input type="text" class="form-control form-control-sm" id="po_no"
                                                        name="po_no" placeholder="Enter PO number">
                                                </div>
                                            </div>

                                            <div class="col-md-3 order-details">
                                                <!-- TERMS -->
                                                <div class="form-group">
                                                    <label for="terms">Terms</label>
                                                    <select class="form-control form-control-sm" id="terms"
                                                        name="terms">
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
                                                        name="po_date" value="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-3 order-details">
                                                <!-- DELIVERY DATE -->
                                                <div class="form-group">
                                                    <label for="delivery_date">Delivery Date</label>
                                                    <input type="date" class="form-control form-control-sm"
                                                        id="delivery_date" name="delivery_date"
                                                        value="<?php echo date('Y-m-d'); ?>">
                                                </div>
                                            </div>

                                            <div class="col-md-8 order-details">
                                                <!-- MEMO -->
                                                <label for="memo" class="form-label">Memo</label>
                                                <input type="text" class="form-control form-control-sm" id="memo"
                                                    name="memo">
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
                                    </div>
                                    <div class="card-body">
                                        <!-- GROSS AMOUNT -->
                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Gross Amount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end"
                                                    id="gross_amount" name="gross_amount" value="0.00" readonly>
                                            </div>
                                        </div>

                                        <!-- DISCOUNT -->
                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Discount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end"
                                                    id="total_discount_amount" name="total_discount_amount" value="0.00"
                                                    readonly>
                                            </div>
                                        </div>

                                        <!-- NET AMOUNT DUE -->
                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Net Amount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end"
                                                    id="net_amount_due" name="net_amount_due" value="0.00" readonly>
                                            </div>
                                        </div>

                                        <!-- VAT PERCENTAGE -->
                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">VAT:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end"
                                                    id="total_input_vat_amount" name="total_input_vat_amount"
                                                    value="0.00" readonly>
                                            </div>
                                        </div>

                                        <!-- VATABLE -->
                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Vatable 12%:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end"
                                                    id="vatable_amount" name="vatable_amount" value="0.00" readonly>
                                            </div>
                                        </div>

                                        <!-- VAT ZERO RATED -->
                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Zero-rated:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end"
                                                    id="zero_rated_amount" name="zero_rated_amount" value="0.00"
                                                    readonly>
                                            </div>
                                        </div>

                                        <!-- VAT EXEMPT -->
                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Vat-Exempt:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end"
                                                    id="vat_exempt_amount" name="vat_exempt_amount" value="0.00"
                                                    readonly>
                                            </div>
                                        </div>

                                        <!-- TOTAL AMOUNT DUE -->
                                        <div class="row">
                                            <label class="col-sm-6 col-form-label fw-bold">Total Amount Due:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end fw-bold"
                                                    id="total_amount_due" name="total_amount_due" value="0.00" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" class="btn btn-primary w-100">Create Purchase
                                            Order</button>
                                    </div>
                                </div>
                            </div>


                            <!-- Items Table Section -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0">Add Purchase Order</h5>
                                            <button type="button" class="btn btn-primary btn-sm" id="addItemBtn">
                                                <i class="fas fa-plus"></i> Add Item
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover" id="itemTable">
                                                    <thead class="bg-light" style="font-size: 12px;">
                                                        <tr>
                                                            <th>Item</th>
                                                            <th>Cost Center</th>
                                                            <th>Description</th>
                                                            <th>Unit</th>
                                                            <th>Quantity</th>
                                                            <th>Cost</th>
                                                            <th>Amount</th>
                                                            <th>Discount Type</th>
                                                            <th>Discount</th>
                                                            <th>Net</th>
                                                            <th>Tax Amount</th>
                                                            <th>Tax Type</th>
                                                            <th>VAT</th>
                                                            <th></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="itemTableBody" style="font-size: 14px;">
                                                        <!-- Items will be dynamically added here -->
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>


<?php require 'views/templates/footer.php' ?>

<script>
    $(document).ready(function () {
        $('#vendor_id').change(function () {
            var selectedVendor = $(this).find(':selected');
            var address = selectedVendor.data('address');
            var tin = selectedVendor.data('account');
            $('#vendor_address').val(address);
            $('#tin').val(tin);
        });

        $('#vendor_id').select2({
            theme: 'bootstrap-5', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Customer',
            allowClear: true
        });


        // $('#terms').change(function () {
        //     var terms = $(this).val();
        //     var deliveryDate = calculateDeliveryDate(terms);
        //     $('#delivery_date').val(deliveryDate);
        // });

        function calculateDeliveryDate(terms) {
            var currentDate = new Date();
            var deliveryDate = new Date(currentDate);

            if (terms === 'Due on Receipt') {
                // Delivery date is the same as the current date
                return currentDate.toISOString().split('T')[0];
            } else {
                var daysToAdd = parseInt(terms.replace('NET ', ''));
                deliveryDate.setDate(deliveryDate.getDate() + daysToAdd);
                return deliveryDate.toISOString().split('T')[0];
            }
        }

        // Populate dropdowns with accounts from PHP
        const products = <?php echo json_encode($products); ?>;
        let itemDropdownOptions = '';
        $.each(products, function (index, product) {
            itemDropdownOptions += `<option value="${product.id}" data-description="${product.item_purchase_description}" data-uom="${product.uom_name}">${product.item_name}</option>`;
        });

        const costCenterOptions = <?php echo json_encode($cost_centers); ?>;
        let costCenterDropdownOptions = '';
        costCenterOptions.forEach(function (costCenter) {
            costCenterDropdownOptions += `<option value="${costCenter.id}">${costCenter.code} - ${costCenter.particular}</option>`;
        });

        // Populate dropdowns with accounts from PHP
        const discountOptions = <?php echo json_encode($discounts); ?>;
        let discountDropdownOptions = '';
        discountOptions.forEach(function (discount) {
            discountDropdownOptions += `<option value="${discount.discount_rate}" data-account-id="${discount.discount_account_id}">${discount.discount_name}</option>`;
        });

        const inputVatOption = <?php echo json_encode($input_vats); ?>;
        let inputVatDropdownOptions = '';
        inputVatOption.forEach(function (input_vat) {
            inputVatDropdownOptions += `<option value="${input_vat.input_vat_rate}" data-account-id="${input_vat.id}">${input_vat.input_vat_name}</option>`;
        });

        // Add a new row to the table
        function addRow() {
            const newRow = `
                <tr>
                    <td><select class="form-control form-control-sm account-dropdown select2" name="item_id[]" onchange="populateFields(this)">${itemDropdownOptions}</select></td>
                    <td><select class="form-control form-control-sm cost-center-dropdown select2" name="cost_center_id[]">${costCenterDropdownOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm description-field" name="description[]" readonly></td>
                    <td><input type="text" class="form-control form-control-sm uom" name="uom[]" readonly></td>
                    <td><input type="text" class="form-control form-control-sm quantity" name="quantity[]" placeholder="Qty"></td>
                    <td><input type="text" class="form-control form-control-sm cost" name="cost[]" placeholder="Enter Cost"></td>
                    <td><input type="text" class="form-control form-control-sm amount" name="amount[]" placeholder="Amount" readonly></td>
                    <td><select class="form-control form-control form-control-sm discount_percentage select2" name="discount_percentage[]">${discountDropdownOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm discount_amount" name="discount_amount[]" placeholder="" readonly></td>
                    <td><input type="text" class="form-control form-control-sm net_amount_before_input_vat" name="net_amount_before_input_vat[]" placeholder="" readonly></td>
                    <td><input type="text" class="form-control form-control-sm net_amount" name="net_amount[]" placeholder=""></td>
                    <td><select class="form-control form-control-sm input_vat_percentage select2" name="input_vat_percentage[]">${inputVatDropdownOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm input_vat_amount" name="input_vat_amount[]" placeholder="" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm removeRow"><i class="fas fa-trash"></i></button></td>
                </tr>`;
            $('#itemTableBody').append(newRow);

            // Add event listener to calculate all values
            $('.quantity, .cost, .discount_percentage, .input_vat_percentage, .input_vat_amount').on('input', function () {
                calculateRowValues($(this).closest('tr'));
                calculateTotalAmount();
            });

            // Calculate totals for existing rows and update totals fields
            $('tr').each(function () {
                calculateRowValues($(this));
                calculateTotalAmount();
            });

            $('#itemTableBody .select2').select2({
                width: '100%',
                theme: 'bootstrap-5' // Use this if you're using Bootstrap 4
            });
        }


        // Function to calculate amounts, discount amounts, and sales tax amounts
        function calculateRowValues(row) {
            const quantity = parseFloat(row.find('.quantity').val()) || 0;
            const cost = parseFloat(row.find('.cost').val()) || 0;
            const discountPercentage = parseFloat(row.find('.discount_percentage').val()) || 0;
            const salesTaxPercentage = parseFloat(row.find('.input_vat_percentage').val()) || 0;

            const amount = quantity * cost;
            const discountAmount = (amount * discountPercentage) / 100;

            const netAmountBeforeTax = amount - discountAmount;
            const salesTaxAmount = (netAmountBeforeTax * salesTaxPercentage) / 100;
            const netAmount = netAmountBeforeTax - salesTaxAmount;

            row.find('.amount').val(amount.toFixed(2));
            row.find('.discount_amount').val(discountAmount.toFixed(2));
            row.find('.net_amount_before_input_vat').val(netAmountBeforeTax.toFixed(2));
            row.find('.input_vat_amount').val(salesTaxAmount.toFixed(2));
            row.find('.net_amount').val(netAmount.toFixed(2));
        }


        // Function to calculate total amount
        function calculateTotalAmount() {
            let totalAmount = 0;
            let totalDiscountAmount = 0;
            let totalNetAmountBeforeTax = 0;
            let totalInputVatAmount = 0;

            let vatableAmount = 0;
            let zeroRatedAmount = 0;
            let vatExemptAmount = 0;

            let totalAmountDue = 0;

            // Calculate total amount
            $('.amount').each(function () {
                const amount = parseFloat($(this).val()) || 0;
                totalAmount += amount;
            });

            // Calculate total discount amount
            $('.discount_amount').each(function () {
                const discount_amount = parseFloat($(this).val()) || 0;
                totalDiscountAmount += discount_amount;
            });

            // Calculate total net amount before input VAT
            $('.net_amount_before_input_vat').each(function () {
                const net_amount_before_input_vat = parseFloat($(this).val()) || 0;
                totalNetAmountBeforeTax += net_amount_before_input_vat;
            });

            // Calculate total input VAT amount
            $('.input_vat_amount').each(function () {
                const input_vat_amount = parseFloat($(this).val()) || 0;
                totalInputVatAmount += input_vat_amount;
            });

            // Calculate vatable, zero-rated, and VAT exempt amounts
            $('.net_amount').each(function () {
                const net_amount = parseFloat($(this).val()) || 0;
                const inputVatName = $(this).closest('tr').find('.input_vat_percentage option:selected').text();

                if (inputVatName === '12%') {
                    vatableAmount += net_amount;
                    console.log("TOTAL 12%: ", vatableAmount);
                } else if (inputVatName === 'E') {
                    vatExemptAmount += net_amount;
                    console.log("TOTAL E: ", vatExemptAmount);
                } else if (inputVatName === 'Z') {
                    zeroRatedAmount += net_amount;
                    console.log("TOTAL Z: ", zeroRatedAmount);
                }
            });

            totalAmountDue = totalInputVatAmount + vatableAmount + vatExemptAmount + zeroRatedAmount;

            // Update the UI
            $("#gross_amount").val(totalAmount.toFixed(2));
            $("#total_discount_amount").val(totalDiscountAmount.toFixed(2));
            $("#net_amount_due").val(totalNetAmountBeforeTax.toFixed(2));
            $("#total_input_vat_amount").val(totalInputVatAmount.toFixed(2));
            $("#vatable_amount").val(vatableAmount.toFixed(2));
            $("#zero_rated_amount").val(zeroRatedAmount.toFixed(2));
            $("#vat_exempt_amount").val(vatExemptAmount.toFixed(2));
            $("#total_amount_due").val(totalAmountDue.toFixed(2));
        }

        // Event listeners
        $('#addItemBtn').click(addRow);

        $(document).on('click', '.removeRow', function () {
            $(this).closest('tr').remove();
            calculateRowValues($(this).closest('tr'));
            calculateTotalAmount();
        });

        // Gather table items and submit form
        function gatherTableItems() {
            const items = [];
            $('#itemTableBody tr').each(function (index) {
                const item = {
                    item_id: $(this).find('select[name="item_id[]"]').val(),
                    cost_center_id: $(this).find('select[name="cost_center_id[]"]').val(),
                    description: $(this).find('input[name="description[]"]').val(),
                    uom: $(this).find('input[name="uom[]"]').val(),
                    quantity: $(this).find('input[name="quantity[]"]').val(),
                    cost: $(this).find('input[name="cost[]"]').val(),
                    amount: $(this).find('input[name="amount[]"]').val(),
                    discount_percentage: $(this).find('select[name="discount_percentage[]"]').val(),
                    discount_amount: $(this).find('input[name="discount_amount[]"]').val(),
                    net_amount_before_input_vat: $(this).find('input[name="net_amount_before_input_vat[]"]').val(),
                    net_amount: $(this).find('input[name="net_amount[]"]').val(),
                    input_vat_percentage: $(this).find('select[name="input_vat_percentage[]"]').val(),
                    input_vat_amount: $(this).find('input[name="input_vat_amount[]"]').val(),
                    input_vat_id: $(this).find('.input_vat_percentage option:selected').data('account-id')
                };

                items.push(item);
            });
            return items;
        }

        $('#writeCheckForm').submit(function (event) {
            event.preventDefault();
            const items = gatherTableItems();
            $('#item_data').val(JSON.stringify(items));

            this.submit();
        });


    });


    // Function to populate multiple fields based on selected option
    function populateFields(select) {
        const selectedOption = $(select).find('option:selected');
        const description = selectedOption.data('description');
        const uom = selectedOption.data('uom');
        // Add more fields as needed

        const row = $(select).closest('tr');
        row.find('.description-field').val(description);
        row.find('.uom').val(uom);
        // Populate more fields as needed
    }
</script>