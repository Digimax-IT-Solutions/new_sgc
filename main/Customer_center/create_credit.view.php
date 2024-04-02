<?php
include __DIR__ . ('../../includes/header.php');
include ('connect.php');

// Fetch product items
$query = "SELECT itemName, itemSalesInfo, itemSrp, uom FROM items";

// Execute the query
$result = $db->query($query);

// Initialize an array to store the product items
$productItems = array();

// Fetch rows one by one
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    // Append each row to the product items array
    $productItems[] = $row;
}

// Convert the result to JSON
$productItemsJSON = json_encode($productItems);
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

    .select2 {
        text-align: left;
        padding-top: 3.1px;
    }
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Credit Memo</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Customer Center</a></li>
                        <li class="breadcrumb-item active">Credit Memo</a></li>
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
                            <form id="addCreditForm" action="" method="POST">
                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <?php
                                        // Assuming $db is your PDO database connection
                                        $customerQuery = "SELECT * FROM customers";

                                        try {
                                            $customerStmt = $db->prepare($customerQuery);
                                            $customerStmt->execute();

                                            $customers = $customerStmt->fetchAll(PDO::FETCH_ASSOC);

                                            echo "<label for='customerName'>Customer Name</label>";
                                            echo "<select name='customerName' id='customerName' class='form-control'>";
                                            echo "<option value='' disabled selected>Select Customer:</option>";
                                            foreach ($customers as $customer) {
                                                echo "<option value='" . $customer['customerName'] . "'>" . $customer['customerName'] . "</option>";
                                            }
                                            echo "</select>";
                                        } catch (PDOException $e) {
                                            // Handle the exception, log the error, or return an error message with MySQL error information
                                            $errorInfo = $customerStmt->errorInfo();
                                            $errorMessage = "Error fetching customers: " . $errorInfo[2]; // MySQL error message
                                            echo "<option value=''>$errorMessage</option>";
                                        }
                                        ?>

                                        <label for="creditID">Credit No</label>
                                        <input type="text" class="form-control" id="creditID" name="creditID">
                                    </div>
                                    <div class="form-group col-md-2 offset-md-2">
                                        <!-- Empty div with offset -->
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label for="creditDate">DATE</label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="creditDate" name="creditDate"
                                                required>
                                        </div>
                                        <label for="poID">PO No</label>
                                        <input type="text" class="form-control" id="poID" name="poID">
                                        <label for="creditBalance" hidden>Total Amount</label>
                                        <div class="input-group" hidden>
                                            <input type="number" class="form-control" id="creditBalance"
                                                name="creditBalance" readonly>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="memo">Memo:</label>
                                        <textarea name="memo" id="memo" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-12">
                                        <button type="button" class="btn btn-success" id="addItemBtn">Add Item</button>
                                    </div>
                                </div>
                                <!-- Select Product Item -->
                                <div class="table-responsive">
                                    <table class="table table-condensed" id="itemTable">
                                        <thead>
                                            <tr>
                                                <th>ITEM</th>
                                                <th>DESCRIPTION</th>
                                                <th>QTY</th>
                                                <th>UOM</th>
                                                <th>RATE</th>
                                                <th>AMOUNT</th>
                                                <th>ACTION</th>
                                            </tr>
                                        </thead>
                                        <tbody id="itemTableBody">
                                            <!-- Each row represents a separate item -->
                                            <!-- You can dynamically add rows using JavaScript/jQuery -->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="modal-footer">
                                    <div class="summary-details">
                                        <div class="container">
                                            <div class="row">
                                                <div class="col-md-4 d-inline-block text-right">
                                                    <label for="vatPercentage">CUSTOMER TAX CODE:</label>
                                                </div>
                                                <div class="col-md-3 d-inline-block">
                                                    <select class="form-control" id="taxTypeSelect">
                                                        <option value="tax" selected disabled>Choose Tax Code</option>
                                                        <option value="tax">Taxable Sales</option>
                                                        <option value="non-tax">Non-Taxable Sales</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <br>

                                            <div class="row">
                                                <div class="col-md-4 d-inline-block text-right">
                                                    <label for="vatPercentage">TAX:</label>
                                                </div>
                                                <div class="col-md-3 d-inline-block">
                                                    <?php
                                                    $query = "SELECT salesTaxID, salesTaxRate, salesTaxName FROM sales_tax";
                                                    $result = $db->query($query);
                                                    echo "<select class='form-control' id='vatPercentage' name='vatPercentage'>";
                                                    echo "<option value='' selected disabled>Choose Tax</option>";

                                                    if ($result) {
                                                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                            // Construct option text with a unique identifier (salesTaxName)
                                                            $optionText = "{$row['salesTaxName']}";

                                                            // Check if salesTaxRate is 0.00 and salesTaxName is "Zero 0%", if so, mark it as selected
                                                            $selected = ($row['salesTaxRate'] == 12 && $row['salesTaxName'] == "12%") ? 'selected' : '';

                                                            echo "<option value='{$row['salesTaxRate']}' data-id='{$row['salesTaxID']}' $selected>{$optionText}</option>";
                                                        }
                                                    }

                                                    echo "</select>";
                                                    ?>
                                                </div>
                                                <div class="col-md-5 d-inline-block">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">&#8369;</span>
                                                        </div>
                                                        <input type="text" class="form-control"
                                                            name="vatPercentageAmount" id="vatPercentageAmount"
                                                            readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <br>

                                            <div class="row" style="font-size: 30px">
                                                <div class="col-md-6 d-inline-block text-right">
                                                    <label>Total Amount Due:</label>
                                                </div>
                                                <div class="col-md-6 d-inline-block">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">&#8369;</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="creditAmount"
                                                            id="creditAmount" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <center><button type="button" class="btn btn-primary" id="saveButton">Submit</button>
                                </center>
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
    // Use the fetched data in JavaScript
    var productItems = <?php echo $productItemsJSON; ?>;
