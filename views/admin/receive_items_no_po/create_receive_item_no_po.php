<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('receive_items');
$accounts = ChartOfAccount::all();
$vendors = Vendor::all();
$products = Product::all();
$terms = Term::all();
$discounts = Discount::all();
$input_vats = InputVat::all();
$purchase_requests = PurchaseRequest::all();
$cost_centers = CostCenter::all();
$locations = Location::all();

$newRRNo = ReceivingReport::getLastRRNo();
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
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3"><strong>Create Receive Items</strong></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="receive_item_no_po_list">Receive Items</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Create Receive Items</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="receive_item_no_po_list" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Receive List
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_receive_item') ?>
                    <?php displayFlashMessage('delete_payment_method') ?>
                    <?php displayFlashMessage('update_payment_method') ?>

                    <!-- Receive Items No PO Form -->
                    <form id="purchaseOrderForm" action="api/receiving_report_controller.php?action=add" method="POST">
                        <input type="hidden" name="action" id="modalAction" value="add" />
                        <input type="hidden" name="id" id="itemId" value="" />
                        <input type="hidden" name="item_data" id="item_data" />

                        <div class="row">
                            <div class="col-12 col-lg-8">
                                <div class="card h-100">
                                    <div class="card-header">
                                    <h5 class="card-title mb-0">Receive Item Details</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <!-- Vendor Details Section -->
                                    <div class="col-12 mb-3">
                                        <h6 class="border-bottom pb-2">Vendor Details</h6>
                                    </div>
                                    <div class="col-md-4 vendor-details">
                                        <label for="vendor_id" class="form-label">
                                            Vendor
                                            <a href="#" id="addNewVendorLink" class="ms-3 text-primary">| Add New</a>
                                        </label>
                                        <select class="form-select form-select-sm select2" id="vendor_name"
                                            name="vendor_name" required>
                                            <option value="">Select Vendor</option>
                                            <?php foreach ($vendors as $vendor): ?>
                                                <option value="<?= $vendor->id ?>"><?= $vendor->vendor_name ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-4 vendor-details">
                                        <label for="location" class="form-label">
                                            Location
                                            <a href="#" id="addNewLocationLink" class="ms-3 text-primary">| Add New</a>
                                        </label>
                                        <select class="form-select form-select-sm" id="location" name="location"
                                            required>
                                            <option value="">Select Location</option>
                                            <?php foreach ($locations as $location): ?>
                                                <option value="<?= $location->id ?>"><?= $location->name ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <!-- Receive Item Details Section -->
                                    <div class="col-12 mt-5 mb-3">
                                        <h6 class="border-bottom pb-2">Receive Items Information</h6>
                                    </div>
                                    <div class="col-md-3 receive-item-details">
                                        <label for="receive_number" class="form-label">RR No</label>
                                        <input type="text" class="form-control form-control-sm" id="receive_number"
                                            name="receive_number" value="<?php echo $newRRNo; ?>" readonly>
                                    </div>
                                    <div class="col-md-3 receive-item-details">
                                        <label for="receive_date" class="form-label">Receive Item Date</label>
                                        <input type="date" class="form-control form-control-sm" id="receive_date"
                                            name="receive_date" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    <div class="col-md-3 receive-item-details">
                                        <label for="terms" class="form-label">Terms</label>
                                        <select class="form-select form-select-sm" id="terms" name="terms" required>
                                            <option value="">Select Terms</option>
                                            <?php foreach ($terms as $term): ?>
                                                <option value="<?= $term->term_name ?>"
                                                    data-days="<?= $term->term_days_due ?>"><?= $term->term_name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 receive-item-details">
                                        <label for="receive_due_date" class="form-label">Due Date</label>
                                        <input type="date" class="form-control form-control-sm" id="receive_due_date"
                                            name="receive_due_date" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    <div class="col-md-4 receive-item-details">
                                        <label for="account_id" class="form-label">Account</label>
                                        <select class="form-select" id="account_id" name="account_id">
                                            <?php foreach ($accounts as $account): ?>
                                                <?php if ($account->account_type == 'Accounts Payable'): ?>
                                                    <option value="<?= $account->id ?>">
                                                        <?= $account->account_code ?> - <?= $account->account_description ?>
                                                    </option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-8 receive-item-details">
                                        <label for="memo" class="form-label">Memo</label>
                                        <input type="text" class="form-control form-control-sm" id="memo" name="memo">
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
                                            <label class="col-sm-6 col-form-label">Input VAT:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end"
                                                    id="total_vat_amount" name="total_vat_amount"
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
                                    <div class="card-footer text-center">
                                        <button type="submit" class="btn btn-primary"><i
                                                class="fas fa-save me-2"></i>Save and Print</button>
                                    </div>
                                </div>
                            </div>


                            <!-- Items Table Section -->
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0">Add Receive Items</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover" id="itemTable">
                                                    <thead class="bg-light" style="font-size: 12px;">
                                                        <tr>
                                                            <th style="width: 200px">Item</th>
                                                            <th style="width: 250px">Cost Center</th>
                                                            <th>Description</th>
                                                            <th>Quantity</th>
                                                            <th>Unit</th>
                                                            <th>Cost</th>
                                                            <th>Amount</th>
                                                            <th>Discount Type</th>
                                                            <th>Discount</th>
                                                            <th>Net</th>
                                                            <th>Taxable Amount</th>
                                                            <th>Tax Type</th>
                                                            <th>Input Vat</th>
                                                            <th></th>
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
        </div>
    </main>
    <div id="loadingOverlay" style="display: none;">
        <div class="spinner"></div>
        <div class="message">Processing Receive Items</div>
    </div>
