<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('purchase_request');
$locations = Location::all();
$products = Product::all();
$cost_centers = CostCenter::all();
$newPrNo = PurchaseRequest::getLastPrNo();


$page = 'view_purchase_request';
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
                        <h1 class="h3"><strong>View Purchase Request</strong></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="purchase_request">Purchase Request</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Create Purchase Request</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="purchase_request" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Purchases List
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <?php
                    if (isset($_GET['id'])) {
                        $id = $_GET['id'];

                        $purchase_request = PurchaseRequest::find($id);

                        if ($purchase_request) { ?>
                            <!-- Purchase Order Form -->
                            <form id="purchaseOrderForm" action="api/purchase_request_controller.php?action=add" method="POST">
                                <input type="hidden" name="action" id="modalAction" value="add" />
                                <input type="hidden" name="id" id="itemId" value="" />
                                <input type="hidden" name="item_data" id="item_data" />

                                <div class="row">
                                    <div class="col-12 col-lg-12">
                                        <div class="card h-100">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">Purchase Request Details</h5>
                                            </div>

                                            <div class="card-body">

                                                <div class="row g-2">
                                                    <!-- PR No -->
                                                    <div class="col-md-3 order-details">
                                                        <div class="form-group">
                                                            <!-- PURCHASE ORDER NO -->
                                                            <label for="pr_no">Purchase Request #:</label>
                                                            <input type="text" class="form-control form-control-sm" id="pr_no"
                                                                name="pr_no" 
                                                                <?php if ($purchase_request->status == 4): ?>
                                                                        value="<?php echo htmlspecialchars($newPrNo); ?>" readonly>
                                                                <?php else: ?>
                                                                    value="<?php echo htmlspecialchars($purchase_request->pr_no); ?>" disabled>
                                                                <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row g-2">
                                                    <!-- Requesting Section -->
                                                    <div class="col-md-4 order-details">
                                                        <label for="location" class="form-label">Location</label>
                                                        <select class="form-select form-select-sm" id="location" name="location" 
                                                        <?php if ($purchase_request->status != 4) echo 'disabled'; ?>>
                                                            <?php
                                                                // Array to prevent duplicates
                                                                $used_locations = [];
                                                                $selected_location = $purchase_request->location ?? ''; // Assuming this holds the selected location

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

                                                    <div class="col-md-3 order-details">
                                                        <!-- DATE -->
                                                        <div class="form-group">
                                                            <label for="date">Date</label>
                                                            <input type="date" class="form-control form-control-sm" id="date"
                                                                name="date" value="<?= $purchase_request->date ?>"   <?php echo ($purchase_request->status != 4) ? 'disabled' : ''; ?>>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 order-details">
                                                        <!-- DELIVERY DATE -->
                                                        <div class="form-group">
                                                            <label for="required_date">Required Date</label>
                                                            <input type="date" class="form-control form-control-sm"
                                                                id="required_date" name="required_date"
                                                                value="<?= $purchase_request->required_date ?>"  <?php echo ($purchase_request->status != 4) ? 'disabled' : ''; ?>>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 order-details"></div>
                                                    <div class="col-md-3 order-details"></div>

                                                    <div class="col-md-8 order-details">
                                                        <!-- MEMO -->
                                                        <label for="memo" class="form-label">Memo/Purpose</label>
                                                        <input type="text" class="form-control form-control-sm" id="memo"
                                                            name="memo" value="<?= $purchase_request->memo ?>"  <?php echo ($purchase_request->status != 4) ? 'disabled' : ''; ?>>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row g-2">
                                                    <div class="col-md-12 text-center">
                                                    <?php if ($purchase_request->status == 4): ?>
                                                        <!-- Buttons to show when invoice_status is 4 -->
                                                        <button type="button" id="saveDraftBtn" class="btn btn-secondary me-2">Update Draft</button>
                                                        <button type="submit" class="btn btn-info me-2">Save as Final</button>
                                                    <?php elseif ($purchase_request->status == 3): ?>
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

                                    <!-- Items Table Section -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h5 class="card-title mb-0">Purchase Request Items</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-hover" id="itemTable">
                                                            <thead class="bg-light" style="font-size: 12px;">
                                                                <tr>
                                                                    <th>Item</th>
                                                                    <th>Cost Center</th>
                                                                    <th>Description</th>
                                                                    <th style="width: 100px; background-color: #e6f3ff;">Quantity</th>
                                                                    <?php if ($purchase_request->status != 4): ?>
                                                                        <!-- Show these headers only if status is NOT 4 -->
                                                                        <th style="width: 100px; background-color: #e6f3ff;">Ordered QTY</th>
                                                                        <th style="width: 100px; background-color: #e6f3ff;">Balance QTY</th>
                                                                    <?php endif; ?>
                                                                    <th style="width: 100px">Unit</th>
                                                                    <th style="width: 80px; text-align: center">Closed</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="itemTableBody" style="font-size: 14px;">
                                                                <?php if ($purchase_request): ?>
                                                                    <?php foreach ($purchase_request->details as $detail): ?>
                                                                        <tr>
                                                                            <td>
                                                                                <select
                                                                                    class="form-control form-control-sm item-dropdown"
                                                                                    name="item_id[]" <?php echo ($purchase_request->status != 4) ? 'disabled' : ''; ?>>
                                                                                    <?php foreach ($products as $product): ?>
                                                                                    <option value="<?= htmlspecialchars($product->id) ?>"
                                                                                        <?= ($product->id == $detail['item_id']) ? 'selected' : '' ?>
                                                                                        data-item-name="<?= htmlspecialchars($product->item_name) ?>"
                                                                                        data-description="<?= htmlspecialchars($product->item_sales_description) ?>"
                                                                                        data-uom="<?= htmlspecialchars($product->uom_name) ?>"
                                                                                        data-cost-price="<?= htmlspecialchars($product->item_cost_price) ?>"
                                                                                        data-cogs-account-id="<?= htmlspecialchars($product->item_cogs_account_id) ?>"
                                                                                        data-income-account-id="<?= htmlspecialchars($product->item_income_account_id) ?>"
                                                                                        data-asset-account-id="<?= htmlspecialchars($product->item_asset_account_id) ?>">
                                                                                        <?= htmlspecialchars($product->item_name) ?>
                                                                                    </option>
                                                                                <?php endforeach; ?>
                                                                                </select>
                                                                            </td>
                                                                            <td>
                                                                                <select
                                                                                    class="form-control form-control-sm cost-center-dropdown"
                                                                                    name="cost_center_id[]" <?php echo ($purchase_request->status != 4) ? 'disabled' : ''; ?>>
                                                                                    <?php
                                                                                        // Array to prevent duplicates
                                                                                        $used_cost_center_ids = [];
                                                                                        foreach ($cost_centers as $cost_center):
                                                                                            if (!in_array($cost_center->id, $used_cost_center_ids)):
                                                                                                $used_cost_center_ids[] = $cost_center->id; // Track used cost center IDs
                                                                                    ?>
                                                                                                <option value="<?= htmlspecialchars($cost_center->id) ?>"
                                                                                                    <?= ($cost_center->id == $detail['cost_center_id']) ? 'selected' : '' ?>>
                                                                                                    <?= htmlspecialchars($cost_center->particular) ?>
                                                                                                </option>
                                                                                    <?php
                                                                                            endif;
                                                                                        endforeach;
                                                                                    ?>
                                                                                </select>
                                                                            </td>
                                                                            <td>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm item_purchase_description"
                                                                                    name="item_purchase_description[]"
                                                                                    value="<?= htmlspecialchars($detail['item_purchase_description']) ?>"
                                                                                    readonly>
                                                                            </td>
                                                                            <td class="text-right" style="background-color: #e6f3ff;">
                                                                                <input type="text" class="form-control form-control-sm quantity text-right" name="quantity[]"
                                                                                    value="<?= htmlspecialchars($detail['quantity']) ?>" <?php echo ($purchase_request->status != 4) ? 'disabled' : ''; ?>>
                                                                            </td>

                                                                            <?php if ($purchase_request->status == 4): ?>
                                                                                <!-- Content is hidden if status is 4 -->
                                                                                <td style="display: none;">
                                                                                    <input type="hidden" class="form-control form-control-sm quantity text-right" name="quantity[]"
                                                                                        value="<?= htmlspecialchars($detail['ordered_quantity']) ?>">
                                                                                </td>

                                                                                <td style="display: none;">
                                                                                    <input type="hidden" class="form-control form-control-sm quantity text-right" name="quantity[]"
                                                                                        value="<?= htmlspecialchars($detail['balance_quantity']) ?>">
                                                                                </td>
                                                                            <?php else: ?>
                                                                                <!-- Content is visible if status is not 4 -->
                                                                                <td class="text-right" style="background-color: #e6f3ff;">
                                                                                    <input type="text" class="form-control form-control-sm quantity text-right" name="quantity[]"
                                                                                        value="<?= htmlspecialchars($detail['ordered_quantity']) ?>">
                                                                                </td>

                                                                                <td class="text-right" style="background-color: #e6f3ff;">
                                                                                    <input type="text" class="form-control form-control-sm quantity text-right" name="quantity[]"
                                                                                        value="<?= htmlspecialchars($detail['balance_quantity']) ?>">
                                                                                </td>
                                                                            <?php endif; ?>



                                                                            <td>
                                                                                <input type="text"
                                                                                    class="form-control form-control-sm uom_name"
                                                                                    name="uom_name[]"
                                                                                    value="<?= htmlspecialchars($detail['name']) ?>"
                                                                                    <?php echo ($purchase_request->status != 4) ? 'disabled' : 'readonly'; ?>>
                                                                            </td>

                                                                            <td style="text-align: center">
                                                                                <input type="checkbox"
                                                                                    value="<?= htmlspecialchars($detail['status']) ?>"
                                                                                    <?= ($detail['status'] == 1) ? 'checked' : '' ?>
                                                                                    class="green-checkbox" onclick="return false;">
                                                                                <style>
                                                                                    .green-checkbox {
                                                                                        accent-color: green;
                                                                                        pointer-events: none;
                                                                                    }
                                                                                </style>
                                                                            </td>
                                                                        </tr>
                                                                    <?php endforeach; ?>
                                                                <?php endif; ?>
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
                    <?php
                        } else {
                            // Handle the case where the check is not found
                            echo "PO not found.";
                            exit;
                        }
                    } else {
                        // Handle the case where the ID is not provided
                        echo "No ID provided.";
                        exit;
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>

    <iframe id="printFrame" style="display:none;"></iframe>
    <div id="loadingOverlay" class="loading-overlay" style="display:none;">
        <div class="spinner"></div>
        <div class="message">Processing Purchase Order</div>
    </div>

</div>

<?php require 'views/templates/footer.php' ?>

<script>
    $(document).ready(function() {
        initializeSelect2();

       
        
    });

    function initializeSelect2() {
        // Other Select2 initializations (if needed)
        $('#location').select2({
            theme: 'classic',
            allowClear: false
        });

        $('.item-dropdown, .cost-center-dropdown').select2({
            theme: 'classic',
            allowClear: false
        });
    }
    

    document.getElementById('reprintButton').addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Reprint Purchase Order?',
            text: "Are you sure you want to reprint this purchase order?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, reprint it!'
        }).then((result) => {
            if (result.isConfirmed) {
                printPurchaseOrder(<?= $purchase_request->id ?>, 2); // Pass 2 for reprint
            }
        });
    });

    // Attach event listener for the void button
    document.getElementById('voidButton').addEventListener('click', function (e) {
        e.preventDefault();
        Swal.fire({
            title: 'Void Purchase Request?',
            text: "Are you sure you want to void this purchase request? This action cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, void it!'
        }).then((result) => {
            if (result.isConfirmed) {
                voidCheck(<?= $purchase_request->id ?>);
            }
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
            url: 'api/purchase_request_controller.php',
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
                    const printContentUrl = `print_purchase_request?action=print&id=${id}`;

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
            url: 'api/purchase_request_controller.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'void_check',
                id: id
            },
            success: function (response) {
                hideLoadingOverlay(); // Hide the loading overlay on success
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Purchase Request has been voided successfully.'
                    }).then(() => {
                        location.reload(); // Reload the page to reflect changes
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to void purchase request: ' + (response.message || 'Unknown error')
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                hideLoadingOverlay(); // Hide the loading overlay on error
                console.error('AJAX error:', textStatus, errorThrown);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while voiding the purchase request: ' + textStatus
                });
            }
        });
    }



