<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();
$accounts = ChartOfAccount::all();
$cost_centers = CostCenter::all();
$customers = Customer::all();
$products = Product::all();
$terms = Term::all();
$locations = Location::all();
$payment_methods = PaymentMethod::all();
$wtaxes = WithholdingTax::all();
// $credit_memo = CreditMemo::all();

$discounts = Discount::all();
$input_vats = InputVat::all();
$sales_taxes = SalesTax::all();
$newCreditNo = CreditMemo::getLastCreditNo();


$page = 'credit_memo'; // Set the variable corresponding to the current page
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

        .loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            z-index: 9999;
            justify-content: center;
            align-items: center;
        }

        .loading-overlay .spinner {
            border: 16px solid #54BD69;
            border-top: 16px solid #fff;
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
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
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3"><strong>View Credit Memo</strong></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="credit_memo">Credit Memo</a></li>
                                <li class="breadcrumb-item active" aria-current="page">View Credit Memo</li>
                            </ol>
                        </nav>
                    </div>
                    <a href="credit_memo" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Credits List
                    </a>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_payment_method'); ?>
                    <?php displayFlashMessage('delete_payment_method'); ?>
                    <?php displayFlashMessage('update_payment_method'); ?>

                    <?php if (isset($_GET['id'])): ?>
                        <?php
                        $id = $_GET['id'];
                        $credit_memo = CreditMemo::find($id);
                        if ($credit_memo):
                        ?>
                            <form id="writeCheckForm" action="api/credit_controller.php?action=add" method="POST">
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
                                                    <!-- Customer Details Section -->
                                                    <div class="col-12 mb-3">
                                                        <h6 class="border-bottom pb-2">Customer Details</h6>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="credit_no">Credit No</label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    id="credit_no" name="credit_no"
                                                                <?php if ($credit_memo->status == 4): ?>
                                                                    value="<?php echo htmlspecialchars($newCreditNo); ?>" readonly>
                                                                <?php else: ?>
                                                                    value="<?php echo htmlspecialchars($credit_memo->credit_no); ?>" disabled>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="credit_date">Date</label>
                                                                <input type="date" class="form-control form-control-sm"
                                                                    id="credit_date" name="credit_date"
                                                                    value="<?= date('Y-m-d', strtotime($credit_memo->credit_date)) ?>" disabled>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="credit_account_id">Account</label>
                                                                <select class="form-control form-control-sm select2"
                                                                    id="credit_account_id" name="credit_account_id" disabled>
                                                                    <!-- Default selected option -->
                                                                    <option
                                                                        value="<?= htmlspecialchars($credit_memo->credit_account) ?>"
                                                                        selected>
                                                                        <?= htmlspecialchars($credit_memo->account_code . ' - ' . $credit_memo->account_description) ?>
                                                                    </option>
                                                                    <?php foreach ($accounts as $acc): ?>
                                                                        <option
                                                                            value="<?= htmlspecialchars($acc->account_code) ?>"
                                                                            <?= $acc->account_code == $credit_memo->credit_account ? 'selected' : '' ?>>
                                                                            <?= htmlspecialchars($acc->account_code . ' - ' . $acc->account_description) ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                <input type="hidden" id="credit_account_id_hidden" name="credit_account_id">
                                                                </select>
                                                            </div>
                                                        </div>


                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="customer_id">Customer</label>
                                                                <select class="form-control form-control-sm select2"
                                                                    id="customer_id" name="customer_id" disabled>
                                                                    <option
                                                                        value="<?= htmlspecialchars($credit_memo->customer_name) ?>">
                                                                        <?= htmlspecialchars($credit_memo->customer_name) ?>
                                                                    </option>
                                                                    <?php foreach ($customers as $customer): ?>
                                                                        <option value="<?= htmlspecialchars($customer->id) ?>">
                                                                            <?= htmlspecialchars($customer->customer_name) ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <div class="form-group">
                                                                <label for="credit_memo">Memo</label>
                                                                <input type="text" class="form-control form-control-sm"
                                                                    id="credit_memo" name="credit_memo" placeholder="Enter memo"
                                                                    value="<?= htmlspecialchars($credit_memo->memo) ?>" disabled>
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
                                                <div class="row">
                                                    <label class="col-sm-6 col-form-label">Gross Amount:</label>
                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control-plaintext text-end"
                                                            id="gross_amount" name="gross_amount"
                                                            value="<?= number_format($credit_memo->gross_amount, 2, '.', ',')?>"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label class="col-sm-6 col-form-label">Net Amount:</label>
                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control-plaintext text-end"
                                                            id="net_amount_due" name="net_amount_due"
                                                            value="<?= number_format($credit_memo->net_amount_due, 2, '.', ',')?>"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label class="col-sm-6 col-form-label">Output VAT:</label>
                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control-plaintext text-end"
                                                            id="vat_percentage_amount" name="vat_percentage_amount"
                                                            value="<?= number_format($credit_memo->vat_percentage_amount, 2, '.', ',')?>"
                                                            readonly>
                                                        <input type="hidden" class="form-control" name="vat_account_id"
                                                            id="vat_account_id">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label class="col-sm-6 col-form-label">Taxable Amount:</label>
                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control-plaintext text-end"
                                                            id="net_of_vat" name="net_of_vat"
                                                            value="<?= number_format($credit_memo->net_of_vat, 2, '.', ',')?>"
                                                            readonly>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label class="col-sm-6 col-form-label">Tax Withheld (%):</label>
                                                    <div class="col-sm-6">
                                                        <select class="form-control form-control-sm"
                                                            id="tax_withheld_percentage" name="tax_withheld_percentage"
                                                            disabled>
                                                            <?php foreach ($wtaxes as $wtax): ?>
                                                                <option value="<?= htmlspecialchars($wtax->wtax_rate) ?>"
                                                                    data-account-id="<?= htmlspecialchars($wtax->wtax_account_id) ?>"
                                                                    <?= $wtax->wtax_rate == $credit_memo->tax_withheld_amount ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($wtax->wtax_name) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label class="col-sm-6 col-form-label">Tax Withheld Amount:</label>
                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control-plaintext text-end"
                                                            id="tax_withheld_amount" name="tax_withheld_amount"
                                                            value="<?= number_format($credit_memo->tax_withheld_amount, 2, '.', ',')?>"
                                                            readonly>
                                                        <input type="hidden" class="form-control" name="tax_withheld_account_id"
                                                            id="tax_withheld_account_id">
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <label class="col-sm-6 col-form-label fw-bold">Total Amount Due:</label>
                                                    <div class="col-sm-6">
                                                        <input type="text" class="form-control-plaintext text-end fw-bold"
                                                            id="total_amount_due" name="total_amount_due"
                                                            value="<?= number_format($credit_memo->total_amount_due, 2, '.', ',')?>"
                                                            readonly>
                                                    </div>
                                                    <div class="row mt-4">
                                                        <div class="col-md-10 d-inline-block">

                                                        <?php if ($credit_memo->status == 4): ?>
                                                            <!-- Buttons to show when invoice_status is 4 -->
                                                            <button type="submit" class="btn btn-info me-2">Save and Print</button>
                                                        <?php elseif ($credit_memo->status == 3): ?>
                                                            <!-- Button to show when invoice_status is 3 -->
                                                            <a class="btn btn-primary" href="#" id="reprintButton">
                                                                <i class="fas fa-print"></i> Reprint
                                                            </a>
                                                        <?php else: ?>
                                                            <!-- Buttons to show when invoice_status is neither 3 nor 4 -->
                                                            <button type="button" class="btn btn-secondary me-2" id="voidButton">Void</button>
                                                            <a class="btn btn-primary" href="#" id="reprintButton">
                                                                <i class="fas fa-print"></i> Reprint
                                                            </a>
                                                        <?php endif; ?>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <!-- Credit Memo Items Section -->
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
                                                        <tbody id="itemTableBody"
                                                            class="table-group-divider table-divider-color">
                                                            <?php if ($credit_memo): ?>
                                                                <?php foreach ($credit_memo->details as $detail): ?>
                                                                    <tr>
                                                                        <td>
                                                                            <select
                                                                                class="form-control form-control-sm account-dropdown select2"
                                                                                name="account_code[]" disabled>
                                                                                <option value="">Select Account</option>
                                                                                <?php foreach ($accounts as $acc): ?>
                                                                                    <option value="<?= htmlspecialchars($acc->id) ?>"
                                                                                        <?= ($acc->id == $detail['account_id']) ? 'selected' : '' ?>>
                                                                                        <?= htmlspecialchars($acc->account_code . ' - ' . $acc->account_description) ?>
                                                                                    </option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <select
                                                                                class="form-control form-control-sm cost-dropdown select2"
                                                                                name="cost_center_id[]" disabled>
                                                                                <?php foreach ($cost_centers as $cost_center): ?>
                                                                                    <option
                                                                                        value="<?= htmlspecialchars($cost_center->id) ?>"
                                                                                        <?= ($cost_center->id == $detail['cost_center_id']) ? 'selected' : '' ?>>
                                                                                        <?= htmlspecialchars($cost_center->code . ' - ' . $cost_center->particular) ?>
                                                                                    </option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                        </td>
                                                                        <td><input type="text" class="form-control form-control-sm memo"
                                                                                name="memo[]"
                                                                                value="<?= htmlspecialchars($detail['memo']) ?>" disabled></td>
                                                                        <td><input type="text"
                                                                                class="form-control form-control-sm amount"
                                                                                name="amount[]" placeholder="Enter amount"
                                                                                value="<?= number_format($detail['amount'], 2, '.', ',')?>"
                                                                                disabled>
                                                                                </td>
                                                                               
                                                                        <td><input type="text"
                                                                                class="form-control form-control-sm net_amount_due"
                                                                                name="net_amount_due[]"
                                                                                value="<?= number_format($credit_memo->net_amount_due, 2, '.', ',')?>"
                                                                                disabled>
                                                                        </td>
                                                                        <td><input type="text"
                                                                                class="form-control form-control-sm net_of_vat"
                                                                                name="net_of_vat[]"
                                                                                value="<?= number_format($credit_memo->net_of_vat, 2, '.', ',')?>"
                                                                                disabled>
                                                                        </td>
                                                                        <td><select
                                                                                class="form-control form-control-sm input_vat_percentage select2"
                                                                                name="input_vat_percentage[]" disabled>
                                                                                <?php foreach ($input_vats as $vat): ?>
                                                                                    <option
                                                                                        value="<?= htmlspecialchars($vat->input_vat_rate) ?>"
                                                                                        <?= ($vat->input_vat_rate == $detail['vat_percentage']) ? 'selected' : '' ?>>
                                                                                        <?= htmlspecialchars($vat->input_vat_name) ?>
                                                                                    </option>
                                                                                <?php endforeach; ?>
                                                                            </select></td>
                                                                        <td><input type="text"
                                                                                class="form-control form-control-sm input_vat_amount"
                                                                                name="input_vat_amount[]"
                                                                                value="<?= htmlspecialchars($credit_memo->vat_percentage_amount) ?>" disabled>
                                                                        </td>
                                                                        <td><button type="button"
                                                                                class="btn btn-sm btn-danger removeRow"><i
                                                                                    class="fas fa-trash"></i></button></td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        <?php else: ?>
                            <p>PO not found.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <p>No ID provided.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </main>
    <iframe id="printFrame" style="display:none;"></iframe>
    <div id="loadingOverlay" class="loading-overlay">
        <div class="spinner"></div>
    </div>
</div>

<?php require 'views/templates/footer.php' ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('reprintButton').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Reprint Credit?',
                text: "Are you sure you want to reprint this credit?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reprint it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    printCredit(<?= $credit_memo->id ?>, 2); // Pass 2 for reprint
                }
            });
        });

        // Attach event listener for the void button
        document.getElementById('voidButton').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Void Credit Memo?',
                text: "Are you sure you want to void this credit memo? This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, void it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    voidCheck(<?= $credit_memo->id ?>);
                }
            });
        });
    });

    function showLoadingOverlay() {
        document.getElementById('loadingOverlay').style.display = 'flex';
    }

    function hideLoadingOverlay() {
        document.getElementById('loadingOverlay').style.display = 'none';
    }

    function printCredit(id, printStatus) {
        showLoadingOverlay();

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
                    const printFrame = document.getElementById('printFrame');
                    const printContentUrl = `print_credit?action=print&id=${id}`;

                    printFrame.src = printContentUrl;

                    printFrame.onload = function() {
                        printFrame.contentWindow.focus();
                        printFrame.contentWindow.print();
                        hideLoadingOverlay();
                    };
                } else {
                    hideLoadingOverlay();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update print status: ' + (response.message || 'Unknown error')
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                hideLoadingOverlay();
                console.error('AJAX error:', textStatus, errorThrown);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while updating print status: ' + textStatus
                });
            }
        });
    }

    function voidCheck(id) {
        showLoadingOverlay(); // Show the loading overlay before making the request

        $.ajax({
            url: 'api/credit_controller.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'void_check',
                id: id
            },
            success: function(response) {
                hideLoadingOverlay(); // Hide the loading overlay on success
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Credit Memo has been voided successfully.'
                    }).then(() => {
                        location.reload(); // Reload the page to reflect changes
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to void credit: ' + (response.message || 'Unknown error')
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                hideLoadingOverlay(); // Hide the loading overlay on error
                console.error('AJAX error:', textStatus, errorThrown);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while voiding the credit: ' + textStatus
                });
            }
        });
    }
