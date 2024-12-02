<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('credit_memo');
$accounts = ChartOfAccount::all();
$cost_centers = CostCenter::all();
$customers = Customer::all();
$wtaxes = WithholdingTax::all();
$discounts = Discount::all();
$input_vats = InputVat::all();
$output_vats = SalesTax::all();
$sales_taxes = SalesTax::all();
$locations = Location::all();
$terms = Term::all();


$newCreditNo = CreditMemo::getLastCreditNo();

$page = 'credit_memo'; // Set the variable corresponding to the current page
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
    <?php include 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3"><strong>Create Credit Memo</strong></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="credit_memo">Credit Memo</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Create Credit Memo</li>
                            </ol>
                        </nav>
                    </div>
                    <a href="credit_memo" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Credits List
                    </a>
                </div>
            </div>

            <form id="creditMemoForm" action="api/credit_controller.php?action=add" method="POST">
                <input type="hidden" name="action" value="add" />
                <input type="hidden" name="id" id="itemId" value="" />
                <input type="hidden" name="item_data" id="item_data" />

                <div class="row">
                    <div class="col-12 col-lg-8">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Credit Memo Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <!-- Credit Details Section -->
                                    <div class="col-12 mb-3">
                                        <h6 class="border-bottom pb-2">Customer Details</h6>
                                    </div>

                                    <div class="row">
                                        <!-- CREDIT NO -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="credit_no">Credit No</label>
                                                <input type="text" class="form-control form-control-sm" id="credit_no" name="credit_no" value="<?php echo $newCreditNo; ?>" readonly>
                                            </div>
                                        </div>

                                        <!-- DATE -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="credit_date">Date</label>
                                                <input type="date" class="form-control form-control-sm" id="credit_date" name="credit_date" value="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                        </div>

                                        <!-- ACCOUNT -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="credit_account_id">Account</label>
                                                <select class="form-control form-control-sm select2" id="credit_account_id" name="credit_account_id" required>
                                                    <option value="">Select Account</option>
                                                    <?php foreach ($accounts as $account): ?>
                                                        <?php if ($account->account_type == 'Accounts Receivable'): ?>
                                                            <option value="<?= $account->id ?>">
                                                                <?= $account->account_code ?> - <?= $account->account_description ?>
                                                            </option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <!-- CUSTOMER -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="customer_name" class="form-label">
                                                    Customer
                                                    <a href="#" id="addNewCustomerLink" class="ms-3 text-primary">| Add New</a>
                                                </label>
                                                <select class="form-control form-control-sm select2" id="customer_id" name="customer_id" required>
                                                    <option value="">Select Customer</option>
                                                    <?php foreach ($customers as $customer): ?>
                                                        <option value="<?= $customer->id ?>"><?= $customer->customer_name ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- MEMO -->
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="credit_memo">Memo</label>
                                                <input type="text" class="form-control form-control-sm" id="credit_memo" name="credit_memo" placeholder="Enter memo">
                                            </div>
                                        </div>

                                        <!-- LOCATION -->
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
                                        <input type="text" class="form-control-plaintext text-end" id="gross_amount"
                                            name="gross_amount" value="0.00" readonly>
                                    </div>
                                </div>

                                <!-- NET AMOUNT DUE -->
                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Net Amount:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end" id="net_amount_due"
                                            name="net_amount_due" value="0.00" readonly>
                                    </div>
                                </div>

                                <!-- VAT PERCENTAGE -->
                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Output VAT:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end"
                                            id="vat_percentage_amount" name="vat_percentage_amount" value="0.00"
                                            readonly>
                                        <input type="hidden" class="form-control" name="vat_account_id"
                                            id="vat_account_id">
                                    </div>
                                </div>

                                <!-- NET OF VAT -->
                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Taxable Amount:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end" id="net_of_vat"
                                            name="net_of_vat" value="0.00" readonly>
                                    </div>
                                </div>

                                <!-- TAX WITHHELD -->
                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Tax Withheld (%):</label>
                                    <div class="col-sm-6">
                                        <select class="form-select form-select-sm" id="tax_withheld_percentage" name="tax_withheld_percentage" required>
                                            <option value="" selected disabled>Select Tax Withholding</option>
                                            <?php foreach ($wtaxes as $wtax): ?>
                                                <option value="<?= $wtax->id ?>"
                                                    data-rate="<?= $wtax->wtax_rate ?>"
                                                    data-account-id="<?= $wtax->id ?>"
                                                    <?= strpos($wtax->wtax_name, 'N/A') !== false ? 'selected' : '' ?>>
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
                                            id="tax_withheld_amount" name="tax_withheld_amount" value="0.00" readonly>
                                        <input type="hidden" class="form-control" name="tax_withheld_account_id"
                                            id="tax_withheld_account_id">
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
                                <button type="button" id="saveDraftBtn" class="btn btn-secondary me-2">Save as
                                    Draft</button>
                                <button type="submit" class="btn btn-info me-2">Save and Print</button>
                                <button type="reset" class="btn btn-danger">Clear</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">Credit Memo Items</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover" id="itemTable">
                                    <thead class="bg-light" style="font-size: 12px;">
                                        <tr>
                                            <th style="width: 15%;">ACCOUNT</th>
                                            <th style="width: 15%;">COST CENTER</th>
                                            <th style="width: 9%;">DESC</th>
                                            <th style="width: 8%;">GROSS</th>
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
                                <i class="fas fa-plus"></i> Add Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

