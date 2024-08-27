<?php
//Guard
include '_guards.php';
Guard::adminOnly();
$accounts = ChartOfAccount::all();
$vendors = Vendor::all();
$customers = Customer::all();
$other_names = OtherNameList::all();
$cost_centers = CostCenter::all();
$discounts = Discount::all();
$wtaxes = WithholdingTax::all();
$input_vats = InputVat::all();
if (get('action') === 'update') {
    $action = 'update';
    $vendor = WriteCheck::find(get('id'));
}

$newWriteCvNo = WriteCheck::getLastCheckNo();

?>
<?php include 'views/templates/header.php' ?>
<?php include 'views/templates/sidebar.php' ?>
<div class="main">
    <style>
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
    <?php include 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="h3"><strong>View New Check</strong></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="write_check">Write Check</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View Write Check</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
        <?php if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $check = WriteCheck::find($id);
            if ($check) { ?>
                <form method="POST" action="api/write_check_controller.php" id="writeCheckForm">
                    <input type="hidden" name="action" id="modalAction" value="update" />
                    <input type="hidden" name="id" id="itemId" value="<?= $check->id ?>" />
                    <input type="hidden" name="item_data" id="item_data" />

                    <div class="row">
                        <!-- Check Details Section -->
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
                                                    <select class="form-control form-control-sm" id="bank_account_id"
                                                        name="bank_account_id" disabled>
                                                        <option value="<?= $check->account_id ?>">
                                                            <?= $check->account_name ?>
                                                        </option>
                                                        <?php foreach ($accounts as $account): ?>
                                                            <?php if ($account->account_type == 'Bank'): ?>
                                                                <option value="<?= $account->id ?>"
                                                                    <?= ($account->id == $check->account_id) ? 'selected' : '' ?>>
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
                                                    <select class="form-control form-control-sm" id="payee_type"
                                                        name="payee_type" disabled>
                                                        <option value="<?= $check->payee_type ?>" selected>
                                                            <?= ucwords(str_replace('_', ' ', $check->payee_type)) . 's' ?>
                                                        </option>
                                                        <option value="customers">Customer</option>
                                                        <option value="vendors">Vendor</option>
                                                        <option value="other_name">Other Names</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- SELECT PAYEE -->
                                            <div class="col-md-4 customer-details">
                                                <div class="form-group">
                                                    <label for="payee_id">Payee <span class="text-muted"
                                                            id="payee_type_display"></span></label>
                                                    <select class="form-control form-control-sm" id="payee_id" name="payee_id"
                                                        disabled>
                                                        <option value="<?= $check->payee_id ?>" selected>
                                                            <?= $check->payee_name ?>
                                                        </option>
                                                        <!-- Payee options will be populated dynamically based on the payee type selection -->
                                                    </select>
                                                </div>
                                            </div>

                                            <!-- PAYEE ADDRESS -->
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="payee_address">Address</label>
                                                    <input type="text" class="form-control form-control-sm" id="payee_address"
                                                        name="payee_address" value="<?= $check->payee_address ?>" disabled>
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
                                                    name="cv_no"
                                                    <?php if ($check->status == 4): ?>
                                                        value="<?php echo htmlspecialchars($newWriteCvNo); ?>" readonly>
                                                    <?php else: ?>
                                                        value="<?php echo htmlspecialchars($check->cv_no); ?>" disabled>
                                                    <?php endif; ?>
                                                  
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <!-- CHECK NO -->
                                            <div class="form-group">
                                                <label for="ref_no">Ref Doc No</label>
                                                <input type="text" class="form-control form-control-sm" id="ref_no"
                                                    name="ref_no"
                                                    value="<?= $check->ref_no ?>" disabled>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <!-- CHECK NO -->
                                            <div class="form-group">
                                                <label for="check_no">Check No</label>
                                                <input type="text" class="form-control form-control-sm" id="check_no"
                                                    name="check_no"
                                                    value="<?= $check->check_no ?>" disabled>
                                            </div>
                                        </div>



                                        <div class="col-md-3">
                                            <!-- SELECT DATE -->
                                            <div class="form-group">
                                                <label for="check_date">Check Date</label>
                                                <?php
                                                // Ensure the date is in 'yyyy-MM-dd' format or set to an empty string if invalid
                                                $checkDate = $check->check_date;
                                                $formattedDate = ($checkDate && $checkDate !== '0000-00-00') ? date('Y-m-d', strtotime($checkDate)) : '';
                                                ?>
                                                <input type="date" class="form-control form-control-sm" id="check_date"
                                                    name="check_date" value="<?= htmlspecialchars($formattedDate) ?>" disabled>
                                            </div>
                                        </div>

                                      

                                        <div class="col-md-8 order-details">
                                            <!-- MEMO -->
                                            <div class="form-group">
                                                <label for="memo" class="form-label">Memo</label>
                                                <input type="text" class="form-control form-control-sm" id="credit_memo"
                                                    name="credit_memo" placeholder="Enter memo"
                                                    value="<?= htmlspecialchars($check->memo) ?>" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- SUMMARY SECTION -->
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
                                                name="gross_amount" value="<?= number_format($check->gross_amount, 2, '.', ',') ?>" readonly>
                                        </div>
                                    </div>
                                    <!-- DISCOUNT -->
                                    <div class="row">
                                        <label class="col-sm-6 col-form-label">Discount:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control-plaintext text-end" id="discount_amount"
                                                name="discount_amount"
                                                value="<?= number_format($check->discount_amount, 2, '.', ',') ?>" readonly> 
                                            <input type="text" class="form-control" name="discount_account_id"
                                                id="discount_account_id" hidden>
                                        </div>
                                    </div>
                                    <!-- NET AMOUNT DUE -->
                                    <div class="row">
                                        <label class="col-sm-6 col-form-label">Net Amount:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control-plaintext text-end" id="net_amount_due"
                                                name="net_amount_due" value="<?= number_format($check->net_amount_due, 2, '.', ',') ?>" readonly> 
                                        </div>
                                    </div>
                                    <!-- VAT PERCENTAGE -->
                                    <div class="row">
                                        <label class="col-sm-6 col-form-label">Input Vat:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control-plaintext text-end"
                                                id="vat_percentage_amount" name="vat_percentage_amount"
                                                value="<?= number_format($check->vat_percentage_amount, 2, '.', ',') ?>" readonly>
                                            <input type="text" class="form-control" name="vat_account_id" id="vat_account_id"
                                                hidden>
                                        </div>
                                    </div>
                                    <!-- NET OF VAT -->
                                    <div class="row">
                                        <label class="col-sm-6 col-form-label">Taxable Amount:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control-plaintext text-end" id="net_of_vat"
                                                name="net_of_vat" 
                                                value="<?= number_format($check->net_of_vat, 2, '.', ',') ?>" readonly>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <label class="col-sm-6 col-form-label">Tax Withheld (%):</label>
                                        <div class="col-sm-6">
                                            <select class="form-control form-control-sm" id="tax_withheld_percentage"
                                                name="tax_withheld_percentage" disabled>
                                                <?php foreach ($wtaxes as $wtax): ?>
                                                    <option value="<?= htmlspecialchars($wtax->wtax_rate) ?>"
                                                        data-account-id="<?= htmlspecialchars($wtax->wtax_account_id) ?>"
                                                        <?= $wtax->wtax_rate == $check->tax_withheld_amount ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($wtax->wtax_name) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- TAX WITHHELD -->
                                    <div class="row">
                                        <label class="col-sm-6 col-form-label">Tax Withheld:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control-plaintext text-end" id="tax_withheld_amount"
                                                name="tax_withheld_amount" 
                                                value="<?= number_format($check->tax_withheld_amount, 2, '.', ',') ?>" readonly>
                                                <input type="hidden" class="form-control" name="tax_withheld_account_id"
                                                id="tax_withheld_account_id">
                                        </div>
                                    </div>
                                    <!-- TOTAL PAYABLE -->
                                    <div class="row">
                                        <label class="col-sm-6 col-form-label">Total Payable:</label>
                                        <div class="col-sm-6">
                                            <input type="text" class="form-control-plaintext text-end" id="total_amount_due"
                                                name="total_amount_due"
                                                value="<?= number_format($check->total_amount_due, 2, '.', ',') ?>" readonly>
                                        </div>
                                    </div>
                                    <!-- Submit Button -->
                                    <div class="card-footer d-flex justify-content-center">
                                            <?php if ($check->status == 4): ?>
                                                <!-- Buttons to show when invoice_status is 4 -->
                                                <button type="submit" class="btn btn-info me-2">Save and Print</button>
                                            <?php elseif ($check->status == 3): ?>
                                                <!-- Button to show when invoice_status is 3 -->
                                                <a class="btn btn-primary" href="#" id="reprintButton">
                                                    <i class="fas fa-print"></i> Reprint
                                                </a>
                                            <?php else: ?>
                                                <!-- Buttons to show when invoice_status is neither 3 nor 4 -->
                                                <button type="button" class="btn btn-secondary btn-sm" id="voidButton">Void</button>
                                                <a class="btn btn-primary btn-sm" href="#" id="reprintButton">
                                                    <i class="fas fa-print"></i> Reprint
                                                </a>
                                                <a class="btn btn-success btn-sm" href="#" id="printChequeButton">
                                                    <i class="fas fa-print"></i> Print Cheque
                                                </a>
                                                <a class="btn btn-danger btn-sm" href="#" id="printCrossButton">
                                                    <i class="fas fa-print"></i> Print Cross Cheque
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <!-- CHECK ITEMS -->
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
                                                    </tr>
                                                </thead>
                                                <tbody id="itemTableBody" style="font-size: 14px;">
                                                    <!-- Existing rows or dynamically added rows will be appended here -->
                                                    <?php
                                                    if ($check) {
                                                        foreach ($check->details as $detail) {
                                                    ?>
                                                            <tr>
                                                                <td>
                                                                    <select
                                                                        class="form-control form-control-sm account-dropdown select2"
                                                                        name="account_code[]" disabled>
                                                                        <?php foreach ($accounts as $acc): ?>
                                                                            <option value="<?= htmlspecialchars($acc->id) ?>"
                                                                                <?= ($acc->id == $detail['account_id']) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($acc->account_code . ' - ' . $acc->account_description) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </td>

                                                                <td>
                                                                    <select class="form-control form-control-sm cost-dropdown select2"
                                                                        name="cost_center_id[]" disabled>
                                                                        <?php foreach ($cost_centers as $cost_center): ?>
                                                                            <option value="<?= htmlspecialchars($cost_center->id) ?>"
                                                                                <?= ($cost_center->id == $detail['cost_center_id']) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($cost_center->code . ' - ' . $cost_center->particular) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm memo"
                                                                        name="memo[]" placeholder="Enter memo"
                                                                        value="<?= htmlspecialchars($detail['memo'] ?? $check->memo) ?>" disabled>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm amount"
                                                                        name="amount[]" placeholder="Enter amount"
                                                                        value="<?= number_format($detail['amount'], 2, '.', ',') ?>" disabled>
                                                                </td>

                                                                <td>
                                                                    <select
                                                                        class="form-control form-control-sm discount-dropdown select2"
                                                                        name="discount_percentage[]" disabled>
                                                                        <?php foreach ($discounts as $discount): ?>
                                                                            <option
                                                                                value="<?= htmlspecialchars($discount->discount_rate) ?>"
                                                                                <?= ($discount->discount_rate == $detail['discount_percentage']) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($discount->discount_description) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </td>

                                                                <td>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm discount-amount"
                                                                        name="discount_amount[]" placeholder=""
                                                                        value="<?= number_format($detail['discount_amount'], 2, '.', ',') ?>"
                                                                        readonly>
                                                                </td>

                                                                <td>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm net-amount-before-vat"
                                                                        name="net_amount_before_vat[]" placeholder=""
                                                                        value="<?= number_format($detail['net_amount_before_vat'], 2, '.', ',') ?>"
                                                                        readonly>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm net-amount"
                                                                        name="net_amount[]" placeholder=""
                                                                        value="<?= number_format($detail['net_amount'], 2, '.', ',') ?>" readonly>
                                                                </td>

                                                                <td>
                                                                    <select class="form-control form-control-sm vat_percentage select2"
                                                                        name="vat_percentage[]" disabled>
                                                                        <?php foreach ($input_vats as $vat): ?>
                                                                            <option value="<?= htmlspecialchars($vat->input_vat_rate) ?>"
                                                                                <?= ($vat->input_vat_rate == $detail['vat_percentage']) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($vat->input_vat_name) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm input-vat"
                                                                        name="vat_amount[]" placeholder=""
                                                                        value="<?= number_format($detail['input_vat'], 2, '.', ',') ?>" readonly>
                                                                </td>
                                                            </tr>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            <?php } else { ?>
                <div class="alert alert-danger" role="alert">
                    Check not found.
                </div>
            <?php }
        } else { ?>
            <div class="alert alert-danger" role="alert">
                No ID provided.
            </div>
        <?php } ?>
    </main>
</div>
<!-- PRINT -->
<iframe id="printFrame" style="display:none;"></iframe>
<div id="loadingOverlay" class="loading-overlay">
    <div class="spinner"></div>
</div>
<?php require 'views/templates/footer.php' ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Attach event listener for the reprint button
        document.getElementById('reprintButton').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Reprint Write Check?',
                text: "Are you sure you want to reprint this write check?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reprint it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    printCheck(<?= $check->id ?>, 2); // Pass 2 for reprint
                }
            });
        });

        // Attach event listener for the void button
        document.getElementById('voidButton').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Void Write Check?',
                text: "Are you sure you want to void this write check? This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, void it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    voidCheck(<?= $check->id ?>);
                }
            });
        });

        // Attach event listener for the print cheque button
        document.getElementById('printChequeButton').addEventListener('click', function(e) {
            e.preventDefault();
            printCheque(<?= $check->id ?>);
        });

        document.getElementById('printCrossButton').addEventListener('click', function(e) {
            e.preventDefault();
            printCrossCheque(<?= $check->id ?>);
        });
    });

    function showLoadingOverlay() {
        document.getElementById('loadingOverlay').style.display = 'flex';
    }

    function hideLoadingOverlay() {
        document.getElementById('loadingOverlay').style.display = 'none';
    }

    function printCheck(id, printStatus) {
        showLoadingOverlay();

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
                    const printFrame = document.getElementById('printFrame');
                    const printContentUrl = `print_check?action=print&id=${id}`;

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
            url: 'api/write_check_controller.php',
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
                        text: 'Check has been voided successfully.'
                    }).then(() => {
                        location.reload(); // Reload the page to reflect changes
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to void check: ' + (response.message || 'Unknown error')
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                hideLoadingOverlay(); // Hide the loading overlay on error
                console.error('AJAX error:', textStatus, errorThrown);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while voiding the check: ' + textStatus
                });
            }
        });
    }

    function printCheque(id) {
        Swal.fire({
            title: 'Print Cheque?',
            text: "Are you sure you want to print this cheque?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, print it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // If confirmed, open the print_cheque.php in a new window
                const printFrame = document.getElementById('printFrame');
                const printContentUrl = `print_cheque?action=print&id=${id}`;

                printFrame.src = printContentUrl;

                printFrame.onload = function() {
                    printFrame.contentWindow.focus();
                    printFrame.contentWindow.print();
                    hideLoadingOverlay();
                };
            }
        });
    }

    function printCrossCheque(id) {
        Swal.fire({
            title: 'Print Cross Cheque?',
            text: "Are you sure you want to print this with cross cheque?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, print it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // If confirmed, open the print_cheque.php in a new window
                const printFrame = document.getElementById('printFrame');
                const printContentUrl = `print_cross_cheque?action=print&id=${id}`;

                printFrame.src = printContentUrl;

                printFrame.onload = function() {
                    printFrame.contentWindow.focus();
                    printFrame.contentWindow.print();
                    hideLoadingOverlay();
                };
            }
        });
    }
