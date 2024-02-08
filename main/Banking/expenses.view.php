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
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Write Check - Expenses</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Banking</a></li>
                        <li class="breadcrumb-item"><a href="write_check">Write Check</a></li>
                        <li class="breadcrumb-item active">Expenses</li>
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
                            <form id="writeCheckForm" action="" method="POST">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="bankAccountName">Bank:</label>
                                        <select name="bankAccountName" id="bankAccountName" class="form-control" required>
                                            <option value="" disabled selected>Bank Account</option>
                                            <?php
                                                    // Fetch vendors with purchase orders having poStatus = 'WAITING FOR DELIVERY' from the database and populate the dropdown in the modal
                                                    $vendorQuery = "SELECT * FROM chart_of_accounts where account_type = 'Bank'";

                                                    try {
                                                        $vendorStmt = $db->prepare($vendorQuery);
                                                        $vendorStmt->execute();

                                                        $vendors = $vendorStmt->fetchAll(PDO::FETCH_ASSOC);

                                                        foreach ($vendors as $vendor) {
                                                            echo "<option value='" . htmlspecialchars($vendor['account_name'], ENT_QUOTES) . "' data-poid='{$vendor['account_id']}'>" . htmlspecialchars($vendor['account_code'] . ' ' . $vendor['account_name'], ENT_QUOTES) . "</option>";
                                                        }
                                                    } catch (PDOException $e) {
                                                        // Handle the exception, log the error or return an error message with MySQL error information
                                                        $errorInfo = $vendorStmt->errorInfo();
                                                        $errorMessage = "Error fetching vendors: " . $errorInfo[2]; // MySQL error message
                                                        echo "<option value=''>$errorMessage</option>";
                                                    }
                                                    ?>
                                        </select>
                                    
                                        <label for="payee_name">Payee Name:</label>
                                        <?php
                                        $allQuery = "SELECT 'Vendor' as source, vendorName FROM vendors 
                                                    UNION 
                                                    SELECT 'Customer' as source, customerName FROM customers
                                                    UNION 
                                                    SELECT 'Other Names' as source, otherName FROM other_names";

                                        try {
                                            $allStmt = $db->prepare($allQuery);
                                            $allStmt->execute();

                                            $alls = $allStmt->fetchAll(PDO::FETCH_ASSOC);

                                            echo "<select name='payeeName' id='payeeName' class='form-control' required>";
                                            echo "<option value='' disabled selected>Select Payee:</option>";

                                            foreach ($alls as $all) {
                                                $optionValue = htmlspecialchars($all['source'], ENT_QUOTES);
                                                $optionValue1 = htmlspecialchars($all['vendorName'], ENT_QUOTES);
                                                echo "<option value='" . $optionValue1 . "'>" . $optionValue . " | " .$optionValue1 ."</option>";
                                            }

                                            echo "</select>";
                                        } catch (PDOException $e) {
                                            // Handle the exception, log the error, or return an error message with MySQL error information
                                            $errorInfo1 = $allStmt->errorInfo();
                                            $errorMessage1 = "Error fetching vendors: " . $errorInfo1[2]; // MySQL error message
                                            echo "<option value=''>$errorMessage1</option>";
                                        }
                                        ?>


                                   
                                        <label for="address">Address:</label>
                                        <textarea name="address" id="address" class="form-control" rows="3" REQUIRED></textarea>
                                    </div>
                                    <div class="form-group col-md-2 offset-md-2">
                                        <!-- Empty div with offset -->
                                    </div>
                                    
                                    <div class="form-group col-md-2">
                                        <label for="checkDate">DATE</label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="checkDate" name="checkDate" required>
                                        </div>
                                        <label for="referenceNo">REFERENCE NO.</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="referenceNo" name="referenceNo" placeholder="REFERENCE NO" required>
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
                                </div>
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="expenses-tab" data-toggle="tab" href="#expenses" role="tab">Expenses</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" id="items-tab" data-toggle="tab" href="#items" role="tab">Items</a>
                                    </li>
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <!-- Expenses Tab -->
                                    <div class="tab-pane show active" id="expenses" role="tabpanel">
                              
                                        <table class="table table-bordered" id="itemTable">
                                            <thead>
                                                <tr>
                                                    <th>Account</th>
                                                    <th>Memo</th>
                                                    <th>Amount</th>
                                                    <th>ACTION</th>
                                                </tr>
                                            </thead>
                                            <tbody id="itemTableBody1">
                                                <!-- Each row represents a separate item -->
                                                <!-- You can dynamically add rows using JavaScript/jQuery -->
                                            </tbody>
                                        </table>
                                        <button type="button" class="btn btn-success" id="addItemBtnn">Add Expense</button>
                                    </div>

                                    <!-- Items Tab -->
                                    <div class="tab-pane" id="items" role="tabpanel">
                                     
                                        <table class="table table-bordered" id="itemTable">
                                        <thead>
                                            <tr>
                                                <th>ITEM</th>
                                                <th>DESCRIPTION</th>
                                                <th>QTY</th>
                                                <th>UOM</th>
                                                <th>COST</th>
                                                <th>AMOUNT</th>
                                                <th>ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemTableBody">
                                            <!-- Each row represents a separate item -->
                                            <!-- You can dynamically add rows using JavaScript/jQuery -->
                                        </tbody>
                                    </table>
                                    <button type="button" class="btn btn-success" id="addItemBtn">Add Item</button>
                                    </div>
                                </div>

                                <center><button type="button" class="btn btn-primary" id="saveAndNewButton">Submit</button></center>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include __DIR__ . ('../../includes/footer.php'); ?>
