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

// Generate the new CI number by getting the last CI number and incrementing it
// $ci_no = OfficialReceipt::getLastOrNo();


?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>

<style>
    .styled-table {
        border-collapse: collapse;
        margin: 25px 0;
        font-size: 0.9em;
        font-family: sans-serif;
        min-width: 400px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
    }

    .styled-table .text-right {
        text-align: center;
    }

    .styled-table thead tr {
        background-color: rgb(4, 47, 48);
        color: #ffffff;
        text-align: left;
    }


    .styled-table tbody tr {
        border-bottom: 2px solid rgb(4, 47, 48);
    }


    .styled-table tbody tr:last-of-type {
        border-bottom: 2px solid rgb(4, 47, 48);
    }


    .form-label {
        font-size: 0.675rem;
        margin-bottom: 0.25rem;
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
                        <h1 class="h3"><strong> Cash Invoice</strong></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="receive_payment">Payments</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Create Cash Invoice</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <!-- New Cash Invoice Button with Custom Styles -->
                        <a href="" class="btn btn-success">
                            <i class="fas fa-plus-circle"></i> Create new
                        </a>

                        <!-- Back Button (no change) -->
                        <a href="or_list" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>

                </div>
            </div>

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
                                    <!-- <h1 style="color: rgb(4, 47, 48);">Cash Invoice #</h1> -->

                                    <div class="col-md-4 customer-details">
                                        <label for="invoice_no" class="form-label" style="color: rgb(4, 47, 48);">INVOICE #</label>
                                        <input type="text" class="form-control form-control-sm" id="invoice_no"
                                            name="invoice_no">
                                    </div>


                                    <!-- customer_id -->
                                    <!-- Customer Dropdown with Add New Customer Option -->
                                    <!-- Customer Dropdown with Add New Customer option next to the label -->
                                    <div class="col-md-4 customer-details">
                                        <label for="customer_name" class="form-label">
                                            Customer
                                            <a href="#" id="addNewCustomerLink" class="ms-3 text-primary">| Add New</a>
                                        </label>
                                        <select class="form-select form-select-sm select2" id="customer_id" name="customer_id" required>
                                            <option value="">Select Customer</option>
                                            <?php foreach ($customers as $customer): ?>
                                                <option value="<?= $customer->id ?>" data-customer-name="<?= htmlspecialchars($customer->customer_name, ENT_QUOTES, 'UTF-8') ?>">
                                                    <?= $customer->customer_name ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>


                                    <div class="col-md-4 customer-details">
                                        <label for="customer_tin" class="form-label">TIN</label>
                                        <input type="text" class="form-control form-control-sm" id="customer_tin"
                                            name="customer_tin" readonly>
                                    </div>
                                    <div class="col-md-4 customer-details">
                                        <label for="customer_email" class="form-label">Email</label>
                                        <input type="email" class="form-control form-control-sm" id="customer_email"
                                            name="customer_email" readonly>
                                    </div>
                                    <div class="col-md-4 customer-details">
                                        <label for="billing_address" class="form-label">Billing Address</label>
                                        <input type="text" class="form-control form-control-sm" id="billing_address"
                                            name="billing_address" readonly>
                                    </div>
                                    <div class="col-md-4 customer-details">
                                        <label for="business_style" class="form-label">Business Style</label>
                                        <input type="text" class="form-control form-control-sm" id="business_style"
                                            name="business_style" readonly>
                                    </div>
                                    <!-- location -->
                                    <div class="col-md-4 customer-details">
                                        <label for="location" class="form-label">
                                            Location
                                            <a href="#" id="addNewLocationLink" class="ms-3 text-primary">| Add New</a>
                                        </label>
                                        <select class="form-select form-select-sm" id="location" name="location" required>
                                            <option value="">Select Location</option>
                                            <?php foreach ($locations as $location): ?>
                                                <option value="<?= $location->id ?>"><?= $location->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <!-- customer_po -->
                                    <div class="col-md-4 customer-details">
                                        <label for="customer_po" class="form-label">Customer PO No.</label>
                                        <input type="text" class="form-control form-control-sm" id="customer_po"
                                            name="customer_po">
                                    </div>
                                    <!-- rep_no -->
                                    <div class="col-md-4 customer-details">
                                        <label for="rep" class="form-label">Rep No</label>
                                        <input type="text" class="form-control form-control-sm" id="rep" name="rep">
                                    </div>
                                    <!-- OR Details Section -->
                                    <div class="col-12 mt-3 mb-3">
                                        <h6 class="border-bottom pb-2">Payment Information</h6>
                                    </div>
                                    <!-- or_number -->
                                    <div class="col-md-3 invoice-details">
                                        <label for="or_number" class="form-label">OR Number</label>
                                        <input type="text" class="form-control form-control-sm" id="or_number"
                                            name="or_number">
                                    </div>
                                    <!-- or_date -->
                                    <div class="col-md-3 invoice-details">
                                        <label for="or_date" class="form-label">Payment Date</label>
                                        <input type="date" class="form-control form-control-sm" id="or_date"
                                            name="or_date" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    <!-- or_account_id -->
                                    <div class="col-md-3 invoice-details">
                                        <label for="or_account_id" class="form-label">Account</label>
                                        <select class="form-select form-select-sm" id="or_account_id"
                                            name="or_account_id" required>
                                            <option value="">Select Account</option>
                                            <!-- Populate with account options -->

                                        </select>
                                    </div>
                                    <!-- payment_method -->
                                    <div class="col-md-3 invoice-details">
                                        <label for="payment_method" class="form-label">Payment Method</label>
                                        <select class="form-select form-select-sm" id="payment_method"
                                            name="payment_method" required>
                                            <option value="">Select Payment Method</option>
                                            <?php foreach ($payment_methods as $payment_method): ?>
                                                <option value="<?= $payment_method->payment_method_name ?>">
                                                    <?= $payment_method->payment_method_name ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <!-- check_no -->
                                    <div class="col-md-3 invoice-details">
                                        <label for="check_no" class="form-label">Check No</label>
                                        <input type="text" class="form-control form-control-sm" id="check_no"
                                            name="check_no">
                                    </div>
                                    <!-- so_no -->
                                    <div class="col-md-3 invoice-details">
                                        <label for="so_no" class="form-label">J.O No.</label>
                                        <input type="text" class="form-control form-control-sm" id="so_no" name="so_no">
                                    </div>
                                    <!-- memo -->
                                    <div class="col-md-6 invoice-details">
                                        <label for="memo" class="form-label">Memo</label>
                                        <input type="text" class="form-control form-control-sm" id="memo" name="memo">
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
                                        <input type="text" class="form-control-plaintext text-end" id="gross_amount"
                                            name="gross_amount" value="0.00" readonly>
                                    </div>
                                </div>
                                <!-- total_discount_amount -->
                                <!-- discount_account_id -->
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
                                <!-- net_amount_due -->
                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Net Amount:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end" id="net_amount_due"
                                            name="net_amount_due" value="0.00" readonly>
                                    </div>
                                </div>
                                <!-- total_vat_amount -->
                                <div class="row">
                                    <label class="col-sm-6 col-form-label">VAT:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end" id="total_vat_amount"
                                            name="total_vat_amount" value="0.00" readonly>
                                    </div>
                                </div>
                                <!-- vatable_amount -->
                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Vatable 12%:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end" id="vatable_amount"
                                            name="vatable_amount" value="0.00" readonly>
                                    </div>
                                </div>
                                <!-- zero_rated_amount -->
                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Zero-rated:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end"
                                            id="zero_rated_amount" name="zero_rated_amount" value="0.00" readonly>
                                    </div>
                                </div>
                                <!-- vat_exempt_amount -->
                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Vat-Exempt:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end"
                                            id="vat_exempt_amount" name="vat_exempt_amount" value="0.00" readonly>
                                    </div>
                                </div>
                                <!-- tax_withheld_percentage -->
                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Tax Withheld (%):</label>
                                    <div class="col-sm-6">
                                        <select class="form-select form-select-sm" id="tax_withheld_percentage"
                                            name="tax_withheld_percentage">

                                            <?php foreach ($wtaxes as $wtax): ?>
                                                <option value="<?= $wtax->id ?>" data-rate="<?= $wtax->wtax_rate ?>"
                                                    data-account-id="<?= $wtax->wtax_account_id ?>"
                                                    <?= strpos($wtax->wtax_name, 'N/A') !== false ? 'selected' : '' ?>>
                                                    <?= $wtax->wtax_name ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <!-- tax_withheld_amount -->
                                <!-- tax_withheld_account_id -->
                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Tax Withheld Amount:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end"
                                            id="tax_withheld_amount" name="tax_withheld_amount" value="0.00" readonly>
                                        <input type="hidden" class="form-control" name="tax_withheld_account_id"
                                            id="tax_withheld_account_id">
                                    </div>
                                </div>
                                <!-- total_amount_due -->
                                <div class="row">
                                    <label class="col-sm-6 col-form-label fw-bold">Total Amount Due:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end fw-bold"
                                            id="total_amount_due" name="total_amount_due" value="0.00" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex">
                                <button type="button" id="saveDraftBtn" class="btn btn-secondary me-2">Save as
                                    Draft</button>
                                <button type="submit" class="btn btn-info me-2">Save and Print</button>
                                <button type="reset" class="btn btn-danger">Clear</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card" style="border: 1px solid rgb(4, 47, 48);">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Items / Services</h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="styled-table" id="itemTable">
                                        <thead class="bg-light" style="font-size: 15px;">
                                            <tr>
                                                <th style="width: 15%;">Item</th>
                                                <th style="width: 9%;">Description</th>
                                                <th style="width: 5%;">Quantity</th>
                                                <th class="text-right" style="width: 3%;">Unit</th>
                                                <th class="text-right" style="width: 11%;">Cost</th>
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

    <div id="loadingOverlay" style="display: none;">
        <div class="spinner"></div>
        <div class="message">Processing Payment</div>
    </div>
</div>

<?php require 'views/templates/footer.php' ?>


<iframe id="printFrame" style="display:none;"></iframe>


<!-- Bootstrap Modal for Adding New Customer -->
<?php include('layouts/add_customer.php'); ?>
<?php include('layouts/add_location.php'); ?>


<script>
    // Open the modal when the "Add New Customer" link is clicked
    document.getElementById("addNewCustomerLink").addEventListener("click", function() {
        const addCustomerModal = new bootstrap.Modal(document.getElementById("addCustomerModal"));
        addCustomerModal.show();
    });


    // Handle the customer addition form submission
    document.getElementById("addCustomerSubmit").addEventListener("click", function() {
        const form = document.getElementById("addCustomerForm");
        const formData = new FormData(form);

        // Set action to direct_add
        formData.set("action", "direct_add");

        fetch("api/masterlist/direct_add_customer.php", {
                method: "POST",
                body: formData,
            })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert("Customer added successfully");

                    // Add the new customer to the dropdown
                    const customerSelect = document.getElementById("customer_id");
                    const newOption = document.createElement("option");
                    newOption.value = data.customer.id;
                    newOption.textContent = data.customer.customer_name;
                    newOption.selected = true; // Automatically select the new customer
                    customerSelect.appendChild(newOption);

                    // Close the modal
                    const addCustomerModal = bootstrap.Modal.getInstance(document.getElementById("addCustomerModal"));
                    addCustomerModal.hide();

                    // Remove all modal backdrops
                    document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());

                    // Reset the form
                    form.reset();
                } else {
                    alert("Failed to add customer: " + data.message);
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("An error occurred while adding the customer.");
            });
    });


    // Open the modal when the "Add New Location" link is clicked
    document.getElementById("addNewLocationLink").addEventListener("click", function() {
        const addLocationModal = new bootstrap.Modal(document.getElementById("addLocationModal"));
        addLocationModal.show();
    });

    // Handle the location addition form submission
    document.getElementById("addLocationSubmit").addEventListener("click", function() {
        const form = document.getElementById("addLocationForm");
        const formData = new FormData(form);

        // Set action to direct_add
        formData.set("action", "direct_add");

        fetch("api/masterlist/direct_add_location.php", {
                method: "POST",
                body: formData,
            })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    alert("Location added successfully");

                    // Close the modal
                    const addLocationModal = bootstrap.Modal.getInstance(document.getElementById("addLocationModal"));
                    addLocationModal.hide();

                    // Remove all modal backdrops
                    document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());

                    // Reset the form
                    form.reset();
                } else {
                    alert("Failed to add location: " + data.message);
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("An error occurred while adding the location.");
            });
    });