</div>

<div id="loadingOverlay" style="display: none;">
    <div class="spinner"></div>
    <div class="message">Processing Credit Memo</div>
</div>

<?php require 'views/templates/footer.php' ?>

<iframe id="printFrame" style="display:none;"></iframe>

<!-- Bootstrap Modal for Adding New Customer and New Location -->
<?php include('layouts/add_customer.php'); ?>
<?php
require_once(__DIR__ . '/../layouts/add_location.php');
require_once(__DIR__ . '/../layouts/add_customer.php');
?>

// modal script
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

                    // Reload the page after successful addition
                    location.reload();
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

                    // Reload the page after successful addition
                    location.reload();
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
    $(document).ready(function() {
        // Constants and cached DOM elements
        const $itemTableBody = $('#itemTableBody');
        const $addItemBtn = $('#addItemBtn');
        const $creditMemoForm = $('#creditMemoForm');
        const $taxWithheldPercentage = $('#tax_withheld_percentage');
        const $loadingOverlay = $('#loadingOverlay');
        const $clearBtn = $('button[type="reset"]');

        // Initialize Select2 for customer and credit account
        $('#customer_id, #credit_account, #credit_account_id').select2({
            theme: 'classic',
            width: '100%'
        });

        $('#saveDraftBtn').click(function(e) {
            e.preventDefault();
            saveDraft();
        });

        function saveDraft() {
            const items = gatherTableItems();


            if (items === false || items.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please add Gross for account first'
                });
                return;
            }

            $('#item_data').val(JSON.stringify(items));

            const formData = new FormData($('#creditMemoForm')[0]);
            formData.append('action', 'save_draft');

            formData.append('credit_account_id', $('#credit_account_id').val());
            const selectedWtaxId = $('#tax_withheld_percentage').val();
            formData.append('tax_withheld_percentage', selectedWtaxId);

            $('#loadingOverlay').fadeIn();

            $.ajax({
                url: 'api/credit_controller.php',
                type: 'POST',
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#loadingOverlay').fadeOut();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Credit memo saved as draft successfully!',
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
                    $('#loadingOverlay').fadeOut();
                    console.error('AJAX error:', textStatus, errorThrown);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving the draft: ' + textStatus
                    });
                }
            });
        }
        document.getElementById('tax_withheld_percentage').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var accountId = selectedOption.getAttribute('data-account-id');
            var wtaxRate = selectedOption.getAttribute('data-rate');
            console.log('Tax Withheld Account ID:', accountId);
            console.log('Tax Withheld Rate:', wtaxRate);
            document.getElementById('tax_withheld_account_id').value = accountId;
            // You may want to update other fields or calculations based on the new wtax rate here
            calculateNetAmount(); // Recalculate amounts if needed
        });


        // Event listeners
        $addItemBtn.on('click', addRow);


        $itemTableBody.on('click', '.removeRow', function() {
            $(this).closest('tr').remove();
            calculateNetAmount();
        });

        $itemTableBody.on('input', '.amount', calculateNetAmount);
        $itemTableBody.on('change', '.vat_percentage', calculateNetAmount);
        $taxWithheldPercentage.on('change', calculateNetAmount);
        $clearBtn.on('click', clearForm);


        $creditMemoForm.on('submit', function(event) {
            event.preventDefault();

            if (!validateForm()) {
                return;
            }

            const items = gatherTableItems();

            if (items === false || items.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please add valid Account first'
                });
                return;
            }

            $('#item_data').val(JSON.stringify(items));
            console.log('Set item_data value:', $('#item_data').val());

            // Show loading overlay
            $loadingOverlay.fadeIn();

            // Submit the form data via AJAX
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
                success: function(response) {
                    // Hide loading overlay
                    $loadingOverlay.fadeOut();

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Credit submitted successfully!',
                            showCancelButton: false,
                            confirmButtonText: 'Print'
                        }).then((result) => {
                            if (result.isConfirmed && response.id) {
                                printCredit(response.id, 1);
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error saving credit: ' + (response.message || 'Unknown error')
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Hide loading overlay
                    $loadingOverlay.fadeOut();

                    console.error('AJAX error:', textStatus, errorThrown);
                    console.log('Response Text:', jqXHR.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving the credit: ' + textStatus
                    });
                }
            });
        });

        function validateForm() {
            let isValid = true;
            let firstInvalidField = null;

            // Validate customer
            if (!$('#customer_id').val()) {
                isValid = false;
                firstInvalidField = $('#customer_id');
            }

            // Validate credit account
            if (!$('#credit_account_id').val()) {
                isValid = false;
                firstInvalidField = firstInvalidField || $('#credit_account_id');
            }

            // Validate date
            if (!$('#credit_date').val()) {
                isValid = false;
                firstInvalidField = firstInvalidField || $('#credit_date');
            }

            // Validate items table
            if ($('#itemTableBody tr').length === 0) {
                isValid = false;
                Swal.fire({
                    icon: 'warning',
                    title: 'No Items',
                    text: 'Please add at least one Account to the credit memo.'
                });
                return false;
            }

            $('.account-dropdown').each(function() {
                if (!$(this).val()) {
                    isValid = false;
                    firstInvalidField = firstInvalidField || $(this);
                }
            });

            if (!isValid) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Incomplete Form',
                    text: 'Please fill in all required fields.'
                });
                if (firstInvalidField) {
                    firstInvalidField.focus();
                }
            }

            return isValid;
        }

        function clearForm(e) {
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
            $('#gross_amount, #net_amount_due, #vat_percentage_amount, #net_of_vat, #tax_withheld_amount, #total_amount_due').val('0.00');

            // Clear hidden inputs
            $('#item_data').val('');

            // Reset date input to current date
            $('#credit_date').val(new Date().toISOString().split('T')[0]);

            Swal.fire({
                icon: 'success',
                title: 'Cleared',
                text: 'All fields have been reset.',
                timer: 1800,
                showConfirmButton: false
            });
        }

        // Populate dropdowns with accounts and VAT options
        const accounts = <?php echo json_encode($accounts); ?>;
        const vatOptions = <?php echo json_encode($output_vats); ?>;

        const accountDropdownOptions = accounts.map(account =>
            `<option value="${account.id}">${account.account_code}-${account.account_description}</option>`
        ).join('');


        const vatDropdownOptions = vatOptions.map(vat =>
            `<option value="${vat.sales_tax_rate}" data-account-id="${vat.sales_tax_account_id}">${vat.sales_tax_name}</option>`
        ).join('');

        // Populate dropdowns with cost centers from PHP
        const costCenterOptions = <?php echo json_encode($cost_centers); ?>;
        let costCenterDropdownOptions = '';
        costCenterOptions.forEach(function(cost) {
            costCenterDropdownOptions += `<option value="${cost.id}">${cost.code} - ${cost.particular}</option>`;
        });

        // Function to add a new row
        function addRow() {
            const newRow = `
            <tr>
                <td><select class="form-select form-select-sm account-dropdown select2" name="account_id[]" required>${accountDropdownOptions}</select></td>
                <td><select class="form-control form-control-sm cost-dropdown select2" name="cost_center_id[]">${costCenterDropdownOptions}</select></td>
                <td><input type="text" class="form-control form-control-sm memo" name="memo[]" placeholder="Description"></td>
                <td><input type="number" step="0.01" class="form-control form-control-sm amount" name="amount[]" placeholder="0.00"></td>
                <td><input type="text" class="form-control form-control-sm net-amount-before-vat" name="net_amount_before_vat[]" readonly></td>
                <td><input type="text" class="form-control form-control-sm net-amount" name="net_amount[]" readonly></td>
                <td><select class="form-select form-select-sm vat_percentage select2" name="vat_percentage[]">${vatDropdownOptions}</select></td>
                <td><input type="text" class="form-control form-control-sm input-vat" name="vat_amount[]" readonly></td>
                <td><button type="button" class="btn btn-danger btn-sm removeRow"><i class="fas fa-trash"></i></button></td>
            </tr>`;

            // Append the new row to the table body
            $itemTableBody.append(newRow);

            // Initialize Select2 on the new dropdowns
            $itemTableBody.find('.select2').select2({
                width: '100%',
                theme: 'classic' // Use this if you're using Bootstrap 4
            });

            console.log('Added a new row');
        }

        function calculateNetAmount() {
            let totalAmount = 0;
            let totalNetAmount = 0;
            let totalVat = 0;
            let totalTaxableAmount = 0;

            $('.amount').each(function() {
                const $row = $(this).closest('tr');
                const amount = parseFloat($(this).val()) || 0;
                const vatPercentage = parseFloat($row.find('.vat_percentage').val()) || 0;

                totalAmount += amount;

                const vatPercentageAmount = vatPercentage / 100;
                const netAmount = amount / (1 + vatPercentageAmount);
                const vatAmount = amount - netAmount;

                $row.find('.net-amount-before-vat').val(amount.toFixed(2));
                $row.find('.net-amount').val(netAmount.toFixed(2));
                $row.find('.input-vat').val(vatAmount.toFixed(2));

                totalNetAmount += amount;
                totalVat += vatAmount;
                totalTaxableAmount += netAmount;
            });

            updateTotals(totalAmount, totalNetAmount, totalVat, totalTaxableAmount);
        }

        function updateTotals(totalAmount, totalNetAmount, totalVat, totalTaxableAmount) {
            $("#gross_amount").val(totalAmount.toFixed(2));
            $("#net_amount_due").val(totalNetAmount.toFixed(2));
            $("#vat_percentage_amount").val(totalVat.toFixed(2));
            $("#net_of_vat").val(totalTaxableAmount.toFixed(2));

            const selectedTaxWithheld = $("#tax_withheld_percentage option:selected");
            const taxWithheldPercentage = parseFloat(selectedTaxWithheld.data('rate')) || 0;

            const taxWithheldAmount = (taxWithheldPercentage / 100) * totalTaxableAmount;
            $("#tax_withheld_amount").val(taxWithheldAmount.toFixed(2));

            const totalAmountDue = totalNetAmount - taxWithheldAmount;
            $("#total_amount_due").val(totalAmountDue.toFixed(2));

            console.log('Updated totals:', {
                totalAmount,
                totalNetAmount,
                totalVat,
                totalTaxableAmount,
                taxWithheldAmount,
                totalAmountDue
            });
        }

        function gatherTableItems() {
            const items = [];
            let hasEmptyAmount = false;
            let hasInvalidAccount = false;
            let firstEmptyAmountRow;
            let firstInvalidAccountRow;

            $('#itemTableBody tr').each(function() {
                const $row = $(this);
                const amount = parseFloat($row.find('.amount').val()) || 0;
                const accountId = $row.find('.account-dropdown').val();

                // Check if the account dropdown is not selected or invalid
                if (!accountId || accountId === '') {
                    hasInvalidAccount = true;
                    if (!firstInvalidAccountRow) {
                        firstInvalidAccountRow = $row; // Store the first row with an invalid account
                    }
                }

                // Check if the amount is empty or zero
                if (amount <= 0) {
                    hasEmptyAmount = true;
                    if (!firstEmptyAmountRow) {
                        firstEmptyAmountRow = $row; // Store the first row with empty/zero amount
                    }
                }

                // If no issues, gather the item data
                if (!hasInvalidAccount && !hasEmptyAmount) {
                    const item = {
                        account_id: +$(this).find('.account-dropdown').val() || 0,
                        cost_center_id: $(this).find('.cost-dropdown').val() || '',
                        memo: $(this).find('.memo').val(),
                        amount: parseFloat($(this).find('.amount').val()) || 0,
                        net_amount_before_vat: parseFloat($(this).find('.net-amount-before-vat').val()) || 0,
                        net_amount: parseFloat($(this).find('.net-amount').val()) || 0,
                        vat_percentage: parseFloat($(this).find('.vat_percentage').val()) || 0,
                        sales_tax: parseFloat($(this).find('.input-vat').val()) || 0,
                        sales_tax_account_id: $(this).find('.vat_percentage option:selected').data('account-id')
                    };
                    items.push(item);
                }
            });

            // Handle validation errors for amount or account dropdowns
            if (hasEmptyAmount) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please enter valid amounts in all rows.'
                });
                firstEmptyAmountRow.find('.amount').focus(); // Focus on the first invalid amount field
                return false;
            }

            if (hasInvalidAccount) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please select a valid account for all rows.'
                });
                firstInvalidAccountRow.find('.account-dropdown').focus(); // Focus on the first invalid account dropdown
                return false;
            }

            return items;
        }


        function printCredit(id, printStatus) {
            // First, update the print status
            $.ajax({
                url: 'api/credit_controller.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'update_print_status',
                    id: id,
                    print_status: printStatus
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Print status updated, now printing Credit:', id);
                        // Open the print_credit.php file in a new window
                        const printFrame = document.getElementById('printFrame');
                        const printContentUrl = `print_credit?action=print&id=${id}`;

                        printFrame.src = printContentUrl;

                        printFrame.onload = function() {
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
</script>