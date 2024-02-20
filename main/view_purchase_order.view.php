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
    $purchaseOrderStatus = $purchaseOrder['poStatus'];
    // Check if purchase order details are found
    if ($purchaseOrder) {
        // Fetch purchase order items
        $queryOrderItems = "SELECT * FROM purchase_order_items WHERE poID = :poID";
        $stmtOrderItems = $db->prepare($queryOrderItems);
        $stmtOrderItems->bindParam(':poID', $poID);
        $stmtOrderItems->execute();

        $purchaseOrderItems = $stmtOrderItems->fetchAll(PDO::FETCH_ASSOC);
            // Check the status of the purchase order
    $purchaseOrderStatus = $purchaseOrder['poStatus'];
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
    .select2 {
        text-align: left;
        padding-top: 3.1px;
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
                            <form id="vendorForm" method="POST">
                                <input type="text" class="form-control" name="poID" value="<?php echo $purchaseOrder['poID']; ?>" hidden>
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
                                        <input type="text" class="form-control" id="poNo" name="poNo" value="<?php echo $purchaseOrder['poNo']; ?>" required>
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
                                                    echo "<option value='{$row['payment_name']}'>{$row['payment_name']}</option>";
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
                                    <div class="form-group col-md-4">
                                        <label for="memo">MEMO:</label>
                                        <textarea class="form-control" id="memo" name="memo" rows="3" cols="50" value="<?php echo $purchaseOrder['memo']; ?>"><?php echo $purchaseOrder['memo']; ?></textarea>
                                        <br><br>
                                        <div class="form-group">
    <?php
        $status = $purchaseOrder['poStatus'];
        $statusColor = ($status === 'RECEIVED') ? 'green' : 'red';
        echo "<h2 style='color: {$statusColor}'>{$status}</h2>";
    ?>
</div>
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
                                                    <input type="number" class="form-control" name="discountPercentage" id="discountPercentage" value="<?php echo $purchaseOrder['discountPercentage']; ?>" placeholder="Enter %">
                                                </div>
                                                <div class="col-md-5 d-inline-block">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">&#8369;</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="discountAmount" id="discountAmount" readonly>
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



<!-- DATE -->
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
                var currentDate = new Date($("#poDate").val());
                currentDate.setDate(currentDate.getDate() + parseInt(daysDue));
                setFormattedDate($("#poDueDate"), currentDate);
            } else {
                $("#poDueDate").val("");
            }
        }

        // Initialize receiveItemDate with the current date
        var currentDate = new Date();
        setFormattedDate($("#poDate"), currentDate);

        // Event listener for when the receiveItemDate input changes
        $("#poDate").on("change", function() {
            updateDueDate();
        });

        // Event listener for when the terms dropdown changes
        $("#terms").on("change", function() {
            updateDueDate();
        });

    });
