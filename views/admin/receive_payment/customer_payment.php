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
$wtaxes = WithholdingTax::all();  // Fetch the withholding taxes
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
                    <h1 class="h3"><strong>Collection Receipt</strong></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="receive_payment">Payments</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Create Collection Receipt</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <?php displayFlashMessage('add_payment') ?>
            <form id="invoiceForm" action="api/receive_payment_controller.php?action=add" method="POST">
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
                                        <select class="form-select form-select-sm select2" id="customer_name"
                                            name="customer_name" required>
                                            <option value="">Select Customer</option>
                                            <?php foreach ($customers as $customer): ?>
                                                <option value="<?= $customer->id ?>"><?= $customer->customer_name ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4 customer-details">
                                        <label for="credit_balance" class="form-label">Balance</label>
                                        <input type="text" class="form-control form-control-sm" id="credit_balance"
                                            name="credit_balance" readonly>
                                    </div>



                                    <!-- Payment Details Section -->
                                    <div class="row g-3">
                                        <div class="col-12 mt-4 mb-3">
                                            <h6 class="border-bottom pb-2"></h6>
                                        </div>

                                        <!-- First Row -->
                                        <div class="col-md-4 invoice-details">
                                            <label for="account_id" class="form-label">Account</label>
                                            <select class="form-select" id="account_id" name="account_id">
                                                <?php foreach ($accounts as $account): ?>
                                                    <?php if ($account->account_description == 'Undeposited Funds'): ?>
                                                        <option value="<?= $account->id ?>">
                                                            <?= $account->id ?> - <?= $account->account_description ?>
                                                        </option>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="col-md-4 invoice-details">
                                            <label for="payment_method" class="form-label">Payment Method</label>
                                            <select class="form-select" id="payment_method" name="payment_method"
                                                required>
                                                <option value="">Select Payment</option>
                                                <?php foreach ($payment_methods as $payment_method): ?>
                                                    <option value="<?= $payment_method->id ?>">
                                                        <?= $payment_method->payment_method_name ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>


                                        <!-- Second Row -->
                                        <div class="row">
                                            <div class="col-md-3 invoice-details">
                                                <label for="cr_no" class="form-label">CR No.</label>
                                                <input type="text" class="form-control" id="cr_no" name="cr_no"
                                                    value="<?php echo $newCrNo; ?>" readonly>
                                            </div>
                                            <div class="col-md-3 invoice-details">
                                                <label for="reference_no" class="form-label">Reference / Check #</label>
                                                <input type="text" class="form-control" id="reference_no"
                                                    name="reference_no" required>
                                            </div>
                                            <div class="col-md-3 invoice-details">
                                                <label for="payment_date" class="form-label">Payment Date</label>
                                                <input type="date" class="form-control" id="payment_date"
                                                    name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>
                                            </div>
                                        </div>

                                        <!-- Memo Field (Full Width) -->
                                        <div class="col-12 invoice-details">
                                            <label for="memo" class="form-label">Memo</label>
                                            <input type="text" class="form-control" id="memo" name="memo">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="card-title">Amounts For Selected Invoices</h5>
                            </div>
                            <div class="card-body">

                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Open Balance:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end"
                                            id="summary_amount_due" name="summary_amount_due" value="0.00" readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-6 col-form-label fw-bold">Applied Payment:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end fw-bold"
                                            id="summary_applied_amount" name="summary_applied_amount" value="0.00"
                                            readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-6 col-form-label">Discount & Credits Applied:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end"
                                            id="applied_credits_discount" name="applied_credits_discount" value="0.00"
                                            readonly>
                                    </div>
                                </div>
                                <div class="row">
                                    <label class="col-sm-6 col-form-label fw-bold">Remaining Balance:</label>
                                    <div class="col-sm-6">
                                        <input type="text" class="form-control-plaintext text-end fw-bold"
                                            id="total_amount_due" name="total_amount_due" value="0.00" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="button" id="saveDraftBtn" class="btn btn-secondary me-2">Save as
                                    Draft</button>
                                <button type="submit" class="btn btn-info me-2">Save and Print</button>
                                <button type="reset" class="btn btn-danger">Clear</button>
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
                                        <tbody id="itemTableBody" style="font-size: 16px;">
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
        <div class="message">Processing Payment</div>
    </div>
</div>

