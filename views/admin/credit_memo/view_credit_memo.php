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
$products = Product::all();
$terms = Term::all();
$locations = Location::all();
$payment_methods = PaymentMethod::all();
$wtaxes = WithholdingTax::all();
$credit_memo = CreditMemo::all();


$output_vats = SalesTax::all();

$discounts = Discount::all();
$input_vats = InputVat::all();
$sales_taxes = SalesTax::all();
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
                        <h1 class="h3"><strong>View Credit Memo</strong></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="credit_memo">Credit Memo</a></li>
                                <li class="breadcrumb-item active" aria-current="page">View Credit Memo</li>
                            </ol>
                        </nav>
                    </div>
                <div>
                    <a href="credit_memo" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Credits List
                    </a>
                </div>
            </div>
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
                                <!-- Credit Details Section -->
                                <div class="col-12 col-lg-8">
                                    <div class="card h-100">
                                        <div class="card-header">
                                            <h5 class="card-title mb-0">Credit Memo Details</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-2">
                                                <div class="col-12 mb-3">
                                                    <h6 class="border-bottom pb-2">Customer Details</h6>
                                                </div>
                                                <div class="row">
                                                    <!-- CREDIT NO -->
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="credit_no">Credit No</label>
                                                            <input type="text" class="form-control form-control-sm" id="credit_no" name="credit_no"
                                                                <?php if ($credit_memo->status == 4): ?>
                                                                    value="<?php echo htmlspecialchars($newCreditNo); ?>" readonly>
                                                                <?php else: ?>
                                                                    value="<?php echo htmlspecialchars($credit_memo->credit_no); ?>" disabled>
                                                                <?php endif; ?>
                                                        </div>
                                                    </div>

                                                    <!-- DATE -->
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="credit_date">Date</label>
                                                            <input type="date" class="form-control form-control-sm" id="credit_date" name="credit_date"
                                                                value="<?= date('Y-m-d', strtotime($credit_memo->credit_date)) ?>"
                                                                <?php echo ($credit_memo->status == 4) ? '' : 'disabled'; ?>>
                                                        </div>
                                                    </div>

                                                    <!-- ACCOUNT -->
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="credit_account_id">Account</label>
                                                            <select class="form-control form-control-sm select2" id="credit_account_id" name="credit_account_id" 
                                                                <?php if ($credit_memo->status != 4) echo 'disabled'; ?>>
                                                                <?php
                                                                // Prevent duplicates
                                                                $used_accounts = [];
                                                                $selected_account = $credit_memo->credit_account_id ?? '';

                                                                foreach ($accounts as $account):
                                                                    if ($account->account_type == 'Accounts Receivable' && !in_array($account->account_code, $used_accounts)):
                                                                        $used_accounts[] = $account->account_code;
                                                                ?>
                                                                    <option value="<?= htmlspecialchars($account->id) ?>" 
                                                                        <?= $account->id == $selected_account ? 'selected' : '' ?>>
                                                                        <?= htmlspecialchars($account->account_code . ' - ' . $account->account_description) ?>
                                                                    </option>
                                                                <?php
                                                                    endif;
                                                                endforeach;
                                                                ?>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <!-- CUSTOMER -->
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="customer_id">Customer</label>
                                                            <select class="form-control form-control-sm select2" id="customer_id" name="customer_id"
                                                                <?php echo ($credit_memo->status == 4) ? '' : 'disabled'; ?>>
                                                                <option value="<?= htmlspecialchars($credit_memo->customer_id) ?>" selected>
                                                                    <?= htmlspecialchars($credit_memo->customer_name) ?>
                                                                </option>
                                                                <?php foreach ($customers as $customer): ?>
                                                                    <option value="<?= htmlspecialchars($customer->id) ?>" 
                                                                        <?= $customer->id == $credit_memo->customer_id ? 'selected' : '' ?>>
                                                                        <?= htmlspecialchars($customer->customer_name) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <!-- MEMO -->
                                                    <div class="col-md-4">
                                                        <div class="form-group">
                                                            <label for="credit_memo">Memo</label>
                                                            <input type="text" class="form-control form-control-sm" id="credit_memo" name="credit_memo"
                                                                placeholder="Enter memo" value="<?= htmlspecialchars($credit_memo->memo) ?>"
                                                                <?php echo ($credit_memo->status == 4) ? '' : 'disabled'; ?>>
                                                        </div>
                                                    </div>

                                                    <!-- LOCATION -->
                                                    <div class="col-md-4">
                                                    <div class="form-group">
    <label for="location" class="form-label">Location</label>
    <select class="form-control form-control-sm" id="location" name="location" 
    <?php if ($credit_memo->status != 4) echo 'disabled'; ?>>
    <?php
        // Array to prevent duplicates
        $used_locations = [];
        $selected_location = $credit_memo->location ?? ''; // Assuming this holds the selected location
        // Locations
        foreach ($locations as $location):
            if (!in_array($location->name, $used_locations)):
                $used_locations[] = $location->name; // Track used locations
    ?>
                <option value="<?= htmlspecialchars($location->id) ?>" 
                        <?= $location->id == $selected_location ? 'selected' : '' ?>>
                    <?= htmlspecialchars($location->name) ?>
                </option>
    <?php
            endif;
        endforeach;
    ?>
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
                                            <!-- GROSS AMOUNT -->
                                            <div class="row mb-2">
                                                <label class="col-sm-6 col-form-label">Gross Amount:</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control-plaintext text-end"
                                                        id="gross_amount" name="gross_amount"
                                                        value="<?= number_format($credit_memo->gross_amount, 2, '.', ',') ?>"
                                                        <?php echo ($credit_memo->status == 4) ? '' : 'readonly'; ?>>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="col-sm-6 col-form-label">Net Amount:</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control-plaintext text-end"
                                                        id="net_amount_due" name="net_amount_due"
                                                        value="<?= number_format($credit_memo->net_amount_due, 2, '.', ',') ?>"
                                                        <?php echo ($credit_memo->status == 4) ? '' : 'readonly'; ?>>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="col-sm-6 col-form-label">Output VAT:</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control-plaintext text-end"
                                                        id="vat_percentage_amount" name="vat_percentage_amount"
                                                        value="<?= number_format($credit_memo->vat_percentage_amount, 2, '.', ',') ?>"
                                                        <?php echo ($credit_memo->status == 4) ? '' : 'readonly'; ?>>
                                                    <input type="hidden" class="form-control" name="vat_account_id" id="vat_account_id">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <label class="col-sm-6 col-form-label">Taxable Amount:</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control-plaintext text-end"
                                                        id="net_of_vat" name="net_of_vat"
                                                        value="<?= number_format($credit_memo->net_of_vat, 2, '.', ',') ?>"
                                                        <?php echo ($credit_memo->status == 4) ? '' : 'readonly'; ?>>
                                                </div>
                                            </div>

                                            <?php if ($credit_memo->status == 4): ?>
                                                <!-- Show editable Tax Withheld dropdown when invoice_status is 4 -->
                                                <div class="row">
                                                    <label class="col-sm-6 col-form-label">Tax Withheld (%):</label>
                                                    <div class="col-sm-6">
                                                        <select class="form-select form-select-sm" id="tax_withheld_percentage" name="tax_withheld_percentage">
                                                            <?php
                                                                // Array to prevent duplicates
                                                                $used_wtaxes = [];
                                                                foreach ($wtaxes as $wtax):
                                                                    if (!in_array($wtax->id, $used_wtaxes)):
                                                                        $used_wtaxes[] = $wtax->id; // Track used tax rates
                                                            ?>
                                                                        <option value="<?= htmlspecialchars($wtax->id) ?>" 
                                                                                <?= $wtax->id == $credit_memo->tax_withheld_percentage ? 'selected' : '' ?>>
                                                                            <?= htmlspecialchars($wtax->wtax_name) ?>
                                                                        </option>
                                                            <?php
                                                                    endif;
                                                                endforeach;
                                                            ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php else: ?>
                                                <!-- Show disabled Tax Withheld dropdown when invoice_status is not 4 -->
                                                <div class="row">
                                                    <label class="col-sm-6 col-form-label">Tax Withheld (%):</label>
                                                    <div class="col-sm-6">
                                                        <select class="form-control form-control-sm" id="tax_withheld_percentage"
                                                            name="tax_withheld_percentage" <?php echo ($credit_memo->status != 4) ? 'disabled' : ''; ?>>
                                                            <?php foreach ($wtaxes as $wtax): ?>
                                                                <option value="<?= htmlspecialchars($wtax->id) ?>"
                                                                    <?= $wtax->id == $credit_memo->tax_withheld_percentage ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($wtax->wtax_name) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php endif; ?>

                                            <div class="row">
                                                <label class="col-sm-6 col-form-label">Tax Withheld Amount:</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control-plaintext text-end"
                                                        id="tax_withheld_amount" name="tax_withheld_amount"
                                                        value="<?= number_format($credit_memo->tax_withheld_amount, 2, '.', ',')?>"
                                                        <?php echo ($credit_memo->status == 4) ? '' : 'readonly'; ?>>
                                                    <input type="hidden" class="form-control" name="tax_withheld_account_id" id="tax_withheld_account_id">
                                                </div>
                                            </div>


                                            <!-- TOTAL AMOUNT DUE -->
                                            <div class="row">
                                                <label class="col-sm-6 col-form-label fw-bold">Total Amount Due:</label>
                                                <div class="col-sm-6">
                                                    <input type="text" class="form-control-plaintext text-end fw-bold"
                                                        id="total_amount_due" name="total_amount_due"
                                                        value="<?= number_format($credit_memo->total_amount_due, 2, '.', ',')?>"
                                                        <?php echo ($credit_memo->status == 4) ? '' : 'readonly'; ?>>
                                                </div>
                                            </div>

                                        </div>
                                        <div class="card-footer d-flex justify-content-center">
                                            <?php if ($credit_memo->status == 4): ?>
                                                <!-- Buttons to show when status is 4 -->
                                                <button type="button" id="saveDraftBtn" class="btn btn-secondary me-2">Update Draft</button>
                                                <button type="submit" class="btn btn-info me-2">Save as Final</button>
                                            <?php elseif ($credit_memo->status == 3): ?>
                                                <!-- Button to show when invoice_status is 3 -->
                                                <a class="btn btn-primary" href="#" id="reprintButton">
                                                    <i class="fas fa-print"></i> Reprint
                                                </a>
                                            <?php else: ?>
                                                <!-- Buttons to show when status is neither 3 nor 4 -->
                                                <button type="button" class="btn btn-secondary me-2" id="voidButton">Void</button>
                                                <a class="btn btn-primary" href="#" id="reprintButton">
                                                    <i class="fas fa-print"></i> Reprint
                                                </a>
                                            <?php endif; ?>
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
                                                    <tbody id="itemTableBody" class="table-group-divider table-divider-color">
                                                        <?php if ($credit_memo): ?>
                                                            <?php foreach ($credit_memo->details as $detail): ?>
                                                                <tr>
                                                                    <td>
                                                                        <select class="form-control form-control-sm account-dropdown select2" id="account_id" name="account_id[]" 
                                                                            <?php echo ($credit_memo->status == 4) ? '' : 'disabled'; ?>>
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
                                                                        <select class="form-control form-control-sm cost-dropdown select2" id="cost_center_id" name="cost_center_id[]"
                                                                            <?php echo ($credit_memo->status == 4) ? '' : 'disabled'; ?>>
                                                                            <?php foreach ($cost_centers as $cost_center): ?>
                                                                                <option value="<?= htmlspecialchars($cost_center->id) ?>"
                                                                                    <?= ($cost_center->id == $detail['cost_center_id']) ? 'selected' : '' ?>>
                                                                                    <?= htmlspecialchars($cost_center->code . ' - ' . $cost_center->particular) ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control form-control-sm memo" name="memo[]"
                                                                            value="<?= htmlspecialchars($detail['memo']) ?>" 
                                                                            <?php echo ($credit_memo->status == 4) ? '' : 'disabled'; ?>>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control form-control-sm amount" name="amount[]"
                                                                            placeholder="Enter amount"
                                                                            value="<?= number_format($detail['amount'], 2, '.', ',')?>"
                                                                            <?php echo ($credit_memo->status == 4) ? '' : 'disabled'; ?>>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control form-control-sm net_amount_due" name="net_amount_due[]"
                                                                            value="<?= number_format($detail['amount'], 2, '.', ',')?>"
                                                                            <?php echo ($credit_memo->status == 4) ? '' : 'disabled'; ?>>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control form-control-sm net_of_vat" name="net_of_vat[]"
                                                                        value="<?= number_format($detail['net_amount'], 2, '.', ',')?>"
                                                                        <?php echo ($credit_memo->status == 4) ? '' : 'disabled'; ?>>
                                                                    </td>
                                                                    <td>
                                                                        <select class="form-control form-control-sm input_vat_percentage select2" id="input_vat_percentage" name="input_vat_percentage[]"
                                                                            <?php echo ($credit_memo->status == 4) ? '' : 'disabled'; ?>>
                                                                            <?php foreach ($input_vats as $vat): ?>
                                                                                <option value="<?= htmlspecialchars($vat->input_vat_rate) ?>"
                                                                                    data-account-id="<?= htmlspecialchars($vat->input_vat_account_id) ?>"
                                                                                    <?= ($vat->input_vat_rate == $detail['vat_percentage']) ? 'selected' : '' ?>>
                                                                                    <?= htmlspecialchars($vat->input_vat_name) ?>
                                                                                </option>
                                                                            <?php endforeach; ?>
                                                                        </select>
                                                                    </td>
                                                                    <td>
                                                                        <input type="text" class="form-control form-control-sm input_vat_amount" name="input_vat_amount[]"
                                                                            value="<?= htmlspecialchars($credit_memo->vat_percentage_amount) ?>" 
                                                                            <?php echo ($credit_memo->status == 4) ? '' : 'disabled'; ?>>
                                                                    </td>
                                                                    <td>
                                                                        <button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-trash"></i></button>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
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
                        <?php else: ?>
                    <div class="alert alert-warning" role="alert">
                            Credit memo not found. Please check the ID and try again.
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-warning" role="alert">
                        No credit memo ID provided. Please select a valid credit memo.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

