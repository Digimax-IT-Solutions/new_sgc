<?php
include __DIR__ . ('../../includes/header.php');
include('connect.php');

$journal = null;
$journalDetails = [];

// Validate and sanitize the 'ID' parameter from the URL
if (isset($_GET['ID']) && is_numeric($_GET['ID'])) {
    $ID = $_GET['ID'];

    try {
        // Query to retrieve sales invoice details
        $queryInvoice = "SELECT * FROM general_journal WHERE ID = :ID";
        $stmtInvoice = $db->prepare($queryInvoice);
        $stmtInvoice->bindParam(':ID', $ID, PDO::PARAM_INT); // Bind parameter as integer
        $stmtInvoice->execute();

        // Fetch the invoice details if needed
        $journal = $stmtInvoice->fetch(PDO::FETCH_ASSOC);

        if ($journal) {
            // Fetch purchase order items
            $queryJournalDetails = "SELECT account, debit, credit, name AS names, memo FROM journal_entries WHERE general_journal_id = :ID";
            $stmtJournalDetails = $db->prepare($queryJournalDetails);
            $stmtJournalDetails->bindParam(':ID', $ID);
            $stmtJournalDetails->execute();

            $journalDetails = $stmtJournalDetails->fetchAll(PDO::FETCH_ASSOC);
        } else {

            echo "Error fetching invoice: " . $e->getMessage(); // Redirect to the main page or display an error message
            exit();
        }
    } catch (PDOException $e) {
        // Handle the exception, log the error, or return an error message
        echo "Error fetching invoice: " . $e->getMessage();
        exit(); // Exit after encountering an error
    }
} else {
    // Output JavaScript alert for an invalid invoice ID
    echo "<script>alert('Invalid Invoice ID');</script>";
    exit();
}
$query = "SELECT account_id, account_type, account_name FROM chart_of_accounts";
$result = $db->query($query);

$chartOfAccount = array();

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $chartOfAccount[] = $row;
}

$query = "SELECT 'Vendor' as source, vendorName as account_name FROM vendors
          UNION 
          SELECT 'Customer' as source, customerName as account_name FROM customers
          UNION 
          SELECT 'Other Names' as source, otherName as account_name FROM other_names";
$result = $db->query($query);

$otherNames = array();

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $otherNames[] = $row;
}

$chartOfAccountJSON = json_encode($chartOfAccount);
$otherNamesJSON = json_encode($otherNames);
?>
<style>
    /* Add styles for active status */
    .active {
        color: green;
        /* Change the text color for active status */
    }

    /* Add styles for inactive status */
    .inactive {
        color: red;
        /* Change the text color for inactive status */
    }

    /* Add a hover effect to the dropdown items */
    .dropdown-item:hover {
        background-color: rgb(0, 149, 77) !important;
        /* Change the background color on hover */
        color: white;
        /* Change the text color on hover */
    }

    #generalJournalTable {
        border-collapse: collapse;
        width: 100%;
        table-layout: fixed;
    }

    #generalJournalTable th,
    #generalJournalTable td {
        text-align: center;
        padding: 1px;
        /* Adjust the padding as needed */
    }

    /* Set the width of the first th and td */
    #generalJournalTable th:first-child,
    #generalJournalTable td:first-child {
        width: 300px;
    }

    /* Set the width of the fourth th and td */
    #generalJournalTable th:nth-child(4),
    #generalJournalTable td:nth-child(4) {
        width: 200px;
    }
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Create General Journal</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Accounting</a></li>
                        <li class="breadcrumb-item active">Create General Journal</li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form id="enterBillsForm" action="" name="enterBillsForm" method="POST">
                                <div class="form-row">
                                    <div class="form-row">
                                        <div class="form-group col-md-3">
                                            <label for="entry_date">ENTRY DATE</label>
                                            <div class="input-group">
                                                <input type="hidden" name="ID" value="<?php echo $ID; ?>">
                                                <input type="date" class="form-control" id="entry_date" name="entry_date" value="<?= $journal['journal_date']; ?>" required>
                                            </div>
                                        </div>

                                        <div class="form-group col-md-2 offset-md-2">
                                            <!-- Empty div with offset -->
                                        </div>

                                        <div class="form-group col-md-2">
                                            <label for="entry_no">ENTRY NO.</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="entry_no" name="entry_no" value="<?= $journal['entry_no']; ?>" required>
                                            </div>
                                        </div>

                                        <table class="table table-bordered" id="generalJournalTable">
                                            <thead>
                                                <tr>
                                                    <th>Account</th>
                                                    <th>Debit</th>
                                                    <th>Credit</th>
                                                    <th>Name</th>
                                                    <th>Memo</th>
                                                    <th>ACTION</th>
                                                </tr>
                                            </thead>
                                            <tbody id="generalJournalTableBody">

                                                <!-- Each row represents a separate item -->
                                                <!-- You can dynamically add rows using JavaScript/jQuery -->
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
                                        <!-- <div class="col-md-10 d-inline-block">
                                            <button type="button" class="btn btn-success" id="addAccountButton">
                                                <i class="fas fa-plus"></i> Add Account
                                            </button>
                                        </div> -->
                                    </div>
                                </div>
                                <br><br>
                                <!-- Submit Button -->
                                <div class="row">
                                    <div class="col-md-10 d-inline-block">
                                        <button type="button" class="btn btn-success" id="saveButton">
                                            <i class="fas fa-save"></i> Save General Journal
                                        </button>
                                        <button type="button" class="btn btn-danger" id="deleteButton">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <!-- <input type="text" class="form-control" name="grossAmount" id="grossAmount" readonly> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include __DIR__ . ('../../includes/footer.php'); ?>
