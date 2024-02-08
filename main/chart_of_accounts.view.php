<?php include __DIR__ . ('/includes/header.php'); ?>
<?php                     include('connect.php'); ?>

<style>
 #accountsTable {
    border-collapse: collapse;
    width: 100%;
}

#accountsTable th,
#accountsTable td {
    padding: 1px;
    /* Adjust the padding as needed */
}
#accountsTable tbody tr:hover {
    color: white;
    background-color: rgb(0, 149, 77); /* Set your desired background color here */
}
</style>


<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Chart of Accounts</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-left">
                                        <li class="breadcrumb-item"><a style="color:maroon;" href="dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Master List</li>
                                        <li class="breadcrumb-item active">Chart of Accounts</li>
                                    </ol>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <button class="btn btn-success" data-toggle="modal"
                                            data-target="#addAccountModal"
                                            style="background-color: rgb(0, 149, 77); color: white;">
                                            New Account
                                        </button>
                                    </ol>
                                </div>
                            </div>
                            <br><br>
                            <table id="accountsTable" class="table table-hover table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <!-- <th>Account ID</th> -->
                                        <!-- <th>ACCOUNT #</th> -->
                                        <th>ACCOUNT CODE</th>
                                        <th>ACCOUNT NAME</th>
                                        <th>ACCOUNT TYPE</th>
                                        <th>DESCRIPTION</th>
                                        <th>SUB-ACCOUNT OF</th>
                                        <th>BALANCE</th>

                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Your accounts data will go here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include('includes/footer.php'); ?>
</div>

<!-- Add Account Modal -->
<div class="modal" id="addAccountModal" tabindex="-1" role="dialog" aria-labelledby="addAccountModal"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                <h5 class="modal-title" id="addAccountModalLabel"><b>ADD NEW ACCOUNT</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background-color: rgb(0,149,77); color: white;">
                <form id="addAccountForm">
                    <?php
// Fetch the last ID from the charts_of_accounts table
$stmt = $db->prepare("SELECT account_id FROM chart_of_accounts ORDER BY account_id DESC LIMIT 1");
$stmt->execute();
$lastIDRow = $stmt->fetch(PDO::FETCH_ASSOC);

    // If there are existing records, extract the numeric part and increment
    if ($lastIDRow) {
        $numericPart = intval($lastIDRow['account_id']) + 1;
    } else {
        // If no existing records, start with 1
        $numericPart = 1;
    }
// Generate the new ID
$newID = 'AC-' . str_pad($numericPart, 6, '0', STR_PAD_LEFT);

// Use $newID for inserting the new record into the database
// INSERT INTO charts_of_accounts (id, other_columns) VALUES ('$newID', '...');