</script>

<script>
    $(document).ready(function() {

        $('#customer_id').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            allowClear: false
        });

        $('#credit_account_id').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            allowClear: false
        });

        $('#tax_withheld_percentage').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            allowClear: false
        });

        $('#itemTableBody .select2').select2({
            width: '100%',
            allowClear: true,
            theme: 'classic' // Or 'bootstrap4' if you're using Bootstrap 4
        });


        // Populate dropdowns with accounts from PHP
        const accounts = <?php echo json_encode($accounts); ?>;
        let accountDropdownOptions = '';
        accounts.forEach(function(account) {
            accountDropdownOptions += `<option value="${account.id}">${account.account_code} - ${account.account_description}</option>`;
        });

        // Populate dropdowns with discounts from PHP
        const discountOptions = <?php echo json_encode($discounts); ?>;
        let discountDropdownOptions = '';
        discountOptions.forEach(function(discount) {
            discountDropdownOptions += `<option value="${discount.discount_rate}" data-account-id="${discount.discount_account_id}">${discount.discount_name}</option>`;
        });

        // Populate dropdowns with VAT options from PHP
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

        // Add a new row to the table
        function addRow() {
            const newRow = `
                    <tr>
                        <td><select class="form-control form-control-sm account-dropdown select2" name="account_id[]" required>${accountDropdownOptions}</select></td>
                        <td><select class="form-control form-control-sm cost-dropdown select2" name="cost_center_id[]">${costCenterDropdownOptions}</select></td>
                        <td><input type="text" class="form-control form-control-sm memo" name="memo[]" placeholder="Enter memo"></td>
                        <td><input type="text" class="form-control form-control-sm amount" name="amount[]" placeholder="Enter amount"></td>
                        <td><input type="text" class="form-control form-control-sm net-amount-before-vat" name="net_amount_before_vat[]" placeholder="" readonly></td>
                        <td><input type="text" class="form-control form-control-sm net-amount" name="net_amount[]" placeholder="" readonly></td>
                        <td><select class="form-control form-control-sm vat_percentage select2" name="vat_percentage[]">${vatDropdownOptions}</select></td>
                        <td><input type="text" class="form-control form-control-sm input-vat" name="vat_amount[]" placeholder="" readonly></td>
                        <td><button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-trash"></i></button></td>
                    </tr>`;

            // Append the new row to the table body
            const $newRow = $(newRow).appendTo('#itemTableBody');

            // Initialize the select2 plugin on the new row's dropdown
            $newRow.find('.select2').select2({
                width: '100%',
                placeholder: '',
                theme: 'classic' // Use this if you're using Bootstrap 4
            });
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

                const vatAmount = (vatPercentage / 100) * netAmountBeforeVAT;
                $(this).closest('tr').find('.input-vat').val(vatAmount.toFixed(2)); // Update VAT amount field
                totalVat += vatAmount;

                const netAmount = netAmountBeforeVAT - vatAmount;
                $(this).closest('tr').find('.net-amount').val(netAmount.toFixed(2)); // Update net amount field
                totalTaxableAmount += netAmount;
            });

            // Update total fields
            $("#gross_amount").val(totalAmount.toFixed(2));
            $("#discount_amount").val(totalDiscountAmount.toFixed(2));
            $("#net_amount_due").val(totalNetAmount.toFixed(2));
            $("#vat_percentage_amount").val(totalVat.toFixed(2));
            $("#net_of_vat").val(totalTaxableAmount.toFixed(2));

            const taxWithheldPercentage = parseFloat($("#tax_withheld_percentage").val()) || 0;
            const taxWithheldAmount = (taxWithheldPercentage / 100) * totalTaxableAmount;
            $("#tax_withheld_amount").val(taxWithheldAmount.toFixed(2));

            totalAmountDue = totalNetAmount - taxWithheldAmount;
            $("#total_amount_due").val(totalAmountDue.toFixed(2));
        }

        // Event listener for tax withheld percentage change
        $('#tax_withheld_percentage').on('change', function() {
            calculateNetAmount();
        });

        // Event listener for amount input
        $('#itemTableBody').on('input', '.amount', function() {
            calculateNetAmount();
        });

        // Event listener for discount or VAT change
        $('#itemTableBody').on('change', '.discount_percentage, .vat_percentage', function() {
            calculateNetAmount();
        });

        // Event listeners
        $('#addItemBtn').click(addRow);

        $(document).on('click', '.removeRow', function() {
            $(this).closest('tr').remove();
            calculateNetAmount();
        });

        // Gather table items and submit form
        function gatherTableItems() {
            const items = [];
            $('#itemTableBody tr').each(function(index) {
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

                // For the first row only, set input_vat_account_id as input_vat_account_id of the first row
                if (index === 0) {
                    item.input_vat_account_id_first_row = item.input_vat_account_id;
                    item.discount_account_id_first_row = item.discount_account_id;
                }

                items.push(item);
            });
            return items;
        }

        document.getElementById('tax_withheld_percentage').addEventListener('change', function () {
            var selectedOption = this.options[this.selectedIndex];
            var accountId = selectedOption.getAttribute('data-account-id');
            console.log('Tax Withheld Account ID:', accountId);
            document.getElementById('tax_withheld_account_id').value = accountId;
        });

        $('#credit_account_id').on('change', function() {
            $('#credit_account_id_hidden').val($(this).val());
        });

        $('#writeCheckForm').submit(function (event) {
            event.preventDefault();
        
            // Check if the table has any rows
            if ($('#itemTableBody tr').length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Items',
                    text: 'You must add at least one item before submitting the credit memo.'
                });
                return false;
            }

            const items = gatherTableItems();
            $('#item_data').val(JSON.stringify(items));
            const creditStatus = <?= json_encode($credit_memo->status) ?>;
            const id = <?= json_encode($credit_memo->id) ?>;

            if (creditStatus == 4) {
                // Show loading overlay
                document.getElementById('loadingOverlay').style.display = 'flex';
                
                const formData = {
                    action: 'update_draft',
                    id: id,
                    credit_no: $('#credit_no').val(),
                    credit_date: $('#credit_date').val(),
                    customer_id: $('#customer_id').val(),
                    customer_name: $('#customer_id option:selected').text(),
                    credit_account_id: $('#credit_account_id_hidden').val(),
                    memo: $('#credit_memo').val(),
                    gross_amount: $('#gross_amount').val(),
                    net_amount_due: $('#net_amount_due').val(),
                    vat_percentage_amount: $('#vat_percentage_amount').val(),
                    net_of_vat: $('#net_of_vat').val(),
                    tax_withheld_amount: $('#tax_withheld_amount').val(),
                    tax_withheld_account_id: $('#tax_withheld_percentage option:selected').data('account-id'),
                    total_amount_due: $('#total_amount_due').val(),
                    item_data: JSON.stringify(items)
                };

                console.log('Sending data:', formData);  // Log the data being sent

                $.ajax({
                    url: 'api/credit_controller.php',
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    success: function (response) {
                        document.getElementById('loadingOverlay').style.display = 'none';
                        console.log('Selected credit account ID:', $('#credit_account_id').val());
                        console.log('Response:', response);  // Log the response
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Credit memo submitted successfully!',
                                showCancelButton: true,
                                confirmButtonText: 'Print',
                                cancelButtonText: 'Close'
                            }).then((result) => {
                                if (result.isConfirmed && response.id) {
                                    printCredit(response.id, 1); // Pass 1 for initial print
                                } else {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error saving credit memo: ' + (response.message || 'Unknown error')
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
                            text: 'An error occurred while saving the credit memo: ' + textStatus
                        });
                    }
                });
            }
        });
    });

</script>