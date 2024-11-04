<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('write_check');

$accounts = ChartOfAccount::all();
$vendors = Vendor::all();
$customers = Customer::all();
$other_names = OtherNameList::all();
$employee = Employee::all();
$cost_centers = CostCenter::all();
$discounts = Discount::all();
$wtaxes = WithholdingTax::all();
$input_vats = InputVat::all();
$locations = Location::all();

$newWriteCvNo = WriteCheck::getLastCheckNo();
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
                        <h1 class="h3"><strong>Create New Check</strong></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="write_check">Write Check</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Create Write Check</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="write_check" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Checks
                        </a>
                    </div>
                </div>
            </div>

            <form id="writeCheckForm" action="api/write_check_controller.php?action=add" method="POST">
                <input type="hidden" name="action" id="modalAction" value="add" />
                <input type="hidden" name="id" id="itemId" value="" />
                <input type="hidden" name="item_data" id="item_data" />
                <input type="hidden" name="payee_name" id="payee_name" />

                <div class="row">
                    <div class="col-12 col-lg-8">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Write Check Details</h5>
                                <h6 class="border-bottom pb-2"></h6>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <!-- Vendor Details Section -->
                                    <!-- SELECT BANK ACCOUNT -->
                                    <div class="col-md-4 customer-details">
                                        <div class="form-group">
                                            <label for="bank_account_id">Bank Account</label>
                                            <select class="form-control form-control-sm select2" id="bank_account_id"
                                                name="bank_account_id" required>
                                                <option value="">Select Account</option>
                                                <?php foreach ($accounts as $account): ?>
                                                    <?php if ($account->account_type == 'Cash and Cash Equivalents'): ?>
                                                        <option value="<?= $account->id ?>">
                                                            <?= $account->account_code ?> -
                                                            <?= $account->account_description ?>
                                                        </option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <br><br><br> <br>
                                    <div class="col-12 mb-3">
                                        <h6 class="border-bottom pb-2">Payee Details</h6>
                                    </div>

                                    <div class="row">
                                        <!-- SELECT PAYEE TYPE -->
                                        <div class="col-md-4 customer-details">
                                            <div class="form-group">
                                                <label for="payee_type">Payee Type</label>
                                                <select class="form-control form-control-sm select2" id="payee_type"
                                                    name="payee_type" required>
                                                    <option value="select payee type">SELECT PAYEE TYPE</option>
                                                    <option value="customers">Customer</option>
                                                    <option value="vendors">Vendor</option>
                                                    <option value="other_name">Other Names</option>
                                                    <option value="employee">Employee</option>

                                                </select>
                                            </div>
                                        </div>

                                        <!-- SELECT PAYEE -->
                                        <div class="col-md-4 customer-details">
                                            <div class="form-group">
                                                <label for="payee_id">Payee <span class="text-muted"
                                                        id="payee_type_display"></span></label>
                                                <select class="form-control form-control-sm select2" id="payee_id"
                                                    name="payee_id" required>
                                                    <option value="">Select Payee</option>
                                                    <!-- Payee options will be populated dynamically based on the payee type selection -->
                                                </select>
                                            </div>
                                        </div>

                                        <!-- PAYEE ADDRESS -->
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label for="payee_address">Address</label>
                                                <input type="text" class="form-control form-control-sm"
                                                    id="payee_address" name="payee_address">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Write Check Information Section -->
                                <div class="col-12 mt-3 mb-3">
                                    <h6 class="border-bottom pb-2">Write Check Information</h6>
                                </div>

                                <div class="row">
                                    <div class="col-md-3 order-details">
                                        <div class="form-group">
                                            <!-- PURCHASE ORDER NO -->
                                            <label for="cv_no">CV No</label>
                                            <input type="text" class="form-control form-control-sm" id="cv_no"
                                                name="cv_no" value="<?php echo $newWriteCvNo; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <!-- CHECK NO -->
                                        <div class="form-group">
                                            <label for="ref_no">Ref Doc No</label>
                                            <input type="text" class="form-control form-control-sm" id="ref_no"
                                                name="ref_no" placeholder="Enter ref no">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <!-- CHECK NO -->
                                        <div class="form-group">
                                            <label for="check_no">Check No</label>
                                            <input type="text" class="form-control form-control-sm" id="check_no"
                                                name="check_no" placeholder="Enter check no">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <!-- SELECT DATE -->
                                        <div class="form-group">
                                            <label for="check_date">Check Date</label>
                                            <input type="date" class="form-control form-control-sm" id="check_date"
                                                name="check_date" value="<?php echo date('Y-m-d'); ?>" required>
                                        </div>
                                    </div>



                                    <div class="row mt-5">
                                        <!-- MEMO -->
                                        <div class="col-md-8 order-details">
                                            <div class="form-group">
                                                <label for="memo" class="form-label">Memo</label>
                                                <input type="text" class="form-control form-control-sm" id="memo" name="memo">
                                            </div>
                                        </div>

                                        <!-- LOCATION -->
                                        <div class="col-md-4 customer-details">
                                            <div class="form-group">
                                                <label for="location" class="form-label">Location</label>
                                                <select class="form-select form-select-sm" id="location" name="location" required>
                                                    <option value="">Select Location</option>
                                                    <?php foreach ($locations as $location): ?>
                                                        <option value="<?= htmlspecialchars($location->id) ?>"><?= htmlspecialchars($location->name) ?></option>
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
                                            <option value="">Select</option>
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
                                <button type="button" class="btn btn-secondary me-2" id="saveDraftBtn">Save as
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
                            <h5 class="card-title mb-0">Write Checks Items</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover" id="itemTable">
                                    <thead class="bg-light" style="font-size: 12px;">
                                        <tr>
                                            <th style="width: 15%;">ACCOUNT</th>
                                            <th style="width: 15%;">COST</th>
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
                                <i class="fas fa-plus"></i> Add Item
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
    <div class="message">Processing wchecks</div>
