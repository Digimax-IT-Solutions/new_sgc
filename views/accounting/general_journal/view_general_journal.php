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


// Get the journal ID from the query string
$journal_id = $_GET['id'] ?? null;

if (!$journal_id) {
    // Handle case where ID is not provided or invalid
    // You can redirect or show an error message
    die("Invalid request.");
}

// Retrieve the journal entry using the ID
$journal = GeneralJournal::find($journal_id);

if (!$journal) {
    // Handle case where journal entry with the given ID is not found
    die("Journal entry not found.");
}

// Retrieve the journal details
$general_journal_details = GeneralJournal::getJournalDetails($journal_id);

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

        .select2-no-border .select2-selection {
            border: none !important;
            padding: 0 !important;
            box-shadow: none !important;
        }

        .select2-no-border .select2-selection__rendered {
            padding: 0 !important;
            /* Adjust if necessary */
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

            <div class="row">
                <div class="col-sm-12 d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-3"><strong>View</strong> Journal</h1>

                    <div class="d-flex justify-content-end">
                        <a href="general_journal" class="btn btn-secondary">
                            <i class="align-middle" data-feather="arrow-left-circle"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_payment_method') ?>
                    <?php displayFlashMessage('delete_payment_method') ?>
                    <?php displayFlashMessage('update_payment_method') ?>
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <form id="generalJournalForm" action="api/general_journal_controller.php?action=update"
                                method="POST">
                                <input type="hidden" name="action" value="update" />
                                <input type="hidden" name="id" id="itemId" value="" />
                                <input type="hidden" name="item_data" id="item_data" />
                                <input type="hidden" name="id" id="itemId" value="<?= $journal->id ?>">
                                <div class="row">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="journal_date">Entry Date</label>
                                            <input type="date" class="form-control form-control-sm" id="journal_date"
                                                name="journal_date"
                                                value="<?php echo date('Y-m-d', strtotime('+8 hours')); ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="entry_no">Entry No</label>
                                            <input type="text" class="form-control form-control-sm" id="entry_no"
                                                name="entry_no" placeholder="Enter Ref"
                                                <?php if ($journal->status == 4): ?>
                                                    value="<?php echo htmlspecialchars($newEntryNo); ?>" readonly>
                                                <?php else: ?>
                                                    value="<?php echo htmlspecialchars($journal->entry_no); ?>" disabled>
                                                <?php endif; ?>

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
                                        <thead class="" style="font-size: 14px;">
                                            <tr>
                                                <th>Account</th>
                                                <th>Debit</th>
                                                <th>Credit</th>
                                                <th>Name</th>
                                                <th>Memo</th>
                                                <th>Cost Center</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemTableBody">
                                            <!-- Existing rows or dynamically added rows will be appended here -->
                                            <?php if ($general_journal_details): ?>
                                                <?php foreach ($general_journal_details as $detail): ?>
                                                    <tr>
                                                        <td>
                                                            <select class="form-control form-control-sm account-dropdown" name="account_name[]">
                                                                <?php foreach ($accounts as $account): ?>
                                                                    <?php
                                                                        // Check if the current account should be selected
                                                                        $selected = $detail['account_code'] == $account->account_code ? 'selected' : '';
                                                                        // Set the option value to detail's account_id
                                                                        $option_value = htmlspecialchars($account->id);  // Use the account's id for value
                                                                        // Create the option text
                                                                        $option_text = htmlspecialchars($account->account_code . ' - ' . $account->account_description);
                                                                    ?>
                                                                    <option value="<?= $option_value ?>" <?= $selected ?>><?= $option_text ?></option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control form-control-sm debit" name="debit[]" value="<?= htmlspecialchars($detail['debit']) ?>">
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control form-control-sm credit" name="credit[]" value="<?= htmlspecialchars($detail['credit']) ?>">
                                                        </td>
                                                        <td>
                                                            <select class="form-control form-control-sm name-dropdown select2" name="name[]">
                                                                <?php foreach ($combinedNames as $combinedName): ?>
                                                                    <?php
                                                                        $selected = ($detail['name'] == $combinedName['name']) ? 'selected' : '';
                                                                    ?>
                                                                    <option value="<?= htmlspecialchars($combinedName['name']) ?>" data-type="<?= htmlspecialchars($combinedName['type']) ?>" <?= $selected ?>>
                                                                        <?= htmlspecialchars($combinedName['name']) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" class="form-control form-control-sm memo" name="memo[]" value="<?= htmlspecialchars($detail['memo']) ?>">
                                                        </td>
                                                        <td>
                                                            <select class="form-control form-control-sm cost-dropdown select2" name="cost_center_id[]">
                                                                <?php foreach ($cost_centers as $cost_center): ?>
                                                                    <option value="<?= htmlspecialchars($cost_center->id) ?>" <?= ($cost_center->id == $detail['cost_center_id']) ? 'selected' : '' ?>>
                                                                        <?= htmlspecialchars($cost_center->code . ' - ' . $cost_center->particular) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-danger removeRow"><i class="fas fa-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <th>TOTAL</th>
                                                <th id="totalDebit">0.00</th>
                                                <th id="totalCredit">0.00</th>

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
                                                name="memo" placeholder="Enter memo" value="<?= $journal->memo ?>"
                                                required>
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
                                    <?php if ($journal->status == 4): ?>
                                        <!-- Buttons to show when invoice_status is 4 -->
                                        <button type="submit" class="btn btn-info me-2">Save and Print</button>
                                    <?php elseif ($journal->status == 3): ?>
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
                                        </a>
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
    <iframe id="printFrame" style="display:none;"></iframe>
    <div id="loadingOverlay" style="display: none;">
        <div class="spinner"></div>
    </div>

    <?php require 'views/templates/footer.php' ?>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('reprintButton').addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Reprint Invoice?',
                text: "Are you sure you want to reprint this invoice?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, reprint it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    printGeneralJournal(<?= $journal->id ?>, 2);  // Pass 2 for reprint
                }
            });
        });

        // Attach event listener for the void button
        document.getElementById('voidButton').addEventListener('click', function (e) {
            e.preventDefault();
            Swal.fire({
                title: 'Void General Journal?',
                text: "Are you sure you want to void this general journal? This action cannot be undone.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, void it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    voidCheck(<?= $journal->id ?>);
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

    function printGeneralJournal(id, printStatus) {
        showLoadingOverlay();

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
                    const printFrame = document.getElementById('printFrame');
                    const printContentUrl = `print_general_journal?action=print&id=${id}`;

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
            url: 'api/general_journal_controller.php',
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
                        text: 'General Journal has been voided successfully.'
                    }).then(() => {
                        location.reload(); // Reload the page to reflect changes
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to void general: ' + (response.message || 'Unknown error')
                    });
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                hideLoadingOverlay(); // Hide the loading overlay on error
                console.error('AJAX error:', textStatus, errorThrown);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while voiding the general: ' + textStatus
                });
            }
        });
    }