</div>
<script>
$(document).ready(function () {
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
        $("#writeCheckForm").on("keyup", "input.amount-input", function() {
                // Recalculate the gross amount when an "Amount" input changes
                calculateGrossAmount();
        });
    // Add expense row when the "Add Expense" button is clicked
    $("#addItemBtnn").on("click", function () {
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
        $("#itemTableBody1").append(newRow);

        // Increment the counter
        expenseCounter++;
    });

    // Function to remove an expense row
    window.removeExpenseRow = function (button) {
        // Find the closest row and remove it
        $(button).closest('tr').remove();
    };
});
</script>
<script>
    $(document).ready(function() {
        $("#saveAndNewButton").on("click", function () {
            // Validate the form
            if (!isFormValid()) {
                // Show SweetAlert error if the form is not valid
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Please fill in all required fields.', // Customize the error message
                });
                return;
            }

            // Use AJAX to send form data to the server-side PHP script
            $.ajax({
                url: 'modules/banking/write_check/save_write_check.php',
                method: 'POST',
                data: $("#writeCheckForm").serialize(),
                success: function (response) {
                    // Handle the success response using SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response,
                    }).then((result) => {
                        // Redirect to the expenses page after clicking OK
                        if (result.isConfirmed) {
                            window.location.href = 'write_check';
                        }
                    });
                },
                error: function (xhr, status, error) {
                    // Handle the error response using SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Write check submitted successfully!',
                    }).
                    then((result) => {
                        // Redirect to the expenses page after clicking OK
                        if (result.isConfirmed) {
                            window.location.href = 'write_check';
                        }
                    });
                    
                }
            });
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

            if ($("#payeeName").val() === null) {
            // If the selected option value is null or empty
            isValid = false;
            highlightInvalidField($("#payeeName")); // Highlight the field as invalid
            } else {
            // If a valid option is selected
            resetInvalidField($("#payeeName")); // Reset any previous invalid styling
            }

            if ($("#address").val() === '') {
                isValid = false;
                highlightInvalidField($("#address"));
            } else {
                resetInvalidField($("#address"));
            }

            // Add validation logic for each required field
            if ($("#checkDate").val() === '') {
                isValid = false;
                highlightInvalidField($("#checkDate"));
            } else {
                resetInvalidField($("#checkDate"));
            }
            // Add validation logic for each required field
            if ($("#referenceNo").val() === '') {
                isValid = false;
                highlightInvalidField($("#referenceNo"));
            } else {
                resetInvalidField($("#referenceNo"));
            }

            if ($("#itemTableBody1 tr").length === 0 && $("#itemTableBody tr").length === 0) {
            isValid = true;
            } else {
                // Merge the iteration for both tables
            $("#itemTableBody1 tr, #itemTableBody tr").each(function() {
            var accountName = $(this).find("[name='account[]']").val();
            var memo = $(this).find("[name='memo[]']").val();
            var amount = $(this).find("[name='amount[]']").val();

            var itemName = $(this).find(".item-dropdown").val();
            var description = $(this).find(".description-field").val();
            var quantity = $(this).find("[name='quantity[]']").val();
            var rate = $(this).find(".rate-field").val();

            // Check if any of the fields is empty
            if (!accountName || !memo || !amount || !itemName || !description || !quantity || !rate) {
                isValid = true;
                return true; // Exit the loop if any field is empty
            }
            });
            return isValid;
            }
            
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
<script>

        // Function to add a new row for an item
        function addNewItemRow(itemName, description, amount, uom, items) {
            // Create a dropdown for selecting items
            var itemOptions = items.map(item =>
                `<option value="${item.itemName}" data-uom="${item.uom}" data-description="${item.itemPurchaseInfo}" data-amount="${item.itemCost}">${item.itemName} | stock (${item.itemQty})</option>`
            ).join('');


            var newRow = `<tr>
        <td>
            <select class="form-control item-dropdown" name="item[]" required>
                <option value="" selected disabled>Select an Item</option>
                ${itemOptions}
            </select>
        </td>
        <td><input type="text" class="form-control description-field" name="description[]" required readonly></td>
        <td><input type="number" class="form-control amount-input" name="quantity[]" required></td>
        <td><input type="text" class="form-control uom-field" name="uom[]" readonly></td>
        <td><input type="number" class="form-control rate-field amount-input" name="rate[]" required></td>
        <td><input type="number" class="form-control amount-field" name="amount[]" readonly></td>
        <td><button type="button" class="btn btn-danger btn-sm removeItemBtn">Remove</button></td>
    </tr>`;

            $("#itemTableBody").append(newRow);
        }

        // Event listener for adding a new item
        $("#addItemBtn").on("click", function() {
            // Get the selected item
            var selectedItem = $("#selectProduct option:selected");

            // Manually provide itemName, description, and amount
            var itemName = selectedItem.val();
            var description = selectedItem.data("description");
            var amount = selectedItem.data("amount");
            var uom = selectedItem.data("uom");
            // Call the function with the UOM options
            addNewItemRow(itemName, description, uom, amount, <?php echo json_encode($productItems); ?>);
        });

        // Event listener for removing an item
        $("#itemTableBody").on("click", ".removeItemBtn", function() {
            $(this).closest("tr").remove();
            // Recalculate gross amoun
        });
        // Event listener for updating description and amount based on the selected item
        $("#itemTableBody").on("change", ".item-dropdown", function() {
            var row = $(this).closest("tr");
            var selectedOption = $(this).find("option:selected");

            // Update the description field based on the selected item
            var description = selectedOption.data("description");
            row.find(".description-field").val(description !== undefined ? description : '');

            // Update the amount field based on the selected item
            var uom = selectedOption.data("uom");
            row.find(".uom-field").val(uom !== undefined ? uom : '');


            // Update the amount field based on the selected item
            var amount = selectedOption.data("amount");
            row.find(".rate-field").val(amount !== undefined ? amount : '');

            // Trigger the input event to recalculate the amount
            row.find("input[name='quantity[]']").trigger("input");
        });

        function formatAsPHP(number) {
        return new Intl.NumberFormat('en-PH', {
            style: 'currency',
            currency: 'PHP'
        }).format(number);
    }


        // Event listener for updating amount based on quantity and rate
        $("#itemTableBody").on("input", "input[name='quantity[]'], input[name='rate[]']", function() {
            var row = $(this).closest("tr");
            var quantity = parseFloat(row.find("input[name='quantity[]']").val()) || 0;
            var rate = parseFloat(row.find("input[name='rate[]']").val()) || 0;

            var amount = quantity * rate;
            row.find("input[name='amount[]']").val(amount); 
            
            calculateGrossAmount();
        });

</script>





