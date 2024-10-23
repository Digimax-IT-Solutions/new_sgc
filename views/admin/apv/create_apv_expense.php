<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('accounts_payable_voucher');

$accounts = ChartOfAccount::all();
$vendors = Vendor::all();
$cost_centers = CostCenter::all();
$discounts = Discount::all();
$wtaxes = WithholdingTax::all();
$input_vats = InputVat::all();
$terms = Term::all();
$locations = Location::all();


$newAPVoucherNo = Apv::getLastApvNo();

?>

<?php include 'views/templates/header.php' ?>
<?php include 'views/templates/sidebar.php' ?>

<style>
    .select2-container--bootstrap4 .select2-dropdown--below {
        max-height: 85px;
        /* Adjust the height as needed */
        overflow-y: auto;
        /* Enable vertical scrolling */
    }

    .form-label {
        font-size: 0.675rem;
        margin-bottom: 0.25rem;
    }

    .card-body {
        font-size: 0.875rem;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    #itemTable {
        min-width: 2000px;
        /* Adjust this value based on your table's content */
        table-layout: fixed;
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
                        <h1 class="h3"><strong>Create New AP VOUCHER</strong></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="accounts_payable_voucher">APV</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Create New AP VOUCHER</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="accounts_payable_voucher" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to APV List
                        </a>
                    </div>
                </div>
            </div>

            <form id="apvForm" action="api/apv_controller.php?action=add" method="POST">
                <input type="hidden" name="action" id="modalAction" value="add" />
                <input type="hidden" name="id" id="itemId" value="" />
                <input type="hidden" name="item_data" id="item_data" />
                <input type="hidden" name="vendor_name" id="vendor_name" />

                <div class="row">
                    <div class="col-12 col-lg-8">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">APV Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">

                                    <div class="row">
                                        <div class="col-md-3 customer-details">
                                            <div class="form-group">
                                                <!-- AP Voucher No -->
                                                <label for="apv_no">AP Voucher No.</label>
                                                <input type="text" class="form-control form-control-sm" id="apv_no"
                                                    name="apv_no" value="<?php echo $newAPVoucherNo; ?>" readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row mt-3">
                                        <!-- SELECT APV ACCOUNT -->
                                        <div class="col-md-4 customer-details">
                                            <div class="form-group">
                                                <label for="account_id">A/P Account</label>
                                                <select class="form-control form-control-sm select2" id="account_id"
                                                    name="account_id" required>
                                                    <option value=""></option>
                                                    <?php foreach ($accounts as $account): ?>
                                                        <?php if ($account->account_type == 'Accounts Payable'): ?>
                                                            <option value="<?= $account->id ?>">
                                                                <?= $account->account_code ?> -
                                                                <?= $account->account_description ?>
                                                            </option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- SELECT VENDOR -->
                                        <div class="col-md-4 customer-details">
                                            <div class="form-group">
                                                <label for="vendor_id">Vendor<span class="text-muted"
                                                        id="payee_type_display"></span></label>
                                                <select class="form-control form-control-sm select2" id="vendor_id"
                                                    name="vendor_id" required>
                                                    <option value=""></option>
                                                    <?php foreach ($vendors as $vendor): ?>
                                                        <option value="<?= $vendor->id ?>" data-tin="<?= $vendor->tin ?>"
                                                            data-name="<?= $vendor->vendor_name ?>">
                                                            <?= $vendor->vendor_name ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- VENDOR TIN -->
                                        <div class="col-md-4 customer-details">
                                            <div class="form-group">
                                                <label for="account_id">T.I.N</label>
                                                <input type="text" class="form-control form-control-md" id="tin"
                                                    name="tin">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- APV INFORMATION -->
                                <div class="col-12 mt-3 mb-3">
                                    <h6 class="border-bottom pb-2"></h6>
                                </div>

                                <div class="row">
                                    <div class="col-md-3 order-details">
                                        <div class="form-group">
                                            <!-- PURCHASE ORDER NO -->
                                            <label for="ref_no">Reference Doc No.</label>
                                            <input type="text" class="form-control form-control-md" id="ref_no"
                                                name="ref_no" placeholder="Enter ref doc">
                                        </div>
                                    </div>

                                    <div class="col-md-3 order-details">
                                        <div class="form-group">
                                            <!-- PURCHASE ORDER NO -->
                                            <label for="po_no">PO No.</label>
                                            <input type="text" class="form-control form-control-md" id="po_no"
                                                name="po_no" placeholder="Enter PO No">
                                        </div>
                                    </div>

                                    <div class="col-md-3 order-details">
                                        <div class="form-group">
                                            <!-- PURCHASE ORDER NO -->
                                            <label for="rr_no">RR No.</label>
                                            <input type="text" class="form-control form-control-md" id="rr_no"
                                                name="rr_no" placeholder="Enter RR No">
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <!-- SELECT DATE -->
                                        <div class="form-group">
                                            <label for="apv_date">APV Date</label>
                                            <input type="date" class="form-control form-control-md" id="apv_date"
                                                name="apv_date" value="<?php echo date('Y-m-d'); ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="terms_id">Terms</label>
                                            <select class="form-select form-select-md" id="terms_id" name="terms_id">
                                                <option value="">Select Terms</option>
                                                <?php foreach ($terms as $term): ?>
                                                    <option value="<?= htmlspecialchars($term->id) ?>"
                                                        data-days="<?= htmlspecialchars($term->term_days_due) ?>">
                                                        <?= htmlspecialchars($term->term_name) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <!-- SELECT DATE -->
                                        <div class="form-group">
                                            <label for="apv_due_date">Due Date</label>
                                            <input type="date" class="form-control form-control-md" id="apv_due_date"
                                                name="apv_due_date" value="<?php echo date('Y-m-d'); ?>" required>
                                        </div>
                                    </div>

                                    <div class="row order-details mt-5">
                                        <div class="col-md-8">
                                            <!-- MEMO -->
                                            <div class="form-group">
                                                <label for="memo">Memo</label>
                                                <input type="text" class="form-control form-control-md" id="memo" name="memo">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <!-- LOCATION -->
                                            <div class="form-group">
                                                <label for="location" class="form-label">Location</label>
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
                    </div>

                    <!-- Summary Section -->
                    <div class="col-12 col-lg-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title">Summary</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-2">
                                    <!-- Placeholder for potential future features -->
                                </div>

                                <!-- GROSS AMOUNT -->
                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Gross Amount:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end" id="gross_amount"
                                            name="gross_amount" value="0.00" readonly>
                                    </div>
                                </div>

                                <!-- DISCOUNT -->
                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Discount:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end" id="discount_amount"
                                            name="discount_amount" value="0.00" readonly>
                                        <input type="text" class="form-control" name="discount_account_id"
                                            id="discount_account_id" hidden>
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
                                    <label class="col-sm-6 col-form-label">Input Vat:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end"
                                            id="vat_percentage_amount" name="vat_percentage_amount" value="0.00"
                                            readonly>
                                        <input type="text" class="form-control" name="vat_account_id"
                                            id="vat_account_id" hidden>
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
                                        <select class="form-select form-select-sm" id="tax_withheld_percentage"
                                            name="tax_withheld_percentage">
                                            <?php foreach ($wtaxes as $wtax): ?>
                                                <option value="<?= $wtax->id ?>" data-rate="<?= $wtax->wtax_rate ?>"
                                                    data-account-id="<?= $wtax->wtax_account_id ?>"
                                                    <?= strpos($wtax->wtax_name, 'V-N/A') !== false ? 'selected' : '' ?>>
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
                            <div class="card-footer d-flex">
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
                            <h5 class="card-title mb-0">APV Accounts</h5>

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
                                <i class="fas fa-plus"></i> Add Account
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>
    <div id="loadingOverlay" style="display: none;">
        <div class="spinner"></div>
        <div class="message">Processing APV</div>
    </div>
</div>



<?php require 'views/templates/footer.php' ?>

<iframe id="printFrame" style="display:none;"></iframe>

<script>
    document.getElementById('terms_id').addEventListener('change', function () {
        var selectedTerm = this.options[this.selectedIndex];
        var daysDue = parseInt(selectedTerm.getAttribute('data-days'), 10);

        var invoiceDate = document.getElementById('apv_date').value;

        if (invoiceDate && !isNaN(daysDue)) {
            var dueDate = new Date(invoiceDate);

            // Add the days due to the APV date
            dueDate.setDate(dueDate.getDate() + daysDue);

            // Format the date to yyyy-mm-dd
            var year = dueDate.getFullYear();
            var month = ('0' + (dueDate.getMonth() + 1)).slice(-2);
            var day = ('0' + dueDate.getDate()).slice(-2);

            // Set the formatted due date
            document.getElementById('apv_due_date').value = `${year}-${month}-${day}`;
        } else {
            console.log("Invalid Invoice Date or Days Due.");
        }
    });
</script>

<script>
    $(document).ready(function () {

        $('button[type="reset"]').on('click', function (e) {
            e.preventDefault(); // Prevent default reset behavior

            // Clear all input fields
            $('input').val('');

            // Reset all select elements to their default option
            $('select').each(function () {
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

        $('#saveDraftBtn').click(function (e) {
            e.preventDefault();
            saveDraft();
        });

        function saveDraft() {
            const items = gatherTableItems();

            // Check if there are any items
            if (items.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please add items before saving as draft'
                });
                return;
            }

            $('#item_data').val(JSON.stringify(items));

            // Prepare the form data
            const formData = new FormData($('#apvForm')[0]);
            formData.append('action', 'save_draft');

            // Show the loading overlay
            document.getElementById('loadingOverlay').style.display = 'flex';

            // Use AJAX to submit the form
            $.ajax({
                url: 'api/apv_controller.php',
                type: 'POST',
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    document.getElementById('loadingOverlay').style.display = 'none';

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'APV saved as draft successfully!',
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
                error: function (jqXHR, textStatus, errorThrown) {
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

        $('#vendor_id').on('select2:select', function (e) {
            var data = e.params.data;
            $('#vendor_name').val($(data.element).data('name'));
        });

        document.getElementById('tax_withheld_percentage').addEventListener('change', function () {
            var selectedOption = this.options[this.selectedIndex];
            var accountId = selectedOption.getAttribute('data-account-id');
            console.log(accountId);
            document.getElementById('tax_withheld_account_id').value = accountId;
        });

        $('#account_id').select2({
            theme: 'classic',
            width: '100%',
            placeholder: 'Select AP Account',
            allowClear: false,
            containerCssClass: 'select2--medium', // Add this line
            dropdownCssClass: 'select2--medium', // Add this line
        });



        $('#payee_type').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Payee Type',
            allowClear: false
        });

        $('#vendor_id').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Vendor',
            allowClear: false
        });
        $('#location').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Location',
            allowClear: false
        });

        // Populate dropdowns with accounts from PHP
        const accounts = <?php echo json_encode($accounts); ?>;
        let accountDropdownOptions = '';
        $.each(accounts, function (index, account) {
            accountDropdownOptions += `<option value="${account.id}">${account.account_description}</option>`;
        });

        // Populate dropdowns with discounts from PHP
        const discountOptions = <?php echo json_encode($discounts); ?>;
        let discountDropdownOptions = '';
        discountOptions.forEach(function (discount) {
            discountDropdownOptions += `<option value="${discount.discount_rate}" data-account-id="${discount.discount_account_id}">${discount.discount_name}</option>`;
        });

        // Populate dropdowns with VAT from PHP
        const vatOptions = <?php echo json_encode($input_vats); ?>;
        let vatDropdownOptions = '';
        vatOptions.forEach(function (vat) {
            vatDropdownOptions += `<option value="${vat.input_vat_rate}" data-account-id="${vat.input_vat_account_id}">${vat.input_vat_name}</option>`;
        });

        // Populate dropdowns with cost centers from PHP
        const costCenterOptions = <?php echo json_encode($cost_centers); ?>;
        let costCenterDropdownOptions = '';
        costCenterOptions.forEach(function (cost) {
            costCenterDropdownOptions += `<option value="${cost.id}">${cost.code} - ${cost.particular}</option>`;
        });

        function addRow() {
            const newRow = `
                <tr>
                    <td><select class="form-control form-control-sm account-dropdown select2" name="account_id[]" required>${accountDropdownOptions}</select></td>
                    <td><select class="form-control form-control-sm cost-dropdown select2" name="cost_center_id[]">${costCenterDropdownOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm memo" name="memo[]" placeholder="Enter Desc"></td>
                    <td><input type="text" class="form-control form-control-sm amount" name="amount[]" placeholder="Enter amount"></td>
                    <td><select class="form-control form-control-sm discount_percentage select2" name="discount_percentage[]">${discountDropdownOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm discount-amount" name="discount_amount[]" placeholder="" readonly></td>
                    <td><input type="text" class="form-control form-control-sm net-amount-before-vat" name="net_amount_before_vat[]" placeholder="" readonly></td>
                    <td><input type="text" class="form-control form-control-sm net-amount" name="net_amount[]" placeholder="" readonly></td>
                    <td><select class="form-control form-control-sm vat_percentage select2" name="vat_percentage[]">${vatDropdownOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm input-vat" name="vat_amount[]" placeholder="" readonly></td>
                    <td><button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-trash"></i></button></td>
                </tr>`;

            // Append the new row to the table body
            $('#itemTableBody').append(newRow);

            // Initialize Select2 on the new dropdowns
            $('#itemTableBody .select2').select2({
                width: '100%',
                theme: 'classic' // Use this if you're using Bootstrap 4
            });
        }

        $('#vendor_id').change(function () {
            var selectedVendor = $(this).find(':selected');
            var tin = selectedVendor.data('tin');
            $('#tin').val(tin);
        });

        // Function to calculate net amount
        function calculateNetAmount() {
            let totalAmount = 0;
            let totalDiscountAmount = 0;
            let totalNetAmount = 0;
            let totalVat = 0;
            let totalTaxableAmount = 0;
            let totalAmountDue = 0;

            $('.amount').each(function () {
                const amount = parseFloat($(this).val()) || 0;
                totalAmount += amount;
                const discountPercentage = parseFloat($(this).closest('tr').find('.discount_percentage').val()) || 0;
                const vatPercentage = parseFloat($(this).closest('tr').find('.vat_percentage').val()) || 0;

                const discountAmount = (discountPercentage / 100) * amount;
                $(this).closest('tr').find('.discount-amount').val(discountAmount.toFixed(2)); // Update discount amount field
                totalDiscountAmount += discountAmount;

                const netAmountBeforeVAT = amount - discountAmount;
                $(this).closest('tr').find('.net-amount-before-vat').val(netAmountBeforeVAT.toFixed(2)); // Update net amount before VAT field
                totalNetAmount += netAmountBeforeVAT;

                const vatPercentageAmount = vatPercentage / 100;
                const netAmount = netAmountBeforeVAT / (1 + vatPercentageAmount);
                $(this).closest('tr').find('.net-amount').val(netAmount.toFixed(2)); // Update net amount field

                const vatAmount = netAmountBeforeVAT - netAmount;
                $(this).closest('tr').find('.input-vat').val(vatAmount.toFixed(2)); // Update VAT amount field
                totalVat += vatAmount;

                totalTaxableAmount += netAmount;
            });

            // Update total fields
            $("#gross_amount").val(totalAmount.toFixed(2));
            $("#discount_amount").val(totalDiscountAmount.toFixed(2));
            $("#net_amount_due").val(totalNetAmount.toFixed(2));
            $("#vat_percentage_amount").val(totalVat.toFixed(2));
            $("#net_of_vat").val(totalTaxableAmount.toFixed(2));

            // Get the selected tax withheld option
            const selectedTaxWithheld = $("#tax_withheld_percentage option:selected");
            const taxWithheldPercentage = parseFloat(selectedTaxWithheld.data('rate')) || 0;
            const taxWithheldAmount = (taxWithheldPercentage / 100) * totalTaxableAmount;
            $("#tax_withheld_amount").val(taxWithheldAmount.toFixed(2));

            totalAmountDue = totalNetAmount - taxWithheldAmount;
            $("#total_amount_due").val(totalAmountDue.toFixed(2));
        }

        // Event listener for tax withheld percentage change
        $('#tax_withheld_percentage').on('change', function () {
            calculateNetAmount();
        });

        // Event listener for amount input
        $('#itemTableBody').on('input', '.amount', function () {
            calculateNetAmount();
        });

        // Event listener for discount or VAT change
        $('#itemTableBody').on('change', '.discount_percentage, .vat_percentage', function () {
            calculateNetAmount();
        });

        // Event listeners
        $('#addItemBtn').click(addRow);
        $(document).on('click', '.removeRow', function () {
            $(this).closest('tr').remove();
            calculateNetAmount();
        });

        // Gather table items and submit form
        function gatherTableItems() {
            const items = [];
            $('#itemTableBody tr').each(function (index) {
                const item = {
                    account_id: $(this).find('.account-dropdown').val(),
                    cost_center_id: $(this).find('.cost-dropdown').val(),
                    memo: $(this).find('.memo').val(),
                    amount: $(this).find('.amount').val(),
                    discount_percentage: $(this).find('.discount_percentage').val(),
                    discount_amount: $(this).find('.discount-amount').val(),
                    net_amount_before_vat: $(this).find('.net-amount-before-vat').val(),
                    net_amount: $(this).find('.net-amount').val(),
                    vat_percentage: $(this).find('.vat_percentage').val(),
                    input_vat: $(this).find('.input-vat').val(),
                    discount_account_id: $(this).find('.discount_percentage option:selected').data('account-id'),
                    input_vat_account_id: $(this).find('.vat_percentage option:selected').data('account-id')
                };

                // For the first row only, set input_vat_account_id as input_vat_account_id of the first row
                if (index === 0) {
                    item.input_vat_account_id_first_row = item.input_vat_account_id;
                    item.discount_account_id_first_row = item.discount_account_id;
                }

                items.push(item);
            });
            return items;
        }
        $('#apvForm').submit(function (event) {
            event.preventDefault();

            const items = gatherTableItems();

            if (items.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please add items first'
                });
                return;
            }

            $('#item_data').val(JSON.stringify(items));

            // Show loading overlay
            $('#loadingOverlay').css('display', 'flex');

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
                success: function (response) {
                    // Hide loading overlay
                    $('#loadingOverlay').hide();

                    if (response.success) {
                        const transactionId = response.id;

                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'APV submitted successfully!',
                            showCancelButton: false,
                            confirmButtonText: 'Print'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                updatePrintStatusAndPrint(transactionId, 1);
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error saving check: ' + (response.message || 'Unknown error')
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    // Hide loading overlay
                    $('#loadingOverlay').hide();

                    console.error('AJAX error:', textStatus, errorThrown);
                    console.log('Response Text:', jqXHR.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving the check: ' + textStatus
                    });
                }
            });
        });

        function updatePrintStatusAndPrint(id, printStatus) {
            $.ajax({
                url: 'api/apv_controller.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'update_print_status',
                    id: id,
                    print_status: printStatus
                },
                success: function (response) {
                    if (response.success) {
                        console.log('Print status updated successfully');

                        const printFrame = document.getElementById('printFrame');
                        const printContentUrl = `print_apv?action=print&id=${id}`;

                        printFrame.src = printContentUrl;

                        printFrame.onload = function () {
                            printFrame.contentWindow.focus();
                            printFrame.contentWindow.print();
                        };

                    } else {
                        console.error('Failed to update print status:', response.message);
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
</script>