</div>
</div>


<?php require 'views/templates/footer.php' ?>


<iframe id="printFrame" style="display:none;"></iframe>

<script>
    $(document).ready(function() {
        // Data from server (ideally, this should be fetched via an API)
        var customers = <?= json_encode($customers) ?>;
        var vendors = <?= json_encode($vendors) ?>;
        var otherNames = <?= json_encode($other_names) ?>;

        const payeeTypeSelect = $('#payee_type');
        const payeeDropdown = $('#payee_id');
        const payeeNameInput = $('#payee_name');

        // Event handlers
        payeeTypeSelect.change(handlePayeeTypeChange);
        payeeDropdown.change(handlePayeeSelectionChange);

        function handlePayeeTypeChange() {
            const payeeType = payeeTypeSelect.val();
            resetPayeeDropdown();
            populatePayeeDropdown(payeeType);
        }

        function handlePayeeSelectionChange() {
            const payeeId = payeeDropdown.val();
            const payeeType = payeeTypeSelect.val();
            updatePayeeName(payeeType, payeeId);
        }

        function resetPayeeDropdown() {
            payeeDropdown.empty().append('<option value="">Select Payee</option>');
            payeeNameInput.val('');
        }

        function populatePayeeDropdown(payeeType) {
            const data = getDataByPayeeType(payeeType);
            data.forEach(item => {
                payeeDropdown.append(`<option value="${item.id}">${item.name}</option>`);
            });
        }

        function updatePayeeName(payeeType, payeeId) {
            const data = getDataByPayeeType(payeeType);
            const selectedPayee = data.find(item => item.id == payeeId);
            const name = selectedPayee ? selectedPayee.name : '';
            payeeNameInput.val(name);
        }

        function getDataByPayeeType(payeeType) {
            switch (payeeType) {
                case 'customers':
                    return mapData(customers, 'customer_name', 'customer_address');
                case 'vendors':
                    return mapData(vendors, 'vendor_name', 'vendor_address');
                case 'other_name':
                    return mapData(otherNames, 'other_name', 'other_name_address');
                default:
                    return [];
            }
        }

        function mapData(data, nameKey, addressKey) {
            return data.map(item => ({
                id: item.id,
                name: item[nameKey],
                address: item[addressKey]
            }));
        }
    });
