<?php
include __DIR__ . ('../../includes/header.php');
?>
<?php
include('connect.php');
$query = "SELECT itemName, itemPurchaseInfo, itemCost, uom, itemQty FROM items";
$result = $db->query($query);

$productItems = array();

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $productItems[] = $row;
}
?>
<?php
$query = "SELECT account_id, account_type, account_name FROM chart_of_accounts";
$result = $db->query($query);

$chartOfAccount = array();

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $chartOfAccount[] = $row;
}
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
    }

    #itemTable th,
    #itemTable td {
        text-align: center;
        padding: 2px;
        /* Adjust the padding as needed */
    }
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Enter Bills - Bill</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Vendor Center</a></li>
                        <li class="breadcrumb-item"><a href="write_check">Enter Bills</a></li>
                        <li class="breadcrumb-item active">Bill</li>
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
                                    <div class="form-group col-md-3">
                                        <label for="bankAccountName">A/P Account:</label>
                                        <select name="bankAccountName" id="bankAccountName" class="form-control" required>
                                            <option value="" disabled selected>Accounts Payable(A/P)</option>
                                            <?php
                                            // Fetch vendors with purchase orders having poStatus = 'WAITING FOR DELIVERY' from the database and populate the dropdown in the modal
                                            $accountQuery = "SELECT * FROM chart_of_accounts where account_type = 'Accounts Payable'";

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

                                        <label for="vendor">Vendor</label>
                                        <select class="form-control" id="vendor" name="vendor">
                                            <option value="" disabled selected>Select vendor</option>
                                            <?php
                                            // Fetch vendors with purchase orders having poStatus = 'WAITING FOR DELIVERY' from the database and populate the dropdown in the modal
                                            $vendorQuery = "SELECT * FROM vendors";

                                            try {
                                                $vendorStmt = $db->prepare($vendorQuery);
                                                $vendorStmt->execute();

                                                $vendors = $vendorStmt->fetchAll(PDO::FETCH_ASSOC);

                                                foreach ($vendors as $vendor) {
                                                    echo "<option value='{$vendor['vendorName']}'>{$vendor['vendorName']}</option>";
                                                }
                                            } catch (PDOException $e) {
                                                // Handle the exception, log the error or return an error message with MySQL error information
                                                $errorInfo = $vendorStmt->errorInfo();
                                                $errorMessage = "Error fetching vendors: " . $errorInfo[2]; // MySQL error message
                                                echo "<option value=''>$errorMessage</option>";
                                            }
                                            ?>
                                        </select>


                                        <label for="address">Address:</label>
                                        <textarea name="address" id="address" class="form-control" rows="3" required></textarea>


                                        <label for="terms">TERMS</label>
                                        <select class="form-control" id="terms" name="terms" required>
                                            <option value="">Select Term</option>
                                            <?php
                                            $query = "SELECT term_id, term_name, term_days_due FROM terms";
                                            $result = $db->query($query);

                                            if ($result) {
                                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<option value='{$row['term_name']}' data-days-due='{$row['term_days_due']}'>{$row['term_name']}</option>";
                                                }
                                            }
                                            ?>
                                        </select>


                                    </div>

                                    <div class="form-group col-md-2 offset-md-2">
                                        <!-- Empty div with offset -->
                                    </div>

                                    <div class="form-group col-md-2">

                                        <label for="reference_no">REFERENCE NO.</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="reference_no" name="reference_no" placeholder="REFERENCE NO" required>
                                        </div>
                                        <label for="bill_date">Bill Due</label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="bill_date" name="bill_date" required>
                                        </div>
                                        <label for="bill_due">Discount Date</label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="bill_due" name="bill_due" required>
                                        </div>
                                        <label for="total_amount">Amount Due</label>
                                        <div class="total" style="font-size: 30px;">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="total_amount" name="total_amount" value="" hidden>
                                                <input type="text" class="form-control" id="display_amount" value="" readonly>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="memo">Memo:</label>
                                        <textarea name="memos" id="memos" class="form-control" rows="3"></textarea>
                                    </div>

                                    <table class="table table-bordered" id="itemTable">
                                        <thead>
                                            <tr>
                                                <th>Account</th>
                                                <th>Memo</th>
                                                <th>Amount</th>
                                                <th>ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemTableBody">
                                            <!-- Each row represents a separate item -->
                                            <!-- You can dynamically add rows using JavaScript/jQuery -->
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-success" id="addItemBtn">Add Accounts</button>
                                <center><button type="button" class="btn btn-primary" id="saveAndCloseButton">Submit</button></center>
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
    $(document).ready(function() {
        // Function to set the formatted date value for a given input field
        function setFormattedDate(inputField, date) {
            var formattedDate = date.toISOString().split('T')[0];
            inputField.val(formattedDate);
        }

        // Function to calculate due date based on selected term and update receiveItemDueDate
        function updateDueDate() {
            var selectedTerm = $("#terms").find("option:selected");
            var daysDue = selectedTerm.data("days-due");

            if (daysDue !== undefined) {
                var currentDate = new Date($("#bill_date").val());
                currentDate.setDate(currentDate.getDate() + parseInt(daysDue));
                setFormattedDate($("#bill_due"), currentDate);
            } else {
                $("#bill_due").val("");
            }
        }

        // Initialize receiveItemDate with the current date
        var currentDate = new Date();
        setFormattedDate($("#bill_date"), currentDate);

        // Event listener for when the receiveItemDate input changes
        $("#bill_date").on("change", function() {
            updateDueDate();
        });

        // Event listener for when the terms dropdown changes
        $("#terms").on("change", function() {
            updateDueDate();
        });

        // Counter to track the number of added expense rows
        var expenseCounter = 1;

        function calculateGrossAmount() {
            var grossAmount = 0;
            // Iterate through all input elements with name "amount[]"
            $("input[name='amount[]']").each(function() {
                grossAmount += parseFloat($(this).val()) || 0;
            });

            $("#total_amount").val(grossAmount.toFixed(2));
                document.getElementById("total_amount").value = grossAmount.toFixed(2);

    // Format the display value for the user
                var formattedAmount = "₱" + grossAmount.toFixed(2);
                document.getElementById("display_amount").value = formattedAmount;   
    // If you want to prevent the form from being submitted for demonstration purposes
                event.preventDefault();
            }

        $("#enterBillsForm").on("keyup", "input.amount-input", function() {
            // Recalculate the gross amount when an "Amount" input changes
            calculateGrossAmount();
        });
        // Add expense row when the "Add Expense" button is clicked
        $("#addItemBtn").on("click", function() {
            // Build options for the select dropdown
            var options = "";
            <?php foreach ($chartOfAccount as $account) : ?>
                options += '<option value="<?= $account["account_name"] ?>"><?= $account["account_name"] ?></option>';
            <?php endforeach; ?>

            // Create a new row with the select dropdown
            var newRow = '<tr>' +
                '<td><select name="account[]" class="form-control">' + options + '</select></td>' +
                '<td><input type="text" name="memo[]" class="form-control" placeholder="Memo"></td>' +
                '<td><input type="number" name="amount[]" class="form-control amount-input" placeholder="Amount"></td>' +
                '<td><button type="button" class="btn btn-danger" onclick="removeExpenseRow(this)">Remove</button></td>' +
                '</tr>';

            // Append the new row to the table
            $("#itemTableBody").append(newRow);

            // Increment the counter
            expenseCounter++;

        });

        // Function to remove an expense row
        window.removeExpenseRow = function(button) {
            // Find the closest row and remove it
            $(button).closest('tr').remove();
            calculateGrossAmount();
        };

    });
