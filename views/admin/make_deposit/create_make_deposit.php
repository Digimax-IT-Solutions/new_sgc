<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('make_deposit');
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

$page = 'make_deposit'; // Set the variable corresponding to the current page
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
    </style>
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3"><strong>Create Deposit</strong></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="make_deposit">Make Deposit</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Create Deposit</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="make_deposit" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Make Deposit
                        </a>
                    </div>
                </div>
            </div>

            <form id="invoiceForm" action="api/invoice_controller.php?action=add" method="POST">
                <input type="hidden" name="action" id="modalAction" value="add" />
                <input type="hidden" name="id" id="itemId" value="" />
                <input type="hidden" name="item_data" id="item_data" />

                <div class="row">
                    <div class="col-12 col-lg-8">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Make Deposit Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <!-- Customer Details Section -->
                                    <div class="col-12 mb-3">
                                        <h6 class="border-bottom pb-2">Deposit Details</h6>
                                    </div>
                                    <div class="col-md-4 customer-details">
                                        <label for="invoice_number">Deposit to</label>
                                        <input type="text" class="form-control form-control-sm" id="invoice_number"
                                            name="invoice_number" placeholder="Enter invoice #">
                                    </div>
                                    <div class="col-md-4 customer-details">
                                        <label for="invoice_date">Date</label>
                                        <input type="date" class="form-control form-control-sm" id="invoice_date"
                                            name="invoice_date" value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                    <div class="col-md-4 customer-details">
                                        <label for="tin">Deposit ID</label>
                                        <input type="text" class="form-control form-control-sm" id="tin" name="tin">
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
                                <div class="card-body">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" id="cash_sales"
                                            name="cash_sales" checked>
                                        <label class="form-check-label fw-bold" for="cash_sales">Cash Sales</label>
                                    </div>

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
                                                id="total_discount_amount" name="total_discount_amount" value="0.00"
                                                readonly>
                                            <input type="text" class="form-control" name="discount_account_id"
                                                id="discount_account_id" hidden>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-sm-6 col-form-label">Net Amount:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control-plaintext text-end"
                                                id="net_amount_due" name="net_amount_due" value="0.00" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-sm-6 col-form-label">VAT:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control-plaintext text-end"
                                                id="total_vat_amount" name="total_vat_amount" value="0.00" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-sm-6 col-form-label">Vatable 12%:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control-plaintext text-end"
                                                id="vatable_amount" name="vatable_amount" value="0.00" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-sm-6 col-form-label">Zero-rated:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control-plaintext text-end"
                                                id="zero_rated_amount" name="zero_rated_amount" value="0.00" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-sm-6 col-form-label">Vat-Exempt:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control-plaintext text-end"
                                                id="vat_exempt_amount" name="vat_exempt_amount" value="0.00" readonly>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-sm-6 col-form-label">Tax Withheld (%):</label>
                                        <div class="col-sm-6">
                                            <select class="form-select form-select-sm" id="tax_withheld_percentage"
                                                name="tax_withheld_percentage">
                                                <option value="">Select</option>
                                                <!-- Add options here -->
                                                <?php foreach ($wtaxes as $wtax): ?>
                                                    <option value="<?= $wtax->wtax_rate ?>"
                                                        data-account-id="<?= $wtax->wtax_account_id ?>">
                                                        <?= $wtax->wtax_name ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <label class="col-sm-6 col-form-label">Tax Withheld Amount:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control-plaintext text-end"
                                                id="tax_withheld_amount" name="tax_withheld_amount" value="0.00"
                                                readonly>
                                            <input type="hidden" class="form-control" name="tax_withheld_account_id"
                                                id="tax_withheld_account_id">
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
                            <div class="card-footer d-flex">
                                <button type="button" class="btn btn-secondary me-2">Save as Draft</button>
                                <button type="submit" class="btn btn-info me-2">Save and Print</button>
                                <button type="reset" class="btn btn-danger">Clear</button>
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
        </div>
    </main>
</div>


<?php require 'views/templates/footer.php' ?>