<?php require 'views/templates/footer.php' ?>

<iframe id="printFrame" style="display:none;"></iframe>


<script>
    $(document).ready(function () {
        $('#invoiceForm').submit(function (event) {
            event.preventDefault();
            const selectedInvoices = gatherSelectedInvoices();

            // Show the loading overlay
            document.getElementById('loadingOverlay').style.display = 'flex';

            // Check if there are any selected invoices
            if (selectedInvoices.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please select at least one invoice to pay'
                });
                document.getElementById('loadingOverlay').style.display = 'none';
                return;
            }

            // Prepare the form data
            const formData = new FormData(this);
            formData.append('selected_invoices', JSON.stringify(selectedInvoices));

            // Use AJAX to submit the form
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    document.getElementById('loadingOverlay').style.display = 'none';

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Payment recorded successfully!',
                            showCancelButton: true,
                            confirmButtonText: 'Print',
                            cancelButtonText: 'OK'
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
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving the payment: ' + textStatus
                    });
                }
            });
        });
    });

    function printMethod(id, printStatus) {
        // First, update the print status
        $.ajax({
            url: 'api/receive_payment_controller.php',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'update_print_status',
                id: id,
                print_status: printStatus
            },
            success: function (response) {
                if (response.success) {
                    console.log('Print status updated, now printing payment:', id);
                    const printFrame = document.getElementById('printFrame');
                    const printContentUrl = `print_payment?action=print&id=${id}`;

                    printFrame.src = printContentUrl;

                    printFrame.onload = function () {
                        printFrame.contentWindow.focus();
                        printFrame.contentWindow.print();
                    };
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update print status: ' + (response.message || 'Unknown error')
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
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


<script>

    $(document).ready(function () {
        initializeSelect2();
        setupEventListeners();

        $('button[type="reset"]').on('click', function (e) {
            e.preventDefault(); // Prevent default reset behavior

            // Clear all input fields except #cr_no
            $('input').not('#cr_no').val('');

            // Reset all select elements to their default option
            $('select').each(function () {
                $(this).val($(this).find("option:first").val()).trigger('change');
            });

            // Clear the item table
            $('#itemTableBody').empty();

            // Reset all Select2 dropdowns
            $('.select2').val(null).trigger('change');

            // Reset summary section
            $('#gross_amount, #total_discount_amount, #net_amount_due, #total_vat_amount, #vatable_amount, #zero_rated_amount, #vat_exempt_amount, #tax_withheld_amount, #total_amount_due').val('0.00');

            // Clear hidden inputs
            $('#item_data, #customer_name_hidden, #discount_account_id, #tax_withheld_account_id').val('');

            // Reset date input to current date
            $('#invoice_date').val(new Date().toISOString().split('T')[0]);

            // Optionally, you can add a confirmation message
            Swal.fire({
                icon: 'success',
                title: 'Cleared',
                text: 'All fields have been reset except CR No.',
                timer: 1800,
                showConfirmButton: false
            });
        });

        $('#saveDraftBtn').click(function (e) {
            e.preventDefault();
            saveDraft();
        });

    });

    function saveDraft() {
        // Gather the selected invoices and their payment details
        const selectedInvoices = gatherSelectedInvoices();

        // Check if there are any selected invoices
        if (selectedInvoices.length === 0) {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please select at least one invoice before saving as draft'
            });
            return;
        }

        // Prepare the form data
        const formData = new FormData($('#invoiceForm')[0]);
        formData.append('action', 'save_draft');
        formData.append('selected_invoices', JSON.stringify(selectedInvoices));

        // Show the loading overlay
        document.getElementById('loadingOverlay').style.display = 'flex';

        // Use AJAX to submit the form
        $.ajax({
            url: 'api/receive_payment_controller.php',
            type: 'POST',
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                document.getElementById('loadingOverlay').style.display = 'none';

                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Payment receipt saved as draft successfully!',
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
            error: function (jqXHR, textStatus, errorThrown) {
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
                creditMemos = data.creditMemos; // Store credit memos globally

                if (data.invoices.length === 0) {
                    showNoInvoicesMessage(tableBody);
                    return;
                }

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
                        <td><input type="text" name="payments[${invoice.id}]" class="form-control payment-input" data-invoice-id="${invoice.id}""></td>
                
                    </tr>
                `;
            tableBody.innerHTML += row;
        });

        // Add event listeners
        document.querySelectorAll('.payment-input').forEach(input => {
            input.addEventListener('input', updateAmountDue);
        });

        document.querySelectorAll('.add-discount-credit').forEach(button => {
            button.addEventListener('click', handleDiscountCredit);
        });

        document.querySelectorAll('.add-discount-credit').forEach(button => {
            button.addEventListener('click', displayCreditMemos);
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

    function updateAmountDue(event) {
        const paymentInput = event.target;
        const row = paymentInput.closest('tr');
        const amountDueCell = row.querySelector('.amount-due');
        const balanceDue = parseFloat(row.cells[5].textContent); // Original amount (balance_due)
        const paymentAmount = parseFloat(paymentInput.value) || 0;

        const newAmountDue = Math.max(balanceDue - paymentAmount, 0);
        amountDueCell.textContent = newAmountDue.toFixed(2);

        // Update the max value of the payment input
        paymentInput.max = newAmountDue.toFixed(2);

        // Update payment summary
        updatePaymentSummary();
    }

    function updatePaymentSummary() {
        let totalAmountDue = parseFloat(document.getElementById('credit_balance').value) || 0;
        let totalAppliedAmount = 0;
        let totalDiscountCredit = 0;

        document.querySelectorAll('#itemTable tbody tr').forEach(row => {
            const checkbox = row.querySelector('.invoice-select');
            if (checkbox && checkbox.checked) {
                const paymentInput = row.querySelector('.payment-input');
                const creditInputs = row.querySelectorAll('.credit-input');
                const discountAmountInput = row.querySelector('.discount-amount-input');

                if (paymentInput) {
                    const paymentAmount = parseFloat(paymentInput.value) || 0;
                    totalAppliedAmount += paymentAmount;
                }

                creditInputs.forEach(input => {
                    const discountCreditAmount = parseFloat(input.value) || 0;
                    totalDiscountCredit += discountCreditAmount;
                });

                if (discountAmountInput) {
                    const discountAmount = parseFloat(discountAmountInput.value) || 0;
                    totalDiscountCredit += discountAmount;
                }
            }
        });

        const summaryAmountDue = document.getElementById('summary_amount_due');
        const summaryAppliedAmount = document.getElementById('summary_applied_amount');
        const summaryDiscountCredits = document.getElementById('applied_credits_discount');
        const totalAmountDueElement = document.getElementById('total_amount_due');

        if (summaryAmountDue) summaryAmountDue.value = totalAmountDue.toFixed(2);
        if (summaryAppliedAmount) summaryAppliedAmount.value = totalAppliedAmount.toFixed(2);
        if (summaryDiscountCredits) summaryDiscountCredits.value = totalDiscountCredit.toFixed(2);
        if (totalAmountDueElement) totalAmountDueElement.value = (totalAmountDue - totalAppliedAmount - totalDiscountCredit).toFixed(2);
    }

    function setupEventListeners() {
        $('#customer_name').on('change', function () {
            fetchAndDisplayInvoices(this.value);
        });
        $('#payment_amount').on('input', updatePaymentSummary);

    }

    // Update the handleDiscountCredit function to display credit memos
    function handleDiscountCredit(event) {
        const invoiceId = event.target.dataset.invoiceId;
        console.log(`Add discount/credit for invoice ${invoiceId}`);
        displayCreditMemos(creditMemos, invoiceId);
    }

    function displayCreditMemos(creditMemos, invoiceId) {

        // Populate dropdowns with accounts from PHP
        const accounts = <?php echo json_encode($accounts); ?>;
        let accountDropdownOptions = '';
        $.each(accounts, function (index, account) {
            accountDropdownOptions += `<option value="${account.id}">${account.account_description}</option>`;
        });
        
        const wtaxes = <?php echo json_encode($wtaxes); ?>;
        let wtaxDropdownOptions = '';
        $.each(wtaxes, function (index, wtax) {
            wtaxDropdownOptions += `<option value="${wtax.id}">${wtax.wtax_description}</option>`;
        });


        // Create modal HTML with Font Awesome icons and a Discount tab
        const modalHtml = `
            <div class="modal fade" id="creditMemoModal" tabindex="-1" role="dialog" aria-labelledby="creditMemoModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="creditMemoModalLabel"><i class="fas fa-file-invoice-dollar"></i> Available Credit Memos</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <!-- Tabs navigation -->
                            <ul class="nav nav-tabs" id="creditMemoTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="memos-tab" data-toggle="tab" href="#memos" role="tab" aria-controls="memos" aria-selected="true">
                                        <i class="fas fa-file-invoice-dollar"></i> Credit Memos
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="discount-tab" data-toggle="tab" href="#discount" role="tab" aria-controls="discount" aria-selected="false">
                                        <i class="fas fa-tags"></i> Discount
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="withheld-tab" data-toggle="tab" href="#withheld" role="tab" aria-controls="withheld" aria-selected="false">
                                        <i class="fas fa-percent"></i> Tax Withheld
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content" id="creditMemoTabsContent">
                                <!-- Credit Memos Tab Content -->
                                <div class="tab-pane fade show active" id="memos" role="tabpanel" aria-labelledby="memos-tab">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="selectAll"></th>
                                                <th><i class="fas fa-receipt"></i> Credit Memo #</th>
                                                <th><i class="fas fa-calendar-alt"></i> Date</th>
                                                <th><i class="fas fa-money-bill-wave"></i> Amount (₱)</th>
                                                <th><i class="fas fa-hand-holding-usd"></i> Apply Amount</th>
                                                <th><i class="fas fa-cogs"></i> Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="creditMemoTableBody">
                                            ${creditMemos.map(memo => `
                                                <tr>
                                                    <td>
                                                        <input type="checkbox" class="credit-memo-checkbox" data-credit-id="${memo.id}" data-credit-no="${memo.credit_no}">
                                                        <input type="hidden" name="credit_no" id="credit_no" value="">
                                                    </td>
                                                    <td>${memo.credit_no}</td>
                                                    <td>${memo.credit_date}</td>
                                                    <td>₱${parseFloat(memo.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                                                    <td>
                                                        <input type="number" class="form-control apply-amount" min="1" max="${parseFloat(memo.amount).toFixed(2)}" step="0.01" value="1" disabled>
                                                        <small class="form-text text-muted"><i class="fas fa-info-circle"></i> Enter an amount between 1 and ₱${parseFloat(memo.amount).toFixed(2)}</small>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-primary apply-credit" data-credit-id="${memo.id}" data-amount="${parseFloat(memo.amount).toFixed(2)}" data-invoice-id="${invoiceId}" disabled>
                                                            <i class="fas fa-check-circle"></i> Apply
                                                        </button>
                                                    </td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Discount Tab Content -->
                                <div class="tab-pane fade" id="discount" role="tabpanel" aria-labelledby="discount-tab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="discountAccount"><i class="fas fa-university"></i> Account</label>
                                                <select class="form-control form-control-sm account-dropdown" id="discountAccount" name="discount_account_id[]">${accountDropdownOptions}</select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="discountAmount"><i class="fas fa-money-bill"></i> Amount (₱)</label>
                                                <input type="number" class="form-control form-control-sm" id="discountAmount" name="discount_amount" min="0" step="0.01" placeholder="Enter discount amount">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Tax Withheld Tab Content -->
                                <div class="tab-pane fade" id="withheld" role="tabpanel" aria-labelledby="withheld-tab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="withheldAccount"><i class="fas fa-university"></i> Account</label>
                                                <select class="form-control form-control-sm account-dropdown" id="withheldAccount" name="withheld_account_id[]">${wtaxDropdownOptions}</select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="withheldAmount"><i class="fas fa-percent"></i> Amount (₱)</label>
                                                <input type="number" class="form-control form-control-sm" id="withheldAmount" name="withheld_amount" min="0" step="0.01" placeholder="Enter withheld amount">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                            <button type="button" class="btn btn-primary d-none" id="applySelectedMemos"><i class="fas fa-check-circle"></i> Apply Selected</button>
                            <button type="button" class="btn btn-success d-none" id="applyDiscount"><i class="fas fa-tag"></i> Apply Discount</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Append modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);

        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('creditMemoModal'));
        modal.show();

        // Initialize Select2 for the dropdown in the Discount tab
        $('#discountAccount').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Select Account',
            allowClear: false,
            dropdownParent: $('#creditMemoModal'),
            language: {
                noResults: function() {
                    return "No matching accounts found";
                }
            }
        });

        // Replace accountDropdownOptions in the withheld tab with wtaxDropdownOptions
        document.getElementById('withheldAccount').innerHTML = wtaxDropdownOptions;

        // Initialize Select2 for the dropdown in the Withheld tab
        $('#withheldAccount').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Select Withholding Tax',
            allowClear: false,
            dropdownParent: $('#creditMemoModal'),
            language: {
                noResults: function() {
                    return "No matching withholding taxes found";
                }
            }
        });



        // Initialize tabs
        const tabElements = document.querySelectorAll('#creditMemoTabs a[data-toggle="tab"]');
        tabElements.forEach(tab => {
            tab.addEventListener('click', function (event) {
                event.preventDefault();
                const targetTab = new bootstrap.Tab(event.target);
                targetTab.show();
            });
        });

        // Toggle button visibility based on the active tab
        function updateButtonVisibility() {
            const activeTab = document.querySelector('#creditMemoTabs .nav-link.active').getAttribute('href');
            const applySelectedMemosButton = document.getElementById('applySelectedMemos');
            const applyDiscountButton = document.getElementById('applyDiscount');

            if (activeTab === '#memos') {
                applySelectedMemosButton.classList.remove('d-none');
                applyDiscountButton.classList.add('d-none');
            } else if (activeTab === '#discount') {
                applySelectedMemosButton.classList.add('d-none');
                applyDiscountButton.classList.remove('d-none');
            }
        }

        // Initial button visibility setup
        updateButtonVisibility();

        // Update button visibility on tab change
        tabElements.forEach(tab => {
            tab.addEventListener('shown.bs.tab', updateButtonVisibility);
        });

        // Enable/Disable input fields and buttons based on checkbox selection
        document.querySelectorAll('.credit-memo-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                const row = this.closest('tr');
                const inputField = row.querySelector('.apply-amount');
                const applyButton = row.querySelector('.apply-credit');

                if (this.checked) {
                    inputField.disabled = false;
                    applyButton.disabled = false;
                } else {
                    inputField.disabled = true;
                    applyButton.disabled = true;
                }
            });
        });

        // Select/Deselect all checkboxes
        document.getElementById('selectAll').addEventListener('change', function () {
            const allChecked = this.checked;
            document.querySelectorAll('.credit-memo-checkbox').forEach(checkbox => {
                checkbox.checked = allChecked;
                checkbox.dispatchEvent(new Event('change'));
            });
        });

        // Event listener for Apply Discount button
        document.getElementById('applyDiscount').addEventListener('click', function () {
            const discountAccountId = document.getElementById('discountAccount').value;
            const discountAmount = parseFloat(document.getElementById('discountAmount').value);

            if (!discountAccountId || isNaN(discountAmount) || discountAmount <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Discount',
                    text: 'Please select an account and enter a valid discount amount.',
                });
                return;
            }

            // Apply discount
            applyDiscount(discountAccountId, discountAmount, invoiceId);

            // Close the modal
            $('#creditMemoModal').modal('hide');
        });


        // Event listener for Apply Selected button
        document.getElementById('applySelectedMemos').addEventListener('click', function () {
            const selectedMemos = [];
            document.querySelectorAll('.credit-memo-checkbox:checked').forEach(checkbox => {
                const row = checkbox.closest('tr');
                const creditId = checkbox.dataset.creditId;
                const creditNo = checkbox.dataset.creditNo;
                const inputField = row.querySelector('.apply-amount');
                const amount = parseFloat(inputField.value);

                selectedMemos.push({ creditId, creditNo, amount });
            });

            if (selectedMemos.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'No Memo Selected',
                    text: 'Please select at least one credit memo before applying.',
                });
                return;
            }

            // Apply selected credit memos
            applyCreditMemos(selectedMemos, invoiceId);

            // Update UI to reflect the applied credits
            selectedMemos.forEach(memo => {
                const row = document.querySelector(`[data-credit-id="${memo.creditId}"]`).closest('tr');
                row.classList.add('table-success');
                row.querySelector('.apply-credit').innerHTML = '<i class="fas fa-check-circle"></i> Applied';
                row.querySelector('.apply-credit').disabled = true;
                row.querySelector('.apply-amount').disabled = true;
                row.querySelector('.credit-memo-checkbox').disabled = true;
            });

            // Update the applied credits/discounts section dynamically
            updatePaymentSummary(selectedMemos.reduce((total, memo) => total + memo.amount, 0));


            // Close the modal
            $('#creditMemoModal').modal('hide');
        });

        // Real-time validation for apply amount inputs
        document.querySelectorAll('.apply-amount').forEach(input => {
            input.addEventListener('input', function () {
                const maxAmount = parseFloat(this.max);
                const amount = parseFloat(this.value);

                if (amount >= 1 && amount <= maxAmount) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            });
        });
    }

    function applyDiscount(discountAccountId, discountAmount, invoiceId) {
        const invoiceRow = document.querySelector(`#itemTable tbody tr[data-invoice-id="${invoiceId}"]`);
        if (invoiceRow) {
            const amountDueCell = invoiceRow.querySelector('.amount-due');
            let currentAmountDue = parseFloat(amountDueCell.textContent);

            // Update amount due
            currentAmountDue = Math.max(currentAmountDue - discountAmount, 0);
            amountDueCell.textContent = currentAmountDue.toFixed(2);

            // Add or update discount inputs
            updateOrAddInput(invoiceRow, 'discount-account-input', discountAccountId, 'hidden');
            updateOrAddInput(invoiceRow, 'discount-amount-input', discountAmount, 'hidden');

            // Update the max value of the payment input
            const paymentInput = invoiceRow.querySelector('.payment-input');
            paymentInput.max = currentAmountDue;

            // Update payment summary
            updatePaymentSummary();

            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Discount Applied',
                text: `A discount of ₱${discountAmount.toFixed(2)} has been applied to the invoice.`,
            });
        }
    }

    // Update applyCreditMemo to use the correct invoice row
    function applyCreditMemos(selectedMemos, invoiceId) {
        const invoiceRow = document.querySelector(`#itemTable tbody tr[data-invoice-id="${invoiceId}"]`);
        if (invoiceRow) {
            const amountDueCell = invoiceRow.querySelector('.amount-due');
            let currentAmountDue = parseFloat(amountDueCell.textContent);

            // Remove existing credit inputs
            invoiceRow.querySelectorAll('.credit-input, .credit-no-input').forEach(el => el.remove());

            selectedMemos.forEach(memo => {
                currentAmountDue = Math.max(currentAmountDue - memo.amount, 0);

                // Add new credit input
                const creditInput = document.createElement('input');
                creditInput.type = 'hidden';
                creditInput.className = 'credit-input';
                creditInput.name = `credits[${invoiceId}][]`;
                creditInput.value = memo.amount;
                invoiceRow.appendChild(creditInput);

                // Add new credit_no input
                const creditNoInput = document.createElement('input');
                creditNoInput.type = 'hidden';
                creditNoInput.className = 'credit-no-input';
                creditNoInput.name = `credit_nos[${invoiceId}][]`;
                creditNoInput.value = memo.creditNo;
                invoiceRow.appendChild(creditNoInput);
            });

            amountDueCell.textContent = currentAmountDue.toFixed(2);

            // Update the max value of the payment input
            const paymentInput = invoiceRow.querySelector('.payment-input');
            paymentInput.max = currentAmountDue;

            // Update payment summary
            updatePaymentSummary();

            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'Credit Memos Applied',
                text: `A total of ${selectedMemos.length} credit memo(s) have been applied to the invoice.`,
            });
        }
    }


    function updateOrAddInput(row, className, value, type = 'text') {
        let input = row.querySelector(`.${className}`);
        if (!input) {
            input = document.createElement('input');
            input.type = type;
            input.className = className;
            input.name = className === 'discount-account-input' ? 'discount_account_id[]' : 'discount_amount[]';
            row.appendChild(input);
        }
        input.value = value;
    }



    function submitPayment(event) {
        event.preventDefault();

        // Show the loading overlay
        document.getElementById('loadingOverlay').style.display = 'flex';

        // Create and play the audio
        const audio = new Audio('photos/rr.mp3');
        audio.play();

        // Update the gatherSelectedInvoices function
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

        document.getElementById('invoiceForm').appendChild(selectedInvoicesInput);

        // Submit the form after a short delay to allow the audio to start playing
        setTimeout(() => {
            document.getElementById('invoiceForm').submit();
        }, 800);
    }

</script>