</script>


<script>
    $(document).ready(function() {
        // Data from server (ideally, this should be fetched via an API)
        var customers = <?= json_encode($customers) ?>;
        var vendors = <?= json_encode($vendors) ?>;
        var otherNames = <?= json_encode($other_names) ?>;

        const payeeTypeSelect = $('#payee_type');
        const payeeDropdown = $('#payee_id');
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
            updatePayeeAddress(payeeType, payeeId);
        }

        function resetPayeeDropdown() {
            payeeDropdown.empty().append('<option value="">Select Payee</option>');
            payeeAddressInput.val('');
        }

        function populatePayeeDropdown(payeeType) {
            const data = getDataByPayeeType(payeeType);
            data.forEach(item => {
                payeeDropdown.append(`<option value="${item.id}">${item.name}</option>`);
            });
        }

        function updatePayeeAddress(payeeType, payeeId) {
            const data = getDataByPayeeType(payeeType);
            const selectedPayee = data.find(item => item.id == payeeId);
            const address = selectedPayee ? selectedPayee.address : '';
            payeeAddressInput.val(address);
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

        // Initialize Select2 for existing dropdowns in the table
        $('#itemTableBody').find('.select2').select2({
            width: '100%',
            theme: 'classic' // Adjust this if using a different Bootstrap version
        });

        // Initialize Select2 for customer and credit account
        $('#bank_account_id').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            allowClear: false
        });

        $('#payee_id').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            allowClear: false
        });

        $('#payee_type').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            allowClear: false
        });

        $('#tax_withheld_percentage').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            allowClear: false
        });

        document.getElementById('tax_withheld_percentage').addEventListener('change', function () {
            var selectedOption = this.options[this.selectedIndex];
            var accountId = selectedOption.getAttribute('data-account-id');
            console.log(accountId);
            document.getElementById('tax_withheld_account_id').value = accountId;
        });

        // Variables to hold options for dropdowns
        const accountDropdownOptions = <?php echo json_encode(array_map(function ($acc) {
                                            return ['id' => $acc->id, 'text' => htmlspecialchars($acc->account_code . ' - ' . $acc->account_description)];
                                        }, $accounts)); ?>.map(account => `<option value="${account.id}">${account.text}</option>`).join('');

        const discountDropdownOptions = <?php echo json_encode(array_map(function ($discount) {
                                            return ['rate' => $discount->discount_rate, 'text' => htmlspecialchars($discount->discount_description)];
                                        }, $discounts)); ?>.map(discount => `<option value="${discount.rate}">${discount.text}</option>`).join('');

        const vatDropdownOptions = <?php echo json_encode(array_map(function ($vat) {
                                        return ['rate' => $vat->input_vat_rate, 'text' => htmlspecialchars($vat->input_vat_name)];
                                    }, $input_vats)); ?>.map(vat => `<option value="${vat.rate}">${vat.text}</option>`).join('');

        // Populate dropdowns with cost centers from PHP
        const costCenterOptions = <?php echo json_encode($cost_centers); ?>;
        let costCenterDropdownOptions = '';
        costCenterOptions.forEach(function(cost) {
            costCenterDropdownOptions += `<option value="${cost.id}">${cost.code} - ${cost.particular}</option>`;
        });

        // Function to calculate net amount
        function calculateNetAmount() {
            let totalAmount = 0;
            let totalDiscountAmount = 0;
            let totalNetAmount = 0;
            let totalVat = 0;
            let totalTaxableAmount = 0;
            let totalAmountDue = 0;

            $('#itemTableBody tr').each(function() {
                const amount = parseFloat($(this).find('.amount').val()) || 0;
                const discountPercentage = parseFloat($(this).find('.discount-dropdown').val()) || 0;
                const vatPercentage = parseFloat($(this).find('.vat_percentage').val()) || 0;

                const discountAmount = (discountPercentage / 100) * amount;
                $(this).find('.discount-amount').val(discountAmount.toFixed(2)); // Update discount amount field

                const netAmountBeforeVAT = amount - discountAmount;
                $(this).find('.net-amount-before-vat').val(netAmountBeforeVAT.toFixed(2)); // Update net amount before VAT field

                const vatPercentageAmount = vatPercentage / 100;
                const netAmount = netAmountBeforeVAT / (1 + vatPercentageAmount);
                $(this).find('.net-amount').val(netAmount.toFixed(2)); // Update net amount field

                const vatAmount = netAmountBeforeVAT - netAmount;
                $(this).find('.input-vat').val(vatAmount.toFixed(2)); // Update VAT amount field

                totalAmount += amount;
                totalDiscountAmount += discountAmount;
                totalNetAmount += netAmountBeforeVAT;
                totalVat += vatAmount;
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

        // Event listeners
        $('#itemTableBody').on('input', '.amount, .discount-dropdown, .vat_percentage', function() {
            calculateNetAmount();
        });

        $('#tax_withheld_percentage').on('change', function() {
            calculateNetAmount();
        });

        $('#addItemBtn').click(function() {
            addRow();
        });

        $('#itemTableBody').on('click', '.removeRow', function() {
            $(this).closest('tr').remove();
            calculateNetAmount();
        });

        // Function to add a new row
        function addRow() {
            const newRow = `
                <tr>
                    <td><select class="form-control form-control-sm account-dropdown select2" name="account_code[]" required>${accountDropdownOptions}</select></td>
                    <td><select class="form-control form-control-sm cost-dropdown select2" name="cost_center_id[]">${costCenterDropdownOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm memo" name="memo[]" placeholder="Enter memo"></td>
                    <td><input type="text" class="form-control form-control-sm amount" name="amount[]" placeholder="Enter amount"></td>
                    <td><select class="form-control form-control-sm discount-dropdown select2" name="discount_percentage[]">${discountDropdownOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm discount-amount" name="discount_amount[]" placeholder="" readonly></td>
                    <td><input type="text" class="form-control form-control-sm net-amount-before-vat" name="net_amount_before_vat[]" placeholder="" readonly></td>
                    <td><input type="text" class="form-control form-control-sm net-amount" name="net_amount[]" placeholder="" readonly></td>
                    <td><select class="form-control form-control-sm vat_percentage select2" name="vat_percentage[]">${vatDropdownOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm input-vat" name="vat_amount[]" placeholder="" readonly></td>
                    <td><button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-trash"></i></button></td>
                </tr>`;

            $('#itemTableBody').append(newRow);

            // Initialize Select2 on the new dropdowns
            $('#itemTableBody tr:last-child').find('.select2').select2({
                width: '100%',
                theme: 'classic' // Use this if you're using Bootstrap 4
            });

        }

        // Gather table items and submit form
        function gatherTableItems() {
            const items = [];
            $('#itemTableBody tr').each(function() {
                const item = {
                    account_id: $(this).find('.account-dropdown').val(),
                    cost_center_id: $(this).find('.cost-dropdown').val(),
                    memo: $(this).find('.memo').val(),
                    amount: $(this).find('.amount').val(),
                    discount_percentage: $(this).find('.discount-dropdown').val(),
                    discount_amount: $(this).find('.discount-amount').val(),
                    net_amount_before_vat: $(this).find('.net-amount-before-vat').val(),
                    net_amount: $(this).find('.net-amount').val(),
                    vat_percentage: $(this).find('.vat_percentage').val(),
                    input_vat: $(this).find('.input-vat').val(),
                    discount_account_id: $(this).find('.discount-dropdown option:selected').data('account-id'),
                    input_vat_account_id: $(this).find('.vat_percentage option:selected').data('account-id')
                };

                items.push(item);
            });
            return items;
        }

        $('#writeCheckForm').submit(function(event) {
            event.preventDefault();

            // Check if the table has any rows
            if ($('#itemTableBody tr').length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Items',
                    text: 'You must add at least one item before submitting the wchecks.'
                });
                return false;
            }

            const items = gatherTableItems();
            $('#item_data').val(JSON.stringify(items));
            const wcheckStatus = <?= json_encode($check->status) ?>;
            const id = <?= json_encode($check->id) ?>;


            if (wcheckStatus == 4) {
                // Show loading overlay
                document.getElementById('loadingOverlay').style.display = 'flex';

                const formData = {
                    action: 'update_draft',
                    id: id,
                    cv_no: $('#cv_no').val(),
                    item_data: JSON.stringify(items)
                };

                $.ajax({
                    url: 'api/write_check_controller.php',
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    success: function(response) {
                        document.getElementById('loadingOverlay').style.display = 'none';
                        console.log('Response:', response);  // Log the response
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Write Check submitted successfully!',
                                showCancelButton: true,
                                confirmButtonText: 'Print',
                                cancelButtonText: 'Close'
                            }).then((result) => {
                                if (result.isConfirmed && response.checkId) {
                                    printCheck(response.checkId, 1); // Pass 1 for initial print
                                } else {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error saving write check: ' + (response.message || 'Unknown error')
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
                            text: 'An error occurred while saving the write check: ' + textStatus
                        });
                    }
                });
            }
        });

        function printCheck(id, printStatus) {
            showLoadingOverlay();

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
                        const printFrame = document.getElementById('printFrame');
                        const printContentUrl = `print_check?action=print&id=${id}`;

                        printFrame.src = printContentUrl;

                        printFrame.onload = function() {
                            console.log('Print frame loaded, attempting to print...');
                            hideLoadingOverlay();
                            printFrame.contentWindow.focus();
                            printFrame.contentWindow.print();
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
    });
</script>