</script>
<script>
    $(document).ready(function () {
        var itemCount = 0; // Variable to keep track of the count of added items
        var maxItems = 17; // Maximum number of items allowed

        function addNewItemRow(itemName, description, uom, amount, items, maxItems) {
            if (itemCount >= maxItems) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'You can only add up to ' + maxItems + ' items. Please create a new invoice for additional items.'
                });
                return; // Exit the function if the limit is reached
            }

            // Create a dropdown for selecting items
            var itemOptions = items.map(item =>
                `<option value="${item.itemName}" data-uom="${item.uom}" data-description="${item.itemSalesInfo}" data-amount="${item.itemCost}">${item.itemName}</option>`
            ).join('');

            var newRow = `<tr>
        <td>
            <select class="item-dropdown select2" style="width: 400px;" name="item[]" required>
                <option value="" selected disabled>Select an Item</option>
                ${itemOptions}
            </select>
        </td>
        <td><input type="text" class="form-control description-field" name="description[]" required></td>
        <td><input type="number" class="form-control" name="quantity[]" required></td>
        <td><input type="text" class="form-control uom-field" name="uom[]" readonly></td>
        <td><input type="number" class="form-control rate-field" name="rate[]" required></td>
        <td><input type="number" class="form-control amount-field" name="amount[]" readonly></td>
        <td><button type="button" class="btn btn-danger btn-sm removeItemBtn">Remove</button></td>
    </tr>`;

            $("#itemTableBody").append(newRow);
            $('.item-dropdown').last().select2({
                placeholder: "Search for an item",
                minimumInputLength: 1 // Minimum characters to start searching
            });

            itemCount++; // Increment the count of added items
        }

        // Event listener for adding a new item
        $("#addItemBtn").on("click", function () {
            // Get the selected item
            var selectedItem = $("#selectProduct option:selected");

            // Manually provide itemName, description, and amount
            var itemName = selectedItem.val();
            var description = selectedItem.data("description");
            var amount = selectedItem.data("amount");
            var uom = selectedItem.data("uom");

            // Call the function with the UOM options and maxItems
            addNewItemRow(itemName, description, uom, amount, <?php echo json_encode($productItems); ?>, maxItems);
        });

        // Event listener for removing an item
        $("#itemTableBody").on("click", ".removeItemBtn", function () {
            $(this).closest("tr").remove();

            // Decrement itemCount when an item is removed
            itemCount--;

            // Recalculate gross amount
            calculateGrossAmount();
            // Recalculate other percentages
            calculatePercentages();
        });

        // Event listener for updating description and amount based on the selected item
        $("#itemTableBody").on("change", ".item-dropdown", function () {
            var row = $(this).closest("tr");
            var selectedOption = $(this).find("option:selected");

            // Update the description field based on the selected item
            var description = selectedOption.data("description");
            row.find(".description-field").val(description !== undefined ? description : '');

            // Update the amount field based on the selected item
            var amount = selectedOption.data("amount");
            row.find(".rate-field").val(amount !== undefined ? amount : '');


            // Update the amount field based on the selected item
            var uom = selectedOption.data("uom");
            row.find(".uom-field").val(uom !== undefined ? uom : '');

            // Trigger the input event to recalculate the amount
            row.find("input[name='quantity[]']").trigger("input");
        });

        // Function to format number as Philippine Peso (PHP)
        function formatAsPHP(number) {
            return new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP'
            }).format(number);
        }


        // Event listener for updating amount based on quantity and rate
        $("#itemTableBody").on("input", "input[name='quantity[]'], input[name='rate[]']", function () {
            var row = $(this).closest("tr");
            var quantity = parseFloat(row.find("input[name='quantity[]']").val()) || 0;
            var rate = parseFloat(row.find("input[name='rate[]']").val()) || 0;

            var amount = quantity * rate;
            row.find("input[name='amount[]']").val(amount);

            // Recalculate gross amount
            calculateGrossAmount();
            // Recalculate other percentages
            calculatePercentages();
        });

        // Function to calculate the gross amount
        function calculateGrossAmount() {
            var grossAmount = 0;
            $("input[name='amount[]']").each(function () {
                grossAmount += parseFloat($(this).val()) || 0;
            });
            $("#grossAmount").val(grossAmount.toFixed(2));
        }
        // Function to calculate other percentages
        function calculatePercentages() {
            var grossAmount = 0;
            $("input[name='amount[]']").each(function () {
                grossAmount += parseFloat($(this).val()) || 0;
            });

            // Check if the #vatPercentage select is enabled
            if ($("#vatPercentage").prop("disabled")) {
                // If disabled, set vatPercentageAmount and totalAmountDue to 0
                var vatPercentageAmount = 0;
                var totalAmountDue = grossAmount;
            } else {
                // If enabled, proceed with the calculations
                var vatPercentage = parseFloat($("#vatPercentage").val()) || 0;

                // Calculate vatPercentageAmount
                var vatPercentageAmount = grossAmount / 100 * vatPercentage;
            }

            // Update the #vatPercentageAmount and #creditAmount fields
            $("#vatPercentageAmount").val(vatPercentageAmount.toFixed(2));
            var totalAmountDue = grossAmount + vatPercentageAmount;
            $("#creditAmount").val(totalAmountDue.toFixed(2));
            $("#creditBalance").val(totalAmountDue.toFixed(2));
        }

        // Event listener for updating percentages
        $("#vatPercentage").on("input", function () {
            calculatePercentages();
        });

    });
