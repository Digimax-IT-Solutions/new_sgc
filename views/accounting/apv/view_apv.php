<?php
//Guard
include '_guards.php';

Guard::adminOnly();
$accounts = ChartOfAccount::all();
$vendors = Vendor::all();
$cost_centers = CostCenter::all();
$discounts = Discount::all();
$wtaxes = WithholdingTax::all();
$input_vats = InputVat::all();
$terms = Term::all();

$newAPVoucherNo = Apv::getLastApvNo();

?>

<?php include 'views/templates/header.php' ?>
<?php include 'views/templates/sidebar.php' ?>

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

    #itemTable {
        table-layout: fixed;
    }
</style>

<div class="main">
    <?php include 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="h3"><strong>View APV Expense</strong></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="accounts_payable_voucher">APV</a></li>
                            <li class="breadcrumb-item active" aria-current="page">View AP VOUCHER</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <?php if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $apv = Apv::find($id);
                if ($apv) { ?>
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
                                                        <input type="text" class="form-control form-control-sm" id="apv_no" name="apv_no"
                                                        <?php if ($apv->status == 4): ?>
                                                    value="<?php echo htmlspecialchars($newAPVoucherNo); ?>" readonly>
                                                <?php else: ?>
                                                    value="<?php echo htmlspecialchars($apv->apv_no); ?>" disabled>
                                                <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mt-3">
                                                <!-- SELECT APV ACCOUNT -->
                                                <div class="col-md-4 customer-details">
                                                    <div class="form-group">
                                                        <label for="account_id">A/P Account</label>
                                                        <select class="form-control form-control-sm select2" id="account_id" name="account_id" disabled>
                                                            <option value="<?= $apv->account_id ?>">
                                                                <?= $apv->account_name ?>
                                                            </option>
                                                            <?php foreach ($accounts as $account) : ?>
                                                                <?php if ($account->account_type == 'Accounts Payable') : ?>
                                                                    <option value="<?= $account->id ?>" <?= ($account->id == $apv->account_id) ? 'selected' : '' ?>>
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
                                                        <label for="vendor_id">Vendor<span class="text-muted" id="payee_type_display"></span></label>
                                                        <select class="form-control form-control-sm select2" id="vendor_id" name="vendor_id" disabled>
                                                            <option value="<?= $apv->vendor_id ?>">
                                                                <?= $apv->vendor_name ?>
                                                            </option>
                                                            <?php foreach ($vendors as $vendor) : ?>
                                                                <option value="<?= $vendor->id ?>" data-name="<?= $vendor->vendor_name ?>">
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
                                                    <input type="text" class="form-control form-control-md" id="ref_no" name="ref_no" placeholder="Enter ref doc" value="<?= $apv->ref_no ?>" disabled>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <!-- SELECT DATE -->
                                                <div class="form-group">
                                                    <label for="apv_date">Date</label>
                                                    <input type="date" class="form-control form-control-md" id="apv_date" name="apv_date" value="<?= $apv->apv_date ?>" disabled>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label for="terms_id">Terms</label>
                                                    <select class="form-select form-select-md" id="terms_id" name="terms_id" disabled>
                                                        <option value="<?= $apv->terms_id ?>">
                                                            <?= $apv->terms_id ?>
                                                        </option>
                                                        <?php foreach ($terms as $term) : ?>
                                                            <option value="<?= $term->id ?>" data-days="<?= $term->term_days_due ?>"><?= $term->term_name ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-3">
                                                <!-- SELECT DATE -->
                                                <div class="form-group">
                                                    <label for="apv_due_date">Due Date</label>
                                                    <input type="date" class="form-control form-control-md" id="apv_due_date" name="apv_due_date" value="<?= htmlspecialchars($apv->apv_due_date) ?>" disabled>
                                                </div>
                                            </div>

                                            <div class="col-md-8 order-details mt-5">
                                                <!-- MEMO -->
                                                <div class="form-group">
                                                    <label for="memo">Memo</label>
                                                    <input type="text" class="form-control form-control-md" id="memo" name="memo" value="<?= htmlspecialchars($apv->memo) ?>" disabled>
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
                                        <?php if ($apv->status == 0): ?>
                                            <span class="badge bg-danger">Unpaid</span>
                                        <?php elseif ($apv->status == 1): ?>
                                            <span class="badge bg-success">Paid</span>
                                        <?php elseif ($apv->status == 2): ?>
                                            <span class="badge bg-warning">Partially Paid</span>
                                        <?php elseif ($apv->status == 3): ?>
                                        <span class="badge bg-secondary">Void</span>
                                        <?php elseif ($apv->status == 4): ?>
                                            <span class="badge bg-info text-dark">Draft</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-check form-switch mb-2">
                                            <!-- Placeholder for potential future features -->
                                        </div>

                                        <!-- GROSS AMOUNT -->
                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Gross Amount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="gross_amount" name="gross_amount" value="<?= htmlspecialchars(number_format($apv->gross_amount, 2)) ?>" readonly>
                                            </div>
                                        </div>

                                        <!-- DISCOUNT -->
                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Discount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="discount_amount" name="discount_amount" value="<?= htmlspecialchars(number_format($apv->discount_amount, 2)) ?>" readonly>
                                                <input type="text" class="form-control" name="discount_account_id" id="discount_account_id" hidden>
                                            </div>
                                        </div>

                                        <!-- NET AMOUNT DUE -->
                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Net Amount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="net_amount_due" name="net_amount_due" value="<?= htmlspecialchars(number_format($apv->net_amount_due, 2)) ?>" readonly>
                                            </div>
                                        </div>

                                        <!-- VAT PERCENTAGE -->
                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Input Vat:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="vat_percentage_amount" name="vat_percentage_amount" value="<?= htmlspecialchars(number_format($apv->vat_percentage_amount, 2)) ?>" readonly>
                                                <input type="text" class="form-control" name="vat_account_id" id="vat_account_id" hidden>
                                            </div>
                                        </div>

                                        <!-- NET OF VAT -->
                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Taxable Amount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="net_of_vat" name="net_of_vat" value="<?= htmlspecialchars(number_format($apv->net_of_vat, 2)) ?>" readonly>
                                            </div>
                                        </div>

                                        <!-- TAX WITHHELD -->
                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Tax Withheld (%):</label>
                                            <div class="col-sm-6">
                                                <select class="form-select form-select-sm" id="tax_withheld_percentage" name="tax_withheld_percentage" disabled>
                                                    <?php foreach ($wtaxes as $wtax) : ?>
                                                        <option value="<?= $wtax->wtax_rate ?>" data-account-id="<?= $wtax->wtax_account_id ?>">
                                                            <?= $wtax->wtax_name ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Tax Withheld Amount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="tax_withheld_amount" name="tax_withheld_amount" value="<?= htmlspecialchars(number_format($apv->tax_withheld_amount, 2)) ?>" readonly>
                                                <input type="hidden" class="form-control" name="tax_withheld_account_id" id="tax_withheld_account_id">
                                            </div>
                                        </div>

                                        <!-- TOTAL AMOUNT DUE -->
                                        <div class="row">
                                            <label class="col-sm-6 col-form-label fw-bold">Total Amount Due:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end fw-bold" id="total_amount_due" name="total_amount_due" value="<?= htmlspecialchars(number_format($apv->total_amount_due, 2)) ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer d-flex justify-content-center">
                                        <?php if ($apv->status == 4): ?>
                                            <!-- Buttons to show when invoice_status is 4 -->
                                            <button type="submit" class="btn btn-info me-2">Save and Print</button>
                                        <?php elseif ($apv->status == 3): ?>
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
                                                    <?php
                                                    if ($apv) {
                                                        foreach ($apv->details as $detail) {
                                                    ?>
                                                            <tr>
                                                                <td>
                                                                    <select class="form-control form-control-sm account-dropdown select2" name="account_code[]" disabled>
                                                                        <?php foreach ($accounts as $acc) : ?>
                                                                            <option value="<?= htmlspecialchars($acc->id) ?>" <?= ($acc->id == $detail['account_id']) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($acc->account_code . ' - ' . $acc->account_description) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </td>

                                                                <td>
                                                                    <select class="form-control form-control-sm cost-dropdown select2" name="cost_center_id[]" disabled>
                                                                        <?php foreach ($cost_centers as $cost_center) : ?>
                                                                            <option value="<?= htmlspecialchars($cost_center->id) ?>" <?= ($cost_center->id == $detail['cost_center_id']) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($cost_center->code . ' - ' . $cost_center->particular) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm memo" name="memo[]" placeholder="Enter memo" value="<?= htmlspecialchars($detail['memo']) ?>">
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm amount" name="amount[]" placeholder="Enter amount" value="<?= htmlspecialchars($detail['amount']) ?>">
                                                                </td>

                                                                <td>
                                                                    <select class="form-control form-control-sm discount-dropdown select2" name="discount_percentage[]" disabled>
                                                                        <?php foreach ($discounts as $discount) : ?>
                                                                            <option value="<?= htmlspecialchars($discount->discount_rate) ?>" <?= ($discount->discount_rate == $detail['discount_percentage']) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($discount->discount_description) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm discount-amount" name="discount_amount[]" placeholder="" value="<?= htmlspecialchars($detail['discount_amount']) ?>" readonly>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm net-amount-before-vat" name="net_amount_before_vat[]" placeholder="" value="<?= htmlspecialchars($detail['net_amount_before_vat']) ?>" readonly>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm net-amount" name="net_amount[]" placeholder="" value="<?= htmlspecialchars($detail['net_amount']) ?>" readonly>
                                                                </td>

                                                                <td>
                                                                    <select class="form-control form-control-sm vat_percentage select2" name="vat_percentage[]" disabled>
                                                                        <?php foreach ($input_vats as $vat) : ?>
                                                                            <option value="<?= htmlspecialchars($vat->input_vat_rate) ?>" <?= ($vat->input_vat_rate == $detail['vat_percentage']) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($vat->input_vat_name) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm input-vat" name="vat_amount[]" placeholder="" value="<?= htmlspecialchars($detail['input_vat']) ?>" readonly>
                                                                </td>

                                                                <td>
                                                                    <button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-trash"></i></button>
                                                                </td>
                                                            </tr>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
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
                    </form>
            <?php
                    // Invoice found, you can now display the details
                } else {
                    // Handle the case where the invoice is not found
                    echo "APV not found.";
                    exit;
                }
            } else {
                // Handle the case where the ID is not provided
                echo "No ID provided.";
                exit;
            }
            ?>
        </div>
    </main>
    <div id="loadingOverlay" style="display: none;">
        <div class="spinner"></div>
        <div class="message">Processing wchecks</div>
    </div>
</div>



<?php require 'views/templates/footer.php' ?>

<iframe id="printFrame" style="display:none;"></iframe>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('reprintButton').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Reprint APV?',
                text: "Are you sure you want to reprint this apv?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reprint it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    printApv(<?= $apv->id ?>, 2); // Pass 2 for reprint
                }
            });
        });

        // Attach event listener for the void button
        document.getElementById('voidButton').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Void APV?',
                text: "Are you sure you want to void this APV? This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, void it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    voidApv(<?= $apv->id ?>);
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

    function printApv(id, printStatus) {
        showLoadingOverlay();

        $.ajax({
            url: 'api/apv_controller.php',
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
                    const printContentUrl = `print_apv?action=print&id=${id}`;

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

    function voidApv(id) {
        showLoadingOverlay();

        $.ajax({
            url: 'api/apv_controller.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'void_apv',
                id: id
            },
            success: function(response) {
                hideLoadingOverlay();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'APV has been voided successfully.'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to void APV: ' + (response.message || 'Unknown error')
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                hideLoadingOverlay();
                console.error('AJAX error:', textStatus, errorThrown);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while voiding the APV: ' + textStatus
                });
            }
        });
    }

