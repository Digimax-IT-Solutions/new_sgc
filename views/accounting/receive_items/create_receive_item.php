<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();
$accounts = ChartOfAccount::all();
$vendors = Vendor::all();
$products = Product::all();
$terms = Term::all();
$locations = Location::all();
$wtaxes = WithholdingTax::all();
$discounts = Discount::all();
$input_vats = InputVat::all();
$sales_taxes = SalesTax::all();

$newRRNo = ReceivingReport::getLastRRNo();

$page = 'sales_purchase-order'; // Set the variable corresponding to the current page
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>

<style>
    .card {
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .card-header {
        background-color: #fff;
        color: white;
        border-radius: 8px 8px 0 0;
    }

    .form-control,
    .form-select {
        border-radius: 4px;
        border: 1px solid #ced4da;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #3498db;
        box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
    }

    .btn-primary {
        background-color: #3498db;
        border-color: #3498db;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #2980b9;
        border-color: #2980b9;
        transform: translateY(-2px);
    }

    #itemTable {
        border-collapse: separate;
        border-spacing: 0;
    }



    #itemTable tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #itemTable tbody tr:hover {
        background-color: #e6f3ff;
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
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3"><strong>Receive Items</strong></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="receive_items">Receive</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Receiving Items</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="receive_items" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Receive Items List
                        </a>
                    </div>
                </div>
            </div>

            <form id="receiveItemForm" action="api/receiving_report_controller.php?action=add" method="POST">
                <input type="hidden" name="action" id="modalAction" value="add" />
                <input type="hidden" name="id" id="itemId" value="" />
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
                                        <label for="vendor_name" class="form-label">Vendor</label>
                                        <select class="form-select form-select-sm select2" id="vendor_name" name="vendor_name" required>
                                            <option value="">Select Customer</option>
                                            <?php foreach ($vendors as $vendor) : ?>
                                                <option value="<?= $vendor->id ?>"><?= $vendor->vendor_name ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-4 vendor-details">
                                        <label for="location" class="form-label">Location</label>
                                        <select class="form-select form-select-sm" id="location" name="location" required>
                                            <option value="">Select Location</option>
                                            <?php foreach ($locations as $location) : ?>
                                                <option value="<?= $location->name ?>"><?= $location->name ?>
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
                                        <input type="text" class="form-control form-control-sm" id="receive_number" name="receive_number" value="<?php echo $newRRNo; ?>" readonly>
                                    </div>
                                    <div class="col-md-3 receive-item-details">
                                        <label for="receive_date" class="form-label">Receive Item Date</label>
                                        <input type="date" class="form-control form-control-sm" id="receive_date" name="receive_date" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    <div class="col-md-3 receive-item-details">
                                        <label for="terms" class="form-label">Terms</label>
                                        <select class="form-select form-select-sm" id="terms" name="terms">
                                            <option value="">Select Terms</option>
                                            <?php foreach ($terms as $term) : ?>
                                                <option value="<?= $term->term_name ?>" data-days="<?= $term->term_days_due ?>"><?= $term->term_name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-3 receive-item-details">
                                        <label for="receive_due_date" class="form-label">Due Date</label>
                                        <input type="date" class="form-control form-control-sm" id="receive_due_date" name="receive_due_date" value="<?php echo date('Y-m-d'); ?>" required>
                                    </div>
                                    <div class="col-md-4 receive-item-details">
                                        <label for="account_id" class="form-label">Account</label>
                                        <select class="form-select" id="account_id" name="account_id">
                                            <?php foreach ($accounts as $account) : ?>
                                                <?php if ($account->account_type == 'Accounts Payable') : ?>
                                                    <option value="<?= $account->id ?>">
                                                        <?= $account->id ?> - <?= $account->account_description ?>
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

                    <div class="col-12 col-lg-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title">Summary</h5>
                            </div>
                            <div class="card-body">

                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Gross Amount:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end" id="gross_amount" name="gross_amount" value="0.00" readonly>
                                    </div>
                                </div>

                                <!-- Repeat for other summary fields -->

                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Discount:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end" id="total_discount_amount" name="total_discount_amount" value="0.00" readonly>
                                        <input type="text" class="form-control" name="discount_account_id" id="discount_account_id" hidden>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Net Amount:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end" id="net_amount_due" name="net_amount_due" value="0.00" readonly>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-6 col-form-label">VAT:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end" id="total_vat_amount" name="total_vat_amount" value="0.00" readonly>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Vatable 12%:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end" id="vatable_amount" name="vatable_amount" value="0.00" readonly>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Zero-rated:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end" id="zero_rated_amount" name="zero_rated_amount" value="0.00" readonly>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Vat-Exempt:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end" id="vat_exempt_amount" name="vat_exempt_amount" value="0.00" readonly>
                                    </div>
                                </div>


                                <div class="row">
                                    <label class="col-sm-6 col-form-label fw-bold">Total Amount Due:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end fw-bold" id="total_amount_due" name="total_amount_due" value="0.00" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer text-center">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Print</button>
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
                                                <th style="width: 1%;"><input type="checkbox" id="selectAll" /></th>
                                                <th>PO #</th>
                                                <th>Item</th>
                                                <th style="width: 10%; background-color: #e6f3ff;">Quantity</th>
                                                <th>Cost Center</th>
                                                <th>Desc</th>
                                                <th>U/M</th>
                                                <th>Cost</th>
                                                <th class="text-right">Amount</th>
                                                <th class="text-right">Disc Type</th>
                                                <th class="text-right">Discount</th>
                                                <th class="text-right">Net</th>
                                                <th class="text-right">Tax Amt</th>
                                                <th class="text-right">Tax Type</th>
                                                <th class="text-right">VAT</th>
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
            </form>
        </div>
    </main>

    <div id="loadingOverlay" style="display: none;">
        <div class="spinner"></div>
        <div class="message">Processing Receiving Items</div>
    </div>