</script>


<script>
$(document).ready(function () {
    // Initialize Select2 for existing dropdowns
    $('.account-dropdown, .cost-dropdown, .name-dropdown').select2({
            theme: 'bootstrap-5',
            width: '100%'
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
            $('#save').prop('disabled', true); // Disable save button
        } else {
            $('#balanceAlert').hide(); // Hide the balance alert
            $('#save').prop('disabled', false); // Enable save button
        }

        // Check for inconsistent entries
        checkInconsistentEntries();

        let isConsistent = isDataConsistent();
            $('#saveDraftBtn, button[type="submit"]').prop('disabled', !isConsistent);

            if (!isConsistent) {
                $('#saveDraftBtn, button[type="submit"]').addClass('btn-secondary').removeClass('btn-info');
            } else {
                $('#saveDraftBtn').removeClass('btn-secondary').addClass('btn-secondary');
                $('button[type="submit"]').removeClass('btn-secondary').addClass('btn-info');
            }
    }

    // Function to check inconsistent entries
    function checkInconsistentEntries() {
        let inconsistentAccounts = []; // Array to hold inconsistent accounts

        $('#itemTableBody').find('tr').each(function () {
            let debit = parseFloat($(this).find('.debit').val()) || 0;
            let credit = parseFloat($(this).find('.credit').val()) || 0;

            // Check for inconsistencies
            if (debit !== 0 && credit !== 0) {
                let accountName = $(this).find('.account-dropdown option:selected').text().trim();
                if (!inconsistentAccounts.includes(accountName)) {
                    inconsistentAccounts.push(accountName);
                }
            }
        });

        // Check for inconsistent entries
        if (inconsistentAccounts.length > 0) {
            let inconsistentText = 'Inconsistent entries found. The following accounts have both debit and credit entries: ';
            inconsistentText += inconsistentAccounts.join(', ');
            $('#inconsistentAlert').text(inconsistentText).show(); // Show the inconsistent entries alert
            $('#save').prop('disabled', true); // Disable save button
        } else {
            $('#inconsistentAlert').hide(); // Hide the inconsistent entries alert
        }
    }

    // Function to generate options for combined names
    function generateNameOptions() {
        const combinedNames = <?php echo json_encode($combinedNames); ?>;
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
        costCenterDropdownOptions += `<option value="${cost.id}">${cost.code} - ${cost.particular}</option>`;
    });

    // Add a new row to the table
    function addRow() {
        const newRow = `
        <tr>
            <td><select class="form-control form-control-sm account-dropdown" name="account_id[]" required>${accountDropdownOptions}</select></td>
            <td class="debit-cell"><input type="text" class="form-control form-control-sm debit" name="debit[]" placeholder="0.00"></td>
            <td class="credit-cell"><input type="text" class="form-control form-control-sm credit" name="credit[]" placeholder="0.00"></td>
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
        // Destroy Select2 before removing the row to prevent memory leaks
        $(this).closest('tr').find('.account-dropdown, .name-dropdown, .cost-dropdown').select2('destroy');
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

    $('#generalJournalForm').submit(function (event) {
        event.preventDefault();

        // Check if the table has any rows
        if ($('#itemTableBody tr').length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Items',
                text: 'You must add at least one item before submitting the journal.'
            });
            return false;
        }

        const items = gatherTableItems();
        $('#item_data').val(JSON.stringify(items));
        const journalStatus = <?= json_encode($journal->status) ?>;
        const id = <?= json_encode($journal->id) ?>;

        // Show loading overlay
        document.getElementById('loadingOverlay').style.display = 'flex';

        const formData = new FormData(this);
        formData.append('action', 'update');
        formData.append('id', id);
        formData.append('item_data', JSON.stringify(items));
        formData.append('status', 0); // Explicitly set status to 0

        $.ajax({
            url: 'api/general_journal_controller.php',
            type: 'POST',
            dataType: 'json',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                document.getElementById('loadingOverlay').style.display = 'none';
                console.log('Response:', response);  // Log the response
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'General journal updated successfully!',
                        showCancelButton: true,
                        confirmButtonText: 'Print',
                        cancelButtonText: 'Close'
                    }).then((result) => {
                        if (result.isConfirmed && response.id) {
                            printGeneralJournal(response.id, 1); // Pass 1 for initial print
                        } else {
                            location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error updating general journal: ' + (response.message || 'Unknown error')
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
                    text: 'An error occurred while updating the general journal: ' + textStatus
                });
            }
        });
    });
});

</script>
</div>