</script>

<script>
    $(document).ready(function() {

        $('#vendor_id').on('select2:select', function(e) {
            var data = e.params.data;
            $('#vendor_name').val($(data.element).data('name'));
        });

        document.getElementById('tax_withheld_percentage').addEventListener('change', function() {
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

        $('#itemTableBody').find('.select2').select2({
            width: '100%',
            theme: 'classic' // Adjust this if using a different Bootstrap version
        });

        // Populate dropdowns with accounts from PHP
        const accounts = <?php echo json_encode($accounts); ?>;
        let accountDropdownOptions = '';
        $.each(accounts, function(index, account) {
            accountDropdownOptions += `<option value="${account.id}">${account.account_description}</option>`;
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
        $('#apvForm').submit(function(event) {
            event.preventDefault();
            
            // Check if the table has any rows
            if ($('#itemTableBody tr').length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Items',
                    text: 'You must add at least one item before submitting the invoice.'
                });
                return false;
            }

            const items = gatherTableItems();
            $('#item_data').val(JSON.stringify(items));
            const apvStatus = <?= json_encode($apv->status) ?>;
            const apvId = <?= json_encode($apv->id) ?>;

            if (apvStatus == 4) {
                // Show loading overlay
                document.getElementById('loadingOverlay').style.display = 'flex';
                const formData = {
                    action: 'update_draft',
                    id: apvId,
                    apv_no: $('#apv_no').val(),
                    item_data: JSON.stringify(items)
                };

                $.ajax({
                    url: 'api/apv_controller.php',
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    success: function(response) {
                        document.getElementById('loadingOverlay').style.display = 'none';
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'APV submitted successfully!',
                                showCancelButton: true,
                                confirmButtonText: 'Print',
                                cancelButtonText: 'Close'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    printApv(apvId, 1); // Pass 1 for initial print
                                } else {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error saving APV: ' + (response.message || 'Unknown error')
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        document.getElementById('loadingOverlay').style.display = 'none';
                        console.error('AJAX error:', textStatus, errorThrown);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while saving the APV: ' + textStatus
                        });
                    }
                });
            }
        });
    });
</script>