</div>

<div id="loadingOverlay" style="display: none;">
    <div class="spinner"></div>
    <div class="message">Processing Credit Memo</div>
</div>
<iframe id="printFrame" style="display:none;"></iframe>


<?php require 'views/templates/footer.php' ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
            <?php if (isset($credit_memo) && $credit_memo): ?>
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
        <?php endif; ?>
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
    $(document).ready(function () {
        // Constants and cached DOM elements
        const $itemTableBody = $('#itemTableBody');
        const $addItemBtn = $('#addItemBtn');
        const $creditMemoForm = $('#creditMemoForm');
        const $taxWithheldPercentage = $('#tax_withheld_percentage');
        const $loadingOverlay = $('#loadingOverlay');
        const $clearBtn = $('button[type="reset"]');

        // Initialize Select2 for customer and credit account
        $('#customer_id, #credit_account, #location').select2({
            theme: 'classic',
            width: '100%'
        });

        $('#credit_account_id').select2({
            theme: 'classic',
            width: '100%',
            data: <?php echo json_encode(array_map(function($acc) {
                return [
                    'id' => $acc->id,
                    'text' => $acc->account_code . ' - ' . $acc->account_description
                ];
            }, $accounts)); ?>
        });

        $('#account_id, #cost_center_id, #input_vat_percentage').select2({
            theme: 'classic',
            width: '100%'
        });

       
        document.getElementById('tax_withheld_percentage').addEventListener('change', function () {
            var selectedOption = this.options[this.selectedIndex];
            var accountId = selectedOption.getAttribute('data-account-id');
            console.log('Tax Withheld Account ID:', accountId);
            document.getElementById('tax_withheld_account_id').value = accountId;
        });

        // Event listeners
        $addItemBtn.on('click', addRow);


        $itemTableBody.on('click', '.removeRow', function () {
            $(this).closest('tr').remove();
            calculateNetAmount();
        });

        $itemTableBody.on('click', '.removeRow', function () {
            $(this).closest('tr').remove();
            calculateTotals();
        });

        $itemTableBody.on('input', '.amount', calculateTotals);
        $itemTableBody.on('change', '.input_vat_percentage', calculateTotals);
        $taxWithheldPercentage.on('change', calculateTotals);

        $itemTableBody.on('input', '.amount', calculateNetAmount);
        $itemTableBody.on('change', '.vat_percentage', calculateNetAmount);
        $taxWithheldPercentage.on('change', calculateNetAmount);
        $clearBtn.on('click', clearForm);
        $creditMemoForm.on('submit', function (event) {
            event.preventDefault();

            if (!validateForm()) {
                return;
            }
            if ($itemTableBody.find('tr').length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'You must add at least one account before submitting the credit memo.'
                });
                return;
            }

            const tableItems = gatherTableItems();
            console.log('Gathered table items:', tableItems);

            $('#item_data').val(JSON.stringify(tableItems));
            console.log('Set item_data value:', $('#item_data').val());

            // Show loading overlay
            $loadingOverlay.fadeIn();

            // Submit the form data via AJAX
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
                success: function (response) {
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
                error: function (jqXHR, textStatus, errorThrown) {
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

        function calculateTotals() {
            let totalAmount = 0;
            let totalNetAmount = 0;
            let totalVat = 0;
            let totalTaxableAmount = 0;

            $('.amount').each(function () {
                const $row = $(this).closest('tr');
                const amount = parseFloat($(this).val()) || 0;
                const vatPercentage = parseFloat($row.find('.input_vat_percentage').val()) || 0;

                totalAmount += amount;

                const vatPercentageAmount = vatPercentage / 100;
                const netAmount = amount / (1 + vatPercentageAmount);
                const vatAmount = amount - netAmount;

                $row.find('.net_amount_due').val(amount.toFixed(2));
                $row.find('.net_of_vat').val(netAmount.toFixed(2));
                $row.find('.input_vat_amount').val(vatAmount.toFixed(2));

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

            const taxWithheldPercentage = parseFloat($taxWithheldPercentage.val()) || 0;
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
       
        function validateForm() {
            let isValid = true;
            $('.account-dropdown').each(function () {
                if (!$(this).val()) {
                    isValid = false;
                    $(this).addClass('is-invalid');
                } else {
                    $(this).removeClass('is-invalid');
                }
            });
            // Add more validation as needed
            return isValid;
        }

        function clearForm(e) {
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
        costCenterOptions.forEach(function (cost) {
            costCenterDropdownOptions += `<option value="${cost.id}">${cost.code} - ${cost.particular}</option>`;
        });

        function addRow() {
            const newRow = `
            <tr>
                <td>
                    <select class="form-control form-control-sm account-dropdown select2" name="account_id[]">
                        <option value="">Select Account</option>
                        ${accountDropdownOptions}
                    </select>
                </td>
                <td>
                    <select class="form-control form-control-sm cost-dropdown select2" name="cost_center_id[]">
                        ${costCenterDropdownOptions}
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm memo" name="memo[]">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm amount" name="amount[]" placeholder="Enter amount">
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm net_amount_due" name="net_amount_due[]" readonly>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm net_of_vat" name="net_of_vat[]" readonly>
                </td>
                <td>
                    <select class="form-control form-control-sm input_vat_percentage select2" name="input_vat_percentage[]">
                        ${vatDropdownOptions}
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control form-control-sm input_vat_amount" name="input_vat_amount[]" readonly>
                </td>
                <td>
                    <button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-trash"></i></button>
                </td>
            </tr>`;

            $itemTableBody.append(newRow);

            // Initialize Select2 on the new dropdowns
            $itemTableBody.find('.select2').select2({
                theme: 'classic',
                width: '100%'
            });

            console.log('Added a new row');
        }


        function calculateNetAmount() {
            let totalAmount = 0;
            let totalNetAmount = 0;
            let totalVat = 0;
            let totalTaxableAmount = 0;

            $('.amount').each(function () {
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

            const taxWithheldPercentage = parseFloat($taxWithheldPercentage.val()) || 0;
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
            let hasEmptyAccount = false;
            let hasEmptyAmount = false;
            let firstEmptyAccountRow;
            let firstEmptyAmountRow;

            $('#itemTableBody tr').each(function () {
                const account_id =  +$(this).find('.account-dropdown').val() || 0;
                const amount = parseFloat($(this).find('.amount').val()) || 0;

                const item = {
                    account_id:account_id,
                    cost_center_id: $(this).find('.cost-dropdown').val() || '',
                    memo: $(this).find('.memo').val(),
                    amount: amount,
                    net_amount_before_vat: parseFloat($(this).find('.net_amount_due').val()) || 0,
                    net_amount: parseFloat($(this).find('.net_of_vat').val()) || 0,
                    vat_percentage: parseFloat($(this).find('.vat_percentage').val()) || 0,
                    sales_tax: parseFloat($(this).find('.input_vat_amount').val()) || 0,
                    taxable_amount: parseFloat($(this).find('.amount').val()) || 0,
                    sales_tax_account_id: $(this).find('.input_vat_percentage option:selected').data('account-id'),
                    vat_percentage: $(this).find('.input_vat_percentage').val() // This now captures the selected VAT ID
                };
                items.push(item);
            });

             // Show warnings based on which validation failed
             if (hasEmptyAccount) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please select an item.'
                }).then(() => {
                    // Highlight the first row with an empty item
                    firstEmptyAccountRow.find('select[name="item_id[]"]').focus().css('border', '2px solid red');
                });
                return false;
            }

            if (hasEmptyAmount) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning',
                    text: 'Please enter a Gross for every item.'
                }).then(() => {
                    // Highlight the first row with an empty quantity
                    firstEmptyAmountRow.find('input[name="cost[]"]').focus().css('border', '2px solid red');
                });
                return false;
            }

            return items;
        }

        document.getElementById('tax_withheld_percentage').addEventListener('change', function () {
            var selectedWtaxId = this.value;
            console.log('Selected Tax Withheld ID:', selectedWtaxId);
            // If you need to update any other fields based on this selection, do it here
            // For example:
            // document.getElementById('some_other_field').value = selectedWtaxId;
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
            <?php if (isset($credit_memo) && $credit_memo): ?>
            const creditStatus = <?= json_encode($credit_memo->status) ?>;
            const id = <?= json_encode($credit_memo->id) ?>;

            if (creditStatus == 4) {
                // Show loading overlay
                document.getElementById('loadingOverlay').style.display = 'flex';
                
                const formData = {
                    action: 'save_final',
                    id: id,
                    credit_no: $('#credit_no').val(),
                    credit_date: $('#credit_date').val(),
                    customer_id: $('#customer_id').val(),
                    customer_name: $('#customer_id option:selected').text(),
                    credit_account_id: $('#credit_account_id').val(),
                    memo: $('#credit_memo').val(),
                    location: $('#location').val(),
                    gross_amount: $('#gross_amount').val(),
                    net_amount_due: $('#net_amount_due').val(),
                    vat_percentage_amount: $('#vat_percentage_amount').val(),
                    net_of_vat: $('#net_of_vat').val(),
                    tax_withheld_amount: $('#tax_withheld_amount').val(),
                    tax_withheld_account_id: $('#tax_withheld_percentage option:selected').data('account-id'),
                    tax_withheld_percentage: $('#tax_withheld_percentage').val(), // This now sends the ID
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
                                text: 'Failed to submit credit memo: ' + (response.message || 'Unknown error')
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
            <?php else: ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Credit memo not found. Please check the ID and try again.'
            });
            <?php endif; ?>
        });

        $('#saveDraftBtn').on('click', function(e) {
            e.preventDefault();
            updateDraft();
        });

        function updateDraft() {
            if ($('#itemTableBody tr').length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Items',
                    text: 'You must add at least one item before updating the draft.'
                });
                return false;
            }

            const items = gatherTableItems();
            $('#item_data').val(JSON.stringify(items));

            document.getElementById('loadingOverlay').style.display = 'flex';

            const formData = {
                action: 'update_draft',
                id: <?= json_encode($credit_memo->id) ?>,
                credit_date: $('#credit_date').val(),
                customer_id: $('#customer_id').val(),
                customer_name: $('#customer_id option:selected').text(),
                credit_account_id: $('#credit_account_id').val(),
                memo: $('#credit_memo').val(),
                location: $('#location').val(),
                gross_amount: $('#gross_amount').val(),
                net_amount_due: $('#net_amount_due').val(),
                vat_percentage_amount: $('#vat_percentage_amount').val(),
                net_of_vat: $('#net_of_vat').val(),
                tax_withheld_amount: $('#tax_withheld_amount').val(),
                tax_withheld_percentage: $('#tax_withheld_percentage').val(),
                total_amount_due: $('#total_amount_due').val(),
                item_data: JSON.stringify(items)
            };

            console.log('Sending draft update data:', formData);

            $.ajax({
                url: 'api/credit_controller.php',
                type: 'POST',
                dataType: 'json',
                data: formData,
                success: function (response) {
                    document.getElementById('loadingOverlay').style.display = 'none';
                    console.log('Draft update response:', response);
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Credit memo draft updated successfully!',
                            showConfirmButton: true,
                            confirmButtonText: 'OK'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to update credit memo draft: ' + (response.message || 'Unknown error')
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
                        text: 'An error occurred while updating the credit memo draft: ' + textStatus
                    });
                }
            });
        }
    });

</script>