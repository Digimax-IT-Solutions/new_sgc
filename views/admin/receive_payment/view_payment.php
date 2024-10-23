<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('receive_payment');
$accounts = ChartOfAccount::all();
$customers = Customer::all();
$payment_methods = PaymentMethod::all();

$newCrNo = Payment::getLastCrNo();

?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <style>
        .custom-select2-dropdown {
            z-index: 9999;
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-container--open .select2-dropdown {
            top: 100%;
            left: 0;
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
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-4">
                <div class="col-12">
                    <h1 class="h3"><strong>Customer Payment</strong></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="receive_payment">Payments</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Customer Payment</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <?php displayFlashMessage('add_payment') ?>
            <?php if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $payments = Payment::find($id);
                if ($payments) { ?>
            <form id="paymentForm" action="api/receive_payment_controller.php?action=add" method="POST">
                <input type="hidden" name="action" id="modalAction" value="add" />
                <div class="row">
                    <div class="col-12 col-lg-8">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Receive From</h5>
                            </div>
                            <div class="card-body">
                                <div class="row g-2">
                                    <!-- Receive From Section -->
                                    <div class="col-md-4 customer-details">
                                        <label for="customer_name" class="form-label">Customer</label>
                                        <select class="form-select form-select-sm select2" id="customer_name" name="customer_name" disabled>
                                            <option value="">Select Customer</option>
                                            <?php
                                                // Array to prevent duplicates
                                                $used_customers = [];
                                                $selected_customer_id = $payments->customer_id ?? ''; // Assuming this holds the selected customer ID

                                                foreach ($customers as $customer):
                                                    if (!in_array($customer->id, $used_customers)):
                                                        $used_customers[] = $customer->id; // Track used customer IDs
                                            ?>
                                                        <option value="<?= htmlspecialchars($customer->id) ?>" 
                                                                <?= $customer->id == $selected_customer_id ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($customer->customer_name) ?>
                                                        </option>
                                            <?php
                                                    endif;
                                                endforeach;
                                            ?>
                                        </select>
                                    </div>



                                    <div class="col-md-4 customer-details">
                                        <label for="credit_balance" class="form-label">Balance</label>
                                        <input type="text" class="form-control form-control-sm" id="credit_balance"
                                            name="credit_balance" 
                                            value="<?= number_format($payments->credit_balance, 2, '.', ',') ?>" readonly>
                                    </div>

                                    <!-- Payment Details Section -->
                                    <div class="row g-3">
                                        <div class="col-12 mt-4 mb-3">
                                            <h6 class="border-bottom pb-2"></h6>
                                        </div>
                                            <!-- First Row -->
                                            <div class="col-md-3 invoice-details">
                                                <label for="account_id" class="form-label">Account</label>
                                                <select class="form-select" id="account_id" name="account_id" disabled>
                                                    <?php foreach ($accounts as $account): ?>
                                                        <?php if ($account->account_description == 'Undeposited Funds'): ?>
                                                            <option value="<?= $account->id ?>" 
                                                                <?= ($account->id == $payments->account_id) ? 'selected' : '' ?>>
                                                                <?= $account->id ?> - <?= $account->account_description ?>
                                                            </option>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            
                                            <div class="col-md-3 invoice-details">
                                                <label for="payment_method" class="form-label">Payment Method</label>
                                                <select class="form-select" id="payment_method" name="payment_method" disabled>
                                                    <option value="">Select Payment</option>
                                                    <?php
                                                        // Array to prevent duplicates
                                                        $used_payment_methods = [];
                                                        $selected_payment_method = $payments->payment_method ?? ''; // Assuming this holds the selected payment method

                                                        // Payment Methods
                                                        foreach ($payment_methods as $payment_method):
                                                            if (!in_array($payment_method->payment_method_name, $used_payment_methods)):
                                                                $used_payment_methods[] = $payment_method->payment_method_name; // Track used payment methods
                                                    ?>
                                                                <option value="<?= htmlspecialchars($payment_method->payment_method_name) ?>" 
                                                                        <?= $payment_method->payment_method_name == $selected_payment_method ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($payment_method->payment_method_name) ?>
                                                                </option>
                                                    <?php
                                                            endif;
                                                        endforeach;
                                                    ?>
                                                </select>
                                            </div>

                                     

                                        <!-- Second Row -->
                                        <div class="row">
                                            <div class="col-md-3 invoice-details">
                                                <label for="cr_no" class="form-label">CR No.</label>
                                                <input type="text" class="form-control" id="cr_no" name="cr_no" 
                                                <?php if ($payments->status == 4): ?>
                                                    value="<?php echo htmlspecialchars($newCrNo); ?>" readonly>
                                                <?php else: ?>
                                                    value="<?php echo htmlspecialchars($payments->cr_no); ?>" disabled>
                                                <?php endif; ?>
                                            </div>
                                            <div class="col-md-3 invoice-details">
                                                <label for="reference_no" class="form-label">Reference / Check #</label>
                                                <input type="text" class="form-control" id="reference_no" name="reference_no" value="<?= htmlspecialchars($payments->ref_no) ?>" readonly>
                                            </div>
                                            <div class="col-md-3 invoice-details">
                                                <label for="payment_date" class="form-label">Payment Date</label>
                                                <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?= htmlspecialchars($payments->payment_date) ?>" readonly>
                                            </div>
                                        </div>

                                        <!-- Memo Field (Full Width) -->
                                        <div class="col-12 invoice-details">
                                            <label for="memo" class="form-label">Memo</label>
                                            <input type="text" class="form-control" id="memo" name="memo" value="<?= htmlspecialchars($payments->memo) ?>" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title">Ammounts For Selected Invoices</h5>
                                <?php if ($payments->status == 0): ?>
                                    <span class="badge bg-danger">Unpaid</span>
                                <?php elseif ($payments->status == 1): ?>
                                    <span class="badge bg-success">Paid</span>
                                <?php elseif ($payments->status == 2): ?>
                                    <span class="badge bg-warning">Partially Paid</span>
                                <?php elseif ($payments->status == 3): ?>
                                <span class="badge bg-secondary">Void</span>
                                <?php elseif ($payments->status == 4): ?>
                                    <span class="badge bg-info text-dark">Draft</span>
                                <?php endif; ?>
                            </div>
                            <div class="card-body">

                            <div class="row">
                                    <label class="col-sm-6 col-form-label">Open Balance:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end"
                                            id="summary_amount_due" name="summary_amount_due" 
                                            value="<?= number_format($payments->summary_amount_due, 2, '.', ',') ?>" readonly>
                                    </div>
                                </div>

                                <div class="row">
                                    <label class="col-sm-6 col-form-label fw-bold">Applied Payment:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end fw-bold"
                                            id="summary_applied_amount" name="summary_applied_amount"  value="<?= number_format($payments->summary_applied_amount, 2, '.', ',') ?>" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Discount & Credits Applied:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end"
                                            id="applied_credits_discount" name="applied_credits_discount"  value="<?= number_format($payments->applied_credits_discount, 2, '.', ',') ?>" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-6 col-form-label fw-bold">Remaining Balance:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end fw-bold"
                                            id="total_amount_due" name="total_amount_due" value="<?= number_format($payments->credit_balance, 2, '.', ',') ?>" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-center">
                                <?php if ($payments->status == 4): ?>
                                    <button type="button" id="saveDraftBtn" class="btn btn-secondary me-2">Update Draft</button>
                                            <button type="submit" class="btn btn-info me-2">Save as Final</button>
                                <?php elseif ($payments->status == 3): ?>
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
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Customers Invoice</h5>
                                <!-- <button type="button" class="btn btn-primary btn-sm" id="selectAll">
                                    Select All
                                </button> -->
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-sm table-hover" id="itemTable">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="selectAll" /></th>
                                                <th hidden>Invoice Account ID</th>
                                                <th>Invoice #</th>
                                                <th>Date</th>
                                                <th>Customer</th>
                                                <th>Original Amount</th>
                                                <th>Discount & Credit</th>
                                                <th>Amount Due</th>
                                                <th>Payment Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemTableBody" style="font-size: 16px;">
                                            <?php
                                            if ($payments) {
                                                foreach ($payments->details as $detail) {
                                                    $checked = ($detail['balance_due'] <= 0) ? 'checked' : '';
                                                    ?>
                                                    <tr data-invoice-id="<?= $detail['id'] ?>">
                                                        <td>
                                                            <input type="checkbox" class="invoice-select" data-invoice-id="<?= $detail['id'] ?>" <?= $checked ?> />
                                                        </td>
                                                        <td hidden><?= $detail['invoice_account_id'] ?></td>
                                                        <td><?= $detail['invoice_number'] ?></td>
                                                        <td><?= $detail['invoice_date'] ?></td>
                                                        <td><?= $detail['customer_name'] ?></td>
                                                        <td><?= number_format($detail['total_amount_due'], 2, '.', ',') ?></td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-primary add-discount-credit" data-invoice-id="<?= $detail['id'] ?>">Add Discount/Credit</button>
                                                        </td>
                                                        <td class="amount-due"><?= number_format($detail['balance_due'], 2, '.', ',') ?></td>
                                                        <td>
                                                            <input type="text" name="payments[<?= $detail['id'] ?>]" 
                                                                class="form-control payment-input" 
                                                                data-invoice-id="<?= $detail['id'] ?>" 
                                                                value="<?= number_format($detail['amount_applied'], 2, '.', ',') ?>" 
                                                                readonly>
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
                    // Receive found, you can now display the details
                } else {
                    // Handle the case where the Receive is not found
                    echo "Receive Payment not found.";
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
        <div class="message">Processing Payment</div>
    </div>
</div>

<?php require 'views/templates/footer.php' ?>

<iframe id="printFrame" style="display:none;"></iframe>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('reprintButton').addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Reprint Payment?',
                text: "Are you sure you want to reprint this payment?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reprint it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    printMethod(<?= $payments->id ?>, 2);  // Pass 2 for reprint
                }
            });
        });

        // Attach event listener for the void button
        document.getElementById('voidButton').addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Void Payment?',
                text: "Are you sure you want to void this payment? This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, void it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    voidCheck(<?= $payments->id ?>);
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

    function printMethod(id, printStatus) {
        showLoadingOverlay();

        $.ajax({
            url: 'api/receive_payment_controller.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'update_print_status',
                id: id,  // Changed from payment_id to id
                print_status: printStatus
            },
            success: function (response) {
                if (response.success) {
                    const printFrame = document.getElementById('printFrame');
                    const printContentUrl = `print_payment?action=print&id=${id}`;

                    printFrame.src = printContentUrl;

                    printFrame.onload = function () {
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
            error: function (jqXHR, textStatus, errorThrown) {
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
            url: 'api/receive_payment_controller.php',
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
                        text: 'Payment has been voided successfully.'
                    }).then(() => {
                        location.reload(); // Reload the page to reflect changes
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to void payment: ' + (response.message || 'Unknown error')
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                hideLoadingOverlay(); // Hide the loading overlay on error
                console.error('AJAX error:', textStatus, errorThrown);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while voiding the payment: ' + textStatus
                });
            }
        });
    }
