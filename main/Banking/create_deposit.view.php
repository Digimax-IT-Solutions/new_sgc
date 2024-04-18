<?php
include __DIR__ . ('../../includes/header.php');
include('connect.php');

// Fetch all necessary data in one go
$query = "SELECT payment_id, payment_name FROM payment_methods;
          SELECT account_id, account_type, account_name FROM chart_of_accounts;
          SELECT 'Vendor' as source, vendorName as account_name FROM vendors
          UNION 
          SELECT 'Customer' as source, customerName as account_name FROM customers
          UNION 
          SELECT 'Other Names' as source, otherName as account_name FROM other_names";
$stmt = $db->prepare($query);
$stmt->execute();

// Fetch results
$paymentMethods = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->nextRowset();
$chartOfAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
$stmt->nextRowset();
$allAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    .breadcrumb {
        background-color: white;
    }

    .summary-details input {
        font-size: 90%;
        /* Adjust the percentage as needed */
    }

    .input-group-text {
        font-size: 50%;

    }

    .form-control {
        font-size: 80%;

    }

    .form-group label {
        font-size: 70%;
    }

    #itemTable {
        border-collapse: collapse;
        width: 100%;
        table-layout: fixed;
    }

    #itemTable th,
    #itemTable td {
        text-align: center;
        padding: 2px;
        /* Adjust the padding as needed */
    }

    #makeDepoTable {
        border-collapse: collapse;
        width: 100%;
        table-layout: fixed;
    }

    #makeDepoTable th,
    #makeDepoTable td {
        padding: 1px;
        white-space: nowrap;
        overflow: hidden;
        /* Hides any overflowing content */
        text-overflow: ellipsis;
        /* Adjust the padding as needed */
    }

    #makeDepoTable th:first-child {
        width: 20px;
        /* Set width of the first td */
    }
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Banking - Make Deposit</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Banking</a></li>
                        <li class="breadcrumb-item active">Make Deposit</li>
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
                            <form id="enterBillsForm" name="enterBillsForm" method="POST">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="bankAccountName">Deposit To:</label>
                                        <select name="bankAccountName" id="bankAccountName" class="form-control" required>
                                            <option value="" disabled selected>BANK</option>
                                            <?php
                                            // Fetch vendors with purchase orders having poStatus = 'WAITING FOR DELIVERY' from the database and populate the dropdown in the modal
                                            $accountQuery = "SELECT * FROM chart_of_accounts where account_type = 'Bank'";

                                            try {
                                                $accountStmt = $db->prepare($accountQuery);
                                                $accountStmt->execute();

                                                $accounts = $accountStmt->fetchAll(PDO::FETCH_ASSOC);

                                                foreach ($accounts as $account) {
                                                    echo "<option value='" . htmlspecialchars($account['account_name'], ENT_QUOTES) . "' data-poid='{$account['account_id']}'>" . htmlspecialchars($account['account_code'] . ' ' . $account['account_name'], ENT_QUOTES) . "</option>";
                                                }
                                            } catch (PDOException $e) {
                                                // Handle the exception, log the error or return an error message with MySQL error information
                                                $errorInfo = $accountStmt->errorInfo();
                                                $errorMessage = "Error fetching vendors: " . $errorInfo[2]; // MySQL error message
                                                echo "<option value=''>$errorMessage</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label for="deposit_date">Date:</label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="deposit_date" name="deposit_date" required>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label for="total_deposit">Total Deposit:</label>
                                        <input type="text" id="total_deposit" name="total_deposit" class="form-control" readonly>
                                    </div>

                                    <div class="form-group cold-md-2">
                                        <label for="deposit_id">Deposit ID:</label>
                                        <input class="form-control" type="text" name="deposit_id" required>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label for="memo">Memo:</label>
                                        <textarea name="memos" id="memos" class="form-control" rows="3">Deposit</textarea>
                                    </div>
                                </div>
                                <div>
                                    <table class="table table-bordered" id="itemTable">
                                        <thead>
                                            <tr>
                                                <th>RECEIVED FROM</th>
                                                <th>FROM ACCOUNT</th>
                                                <th>MEMO</th>
                                                <th>CHECK NO.</th>
                                                <th>PAYMENT METHOD</th>
                                                <th>AMOUNT</th>
                                                <th>ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemTableBody">

                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-success" id="addItemBtn">Add Accounts</button>
                                <center>
                                    <button type="button" class="btn btn-success" id="saveAndNewButton">Save and New</button>
                                    <button type="button" class="btn btn-info" id="saveAndCloseButton">Save and Close</button>
                                    <button type="button" class="btn btn-warning" id="clearButton">Clear</button>
                                </center>
                            </form>
                            <!-- <input type="text" class="form-control" name="grossAmount" id="grossAmount" readonly> -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <!-- Modal -->
    <div class="modal fade" id="makeDepositModal" tabindex="-1" role="dialog" aria-labelledby="makeDepositModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="makeDepositModalLabel">Banking - Make Deposit</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success">Save</button>
                </div>
            </div>
        </div>
    </div>

    <?php include __DIR__ . ('../../includes/footer.php'); ?>
</div>
<script>
    $(document).ready(function() {
        function calculateTotal() {
            var total = 0;
            // Iterate over each row in the table and sum up the amounts
            $('#itemTable tbody tr').each(function() {
                var amount = parseFloat($(this).find('td:nth-child(6) input').val()) || 0; // Get the amount from the input field in each row
                total += amount;
            });
            // Update the total deposit input field with the calculated total
            $('#total_deposit').val(total.toFixed(2));
        }

        // Add event listener to input fields with class "amount-input"
        $('#itemTableBody').on('input', '.amount-input', function() {
            calculateTotal();
        });
        // Add expense row when the "Add Expense" button is clicked
        $("#addItemBtn").on("click", function() {
            // Build options for the select dropdown
            var allAccs = <?php echo json_encode($allAccounts); ?>;
            var options = <?php echo json_encode($chartOfAccounts); ?>;
            var paymeths = <?php echo json_encode($paymentMethods); ?>;

            // Create a new row with the select dropdown
            var newRow = '<tr>' +
                '<td><select name="received_from[]"  class="form-control">';
            $.each(allAccs, function(index, account) {
                newRow += '<option value="' + account["account_name"] + '">' + account["account_name"] + ' | ' + account["source"] + '</option>';
            });
            newRow += '</select></td>' +
                '<td><select name="from_account[]" class="form-control">';
            $.each(options, function(index, account) {
                newRow += '<option value="' + account["account_name"] + '">' + account["account_name"] + '</option>';
            });
            newRow += '</select></td>' +
                '<td><input type="text" name="memo[]" class="form-control" placeholder="Memo"></td>' +
                '<td><input type="text" name="check_no[]" class="form-control"></td>' +
                '<td><select name="payment_method[]" class="form-control">';
            $.each(paymeths, function(index, method) {
                newRow += '<option value="' + method["payment_name"] + '">' + method["payment_name"] + '</option>';
            });
            newRow += '</select></td>' +
                '<td><input type="number" name="amount[]" class="form-control amount-input" placeholder="Amount"></td>' +
                '<td><button type="button" class="btn btn-danger removeItemBtn">Remove</button></td>' +
                '</tr>';

            // Append the new row to the table
            $("#itemTableBody").append(newRow);

            // Increment the counter
            calculateTotal();
        });

        $("#itemTableBody").on("click", ".removeItemBtn", function() {
            $(this).closest("tr").remove();
            calculateTotal();
        });

        function checkForBills() {
            console.log('Sending AJAX request to check for bills...');
            $.ajax({
                url: 'modules/banking/make_deposit/check_for_bills.php',
                method: 'GET',
                dataType: 'json',
                success: function(paymentData) {
                    var tableHtml = '<table id="makeDepoTable" class="table">' +
                        '<thead><tr>' +
                        '<th></th>' +
                        '<th>Date</th>' +
                        '<th>NO.</th>' +
                        '<th>Payment Method</th>' +
                        '<th>Customer Name</th>' +
                        '<th style="text-align: right">Payment Amount</th>' +
                        '</tr></thead><tbody>';

                    paymentData.forEach(function(row) {
                        // Parse payment_amount as float
                        var paymentAmount = parseFloat(row.payment_amount);

                        tableHtml += '<tr>' +
                            '<td><input type="checkbox" id="' + row.RefNo + '" class="payment-checkbox" ' +
                            'data-customername="' + row.customerName + '" ' +
                            'data-ar_account="' + row.ar_account + '" ' +
                            'data-refno="' + row.RefNo + '" ' +
                            'data-paymenttype="' + row.paymentType + '" ' +
                            'data-paymentamount="' + paymentAmount + '" ' + // Use parsed paymentAmount here
                            '></td>' +
                            '<td>' + row.receivedDate + '</td>' +
                            '<td>' + row.RefNo + '</td>' +
                            '<td>' + row.paymentType + '</td>' +
                            '<td>' + row.customerName + '</td>' +
                            '<td style="text-align: right">' + paymentAmount.toFixed(2) + '</td>' + // Use parsed paymentAmount here
                            '<td hidden>' + row.ar_account + '</td>' +
                            '</tr>';
                    });


                    tableHtml += '</tbody></table>';

                    $('#makeDepositModal .modal-body').html(tableHtml);
                    $('#makeDepositModal').modal('show');

                    // Add event listener for checkbox change
                    $('.payment-checkbox').change(function() {
                        if ($(this).is(':checked')) {
                            var ID = $(this).data('id');
                            var customerName = $(this).data('customername');
                            var arAccount = $(this).data('ar_account');
                            var RefNo = $(this).data('refno');
                            var paymentType = $(this).data('paymenttype');
                            var paymentAmount = $(this).data('paymentamount');

                            console.log('Customer Name:', customerName);
                            console.log('AR Account:', arAccount);
                            console.log('Ref No:', RefNo);

                            // Now you have access to the associated data, do whatever you need with it
                        }
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Error checking for bills:', error);
                }
            });
        }

        // Call the function to check for bills when the page is loaded
        checkForBills();


        // Call the function to check for bills when the page is loaded
        checkForBills();

        $('#makeDepositModal').on('click', '.btn-success', function() {
            var checkedCheckbox = $('#makeDepoTable').find('tbody input[type="checkbox"]:checked');
            if (checkedCheckbox.length > 0) {
                checkedCheckbox.each(function() {
                    var selectedRow = $(this).closest('tr');
                    var receivedFrom = selectedRow.find('td:nth-child(5)').text().trim();
                    var fromAccount = selectedRow.find('td:nth-child(7)').text().trim();
                    var memo = ''; // You may prompt the user to input a memo here
                    var checkNo = selectedRow.find('td:nth-child(3)').text().trim();
                    var paymentMethod = selectedRow.find('td:nth-child(4)').text().trim();
                    var amount = selectedRow.find('td:nth-child(6)').text().trim();
                    var id = selectedRow.find('td:nth-child(2)').text().trim(); // Get the ID of the selected row

                    // Log the ID along with other details

                    console.log('Received From:', receivedFrom);
                    console.log('From Account:', fromAccount);
                    console.log('Memo:', memo);
                    console.log('Check No:', checkNo);
                    console.log('Payment Method:', paymentMethod);
                    console.log('Amount:', amount);

                    // Create a new row with the received data
                    var newRow = '<tr>' +
                        '<td><select name="received_from[]" class="form-control">' +
                        '<option value="' + receivedFrom + '">' + receivedFrom + '</option>' +
                        '</select></td>' +
                        '<td><select name="from_account[]" class="form-control">' +
                        '<option value="' + fromAccount + '">' + fromAccount + '</option>' +
                        '</select></td>' +
                        '<td><input type="text" name="memo[]" class="form-control" placeholder="Memo" value="' + memo + '"></td>' +
                        '<td><input type="text" name="check_no[]" class="form-control" value="' + checkNo + '"></td>' +
                        '<td><select name="payment_method[]" class="form-control">' +
                        '<option value="' + paymentMethod + '">' + paymentMethod + '</option>' +
                        '</select></td>' +
                        '<td><input type="number" name="amount[]" class="form-control amount-input" placeholder="Amount" value="' + amount + '"></td>' +
                        '<td><button type="button" class="btn btn-danger removeItemBtn">Remove</button></td>' +
                        '</tr>';
                    // Append the new row to the table
                    $("#itemTableBody").append(newRow);
                });
                calculateTotal();
                // Hide the modal after adding rows
                $('#makeDepositModal').modal('hide');
            } else {
                console.log('Please select a row.');
                Swal.fire({
                    icon: 'warning',
                    title: 'No Row Selected',
                    text: 'Please select a row before proceeding.',
                    confirmButtonText: 'OK'
                });
            }
        });
        // Add event listener to the "Save and Close" button
        $('#saveAndCloseButton').on('click', function() {
            // Serialize form data
            var formData = $('#enterBillsForm').serialize();

            // Get the IDs of the checked rows in the modal
            var checkedIds = [];
            $('#makeDepoTable tbody input[type="checkbox"]:checked').each(function() {
                checkedIds.push($(this).attr('id'));
            });

            // Append the checked IDs to the form data
            formData += '&' + $.param({
                'checkedIds[]': checkedIds
            });

            // Log the formData to console
            console.log('Form Data:', formData);

            // Make AJAX request to submit form data
            $.ajax({
                url: 'modules/banking/make_deposit/save_make_deposit.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    // Handle success response
                    console.log('Form submitted successfully:', response);
                    // Display SweetAlert success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Form submitted successfully',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        // Optionally, redirect to another page after success
                        window.location.href = 'make_deposit';
                    });
                },
                error: function(xhr, status, error) {
                    // Log the server response to inspect it
                    console.log('Server Response:', xhr.responseText);

                    // Handle error response
                    console.error('Error submitting form:', error);
                    // Display SweetAlert error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Please complete all required fields.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        $('#saveAndNewButton').on('click', function() {
            // Serialize form data
            var formData = $('#enterBillsForm').serialize();

            // Get the IDs of the checked rows in the modal
            var checkedIds = [];
            $('#makeDepoTable tbody input[type="checkbox"]:checked').each(function() {
                checkedIds.push($(this).attr('id'));
            });

            // Append the checked IDs to the form data
            formData += '&' + $.param({
                'checkedIds[]': checkedIds
            });

            // Log the formData to console
            console.log('Form Data:', formData);

            // Make AJAX request to submit form data
            $.ajax({
                url: 'modules/banking/make_deposit/save_make_deposit.php',
                method: 'POST',
                data: formData,
                dataType: 'json',
                success: function(response) {
                    // Handle success response
                    console.log('Form submitted successfully:', response);
                    // Display SweetAlert success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Form submitted successfully',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        // Optionally, redirect to another page after success
                        window.location.href = 'make_deposit';
                    });
                },
                error: function(xhr, status, error) {
                    // Log the server response to inspect it
                    console.log('Server Response:', xhr.responseText);

                    // Handle error response
                    console.error('Error submitting form:', error);
                    // Display SweetAlert error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Please complete all required fields.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });


    });
</script>