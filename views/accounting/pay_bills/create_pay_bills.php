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

$page = 'sales_invoice'; // Set the variable corresponding to the current page
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <style>
        .table-sm .form-control {
            border: none;
            padding: 0;
            background-color: transparent;
            box-shadow: none;
            height: auto;
            line-height: inherit;
            font-size: inherit;
        }

        .select2-no-border .select2-selection {
            border: none !important;
            padding: 0 !important;
            box-shadow: none !important;
        }

        .select2-no-border .select2-selection__rendered {
            padding: 0 !important;
        }
    </style>
    <?php require 'views/templates/navbar.php' ?>
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3"><strong>Create Pay Bills</strong></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="pay_bills">Pay Bills</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Create Pay Bills</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="pay_bills" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Pay Bills
                        </a>
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
                                    <h5 class="card-title mb-0">Pay Bills Details</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-12 mb-3">
                                            <h6 class="border-bottom pb-2">Customer Details</h6>
                                        </div>
                                        <!-- Customer Details fields -->
                                        <div class="col-md-4 customer-details">
                                            <label for="account_name">Account</label>
                                            <select class="form-control form-control-sm select2" id="account_name"
                                                name="account_name">
                                                <option value="">Select Account</option>
                                                <?php foreach ($accounts as $account): ?>
                                                    <option value="<?= $account->id ?>"><?= $account->account_code ?> -
                                                        <?= $account->account_description ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="col-md-4 customer-details">
                                            <label for="customer_name">Filter By</label>
                                            <select class="form-control form-control-sm select2" id="customer_name"
                                                name="customer_name">
                                                <option value="">Filtered</option>
                                                <?php foreach ($customers as $customer): ?>
                                                    <option value="<?= $customer->id ?>"><?= $customer->customer_name ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="col-md-4 customer-details">
                                            <label for="payment_method">Payment Method</label>
                                            <select class="form-control form-control-sm select2" id="payment_method"
                                                name="payment_method">
                                                <option value="">Select Payment Method</option>
                                                <?php foreach ($payment_methods as $payment_method): ?>
                                                    <option value="<?= $payment_method->id ?>">
                                                        <?= $payment_method->payment_method_name ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>


                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="invoice_date">Ending Balance</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="invoice_date" name="invoice_date">
                                            </div>

                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="">Check Date</label>
                                                <input type="date" class="form-control form-control-sm"
                                                    id="invoice_due_date" name="invoice_due_date">
                                            </div>
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
                                <div class="col-md-2">
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                    id="flexSwitchCheckDefault">
                                                <label class="form-check-label" for="flexSwitchCheckDefault">Show
                                                    Bills</label>
                                            </div>
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
                                                <!-- Table header -->
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
        </div>
    </main>