<script>

    $(document).ready(function () {

        $('#customer_id').select2({
            theme: 'bootstrap-5', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Customer',
            allowClear: true
        });

        $('#tax_withheld_percentage').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Tax Withheld',
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

        // INVOICE TERMS 
        $('#invoice_terms').change(function () {
            var terms = $(this).val();
            var deliveryDate = calculateDeliveryDate(terms);
            $('#invoice_due_date').val(deliveryDate);
        });

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
        <td><select class="form-control form-control-sm account-dropdown select2" name="item_id[]" onchange="populateFields(this)">${itemDropdownOptions}</select></td>
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
                theme: 'classic' // Use this if you're using Bootstrap 4
            });

            $newRow.find('.quantity, .cost, .discount_percentage, .sales_tax_percentage, .sales_tax_amount').on('input', function () {
                calculateRowValues($(this).closest('tr'));
                calculateTotalAmount();
            });

            calculateRowValues($newRow);
            calculateTotalAmount();
        });

        // Calculate row values
        function calculateRowValues(row) {
            const quantity = parseFloat(row.find('.quantity').val()) || 0;
            const cost = parseFloat(row.find('.cost').val()) || 0;
            const discountPercentage = parseFloat(row.find('.discount_percentage').val()) || 0;
            const salesTaxPercentage = parseFloat(row.find('.sales_tax_percentage').val()) || 0;

            const amount = quantity * cost;
            const discountAmount = (amount * discountPercentage) / 100;
            const netAmountBeforeTax = amount - discountAmount;
            const salesTaxAmount = (netAmountBeforeTax / (1 + salesTaxPercentage / 100)) * (salesTaxPercentage / 100);
            const netAmount = netAmountBeforeTax - salesTaxAmount;

            row.find('.amount').val(amount.toFixed(2));
            row.find('.discount_amount').val(discountAmount.toFixed(2));
            row.find('.net_amount_before_sales_tax').val(netAmountBeforeTax.toFixed(2));
            row.find('.sales_tax_amount').val(salesTaxAmount.toFixed(2));
            row.find('.net_amount').val(netAmount.toFixed(2));
        }

        function calculateTotalAmount() {
            const totals = {
                totalAmount: 0, totalDiscountAmount: 0, totalNetAmountBeforeTax: 0, totalInputVatAmount: 0,
                vatableAmount: 0, zeroRatedAmount: 0, vatExemptAmount: 0
            };

            $('.amount, .discount_amount, .net_amount_before_sales_tax, .sales_tax_amount, .net_amount').each(function () {
                const value = parseFloat($(this).val()) || 0;
                const inputVatName = $(this).closest('tr').find('.sales_tax_percentage option:selected').text();

                if ($(this).hasClass('amount')) totals.totalAmount += value;
                else if ($(this).hasClass('discount_amount')) totals.totalDiscountAmount += value;
                else if ($(this).hasClass('net_amount_before_sales_tax')) totals.totalNetAmountBeforeTax += value;
                else if ($(this).hasClass('sales_tax_amount')) totals.totalInputVatAmount += value;
                else if ($(this).hasClass('net_amount')) {
                    if (inputVatName === '12%') totals.vatableAmount += value;
                    else if (inputVatName === 'E') totals.vatExemptAmount += value;
                    else if (inputVatName === 'Z') totals.zeroRatedAmount += value;
                }
            });

            // Update form fields
            $("#gross_amount").val(totals.totalAmount.toFixed(2));
            $("#total_discount_amount").val(totals.totalDiscountAmount.toFixed(2));
            $("#net_amount_due").val(totals.totalNetAmountBeforeTax.toFixed(2));
            $("#total_vat_amount").val(totals.totalInputVatAmount.toFixed(2));
            $("#vatable_amount").val(totals.vatableAmount.toFixed(2));
            $("#zero_rated_amount").val(totals.zeroRatedAmount.toFixed(2));
            $("#vat_exempt_amount").val(totals.vatExemptAmount.toFixed(2));

            // Get the selected tax withheld option
            const selectedTaxWithheld = $("#tax_withheld_percentage option:selected");
            const taxWithheldPercentage = parseFloat(selectedTaxWithheld.val()) || 0;
            const taxWithheldName = selectedTaxWithheld.text();

            // Calculate tax withheld amount based on the sum of vatable, zero-rated, and vat-exempt amounts
            const taxableBase = totals.vatableAmount + totals.zeroRatedAmount + totals.vatExemptAmount;
            const taxWithheldAmount = (taxWithheldPercentage / 100) * taxableBase;

            $("#tax_withheld_amount").val(taxWithheldAmount.toFixed(2));

            // Calculate total amount due
            const subtotal = totals.totalInputVatAmount + taxableBase;
            const totalAmountDue = subtotal - taxWithheldAmount;

            $("#total_amount_due").val(totalAmountDue.toFixed(2));
        }
        // REMOVE ITEM
        $(document).on('click', '.removeRow', function () {
            $(this).closest('tr').remove();
            calculateRowValues($(this).closest('tr'));
            calculateTotalAmount();
        });

        // Event listener for tax withheld percentage change
        $('#tax_withheld_percentage').on('change', function () {
            calculateTotalAmount();
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

        // Gather table items function (unchanged)
        function gatherTableItems() {
            const items = [];
            $('#itemTableBody tr').each(function (index) {
                const item = {
                    item_id: $(this).find('select[name="item_id[]"]').val(),
                    item_name: $(this).find('.item-name').val(),
                    quantity: $(this).find('input[name="quantity[]"]').val(),
                    cost: $(this).find('input[name="cost[]"]').val(),
                    cost_price: $(this).find('input[name="cost_price[]"]').val(),
                    amount: $(this).find('input[name="amount[]"]').val(),
                    discount_percentage: $(this).find('select[name="discount_percentage[]"]').val(),
                    discount_amount: $(this).find('input[name="discount_amount[]"]').val(),
                    discount_account_id: $(this).find('.discount_percentage option:selected').data('account-id'),
                    net_amount_before_sales_tax: $(this).find('input[name="net_amount_before_sales_tax[]"]').val(),
                    net_amount: $(this).find('input[name="net_amount[]"]').val(),
                    sales_tax_percentage: $(this).find('select[name="sales_tax_percentage[]"]').val(),
                    sales_tax_amount: $(this).find('input[name="sales_tax_amount[]"]').val(),
                    output_vat_id: $(this).find('.sales_tax_percentage option:selected').data('account-id'),
                    cogs_account_id: $(this).find('.item-cogs-account-id').val(),
                    income_account_id: $(this).find('.item-income-account-id').val(),
                    asset_account_id: $(this).find('.item-asset-account-id').val()
                };
                items.push(item);
            });
            return items;
        }

        $('#invoiceForm').submit(function (event) {
            event.preventDefault();
            const items = gatherTableItems();

            // Show the loading overlay
            document.getElementById('loadingOverlay').style.display = 'flex';

            // Create and play the audio
            const audio = new Audio('photos/rr.mp3');
            audio.play().catch(e => console.error('Error playing audio:', e));

            // Check if there are any items
            if (items.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please add items first'
                });
                document.getElementById('loadingOverlay').style.display = 'none';
                return;
            }

            $('#item_data').val(JSON.stringify(items));

            // Get unique discount account IDs and output VAT IDs
            const discountAccountIds = getUniqueDiscountAccountIds();
            const outputVatIds = getUniqueOutputVatIds();

            // Add hidden fields for discount_account_ids and output_vat_ids
            $(this).append(`<input type="hidden" name="discount_account_ids" value="${discountAccountIds.join(',')}">`);
            $(this).append(`<input type="hidden" name="output_vat_ids" value="${outputVatIds.join(',')}">`);

            // Use AJAX to submit the form
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
                success: function (response) {
                    document.getElementById('loadingOverlay').style.display = 'none';

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Invoice submitted successfully!',
                            showCancelButton: true,
                            confirmButtonText: 'Print',
                            cancelButtonText: 'No, thanks'
                        }).then((result) => {
                            if (result.isConfirmed && response.invoiceId) {
                                printInvoice(response.invoiceId, 1);  // Pass 1 for initial print
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
                error: function (jqXHR, textStatus, errorThrown) {
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
        });

        function printInvoice(invoiceId, printStatus) {
            // First, update the print status
            $.ajax({
                url: 'api/invoice_controller.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'update_print_status',
                    invoice_id: invoiceId,
                    print_status: printStatus
                },
                success: function (response) {
                    if (response.success) {
                        // If the status was updated successfully, proceed with printing
                        console.log('Print status updated, now printing invoice:', invoiceId);
                        // Open a new window with the print view
                        const printFrame = document.getElementById('printFrame');
                        const printContentUrl = `print_invoice?action=print&id=${invoiceId}`;

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
    });

    // Function to populate multiple fields based on selected option
    function populateFields(select) {
        const selectedOption = $(select).find('option:selected');
        const itemName = selectedOption.data('item-name');
        const description = selectedOption.data('description');
        const uom = selectedOption.data('uom');
        const costPrice = selectedOption.data('cost-price');
        const cogsAccountId = selectedOption.data('cogs-account-id');
        const incomeAccountId = selectedOption.data('income-account-id');
        const assetAccountId = selectedOption.data('asset-account-id');

        const row = $(select).closest('tr');
        row.find('.item-name').val(itemName);
        row.find('.description-field').val(description);
        row.find('.uom').val(uom);
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

</script>