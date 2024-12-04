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
        min-width: 800px;
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
                                                <label for="vendor_id" class="form-label">
                                                    Vendor
                                                    <a href="#" id="addNewVendorLink" class="ms-3 text-primary">| Add New</a>
                                                </label>
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
                                            <th style="width: 50px;"><input type="checkbox"></th>
                                            <th>Due Date</th>
                                            <th>PO No.</th>
                                            <th>RR No.</th>
                                            <th>Ref Doc No.</th>
                                            <th>Vendor</th>
                                            <th>Amount Payable</th>
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
        </div>
    </main>
    <div id="loadingOverlay" style="display: none;">
        <div class="spinner"></div>
        <div class="message">Processing wchecks</div>
    </div>
</div>



<?php require 'views/templates/footer.php' ?>

<iframe id="printFrame" style="display:none;"></iframe>




<!-- Bootstrap Modal for Adding New Customer and New Location -->
<?php
require_once(__DIR__ . '/../layouts/add_location.php');
require_once(__DIR__ . '/../layouts/add_vendor.php');
?>

// modal script
<script>
    // Open the modal when the "Add New Location" link is clicked
    document.getElementById("addNewLocationLink").addEventListener("click", function() {
        const addLocationModal = new bootstrap.Modal(document.getElementById("addLocationModal"));
        addLocationModal.show();
    });

    // Open the modal when the "Add New Vendor" button is clicked
    document.getElementById("addNewVendorLink").addEventListener("click", function () {
        const addVendorModal = new bootstrap.Modal(document.getElementById("addVendorModal"));
        addVendorModal.show();
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

    // Handle the vendor addition form submission
    document.getElementById("addVendorSubmit").addEventListener("click", function() {
        const form = document.getElementById("addVendorForm");
        const formData = new FormData(form);

        // Set action to direct_add
        formData.set("action", "direct_add");

        fetch("api/masterlist/direct_add_vendor.php", {
            method: "POST",
            body: formData,
        })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                alert("Vendor added successfully");

                // Add the new vendor to the dropdown
                const vendorSelect = document.getElementById("vendor_name");
                const newOption = document.createElement("option");
                newOption.value = data.vendor.id;
                newOption.textContent = data.vendor.vendor_name;
                newOption.selected = true; // Automatically select the new vendor
                vendorSelect.appendChild(newOption);

                // Close the modal
                const addVendorModal = bootstrap.Modal.getInstance(document.getElementById("addVendorModal"));
                addVendorModal.hide();

                // Remove all modal backdrops
                document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());

                // Reset the form
                form.reset();
            } else {
                alert("Failed to add vendor: " + data.message);
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            alert("An error occurred while adding the vendor.");
        });
    });
</script>

