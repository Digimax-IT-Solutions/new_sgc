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
    $(document).ready(function() {
        $('#invoiceForm').submit(function(event) {
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
                success: function(response) {
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
                error: function(jqXHR, textStatus, errorThrown) {
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
            success: function(response) {
                if (response.success) {
                    console.log('Print status updated, now opening payment in new tab:', id);
                    const printContentUrl = `print_payment?action=print&id=${id}`;

                    // Open the URL in a new tab and trigger print
                    const printWindow = window.open(printContentUrl, '_blank');

                    // Wait for the content to load before triggering print
                    printWindow.onload = function() {
                        printWindow.focus();
                        printWindow.print();
                    };
                } else {
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


<script>
    $(document).ready(function() {
        initializeSelect2();
        setupEventListeners();

        $('button[type="reset"]').on('click', function(e) {
            e.preventDefault(); // Prevent default reset behavior

            // Clear all input fields except #cr_no
            $('input').not('#cr_no').val('');

            // Reset all select elements to their default option
            $('select').each(function() {
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

        $('#saveDraftBtn').click(function(e) {
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
            success: function(response) {
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
            error: function(jqXHR, textStatus, errorThrown) {
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
                        <td>${invoice.balance_due}</td>
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

        document.getElementById('selectAll').addEventListener('change', function() {
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
        const discountInput = row.querySelector('.discount-amount-input');
        const creditInput = row.querySelector('.credit-input');

        // Get discount and credit amounts
        const discountAmount = discountInput ? parseFloat(discountInput.value) || 0 : 0;
        const creditAmount = creditInput ? parseFloat(creditInput.value) || 0 : 0;

        // Calculate new amount due considering payment, discount, and credit
        const newAmountDue = Math.max(balanceDue - paymentAmount - discountAmount - creditAmount, 0);
        amountDueCell.textContent = newAmountDue.toFixed(2);

        // Update the max value of the payment input
        paymentInput.max = newAmountDue.toFixed(2);

        // Update payment summary
        updatePaymentSummary();
    }

    function updatePaymentSummary() {
        let totalAmountDue = 0;
        let totalAppliedAmount = 0;
        let totalDiscountCredit = 0;

        document.querySelectorAll('#itemTable tbody tr').forEach(row => {
            const checkbox = row.querySelector('.invoice-select');
            if (checkbox && checkbox.checked) { // Only process selected rows
                const balanceDueCell = row.cells[5]; // Access the original balance_due cell

                if (balanceDueCell) {
                    const balanceDue = parseFloat(balanceDueCell.textContent) || 0;
                    totalAmountDue += balanceDue; // Sum the original balance_due for selected rows
                }

                const paymentInput = row.querySelector('.payment-input');
                const discountInput = row.querySelector('.discount-amount-input');
                const creditInput = row.querySelector('.credit-input');

                if (paymentInput) {
                    const paymentAmount = parseFloat(paymentInput.value) || 0;
                    totalAppliedAmount += paymentAmount;
                }

                if (discountInput) {
                    const discountAmount = parseFloat(discountInput.value) || 0;
                    totalDiscountCredit += discountAmount;
                }

                if (creditInput) {
                    const creditAmount = parseFloat(creditInput.value) || 0;
                    totalDiscountCredit += creditAmount;
                }
            }
        });

        // Update the relevant summary fields
        const summaryAmountDue = document.getElementById('summary_amount_due');
        const summaryAppliedAmount = document.getElementById('summary_applied_amount');
        const summaryDiscountCredits = document.getElementById('applied_credits_discount');
        const totalAmountDueElement = document.getElementById('total_amount_due');

        // Set summaryAmountDue to reflect only the sum of the original balance_due values
        if (summaryAmountDue) summaryAmountDue.value = totalAmountDue.toFixed(2);
        if (summaryAppliedAmount) summaryAppliedAmount.value = totalAppliedAmount.toFixed(2);
        if (summaryDiscountCredits) summaryDiscountCredits.value = totalDiscountCredit.toFixed(2);

        // Calculate total amount due as per the new formula
        const calculatedTotalAmountDue = totalAmountDue - (totalAppliedAmount + totalDiscountCredit);
        if (totalAmountDueElement) {
            totalAmountDueElement.value = calculatedTotalAmountDue.toFixed(2);
        }
    }




    function setupEventListeners() {
        $('#customer_name').on('change', function() {
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
        if (!Array.isArray(creditMemos)) {
            console.error("creditMemos is not an array", creditMemos);
            return;
        }

        const accounts = <?php echo json_encode($accounts); ?>;
        const wtaxes = <?php echo json_encode($wtaxes); ?>;

        const createOptions = (data) => data.map(item => `<option value="${item.id}">${item.description || item.account_description || item.wtax_description}</option>`).join('');

        const accountDropdownOptions = createOptions(accounts);
        const wtaxDropdownOptions = createOptions(wtaxes);

        const modalHtml = `
    <div class="modal fade" id="creditMemoModal" tabindex="-1" role="dialog" aria-labelledby="creditMemoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="creditMemoModalLabel"><i class="fas fa-file-invoice-dollar"></i> Discounts and Credit</h5>
            
                </div>
                <div class="modal-body">
                    <ul class="nav nav-tabs" id="creditMemoTabs" role="tablist">
                        ${['Credit Memos', 'Discount', 'Tax Withheld'].map((title, index) => `
                            <li class="nav-item">
                                <a class="nav-link${index === 0 ? ' active' : ''}" id="${title.toLowerCase().replace(' ', '-')}-tab" data-toggle="tab" href="#${title.toLowerCase().replace(' ', '-')}" role="tab" aria-controls="${title.toLowerCase().replace(' ', '-')}" aria-selected="${index === 0}">
                                    <i class="fas fa-${index === 0 ? 'file-invoice-dollar' : index === 1 ? 'tags' : 'percent'}"></i> ${title}
                                </a>
                            </li>`).join('')}
                    </ul>
                    <div class="tab-content" id="creditMemoTabsContent">
                        <div class="tab-pane fade show active" id="credit-memos" role="tabpanel" aria-labelledby="credit-memos-tab">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selectAll"></th>
                                        <th><i class="fas fa-receipt"></i> Credit Memo #</th>
                                        <th><i class="fas fa-calendar-alt"></i> Date</th>
                                        <th><i class="fas fa-money-bill-wave"></i> Amount (₱)</th>
                                        <th><i class="fas fa-hand-holding-usd"></i> Apply Amount</th>
                                      
                                    </tr>
                                </thead>
                                <tbody id="creditMemoTableBody">
                                    ${creditMemos.map(memo => `
                                        <tr>
                                            <td>
                                                <input type="checkbox" class="credit-memo-checkbox" data-credit-id="${memo.id}" data-credit-no="${memo.credit_no}">
                                                <input type="hidden" name="credit_no" value="">
                                            </td>
                                            <td>${memo.credit_no}</td>
                                            <td>${memo.credit_date}</td>
                                            <td>₱${parseFloat(memo.amount).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                                            <td>
                                                <input type="number" class="form-control apply-amount" min="1" max="${parseFloat(memo.amount).toFixed(2)}" step="0.01" value="1" disabled>
                                                <small class="form-text text-muted"><i class="fas fa-info-circle"></i> Enter an amount between 1 and ₱${parseFloat(memo.amount).toFixed(2)}</small>
                                            </td>
                                          
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                        ${['Discount', 'Tax Withheld'].map((title, index) => `
                            <div class="tab-pane fade" id="${title.toLowerCase().replace(' ', '-')}" role="tabpanel" aria-labelledby="${title.toLowerCase().replace(' ', '-')}-tab">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="${title.toLowerCase().replace(' ', '')}Account"><i class="fas fa-university"></i> Account</label>
                                            <select class="form-control form-control-sm account-dropdown" id="${title.toLowerCase().replace(' ', '')}Account" name="${title.toLowerCase().replace(' ', '')}_account_id[]">${index === 0 ? accountDropdownOptions : wtaxDropdownOptions}</select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="${title.toLowerCase().replace(' ', '')}Amount"><i class="fas fa-money-bill"></i> Amount (₱)</label>
                                            <input type="number" class="form-control form-control-sm" id="${title.toLowerCase().replace(' ', '')}Amount" name="${title.toLowerCase().replace(' ', '')}_amount" min="0" step="0.01" placeholder="Enter ${title.toLowerCase()} amount">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
                <div class="modal-footer">
                    <div>
                        <strong>Total Credit Memos:</strong> <span id="totalCreditMemos">₱0.00</span>
                    </div>
                    <div>
                        <strong>Total Discount Applied:</strong> <span id="totalDiscountApplied">₱0.00</span>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times"></i> Close</button>
                    <button type="button" class="btn btn-primary d-none" id="applySelectedMemos"><i class="fas fa-check-circle"></i> Apply Selected</button>
                    <button type="button" class="btn btn-success d-none" id="applyDiscount"><i class="fas fa-tag"></i> Apply Discount</button>
                </div>
            </div>
        </div>
    </div>
    `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        const modal = new bootstrap.Modal(document.getElementById('creditMemoModal'));
        modal.show();

        // Function to update totals
        const updateTotals = () => {
            const totalCreditMemos = Array.from(document.querySelectorAll('.credit-memo-checkbox:checked')).reduce((total, checkbox) => {
                const row = checkbox.closest('tr');
                const amount = parseFloat(row.querySelector('.apply-amount').value);
                return total + amount;
            }, 0);

            const discountAmount = parseFloat(document.getElementById('discountAmount').value) || 0;

            document.getElementById('totalCreditMemos').textContent = `₱${totalCreditMemos.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            document.getElementById('totalDiscountApplied').textContent = `₱${discountAmount.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        };

        // Initialize Select2 for dropdowns
        $('#discountAccount, #withheldAccount').select2({
            theme: 'classic',
            width: '100%',
            placeholder: 'Select Account',
            allowClear: false,
            dropdownParent: $('#creditMemoModal'),
            language: {
                noResults: () => "No matching accounts found"
            }
        });

        // Initialize tabs
        document.querySelectorAll('#creditMemoTabs a[data-toggle="tab"]').forEach(tab => {
            tab.addEventListener('click', (event) => {
                event.preventDefault();
                new bootstrap.Tab(event.target).show();
            });
        });

        // Update button visibility based on the active tab
        const updateButtonVisibility = () => {
            const activeTab = document.querySelector('#creditMemoTabs .nav-link.active').getAttribute('href');
            document.getElementById('applySelectedMemos').classList.toggle('d-none', activeTab !== '#credit-memos');
            document.getElementById('applyDiscount').classList.toggle('d-none', activeTab === '#credit-memos');
        };

        updateButtonVisibility();
        document.querySelectorAll('#creditMemoTabs a[data-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', updateButtonVisibility);
        });

        // Enable/Disable input fields and buttons based on checkbox selection
        document.querySelectorAll('.credit-memo-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                const row = checkbox.closest('tr');
                const inputField = row.querySelector('.apply-amount');
                const applyButton = row.querySelector('.apply-credit');
                inputField.disabled = !checkbox.checked;
                applyButton.disabled = !checkbox.checked;
                updateTotals(); // Update totals whenever a checkbox changes
            });
        });

        // Select/Deselect all checkboxes
        document.getElementById('selectAll').addEventListener('change', function() {
            const allChecked = this.checked;
            document.querySelectorAll('.credit-memo-checkbox').forEach(checkbox => {
                checkbox.checked = allChecked;
                checkbox.dispatchEvent(new Event('change'));
            });
        });

        // Event listener for Apply Discount button
        document.getElementById('applyDiscount').addEventListener('click', () => {
            const discountAccountId = document.getElementById('discountAccount').value;
            const discountAmount = parseFloat(document.getElementById('discountAmount').value);
            if (!discountAccountId || isNaN(discountAmount) || discountAmount <= 0) {
                alert("Please select a valid account and enter a positive discount amount.");
                return;
            }

            // Apply discount
            applyDiscount(discountAccountId, discountAmount, invoiceId);
            updateTotals(); // Update totals after applying discount
        });

        // Event listener for Apply Selected Memos button
        document.getElementById('applySelectedMemos').addEventListener('click', () => {
            const selectedMemos = Array.from(document.querySelectorAll('.credit-memo-checkbox:checked')).map(checkbox => {
                const row = checkbox.closest('tr');
                return {
                    id: checkbox.dataset.creditId,
                    amount: parseFloat(row.querySelector('.apply-amount').value),
                    creditNo: checkbox.dataset.creditNo
                };
            });

            if (selectedMemos.length === 0) {
                alert("Please select at least one credit memo to apply.");
                return;
            }

            // Apply selected credit memos
            applyCreditMemos(selectedMemos, invoiceId);
            updateTotals(); // Update totals after applying selected memos
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
</script>