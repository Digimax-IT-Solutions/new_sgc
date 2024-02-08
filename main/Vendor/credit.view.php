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
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Enter Bills - Debit Memo</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Vendor Center</a></li>
                        <li class="breadcrumb-item"><a href="write_check">Enter Bills</a></li>
                        <li class="breadcrumb-item active">Credit</li>
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
                            <form action="process_write_check.php" method="post">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="payee_type">Vendor</label>
                                        <select class="form-control" id="" name="">
                                            <option value="" selected disabled>Select vendor</option>
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
                                    </div>

                                    <div class="form-group col-md-2 offset-md-2">
                                        <!-- Empty div with offset -->
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label for="receiveItemDate">DATE</label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="receiveItemDate" name="receiveItemDate">
                                        </div>
                                        <label for="refNo">REFERENCE NO.</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="refNo" name="refNo" placeholder="REFERENCE NO">
                                        </div>
                                        <label for="total">Credit Amount</label>
                                        <div class="total" style="font-size: 30px;">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="total" name="total" value="">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="memo">Memo:</label>
                                        <textarea name="memo" id="memo" class="form-control" rows="3"></textarea>
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
                                    <div class="tab-pane fade show active" id="expenses" role="tabpanel">

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
                                        <button type="button" class="btn btn-success" id="addItemBtnn">Add
                                            Expense</button>
                                    </div>

                                    <!-- Items Tab -->
                                    <div class="tab-pane fade" id="items" role="tabpanel">

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

                                <center><button type="submit" class="btn btn-primary">Submit</button></center>
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
    $(document).ready(function() {
        var $chartOfAccount = <?php echo json_encode($chartOfAccount); ?>;

        function addNewItemRow(accountName) {
            // Filter the corresponding account details based on the selected accountName
            var selectedAccount = $chartOfAccount.find(account => account.account_name === accountName);

            // Create a dropdown for selecting items
            var itemOptions = $chartOfAccount.map(item =>
                `<option value="${item.account_name}">${item.account_name}</option>`
            ).join('');

            var newRow = `<tr>
            <td>
                <select class="form-control item-dropdown" required>
                    <option value="" selected disabled>Select an Account</option>
                    ${itemOptions}
                </select>
            </td>
            <td><input type="text" class="form-control description-field" required></td>
            <td><input type="number" class="form-control" required></td>
            <td><button type="button" class="btn btn-danger btn-sm removeItemBtn">Remove</button></td>
        </tr>`;

            $("#itemTableBody1").append(newRow);
        }

        // Event listener for adding a new item
        $("#addItemBtnn").on("click", function() {
            // Call the function without any argument for now
            addNewItemRow();
        });

        $("#itemTableBody1").on("click", ".removeItemBtn", function() {
            $(this).closest("tr").remove();
        });
    });
</script>
<script>
    $(document).ready(function() {
        function addNewItemRow(itemName, description, amount, uom, items) {
            // Create a dropdown for selecting items
            var itemOptions = items.map(item =>
                `<option value="${item.itemName}" data-uom="${item.uom}" data-description="${item.itemPurchaseInfo}" data-amount="${item.itemCost}">${item.itemName}</option>`
            ).join('');


            var newRow = `<tr>
        <td>
            <select class="form-control item-dropdown" name="item[]" required>
                <option value="" selected disabled>Select an Item</option>
                ${itemOptions}
            </select>
        </td>
        <td><input type="text" class="form-control description-field" name="description[]" required readonly></td>
        <td><input type="number" class="form-control" name="quantity[]" required></td>
        <td><input type="text" class="form-control uom-field" name="uom[]" readonly></td>
        <td><input type="number" class="form-control rate-field" name="rate[]" required></td>
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
            // Recalculate gross amount
            calculateGrossAmount();
            // Recalculate other percentages
            calculatePercentages();
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

        // Function to format number as Philippine Peso (PHP)
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

            // Recalculate gross amount
            calculateGrossAmount();
            // Recalculate other percentages
            calculatePercentages();
        });

        // Function to calculate the gross amount
        function calculateGrossAmount() {
            var grossAmount = 0;
            $("input[name='amount[]']").each(function() {
                grossAmount += parseFloat($(this).val()) || 0;
            });
            $("#grossAmount").val(grossAmount.toFixed(2));
        }
        // Function to calculate other percentages
        function calculatePercentages() {
            var grossAmount = parseFloat($("#grossAmount").val()) || 0;
            var discountPercentage = parseFloat($("#discountPercentage").val()) || 0;
            var vatPercentage = parseFloat($("#vatPercentage").val()) || 0;
            var taxWithheldPercentage = parseFloat($("#taxWithheldPercentage").val()) || 0;

            // FOOTER CALCULATION
            var discountAmount = (discountPercentage / 100) * grossAmount;
            var netAmountDue = grossAmount - discountAmount;
            $("#netAmountDue").val((netAmountDue.toFixed(2)));
            $("#discountAmount").val((discountAmount.toFixed(2)));
            var vatPercentageAmount = (vatPercentage / 100) * netAmountDue;
            var netOfVat = netAmountDue / (1 + vatPercentage / 100);
            $("#vatPercentageAmount").val((vatPercentageAmount.toFixed(2)));
            $("#netOfVat").val((netOfVat.toFixed(2)));
            var taxWitheldAmount = (taxWithheldPercentage / 100) * netOfVat;
            $("#taxWitheldAmount").val((taxWitheldAmount.toFixed(2)));
            var totalAmountDue = netAmountDue - taxWitheldAmount;
            $("#totalAmountDue").val((totalAmountDue.toFixed(2)));
        }

        // Event listener for updating percentages
        $("#discountPercentage, #vatPercentage, #taxWithheldPercentage").on("input", function() {
            calculatePercentages();
        });

    });
</script>