</div>


<script>
    $("#deleteButton").on("click", function() {
        // Show a confirmation dialog before proceeding with deletion
        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: 'You are about to delete this entry. This action cannot be undone.',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it',
            cancelButtonText: 'No, cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                // User confirmed, proceed with deletion
                if (validateImbalance()) {
                    // Prepare form data
                    var formData = $("#enterBillsForm").serialize();

                    // Perform the AJAX request to delete data
                    $.ajax({
                        type: "POST",
                        url: "modules/accounting/delete_general_journal.php",
                        data: formData,
                        dataType: "json",
                        success: function(response) {
                            // Display success/error message
                            Swal.fire({
                                icon: response.status === 'success' ? 'success' : 'error',
                                title: response.message,
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                // Redirect to the general_journal page after clicking OK if the response is successful
                                if (response.status === 'success') {
                                    window.location.href = 'general_journal';
                                }
                            });
                        },
                        error: function(xhr, status, error) {
                            // Display error message
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while deleting data.',
                                confirmButtonText: 'OK'
                            });

                            console.log(xhr.responseText); // Log the error response to the console
                        }
                    });
                }
            }
        });
    });

    // Save button event listener
    $("#saveButton").on("click", function() {
        if (validateImbalance()) {
            // Prepare form data
            var formData = $("#enterBillsForm").serialize();

            // Perform the AJAX request to save data
            $.ajax({
                type: "POST",
                url: "modules/accounting/update_general_journal.php",
                data: formData,
                dataType: "json",
                success: function(response) {
                    // Display success/error message
                    Swal.fire({
                        icon: response.status === 'success' ? 'success' : 'error',
                        title: response.message,
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        // Redirect to the sales_invoice page after clicking OK if the response is successful
                        if (response.status === 'success') {
                            window.location.href = 'general_journal';
                        }
                    });
                },
                error: function(xhr, status, error) {
                    // Display error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while saving data.',
                        confirmButtonText: 'OK'
                    });

                    console.log(xhr.responseText); // Log the error response to the console
                }
            });
        }
    });
    // Add one row by default
    addJournalEntry();

    // Add expense row when the "Add Expense" button is clicked
    $("#addAccountButton").on("click", function() {
        addJournalEntry();
    });

    // Event listener for removing an item
    $("#generalJournalTableBody").on("click", ".removeItemBtn", function() {
        $(this).closest("tr").remove();
        updateTotals();
    });

    // Event listener for input changes
    $("#generalJournalTableBody").on("input", "input[name^='debit'], input[name^='credit']", function() {
        updateTotals();
        validateDebitCreditConsistency();
    });


    function addJournalEntry() {
        // Parse JSON data for chartOfAccount
        var chartOfAccount = <?php echo $chartOfAccountJSON; ?>;
        var options = "";
        chartOfAccount.forEach(function(account) {
            options += '<option value="' + account["account_name"] + '">' + account["account_name"] + '</option>';
        });

        // Parse JSON data for otherNames
        var otherNames = <?php echo $otherNamesJSON; ?>;
        var optionss = "";
        otherNames.forEach(function(account) {
            optionss += '<option value="' + account["account_name"] + '">' + account["account_name"] + ' | ' + account["source"] + '</option>';
        });

        <?php foreach ($journalDetails as $details) : ?>
            var newRow = '<tr>' +
                '<td><select name="account[]" class="form-control">' +
                '<option value="<?php echo htmlspecialchars($details['account'], ENT_QUOTES); ?>" selected><?php echo htmlspecialchars($details['account'], ENT_QUOTES); ?></option>' +
                '</select></td>' +
                '<td><input type="number" name="debit[]" class="form-control" value="<?php echo $details['debit']; ?>"></td>' +
                '<td><input type="number" name="credit[]" class="form-control" value="<?php echo $details['credit']; ?>"></td>' +
                '<td><select name="name[]" class="form-control">' +
                '<option value="<?php echo htmlspecialchars($details['names'], ENT_QUOTES); ?>" selected><?php echo htmlspecialchars($details['names'], ENT_QUOTES); ?></option>' +
                '</select></td>' +
                '<td><input type="text" name="memo[]" class="form-control" placeholder="Memo" value="<?php echo $details['memo']; ?>"></td>' +
                '<td><button type="button" class="btn btn-danger btn-sm removeItemBtn">Remove</button></td>'
            '</tr>';
            // Append the new row to the table
            $("#generalJournalTableBody").append(newRow);
        <?php endforeach; ?>
        // Show the remove button for the new row
        $("#generalJournalTableBody tr:last-child .removeItemBtn").show();
        // Update totals
        updateTotals();
    }


    function updateTotals() {
        // For example:
        var totalDebit = 0;
        var totalCredit = 0;

        $('#generalJournalTableBody tr').each(function() {
            var debit = parseFloat($(this).find('td:eq(1) input').val()) || 0;
            var credit = parseFloat($(this).find('td:eq(2) input').val()) || 0;

            totalDebit += debit;
            totalCredit += credit;
        });

        $('#totalDebit').text(totalDebit.toFixed(2));
        $('#totalCredit').text(totalCredit.toFixed(2));
    }

    function validateImbalance() {
        // Check for imbalance
        var totalDebit = parseFloat($('#totalDebit').text()) || 0;
        var totalCredit = parseFloat($('#totalCredit').text()) || 0;

        if (totalDebit !== totalCredit) {
            // Display SweetAlert warning
            Swal.fire({
                icon: 'error',
                title: 'Transaction Imbalance',
                text: 'The total amount in the Debit column must equal the total amount in the Credit column.',
                confirmButtonText: 'OK'
            });

            return false; // Imbalance detected
        }

        return true; // No imbalance
    }



    function validateDebitCreditConsistency() {
        var inconsistentEntries = [];

        $('#generalJournalTableBody tr').each(function() {
            var account = $(this).find('td:eq(0) select').val();
            var debit = parseFloat($(this).find('td:eq(1) input').val()) || 0;
            var credit = parseFloat($(this).find('td:eq(2) input').val()) || 0;

            if ((debit !== 0 && credit !== 0) && (debit > 0 && credit > 0)) {
                inconsistentEntries.push(account);
            }
        });

        if (inconsistentEntries.length > 0) {
            // Display warning about inconsistent entries
            var message = 'Inconsistent entries found. The following accounts have both debit and credit entries: ' + inconsistentEntries.join(', ');

            // Display SweetAlert warning
            Swal.fire({
                icon: 'warning',
                title: 'Inconsistent Entries',
                text: message,
                confirmButtonText: 'OK'
            });
        }
    }
</script>