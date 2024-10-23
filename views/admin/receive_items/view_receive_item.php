<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('receive_items');

use PhpOffice\PhpSpreadsheet\Style\NumberFormat\Wizard\Number;


$accounts = ChartOfAccount::all();
$vendors = Vendor::all();
$products = Product::all();
$terms = Term::all();
$locations = Location::all();
$wtaxes = WithholdingTax::all();
$discounts = Discount::all();
$input_vats = InputVat::all();
$sales_taxes = SalesTax::all();

$purchase_orders = PurchaseOrder::all();
$items = Product::all();
$cost_centers = CostCenter::all();

$page = 'view_receive_item'; // Set the variable corresponding to the current page
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

    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="h3"><strong>Receive Items</strong></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="receive_items">Receive</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Receiving Items</li>
                        </ol>
                    </nav>
                </div>
            </div>

            <?php if (isset($_GET['id'])) {
                $id = $_GET['id'];

                $receive_items = ReceivingReport::find($id);

                if ($receive_items) { ?>
                    <form id="receiveItemForm" action="api/receiving_report_controller.php?action=update" method="POST">
                        <input type="hidden" name="id" id="itemId" value="<?= $receive_items->id ?>">
                        <input type="hidden" name="action" id="modalAction" value="update" />
                        <input type="hidden" name="item_data" id="item_data" />
                        <input type="hidden" id="vendor_name_hidden" name="vendor_name">
                        <div id="hiddenInputContainer"></div>
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
                                                <label for="vendor_id" class="form-label">Vendor</label>
                                                <select class="form-control form-control-sm" id="vendor_id"
                                                    name="vendor_id" disabled>
                                                    <option value="">Select Vendor</option>
                                                    <?php foreach ($vendors as $vendor): ?>
                                                        <option value="<?= $vendor->id ?>"
                                                            <?= $vendor->id == $receive_items->vendor_id ? 'selected' : '' ?>>
                                                            <?= $vendor->vendor_name ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="col-md-4 vendor-details">
                                                <label for="location" class="form-label">Location</label>
                                                <select class="form-control form-control-sm" id="location" name="location" disabled>
                                                    <?php
                                                        // Array to prevent duplicates
                                                        $used_locations = [];
                                                        $selected_location = $receive_items->location ?? ''; // Assuming this holds the selected location

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
                                            <!-- Receive Item Details Section -->
                                            <div class="col-12 mt-5 mb-3">
                                                <h6 class="border-bottom pb-2">Receive Items Information</h6>
                                            </div>
                                            <div class="col-md-3 receive-item-details">
                                                <label for="receive_number" class="form-label">Receive Item Number</label>
                                                <input type="text" class="form-control form-control-sm" id="receive_number" name="receive_number" value="<?= $receive_items->receive_no ?>" disabled>
                                            </div>
                                            <div class="col-md-3 receive-item-details">
                                                <label for="receive_date" class="form-label">Receive Item Date</label>
                                                <input type="date" class="form-control form-control-sm" id="receive_date" name="receive_date" value="<?= $receive_items->receive_date ?>" disabled>
                                            </div>
                                            <div class="col-md-3 receive-item-details">
                                                <label for="terms" class="form-label">Terms</label>
                                                <select class="form-control form-control-sm" id="invoice_terms" name="terms" disabled>
                                                    <option value="">Select Terms</option>
                                                    <option value="<?= htmlspecialchars($receive_items->terms) ?>" selected>
                                                        <?= htmlspecialchars($receive_items->terms) ?>
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-md-3 receive-item-details">
                                                <label for="purchase-order_due_date" class="form-label">Due Date</label>
                                                <input type="date" class="form-control form-control-sm" id="purchase-order_due_date" name="purchase-order_due_date" value="<?= $receive_items->receive_due_date ?>" disabled>
                                            </div>
                                            <div class="col-md-4 receive-item-details">
                                                <label for="account_id" class="form-label">Account</label>
                                                <select class="form-select" id="account_id" name="account_id">
                                                    <?php foreach ($accounts as $account) : ?>
                                                        <?php if ($account->account_type == 'Accounts Payable') : ?>
                                                            <option value="<?= $account->id ?>"
                                                                <?= ($account->id == $receive_items->receive_account_id) ? 'selected' : '' ?>>
                                                                <?= $account->id ?> - <?= $account->account_description ?>
                                                            </option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="col-md-8 receive-item-details">
                                                <label for="memo" class="form-label">Memo</label>
                                                <input type="text" class="form-control form-control-sm" id="memo" name="memo" value="<?= $receive_items->memo ?>" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-lg-4">
                                <div class="card h-100">
                                    <div class="card-header">
                                        <h5 class="card-title">Summary</h5>
                                    </div>
                                    <div class="card-body">

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Gross Amount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="gross_amount" name="gross_amount" value="₱<?= number_format($receive_items->gross_amount, 2, '.', ',') ?>" readonly>
                                            </div>
                                        </div>

                                        <!-- Repeat for other summary fields -->

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Discount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="total_discount_amount" name="total_discount_amount" value="₱<?= number_format($receive_items->discount_amount, 2, '.', ',') ?>" readonly>
                                                <input type="text" class="form-control" name="discount_account_id" id="discount_account_id" hidden>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Net Amount:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="net_amount_due" name="net_amount_due" value="₱<?= number_format($receive_items->net_amount, 2, '.', ',') ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">VAT:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="total_vat_amount" name="total_vat_amount" value="₱<?= number_format($receive_items->input_vat, 2, '.', ',') ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Vatable 12%:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="vatable_amount" name="vatable_amount" value="₱<?= number_format($receive_items->vatable, 2, '.', ',') ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Zero-rated:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="zero_rated_amount" name="zero_rated_amount" value="₱<?= number_format($receive_items->zero_rated, 2, '.', ',')  ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label">Vat-Exempt:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end" id="vat_exempt_amount" name="vat_exempt_amount" value="₱<?= number_format($receive_items->vat_exempt, 2, '.', ',') ?>" readonly>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <label class="col-sm-6 col-form-label fw-bold">Total Amount Due:</label>
                                            <div class="col-sm-6">
                                                <input type="text" class="form-control-plaintext text-end fw-bold" id="total_amount_due" name="total_amount_due" value="₱<?= number_format($receive_items->total_amount, 2, '.', ',') ?>" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="button" class="btn btn-secondary me-2" id="voidButton">Void</button>
                                        <a class="btn btn-primary" href="#" id="reprintButton">
                                            <i class="fas fa-print"></i> Reprint
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h5 class="card-title mb-0">Receive Items</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover" id="itemTable">
                                                <thead class="bg-light" style="font-size: 12px;">
                                                    <tr>
                                                        <th>PO #</th>
                                                        <th>Item</th>
                                                        <th>Cost Center</th>
                                                        <th>PO Quantity</th>
                                                        <th>Delivered</th>
                                                        <th>Receive Quantity</th>
                                                        <th>Cost</th>
                                                        <th>Amount</th>
                                                        <th>Discount Type</th>
                                                        <th>Discount</th>
                                                        <th>Net</th>
                                                        <th>Tax Amount</th>
                                                        <th>Tax Type</th>
                                                        <th>VAT</th>
                                                        <th style="width: 80px; text-align: center">Closed</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="itemTableBody" style="font-size: 14px;">
                                                    <!-- Existing rows or dynamically added rows will be appended here -->
                                                    <?php
                                                    if ($receive_items) {
                                                        foreach ($receive_items->details as $detail) {
                                                    ?>
                                                            <tr>
                                                                <td hidden>
                                                                    <input type="text" class="form-control form-control-sm po_id"
                                                                        name="po_id[]"
                                                                        value="<?= htmlspecialchars($detail['po_id']) ?>" disabled>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm po_no"
                                                                        name="po_no[]"
                                                                        value="<?= htmlspecialchars($detail['po_no']) ?>" disabled>
                                                                </td>

                                                                <td>
                                                                    <select class="form-control form-control-sm item-dropdown select2"
                                                                        name="item_id[]" disabled>

                                                                        <?php foreach ($products as $product): ?>
                                                                            <option value="<?= htmlspecialchars($product->id) ?>"
                                                                                <?= ($product->id == $detail['item_id']) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($product->item_name) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>

                                                                    </select>
                                                                </td>



                                                                <td>
                                                                    <select class="form-control form-control-sm cost-center-dropdown select2"
                                                                        name="cost_center[]">

                                                                        <?php foreach ($cost_centers as $cost_center): ?>
                                                                            <option value="<?= htmlspecialchars($cost_center->id) ?>"
                                                                                <?= ($cost_center->id == $detail['cost_center_id']) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($cost_center->particular) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>

                                                                    </select>
                                                                </td>


                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm memo"
                                                                        name="quantity[]"
                                                                        value="<?= htmlspecialchars($detail['qty']) ?>" disabled>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm memo"
                                                                        value="<?= htmlspecialchars($detail['last_received_qty']) ?>" disabled>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm memo"
                                                                        name="quantity[]"
                                                                        value="<?= htmlspecialchars($detail['quantity']) ?>" disabled>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm amount"
                                                                        name="amount[]" placeholder="Enter amount"
                                                                        value="<?= htmlspecialchars($detail['cost']) ?>" disabled>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm amount"
                                                                        name="amount[]" placeholder="Enter amount"
                                                                        value="<?= htmlspecialchars($detail['amount']) ?>" disabled>
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
                                                                    <input type="text"
                                                                        class="form-control form-control-sm discount-amount"
                                                                        name="discount[]" placeholder=""
                                                                        value="<?= htmlspecialchars($detail['discount']) ?>"
                                                                        disabled>
                                                                </td>

                                                                <td>
                                                                    <input type="text"
                                                                        class="form-control form-control-sm net-amount-before-vat"
                                                                        name="net_amount_before_vat[]" placeholder=""
                                                                        value="<?= htmlspecialchars($detail['net_amount_before_input_vat']) ?>"
                                                                        disabled>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm net-amount"
                                                                        name="net_amount[]" placeholder=""
                                                                        value="<?= htmlspecialchars($detail['net_amount']) ?>" disabled>
                                                                </td>

                                                                <td>
                                                                    <select class="form-control form-control-sm tax-dropdown select2"
                                                                        name="tax_amount[]" disabled>
                                                                        <?php foreach ($input_vats as $input_vat) : ?>
                                                                            <option value="<?= htmlspecialchars($input_vat->input_vat_rate) ?>" <?= ($input_vat->input_vat_rate == $detail['input_vat_percentage']) ? 'selected' : '' ?>>
                                                                                <?= htmlspecialchars($input_vat->input_vat_description) ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </td>

                                                                <td>
                                                                    <input type="text" class="form-control form-control-sm input-vat"
                                                                        name="vat_amount[]" placeholder=""
                                                                        value="<?= htmlspecialchars($detail['input_vat_amount']) ?>" disabled>
                                                                </td>

                                                                <td style="text-align: center">
                                                                    <input type="checkbox"
                                                                        value="<?= htmlspecialchars($detail['po_status']) ?>"
                                                                        <?= ($detail['po_status'] == 1) ? 'checked' : '' ?>
                                                                        class="green-checkbox" onclick="return false;">
                                                                    <style>
                                                                        .green-checkbox {
                                                                            accent-color: green;
                                                                            pointer-events: none;
                                                                        }
                                                                    </style>
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
                    </form>
            <?php
                    // Invoice found, you can now display the details
                } else {
                    // Handle the case where the invoice is not found
                    echo "Receive_items not found.";
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

    <iframe id="printFrame" style="display:none;"></iframe>
    <div id="loadingOverlay" style="display:none;" class="loading-overlay">
        <div class="spinner"></div>
    </div>
</div>

<?php require 'views/templates/footer.php' ?>

<script>
    $(document).ready(function() {
        initializeSelect2();
    });

    function formatNumber(amount, decimalPlaces) {
        const number = parseFloat(amount);
        const formatted = number.toLocaleString('en-PH', {
            style: 'currency',
            currency: 'PHP',
            minimumFractionDigits: decimalPlaces,
            maximumFractionDigits: decimalPlaces
        });
        return formatted;
    }

    function initializeSelect2() {
        // Other Select2 initializations (if needed)
        $('#vendor_id, #account_id, #location, #terms').select2({
            theme: 'classic',
            allowClear: false
        });

        $('.item-dropdown, .cost-center-dropdown, .discount-dropdown, .tax-dropdown').select2({
            theme: 'classic',
            allowClear: false
        });
    }

    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('reprintButton').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Reprint Receiving Report?',
                text: "Are you sure you want to reprint this Receiving Report?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reprint it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    printPurchaseOrder(<?= $receive_items->id ?>, 1); // Pass 2 for reprint
                }
            });
        });

        // Attach event listener for the void button
        document.getElementById('voidButton').addEventListener('click', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Void Purchase Order?',
                text: "Are you sure you want to void this purchase order? This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, void it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    voidCheck(<?= $receive_items->id ?>);
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

    function printPurchaseOrder(id, printStatus) {
        showLoadingOverlay();

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
                    const printFrame = document.getElementById('printFrame');
                    const printContentUrl = `print_receive_item?action=print&id=${id}`;

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
            url: 'api/purchase_order_controller.php',
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
                        text: 'Purchase Order has been voided successfully.'
                    }).then(() => {
                        location.reload(); // Reload the page to reflect changes
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to void purchase: ' + (response.message || 'Unknown error')
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                hideLoadingOverlay(); // Hide the loading overlay on error
                console.error('AJAX error:', textStatus, errorThrown);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while voiding the purchase: ' + textStatus
                });
            }
        });
    }
</script>