</script>



<script>
    document.getElementById('cash_sales').addEventListener('change', function() {
        var labelText = this.checked ? "Cash Sales - Sales Receipt" : "Sales Invoice";
        document.getElementById('cash_sales_text').innerHTML = '&nbsp;&nbsp;' + labelText;
    });

    document.getElementById('officialReceiptFrom').addEventListener('submit', function() {
        const selectElement = document.getElementById('customer_id');
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const customerName = selectedOption.getAttribute('data-customer-name');

        document.getElementById('customer_name_hidden').value = customerName;
        console.log(customerName);
    });

    document.addEventListener("DOMContentLoaded", function() {
        var cashSalesSwitch = document.getElementById('cash_sales');
        var invoiceAccountSelect = document.getElementById('or_account_id');
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

<script>
    $(document).ready(function() {

        $('button[type="reset"]').on('click', function(e) {
            e.preventDefault(); // Prevent default reset behavior

            // Clear all input fields
            $('input').val('');

            // Reset all select elements to their default option
            $('select').each(function() {
                $(this).val($(this).find("option:first").val()).trigger('change');
            });

            // Clear the item table
            $('#itemTableBody').empty();

            // Reset all Select2 dropdowns
            $('.select2').val(null).trigger('change');

            // Reset summary section
            $('#gross_amount, #total_discount_amount, #net_amount_due, #total_vat_amount, #vatable_amount, #zero_rated_amount, #vat_exempt_amount, #tax_withheld_amount, #total_amount_due').val('0.00');

            // Clear hidden inputs
            $('#item_data, #customer_name_hidden, #discount_account_id, #tax_withheld_account_id').val('');

            // Reset date input to current date
            $('#invoice_date').val(new Date().toISOString().split('T')[0]);

            // Optionally, you can add a confirmation message
            Swal.fire({
                icon: 'success',
                title: 'Cleared',
                text: 'All fields have been reset.',
                timer: 1800,
                showConfirmButton: false
            });
        });

        $('#saveDraftBtn').click(function(e) {
            e.preventDefault();
            saveDraft();
        });

        function saveDraft() {
            const items = gatherTableItems();

            // Check if there are any items
            if (items === false || items.length === 0) {
                if (items === false) return;
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please add items first'
                });
                return;
            }

            $('#item_data').val(JSON.stringify(items));

            // Get unique discount account IDs and output VAT IDs
            const discountAccountIds = getUniqueDiscountAccountIds();
            const outputVatIds = getUniqueOutputVatIds();

            // Prepare the form data
            const formData = new FormData($('#officialReceiptFrom')[0]);
            formData.append('action', 'save_draft');
            formData.append('discount_account_ids', discountAccountIds.join(','));
            formData.append('output_vat_ids', outputVatIds.join(','));

            // Show the loading overlay
            document.getElementById('loadingOverlay').style.display = 'flex';

            // Use AJAX to submit the form
            $.ajax({
                url: 'api/or_payment_controller.php',
                type: 'POST',
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    document.getElementById('loadingOverlay').style.display = 'none';

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Invoice saved as draft successfully!',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error saving draft: ' + (response.message || 'Unknown error')
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
                        text: 'An error occurred while saving the draft: ' + textStatus
                    });
                }
            });
        }


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


        $('#or_account_id').select2({
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

        // const itemDropdownOptions = initDropdowns(<?php echo json_encode($products); ?>,
        //     product => `<option value="${product.id}"
        //             data-item-type="${product.item_type}"
        //             data-item-name="${product.item_name}"
        //             data-description="${product.item_sales_description}"
        //             data-uom="${product.uom_name}"
        //             data-cost-price="${product.item_cost_price}"
        //             data-cogs-account-id="${product.item_cogs_account_id}"
        //             data-income-account-id="${product.item_income_account_id}"
        //             data-asset-account-id="${product.item_asset_account_id}"
        //               ${product.item_quantity === 0 && product.item_type !== "Service" ? 'disabled' : ''}>
        //           ${product.item_name} ${product.item_quantity > 0 ? `(${product.item_quantity})` : (product.item_type === "Service" ? '' : '(no stock)')}
        //         </option>`
        // );

        const itemDropdownOptions = initDropdowns(<?php echo json_encode($products); ?>,
            product => `<option value="${product.id}"
                    data-item-type="${product.item_type}"
                    data-item-name="${product.item_name}"
                    data-description="${product.item_sales_description}"
                    data-uom="${product.uom_name}"
                    data-cost-price="${product.item_cost_price}"
                    data-cogs-account-id="${product.item_cogs_account_id}"
                    data-income-account-id="${product.item_income_account_id}"
                    data-asset-account-id="${product.item_asset_account_id}">
                    ${product.item_name} ${product.item_quantity > 0 ? `(${product.item_quantity})` : (product.item_type === "Service" ? '' : '(no stock)')}
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
                    <input type="hidden" class="item-type" name="item_type[]" value="">
                    <input type="hidden" class="item-name" name="item_name[]" value="">
                    <input type="hidden" class="item-cost-price" name="cost_price[]" value="">
                    <input type="hidden" class="item-cogs-account-id" name="item_cogs_account_id[]" value="">
                    <input type="hidden" class="item-income-account-id" name="item_income_account_id[]" value="">
                    <input type="hidden" class="item-asset-account-id" name="item_asset_account_id[]" value="">
                    <td><textarea class="form-control form-control-sm description-field" name="description[]" rows="1"></textarea></td>
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
                totalAmount: 0,
                totalDiscountAmount: 0,
                totalNetAmountBeforeTax: 0,
                totalInputVatAmount: 0,
                vatableAmount: 0,
                zeroRatedAmount: 0,
                vatExemptAmount: 0,
                notApplicable: 0,
                nonVatable: 0
            };

            $('.amount, .discount_amount, .net_amount_before_sales_tax, .sales_tax_amount, .net_amount').each(function() {
                const value = parseFloat($(this).val()) || 0;
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
                    if (inputVatName === '12%') {
                        totals.vatableAmount += value;
                    } else if (inputVatName === 'E') {
                        totals.vatExemptAmount += value;
                    } else if (inputVatName === 'Z') {
                        totals.zeroRatedAmount += value;
                    } else if (inputVatName === 'NA' || inputVatName === 'NV') {
                        // For 'NA' and 'NV', the value is calculated but not added to any category
                        // No operation needed, just skip adding to any total
                    } else {
                        // Fallback: if no VAT type is selected, add to vatable by default
                        totals.vatableAmount += value;
                    }
                }
            });

            const taxableBase = totals.vatableAmount + totals.zeroRatedAmount + totals.vatExemptAmount;

            $("#gross_amount").val(formatNumber(totals.totalAmount)).data('raw-value', totals.totalAmount);
            $("#total_discount_amount").val(formatNumber(totals.totalDiscountAmount)).data('raw-value', totals.totalDiscountAmount);
            $("#net_amount_due").val(formatNumber(totals.totalNetAmountBeforeTax)).data('raw-value', totals.totalNetAmountBeforeTax);
            $("#total_vat_amount").val(formatNumber(totals.totalInputVatAmount)).data('raw-value', totals.totalInputVatAmount);
            $("#vatable_amount").val(formatNumber(totals.vatableAmount)).data('raw-value', totals.vatableAmount);
            $("#zero_rated_amount").val(formatNumber(totals.zeroRatedAmount)).data('raw-value', totals.zeroRatedAmount);
            $("#vat_exempt_amount").val(formatNumber(totals.vatExemptAmount)).data('raw-value', totals.vatExemptAmount);

            const selectedTaxWithheld = $("#tax_withheld_percentage option:selected");
            const taxWithheldPercentage = parseFloat(selectedTaxWithheld.data('rate')) || 0;
            const taxWithheldAccountId = selectedTaxWithheld.data('account-id') || '';

            const taxWithheldAmount = (taxWithheldPercentage / 100) * taxableBase;

            $("#tax_withheld_amount").val(formatNumber(taxWithheldAmount)).data('raw-value', taxWithheldAmount);
            $("#tax_withheld_account_id").val(taxWithheldAccountId);

            // Include notApplicable in the subtotal calculation
            const subtotal = totals.totalInputVatAmount + taxableBase + totals.notApplicable + totals.nonVatable;
            const totalAmountDue = subtotal - taxWithheldAmount;

            $("#total_amount_due").val(formatNumber(totalAmountDue)).data('raw-value', totalAmountDue);
        }

        function formatNumber(value) {
            return parseFloat(value).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        // REMOVE ITEM
        $(document).on('click', '.removeRow', function() {
            $(this).closest('tr').remove();
            calculateRowValues($(this).closest('tr'));
            calculateTotalAmount();
        });

        // Event listener for tax withheld percentage change
        $('#tax_withheld_percentage').on('change', function() {
            calculateTotalAmount();
        });

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

        // Gather table items function (unchanged)
        function gatherTableItems() {
            const items = [];
            let hasEmptyItem = false;
            let hasEmptyQuantity = false;
            let hasEmptySellingPrice = false;
            let firstEmptyItemRow;
            let firstEmptyQuantityRow;
            let firstEmptySellingPriceRow;

            $('#itemTableBody tr').each(function(index) {
                const item_id = $(this).find('select[name="item_id[]"]').val();
                const description = $(this).find('textarea[name="description[]"]').val(); // Get description value
                const quantity = $(this).find('input[name="quantity[]"]').val();
                const cost = $(this).find('input[name="cost[]"]').val();

                // Check if item_id or quantity is empty
                if (!item_id) {
                    hasEmptyItem = true;
                    if (!firstEmptyItemRow) {
                        firstEmptyItemRow = $(this); // Store the first row with empty item_id
                    }
                    return true; // Continue to the next row
                }

                if (!quantity) {
                    hasEmptyQuantity = true;
                    if (!firstEmptyQuantityRow) {
                        firstEmptyQuantityRow = $(this); // Store the first row with empty quantity
                    }
                    return true; // Continue to the next row
                }

                if (!cost) {
                    hasEmptySellingPrice = true;
                    if (!firstEmptySellingPriceRow) {
                        firstEmptySellingPriceRow = $(this); // Store the first row with empty quantity
                    }
                    return true; // Continue to the next row
                }

                const item = {
                    item_id: $(this).find('select[name="item_id[]"]').val(),
                    item_type: $(this).find('.item-type').val(),
                    item_name: $(this).find('.item-name').val(),
                    description: description, // Add description to item object
                    quantity: quantity,
                    cost: cost,
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
                console.log(item);
                items.push(item);
            });

            // Show warnings based on which validation failed
            if (hasEmptyItem) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please select an item.'
                }).then(() => {
                    // Highlight the first row with an empty item
                    firstEmptyItemRow.find('select[name="item_id[]"]').focus().css('border', '2px solid red');
                });
                return false;
            }

            if (hasEmptyQuantity) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please enter a quantity for every item.'
                }).then(() => {
                    // Highlight the first row with an empty quantity
                    firstEmptyQuantityRow.find('input[name="quantity[]"]').focus().css('border', '2px solid red');
                });
                return false;
            }

            if (hasEmptySellingPrice) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please enter a selling price for every item.'
                }).then(() => {
                    // Highlight the first row with an empty quantity
                    firstEmptySellingPriceRow.find('input[name="cost[]"]').focus().css('border', '2px solid red');
                });
                return false;
            }


            return items;
        }

        // SUBMIT TRIGGER
        $('#officialReceiptFrom').submit(function(event) {
            event.preventDefault();
            const items = gatherTableItems();

            if (items === false || items.length === 0) {
                if (items === false) return;
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please add items first'
                });
                return;
            }

            const getNumericValue = (selector) => {
                const value = $(selector).data('raw-value') || unformatNumber($(selector).val());
                return Number(value.toFixed(2));
            };

            // Use raw values for form submission
            $('#gross_amount').val($('#gross_amount').data('raw-value'));
            $('#total_discount_amount').val($('#total_discount_amount').data('raw-value'));
            $('#net_amount_due').val($('#net_amount_due').data('raw-value'));
            $('#total_vat_amount').val($('#total_vat_amount').data('raw-value'));
            $('#vatable_amount').val($('#vatable_amount').data('raw-value'));
            $('#zero_rated_amount').val($('#zero_rated_amount').data('raw-value'));
            $('#vat_exempt_amount').val($('#vat_exempt_amount').data('raw-value'));
            $('#tax_withheld_amount').val($('#tax_withheld_amount').data('raw-value'));
            $('#total_amount_due').val($('#total_amount_due').data('raw-value'));

            $('#item_data').val(JSON.stringify(items));

            // Get unique discount account IDs and output VAT IDs
            const discountAccountIds = getUniqueDiscountAccountIds();
            const outputVatIds = getUniqueOutputVatIds();

            // Add hidden fields for discount_account_ids and output_vat_ids
            $(this).append(`<input type="hidden" name="discount_account_ids" value="${discountAccountIds.join(',')}">`);
            $(this).append(`<input type="hidden" name="output_vat_ids" value="${outputVatIds.join(',')}">`);

            // Show the loading overlay
            document.getElementById('loadingOverlay').style.display = 'flex';
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
                success: function(response) {
                    document.getElementById('loadingOverlay').style.display = 'none';

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Official Receipt Submitted!',
                            showCancelButton: true,
                            confirmButtonText: 'Print',
                            cancelButtonText: 'Save as PDF'
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
                            text: 'Error saving receipt: ' + (response.message || 'Unknown error')
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
        });

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
                success: function(response) {
                    if (response.success) {
                        console.log('Print status updated, now printing receipt:', id);

                        // Construct the print content URL
                        const printContentUrl = `print_or?action=print&id=${id}`;

                        // Open a new window with the print content
                        const printWindow = window.open(printContentUrl, 'PrintWindow', 'width=1000,height=700');

                        // Ensure the new window is ready to print
                        printWindow.onload = function() {
                            printWindow.focus(); // Focus the window to make it active
                            printWindow.print(); // Trigger the print dialog
                        };

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update print status: ' + (response.message || 'Unknown error')
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
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
        const itemType = selectedOption.data('item-type');
        const itemName = selectedOption.data('item-name');
        const description = selectedOption.data('description');
        const uom = selectedOption.data('uom');
        const costPrice = selectedOption.data('cost-price');
        const cogsAccountId = selectedOption.data('cogs-account-id');
        const incomeAccountId = selectedOption.data('income-account-id');
        const assetAccountId = selectedOption.data('asset-account-id');

        const row = $(select).closest('tr');
        row.find('.item-name').val(itemName);
        row.find('.item-type').val(itemType);
        row.find('.description-field').val(description);
        row.find('.uom').val(uom);
        row.find('.item-cost-price').val(costPrice);
        row.find('.item-cogs-account-id').val(cogsAccountId);
        row.find('.item-income-account-id').val(incomeAccountId);
        row.find('.item-asset-account-id').val(assetAccountId);

        console.log("Item Name:" + itemName);
        console.log("Item Type:" + itemType);
        console.log("Cost Price:" + costPrice);
        console.log("COGS:" + cogsAccountId);
        console.log("INCOME:" + incomeAccountId);
        console.log("ASSET:" + assetAccountId);
    }
</script>