</script>
<script>
    $(document).ready(function () {
        $('.select2').select2();

        $('#vatPercentage').prop('disabled', true);
        // Change event for the taxTypeSelect
        $('#taxTypeSelect').on('change', function () {
            var selectedOption = $(this).val();

            // If "Non-Taxable Sales" is selected, disable the vatPercentage select; otherwise, enable it
            if (selectedOption === 'non-tax') {
                $('#vatPercentage').prop('disabled', true);
            } else {
                $('#vatPercentage').prop('disabled', false);
            }
        });

    });
</script>
<script>
    $(document).ready(function () {
        function isFormValid() {
            var isValid = true;

            if ($("#creditID").val() === '') {
                isValid = false;
                highlightInvalidField($("#creditID"));
            } else {
                resetInvalidField($("#creditID"));
            }

            if ($("#poID").val() === '') {
                isValid = false;
                highlightInvalidField($("#poID"));
            } else {
                resetInvalidField($("#poID"));
            }

            if ($("#creditDate").val() === '') {
                isValid = false;
                highlightInvalidField($("#creditDate"));
            } else {
                resetInvalidField($("#creditDate"));
            }

            var customerName = $('#customerName').val();
            if (customerName === null || customerName === '') {
                isValid = false;
                highlightInvalidField($("#customerName"));
            } else {
                resetInvalidField($("#customerName"));
            }

            return isValid;
        }

        function highlightInvalidField(field) {
            field.addClass("is-invalid");
        }

        // Function to reset the highlighting of an invalid field
        function resetInvalidField(field) {
            field.removeClass("is-invalid");
        }

        // Click event for the saveButton
        $("#saveButton").click(function () {

            // Call isFormValid function
            if (!isFormValid()) {
                // Show SweetAlert error if the form is not valid
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Please fill in all required fields.', // Customize the error message
                });
                return;
            }

            if ($("#itemTableBody tr").length === 0) {
                // Show SweetAlert error if no items are added
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Please add at least one item.', // Customize the error message
                });
                return;
            }
            // Proceed with AJAX call if form is valid
            $.ajax({
                type: "POST",
                url: "modules/credit/save_credit.php",
                data: $("#addCreditForm").serialize(),
                success: function (response) {
                    if (response === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'New Credit Memo Added!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function () {
                            window.location.href = 'receive_payments';
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response,
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An unexpected error occurred. Please try again.',
                    });
                }
            });

            // Disable selected option after validation
            var customerName = $('#customerName').val();
            $('#customerName option[value="' + customerName + '"]').prop('disabled', true);
        });

        // Input event for the creditAmount field
        $('#creditAmount').on('input', function () {
            const creditAmount = parseFloat($(this).val());
            $('#creditBalance').val(creditAmount.toFixed(2)); // Update the creditBalance field
        });
    });
</script>