</script>
<!-- SELECT vendor -->
<script>
    $(document).ready(function() {
        // Attach a click event handler to the clear button
        $("#clearButton").on("click", function() {
            // Reset all form fields to their default values
            $("#vendorForm")[0].reset();

            // Additional steps may be required depending on your specific form elements
            // Clearing the item table body
            $("#itemTableBody").empty();

            // Reset calculated fields
            $("#grossAmount, #discountAmount, #netAmountDue, #vatPercentageAmount, #netOfVat, #totalAmountDue")
                .val('');

            // Optionally, you can set default values for specific fields if needed
            // $("#vatPercentage").val('<?php echo $purchaseOrder['vatPercentage']; ?>');
            // $("#paymentMethod, #location, #terms").val('<?php echo $purchaseOrder['paymentMethod']; ?>');



            // Recalculate gross amount
            calculateGrossAmount();
            // Recalculate other percentages
            calculatePercentages();
        });

        // Attach a click event handler to the delete button
// Attach a click event handler to the delete button
$("#deleteButton").on("click", function() {
    // Assuming you have the poID and purchaseOrderStatus available
    var poID = <?php echo $purchaseOrder['poID']; ?>;
    var purchaseOrderStatus = '<?php echo $purchaseOrderStatus; ?>';

    // Check if the status is 'RECEIVED', and if so, prevent the deletion
    if (purchaseOrderStatus === 'RECEIVED') {
        Swal.fire({
            icon: 'error',
            title: 'Error!',
            text: 'Cannot delete received purchase orders',
        });
    } else {
        // Show a confirmation prompt before proceeding
        Swal.fire({
            icon: 'warning',
            title: 'Are you sure?',
            text: 'This action cannot be undone.',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            // Proceed with deletion if the user confirms
            if (result.isConfirmed) {
                // Use AJAX to send a request to delete_purchase_order.php
                $.ajax({
                    url: 'modules/vendor_center/purchase_order/delete_purchase_order.php',
                    method: 'POST',
                    data: {
                        poID: poID
                    },
                    dataType: 'json', // Expect JSON response
                    success: function(response) {
                        // Handle the success response using SweetAlert or any other method
                        if (response.status === "success") {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message,
                            }).then((result) => {
                                // Redirect to the purchase_order page after clicking OK
                                if (result.isConfirmed) {
                                    window.location.href = 'purchase_order';
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: response.message,
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle the error response using SweetAlert or any other method
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'Error deleting Purchase Order. Please try again.',
                        });
                        console.error('Error deleting Purchase Order: ' + error);
                    }
                });
            }
        });
    }
});

        $("#saveAndNewButton").on("click", function() {
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

            // Perform any client-side validation if needed

            // Use AJAX to send form data to the server-side PHP script
            $.ajax({
                url: 'modules/vendor_center/purchase_order/update_purchase_order.php',
                method: 'POST',
                data: $("#vendorForm").serialize(),
                success: function(response) {
                    // Handle the success response using SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response,
                    }).then((result) => {
                        // Redirect to the sales_invoice page after clicking OK
                        if (result.isConfirmed) {
                            window.location.href = 'create_purchase_order';
                        }
                    });
                },
                error: function(xhr, status, error) {
                    // Handle the error response using SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Error update PO. Please try again.',
                    });
                    console.error('Error updating PO: ' + error);
                }
            });
        });
        $("#saveAndCloseButton").on("click", function() {
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

            // Perform any client-side validation if needed
            // Use AJAX to send form data to the server-side PHP script
            $.ajax({
                url: 'modules/vendor_center/purchase_order/update_purchase_order.php',
                method: 'POST',
                data: $("#vendorForm").serialize(),
                dataType: 'json', // Expect JSON response from the server
                success: function(response) {
                    if (response.status === 'success') {
                        // Handle the success response using SweetAlert
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                        }).then((result) => {
                            // Redirect to the purchase_order page after clicking OK
                            if (result.isConfirmed) {
                                window.location.href = 'purchase_order';
                            }
                        });
                    } else {
                        // Handle the error response using SweetAlert
                        Swal.fire({
                            icon: 'error',
                            title: 'Check PO #!',
                            text: '' + response.message,
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Handle the error response using SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Error updating PO. Please try again.',
                    });
                    console.error('Error updating PO: ' + error);
                }
            });
        });
        // Function to validate the form
        function isFormValid() {
            var isValid = true;

            // Add validation logic for each required field
            if ($("#invoiceNo").val() === '') {
                isValid = false;
                highlightInvalidField($("#invoiceNo"));
            } else {
                resetInvalidField($("#invoiceNo"));
            }

            // Add validation logic for each required field
            if ($("#invoiceDate").val() === '') {
                isValid = false;
                highlightInvalidField($("#invoiceDate"));
            } else {
                resetInvalidField($("#invoiceDate"));
            }

            // Add validation logic for each required field
            if ($("#invoiceDueDate").val() === '') {
                isValid = false;
                highlightInvalidField($("#invoiceDueDate"));
            } else {
                resetInvalidField($("#invoiceDueDate"));
            }

            // Add validation logic for each required field
            if ($("#vendor").val() === '') {
                isValid = false;
                highlightInvalidField($("#vendor"));
            } else {
                resetInvalidField($("#vendor"));
            }
            // Add validation logic for each required field
            if ($("#address").val() === '') {
                isValid = false;
                highlightInvalidField($("#address"));
            } else {
                resetInvalidField($("#address"));
            }
            // Add validation logic for each required field
            if ($("#shippingAddress").val() === '') {
                isValid = false;
                highlightInvalidField($("#shippingAddress"));
            } else {
                resetInvalidField($("#shippingAddress"));
            }
            // Add validation logic for each required field
            if ($("#email").val() === '') {
                isValid = false;
                highlightInvalidField($("#email"));
            } else {
                resetInvalidField($("#email"));
            }
            // Add validation logic for each required field
            if ($("#selectAccount").val() === '') {
                isValid = false;
                highlightInvalidField($("#selectAccount"));
            } else {
                resetInvalidField($("#selectAccount"));
            }

            // Add validation logic for each required field
            if ($("#terms").val() === '') {
                isValid = false;
                highlightInvalidField($("#terms"));
            } else {
                resetInvalidField($("#terms"));
            }

            // Add validation logic for each required field
            if ($("#location").val() === '') {
                isValid = false;
                highlightInvalidField($("#location"));
            } else {
                resetInvalidField($("#location"));
            }

            // Add validation logic for each required field
            if ($("#paymentMethod").val() === '') {
                isValid = false;
                highlightInvalidField($("#paymentMethod"));
            } else {
                resetInvalidField($("#paymentMethod"));
            }

            var isValid = true;

            if ($("#itemTableBody tr").length === 0) {
                isValid = false;
            } else {
                // Iterate through each row
                $("#itemTableBody tr").each(function() {
                    var itemName = $(this).find(".item-dropdown").val();
                    var description = $(this).find(".description-field").val();
                    var quantity = $(this).find("[name='quantity[]']").val();

                    var rate = $(this).find(".rate-field").val();

                    // Check if any of the fields is empty
                    if (!itemName || !description || !quantity || !rate) {
                        isValid = false;
                        return false; // Exit the loop if any field is empty
                    }
                });
            }

            // if (!isValid) {
            //     // Display an error message or take appropriate action
            //     alert("Please fill in all required fields for items.");
            // } else {
            //     // Continue with further processing or submission
            // }
            // Add more validation checks for other fields as needed

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



        // // Get the current date
        // var currentDate = new Date();

        // // Format the date as "YYYY-MM-DD" (compatible with the "date" input type)
        // var formattedDate = currentDate.toISOString().split('T')[0];

        // // Set the formatted date as the value for the "invoiceDate" input
        // $("#poDate").val(formattedDate);

        // // Event listener for when the terms dropdown changes
        // $("#terms").on("change", function() {
        //     // Get the selected term and its days due
        //     var selectedOption = $(this).find("option:selected");
        //     var daysDue = selectedOption.data("days-due");

        //     if (daysDue !== undefined) {
        //         // Calculate the due date based on the current date and selected term's days due
        //         var currentDate = new Date();
        //         currentDate.setDate(currentDate.getDate() + parseInt(daysDue));

        //         // Format the date as "YYYY-MM-DD" for the input field
        //         var formattedDate = currentDate.toISOString().split('T')[0];

        //         // Set the calculated due date to the invoiceDueDate input field
        //         $("#poDueDate").val(formattedDate);
        //     } else {
        //         // Clear the invoiceDueDate input field if no days due information is available
        //         $("#poDueDate").val("");
        //     }
        // });

    });
    // Function to handle selection of an existing vendor from the modal
    function selectExistingVendor() {
        var selectedVendor = $("#existingVendor").find(":selected");
        var vendorName = selectedVendor.text();
        var vendorEmail = selectedVendor.data("email");
        var vendorShippingAddress = selectedVendor.data("shipaddress");

        // Set the values in the manual input fields
        $("#vendor").val(vendorName);
        $("#email").val(vendorEmail);
        $("#shippingAddress").val(vendorShippingAddress);
        // Close the modal
        $("#vendorModal").modal("hide");
    }