</div>

<?php require 'views/templates/footer.php' ?>
<iframe id="printFrame" style="display:none;"></iframe>

<script>
    $(document).ready(function() {
        initializeSelect2();
        setupEventListeners();
    });

    function initializeSelect2() {
        $('#vendor_name').select2({
            theme: 'classic',
            allowClear: false
        });
        $('#account_id').select2({
            theme: 'classic',
            allowClear: false
        });
        $('#location').select2({
            theme: 'classic',
            allowClear: false
        });
        $('#terms').select2({
            theme: 'classic',
            allowClear: false
        });
    }

    function fetchAndDisplayPurchaseOrder(vendorId) {
        const tableBody = document.getElementById('itemTableBody');

        if (!vendorId) {
            clearReceiveItemTable();
            clearSummary();
            return;
        }

        showLoadingMessage(tableBody);

        fetch(`api/receiving_report_controller.php?action=get_purchase_order&vendor_id=${vendorId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Received data:', data); // Log the received data
                if (data.purchase_orders.length === 0) {
                    showNopurchase_ordersMessage(tableBody);
                    clearSummary();
                    return;
                }

                populateReceiveItemTable(data.purchase_orders, tableBody);
                updateSummary();
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                showErrorMessage(tableBody, error.message);
                clearSummary();
            });
    }

    function clearReceiveItemTable() {
        const tableBody = document.getElementById('itemTableBody');
        tableBody.innerHTML = '';
    }

    function showLoadingMessage(tableBody) {
        tableBody.innerHTML = '<tr><td colspan="14">Loading...</td></tr>';
    }

    function showNopurchase_ordersMessage(tableBody) {
        tableBody.innerHTML = '<tr><td colspan="14">There are no purchase orders from this vendor</td></tr>';
    }

    function showErrorMessage(tableBody, errorMessage) {
        tableBody.innerHTML = `<tr><td colspan="14">Error fetching purchase orders: ${errorMessage}. Please try again.</td></tr>`;
    }

    const discountOptions = <?php echo json_encode($discounts); ?>;

    function generateDiscountDropdownOptions(selectedRate) {
        return discountOptions.map(discount =>
            `<option value="${discount.discount_rate}" 
                data-account-id="${discount.discount_account_id}"
                ${discount.discount_rate == selectedRate ? 'selected' : ''}>
            ${discount.discount_name}
         </option>`
        ).join('');
    }

    const inputVatOptions = <?php echo json_encode($input_vats); ?>;

    function generateInputVatDropdownOptions(selectedRate) {
        return inputVatOptions.map(inputVat =>
            `<option value="${inputVat.input_vat_rate}" 
                ${inputVat.input_vat_rate == selectedRate ? 'selected' : ''}>
            ${inputVat.input_vat_name}
         </option>`
        ).join('');
    }

    function populateReceiveItemTable(purchase_orders, tableBody) {
        tableBody.innerHTML = '';
        purchase_orders.forEach(purchase_order => {
            const row = `
        <tr data-purchase-order-id="${purchase_order.id}" data-cost-center-id="${purchase_order.cost_center_id}" data-item-id="${purchase_order.item_id}">
            <td><input type="checkbox" class="purchase-order-select" data-purchase-order-id="${purchase_order.id}" /></td>
            <td>${purchase_order.po_no}</td>
            <td>${purchase_order.item}</td>
            <td style="background-color: #e6f3ff;">
                <input type="number" class="form-control form-control-sm text-right quantity" 
                    name="quantity" value="${purchase_order.quantity}" required 
                    style="font-weight: bold; color: #0056b3;" />
            </td>
            <td>${purchase_order.cost_center_name}</td>
            <td>${purchase_order.description}</td>
            <td>${purchase_order.unit}</td>
            <td class="text-right cost">${parseFloat(purchase_order.cost).toFixed(2)}</td>
            <td class="text-right amount">${parseFloat(purchase_order.amount).toFixed(2)}</td>
            <td>
                <select class="form-control form-control-sm discount-type" name="discount-type">
                    ${generateDiscountDropdownOptions(purchase_order.discount_percentage)}
                </select>
            </td>
            <td class="text-right discount">${parseFloat(purchase_order.discount).toFixed(2)}</td>
            <td class="text-right net">${parseFloat(purchase_order.net).toFixed(2)}</td>
            <td class="text-right tax_amount">${parseFloat(purchase_order.tax_amount).toFixed(2)}</td>
            <td>
                <select class="form-control form-control-sm input_vat_percentage" name="input_vat_percentage">
                    ${generateInputVatDropdownOptions(purchase_order.input_vat_percentage)}
                </select>
            </td>
            <td class="text-right vat">${parseFloat(purchase_order.vat).toFixed(2)}</td>
        </tr>
        `;
            tableBody.innerHTML += row;
        });

        // Add hidden inputs for IDs (unchanged)
        const hiddenInputContainer = document.getElementById('hiddenInputContainer') || document.body;
        hiddenInputContainer.innerHTML = purchase_orders.map(po => `
        <input type="hidden" name="purchase_order_ids[]" value="${po.id}">
        <input type="hidden" name="purchase_order_detail_ids[]" value="${po.purchase_order_detail_id}">
        <input type="hidden" name="po_account_ids[]" value="${po.po_account_id}">
        <input type="hidden" name="cost_center_ids[]" value="${po.cost_center_id}">
        <input type="hidden" name="item_ids[]" value="${po.item_id}">
    `).join('');
    }

    function updateSummary() {
        
        let grossAmount = 0;
        let totalDiscountAmount = 0;
        let netAmountDue = 0;
        let totalVatAmount = 0;
        let vatableAmount = 0;
        let zeroRatedAmount = 0;
        let vatExemptAmount = 0;

        $('#itemTableBody tr').each(function() {
            if ($(this).find('.purchase-order-select').prop('checked')) {
                const amount = parseFloat($(this).find('.amount').text()) || 0;
                const discount = parseFloat($(this).find('.discount').text()) || 0;
                const net = parseFloat($(this).find('.net').text()) || 0;
                const vat = parseFloat($(this).find('.vat').text()) || 0;
                const vatPercentage = parseFloat($(this).find('.input_vat_percentage').val()) || 0;

                grossAmount += amount;
                totalDiscountAmount += discount;
                netAmountDue += amount - discount;
                totalVatAmount += vat;

                if (vatPercentage === 12) {
                    vatableAmount += net - vat;
                } else if (vatPercentage === 0) {
                    zeroRatedAmount += net;
                } else {
                    vatExemptAmount += net;
                }
            }
        });

        $('#gross_amount').val(grossAmount.toFixed(2));
        $('#total_discount_amount').val(totalDiscountAmount.toFixed(2));
        $('#net_amount_due').val(netAmountDue.toFixed(2));
        $('#total_vat_amount').val(totalVatAmount.toFixed(2));
        $('#vatable_amount').val(vatableAmount.toFixed(2));
        $('#zero_rated_amount').val(zeroRatedAmount.toFixed(2));
        $('#vat_exempt_amount').val(vatExemptAmount.toFixed(2));

        const totalAmountDue = netAmountDue;
        $('#total_amount_due').val(totalAmountDue.toFixed(2));
    }

    function clearSummary() {
        $('#gross_amount, #total_discount_amount, #net_amount_due, #total_vat_amount, #vatable_amount, #zero_rated_amount, #vat_exempt_amount, #total_amount_due').val('0.00');
    }

    function setupEventListeners() {
        $('#vendor_name').on('change', function() {
            fetchAndDisplayPurchaseOrder(this.value);
        });

        $('#selectAll').on('change', function() {
            const isChecked = $(this).prop('checked');
            $('.purchase-order-select').prop('checked', isChecked);
            updateSummary();
        });

        $(document).on('change', '.purchase-order-select, .quantity, .discount-type, .input_vat_percentage', function() {
            recalculateRow($(this).closest('tr'));
            updateSummary();
        });

        document.getElementById('receiveItemForm').addEventListener('submit', submitPayment);
    }

    function recalculateRow(row) {
        const quantity = parseFloat(row.find('.quantity').val()) || 0;
        const cost = parseFloat(row.find('.cost').text()) || 0;
        const discountRate = parseFloat(row.find('.discount-type').val()) || 0;
        const vatRate = parseFloat(row.find('.input_vat_percentage').val()) || 0;

        const amount = quantity * cost;
        const discountAmount = amount * (discountRate / 100);
        const netAmount = amount - discountAmount;
        const vatAmount = netAmount * (vatRate / 100);

        row.find('.amount').text(amount.toFixed(2));
        row.find('.discount').text(discountAmount.toFixed(2));
        row.find('.net').text(netAmount.toFixed(2));
        row.find('.tax_amount').text(vatAmount.toFixed(2));
        row.find('.vat').text(vatAmount.toFixed(2));
    }

    function submitPayment(event) {
        event.preventDefault();

        document.getElementById('loadingOverlay').style.display = 'flex';

        const formData = new FormData(document.getElementById('receiveItemForm'));

        const itemData = [];
        $('#itemTableBody tr').each(function() {
            if ($(this).find('.purchase-order-select').prop('checked')) {
                const row = $(this);
                itemData.push({
                    po_id: parseInt(row.data('purchase-order-id'), 10),
                    purchase_order_detail_id: parseInt(row.find('input[name="purchase_order_detail_ids[]"]').val(), 10),
                    item_id: parseInt(row.data('item-id'), 10),
                    cost_center_id: parseInt(row.data('cost-center-id'), 10),
                    description: row.find('td:eq(4)').text(),
                    uom: row.find('td:eq(5)').text(),
                    quantity: parseFloat(row.find('.quantity').val()),
                    cost: parseFloat(row.find('.cost').text()),
                    amount: parseFloat(row.find('.amount').text()),
                    discount_percentage: parseFloat(row.find('.discount-type').val()),
                    discount_amount: parseFloat(row.find('.discount').text()),
                    net_amount_before_input_vat: parseFloat(row.find('.net').text()),
                    net_amount: parseFloat(row.find('.net').text()),
                    input_vat_percentage: parseFloat(row.find('.input_vat_percentage').val()),
                    input_vat_amount: parseFloat(row.find('.vat').text())
                });
            }
        });

        console.log('Item Data:', itemData); // Log item data

        formData.append('item_data', JSON.stringify(itemData));

        fetch('api/receiving_report_controller.php?action=add', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const transactionId = data.id; // Get the ID from the 'id' key
                console.log('Received Items ID:', transactionId); // Log the ID
                updatePrintStatusAndPrint(transactionId, 1);
            } else {
                console.error('Error saving Received Items:', data.message);
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Fetch Error:', error);
            alert('An error occurred. Please check the console and try again.');
        })
        .finally(() => {
            document.getElementById('loadingOverlay').style.display = 'none';
        });
}

    function updatePrintStatusAndPrint(id, printStatus) {
        if (!id) {
            console.error('Received Items ID is required.');
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Received Items ID is required.'
            });
            return;
        }

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
                    console.log('Print status updated, now printing Credit:', id);

                    const printFrame = document.getElementById('printFrame');
                    const printContentUrl = `print_receive_item?action=print&id=${id}, '_blank'`;

                    printFrame.src = printContentUrl;

                    printFrame.onload = function() {
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
</script>