</script>
<script>
    $(document).ready(function() {
        $("#saveAndCloseButton").on("click", function() {
            // Validate the form
            if (!isFormValid()) {
                // Show SweetAlert error if the form is not valid
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Please fill in all required fields.', // Customize the error message
                });
            } else {
                // Perform any client-side validation if needed

                // Use AJAX to send form data to the server-side PHP script
                $.ajax({
                    url: 'modules/vendor_center/enter_bills/save_enter_bills.php',
                    method: 'POST',
                    data: $("#enterBillsForm").serialize(),
                    success: function(response) {
                        // Handle the success response using SweetAlert with success icon
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Enter Bills submitted successfully!',
                        }).then((result) => {
                            // Redirect to the sales_invoice page after clicking OK
                            if (result.isConfirmed) {
                                window.location.href = 'enter_bills';
                            }
                        });
                    },
                    error: function(xhr, status, error) {
                        // Handle the error response using SweetAlert with error icon
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Error submitting PO. Please try again.',
                        });
                        console.error('Error submitting PO: ' + error);
                    }
                });
            }
        });

        // Function to validate the form
        function isFormValid() {
            var isValid = true;

            if ($("#bankAccountName").val() === null) {
                // If the selected option value is null or empty
                isValid = false;
                highlightInvalidField($("#bankAccountName")); // Highlight the field as invalid
            } else {
                // If a valid option is selected
                resetInvalidField($("#bankAccountName")); // Reset any previous invalid styling
            }

            if ($("#vendor").val() === null) {
                // If the selected option value is null or empty
                isValid = false;
                highlightInvalidField($("#vendor")); // Highlight the field as invalid
            } else {
                // If a valid option is selected
                resetInvalidField($("#vendor")); // Reset any previous invalid styling
            }

            // Add validation logic for each required field
            if ($("#address").val() === '') {
                isValid = false;
                highlightInvalidField($("#address"));
            } else {
                resetInvalidField($("#address"));
            }

            // Add validation logic for each required field
            if ($("#reference_no").val() === '') {
                isValid = false;
                highlightInvalidField($("#reference_no"));
            } else {
                resetInvalidField($("#reference_no"));
            }

            // Add validation logic for each required field
            if ($("#terms").val() === '') {
                isValid = false;
                highlightInvalidField($("#terms"));
            } else {
                resetInvalidField($("#terms"));
            }

            // Add validation logic for each required field
            if ($("#bill_date").val() === '') {
                isValid = false;
                highlightInvalidField($("#bill_date"));
            } else {
                resetInvalidField($("#bill_date"));
            }

            if ($("#bill_due").val() === '') {
                isValid = false;
                highlightInvalidField($("#bill_due"));
            } else {
                resetInvalidField($("#bill_due"));
            }


            if ($("#itemTableBody tr").length === 0) {
                isValid = false;
            } else {
                // Iterate through each row
                $("#itemTableBody tr").each(function() {
                    var itemName = $(this).find("[name='account[]']").val();
                    var description = $(this).find("[name='memo[]']").val();
                    var quantity = $(this).find("[name='amount[]']").val();

                    // Check if any of the fields is empty
                    if (!itemName || !quantity) {
                        isValid = false;
                        return false; // Exit the loop if any field is empty
                    }
                });
            }
            return isValid;

        }

        // Function to highlight an invalid field
        function highlightInvalidField(field) {
            field.addClass("is-invalid");
        }

        // Function to reset the highlighting of an invalid field
        function resetInvalidField(field) {
            field.removeClass("is-invalid");
        }
    });
</script>