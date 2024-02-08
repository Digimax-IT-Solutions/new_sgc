
<?php foreach ($purchaseOrderItems as $item) : ?>
                                                <tr>
                                                    <td><?php echo $item['item']; ?></td>
                                                    <td><?php echo $item['description']; ?></td>
                                                    <td><?php echo $item['quantity']; ?></td>
                                                    <td><?php echo $item['uom']; ?></td>
                                                    <td><?php echo $item['rate']; ?></td>
                                                    <td><?php echo $item['amount']; ?></td>
                                                </tr>
                                            <?php endforeach; ?>









                                            <?php include __DIR__ . ('/includes/header.php'); ?>
<?php include('connect.php');

// Initialize variables
$purchaseOrder = null;
$purchaseOrderItems = [];

// Check if the 'poID' parameter is set
if (isset($_GET['poID'])) {
    $poID = $_GET['poID'];

    // Query to retrieve purchase order details
    $query = "SELECT * FROM purchase_order WHERE poID = :poID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':poID', $poID);
    $stmt->execute();

    // Fetch purchase order details
    $purchaseOrder = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if purchase order details are found
    if ($purchaseOrder) {
        // Query to retrieve purchase order items
        $queryItems = "SELECT * FROM purchase_order_items WHERE poID = :poID";
        $stmtItems = $db->prepare($queryItems);
        $stmtItems->bindParam(':poID', $poID);
        $stmtItems->execute();

        // Fetch purchase order items
        $purchaseOrderItems = $stmtItems->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // Redirect or display an error if purchase order details are not found
        header("Location: index.php"); // Redirect to the main page or display an error message
        exit();
    }
} else {
    // Redirect or display an error if 'poID' parameter is not set
    header("Location: index.php"); // Redirect to the main page or display an error message
    exit();
}


?>

<?php

// Fetch product items
$query = "SELECT itemName, itemPurchaseInfo, itemCost, uom, itemQty FROM items";
$result = $db->query($query);

$productItems = array();

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $productItems[] = $row;
}

?>