</div>


<?php require 'views/templates/footer.php' ?>
<iframe id="printFrame" style="display:none;"></iframe>

<?php
require_once(__DIR__ . '/../layouts/add_location.php');
require_once(__DIR__ . '/../layouts/add_vendor.php');
?>

 <!-- modal script -->
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

                // Add the new location to the dropdown
                const locationSelect = document.getElementById("location");
                const newOption = document.createElement("option");
                newOption.value = data.location.id;
                newOption.textContent = data.location.location_name; // Changed from data.location.location
                newOption.selected = true;
                locationSelect.appendChild(newOption);

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
            $('#gross_amount, #total_discount_amount, #net_amount_due, #total_vat_amount, #vatable_amount, #zero_rated_amount, #vat_exempt_amount, #total_amount_due').val('0.00');

            // Clear hidden inputs
            $('#item_data').val('');

            // Reset date input to current date
            $('#po_date, #delivery_date').val(new Date().toISOString().split('T')[0]);

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

            // Check if there are any items or validation failed
            if (items === false || items.length === 0) {
                if (items === false) return; // Prevent further execution if validation failed
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please add items'
                });
                return;
            }

            $('#item_data').val(JSON.stringify(items));

            // Prepare the form data
            const formData = new FormData($('#purchaseOrderForm')[0]);
            formData.append('action', 'save_draft');

            // Show the loading overlay
            document.getElementById('loadingOverlay').style.display = 'flex';

            // Use AJAX to submit the form
            $.ajax({
                url: 'api/receiving_report_controller.php',
                type: 'POST',
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    document.getElementById('loadingOverlay').style.display = 'none';

                    if (response.success) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Success',
                            text: 'Receive Items saved as draft!',
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
        $('#vendor_id').change(function() {
            var selectedVendor = $(this).find(':selected');
            var address = selectedVendor.data('address');
            var tin = selectedVendor.data('tin');
            $('#vendor_address').val(address);
            $('#tin').val(tin);
        });

        $('#vendor_id').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Vendor',
            allowClear: true
        });
        $('#location').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Location',
            allowClear: true
        });
        $('#invoice_terms').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Terms',
            allowClear: true
        });
        // Populate dropdowns with accounts from PHP
        const products = <?php echo json_encode($products); ?>;
        let itemDropdownOptions = '<option value="" selected disabled>Select An Item</option>';
        $.each(products, function(index, product) {
            itemDropdownOptions += `<option value="${product.id}" 
            data-description="${product.item_purchase_description}" 
            data-uom="${product.uom_name}" 
            data-asset-account-id="${product.item_asset_account_id}">
            ${product.item_name}
            </option>`;
        });

        const costCenterOptions = <?php echo json_encode($cost_centers); ?>;
        let costCenterDropdownOptions = '';
        costCenterOptions.forEach(function(costCenter) {
            costCenterDropdownOptions += `<option value="${costCenter.id}">${costCenter.code} - ${costCenter.particular}</option>`;
        });

        // Populate dropdowns with accounts from PHP
        const discountOptions = <?php echo json_encode($discounts); ?>;
        let discountDropdownOptions = '';
        discountOptions.forEach(function(discount) {
            discountDropdownOptions += `<option value="${discount.discount_rate}" data-account-id="${discount.discount_account_id}">${discount.discount_name}</option>`;
        });

        const inputVatOption = <?php echo json_encode($input_vats); ?>;
        let inputVatDropdownOptions = '';
        inputVatOption.forEach(function(input_vat) {
            inputVatDropdownOptions += `<option value="${input_vat.input_vat_rate}" data-account-id="${input_vat.input_vat_account_id}">${input_vat.input_vat_name}</option>`;
        });


        // Add a new row to the table
        function addRow() {
            const newRow = `
        <tr>
            <td><select class="form-control form-control-sm account-dropdown select2" name="item_id[]" onchange="populateFields(this)">${itemDropdownOptions}</select></td>
            <td><select class="form-control form-control-sm cost-center-dropdown select2" name="cost_center_id[]">${costCenterDropdownOptions}</select></td>
            <td><input type="text" class="form-control form-control-sm description-field" name="description[]" readonly></td>
            <td><input type="text" class="form-control form-control-sm quantity" name="quantity[]" placeholder="Qty"></td>
            <td><input type="text" class="form-control form-control-sm uom" name="uom[]" readonly></td> 
            <td><input type="text" class="form-control form-control-sm cost" name="cost[]" placeholder="Enter Cost"></td>
            <td><input type="text" class="form-control form-control-sm amount" name="amount[]" placeholder="Amount" readonly></td>
            <td><select class="form-control form-control form-control-sm discount_percentage select2" name="discount_percentage[]">${discountDropdownOptions}</select></td>
            <td><input type="text" class="form-control form-control-sm discount_amount" name="discount_amount[]" placeholder="" readonly></td>
            <td><input type="text" class="form-control form-control-sm net_amount_before_input_vat" name="net_amount[]" placeholder="" readonly></td>
            <td><input type="text" class="form-control form-control-sm net_amount" name="taxable_amount[]" placeholder=""></td>
            <td><select class="form-control form-control-sm input_vat_percentage select2" name="input_vat_percentage[]">${inputVatDropdownOptions}</select></td>
            <td><input type="text" class="form-control form-control-sm input_vat_amount" name="input_vat_amount[]" placeholder="" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm removeRow"><i class="fas fa-trash"></i></button></td>
        </tr>`;
            $('#itemTableBody').append(newRow);

            // Add event listener to calculate all values
            $('.quantity, .cost, .discount_percentage, .input_vat_percentage, .input_vat_amount').on('input', function() {
                calculateRowValues($(this).closest('tr'));
                calculateTotalAmount();
            });

            // Calculate totals for existing rows and update totals fields
            $('tr').each(function() {
                calculateRowValues($(this));
                calculateTotalAmount();
            });

            $('#itemTableBody .select2').select2({
                width: '100%',
                theme: 'classic' // Use this if you're using Bootstrap 4
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


            const salesTaxDecimal = salesTaxPercentage / 100; // Convert to decimal (0.12 for 12%)
            const vat = 1 + salesTaxDecimal; // Convert to decimal (1.12 for 12%)

            const netAmountBeforeTax = amount - discountAmount;

            const salesTaxAmount = (netAmountBeforeTax / vat) * salesTaxDecimal;
            const netAmount = netAmountBeforeTax - salesTaxAmount;

            row.find('.amount').val(amount.toFixed(2));
            row.find('.discount_amount').val(discountAmount.toFixed(2));
            row.find('.net_amount_before_input_vat').val(netAmountBeforeTax.toFixed(2));
            row.find('.net_amount').val(netAmount.toFixed(2));
            row.find('.input_vat_amount').val(salesTaxAmount.toFixed(2));
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
            $('.amount').each(function() {
                const amount = parseFloat($(this).val()) || 0;
                totalAmount += amount;
            });

            // Calculate total discount amount
            $('.discount_amount').each(function() {
                const discount_amount = parseFloat($(this).val()) || 0;
                totalDiscountAmount += discount_amount;
            });

            // Calculate total net amount before input VAT
            $('.net_amount_before_input_vat').each(function() {
                const net_amount_before_input_vat = parseFloat($(this).val()) || 0;
                totalNetAmountBeforeTax += net_amount_before_input_vat;
            });

            // Calculate total input VAT amount
            $('.input_vat_amount').each(function() {
                const input_vat_amount = parseFloat($(this).val()) || 0;
                totalInputVatAmount += input_vat_amount;
            });

            // Calculate vatable, zero-rated, and VAT exempt amounts
            $('.net_amount').each(function() {
                const net_amount = parseFloat($(this).val()) || 0;
                console.log(net_amount);
                const inputVatName = $(this).closest('tr').find('.input_vat_percentage option:selected').text();

                if (inputVatName === '12% (COGS)' || '12% (EXPENSE)') {
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
            $("#total_vat_amount").val(totalInputVatAmount.toFixed(2));
            $("#vatable_amount").val(vatableAmount.toFixed(2));
            $("#zero_rated_amount").val(zeroRatedAmount.toFixed(2));
            $("#vat_exempt_amount").val(vatExemptAmount.toFixed(2));
            $("#total_amount_due").val(totalAmountDue.toFixed(2));
        }

        // Event listeners
        $('#addItemBtn').click(addRow);

        $(document).on('click', '.removeRow', function() {
            $(this).closest('tr').remove();
            calculateRowValues($(this).closest('tr'));
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
                const outputVatId = $(this).find('.input_vat_percentage option:selected').data('account-id');
                if (outputVatId) {
                    uniqueIds.add(outputVatId);
                }
            });
            return Array.from(uniqueIds);
        }

        // Gather table items and submit form
        function gatherTableItems() {
    const items = [];
    let hasEmptyItem = false;
    let hasEmptyQuantity = false;
    let hasEmptyCost = false;
    let firstEmptyItemRow;
    let firstEmptyQuantityRow;
    let firstEmptyCostRow;

    $('#itemTableBody tr').each(function (index) {
        const row = $(this);
        const item_id = row.find('select[name="item_id[]"]').val();
        const qty = parseFloat(row.find('input[name="quantity[]"]').val()) || 0;
        const cost = parseFloat(row.find('input[name="cost[]"]').val()) || 0;

        // Validate required fields
        if (!item_id) {
            hasEmptyItem = true;
            if (!firstEmptyItemRow) firstEmptyItemRow = row;
            return true; // Skip this iteration
        }

        if (qty <= 0) {
            hasEmptyQuantity = true;
            if (!firstEmptyQuantityRow) firstEmptyQuantityRow = row;
            return true;
        }

        if (cost <= 0) {
            hasEmptyCost = true;
            if (!firstEmptyCostRow) firstEmptyCostRow = row;
            return true;
        }

        // Get the selected option for item
        const selectedOption = row.find('select[name="item_id[]"] option:selected');

        // Construct the item object
        const item = {
            item_id: item_id,
            quantity: qty,
            item_asset_account_id: selectedOption.data('asset-account-id'), // From data attribute
            cost_center_id: row.find('select[name="cost_center_id[]"]').val(),
            description: row.find('input[name="description[]"]').val(),
            uom: row.find('input[name="uom[]"]').val(),
            cost: cost,
            amount: row.find('input[name="amount[]"]').val(),
            discount_percentage: row.find('select[name="discount_percentage[]"]').val(),
            discount_amount: row.find('input[name="discount_amount[]"]').val(),
            net_amount_before_input_vat: row.find('input[name="net_amount[]"]').val(),
            net_amount: row.find('input[name="taxable_amount[]"]').val(),
            input_vat_percentage: row.find('select[name="input_vat_percentage[]"]').val(),
            input_vat_amount: row.find('input[name="input_vat_amount[]"]').val(),
            discount_account_id: row.find('.discount_percentage option:selected').data('account-id'),
            input_vat_account_id: row.find('.input_vat_percentage option:selected').data('account-id'),
            cost_per_unit: qty !== 0 ? parseFloat(row.find('.taxable_amount').text()) / qty : 0
        };

        // Add first row-specific fields
        if (index === 0) {
            item.input_vat_account_id_first_row = item.input_vat_account_id;
            item.discount_account_id_first_row = item.discount_account_id;
        }

        items.push(item);
    });

    // Show warnings based on which validation failed
    if (hasEmptyItem) {
        Swal.fire({
            icon: 'warning',
            title: 'Warning',
            text: 'Please select an item.'
        }).then(() => {
            firstEmptyItemRow.find('select[name="item_id[]"]').focus().css('border', '2px solid red');
        });
        return false;
    }

    if (hasEmptyQuantity) {
        Swal.fire({
            icon: 'warning',
            title: 'Warning',
            text: 'Please enter a valid quantity for every item.'
        }).then(() => {
            firstEmptyQuantityRow.find('input[name="quantity[]"]').focus().css('border', '2px solid red');
        });
        return false;
    }

    if (hasEmptyCost) {
        Swal.fire({
            icon: 'warning',
            title: 'Warning',
            text: 'Please enter a valid item cost.'
        }).then(() => {
            firstEmptyCostRow.find('input[name="cost[]"]').focus().css('border', '2px solid red');
        });
        return false;
    }

    return items;
}



        $('#purchaseOrderForm').submit(function(event) {
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

            // Get unique discount account IDs and output VAT IDs
            const discountAccountIds = getUniqueDiscountAccountIds();
            const outputVatIds = getUniqueOutputVatIds();

            // Add hidden fields for discount_account_ids and output_vat_ids
            $('<input>').attr({
                type: 'hidden',
                name: 'discount_account_ids',
                value: discountAccountIds.join(',')
            }).appendTo($(this));

            $('<input>').attr({
                type: 'hidden',
                name: 'output_vat_ids',
                value: outputVatIds.join(',')
            }).appendTo($(this));

            // Show loading overlay
            $('#loadingOverlay').css('display', 'flex');

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
                success: function(response) {
                    // Hide loading overlay
                    $('#loadingOverlay').hide();

                    if (response.success) {
                        const transactionId = response.id;
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Re processed succesfully, Purchase details have beed saved',
                            showCancelButton: true,
                            confirmButtonText: 'Print',
                            cancelButtonText: 'Close'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                updatePrintStatusAndPrint(transactionId, 1);
                            } else {
                                window.location.href = 'purchase_request';
                            }
                        });

                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error saving Re: ' + (response.message || 'Unknown error')
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
                        text: 'An error occurred while saving the Re: ' + textStatus
                    });
                }
            });
        });

        function updatePrintStatusAndPrint(id, printStatus) {
            $.ajax({
                url: 'api/receiving_report_controller.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'update_print_status',
                    id: id,
                    print_status: printStatus
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Print status updated, now printing Re:', id);

                        // Create a new window for printing
                        const printContentUrl = `print_receive_items?action=print&id=${id}`;
                        const printWindow = window.open(printContentUrl, '_blank');

                        // Disable the submit button while printing
                        const submitButton = document.querySelector('.btn-info[type="submit"]');
                        submitButton.disabled = true;

                        // Wait for the new window to load, then print
                        printWindow.onload = function() {
                            printWindow.focus();
                            printWindow.print();

                            // Close the window after printing is done
                            printWindow.onafterprint = function() {
                                printWindow.close();
                                window.location.href = 'purchase_order';
                            };
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

    let warningTimeout;

    function populateFields(select) {
    const selectedOption = $(select).find('option:selected');
    const description = selectedOption.data('description');
    const uom = selectedOption.data('uom');
    const item_id = selectedOption.val();

    console.log('Selected item_id:', item_id); // Log selected item ID
    console.log('Selected description:', description); // Log item description
    console.log('Selected UOM:', uom); // Log item unit of measure

    if (!item_id) {
        console.log('No item selected, clearing fields'); // Debug log
        return;
    }

    // Populate fields directly since PR No check is removed
    const row = $(select).closest('tr');
    row.find('.description-field').val(description);
    row.find('.uom').val(uom);
}
</script>