?>
                    <h5>ACCOUNT # <?php echo $newID; ?></h5>
                    <div class="row"
                        style="background-color: rgb(0,149,77); padding: 20px; color: white; border-radius: 5px;">

                        <div class="col-md-4">
                            <!-- Account Type -->
                            <div class="form-group">
                                <label for="accountType">ACCOUNT TYPE</label>
                                <select class="form-control" id="accountType" name="accountType">
                                    <option value="Bank">BANK</option>
                                    <option value="Accounts Receivable">ACCOUNTS RECEIVABLE</option>
                                    <option value="Other Current Assets">OTHER CURRENT ASSETS</option>
                                    <option value="Accounts Payable">ACCOUNTS PAYABLE</option>
                                    <option value="Other Current Liabilities">OTHER CURRENT LIABILITIES</option>
                                    <option value="Non-current Liabilities">NON-CURRENT LIABILITIES</option>
                                    <option value="Other Non-current Liabilities">OTHER NON-CURRENT LIABILITIES</option>
                                    <option value="Equity">EQUITY</option>
                                    <option value="Income">INCOME</option>
                                    <option value="Cost of Goods Sold">COST OF GOODS SOLD</option>
                                    <option value="Expense">EXPENSE</option>
                                    <option value="Other Income">OTHER INCOME</option>
                                    <option value="Other Expense">OTHER EXPENSE</option>
                                </select>
                            </div>

                            <!-- Account Code -->
                            <div class="form-group">

                                <label for="accountCode">ACCOUNT CODE</label>
                                <input type="text" class="form-control" id="accountCode" name="accountCode"
                                    placeholder="Enter Account Code" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <?php
                    // Include your database connection file




                    try {
                        // Fetch sub-accounts from the charts_of_accounts table
                        $stmt = $db->prepare('SELECT account_id, account_name FROM chart_of_accounts');
                        $stmt->execute();
                        $subAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                        exit;
                    }
                    ?>

                            <!-- Set the retrieved sub-account IDs in your dropdown -->
                            <div class="form-group">
                                <label for="subAccountOf">SUB-ACCOUNT OF</label>
                                <select class="form-control" id="subAccountOf" name="subAccountOf">
                                    <option value="">Select Sub Account</option>
                                    <?php foreach ($subAccounts as $subAccount) : ?>
                                    <option value="<?= $subAccount['account_name']; ?>">
                                        <?= $subAccount['account_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Account Name -->
                            <div class="form-group">
                                <label for="accountName">ACCOUNT NAME</label>
                                <input type="text" class="form-control" id="accountName" name="accountName"
                                    placeholder="Enter Account Name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- Balance -->
                            <div class="form-group">
                                <label for="accountBalance">BALANCE</label>
                                <input type="text" class="form-control" id="accountBalance" name="accountBalance" placeholder="Balance">
                            </div>

                            <!-- Description -->
                            <div class="form-group">
                                <label for="description">DESCRIPTION</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                    placeholder="Enter Description"></textarea>
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" id="saveAccountButton">Save Account</button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
<input type="file" id="fileInput" style="display: none;">

<!-- Edit Location Modal -->
<div class="modal" id="editAccountModal" tabindex="-1" role="dialog" aria-labelledby="editAccountModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                <h5 class="modal-title" id="editAccountModalLabel"><b>Edit Account</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background-color: rgb(0,149,77);">
                <form id="editAccountForm">
                    <div class="row"
                        style="background-color: rgb(0,149,77); padding: 20px; color: white; border-radius: 5px;">
                        <div class="col-md-4">

                            <input type="text" class="form-control" id="editAccountID" name="editAccountID" hidden>

                            <!-- Account Type -->
                            <div class="form-group">
                                <label for="editAccountType">ACCOUNT TYPE</label>
                                <select class="form-control" id="editAccountType" name="editAccountType">
                                    <option value="Bank">Bank</option>
                                    <option value="Accounts Receivable">Accounts Receivable</option>
                                    <option value="Other Current Assets">Other Current Assets</option>
                                    <option value="Accounts Payable">Accounts Payable</option>
                                    <option value="Other Current Liabilities">Other Current Liabilities</option>
                                    <option value="Non-current Liabilities">Non-current Liabilities</option>
                                    <option value="Other Non-current Liabilities">Other Non-current Liabilities</option>
                                    <option value="Equity">Equity</option>
                                    <option value="Income">Income</option>
                                    <option value="Cost of Goods Sold">Cost of Goods Sold</option>
                                    <option value="Expense">Expense</option>
                                    <option value="Other Income">Other Income</option>
                                    <option value="Other Expense">Other Expense</option>
                                </select>
                            </div>

                            <!-- Account Code -->
                            <div class="form-group">
                                <label for="editAccountCode">ACCOUNT CODE</label>
                                <input type="text" class="form-control" id="editAccountCode" name="editAccountCode"
                                    placeholder="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <?php
                    try {
                        // Fetch sub-accounts from the charts_of_accounts table
                        $stmt = $db->prepare('SELECT account_id, account_name FROM chart_of_accounts');
                        $stmt->execute();
                        $subAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    } catch (PDOException $e) {
                        echo "Error: " . $e->getMessage();
                        exit;
                    }
                    ?>

                            <!-- Set the retrieved sub-account IDs in your dropdown -->
                            <div class="form-group">
                                <label for="editSubAccountOf">SUB-ACCOUNT OF</label>
                                <select class="form-control" id="editSubAccountOf" name="editSubAccountOf">
                                    <option value=""></option>
                                    <?php foreach ($subAccounts as $subAccount) : ?>
                                    <option value="<?= $subAccount['account_name']; ?>">
                                        <?= $subAccount['account_name']; ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <!-- Account Name -->
                            <div class="form-group">
                                <label for="editAccountName">ACCOUNT NAME</label>
                                <input type="text" class="form-control" id="editAccountName" name="editAccountName"
                                    placeholder="Enter Account Name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <!-- Balance -->
                            <!-- Balance -->
                            <div class="form-group">
                                <label for="editAccountBalance">BALANCE</label>
                                <input type="text" class="form-control" id="editAccountBalance"
                                    name="editAccountBalance">
                            </div>


                            <!-- Description -->
                            <div class="form-group">
                                <label for="editDescription">DESCRIPTION</label>
                                <textarea class="form-control" id="editDescription" name="editDescription" rows="3"
                                    placeholder="Enter Description"></textarea>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="saveEditAccountButton">Save Changes</button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $("#saveAccountButton").click(function() {
        // Get form data
        var formData = $("#addAccountForm").serialize();

        // AJAX request to store data
        $.ajax({
            type: "POST",
            url: "modules/chart_of_accounts/save_account.php",
            data: formData,
            success: function(response) {
                // Use SweetAlert2 for displaying success or error message
                if (response === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'New Account Added Successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        // Reload the browser after SweetAlert2 is closed
                        location.reload();
                    });
                    $("#addAccountModal").modal("hide");

                    // Update the table after successfully saving the item
                    populateItemsTable();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error saving item. Please try again.',
                        text: response, // Display the MySQL error message
                        showConfirmButton: false,
                        timer: 5000 // Adjust the timer as needed
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error saving item. Please try again.',
                    text: response, // Display the MySQL error message
                    showConfirmButton: false,
                    timer: 5000 // Adjust the timer as needed
                });
            }
        });
    });
    // Variable to check if DataTables is already initialized
    var dataTableInitialized = false;
    // Function to fetch and populate data
    function populateAccountsTable() {
        $.ajax({
            type: "GET",
            url: "modules/chart_of_accounts/get_account.php", // Adjust the URL to the server-side script
            success: function(response) {
                // Parse the JSON response
                var accounts = JSON.parse(response);

                // Clear existing table rows
                $("#accountsTable tbody").empty();

                // Inside the items.forEach() loop where you populate the table
                // <td>${account.account_id}</td>
                accounts.forEach(function(account) {
                    var row = `<tr>
     
                    <td>${account.account_code}</td>
                    <td>${account.account_name}</td>
                    <td>${account.account_type}</td>
                    <td>${account.description}</td>
                    <td>${account.sub_account_of}</td>
                    <td>${account.account_balance}</td>

                    <td>
                    <button type="button" class="btn btn-primary btn-sm editAccountButton" style="background-color: rgb(0, 149, 77); color: white; border: 1px rgb(0, 149, 77);" data-id="${account.account_id}">Edit</button>
                        <button type="button" class="btn btn-danger btn-sm deleteLocationButton" data-id="${account.account_id}">Delete</button></td>
                </tr>`;
                    $("#accountsTable tbody").append(row);


                });

                // Initialize DataTables only if it's not already initialized
                if (!dataTableInitialized) {
                    $('#accountsTable').DataTable({
                        "paging": true,
                        "lengthChange": true,
                        "searching": true,
                        "info": true,
                        "autoWidth": false,
                        "lengthMenu": [10, 25, 50, 100, 500],
                        "ordering": true, // Disable sorting for all columns
                        "dom": 'lBfrtip',
                        "buttons": [{
                                extend: 'copy',
                                exportOptions: {
                                    columns: ':not(:last-child)' // Exclude the last column (ACTION)
                                }
                            },
                            {
                                extend: 'csv',
                                exportOptions: {
                                    columns: ':not(:last-child)' // Exclude the last column (ACTION)
                                }
                            },
                            {
                                extend: 'excel',
                                exportOptions: {
                                    columns: ':not(:last-child)' // Exclude the last column (ACTION)
                                }
                            },
                            {
                                extend: 'pdf',
                                exportOptions: {
                                    columns: ':not(:last-child)' // Exclude the last column (ACTION)
                                }
                            },
                            {
                                extend: 'print',
                                exportOptions: {
                                    columns: ':not(:last-child)' // Exclude the last column (ACTION)
                                }
                            },
                        ],
                        "oLanguage": {
                            "sSearch": "Search:",
                            "sLengthMenu": "Show _MENU_ entries",
                            "sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
                            "sInfoEmpty": "Showing 0 to 0 of 0 entries",
                            "sInfoFiltered": "(filtered from _MAX_ total entries)",
                            "oPaginate": {
                                "sFirst": "First",
                                "sLast": "Last",
                                "sNext": "Next",
                                "sPrevious": "Previous"
                            }
                        }
                    });

                    // Set the flag to indicate DataTables is now initialized
                    dataTableInitialized = true;

                } else {
                    // If DataTables is already initialized, destroy and recreate it
                    $('#accountsTable').DataTable().destroy();
                    $('#accountsTable').DataTable({
                        // Your DataTables options here
                    });
                }
            },
            error: function() {
                console.log("Error fetching data.");
            }
        });
    }
    // Function to display an error using SweetAlert2
    function displayError(errorMessage) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: errorMessage,
            showConfirmButton: false,
            timer: 5000
        });
    }
    // Initial population when the page loads
    populateAccountsTable();


});
// Edit Location functionality
$("#accountsTable").on("click", ".editAccountButton", function() {
    var chartOfAccountID = $(this).data("id");

    // Populate the edit modal with location data
    $.ajax({
        type: "GET",
        url: "modules/chart_of_accounts/get_account_details.php", // Replace with your server-side script
        data: {
            chartOfAccountID: chartOfAccountID
        },
        success: function(response) {
            var chartOfAccountDetails = JSON.parse(response);

            // Populate the edit modal with location details
            $("#editAccountID").val(chartOfAccountDetails.account_id);
            $("#editAccountType").val(chartOfAccountDetails.account_type);
            $("#editAccountCode").val(chartOfAccountDetails.account_code);
            $("#editSubAccountOf").val(chartOfAccountDetails.sub_account_of);
            $("#editAccountName").val(chartOfAccountDetails.account_name);
            $("#editAccountBalance").val(chartOfAccountDetails.account_balance);
            $("#editDescription").val(chartOfAccountDetails.description);

            // Show the edit modal
            $("#editAccountModal").modal("show");
        },
        error: function() {
            console.log("Error fetching location details for edit.");
        }
    });
});
// Save Edit Account Changes functionality
$("#saveEditAccountButton").click(function() {
    var formData = $("#editAccountForm").serialize();

    $.ajax({
        type: "POST",
        url: "modules/chart_of_accounts/update_account.php", // Replace with your server-side script
        data: formData,
        success: function(response) {
            if (response === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'Account updated successfully!',
                    showConfirmButton: false,
                    timer: 1500
                }).then(function() {
                    // Reload the browser after SweetAlert2 is closed
                    location.reload();
                });
            } else {
                // Display the error message in SweetAlert2
                displayError(response);
            }
        },
        error: function() {
            displayError("An unexpected error occurred. Please try again.");
        }
    });
});
// Delete Location functionality
$("#accountsTable").on("click", ".deleteLocationButton", function() {
    var chartOfAccountID = $(this).data("id");

    // Display a confirmation dialog before deleting
    Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Delete the location via AJAX
            $.ajax({
                type: "POST",
                url: "modules/chart_of_accounts/delete_chart_accounts.php",
                data: {
                    deleteChartOfAccountID: chartOfAccountID
                },
                success: function(response) {
                    if (response === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Account deleted successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            // Reload the browser after SweetAlert2 is closed
                            location.reload();
                        });
                        // Update the table after successfully deleting the location
                        populateAccountsTable();

                    } else {
                        // Display the error message in SweetAlert2
                        displayError(response);
                    }
                },
                error: function() {
                    displayError("An unexpected error occurred. Please try again.");
                }
            });
        }
    });
});
// Function to display an error using SweetAlert2
function displayError(errorMessage) {
    Swal.fire({
        icon: 'error',
        title: 'Error deleting location. Please try again.',
        text: errorMessage,
        showConfirmButton: false,
        timer: 5000 // Adjust the timer as needed
    });
}

// Variable to check if DataTables is already initialized
var dataTableInitialized = false;
</script>