<script>
    $(document).ready(function () {

        loadAllReceiveItems();

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

        $('#itemTableBody').on('change', 'input[type="checkbox"]', function () {
            updateSummary();
        });

        function updateSummary() {
            let grossAmount = 0;
            let discountAmount = 0;
            let netAmount = 0;
            let inputVatAmount = 0;

            $('#itemTableBody tr').each(function () {
                const checkbox = $(this).find('input[type="checkbox"]');
                if (checkbox.prop('checked')) {
                    const amountText = $(this).find('.amount').val();
                    const discountText = $(this).find('.discount-amount').val();
                    const inputVatText = $(this).find('.input-vat').val();

                    const amount = parseFloat(amountText.replace(/[^0-9.-]+/g, ""));
                    const discount = parseFloat(discountText.replace(/[^0-9.-]+/g, ""));
                    const inputVat = parseFloat(inputVatText.replace(/[^0-9.-]+/g, ""));

                    if (!isNaN(amount)) {
                        grossAmount += amount;
                    }
                    if (!isNaN(discount)) {
                        discountAmount += discount;
                    }
                    if (!isNaN(inputVat)) {
                        inputVatAmount += inputVat;
                    }
                }
            });

            netAmount = grossAmount - discountAmount;
            const taxableAmount = netAmount - inputVatAmount;

            // Update the summary fields
            $('#gross_amount').val(grossAmount.toFixed(2));
            $('#discount_amount').val(discountAmount.toFixed(2));
            $('#net_amount_due').val(netAmount.toFixed(2));
            $('#vat_percentage_amount').val(inputVatAmount.toFixed(2));
            $('#net_of_vat').val(taxableAmount.toFixed(2));

            // Recalculate other summary fields
            recalculateSummary();
        }

        function recalculateSummary() {
            const grossAmount = parseFloat($('#gross_amount').val().replace(/[^0-9.-]+/g, ""));
            const discountAmount = parseFloat($('#discount_amount').val().replace(/[^0-9.-]+/g, ""));
            const inputVatAmount = parseFloat($('#vat_percentage_amount').val().replace(/[^0-9.-]+/g, ""));
            const taxWithheldPercentage = parseFloat($('#tax_withheld_percentage').val()) || 0;

            const netAmount = grossAmount - discountAmount;
            const taxableAmount = netAmount - inputVatAmount;
            const taxWithheldAmount = netAmount * (taxWithheldPercentage / 100);
            const totalAmountDue = grossAmount - taxWithheldAmount;

            $('#net_amount_due').val(netAmount.toFixed(2));
            $('#net_of_vat').val(taxableAmount.toFixed(2));
            $('#tax_withheld_amount').val(taxWithheldAmount.toFixed(2));
            $('#total_amount_due').val(totalAmountDue.toFixed(2));
        }
        $('#vendor_id').on('select2:select', function (e) {
            var data = e.params.data;
            $('#vendor_name').val($(data.element).data('name'));
        });
        $('#location').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Location',
            allowClear: false
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

        $('#terms_id').select2({
            theme: 'classic',
            width: '100%',
            placeholder: 'Select Terms',
            allowClear: false,
            containerCssClass: 'select2--medium', // Add this line
            dropdownCssClass: 'select2--medium', // Add this line
        });

        $('#vendor_id').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Vendor',
            allowClear: false
        });

        $('#vendor_id').on('change', function () {
            var vendorId = $(this).val();
            filterReceiveItems(vendorId);
        });

        function filterReceiveItems(vendorId) {
            let itemsFound = false;

            if (vendorId) {
                // Loop through each row in the table body except the header
                $('#itemTableBody tr:not(.table-header)').each(function () {
                    var rowVendorId = $(this).data('vendor-id');

                    if (rowVendorId == vendorId) {
                        $(this).show();
                        itemsFound = true;
                    } else {
                        $(this).hide();
                    }
                });

                if (!itemsFound) {
                    // Remove any existing "no items" message
                    $('#itemTableBody tr.no-items').remove();

                    // Add a new row with the message
                    var noItemsRow = `
                        <tr class="no-items">
                            <td colspan="7" class="text-center">Vendor has no Receive Items Transaction</td>
                        </tr>
                    `;
                    $('#itemTableBody').append(noItemsRow);
                } else {
                    // Remove the "no items" message if items are found
                    $('#itemTableBody tr.no-items').remove();
                }
            } else {
                // Show all rows if no vendorId is provided
                $('#itemTableBody tr').show();
                $('#itemTableBody tr.no-items').remove();
            }
        }


        function loadAllReceiveItems() {
            $.ajax({
                url: 'api/apv_controller.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'fetch_all_receive_items'
                },
                success: function (response) {
                    if (response.success) {
                        populateItemTable(response.items || []);
                    } else {
                        console.error('Error fetching receive items:', response.message);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to fetch receive items: ' + (response.message || 'Unknown error')
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.error('AJAX error:', textStatus, errorThrown);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching receive items: ' + textStatus
                    });
                }
            });
        }


        function populateItemTable(items) {
            var tbody = $('#itemTableBody');
            tbody.empty();

            if (items.length === 0) {
                var row = `
                    <tr>
                        <td colspan="7" class="text-center">No receive items available</td>
                    </tr>
                `;
                tbody.append(row);
            } else {
                items.forEach(function (item, index) {
                    var grossAmount = parseFloat(item.gross_amount);
                    var discountAmount = parseFloat(item.discount_amount);
                    var netAmountBeforeVat = (grossAmount - discountAmount).toFixed(2);

                    var row = `
                        <tr data-vendor-id="${item.vendor_id}">
                            <td><input type="checkbox" id="item-${index}" class="item-checkbox"></td>
                            <td>${item.receive_due_date}</td>
                            <td><input type="text" class="form-control form-control-sm po-no" name="po_no[]" value="${item.po_no}" readonly></td>
                            <td><input type="text" class="form-control form-control-sm receive-no" name="receive_no[]" value="${item.receive_no}" readonly></td>
                            <td><input type="text" class="form-control form-control-sm memo" name="memo[]"></td>
                            <td hidden>${item.vendor_id}</td>
                            <td>${item.vendor_name}</td>
                            <td><input type="text" class="form-control form-control-sm amount" name="amount[]" value="${grossAmount.toFixed(2)}" readonly></td>
                            <td hidden><input type="text" class="form-control form-control-sm cost-dropdown" name="cost_center_id[]" value="${item.cost_center_id}"></td>
                            <td hidden><input type="text" class="form-control form-control-sm discount_percentage" name="discount_percentage[]" value="0.00" readonly></td>
                            <td hidden><input type="text" class="form-control form-control-sm net-amount-before-vat" name="net_amount_before_vat[]" value="0.00" readonly></td>
                            <td hidden><input type="text" class="form-control form-control-sm net-amount" name="net_amount[]" value="${item.net_amount}" readonly></td>
                            <td hidden><input type="text" class="form-control form-control-sm vat_percentage" name="vat_percentage[]" value="0.00" readonly></td>
                            <td hidden><input type="text" class="form-control form-control-sm discount-amount" name="discount_amount[]" value="${discountAmount.toFixed(2)}"></td>
                            <td hidden><input type="text" class="form-control form-control-sm input-vat" name="input_vat[]" value="${item.input_vat}"></td>
                            <td hidden><input type="text" class="form-control form-control-sm discount-account-id" name="discount_account_id[]" value="0.00"></td>
                            <td hidden><input type="text" class="form-control form-control-sm input-vat-account-id" name="input_vat_account_id[]" value="0.00"></td>
                            <input type="hidden" class="form-control form-control-sm receive_account_id" name="account_id[]" value="${item.receive_account_id}">
                        </tr>
                    `;
                    tbody.append(row);
                });
            }
        }

        // Gather table items and submit form
        function gatherTableItems() {
            const items = [];
            $('#itemTableBody tr').each(function (index) {
                const item = {
                    account_id: $(this).find('.receive_account_id').val(),
                    cost_center_id: $(this).find('.cost-dropdown').val(),
                    po_no: $(this).find('.po-no').val(),
                    rr_no: $(this).find('.receive-no').val(),
                    memo: $(this).find('.memo').val(),
                    amount: $(this).find('.amount').val(),
                    discount_percentage: $(this).find('.discount_percentage').val(),
                    discount_amount: $(this).find('.discount-amount').val(),
                    net_amount_before_vat: $(this).find('.net-amount-before-vat').val(),
                    net_amount: $(this).find('.net-amount').val(),
                    vat_percentage: $(this).find('.vat_percentage').val(),
                    input_vat: $(this).find('.input-vat').val(),
                    discount_account_id: $(this).find('.discount-account-id').val(),
                    input_vat_account_id: $(this).find('.input-vat-account-id').val()
                };

                if (index === 0) {
                    item.input_vat_account_id_first_row = item.input_vat_account_id;
                    item.discount_account_id_first_row = item.discount_account_id;
                }

                items.push(item);
            });
            return items;
        }

        // Sumbit the form
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
                            text: 'Check submitted successfully!',
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