<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();
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
                                                <select class="form-select" id="payment_method" name="payment_method" required>
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
                                                <input type="text" class="form-control" id="cr_no" name="cr_no" value="<?php echo $newCrNo; ?>" readonly>
                                            </div>
                                            <div class="col-md-3 invoice-details">
                                                <label for="reference_no" class="form-label">Reference / Check #</label>
                                                <input type="text" class="form-control" id="reference_no" name="reference_no" required>
                                            </div>
                                            <div class="col-md-3 invoice-details">
                                                <label for="payment_date" class="form-label">Payment Date</label>
                                                <input type="date" class="form-control" id="payment_date" name="payment_date" value="<?php echo date('Y-m-d'); ?>" required>
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
                                            id="summary_amount_due" name="summary_amount_due" value="0.00" readonly>
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
                const invoice = {
                    invoice_id: checkbox.dataset.invoiceId,
                    invoice_account_id: row.querySelector('td:nth-child(2)').textContent,
                    amount_applied: row.querySelector('.payment-input').value || '0',
                    discount_amount: row.querySelector('.discount-input')?.value || '0',
                    credit_amount: row.querySelector('.credit-input')?.value || '0'
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

        document.querySelectorAll('#itemTable tbody tr').forEach(row => {
            const checkbox = row.querySelector('.invoice-select');
            if (checkbox && checkbox.checked) {
                const amountDueElement = row.querySelector('.amount-due');
                const paymentInput = row.querySelector('.payment-input');

                if (amountDueElement) {
                    const amountDue = parseFloat(amountDueElement.textContent) || 0;
                    totalAmountDue += amountDue;
                }

                if (paymentInput) {
                    const paymentAmount = parseFloat(paymentInput.value) || 0;
                    totalAppliedAmount += paymentAmount;
                }

                // You'll need to implement logic for discount and credits
            }
        });

        const summaryAmountDue = document.getElementById('summary_amount_due');
        const summaryAppliedAmount = document.getElementById('summary_applied_amount');
        const summaryDiscountCredits = document.getElementById('summary_discount_credits');
        const totalAmountDueElement = document.getElementById('total_amount_due');

        if (summaryAmountDue) summaryAmountDue.value = totalAmountDue.toFixed(2);
        if (summaryAppliedAmount) summaryAppliedAmount.value = totalAppliedAmount.toFixed(2);
        if (summaryDiscountCredits) summaryDiscountCredits.value = totalDiscountAndCredits.toFixed(2);
        if (totalAmountDueElement) totalAmountDueElement.value = (totalAmountDue - totalAppliedAmount - totalDiscountAndCredits).toFixed(2);
    }

    function setupEventListeners() {
        $('#customer_name').on('change', function () {
            fetchAndDisplayInvoices(this.value);
        });
        $('#payment_amount').on('input', updatePaymentSummary);

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
                    const invoice = {
                        invoice_id: checkbox.dataset.invoiceId,
                        invoice_account_id: row.querySelector('td:nth-child(2)').textContent,
                        amount_applied: row.querySelector('.payment-input').value || '0',
                        discount_amount: row.querySelector('.discount-input')?.value || '0',
                        credit_amount: row.querySelector('.credit-input')?.value || '0'
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