</script>

<script>
    $(document).ready(function() {
        // Data from server (ideally, this should be fetched via an API)
        var customers = <?= json_encode($customers) ?>;
        var vendors = <?= json_encode($vendors) ?>;
        var otherNames = <?= json_encode($other_names) ?>;
        var employee = <?= json_encode($employee) ?>;

        const payeeTypeSelect = $('#payee_type');
        const payeeDropdown = $('#payee_id');
        const payeeNameInput = $('#payee_name');
        const payeeAddressInput = $('#payee_address');

        // Event handlers
        payeeTypeSelect.change(handlePayeeTypeChange);
        payeeDropdown.change(handlePayeeSelectionChange);

        function handlePayeeTypeChange() {
            const payeeType = payeeTypeSelect.val();
            resetPayeeDropdown();
            populatePayeeDropdown(payeeType);
        }

        function handlePayeeSelectionChange() {
            const payeeId = payeeDropdown.val();
            const payeeType = payeeTypeSelect.val();
            updatePayeeDetails(payeeType, payeeId);
        }

        function resetPayeeDropdown() {
            payeeDropdown.empty().append('<option value="">Select Payee</option>');
            payeeNameInput.val('');
            payeeAddressInput.val('');
        }

        function populatePayeeDropdown(payeeType) {
            const data = getDataByPayeeType(payeeType);
            data.forEach(item => {
                payeeDropdown.append(`<option value="${item.id}">${item.name}</option>`);
            });
        }

        function updatePayeeDetails(payeeType, payeeId) {
            const data = getDataByPayeeType(payeeType);
            const selectedPayee = data.find(item => item.id == payeeId);
            if (selectedPayee) {
                payeeNameInput.val(selectedPayee.name);
                payeeAddressInput.val(selectedPayee.address);
            } else {
                payeeNameInput.val('');
                payeeAddressInput.val('');
            }
        }

        function getDataByPayeeType(payeeType) {
            switch (payeeType) {
                case 'customers':
                    return mapData(customers, 'customer_name', 'billing_address');
                case 'vendors':
                    return mapData(vendors, 'vendor_name', 'vendor_address');
                case 'other_name':
                    return mapData(otherNames, 'other_name', 'other_name_address');
                case 'employee':
                    return mapEmployeeData(employee);
                default:
                    return [];
            }
        }

        function mapData(data, nameKey, addressKey) {
            return data.map(item => ({
                id: item.id,
                name: item[nameKey],
                address: item[addressKey]
            }));
        }

        function mapEmployeeData(data) {
            return data.map(item => ({
                id: item.id,
                name: `${item.first_name} ${item.middle_name} ${item.last_name}`,
                address: [
                    item.house_lot_number,
                    item.street,
                    item.barangay,
                    item.town,
                    item.city
                ].filter(Boolean).join(', ') // Filter out any empty values and join with comma
            }));
        }
    });
</script>