</script>

<script>
    $(document).ready(function () {

        // Add click event listener to the Clear button
        $('button[type="reset"]').on('click', function (e) {
            e.preventDefault(); // Prevent default reset behavior

            $('input').not('#pr_no').val('');

            $('select').each(function () {
                $(this).val($(this).find("option:first").val()).trigger('change');
            });

            $('#itemTableBody').empty();

            $('.select2').val(null).trigger('change');

            $('#item_data').val('');

            $('#date, #required_date').val(new Date().toISOString().split('T')[0]);

            Swal.fire({
                icon: 'success',
                title: 'Cleared',
                text: 'All fields have been reset.',
                timer: 1800,
                showConfirmButton: false
            });
        });

        $('.item-dropdown, .cost-center-dropdown').select2({
            theme: 'classic',
            width: '100%'
        });

        $('#location').select2({
            theme: 'classic', // Use 'bootstrap-5' for Bootstrap 5, 'bootstrap-4' for Bootstrap 4
            width: '100%',
            placeholder: 'Select Location',
            allowClear: true
        });

        const products = <?php echo json_encode($products); ?>;
            let itemDropdownOptions = '<option value="" selected disabled>Select An Item</option>';
            $.each(products, function (index, product) {
                itemDropdownOptions += `<option value="${product.id}" data-description="${product.item_purchase_description}" data-uom="${product.uom_name}">${product.item_code} - ${product.item_name}</option>`;
            });

            const costCenterOptions = <?php echo json_encode($cost_centers); ?>;
            let costCenterDropdownOptions = '<option value="" selected disabled>Select Cost Center</option>';
            costCenterOptions.forEach(function (costCenter) {
                costCenterDropdownOptions += `<option value="${costCenter.id}">${costCenter.code} - ${costCenter.particular}</option>`;
            });

        // Function to add a new row to the table
        function addRow() {
            const newRow = `
                <tr>
                    <td><select class="form-control form-control-sm item-dropdown" name="item_id[]">${itemDropdownOptions}</select></td>
                    <td><select class="form-control form-control-sm cost-center-dropdown" name="cost_center_id[]">${costCenterDropdownOptions}</select></td>
                    <td><input type="text" class="form-control form-control-sm item_purchase_description" name="item_purchase_description[]" readonly></td>
                    <td class="text-right" style="background-color: #e6f3ff;">
                        <input type="text" class="form-control form-control-sm quantity text-right" name="quantity[]">
                    </td>
                    <td><input type="text" class="form-control form-control-sm uom_name" name="uom_name[]" readonly></td>
                    <td><button type="button" class="btn btn-danger btn-sm removeRow"><i class="fas fa-trash"></i></button></td>
                </tr>`;
            $('#itemTableBody').append(newRow);

            // Initialize Select2 for new dropdowns
            $('#itemTableBody tr:last-child .item-dropdown, #itemTableBody tr:last-child .cost-center-dropdown').select2({
                theme: 'classic',
                width: '100%'
            });
        }

        $('#addItemBtn').click(addRow);

        $(document).on('click', '.removeRow', function () {
            $(this).closest('tr').remove();
            calculateRowValues($(this).closest('tr'));
            calculateTotalAmount();
        });

        // Gather table items and submit form
        function gatherTableItems() {
            const items = [];
            $('#itemTableBody tr').each(function (index) {
                const item = {
                    item_id: $(this).find('select[name="item_id[]"]').val(),
                    cost_center_id: $(this).find('select[name="cost_center_id[]"]').val(),
                    description: $(this).find('input[name="description[]"]').val(),
                    uom: $(this).find('input[name="uom[]"]').val(),
                    quantity: $(this).find('input[name="quantity[]"]').val(),
                };
                items.push(item);
            });
            return items;
        }

        $('#purchaseOrderForm').submit(function (event) {
            event.preventDefault();

            // Check if the table has any rows
            if ($('#itemTableBody tr').length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Items',
                    text: 'You must add at least one item before submitting the invoice.'
                });
                document.getElementById('loadingOverlay').style.display = 'none';
                return false;
            }

            const items = gatherTableItems();
            $('#item_data').val(JSON.stringify(items));

            const status = <?= json_encode($purchase_request->status) ?>;
            const purchase_id = <?= json_encode($purchase_request->id) ?>;

            // Log values to ensure they are defined
            console.log('Purchase ID:', purchase_id);
            console.log('PR No:', pr_no);

            if (status == 4) {
                // Show loading overlay
                document.getElementById('loadingOverlay').style.display = 'flex';

                $.ajax({
                    url: 'api/purchase_request_controller.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'save_final',
                        id: purchase_id,
                        pr_no: $('#pr_no').val(),
                        location: $('#location').val(),
                        date: $('#date').val(),
                        required_date: $('#required_date').val(),
                        memo: $('#memo').val(),
                        item_data: JSON.stringify(items)

                    },
                    success: function (response) {
                        document.getElementById('loadingOverlay').style.display = 'none';

                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Purchase Request submitted successfully!',
                                showCancelButton: true,
                                confirmButtonText: 'Print',
                                cancelButtonText: 'Save as PDF'
                            }).then((result) => {
                                if (result.isConfirmed && response.id) {
                                    printPurchaseOrder(response.id, 1); // Pass 1 for initial print
                                } else {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error saving purchase: ' + (response.message || 'Unknown error')
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
                            text: 'An error occurred while saving the purchase: ' + textStatus
                        });
                    }
                });
            }
        });

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

            const items = gatherTableItems();
            $('#item_data').val(JSON.stringify(items));

            const status = <?= json_encode($purchase_request->status) ?>;
            const purchase_id = <?= json_encode($purchase_request->id) ?>;

            if (status == 4) {
                // Show loading overlay
                document.getElementById('loadingOverlay').style.display = 'flex';

                $.ajax({
                    url: 'api/purchase_request_controller.php',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'update_draft',
                        id: purchase_id,
                        pr_no: $('#pr_no').val(),
                        location: $('#location').val(),
                        date: $('#date').val(),
                        required_date: $('#required_date').val(),
                        memo: $('#memo').val(),
                        item_data: JSON.stringify(items)
                    },
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
                                if (result.isConfirmed && response.invoiceId) {
                                    saveAsPDF(response.invoiceId); // Assuming you have a saveAsPDF function
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
    });



    // Function to populate fields based on selected item
    function populateFields(select) {
        const selectedOption = select.find('option:selected');
        const row = select.closest('tr');
        row.find('.item_purchase_description').val(selectedOption.data('description'));
        row.find('.uom_name').val(selectedOption.data('uom'));
    }

    // Attach change event to all item dropdowns (existing and future)
    $(document).on('change', '.item-dropdown', function() {
        populateFields($(this));
    });

    // Populate fields for existing rows on page load
    $('.item-dropdowns').each(function() {
        populateFields($(this));
    });

</script>