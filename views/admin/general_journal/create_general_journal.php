<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();
$accounts = ChartOfAccount::all();
$cost_centers = CostCenter::all();
$customers = Customer::all();
$vendors = Vendor::all();
$other_names = OtherNameList::all();
$general_journal = GeneralJournal::all();

$newEntryNo = GeneralJournal::getLastEntryNo();

// Combine all names into a single array
$combinedNames = [];

foreach ($vendors as $vendor) {
    $combinedNames[] = [
        'id' => $vendor->id,
        'name' => $vendor->vendor_name,
        'type' => 'Vendor'
    ];
}

foreach ($customers as $customer) {
    $combinedNames[] = [
        'id' => $customer->id,
        'name' => $customer->customer_name,
        'type' => 'Customer'
    ];
}

foreach ($other_names as $other_name) {
    $combinedNames[] = [
        'id' => $other_name->id,
        'name' => $other_name->other_name,
        'type' => 'Other Name'
    ];
}

?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <style>
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

        #itemTable {
            font-size: 14px;
            border-collapse: collapse;
            width: 100%;
        }

        #itemTable thead {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        #itemTable th,
        #itemTable td {
            border: 1px solid #dee2e6;
            padding: 8px;
            vertical-align: middle;
        }

        #itemTable tbody tr:nth-of-type(even) {
            background-color: #f8f9fa;
        }

        #itemTable tbody tr:hover {
            background-color: #e9ecef;
        }

        #itemTable tfoot {
            font-weight: bold;
            background-color: #e9ecef;
        }

        .debit-cell,
        .credit-cell {
            text-align: right;
        }

        .memo-cell {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .select2-container--bootstrap-5 .select2-selection {
            border: 1px solid #ced4da;
        }

        /* Updated style for sticky alert */
        #balanceAlert,
        #inconsistentAlert {
            position: fixed;
            top: 70px;
            /* Adjust top position as needed */
            right: 15px;
            z-index: 1000;
            /* Ensure it's above other content */
            display: none;
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


            <h1 class="h3 mb-3"><strong>General</strong> Journal</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="general_journal">General Journal</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Create General Journal</li>
                </ol>
            </nav>

            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_payment_method') ?>
                    <?php displayFlashMessage('delete_payment_method') ?>
                    <?php displayFlashMessage('update_payment_method') ?>
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <form id="writeCheckForm" action="api/general_journal_controller.php?action=add"
                                method="POST">
                                <input type="hidden" name="action" value="add" />
                                <input type="hidden" name="id" id="itemId" value="" />
                                <input type="hidden" name="item_data" id="item_data" />
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="journal_date">Entry Date</label>
                                            <input type="date" class="form-control form-control-sm" id="journal_date"
                                                name="journal_date"
                                                value="<?php echo date('Y-m-d'); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="entry_no">Entry No</label>
                                            <input type="text" class="form-control form-control-sm" id="entry_no"
                                                name="entry_no" value="<?php echo $newEntryNo; ?>" readonly>
                                        </div>
                                    </div>

                                    <div class="col-md-2">
                                        <div class="form-group">

                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">

                                        </div>
                                    </div>
                                    <div class="col-md-2">

                                    </div>
                                </div>
                                <br><br><br>
                                <div class="row">
                                    <div class="col-md-2">
                                    </div>
                                    <div class="col-md-2">
                                    </div>
                                    <div class="col-md-2">
                                    </div>
                                    <div class="col-md-2">
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                        </div>
                                    </div>
                                </div>
                                <div id="balanceAlert" class="alert alert-danger" role="alert" style="display: none;">
                                    The Transaction is not in balance. Please make sure the total amount in the debit
                                    column equals the total amount in the credit column!
                                </div>

                                <div id="inconsistentAlert" class="alert alert-warning" role="alert"
                                    style="display: none;">
                                    Inconsistent entries found. The following accounts have both debit and credit
                                    entries: Petty Cash Fund
                                </div>

                                <div class="table-responsive-sm">
                                    <table class="table table-sm table-bordered" id="itemTable">
                                        <thead>
                                            <tr>
                                                <th style="width: 20%;">Account</th>
                                                <th style="width: 15%;">Debit</th>
                                                <th style="width: 15%;">Credit</th>
                                                <th style="width: 15%;">Name</th>
                                                <th style="width: 15%;">Memo</th>
                                                <th style="width: 15%;">Cost Center</th>
                                                <th style="width: 5%;"></th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemTableBody" class="">
                                            <!-- Existing rows or dynamically added rows will be appended here -->

                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <th>TOTAL</th>
                                                <th id="totalDebit">0.00</th>
                                                <th id="totalCredit">0.00</th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    <!-- Hidden inputs for total debit and total credit -->
                                    <input type="hidden" id="total_debit" name="total_debit">
                                    <input type="hidden" id="total_credit" name="total_credit">
                                    <button type="button" class="btn btn-secondary" id="addItemBtn">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <br><br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="memo">Memo</label>
                                            <input type="text" class="form-control form-control-sm" id="memo"
                                                name="memo" placeholder="Enter memo">
                                        </div>
                                    </div>
                                </div>

                                <br>
                                <div class="row">
                                    <div class="col-md-2">
                                    </div>
                                </div>
                                <br><br>

                                <!-- Submit Button -->
                                <div class="row">
                                    <div class="col-md-10 d-inline-block">
                                    <button type="button" id="saveDraftBtn" class="btn btn-secondary me-2">Save as Draft</button>
                                    <button type="submit" class="btn btn-info me-2">Save and Print</button>
                                    <button type="reset" class="btn btn-danger">Clear</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </main>
    
    <div id="loadingOverlay" style="display: none;">
        <div class="spinner"></div>
        <div class="message">Processing General Journal</div>
    </div>
</div>
<?php require 'views/templates/footer.php' ?>

<iframe id="printFrame" style="display:none;"></iframe>

<script>

    $(document).ready(function () {
        
        $('button[type="reset"]').on('click', function(e) {
            e.preventDefault(); // Prevent default reset behavior

            // Clear all input fields
            $('input').val('');

            // Reset all select elements to their default option
            $('select').each(function() {
                $(this).val($(this).find("option:first").val()).trigger('change');
            });

            // Clear the item table
            $('#itemTableBody').empty();

            // Reset all Select2 dropdowns
            $('.select2').val(null).trigger('change');

            // Reset summary section
            $('#totalDebit, #totalCredit').text('0.00');
            $('#total_debit, #total_credit').val('0.00');

            // Clear hidden inputs
            $('#item_data').val('');
            $('#memo').val('');

            // Reset date input to current date
            $('#journal_date').val(new Date().toISOString().split('T')[0]);

            // Optionally, you can add a confirmation message
            Swal.fire({
                icon: 'success',
                title: 'Cleared',
                text: 'All fields have been reset.',
                timer: 1800,
                showConfirmButton: false
            });
        });
       
        $('#saveDraftBtn').click(function(e) {
            e.preventDefault();
            saveDraft();
        });

        function saveDraft() {
            const items = gatherTableItems();

            if (items.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please add items before saving as draft'
                });
                return;
            }

            $('#item_data').val(JSON.stringify(items));

            const formData = new FormData($('#writeCheckForm')[0]);
            formData.append('action', 'save_draft');

            $('#loadingOverlay').fadeIn();

            $.ajax({
                url: 'api/general_journal_controller.php',
                type: 'POST',
                dataType: 'json',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    $('#loadingOverlay').fadeOut();
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'General journal saved as draft successfully!',
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
                            text: response.message || 'Unknown error occurred while saving draft'
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#loadingOverlay').fadeOut();
                    console.error('AJAX error:', textStatus, errorThrown);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving the draft: ' + textStatus
                    });
                }
            });
        }

        // Initialize Select2 for existing dropdowns
        $('.account-dropdown, .name-dropdown, .cost-dropdown').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        $(document).on('click', '.removeRow', function () {
            // Destroy Select2 before removing the row to prevent memory leaks
            $(this).closest('tr').find('.account-dropdown, .name-dropdown, .cost-dropdown').select2('destroy');
            $(this).closest('tr').remove();
            calculateTotals(); // Update totals when a row is removed
        });

        function isDataConsistent() {
            let totalDebit = parseFloat($('#total_debit').val()) || 0;
            let totalCredit = parseFloat($('#total_credit').val()) || 0;
            let inconsistentAccounts = [];

            $('#itemTableBody').find('tr').each(function () {
                let debit = parseFloat($(this).find('.debit').val()) || 0;
                let credit = parseFloat($(this).find('.credit').val()) || 0;
                if (debit !== 0 && credit !== 0) {
                    let accountName = $(this).find('.account-dropdown option:selected').text().trim();
                    if (!inconsistentAccounts.includes(accountName)) {
                        inconsistentAccounts.push(accountName);
                    }
                }
            });

            return totalDebit === totalCredit && inconsistentAccounts.length === 0;
        }
        // Function to calculate total debit and total credit
        function calculateTotals() {
            let totalDebit = 0;
            let totalCredit = 0;
            let inconsistentAccounts = []; // Array to hold inconsistent accounts

            $('#itemTableBody').find('tr').each(function () {
                let debit = parseFloat($(this).find('.debit').val()) || 0;
                let credit = parseFloat($(this).find('.credit').val()) || 0;

                totalDebit += debit;
                totalCredit += credit;

                // Check for inconsistencies
                if (debit !== 0 && credit !== 0) {
                    let accountName = $(this).find('.account-dropdown option:selected').text().trim();
                    if (!inconsistentAccounts.includes(accountName)) {
                        inconsistentAccounts.push(accountName);
                    }
                }
            });

            $('#totalDebit').text(totalDebit.toFixed(2));
            $('#totalCredit').text(totalCredit.toFixed(2));

            // Set totalDebit and totalCredit in the hidden inputs
            $('#total_debit').val(totalDebit.toFixed(2));
            $('#total_credit').val(totalCredit.toFixed(2));

            // Check if debit and credit are balanced
            if (totalDebit !== totalCredit) {
                $('#balanceAlert').show(); // Show the balance alert
            } else {
                $('#balanceAlert').hide(); // Hide the balance alert
            }

            // Check for inconsistent entries
            if (inconsistentAccounts.length > 0) {
                let inconsistentText = 'Inconsistent entries found. The following accounts have both debit and credit entries: ';
                inconsistentText += inconsistentAccounts.join(', ');
                $('#inconsistentAlert').text(inconsistentText).show(); // Show the inconsistent entries alert
                $('#balanceAlert').hide(); // Hide the balance alert
            } else {
                $('#inconsistentAlert').hide(); // Hide the inconsistent entries alert
            }

            let isConsistent = isDataConsistent();
            $('#saveDraftBtn, button[type="submit"]').prop('disabled', !isConsistent);

            if (!isConsistent) {
                $('#saveDraftBtn, button[type="submit"]').addClass('btn-secondary').removeClass('btn-info');
            } else {
                $('#saveDraftBtn').removeClass('btn-secondary').addClass('btn-secondary');
                $('button[type="submit"]').removeClass('btn-secondary').addClass('btn-info');
            }
        }

        // Populate dropdowns with combined names from PHP
        const combinedNames = <?php echo json_encode($combinedNames); ?>;

        // Function to generate options for combined names
        function generateNameOptions() {
            let options = '';
            combinedNames.forEach(name => {
                options += `<option value="${name.name}" data-type="${name.type}">${name.name}</option>`;
            });
            return options;
        }

        // Populate dropdowns with accounts from PHP
        const accounts = <?php echo json_encode($accounts); ?>;
        let accountDropdownOptions = '';
        $.each(accounts, function (index, account) {
            accountDropdownOptions += `<option value="${account.id}">${account.account_description}</option>`;
        });

        // Populate dropdowns with cost centers from PHP
        const costCenterOptions = <?php echo json_encode($cost_centers); ?>;
        let costCenterDropdownOptions = '';
        costCenterOptions.forEach(function (cost) {
            costCenterDropdownOptions += `<option value="${cost.id}">${cost.particular}</option>`;
        });

        // Add a new row to the table
        function addRow() {
            const newRow = `
            <tr>
                <td><select class="form-control form-control-sm account-dropdown" name="account_id[]" required>${accountDropdownOptions}</select></td>
                <td class="debit-cell"><input type="number" class="form-control form-control-sm debit" name="debit[]" placeholder="0.00"></td>
                <td class="credit-cell"><input type="number" class="form-control form-control-sm credit" name="credit[]" placeholder="0.00"></td>
                <td><select class="form-control form-control-sm name-dropdown" name="name[]" required>${generateNameOptions()}</select></td>
                <td class="memo-cell"><input type="text" class="form-control form-control-sm memo" name="memo[]" placeholder="Enter memo"></td>
                <td><select class="form-control form-control-sm cost-dropdown" name="cost_center_id[]">${costCenterDropdownOptions}</select></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger removeRow"><i class="fas fa-trash"></i></button></td>
            </tr>`;
            $('#itemTableBody').append(newRow);

            // Initialize Select2 for the new dropdowns
            $('#itemTableBody tr:last-child .account-dropdown, #itemTableBody tr:last-child .name-dropdown, #itemTableBody tr:last-child .cost-dropdown').select2({
                theme: 'bootstrap-5',
                width: '100%'
            });

            calculateTotals();
        }

        // Event listener for adding rows
        $('#addItemBtn').click(addRow);

        // Event listener for removing rows
        $(document).on('click', '.removeRow', function () {
            $(this).closest('tr').remove();
            calculateTotals(); // Update totals when a row is removed
        });

        // Event listener for input in debit and credit fields
        $(document).on('input', '.debit, .credit', function () {
            calculateTotals(); // Update totals when input changes in debit or credit fields
        });

        // Calculate totals initially
        calculateTotals();

        // Gather table items for form submission
        function gatherTableItems() {
            const items = [];
            $('#itemTableBody tr').each(function (index) {
                const item = {
                    account_id: $(this).find('.account-dropdown').val(),
                    name: $(this).find('.name-dropdown').val(),
                    debit: $(this).find('.debit').val(),
                    credit: $(this).find('.credit').val(),
                    memo: $(this).find('.memo').val(),
                    cost_center_id: $(this).find('.cost-dropdown').val() // Added cost_center_id
                };

                items.push(item);
            });
            return items;
        }

        $('#writeCheckForm').submit(function (event) {
            event.preventDefault();

            const items = gatherTableItems();

            if (items.length === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please add items first'
                });
                return;
            }

            $('#item_data').val(JSON.stringify(items));

            // Show loading overlay
            $('#loadingOverlay').css('display', 'flex');

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                dataType: 'json',
                data: $(this).serialize(),
                success: function (response) {
                    // Hide loading overlay
                    $('#loadingOverlay').hide();

                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Check submitted successfully!',
                            showCancelButton: true,
                            confirmButtonText: 'Print',
                            cancelButtonText: 'No, thanks'
                        }).then((result) => {
                            if (result.isConfirmed && response.id) {
                                printGeneralJournal(response.id, 1); // Use the correct function name here
                            } else {
                                location.reload();
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Error saving check: ' + (response.message || 'Unknown error')
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    // Hide loading overlay
                    $('#loadingOverlay').hide();

                    console.error('AJAX error:', textStatus, errorThrown);
                    console.log('Response Text:', jqXHR.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving the check: ' + textStatus
                    });
                }
            });
        });

        function printGeneralJournal(id, printStatus) {
            // First, update the print status
            $.ajax({
                url: 'api/general_journal_controller.php',
                type: 'POST',
                dataType: 'json',
                data: {
                    action: 'update_print_status',
                    id: id,
                    print_status: printStatus
                },
                success: function (response) {
                    if (response.success) {
                        // If the status was updated successfully, proceed with printing
                        console.log('Print status updated, now printing general journal:', id);
                        // Open a new window with the print view
                        const printFrame = document.getElementById('printFrame');
                        const printContentUrl = `print_general_journal?action=print&id=${id}`;

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
    });
</script>

</div>