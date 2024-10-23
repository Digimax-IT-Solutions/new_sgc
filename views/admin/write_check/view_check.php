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
                        <h1 class="h3"><strong>View Check</strong></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="write_check">Write Check</a></li>
                                <li class="breadcrumb-item active" aria-current="page">View Write Check</li>
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
            <?php if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $check = WriteCheck::find($id);
            if ($check) { ?>
            <form method="POST" action="api/write_check_controller.php" id="writeCheckForm">
                <input type="hidden" name="action" id="modalAction" value="update" />
                <input type="hidden" name="id" id="itemId" value="<?= $check->id ?>" />
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
                                            <select class="form-control form-control-sm" id="bank_account_id"
                                                name="bank_account_id" <?= ($check->status == 4) ? '' : 'disabled' ?>>
                                                <option value="<?= $check->account_id ?>">
                                                    <?= $check->account_name ?>
                                                </option>
                                                <?php foreach ($accounts as $account): ?>
                                                    <?php if ($account->account_type == 'Cash and Cash Equivalents'): ?>
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
                                                    <select class="form-control form-control-sm" id="payee_type" name="payee_type" <?= ($check->status == 4) ? '' : 'disabled' ?>>
                                                        <option value="<?= $check->payee_type ?>" selected>
                                                            <?= ucwords(str_replace('_', ' ', $check->payee_type)) ?>
                                                        </option>
                                                        <option value="customers">Customer</option>
                                                        <option value="vendors">Vendor</option>
                                                        <option value="other_name">Other Names</option>
                                                        <option value="employee">Employee</option>
                                                    </select>
                                            </div>
                                        </div>
                                        <?php
                                            function getPayeeDetails($payee_type, $payee_id) {
                                                global $customers, $vendors, $other_names, $employee;

                                                $details = [
                                                    'name' => 'Unknown Payee',
                                                    'address' => 'Address Not Found',
                                                ];

                                                switch ($payee_type) {
                                                    case 'customers':
                                                        foreach ($customers as $customer) {
                                                            if ($customer->id == $payee_id) {
                                                                $details['name'] = $customer->customer_name;
                                                                $details['address'] = $customer->billing_address;
                                                                break;
                                                            }
                                                        }
                                                        break;
                                                    case 'vendors':
                                                        foreach ($vendors as $vendor) {
                                                            if ($vendor->id == $payee_id) {
                                                                $details['name'] = $vendor->vendor_name;
                                                                $details['address'] = $vendor->vendor_address;
                                                                break;
                                                            }
                                                        }
                                                        break;
                                                    case 'other_name':
                                                        foreach ($other_names as $other) {
                                                            if ($other->id == $payee_id) {
                                                                $details['name'] = $other->other_name;
                                                                $details['address'] = $other->other_name_address;
                                                                break;
                                                            }
                                                        }
                                                        break;
                                                    case 'employee':
                                                        foreach ($employee as $emp) {
                                                            if ($emp->id == $payee_id) {
                                                                $details['name'] = trim($emp->first_name . ' ' . $emp->middle_name . ' ' . $emp->last_name);
                                                                $details['address'] = trim(implode(', ', array_filter([
                                                                    $emp->house_lot_number,
                                                                    $emp->street,
                                                                    $emp->barangay,
                                                                    $emp->town,
                                                                    $emp->city
                                                                ])));
                                                                break;
                                                            }
                                                        }
                                                        break;
                                                    default:
                                                        $details['name'] = 'Unknown Payee';
                                                        $details['address'] = 'Unknown Address';
                                                }

                                                return $details;
                                            }
                                            
                                            // Fetch the payee details
                                            $payeeDetails = getPayeeDetails($check->payee_type, $check->payee_id);
                                        ?>

                                        <!-- SELECT PAYEE -->
                                        <div class="col-md-4 customer-details">
                                            <div class="form-group">
                                            <label for="payee_id">Payee <span class="text-muted" id="payee_type_display"></span></label>
                                                <select class="form-control form-control-sm" id="payee_id" name="payee_id" <?= ($check->status == 4) ? '' : 'disabled' ?>>
                                                    <option value="<?= $check->payee_id ?>" selected>
                                                        <?= $payeeDetails['name'] ?> <!-- Display the payee name -->
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                    <!-- PAYEE ADDRESS -->
                                        <div class="col-12">
                                        <div class="form-group">
                                        <label for="payee_address">Address</label>
                                            <input type="text" class="form-control form-control-sm" id="payee_address" 
                                                name="payee_address" 
                                                value="<?= $payeeDetails['address'] ?>"
                                                <?= ($check->status == 4) ? '' : 'disabled' ?>>
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
                                        <!-- REF DOC NO -->
                                        <div class="form-group">
                                            <label for="ref_no">Ref Doc No</label>
                                            <input type="text" class="form-control form-control-sm" id="ref_no"
                                                name="ref_no"
                                                value="<?= $check->ref_no ?>" <?= ($check->status == 4) ? '' : 'disabled' ?>>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <!-- CHECK NO -->
                                        <div class="form-group">
                                            <label for="check_no">Check No</label>
                                            <input type="text" class="form-control form-control-sm" id="check_no"
                                                name="check_no"
                                                value="<?= $check->check_no ?>" <?= ($check->status == 4) ? '' : 'disabled' ?>>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <!-- CHECK DATE -->
                                        <div class="form-group">
                                            <label for="check_date">Check Date</label>
                                            <?php
                                            // Ensure the date is in 'yyyy-MM-dd' format or set to an empty string if invalid
                                            $checkDate = $check->check_date;
                                            $formattedDate = ($checkDate && $checkDate !== '0000-00-00') ? date('Y-m-d', strtotime($checkDate)) : '';
                                            ?>
                                            <input type="date" class="form-control form-control-sm" id="check_date"
                                                name="check_date" value="<?= htmlspecialchars($formattedDate) ?>" <?= ($check->status == 4) ? '' : 'disabled' ?>>
                                        </div>
                                    </div>

                                    <div class="row mt-5">
                                        <!-- MEMO -->
                                        <div class="col-md-8 order-details">
                                            <div class="form-group">
                                                <label for="credit_memo" class="form-label">Memo</label>
                                                <input type="text" class="form-control form-control-sm" id="credit_memo"
                                                    name="credit_memo" placeholder="Enter memo"
                                                    value="<?= htmlspecialchars($check->memo) ?>" <?= ($check->status == 4) ? '' : 'disabled' ?>>
                                            </div>
                                        </div>

                                        <!-- LOCATION -->
                                        <div class="col-md-4 customer-details">
                                            <div class="form-group">
                                                <label for="location" class="form-label">Location</label>
                                                <select class="form-control form-control-sm" id="location" name="location" 
                                                    <?php if ($check->status != 4) echo 'disabled'; ?>>
                                                    <option value="">Select Location</option>
                                                    <?php
                                                        // Array to prevent duplicates
                                                        $used_locations = [];
                                                        $selected_location = $check->location ?? ''; // Assuming this holds the selected location

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
                                <?php if ($check->status == 4): ?>
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
                                                                    data-account-id="<?= htmlspecialchars($wtax->wtax_account_id) ?>"
                                                                    data-rate="<?= htmlspecialchars($wtax->wtax_rate) ?>"
                                                                    <?= $wtax->id == $check->tax_withheld_percentage ? 'selected' : '' ?>>
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
                                                <label class="col-sm-6 col-form-label" for="tax_withheld_percentage">Tax Withheld (%):</label>
                                                <div class="col-sm-6">
                                                    <select class="form-control form-control-sm" id="tax_withheld_percentage" name="tax_withheld_percentage" disabled>
                                                        <option value="">Select Tax Withheld</option>
                                                        <?php
                                                        // Array to prevent duplicates
                                                        $used_wtaxes = [];
                                                        foreach ($wtaxes as $wtax):
                                                            if (!in_array($wtax->id, $used_wtaxes)):
                                                                $used_wtaxes[] = $wtax->id; // Track used tax rates
                                                        ?>
                                                                <option value="<?= htmlspecialchars($wtax->id) ?>"
                                                                    data-account-id="<?= htmlspecialchars($wtax->wtax_account_id) ?>"
                                                                    <?= $wtax->id == $check->tax_withheld_percentage ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($wtax->wtax_name) ?>
                                                                </option>
                                                        <?php
                                                            endif;
                                                        endforeach;
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Tax Withheld Amount:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end"
                                            id="tax_withheld_amount" name="tax_withheld_amount"
                                            value="<?= number_format($check->tax_withheld_amount, 2, '.', ',') ?>"
                                            readonly>
                                        <input type="hidden" name="tax_withheld_account_id"
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
                                            <button type="button" id="saveDraftBtn" class="btn btn-secondary me-2">Update Draft</button>
                                            <button type="submit" class="btn btn-info me-2">Save as Final</button>
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
                                        <?php
                                            if ($check) {
                                                foreach ($check->details as $detail) {
                                            ?>
                                                    <tr>
                                                        <td>
                                                            <select class="form-control form-control-sm account-dropdown select2"
                                                                id="account_code" name="account_code[]" <?= ($check->status == 4) ? '' : 'disabled' ?>>
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
                                                              id="cost_center_id"   name="cost_center_id[]" <?= ($check->status == 4) ? '' : 'disabled' ?>>
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
                                                                value="<?= htmlspecialchars($detail['memo'] ?? $check->memo) ?>" <?= ($check->status == 4) ? '' : 'disabled' ?>>
                                                        </td>

                                                        <td>
                                                            <input type="text" class="form-control form-control-sm amount"
                                                                name="amount[]" placeholder="Enter amount"
                                                                value="<?= number_format($detail['amount'], 2, '.', ',') ?>" <?= ($check->status == 4) ? '' : 'disabled' ?>>
                                                        </td>

                                                        <td>
                                                            <select class="form-control form-control-sm discount-dropdown select2 discount_percentage" id='discount_percentage'
                                                                name="discount_percentage[]" <?= ($check->status == 4) ? '' : 'disabled' ?>>
                                                                <?php foreach ($discounts as $discount): ?>
                                                                    <option value="<?= htmlspecialchars($discount->discount_rate) ?>"
                                                                        <?= ($discount->discount_rate == $detail['discount_percentage']) ? 'selected' : '' ?>>
                                                                        <?= htmlspecialchars($discount->discount_name) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </td>

                                                        <td>
                                                            <input type="text" class="form-control form-control-sm discount-amount"
                                                                name="discount_amount[]" value="<?= number_format($detail['discount_amount'], 2, '.', ',') ?>" readonly>
                                                        </td>

                                                        <td>
                                                            <input type="text" class="form-control form-control-sm net-amount-before-vat"
                                                                name="net_amount_before_vat[]" value="<?= number_format($detail['net_amount_before_vat'], 2, '.', ',') ?>" readonly>
                                                        </td>

                                                        <td>
                                                            <input type="text" class="form-control form-control-sm net-amount"
                                                                name="net_amount[]" value="<?= number_format($detail['net_amount'], 2, '.', ',') ?>" readonly>
                                                        </td>

                                                        <td>
                                                            <select class="form-control form-control-sm vat_percentage select2"
                                                               id="vat_percentage"  name="vat_percentage[]" <?= ($check->status == 4) ? '' : 'disabled' ?>>
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
                                                                name="vat_amount[]" value="<?= number_format($detail['input_vat'], 2, '.', ',') ?>" readonly>
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
                                <i class="fas fa-plus"></i> Add Item
                            </button>
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

<div id="loadingOverlay" style="display: none;">
    <div class="spinner"></div>
    <div class="message">Processing wchecks</div>
</div>
</div>


<?php require 'views/templates/footer.php' ?>


<iframe id="printFrame" style="display:none;"></iframe>

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
   $(document).ready(function () {
    // Predefined data (should be fetched via API for better flexibility)
    var customers = <?= json_encode($customers) ?>;
    var vendors = <?= json_encode($vendors) ?>;
    var otherNames = <?= json_encode($other_names) ?>;
    var employees = <?= json_encode($employee) ?>;

    const payeeTypeSelect = $('#payee_type');
    const payeeDropdown = $('#payee_id');
    const payeeAddressInput = $('#payee_address');

    // Initialize dropdown and fields
    initializePayeeData();

    // Event handler for Payee Type change
    payeeTypeSelect.change(function () {
        const payeeType = payeeTypeSelect.val();
        resetPayeeDropdown();
        populatePayeeDropdown(payeeType);
    });

    // Event handler for Payee selection change
    payeeDropdown.change(function () {
        const payeeId = payeeDropdown.val();
        const payeeType = payeeTypeSelect.val();
        updatePayeeDetails(payeeType, payeeId);
    });

    // Function to reset payee dropdown
    function resetPayeeDropdown() {
        payeeDropdown.empty().append('<option value="">Select Payee</option>');
        payeeAddressInput.val('');
    }

    // Function to populate payee dropdown based on payee type
    function populatePayeeDropdown(payeeType) {
        const data = getDataByPayeeType(payeeType);
        if (data.length === 0) {
            payeeDropdown.append('<option value="">No data available</option>');
        } else {
            data.forEach(item => {
                payeeDropdown.append(`<option value="${item.id}">${item.name}</option>`);
            });
        }
    }

    // Function to update payee details like address
    function updatePayeeDetails(payeeType, payeeId) {
        const data = getDataByPayeeType(payeeType);
        const selectedPayee = data.find(item => item.id == payeeId);
        if (selectedPayee) {
            payeeAddressInput.val(selectedPayee.address);
        } else {
            payeeAddressInput.val('');
        }
    }

    // Function to get data by payee type
    function getDataByPayeeType(payeeType) {
        switch (payeeType) {
            case 'customers':
                return mapData(customers, 'customer_name', 'billing_address');
            case 'vendors':
                return mapData(vendors, 'vendor_name', 'vendor_address');
            case 'other_name':
                return mapData(otherNames, 'other_name', 'other_name_address');
            case 'employee':
                return mapEmployeeData(employees);
            default:
                return [];
        }
    }

    // Function to map data to id, name, address format
    function mapData(data, nameKey, addressKey) {
        return data.map(item => ({
            id: item.id,
            name: item[nameKey],
            address: item[addressKey]
        }));
    }

    // Function to map employee data with a full name and address
    function mapEmployeeData(data) {
        return data.map(item => ({
            id: item.id,
            name: `${item.first_name} ${item.middle_name || ''} ${item.last_name}`.trim(),
            address: [item.house_lot_number, item.street, item.barangay, item.town, item.city].filter(Boolean).join(', ')
        }));
    }

    // Function to initialize payee data (populate if already selected)
    function initializePayeeData() {
        const selectedPayeeType = payeeTypeSelect.val();
        if (selectedPayeeType) {
            populatePayeeDropdown(selectedPayeeType);
            const selectedPayeeId = payeeDropdown.val();
            if (selectedPayeeId) {
                updatePayeeDetails(selectedPayeeType, selectedPayeeId);
            }
        }
    }
});
</script>

<script>
    $(document).ready(function () {


        $('#account_code').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Account',
            allowClear: true
        });

        $('#discount_percentage').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Discount',
            allowClear: true
        });

        $('#location').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Location',
            allowClear: true
        });

        $('#cost_center_id').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Cost Center',
            allowClear: true
        });

        $('#vat_percentage').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Vat',
            allowClear: false
        });


   
        document.getElementById('tax_withheld_percentage').addEventListener('change', function () {
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
        $.each(accounts, function (index, account) {
            accountDropdownOptions += `<option value="${account.id}">${account.account_code}-${account.account_description}</option>`;
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

        // Function to parse formatted number back to float
        function parseFormattedNumber(str) {
            return parseFloat(str.replace(/,/g, '')) || 0;
        }

        // Function to calculate net amount for a single row
        function calculateRowNetAmount(row) {
            const amount = parseFormattedNumber(row.find('.amount').val());
            const discountPercentage = parseFloat(row.find('.discount_percentage').val()) || 0;
            const vatPercentage = parseFloat(row.find('.vat_percentage').val()) || 0;

            const discountAmount = (discountPercentage / 100) * amount;
            row.find('.discount-amount').val(formatNumber(discountAmount));

            const netAmountBeforeVAT = amount - discountAmount;
            row.find('.net-amount-before-vat').val(formatNumber(netAmountBeforeVAT));

            const vatPercentageAmount = vatPercentage / 100;
            const netAmount = netAmountBeforeVAT / (1 + vatPercentageAmount);
            row.find('.net-amount').val(formatNumber(netAmount));

            const vatAmount = netAmountBeforeVAT - netAmount;
            row.find('.input-vat').val(formatNumber(vatAmount));

            return {
                amount: amount,
                discountAmount: discountAmount,
                netAmountBeforeVAT: netAmountBeforeVAT,
                netAmount: netAmount,
                vatAmount: vatAmount
            };
        }

        // Event listener for discount percentage change
        $('#itemTableBody').on('change', '.discount_percentage, .discount_amount', function() {
            const row = $(this).closest('tr');
            calculateRowNetAmount(row);
            calculateNetAmount();
        });

        // Event listener for recalculating on input change
        $('#itemTableBody').on('input change', '.amount, .discount_percentage,  .vat_percentage', function () {
            const row = $(this).closest('tr');
            calculateRowNetAmount(row);
            calculateNetAmount();
        });

        // Event listener for adding a new row
        $('#addItemBtn').click(function() {
            addRow();
            calculateNetAmount(); // Recalculate after adding a row
        });

        // Event listener for removing a row
        $(document).on('click', '.removeRow', function () {
            $(this).closest('tr').remove();
            calculateNetAmount();
        });

        calculateNetAmount();

        // Function to calculate net amount for all rows
        function calculateNetAmount() {
            let totalAmount = 0;
            let totalDiscountAmount = 0;
            let totalNetAmount = 0;
            let totalVat = 0;
            let totalTaxableAmount = 0;

            $('#itemTableBody tr').each(function () {
                const rowResults = calculateRowNetAmount($(this));
                totalAmount += rowResults.amount;
                totalDiscountAmount += rowResults.discountAmount;
                totalNetAmount += rowResults.netAmountBeforeVAT;
                totalVat += rowResults.vatAmount;
                totalTaxableAmount += rowResults.netAmount;
            });

            $("#gross_amount").val(formatNumber(totalAmount));
            $("#discount_amount").val(formatNumber(totalDiscountAmount));
            $("#net_amount_due").val(formatNumber(totalNetAmount));
            $("#vat_percentage_amount").val(formatNumber(totalVat));
            $("#net_of_vat").val(formatNumber(totalTaxableAmount));

            // Get the selected tax withheld option
            const selectedTaxWithheld = $("#tax_withheld_percentage option:selected");
            const taxWithheldPercentage = parseFloat(selectedTaxWithheld.data('rate')) || 0;
            const taxWithheldAmount = (taxWithheldPercentage / 100) * totalTaxableAmount;
            $("#tax_withheld_amount").val(formatNumber(taxWithheldAmount));

            const totalAmountDue = totalNetAmount - taxWithheldAmount;
            $("#total_amount_due").val(formatNumber(totalAmountDue));
        }     

        // Event listener for handling input and formatting in the amount field
        $('#itemTableBody').on('focus', '.amount', function() {
            $(this).val(parseFormattedNumber($(this).val()));
        });

        $('#itemTableBody').on('blur', '.amount', function() {
            const formattedValue = formatNumber(parseFormattedNumber($(this).val()));
            $(this).val(formattedValue);
            calculateNetAmount();
        });

        // Event listener for recalculating on input change
        $('#itemTableBody').on('input change', '.amount, .discount_percentage, .vat_percentage', function () {
            calculateNetAmount();
        });

        // Event listener for tax withheld percentage change
        $('#tax_withheld_percentage').on('change', function () {
            calculateNetAmount();
        });



        // Event listener for adding a new row
        $('#addItemBtn').click(function() {
            addRow();
            calculateNetAmount(); // Recalculate after adding a row
        });

        // Event listener for removing a row
        $(document).on('click', '.removeRow', function () {
            $(this).closest('tr').remove();
            calculateNetAmount();
        });

        calculateNetAmount();

        function getUniqueInputVatIds() {
            const uniqueIds = new Set();
            $('#itemTableBody tr').each(function () {
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
            $('#itemTableBody tr').each(function () {
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
            $('#itemTableBody tr').each(function () {
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
            $('#itemTableBody tr').each(function (index) {
                const item = {
                    account_id: $(this).find('.account-dropdown').val(),
                    cost_center_id: $(this).find('.cost-dropdown').val(),
                    memo: $(this).find('.memo').val(),
                    location: $('#location').val(),
                    amount: parseFormattedNumber($(this).find('.amount').val()),
                    discount_percentage: $(this).find('.discount_percentage').val(),
                    discount_amount: parseFormattedNumber($(this).find('.discount-amount').val()),
                    net_amount_before_vat: parseFormattedNumber($(this).find('.net-amount-before-vat').val()),
                    net_amount: parseFormattedNumber($(this).find('.net-amount').val()),
                    vat_percentage: $(this).find('.vat_percentage').val(),
                    input_vat: parseFormattedNumber($(this).find('.input-vat').val()),
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

        function gatherFormData() {
            return {
                action: 'save_final',
                id: <?= json_encode($check->id) ?>,
                cv_no: $('#cv_no').val(),
                ref_no: $('#ref_no').val(),
                check_no: $('#check_no').val(),
                check_date: $('#check_date').val(),
                bank_account_id: $('#bank_account_id').val(),
                payee_type: $('#payee_type').val(),
                payee_id: $('#payee_id').val(),
                payee_name: $('#payee_name').val(),
                payee_address: $('#payee_address').val(),
                memo: $('#credit_memo').val(),
                location: $('#location').val(),
                gross_amount: parseFormattedNumber($('#gross_amount').val()),
                discount_amount: parseFormattedNumber($('#discount_amount').val()),
                net_amount_due: parseFormattedNumber($('#net_amount_due').val()),
                vat_percentage_amount: parseFormattedNumber($('#vat_percentage_amount').val()),
                net_of_vat: parseFormattedNumber($('#net_of_vat').val()),
                tax_withheld_percentage: $('#tax_withheld_percentage').val(),
                tax_withheld_amount: parseFormattedNumber($('#tax_withheld_amount').val()),
                total_amount_due: parseFormattedNumber($('#total_amount_due').val()),
                item_data: JSON.stringify(gatherTableItems())
            };
        }

        function gatherFormDataUpdateDraft() {
            return {
                action: 'update_draft',
                id: <?= json_encode($check->id) ?>,
                cv_no: $('#cv_no').val(),
                ref_no: $('#ref_no').val(),
                check_no: $('#check_no').val(),
                check_date: $('#check_date').val(),
                bank_account_id: $('#bank_account_id').val(),
                payee_type: $('#payee_type').val(),
                payee_id: $('#payee_id').val(),
                payee_name: $('#payee_name').val(),
                payee_address: $('#payee_address').val(),
                memo: $('#credit_memo').val(),
                location: $('#location').val(),
                gross_amount: parseFormattedNumber($('#gross_amount').val()),
                discount_amount: parseFormattedNumber($('#discount_amount').val()),
                net_amount_due: parseFormattedNumber($('#net_amount_due').val()),
                vat_percentage_amount: parseFormattedNumber($('#vat_percentage_amount').val()),
                net_of_vat: parseFormattedNumber($('#net_of_vat').val()),
                tax_withheld_percentage: $('#tax_withheld_percentage').val(),
                tax_withheld_amount: parseFormattedNumber($('#tax_withheld_amount').val()),
                total_amount_due: parseFormattedNumber($('#total_amount_due').val()),
                tax_withheld_account_id: $('#tax_withheld_account_id').val(),
                item_data: JSON.stringify(gatherTableItems())
            };
        }
        
        $('#saveDraftBtn').click(function(event) {
            event.preventDefault();

            // Check if the table has any rows
            if ($('#itemTableBody tr').length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Items',
                    text: 'You must add at least one item before updating the draft.'
                });
                return false;
            }

            const formData = gatherFormDataUpdateDraft();
            const wcheckStatus = <?= json_encode($check->status) ?>;

            if (wcheckStatus == 4) {
                // Show loading overlay
                document.getElementById('loadingOverlay').style.display = 'flex';

                $.ajax({
                    url: 'api/write_check_controller.php',
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    success: function(response) {
                        document.getElementById('loadingOverlay').style.display = 'none';

                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Draft updated successfully!',
                                showCancelButton: true,
                                cancelButtonText: 'Close'
                            }).then((result) => {
                                if (result.isConfirmed && response.checkId) {
                                    saveAsPDF(response.checkId); // Assuming you have a saveAsPDF function
                                } else {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error updating draft: ' + (response.message || 'Unknown error')
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
                            text: 'An error occurred while updating the draft: ' + textStatus
                        });
                    }
                });
            }
        });

        $('#writeCheckForm').submit(function(event) {
            event.preventDefault();

            // Check if the table has any rows
            if ($('#itemTableBody tr').length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Items',
                    text: 'You must add at least one item before submitting the write check.'
                });
                return false;
            }

            const wcheckStatus = <?= json_encode($check->status) ?>;

            if (wcheckStatus == 4) {
                // Show loading overlay
                document.getElementById('loadingOverlay').style.display = 'flex';

                const formData = gatherFormData();

                $.ajax({
                    url: 'api/write_check_controller.php',
                    type: 'POST',
                    dataType: 'json',
                    data: formData,
                    success: function(response) {
                        document.getElementById('loadingOverlay').style.display = 'none';
                        console.log('Response:', response);
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


    });
</script>