<style>
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
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-1" style="font-weight: bold; font-size:30px;">PO# :
                        <?php echo $purchaseOrder['poNo']; ?></h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Vendor Center</a></li>
                        <li class="breadcrumb-item active">Create Purchase Order</li>
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
                    <div class="card" style="background-color: #e5e5e5;">
                        <div class="card-body">
                            <!-- Sales Invoice Form -->
                            <form id="vendorForm" action="" method="POST">
                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label for="vendor">VENDOR NAME | <span><a href="vendor_list">Add new
                                                    vendor</a></span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="vendor" name="vendor" placeholder="Select vendor" value="<?php echo $purchaseOrder['vendor']; ?>">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" style="background-color: rgb(0, 149, 77); color: white; font-size: 70%;" type="button" data-toggle="modal" data-target="#vendorModal">Select</button>
                                            </div>
                                        </div>
                                        <label for="shippingAddress">SHIPPING ADDRESS</label>
                                        <input type="text" class="form-control" id="shippingAddress" name="shippingAddress" value="<?php echo $purchaseOrder['shippingAddress']; ?>">
                                        <label for="email">EMAIL</label>
                                        <input type="text" class="form-control" id="email" name="email" value="<?php echo $purchaseOrder['email']; ?>">
                                    </div>
                                    <!-- Modal for selecting existing vendors -->
                                    <div class="modal" id="vendorModal" tabindex="-1" role="dialog" aria-labelledby="vendorModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">

                                                    <h5 class="modal-title" id="vendorModalLabel">Select Vendor | <a href="vendor_list">Add new
                                                            vendor</a></h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- Place your vendor list here -->
                                                    <select class="form-control" id="existingVendor" name="existingVendor">
                                                        <option value="">Select an existing vendor</option>
                                                        <?php
                                                        // Fetch vendors from the database and populate the dropdown in the modal
                                                        $query = "SELECT vendorID, vendorName, vendorEmail, vendorAddress FROM vendors";
                                                        $result = $db->query($query);

                                                        if ($result) {
                                                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                                echo "<option value='{$row['vendorID']}' data-email='{$row['vendorEmail']}' data-shipaddress='{$row['vendorAddress']}'>{$row['vendorName']}</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary" style="background-color: rgb(0, 149, 77); color: white;" onclick="selectExistingVendor()">Select Vendor</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label for="poNo">PO #</label>
                                        <input type="text" class="form-control" id="poNo" name="poNo" value=" <?php echo $purchaseOrder['poNo']; ?>" required>
                                        <label for="poDate">PO DATE</label>
                                        <input type="date" class="form-control" id="poDate" name="poDate" value="<?php echo $purchaseOrder['poDate']; ?>" required>
                                        <label for="poDueDate">PO DUE DATE</label>
                                        <input type="date" class="form-control" id="poDueDate" name="poDueDate" value="<?php echo $purchaseOrder['poDueDate']; ?>" required>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <!-- <label for="selectAccount">SELECT ACCOUNT | <span><a
                                                    href="chart_of_accounts">Add New Account</a></span></label>
                                        <select class="form-control" id="selectAccount" name="selectAccount" required>
                                            <option value="">Select an Account</option>
                                          
                                        </select> -->

                                        <!-- PAYMENT METHOD -->
                                        <label for="paymentMethod">PAYMENT METHOD | <span><a href="payment_method_list">Add
                                                    New
                                                    Payment</a></span></label>
                                        <select class="form-control" id="paymentMethod" name="paymentMethod" required>
                                            <option value="<?php echo $purchaseOrder['paymentMethod']; ?>">
                                                <?php echo $purchaseOrder['paymentMethod']; ?></option>
                                            <?php
                                            $query = "SELECT payment_id, payment_name FROM payment_methods";
                                            $result = $db->query($query);

                                            if ($result) {
                                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<option value='{$row['payment_id']}'>{$row['payment_name']}</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                        <!-- LOCATION -->
                                        <label for="location">LOCATION | <span><a href="location_list">Add New
                                                    Location</a></span></label>
                                        <select class="form-control" id="location" name="location" required>
                                            <option value="<?php echo $purchaseOrder['location']; ?>">
                                                <?php echo $purchaseOrder['location']; ?></option>
                                            <?php
                                            $query = "SELECT location_id, location_name FROM locations";
                                            $result = $db->query($query);

                                            if ($result) {
                                                while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<option value='{$row['location_name']}'>{$row['location_name']}</option>";
                                                }
                                            }
                                            ?>
                                        </select>
                                        <br>
                                        <!-- <div class="form-group col-md-6">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox"
                                                    class="custom-control-input custom-control-input-green"
                                                    id="accountTypeSwitch" checked>
                                                <label class="custom-control-label custom-control-label-green"
                                                    for="accountTypeSwitch">CASH SALES</label>
                                            </div>
                                        </div> -->
                                    </div>

                                    <!-- Terms -->
                                    <div class="form-group col-md-2">
                                        <label for="terms">TERMS | <span><a href="terms_list">Add New
                                                    Terms</a></span></label>
                                        <select class="form-control" id="terms" name="terms" required>
                                            <option value="<?php echo $purchaseOrder['terms']; ?>">
                                                <?php echo $purchaseOrder['terms']; ?></option>
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
                                    <div class="form-group col-md-2">
                                        <label for="memo">MEMO:</label>
                                        <textarea class="form-control" id="memo" name="memo" rows="3" cols="50" value="<?php echo $purchaseOrder['memo']; ?>"><?php echo $purchaseOrder['memo']; ?></textarea>
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
                                            <!-- GORSS AMOUNT -->
                                            <div class="row">

                                                <div class="col-md-4 d-inline-block text-right">
                                                    <label>Gross Amount:</label>
                                                </div>
                                                <div class="col-md-3 d-inline-block">

                                                </div>
                                                <div class="col-md-5 d-inline-block">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">&#8369;</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="grossAmount" id="grossAmount" readonly value="<?php echo $purchaseOrder['grossAmount']; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- DISCOUNT -->
                                            <div class="row">
                                                <div class="col-md-4 d-inline-block text-right">
                                                    <label for="discountPercentage">Discount
                                                        (%):</label>
                                                </div>
                                                <div class="col-md-3 d-inline-block">
                                                    <input type="number" class="form-control" name="discountPercentage" id="discountPercentage" placeholder="Enter %">
                                                </div>
                                                <div class="col-md-5 d-inline-block">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">&#8369;</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="discountAmount" id="discountAmount" readonly value="">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- NET AMOUND DUE -->
                                            <div class="row">
                                                <div class="col-md-4 d-inline-block text-right">
                                                    <label>Net Amount Due:</label>
                                                </div>
                                                <div class="col-md-3 d-inline-block">

                                                </div>
                                                <div class="col-md-5 d-inline-block">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">&#8369;</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="netAmountDue" id="netAmountDue" readonly value="<?php echo $purchaseOrder['netAmountDue']; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- VAT PERCENTAGE -->
                                            <div class="row">
                                                <div class="col-md-4 d-inline-block text-right">
                                                    <label for="vatPercentage">VAT (%):</label>
                                                </div>
                                                <div class="col-md-3 d-inline-block">
                                                    <select class="form-control" id="vatPercentage" name="vatPercentage" required>
                                                        <?php
                                                        $query = "SELECT salesTaxRate, salesTaxName FROM sales_tax";
                                                        $result = $db->query($query);

                                                        if ($result) {
                                                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                                echo "<option value='{$row['salesTaxRate']}'>{$row['salesTaxName']}</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-5 d-inline-block">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">&#8369;</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="vatPercentageAmount" id="vatPercentageAmount" readonly value="<?php echo $purchaseOrder['vatPercentage']; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- NET OF VAT -->
                                            <div class="row">
                                                <div class="col-md-4 d-inline-block text-right">
                                                    <label>Net of VAT:</label>
                                                </div>
                                                <div class="col-md-3 d-inline-block">

                                                </div>
                                                <div class="col-md-5 d-inline-block">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">&#8369;</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="netOfVat" id="netOfVat" readonly value="<?php echo $purchaseOrder['netOfVat']; ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <br>
                                            <!-- GROSS AMOUNT -->
                                            <div class="row" style="font-size: 30px">
                                                <div class="col-md-6 d-inline-block text-right">
                                                    <label>Total Amount Due:</label>
                                                </div>
                                                <div class="col-md-6 d-inline-block">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">&#8369;</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="totalAmountDue" id="totalAmountDue" value="<?php echo $purchaseOrder['totalAmountDue']; ?>" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <!-- Submit Button -->
                                <div class="row">
                                    <div class="col-md-10 d-inline-block">
                                        <button type="button" class="btn btn-primary" id="saveInvoiceButton">Save</button>
                                        <button type="button" class="btn btn-success" id="saveAndNewButton">Save and
                                            New</button>
                                        <button type="button" class="btn btn-info" id="saveAndCloseButton">Save and
                                            Close</button>
                                        <button type="button" class="btn btn-warning" id="clearButton">Clear</button>
                                        <button type="button" class="btn btn-danger" id="deleteButton">Delete</button>
                                        <button type="button" class="btn btn-secondary" id="printButton">Print</button>
                                    </div>
                                </div>


                            </form>
                            <!-- End Sales Invoice Form -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include('includes/footer.php'); ?>
</div>


<script>
    $(document).ready(function() {
        // Function to add a new row for an item
        function addNewItemRow(itemName, description, uom, rate, amount, items) {
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
                <td><input type="number" class="form-control" name="quantity[]" required></td>
                <td><input type="text" class="form-control uom-field" name="uom[]" readonly></td>
                <td><input type="number" class="form-control rate-field" name="rate[]" required></td>
                <td><input type="number" class="form-control amount-field" name="amount[]" readonly></td>
                <td><button type="button" class="btn btn-danger btn-sm removeItemBtn">Remove</button></td>
            </tr>`;

            $("#itemTableBody").append(newRow);
        }

        // Function to populate existing items
        function populateExistingItems(items) {
            items.forEach(item => {
                addNewItemRow(item.itemName, item.itemPurchaseInfo, item.uom, item.itemCost, 0, <?php echo json_encode($productItems); ?>);
            });
        }

        // Populate existing items when the page loads
        populateExistingItems(<?php echo json_encode($purchaseOrderItems); ?>);

        // Function to update row fields based on the selected item
        function updateRowFields(row, selectedOption) {
            var description = selectedOption.data("description");
            row.find(".description-field").val(description !== undefined ? description : '');

            var uom = selectedOption.data("uom");
            row.find(".uom-field").val(uom !== undefined ? uom : '');

            var amount = selectedOption.data("amount");
            row.find(".rate-field").val(amount !== undefined ? amount : '');

            row.find("input[name='quantity[]']").trigger("input");
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

            // Check if the items array is defined
            var itemsArray = <?php echo json_encode($productItems); ?>;
            if (itemsArray && Array.isArray(itemsArray)) {
                // Call the function with the UOM options
                addNewItemRow(itemName, description, uom, amount, itemsArray);
            } else {
                console.error("Items array is not defined or not an array.");
            }
        });

        // Event listener for removing an item
        $("#itemTableBody").on("click", ".removeItemBtn", function() {
            $(this).closest("tr").remove();
            calculateGrossAmount();
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
            $("#netAmountDue").val(netAmountDue.toFixed(2));
            $("#discountAmount").val(discountAmount.toFixed(2));
            var vatPercentageAmount = (vatPercentage / 100) * netAmountDue;
            var netOfVat = netAmountDue / (1 + vatPercentage / 100);
            $("#vatPercentageAmount").val(vatPercentageAmount.toFixed(2));
            $("#netOfVat").val(netOfVat.toFixed(2));
            var taxWitheldAmount = (taxWithheldPercentage / 100) * netOfVat;
            $("#taxWitheldAmount").val(taxWitheldAmount.toFixed(2));
            var totalAmountDue = netAmountDue - taxWitheldAmount;
            $("#totalAmountDue").val(totalAmountDue.toFixed(2));
        }

        // Event listener for updating percentages
        $("#discountPercentage, #vatPercentage, #taxWithheldPercentage").on("input", function() {
            calculatePercentages();
        });

    });
</script>