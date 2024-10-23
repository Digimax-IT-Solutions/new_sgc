<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('balance_sheet');
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

$page = 'billing_statement'; // Set the variable corresponding to the current page
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

            <div class="row">
                <div class="col-sm-12 d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-3"><strong>Billing</strong>Statement</h1>

                    <div class="d-flex justify-content-end">
                        <a href="billing_statement" class="btn btn-secondary">
                            <i class="align-middle" data-feather="arrow-left-circle"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_payment_method') ?>
                    <?php displayFlashMessage('delete_payment_method') ?>
                    <?php displayFlashMessage('update_payment_method') ?>
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <form id="writeCheckForm" action="api/invoice_controller.php?action=add" method="POST">
                                <input type="hidden" name="action" id="modalAction" value="add" />
                                <input type="hidden" name="id" id="itemId" value="" />
                                <input type="hidden" name="item_data" id="item_data" />

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="invoice_number">Billed To</label>
                                            <input type="text" class="form-control form-control-sm" id="invoice_number"
                                                name="invoice_number" placeholder="Enter invoice #">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="invoice_date">Invoice Date</label>
                                            <input type="date" class="form-control form-control-sm" id="invoice_date"
                                                name="invoice_date" value="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="">Invoice Due Date</label>
                                            <input type="date" class="form-control form-control-sm"
                                                id="invoice_due_date" name="invoice_due_date">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">

                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">

                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="invoice_date">Account</label>
                                            <select class="form-control form-control-sm" id="customer_name"
                                                name="customer_name">
                                                <option value="">Select Account</option>
                                                <?php foreach ($accounts as $account): ?>
                                                    <option value="<?= $account->id ?>"><?= $account->account_code ?> -
                                                        <?= $account->description ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <br><br><br>

                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="invoice_number">A/R Acc</label>
                                            <select class="form-control form-control-sm" id="customer_name"
                                                name="customer_name">
                                                <option value="">Select Customer</option>
                                                <?php foreach ($customers as $customer): ?>
                                                    <option value="<?= $customer->id ?>"><?= $customer->customer_name ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <label for="tin">TIN</label>
                                            <input type="text" class="form-control form-control-sm" id="tin" name="tin">
                                        </div>

                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="invoice_date">Subject</label>
                                            <input type="text" class="form-control form-control-sm" id="invoice_date"
                                                name="invoice_date">
                                        </div>
                                        <div class="form-group">
                                            <label for="tin">C.E No.</label>
                                            <input type="text" class="form-control form-control-sm" id="tin" name="tin">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="invoice_date">Period</label>
                                            <input type="text" class="form-control form-control-sm" id="invoice_date"
                                                name="invoice_date">
                                        </div>
                                        <div class="form-group">
                                            <label for="tin">Business Style</label>
                                            <input type="text" class="form-control form-control-sm" id="tin" name="tin">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="invoice_terms">Terms</label>
                                            <select class="form-control form-control-sm" id="invoice_terms"
                                                name="invoice_terms">
                                                <option value="">Select Terms</option>
                                                <?php foreach ($terms as $term): ?>
                                                    <option value="<?= $term->id ?>"><?= $term->term_name ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">

                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">

                                        </div>
                                    </div>
                                </div>

                                <br><br>
                                <div class="table-responsive-sm">
                                    <table class="table table-sm table-bordered" id="itemTable">
                                        <thead class="" style="font-size: 10px;">
                                            <tr>
                                                <th style="width: 100px;">ITEM</th>
                                                <th style="width: 70px;">DESC</th>
                                                <th style="width: 20px;">U/M</th>
                                                <th style="width: 50px;">QTY</th>
                                                <th style="width: 50px;">COST</th>
                                                <th style="width: 50px;">AMOUNT</th>
                                                <th style="width: 50px;">DSC TYPE</th>
                                                <th style="width: 75px;">DSC</th>
                                                <th style="width: 75px;">NET</th>
                                                <th style="width: 75px;">TAX AMT</th>
                                                <th style="width: 30px;">TAX TYPE</th>
                                                <th style="width: 50px;">VAT</th>
                                                <th style="width: 25px;"></th> <!-- Empty th for spacing -->
                                            </tr>
                                        </thead>
                                        <tbody id="itemTableBody" class="">
                                            <!-- Existing rows or dynamically added rows will be appended here -->
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-secondary" id="addItemBtn">Add Item</button>
                                </div>
                                <br><br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="invoice_memo">Memo</label>
                                            <input type="text" class="form-control form-control-sm" id="invoice_memo"
                                                name="invoice_memo" placeholder="Enter memo">
                                        </div>
                                    </div>
                                </div>

                                <br>
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="invoice_number">Payment Method</label>
                                            <select class="form-control form-control-sm" id="customer_name"
                                                name="customer_name">
                                                <option value="">Select Payment Method</option>
                                                <?php foreach ($payment_methods as $payment_method): ?>
                                                    <option value="<?= $payment_method->id ?>">
                                                        <?= $payment_method->payment_method_name ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="invoice_number">Location</label>
                                            <select class="form-control form-control-sm" id="customer_name"
                                                name="customer_name">
                                                <option value="">Select Location</option>
                                                <?php foreach ($locations as $location): ?>
                                                    <option value="<?= $location->id ?>"><?= $location->name ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                                <br><br>
                                <div class="modal-footer">
                                    <div class="summary-details">
                                        <div class="container">
                                            <!-- GROSS AMOUNT -->
                                            <div class="row">
                                                <div class="col-md-4 d-inline-block text-right">
                                                    <label>Gross Amount:</label>
                                                </div>

                                                <div class="col-md-6 d-inline-block">
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="gross_amount" id="gross_amount" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- DISCOUNT -->
                                            <div class="row">
                                                <div class="col-md-4 d-inline-block text-right">
                                                    <label for="discount_percentage">Discount:</label>
                                                </div>

                                                <div class="col-md-6 d-inline-block">
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="discount_amount" id="discount_amount" readonly>
                                                    </div>
                                                    <input type="text" class="form-control" name="discount_account_id"
                                                        id="discount_account_id" hidden>
                                                </div>
                                            </div>
                                            <!-- NET AMOUND DUE -->
                                            <div class="row">
                                                <div class="col-md-4 d-inline-block text-right">
                                                    <label>Net Amount:</label>
                                                </div>

                                                <div class="col-md-6 d-inline-block">
                                                    <div class="input-group input-group-sm ">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="net_amount_due" id="net_amount_due" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- VAT PERCENTAGE -->
                                            <div class="row">
                                                <div class="col-md-4 d-inline-block text-right">
                                                    <label for="vat_percentage">VAT:</label>
                                                </div>

                                                <div class="col-md-6 d-inline-block">
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="input_vat_amount" id="input_vat_amount" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- VATATABLE -->
                                            <div class="row">
                                                <div class="col-md-4 d-inline-block text-right">
                                                    <label for="">Vatable 12%:</label>
                                                </div>

                                                <div class="col-md-6 d-inline-block">
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="vatable_amount" id="vatable_amount" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- VAT ZERO RATED -->
                                            <div class="row">
                                                <div class="col-md-4 d-inline-block text-right">
                                                    <label for="">Zero-rated:</label>
                                                </div>

                                                <div class="col-md-6 d-inline-block">
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="zero_rated_amount" id="zero_rated_amount" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- VAT EXEMPT -->
                                            <div class="row">
                                                <div class="col-md-4 d-inline-block text-right">
                                                    <label for="">Vat-Exempt:</label>
                                                </div>

                                                <div class="col-md-6 d-inline-block">
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="vat_exempt_amount" id="vat_exempt_amount" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- TAX WITHHELD -->
                                            <div class="row" id="taxWithheldRow">
                                                <div class="col-md-4 d-inline-block text-right">
                                                    <label>Tax Withheld (%):</label>
                                                </div>
                                                <div class="col-md-4 d-inline-block">
                                                    <select class="form-control form-control-sm"
                                                        id="tax_withheld_percentage" name="tax_withheld_percentage"
                                                        required>
                                                        <option value="">-- Select Tax Withheld --</option>
                                                        <?php foreach ($wtaxes as $wtax): ?>
                                                            <option value="<?= $wtax->wtax_rate ?>"
                                                                data-account-id="<?= $wtax->wtax_account_id ?>">
                                                                <?= $wtax->wtax_name ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-4 d-inline-block">
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="tax_withheld_amount" id="tax_withheld_amount"
                                                            readonly>
                                                    </div>
                                                    <input type="hidden" class="form-control"
                                                        name="tax_withheld_account_id" id="tax_withheld_account_id">
                                                </div>
                                            </div>
                                            <br>
                                            <!-- TOTAL AMOUNT DUE -->
                                            <div class="row" style="font-size: 20px">
                                                <div class="col-md-6 d-inline-block text-right">
                                                    <label>Total Amount Due:</label>
                                                </div>

                                                <div class="col-md-6 d-inline-block">
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" class="form-control form-control-sm"
                                                            name="total_amount_due" id="total_amount_due" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Submit Button -->
                                <div class="row">
                                    <div class="col-md-10 d-inline-block">
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </main>




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
                    <td><select class="form-control form-control-sm account-dropdown" name="item_id[]" onchange="populateFields(this)">${itemDropdownOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm description-field" name="description[]" readonly></td>
                    <td><input type="text" class="form-control form-control-sm uom" name="uom[]" readonly></td>
                    <td><input type="text" class="form-control form-control-sm quantity" name="quantity[]" placeholder="Qty"></td>
                    <td><input type="text" class="form-control form-control-sm cost" name="cost[]" placeholder="Enter Cost"></td>
                    <td><input type="text" class="form-control form-control-sm amount" name="amount[]" placeholder="Amount" readonly></td>
                    <td><select class="form-control form-control form-control-sm discount_percentage" name="discount_percentage[]">${discountDropdownOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm discount_amount" name="discount_amount[]" placeholder="" readonly></td>
                    <td><input type="text" class="form-control form-control-sm net_amount_before_input_vat" name="net_amount_before_input_vat[]" placeholder="" readonly></td>
                    <td><input type="text" class="form-control form-control-sm net_amount" name="net_amount[]" placeholder=""></td>
                    <td><select class="form-control form-control-sm input_vat_percentage" name="input_vat_percentage[]">${salesTaxAmountDropdownOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm input_vat_amount" name="input_vat_amount[]" placeholder="" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm removeRow"><i class="fas fa-trash"></i></button></td>
                </tr>`;
                $('#itemTableBody').append(newRow);

                // Initialize Select2 on the newly created select element
                $('.account-dropdown').last().select2({
                    width: '100%',
                    placeholder: 'Select an item',
                    allowClear: false,
                    dropdownParent: $('.account-dropdown').last().closest('td'), // Adjust dropdown positioning if needed

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