</div>


    <?php require 'views/templates/footer.php' ?>

    <script>

        $(document).ready(function () {

            $('#account_name').select2({
                theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
                width: '100%',
                placeholder: 'Select Account',
                allowClear: false
            });


            $('#customer_name').select2({
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

            $('#tax_withheld_percentage').select2({
                theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
                width: '100%',
                placeholder: 'Select Tax Withheld',
                allowClear: false
            });


            $('#vendor_id').change(function () {
                var selectedVendor = $(this).find(':selected');
                var address = selectedVendor.data('address');
                var tin = selectedVendor.data('account');
                $('#vendor_address').val(address);
                $('#tin').val(tin);
            });

            $('#terms').change(function () {
                var terms = $(this).val();
                var deliveryDate = calculateDeliveryDate(terms);
                $('#delivery_date').val(deliveryDate);
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

            // Populate dropdowns with accounts from PHP
            const products = <?php echo json_encode($products); ?>;
            let itemDropdownOptions = '';
            $.each(products, function (index, product) {
                itemDropdownOptions += `<option value="${product.id}" data-description="${product.purchase_description}" data-uom="${product.uom_id}">${product.item_name}</option>`;
            });

            // Populate dropdowns with accounts from PHP
            const discountOptions = <?php echo json_encode($discounts); ?>;
            let discountDropdownOptions = '';
            discountOptions.forEach(function (discount) {
                discountDropdownOptions += `<option value="${discount.discount_rate}" data-account-id="${discount.discount_account_id}">${discount.discount_name}</option>`;
            });

            const salesTaxOption = <?php echo json_encode($sales_taxes); ?>;
            let salesTaxAmountDropdownOptions = '';
            salesTaxOption.forEach(function (sales_tax) {
                salesTaxAmountDropdownOptions += `<option value="${sales_tax.sales_tax_rate}" data-account-id="${sales_tax.id}">${sales_tax.sales_tax_name}</option>`;
            });

            // Add a new row to the table
            function addRow() {
                const newRow = `
                <tr>
                    <td><select class="form-control form-control-sm account-dropdown select2" name="item_id[]" onchange="populateFields(this)">${itemDropdownOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm description-field" name="description[]" readonly></td>
                    <td><input type="text" class="form-control form-control-sm uom" name="uom[]" readonly></td>
                    <td><input type="text" class="form-control form-control-sm quantity" name="quantity[]" placeholder="Qty"></td>
                    <td><input type="text" class="form-control form-control-sm cost" name="cost[]" placeholder="Enter Cost"></td>
                    <td><input type="text" class="form-control form-control-sm amount" name="amount[]" placeholder="Amount" readonly></td>
                    <td><select class="form-control form-control form-control-sm discount-percentage select2" name="discount_percentage[]">${discountDropdownOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm discount_amount" name="discount_amount[]" placeholder="" readonly></td>
                    <td><input type="text" class="form-control form-control-sm net_amount_before_input_vat" name="net_amount_before_input_vat[]" placeholder="" readonly></td>
                    <td><input type="text" class="form-control form-control-sm net_amount" name="net_amount[]" placeholder=""></td>
                    <td><select class="form-control form-control-sm input_vat_percentage select2" name="input_vat_percentage[]">${salesTaxAmountDropdownOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm input_vat_amount" name="input_vat_amount[]" placeholder="" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm removeRow"><i class="fas fa-trash"></i></button></td>
                </tr>`;
                $('#itemTableBody').append(newRow);

                // Initialize Select2 on the newly created select element
               // Initialize Select2 for the new row
               $('#itemTableBody tr:last-child').find('.select2').select2({
                    width: '100%',
                    theme: 'classic' // Use this if you're using Bootstrap 4
                });

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
            }


            // Function to calculate amounts, discount amounts, and sales tax amounts
            // Function to calculate amounts, discount amounts, and sales tax amounts
            function calculateRowValues(row) {

                const quantity = parseFloat(row.find('.quantity').val()) || 0;
                const cost = parseFloat(row.find('.cost').val()) || 0;
                const discountPercentage = parseFloat(row.find('.discount_percentage').val()) || 0;
                const salesTaxPercentage = parseFloat(row.find('.input_vat_percentage').val()) || 0 || 0;

                const amount = quantity * cost;
                const discountAmount = (amount * discountPercentage) / 100;

                const netAmountBeforeTax = amount - discountAmount;

                const vatPercentageAmount = salesTaxPercentage / 100;
                const salesTaxAmount = (netAmountBeforeTax / (1 + salesTaxPercentage / 100)) * (salesTaxPercentage / 100);
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


                $('.amount').each(function () {
                    const amount = parseFloat($(this).val()) || 0;
                    if (!isNaN(amount)) {
                        totalAmount += amount;
                    }
                });

                $('.discount_amount').each(function () {
                    const discount_amount = parseFloat($(this).val()) || 0;
                    if (!isNaN(discount_amount)) {
                        totalDiscountAmount += discount_amount;
                    }
                });

                $('.net_amount_before_input_vat').each(function () {
                    const net_amount_before_input_vat = parseFloat($(this).val()) || 0;
                    if (!isNaN(net_amount_before_input_vat)) {
                        totalNetAmountBeforeTax += net_amount_before_input_vat;
                    }
                });


                $('.input_vat_amount').each(function () {
                    const input_vat_amount = parseFloat($(this).val()) || 0;
                    if (!isNaN(input_vat_amount)) {
                        totalInputVatAmount += input_vat_amount;
                    }
                });


                $('.net_amount').each(function () {
                    const net_amount = parseFloat($(this).val());
                    const inputVatName = $(this).closest('tr').find('.input_vat_percentage option:selected').text();
                    if (!isNaN(net_amount)) {

                        if (inputVatName === '12%') {
                            vatableAmount += net_amount;
                            console.log("TOTAL 12%: ", vatableAmount); // Add this line for debugging

                        } else if (inputVatName === 'E') {
                            vatExemptAmount += net_amount;
                            console.log("TOTAL E: ", net_amount); // Add this line for debugging

                        } else if (inputVatName === 'Z') {
                            zeroRatedAmount += net_amount;
                            console.log("TOTAL Z: ", net_amount); // Add this line for debugging

                        }



                    }
                });

                const taxWithheldPercentage = parseFloat($("#tax_withheld_percentage").val()) || 0;
                console.log(taxWithheldPercentage);
                const taxWithheldAmount = (taxWithheldPercentage / 100) * totalInputVatAmount;
                $("#tax_withheld_amount").val(taxWithheldAmount.toFixed(2));

                totalAmountDueBefore = totalInputVatAmount + vatableAmount + vatExemptAmount + zeroRatedAmount;


                $("#gross_amount").val(totalAmount.toFixed(2));
                $("#discount_amount").val(totalDiscountAmount.toFixed(2));
                $("#net_amount_due").val(totalNetAmountBeforeTax.toFixed(2));
                $("#input_vat_amount").val(totalInputVatAmount.toFixed(2));
                $("#vatable_amount").val(vatableAmount.toFixed(2));
                $("#zero_rated_amount").val(zeroRatedAmount.toFixed(2));
                $("#vat_exempt_amount").val(vatExemptAmount.toFixed(2));
                $("#total_amount_due").val(totalAmountDue.toFixed(2));


            }

            // Event listeners
            $('#addItemBtn').click(addRow);

            // Event listener for tax withheld percentage change
            $('#tax_withheld_percentage').on('change', function () {
                calculateTotalAmount();
            });

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
                        description: $(this).find('input[name="description[]"]').val(),
                        uom: $(this).find('input[name="uom[]"]').val(),
                        quantity: $(this).find('input[name="quantity[]"]').val(),
                        cost: $(this).find('input[name="cost[]"]').val(),
                        amount: $(this).find('input[name="amount[]"]').val(),
                        discount_percentage: $(this).find('select[name="discount_percentage[]"]').val(),
                        discount_amount: $(this).find('input[name="discount_amount[]"]').val(),
                        discount_account_id: $(this).find('.discount_percentage option:selected').data('account-id'),
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