</script>

<script>
    $(document).ready(function() {

        var purchaseOrderItems = <?php echo json_encode($purchaseOrderItems); ?>;
        var productItems = <?php echo json_encode($productItems); ?>;

        // Function to add a new row for an item
        function addNewItemRow(item, description, quantity, uom, rate, amount, items) {

            // Create a dropdown for selecting items
            var itemOptions = items.map(item =>
                `<option value="${item.itemName}" data-uom="${item.uom}" data-description="${item.itemPurchaseInfo}" data-amount="${item.itemCost}">${item.itemName}</option>`
            ).join('');

            var newRow = `<tr>
            <td>
                <select class="item-dropdown select2" style="width: 400px;" name="item[]" required>
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
            $('.item-dropdown').last().select2({
            placeholder: "Search for an item",
            minimumInputLength: 1 // Minimum characters to start searching
            });

            // Set values for the added row
            var newRowInputs = $("#itemTableBody tr:last").find('select');
            newRowInputs.eq(0).val(item); // Set the value for the <select> element
            var newRowInputs2 = $("#itemTableBody tr:last").find('input');
            newRowInputs2.eq(0).val(description);
            newRowInputs2.eq(1).val(quantity);
            newRowInputs2.eq(2).val(uom);
            newRowInputs2.eq(3).val(rate);
            newRowInputs2.eq(4).val(amount);
            // newRowInputs.eq(4).val(uom);
            // newRowInputs.eq(5).val(rate);
            // newRowInputs.eq(6).val(amount);
        }

        // Populate existing purchase order items when the page loads
        purchaseOrderItems.forEach(item => {
            // Use the correct fields from the purchase_order_items array
            addNewItemRow(item.item, item.description, item.quantity, item.uom, item.rate, item.amount,
                productItems);
            // Recalculate gross amount
            calculateGrossAmount();
            // Recalculate other percentages
            calculatePercentages();
        });

        // Event listener for adding a new item
        $("#addItemBtn").on("click", function() {
            // Get the selected item
            var selectedItem = $("#selectProduct option:selected");
            // Manually provide itemName, description, and amount
            var quantity = selectedItem.data("quantity");
            var itemName = selectedItem.val();
            var description = selectedItem.data("description");
            var amount = selectedItem.data("amount");
            var uom = selectedItem.data("uom");

            // Check if the items array is defined
            var itemsArray = productItems || []; // Use the productItems array if defined, otherwise, an empty array
            // Call the function with the UOM options
            addNewItemRow(itemName, description, quantity, uom, amount, 0, itemsArray);
        });

        // Event listener for removing an item
        $("#itemTableBody").on("click", ".removeItemBtn", function() {



            $(this).closest("tr").remove();

            // Recalculate gross amount
            calculateGrossAmount();
            // Recalculate other percentages
            calculatePercentages();
        });

        // Event listener for updating description and amount and uom based on the selected item
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

        $('.select2').select2();
    });
</script>