<script>
    $(document).ready(function() {

        // Add click event listener to the Clear button
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
            $('#gross_amount, #discount_amount, #net_amount_due, #vat_percentage_amount, #net_of_vat, #tax_withheld_amount, #total_amount_due').val('0.00');

            // Clear hidden inputs
            $('#item_data, #payee_name, #discount_account_id, #vat_account_id, #tax_withheld_account_id').val('');

            // Reset date input to current date
            $('#check_date').val(new Date().toISOString().split('T')[0]);

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

            // Get the tax_withheld_account_id
            const taxWithheldAccountId = $('#tax_withheld_account_id').val();

            // Prepare the form data
            const formData = new FormData($('#writeCheckForm')[0]);
            formData.append('action', 'save_draft');
            formData.append('tax_withheld_account_id', taxWithheldAccountId);

            // Add summary data with raw values
            const summaryFields = ['gross_amount', 'discount_amount', 'net_amount_due', 'vat_percentage_amount', 'net_of_vat', 'tax_withheld_amount', 'total_amount_due'];
            summaryFields.forEach(field => {
                const rawValue = $(`#${field}`).data('raw-value') || 0;
                formData.set(field, rawValue);
            });

            // Show the loading overlay
            document.getElementById('loadingOverlay').style.display = 'flex';

            // Use AJAX to submit the form
            $.ajax({
                url: 'api/write_check_controller.php',
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
                            text: 'Check saved as draft successfully!',
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

        document.getElementById('tax_withheld_percentage').addEventListener('change', function() {
            var selectedOption = this.options[this.selectedIndex];
            var accountId = selectedOption.getAttribute('data-account-id');
            console.log(accountId);
            document.getElementById('tax_withheld_account_id').value = accountId;
        });

        $('#bank_account_id').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Account',
            allowClear: false
        });

        $('#location').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Location',
            allowClear: false
        });

        $('#payee_id').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Payee',
            allowClear: false
        });

        $('#payee_type').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Payee Type',
            allowClear: false
        });

        // Populate dropdowns with accounts from PHP
        const accounts = <?php echo json_encode($accounts); ?>;
        let accountDropdownOptions = '';
        $.each(accounts, function(index, account) {
            accountDropdownOptions += `<option value="${account.id}">${account.account_code}-${account.account_description}</option>`;
        });

        // Populate dropdowns with discounts from PHP
        const discountOptions = <?php echo json_encode($discounts); ?>;
        let discountDropdownOptions = '';
        discountOptions.forEach(function(discount) {
            discountDropdownOptions += `<option value="${discount.discount_rate}" data-account-id="${discount.discount_account_id}">${discount.discount_name}</option>`;
        });

        // Populate dropdowns with VAT from PHP
        const vatOptions = <?php echo json_encode($input_vats); ?>;
        let vatDropdownOptions = '';
        vatOptions.forEach(function(vat) {
            vatDropdownOptions += `<option value="${vat.input_vat_rate}" data-account-id="${vat.input_vat_account_id}">${vat.input_vat_name}</option>`;
        });

        // Populate dropdowns with cost centers from PHP
        const costCenterOptions = <?php echo json_encode($cost_centers); ?>;
        let costCenterDropdownOptions = '';
        costCenterOptions.forEach(function(cost) {
            costCenterDropdownOptions += `<option value="${cost.id}">${cost.code} - ${cost.particular}</option>`;
        });

        function addRow() {
            const newRow = `
                <tr>
                    <td><select class="form-control form-control-sm account-dropdown select2" name="account_id[]" required>${accountDropdownOptions}</select></td>
                    <td><select class="form-control form-control-sm cost-dropdown select2" name="cost_center_id[]">${costCenterDropdownOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm memo" name="memo[]" placeholder="Enter memo"></td>
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

            // Initialize Select2 for the new row
            $('#itemTableBody tr:last-child').find('.select2').select2({
                width: '100%',
                theme: 'classic' // Use this if you're using Bootstrap 4
            });
        }

        // Function to format number with commas and two decimal places
        function formatNumber(num) {
            return parseFloat(num).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }

        function parseFormattedNumber(str) {
            return parseFloat(str.replace(/,/g, '')) || 0;
        }

        // Function to calculate net amount
        function calculateNetAmount() {
            let totalAmount = 0;
            let totalDiscountAmount = 0;
            let totalNetAmount = 0;
            let totalVat = 0;
            let totalTaxableAmount = 0;
            let totalAmountDue = 0;

            $('.amount').each(function() {
                const amount = parseFloat($(this).data('raw-value')) || parseFormattedNumber($(this).val());
                totalAmount += amount;
                const discountPercentage = parseFloat($(this).closest('tr').find('.discount_percentage').val()) || 0;
                const vatPercentage = parseFloat($(this).closest('tr').find('.vat_percentage').val()) || 0;

                const discountAmount = (discountPercentage / 100) * amount;
                $(this).closest('tr').find('.discount-amount').val(formatNumber(discountAmount)).data('raw-value', discountAmount);
                totalDiscountAmount += discountAmount;

                const netAmountBeforeVAT = amount - discountAmount;
                $(this).closest('tr').find('.net-amount-before-vat').val(formatNumber(netAmountBeforeVAT)).data('raw-value', netAmountBeforeVAT);
                totalNetAmount += netAmountBeforeVAT;

                const vatPercentageAmount = vatPercentage / 100;
                const netAmount = netAmountBeforeVAT / (1 + vatPercentageAmount);
                $(this).closest('tr').find('.net-amount').val(formatNumber(netAmount)).data('raw-value', netAmount);

                const vatAmount = netAmountBeforeVAT - netAmount;
                $(this).closest('tr').find('.input-vat').val(formatNumber(vatAmount)).data('raw-value', vatAmount);
                totalVat += vatAmount;

                totalTaxableAmount += netAmount;
            });

            $("#gross_amount").val(formatNumber(totalAmount)).data('raw-value', totalAmount);
            $("#discount_amount").val(formatNumber(totalDiscountAmount)).data('raw-value', totalDiscountAmount);
            $("#net_amount_due").val(formatNumber(totalNetAmount)).data('raw-value', totalNetAmount);
            $("#vat_percentage_amount").val(formatNumber(totalVat)).data('raw-value', totalVat);
            $("#net_of_vat").val(formatNumber(totalTaxableAmount)).data('raw-value', totalTaxableAmount);

            const taxWithheldPercentage = parseFloat($("#tax_withheld_percentage").val()) || 0;
            const taxWithheldAmount = (taxWithheldPercentage / 100) * totalTaxableAmount;
            $("#tax_withheld_amount").val(formatNumber(taxWithheldAmount)).data('raw-value', taxWithheldAmount);

            totalAmountDue = totalNetAmount - taxWithheldAmount;
            $("#total_amount_due").val(formatNumber(totalAmountDue)).data('raw-value', totalAmountDue);
        }

        $('#itemTableBody').on('focus', '.amount', function() {
            $(this).val($(this).data('raw-value') || parseFormattedNumber($(this).val()));
        });

        $('#itemTableBody').on('blur', '.amount', function() {
            const rawValue = parseFormattedNumber($(this).val());
            const formattedValue = formatNumber(rawValue);
            $(this).val(formattedValue);
            $(this).data('raw-value', rawValue);
            calculateNetAmount();
        });

        $('#itemTableBody').on('input', '.amount, .discount_percentage, .vat_percentage', function() {
            calculateNetAmount();
        });

        $('#tax_withheld_percentage').on('change', function() {
            calculateNetAmount();
        });

        $('#addItemBtn').click(function() {
            addRow();
            calculateNetAmount();
        });

        $(document).on('click', '.removeRow', function() {
            $(this).closest('tr').remove();
            calculateNetAmount();
        });


        function getUniqueInputVatIds() {
            const uniqueIds = new Set();
            $('#itemTableBody tr').each(function() {
                const inputVatId = $(this).find('.vat_percentage option:selected').data('account-id');
                if (inputVatId) {
                    uniqueIds.add(inputVatId);
                }
            });
            return Array.from(uniqueIds);
        }

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

        // Gather table items and submit form
        function gatherTableItems() {
            const items = [];
            let hasEmptyAmount = false;
            let firstEmptyAmountRow;


            $('#itemTableBody tr').each(function(index) {

                const amount = parseFloat($(this).find('.amount').data('raw-value')) || parseFormattedNumber($(this).find('.amount').val());

                if (!amount) {
                    hasEmptyAmount = true;
                    if (!firstEmptyAmountRow) {
                        firstEmptyAmountRow = $(this); // Store the first row with empty quantity
                    }
                    return true; // Continue to the next row
                }

                const item = {
                    account_id: $(this).find('.account-dropdown').val(),
                    cost_center_id: $(this).find('.cost-dropdown').val(),
                    memo: $(this).find('.memo').val(),
                    amount: amount,
                    discount_percentage: $(this).find('.discount_percentage').val(),
                    discount_amount: parseFormattedNumber($(this).find('.discount-amount').val()),
                    net_amount_before_vat: parseFormattedNumber($(this).find('.net-amount-before-vat').val()),
                    net_amount: parseFormattedNumber($(this).find('.net-amount').val()),
                    vat_percentage: $(this).find('.vat_percentage').val(),
                    input_vat: parseFormattedNumber($(this).find('.input-vat').val()),
                    discount_account_id: $(this).find('.discount_percentage option:selected').data('account-id'),
                    input_vat_account_id: $(this).find('.vat_percentage option:selected').data('account-id')
                };

                if (index === 0) {
                    item.input_vat_account_id_first_row = item.input_vat_account_id;
                    item.discount_account_id_first_row = item.discount_account_id;
                }

                items.push(item);
            });

            if (hasEmptyAmount) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please enter a Amount for every account.'
                }).then(() => {
                    // Highlight the first row with an empty quantity
                    firstEmptyAmountRow.find('input[name="cost[]"]').focus().css('border', '2px solid red');
                });
                return false;
            }

            return items;
        }

        function parseSummaryValue(id) {
            return parseFloat($('#' + id).val().replace(/,/g, '')) || 0;
        }


        // Update the form submission function
        $('#writeCheckForm').submit(function(event) {
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
            $('#item_data').val(JSON.stringify(items));

            // Parse summary values
            const summaryData = {
                gross_amount: parseSummaryValue('gross_amount'),
                discount_amount: parseSummaryValue('discount_amount'),
                net_amount_due: parseSummaryValue('net_amount_due'),
                vat_percentage_amount: parseSummaryValue('vat_percentage_amount'),
                net_of_vat: parseSummaryValue('net_of_vat'),
                tax_withheld_amount: parseSummaryValue('tax_withheld_amount'),
                total_amount_due: parseSummaryValue('total_amount_due')
            };

            // Add summary data to form data
            const formData = $(this).serializeArray();
            $.each(summaryData, function(key, value) {
                formData.push({
                    name: key,
                    value: value
                });
            });

            // Show loading overlay
            $('#loadingOverlay').css('display', 'flex');

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                dataType: 'json',
                data: formData,
                success: function(response) {
                    // Hide loading overlay
                    $('#loadingOverlay').hide();

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Check submitted successfully!',
                            showCancelButton: false,
                            confirmButtonText: 'Print'
                        }).then((result) => {
                            if (result.isConfirmed && response.id) {
                                printWchecks(response.id, 1);
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
                error: function(jqXHR, textStatus, errorThrown) {
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

        function printWchecks(id, printStatus) {
            // First, update the print status
            $.ajax({
                url: 'api/write_check_controller.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'update_print_status',
                    id: id,
                    print_status: printStatus
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Print status updated, now printing check:', id);

                        // Open a new window
                        const printWindow = window.open('', '_blank');
                        const printContentUrl = `print_check?action=print&id=${id}`;

                        // Load the content into the new window
                        printWindow.location.href = printContentUrl;

                        // Wait for the new window to load before printing
                        printWindow.onload = function() {
                            printWindow.focus();
                            printWindow.print();
                            printWindow.close(); // Optionally close the window after printing
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