</script>

<script>

    $(document).ready(function () {
        initializeSelect2();
        setupEventListeners();

      
    });
    // Update the submit event handler
    $('#paymentForm').submit(function (event) {
        event.preventDefault();
        
        // Check if there are any selected invoices
        if ($('#itemTableBody .invoice-select:checked').length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Invoices Selected',
                text: 'You must select at least one invoice before submitting the payment.'
            });
            return false;
        }

        const selectedInvoices = gatherSelectedInvoices();

        // Show loading overlay
        document.getElementById('loadingOverlay').style.display = 'flex';

        $.ajax({
            url: 'api/receive_payment_controller.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'update_draft',
                payment_id: <?= json_encode($payments->id) ?>,
                customer_id: <?= json_encode($payments->customer_id) ?>,
                payment_date: $('#payment_date').val(),
                payment_method_id: $('#payment_method').val(),
                account_id: <?= json_encode($payments->account_id) ?>,
                ref_no: $('#reference_no').val(),
                cr_no: $('#cr_no').val(),
                customer_name: $('#customer_name option:selected').text(),
                memo: $('#memo').val(),
                summary_amount_due: $('#summary_amount_due').val(),
                summary_applied_amount: $('#summary_applied_amount').val(),
                applied_credits_discount: $('#applied_credits_discount').val(),
                selected_invoices: JSON.stringify(selectedInvoices)
            },
            success: function (response) {
                document.getElementById('loadingOverlay').style.display = 'none';
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Payment submitted successfully!',
                        showCancelButton: true,
                        confirmButtonText: 'Print',
                        cancelButtonText: 'Close'
                    }).then((result) => {
                        if (result.isConfirmed && response.payment_id) {
                            printMethod(response.payment_id, 1);
                        } else {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error saving payment: ' + (response.message || 'Unknown error')
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                document.getElementById('loadingOverlay').style.display = 'none';
                console.error('AJAX error:', textStatus, errorThrown);
                console.log('Response Text:', jqXHR.responseText);
                let errorMessage = 'An error occurred while saving the payment';
                try {
                    const errorResponse = JSON.parse(jqXHR.responseText);
                    if (errorResponse && errorResponse.message) {
                        errorMessage += ': ' + errorResponse.message;
                    }
                } catch (e) {
                    errorMessage += ': ' + textStatus;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: errorMessage
                });
            }
        });
    });
    function gatherSelectedInvoices() {
        const selectedInvoices = [];
        document.querySelectorAll('#itemTable tbody tr').forEach(row => {
            const checkbox = row.querySelector('.invoice-select');
            if (checkbox && checkbox.checked) {
                const creditInputs = row.querySelectorAll('.credit-input');
                const creditNoInputs = row.querySelectorAll('.credit-no-input');
                const credits = Array.from(creditInputs).map((input, index) => ({
                    amount: parseFloat(input.value) || 0,
                    credit_no: creditNoInputs[index].value
                }));

                const discountAccountInput = row.querySelector('.discount-account-input');
                const discountAmountInput = row.querySelector('.discount-amount-input');

                const invoice = {
                    invoice_id: checkbox.dataset.invoiceId,
                    invoice_account_id: row.querySelector('td:nth-child(2)').textContent,
                    amount_applied: row.querySelector('.payment-input').value || '0',
                    credits: credits,
                    discount_account_id: discountAccountInput ? discountAccountInput.value : null,
                    discount_amount: discountAmountInput ? parseFloat(discountAmountInput.value) || 0 : 0
                };
                selectedInvoices.push(invoice);
            }
        });
        return selectedInvoices;
    }

    function initializeSelect2() {
        $('#customer_name').select2({
            theme: 'classic',
            allowClear: false,
            dropdownCssClass: 'custom-select2-dropdown'
        });

        $('#account_id').select2({
            theme: 'classic',
            allowClear: false,
            dropdownCssClass: 'custom-select2-dropdown'
        });
        $('#payment_method').select2({
            theme: 'classic',
            allowClear: false,
            dropdownCssClass: 'custom-select2-dropdown'
        });
    }

    function fetchAndDisplayInvoices(customerId) {
        const tableBody = document.getElementById('itemTableBody');
        const creditBalanceInput = document.getElementById('credit_balance');

        if (!customerId) {
            clearInvoiceTable();
            return;
        }

        showLoadingMessage(tableBody);

        fetch(`api/receive_payment_controller.php?action=get_invoices_and_credits&customer_id=${customerId}`)
            .then(response => response.json())
            .then(data => {
                creditBalanceInput.value = parseFloat(data.creditBalance).toFixed(2);

                if (data.invoices.length === 0) {
                    showNoInvoicesMessage(tableBody);
                    return;
                }

                creditMemos = data.creditMemos;
                discountMemos = data.discountMemos;
                populateInvoiceTable(data.invoices, tableBody);
            })
            .catch(error => {
                console.error('Error:', error);
                showErrorMessage(tableBody, error.message);
                creditBalanceInput.value = '';
            });
    }

    function clearInvoiceTable() {
        const tableBody = document.getElementById('itemTableBody');
        tableBody.innerHTML = '';
        document.getElementById('credit_balance').value = '';
    }

    function showLoadingMessage(tableBody) {
        tableBody.innerHTML = '<tr><td colspan="8">Loading...</td></tr>';
    }

    function showNoInvoicesMessage(tableBody) {
        tableBody.innerHTML = '<tr><td colspan="8">There are no unpaid invoices for this customer</td></tr>';
    }

    function showErrorMessage(tableBody, errorMessage) {
        tableBody.innerHTML = `<tr><td colspan="8">Error fetching invoices: ${errorMessage}. Please try again.</td></tr>`;
    }

    function populateInvoiceTable(invoices, tableBody) {
        tableBody.innerHTML = `
                    <tr>
                        <th><input type="checkbox" id="selectAll" /></th>
                        <th hidden>Invoice Account ID</th>
                        <th>Invoice #</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>Original Amount</th>
                        <th>Discount & Credit</th>
                        <th>Amount Due</th>
                        <th>Payment Amount</th>
                    
                    </tr>
                `;
        invoices.forEach(invoice => {
            const row = `
                    <tr data-invoice-id="${invoice.id}">
                        <td><input type="checkbox" class="invoice-select" data-invoice-id="${invoice.id}" /></td>
                        <td hidden>${invoice.invoice_account_id}</td>
                        <td>${invoice.invoice_number}</td>
                        <td>${invoice.invoice_date}</td>
                        <td>${invoice.customer_name}</td>
                        <td>${parseFloat(invoice.balance_due).toFixed(2)}</td>
                        <td>
                        <button type="button" class="btn btn-sm btn-primary add-discount-credit" data-invoice-id="${invoice.id}">Add Discount/Credit</button>
                        </td>
                        <td class="amount-due">${parseFloat(invoice.balance_due).toFixed(2)}</td>
                        <td><input type="number" name="payments[${invoice.id}]" class="form-control payment-input" data-invoice-id="${invoice.id}" step="0.01" min="0" max="${invoice.balance_due}"></td>
                
                    </tr>
                `;
            tableBody.innerHTML += row;
        });

        // Add event listeners
        document.querySelectorAll('.payment-input').forEach(input => {
            input.addEventListener('input', updatePaymentSummary);
        });

        document.querySelectorAll('.add-discount-credit').forEach(button => {
            button.addEventListener('click', handleDiscountCredit);
        });

        document.querySelectorAll('.invoice-select').forEach(checkbox => {
            checkbox.addEventListener('change', updatePaymentSummary);
        });

        document.getElementById('selectAll').addEventListener('change', function () {
            const isChecked = this.checked;
            document.querySelectorAll('.invoice-select').forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updatePaymentSummary();
        });

        updatePaymentSummary();
    }

    function updatePaymentSummary() {
        let totalAmountDue = 0;
        let totalAppliedAmount = 0;
        let totalDiscountAndCredits = 0;

        // Iterate over each row in the table
        document.querySelectorAll('#itemTable tbody tr').forEach(row => {
            const checkbox = row.querySelector('.invoice-select');
            if (checkbox && checkbox.checked) {
                const amountDueElement = row.querySelector('.amount-due');
                const paymentInput = row.querySelector('.payment-input');
                const creditInput = row.querySelector('.credit-input');

                // Calculate total amount due
                if (amountDueElement) {
                    const amountDue = parseFloat(amountDueElement.textContent) || 0;
                    totalAmountDue += amountDue;
                }

                // Calculate total applied amount
                if (paymentInput) {
                    const paymentAmount = parseFloat(paymentInput.value) || 0;
                    totalAppliedAmount += paymentAmount;
                }

                // Calculate total discounts and credits
                if (creditInput) {
                    const creditAmount = parseFloat(creditInput.value) || 0;
                    totalDiscountAndCredits += creditAmount;
                }
            }
        });

        // Update the summary fields
        const summaryAmountDue = document.getElementById('summary_amount_due');
        const summaryAppliedAmount = document.getElementById('summary_applied_amount');
        const summaryDiscountCredits = document.getElementById('applied_credits_discount');
        const totalAmountDueElement = document.getElementById('total_amount_due');

        if (summaryAmountDue) summaryAmountDue.value = totalAmountDue.toFixed(2);
        if (summaryAppliedAmount) summaryAppliedAmount.value = totalAppliedAmount.toFixed(2);
        if (summaryDiscountCredits) summaryDiscountCredits.value = totalDiscountAndCredits.toFixed(2);
        if (totalAmountDueElement) totalAmountDueElement.value = (totalAmountDue - totalAppliedAmount - totalDiscountAndCredits).toFixed(2);

        // Debugging output
        console.log('Total Amount Due:', totalAmountDue.toFixed(2));
        console.log('Total Applied Amount:', totalAppliedAmount.toFixed(2));
        console.log('Total Discounts and Credits:', totalDiscountAndCredits.toFixed(2));
        console.log('Total Amount Due Element:', totalAmountDueElement.value);
    }


    function setupEventListeners() {
        $('#customer_name').on('change', function () {
            fetchAndDisplayInvoices(this.value);
        });
        $('#payment_amount').on('input', updatePaymentSummary);

        // Add this line
        document.getElementById('paymentForm').addEventListener('submit', submitPayment);
    }

    function handleDiscountCredit(event) {
        const invoiceId = event.target.dataset.invoiceId;
        // Implement logic to add discount or credit
        // This could open a modal or prompt for input
        console.log(`Add discount/credit for invoice ${invoiceId}`);
        // After applying discount/credit, update the payment summary
        updatePaymentSummary();
    }


    function submitPayment(event) {
        event.preventDefault();

        // Show the loading overlay
        document.getElementById('loadingOverlay').style.display = 'flex';

        // Create and play the audio
        const audio = new Audio('photos/rr.mp3');
        audio.play();

        // Gather selected invoices and their details
        function gatherSelectedInvoices() {
        const selectedInvoices = [];
        document.querySelectorAll('#itemTable tbody tr').forEach(row => {
            const checkbox = row.querySelector('.invoice-select');
            if (checkbox && checkbox.checked) {
                const creditInputs = row.querySelectorAll('.credit-input');
                const creditNoInputs = row.querySelectorAll('.credit-no-input');
                const credits = Array.from(creditInputs).map((input, index) => ({
                    amount: parseFloat(input.value) || 0,
                    credit_no: creditNoInputs[index].value
                }));

                const discountAccountInput = row.querySelector('.discount-account-input');
                const discountAmountInput = row.querySelector('.discount-amount-input');

                const invoice = {
                    invoice_id: checkbox.dataset.invoiceId,
                    invoice_account_id: row.querySelector('td:nth-child(2)').textContent,
                    amount_applied: row.querySelector('.payment-input').value || '0',
                    credits: credits,
                    discount_account_id: discountAccountInput ? discountAccountInput.value : null,
                    discount_amount: discountAmountInput ? parseFloat(discountAmountInput.value) || 0 : 0
                };
                selectedInvoices.push(invoice);
            }
        });
        return selectedInvoices;
    }

        const selectedInvoices = gatherSelectedInvoices();


        // Check if there are any selected invoices
        if (selectedInvoices.length === 0) {
            alert("Please select at least one invoice to pay");
            document.getElementById('loadingOverlay').style.display = 'none'; // Hide overlay
            return; // Stop form submission
        }

        // Add selected invoices to a hidden input field
        const selectedInvoicesInput = document.createElement('input');
        selectedInvoicesInput.type = 'hidden';
        selectedInvoicesInput.name = 'selected_invoices';
        selectedInvoicesInput.value = JSON.stringify(selectedInvoices);

        console.log(selectedInvoicesInput.value);


        // Submit the form after a short delay to allow the audio to start playing
        setTimeout(() => {
        }, 800);
    }

    // Attach the submitPayment function to the form submission

</script>