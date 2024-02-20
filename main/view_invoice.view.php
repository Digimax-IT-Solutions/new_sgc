<?php include __DIR__ . ('/includes/header.php'); ?>
<?php
include('connect.php');

// Initialize variables
$salesInvoice = null;
$salesInvoiceItems = [];

// Check if the 'poID' parameter is set
if (isset($_GET['invoiceID'])) {
    $invoiceID = $_GET['invoiceID'];

    // Query to retrieve purchase order details
    $query = "SELECT * FROM sales_invoice WHERE invoiceID = :invoiceID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':invoiceID', $invoiceID);
    $stmt->execute();

    // Fetch purchase order details
    $salesInvoice = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if purchase order details are found
    if ($salesInvoice) {
        // Fetch purchase order items
        $queryInvoiceItems = "SELECT * FROM sales_invoice_items WHERE salesInvoiceID = :invoiceID";
        $stmtInvoiceItems = $db->prepare($queryInvoiceItems);
        $stmtInvoiceItems->bindParam(':invoiceID', $invoiceID);
        $stmtInvoiceItems->execute();

        $salesInvoiceItems = $stmtInvoiceItems->fetchAll(PDO::FETCH_ASSOC);
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




// Fetch product items
$query = "SELECT itemName, itemSalesInfo, itemSrp, uom FROM items";

// Execute the query
$result = $db->query($query);

// Fetch all rows at once
$productItems = $result->fetchAll(PDO::FETCH_ASSOC);

// Convert the result to JSON for faster fetching in JavaScript
$productItemsJSON = json_encode($productItems);
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

.custom-control-input-green:checked~.custom-control-label-green::before {
    border-color: #28a745;
    background-color: #28a745;
}

.custom-control-input-green:focus~.custom-control-label-green::before {
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
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
                    <h1 class="m-1" style="font-weight: bold; font-size:30px;">Sales Invoice</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="sales_invoice">Sales Transaction</a></li>
                        <li class="breadcrumb-item active">Sales Invoice</li>

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
                            <form id="salesInvoiceForm" action="" method="POST">
                                <input type="text" class="form-control" name="invoiceID"
                                    value="<?php echo $salesInvoice['invoiceID']; ?>" hidden>
                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label for="customer">CUSTOMER NAME <span><a href="customer_list">Add new
                                                    customer</a></span></label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="customer" name="customer"
                                                placeholder="Select customer"
                                                value="<?php echo $salesInvoice['customer']; ?>">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary"
                                                    style="background-color: rgb(0, 149, 77); color: white; font-size: 70%;"
                                                    type="button" data-toggle="modal"
                                                    data-target="#customerModal">Select</button>
                                            </div>
                                        </div>
                                        <label for="address">BILLING ADDRESS</label>
                                        <input type="text" class="form-control" id="address" name="address"
                                            value="<?php echo $salesInvoice['address']; ?>">
                                        <label for="shippingAddress">SHIPPING ADDRESS</label>
                                        <input type="text" class="form-control" id="shippingAddress"
                                            name="shippingAddress"
                                            value="<?php echo $salesInvoice['shippingAddress']; ?>">
                                        <label for="email">EMAIL</label>
                                        <input type="text" class="form-control" id="email" name="email"
                                            value="<?php echo $salesInvoice['email']; ?>">
                                        <label for="invoiceTin">TIN:</label>
                                        <input type="text" class="form-control" id="invoiceTin" name="invoiceTin"
                                            value="<?php echo $salesInvoice['invoiceTin']; ?>">

                                    </div>
                                    <!-- Modal for selecting existing customers -->
                                    <div class="modal" id="customerModal" tabindex="-1" role="dialog"
                                        aria-labelledby="customerModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">

                                                    <h5 class="modal-title" id="customerModalLabel">Select Customer | <a
                                                            href="customer_list">Add new
                                                            customer</a></h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <!-- Place your customer list here -->
                                                    <select class="form-control" id="existingCustomer"
                                                        name="existingCustomer">
                                                        <option value="">Select an existing customer</option>
                                                        <?php
                                                        // Fetch customers from the database and populate the dropdown in the modal
                                                        $query = "SELECT customerID, customerName, customerEmail, customerBillingAddress, customerShippingAddress FROM customers";
                                                        $result = $db->query($query);

                                                        if ($result) {
                                                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                                echo "<option value='{$row['customerID']}' data-email='{$row['customerEmail']}' data-address='{$row['customerBillingAddress']}' data-shipaddress='{$row['customerShippingAddress']}'>{$row['customerName']}</option>";
                                                            }
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary"
                                                        style="background-color: rgb(0, 149, 77); color: white;"
                                                        onclick="selectExistingCustomer()">Select Customer</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label for="invoiceNo">INVOICE #</label>
                                        <input type="text" class="form-control" id="invoiceNo" name="invoiceNo" required
                                            value="<?php echo $salesInvoice['invoiceNo']; ?>">
                                        <label for="invoiceDate">INVOICE DATE</label>
                                        <input type="date" class="form-control" id="invoiceDate" name="invoiceDate"
                                            required value="<?php echo $salesInvoice['invoiceDate']; ?>">
                                        <label for="invoiceDueDate">INVOICE DUE DATE</label>
                                        <input type="date" class="form-control" id="invoiceDueDate"
                                            name="invoiceDueDate" required
                                            value="<?php echo $salesInvoice['invoiceDueDate']; ?>">
                                        <label for="invoiceBusinessStyle">BUSINESS STYLE</label>
                                        <input type="text" class="form-control" id="invoiceBusinessStyle" name="invoiceBusinessStyle" required
                                        value="<?php echo $salesInvoice['invoiceBusinessStyle']; ?>">
                                    </div>

                                    <div class="form-group col-md-2">
                                        <label for="invoicePo">PURCHASE ORDER</label>
                                        <input type="text" class="form-control" id="invoicePo" name="invoicePo" required
                                        value="<?php echo $salesInvoice['invoicePo']; ?>">
                                        <label for="account">SELECT ACCOUNT | <span><a href="chart_of_accounts">Add New
                                                    Account</a></span></label>
                                        <select class="form-control" id="account" name="account" required>
                                            <option value="<?php echo $salesInvoice['account']; ?>">
                                                <?php echo $salesInvoice['account']; ?>
                                            </option>
                                            <!-- Options will be dynamically populated using JavaScript/jQuery -->
                                        </select>

                                        <!-- PAYMENT METHOD -->
                                        <label for="paymentMethod">PAYMENT METHOD | <span><a
                                                    href="payment_method_list">Add
                                                    New
                                                    Payment</a></span></label>
                                        <select class="form-control" id="paymentMethod" name="paymentMethod" required>
                                            <option value="<?php echo $salesInvoice['paymentMethod']; ?>">
                                                <?php echo $salesInvoice['paymentMethod']; ?></option>
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
                                        <br>
                                        <div class="form-group col-md-6">
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox"
                                                    class="custom-control-input custom-control-input-green"
                                                    id="accountTypeSwitch" checked>
                                                <label class="custom-control-label custom-control-label-green"
                                                    for="accountTypeSwitch">CASH SALES</label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Terms -->
                                    <div class="form-group col-md-2">
                                        <label for="terms">TERMS | <span><a href="terms_list">Add New
                                                    Terms</a></span></label>
                                        <select class="form-control" id="terms" name="terms" required>
                                            <option <?php echo $salesInvoice['terms']; ?>>
                                                <?php echo $salesInvoice['terms']; ?></option>
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

                                        <!-- LOCATION -->
                                        <label for="location">LOCATION | <span><a href="location_list">Add New
                                                    Location</a></span></label>
                                        <select class="form-control" id="location" name="location" required>
                                            <option value="<?php echo $salesInvoice['location']; ?>">
                                                <?php echo $salesInvoice['location']; ?></option>
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


                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="memo">MEMO:</label>
                                        <textarea class="form-control" id="memo" name="memo" rows="3"
                                            cols="50"><?php echo $salesInvoice['memo']; ?></textarea>
                                        <br><br>
                                        <div class="form-group">
                                            <?php
                                            $status = $salesInvoice['invoiceStatus'];
                                            $statusColor = ($status === 'PAID') ? 'green' : 'red';
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
                                                <input type="text" class="form-control" name="grossAmount"
                                                    id="grossAmount" readonly>
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
                                            <input type="number" class="form-control" name="discountPercentage"
                                                id="discountPercentage" placeholder="Enter %">
                                        </div>
                                        <div class="col-md-5 d-inline-block">
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">&#8369;</span>
                                                </div>
                                                <input type="text" class="form-control" name="discountAmount"
                                                    id="discountAmount" readonly>
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
                                                <input type="text" class="form-control" name="netAmountDue"
                                                    id="netAmountDue" readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- VAT PERCENTAGE -->
                                    <div class="row">
                                        <div class="col-md-4 d-inline-block text-right">
                                            <label for="vatPercentage">VAT (%):</label>
                                        </div>
                                        <div class="col-md-3 d-inline-block">
                                            <select class="form-control" id="vatPercentage" name="vatPercentage"
                                                required>
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
                                                <input type="text" class="form-control" name="vatPercentageAmount"
                                                    id="vatPercentageAmount" readonly>
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
                                                <input type="text" class="form-control" name="netOfVat" id="netOfVat"
                                                    readonly>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- TAX WITHHELD -->
                                    <div class="row">
                                        <div class="col-md-4 d-inline-block text-right">
                                            <label>Tax Withheld (%):</label>
                                        </div>
                                        <div class="col-md-3 d-inline-block">
                                            <select class="form-control" id="taxWithheldPercentage"
                                                name="taxWithheldPercentage" required>
                                                <?php
                                                $query = "SELECT wTaxRate, wTaxName FROM wtax";
                                                $result = $db->query($query);

                                                if ($result) {
                                                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                        echo "<option value='{$row['wTaxRate']}'>{$row['wTaxName']}</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-5 d-inline-block">
                                            <div class="input-group">
                                                <div class="input-group">
                                                    <div class="input-group-prepend">
                                                        <span class="input-group-text">&#8369;</span>
                                                    </div>
                                                    <input type="text" class="form-control" name="taxWitheldAmount"
                                                        id="taxWitheldAmount" readonly>
                                                </div>
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
                                                <input type="text" class="form-control" name="totalAmountDue"
                                                    id="totalAmountDue" readonly>
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
                                <button type="button" class="btn btn-secondary"
                                    data-invoice-id="<?php echo $salesInvoice['invoiceID']; ?>"
                                    id="printButton">Print</button>
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
// Use the fetched data in JavaScript
var productItems = <?php echo $productItemsJSON; ?>;
</script>
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
            var currentDate = new Date($("#invoiceDate").val());
            currentDate.setDate(currentDate.getDate() + parseInt(daysDue));
            setFormattedDate($("#invoiceDueDate"), currentDate);
        } else {
            $("#invoiceDueDate").val("");
        }
    }

    // Initialize receiveItemDate with the current date
    var currentDate = new Date();
    setFormattedDate($("#invoiceDate"), currentDate);

    // Event listener for when the receiveItemDate input changes
    $("#invoiceDate").on("change", function() {
        updateDueDate();
    });

    // Event listener for when the terms dropdown changes
    $("#terms").on("change", function() {
        updateDueDate();
    });

});
</script>
<script>
$(document).ready(function() {
    $("#clearButton").on("click", function() {
        // Reset all form fields to their default values
        $("#salesInvoiceForm")[0].reset();

        // Additional steps may be required depending on your specific form elements
        // Clearing the item table body
        $("#itemTableBody").empty();

        // Reset calculated fields
        $("#grossAmount, #discountAmount, #netAmountDue, #vatPercentageAmount, #netOfVat, #totalAmountDue")
            .val('');

        // Optionally, you can set default values for specific fields if needed
        $("#vatPercentage").val('<?php echo $salesInvoice['vatPercentage']; ?>');
        $("#paymentMethod, #location, #terms").val('<?php echo $salesInvoice['paymentMethod']; ?>');

        calculateGrossAmount();
        // Recalculate other percentages
        calculatePercentages();
    });

    // delete invoice data
    // Attach a click event handler to the delete button
    $("#deleteButton").on("click", function() {
        var invoiceID = <?php echo $salesInvoice['invoiceID']; ?>;

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
                // Use AJAX to send a request to delete_invoice.php
                $.ajax({
                    url: 'modules/invoice/delete_invoice.php',
                    method: 'POST',
                    data: {
                        invoiceID: invoiceID
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
                                // Redirect to the sales_invoice page after clicking OK
                                if (result.isConfirmed) {
                                    window.location.href = 'sales_invoice';
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
                            text: 'Error deleting invoice. Please try again.',
                        });
                        console.error('Error deleting invoice: ' + error);
                    }
                });
            }
        });
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
            url: 'modules/invoice/update_invoice.php',
            method: 'POST',
            data: $("#salesInvoiceForm").serialize(),
            success: function(response) {
                // Handle the success response using SweetAlert
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response,
                }).then((result) => {
                    // Reload the page after clicking OK
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            },
            error: function(xhr, status, error) {
                // Handle the error response using SweetAlert
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Error updating invoice. Please try again.',
                });
                console.error('Error updating invoice: ' + error);
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
            url: 'modules/invoice/update_invoice.php',
            method: 'POST',
            data: $("#salesInvoiceForm").serialize(),
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
                            window.location.href = 'sales_invoice';
                        }
                    });
                } else {
                    // Handle the error response using SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Check invoice #!',
                        text: '' + response.message,
                    });
                }
            },
            error: function(xhr, status, error) {
                // Handle the error response using SweetAlert
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Error updating invoice. Please try again.',
                });
                console.error('Error updating invoice: ' + error);
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
        if ($("#invoiceBusinessStyle").val() === '') {
                isValid = false;
                highlightInvalidField($("#invoiceBusinessStyle"));
        } else {
                resetInvalidField($("#invoiceBusinessStyle"));
        }

        // Add validation logic for each required field
        if ($("#invoicePo").val() === '') {
                isValid = false;
                highlightInvalidField($("#invoicePo"));
        } else {
                resetInvalidField($("#invoicePo"));
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
        if ($("#account").val() === '') {
            isValid = false;
            highlightInvalidField($("#account"));
        } else {
            resetInvalidField($("#account"));
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
});
</script>
<!-- SELECT CUSTOMER -->
<script>
// Function to handle selection of an existing customer from the modal
function selectExistingCustomer() {
    var selectedCustomer = $("#existingCustomer").find(":selected");
    var customerName = selectedCustomer.text();
    var customerEmail = selectedCustomer.data("email");
    var customerAddress = selectedCustomer.data("address");
    var customerShippingAddress = selectedCustomer.data("shipaddress");

    // Set the values in the manual input fields
    $("#customer").val(customerName);
    $("#email").val(customerEmail);
    $("#address").val(customerAddress);
    $("#shippingAddress").val(customerShippingAddress);
    // Close the modal
    $("#customerModal").modal("hide");
}
</script>
<!-- SELECT ACCOUNTS -->|

<script>
    $(document).ready(function() {
        // Function to update the account options based on the switch state
        function updateAccountOptions() {
            var accountType = $('#accountTypeSwitch').prop('checked') ? 'Other Current Assets' : 'Accounts Receivable';

            // If the account type is 'Accounts Receivable', uncheck the checkbox
            if (accountType === 'Accounts Receivable') {
                $('#accountTypeSwitch').prop('checked', false);
            }

            $.ajax({
                type: "GET",
                url: "modules/invoice/get_account_options.php",
                data: {
                    accountType: accountType
                },
                success: function(response) {
                    var options = JSON.parse(response);
                    $("#account").empty();
                    var undepositedFundsOption = options.find(option => option.account_name === 'Undeposited Fund');
                    options.forEach(function(option) {
                        $("#account").append(
                            `<option value='${option.account_name}' ${undepositedFundsOption && option.account_name === 'Undeposited Fund' ? 'selected' : ''}>${option.account_name}</option>`
                        );
                    });
                },
                error: function() {
                    console.log("Error fetching account options.");
                }
            });
        }

        // Handle switch state change event
        $('#accountTypeSwitch').on('change', function() {
            // Update account options when the switch state changes
            updateAccountOptions();
        });

        // Call updateAccountOptions initially to set up the page
        updateAccountOptions();
    });
</script>
<script>
    $(document).ready(function() {


        var salesInvoiceItems = <?php echo json_encode($salesInvoiceItems); ?>;
        var productItems = <?php echo json_encode($productItems); ?>;



        // Function to add a new row for an item
        function addNewItemRow(itemName, description, quantity, uom, rate, amount, items) {
    var newRow = `<tr>
        <td>
            <select class="item-dropdown select2" style="width: 400px;" name="item[]" required>
                <option value="" selected disabled>Select an Item</option>`;
    // Add options for item dropdown
    items.forEach(item => {
        newRow += `<option value="${item.itemName}" data-description="${item.itemSalesInfo}" data-amount="${item.itemSrp}">${item.itemName}</option>`;
    });
    newRow += `</select>
        </td>
        <td><input type="text" class="form-control description-field" name="description[]" required value="${description}"></td>
        <td><input type="number" class="form-control" name="quantity[]" required value="${quantity}"></td>
        <td><input type="text" class="form-control uom-field" name="uom[]" readonly value="${uom}"></td>
        <td><input type="number" class="form-control rate-field" name="rate[]" required value="${rate}"></td>
        <td><input type="number" class="form-control amount-field" name="amount[]" readonly value="${amount}"></td>
        <td><button type="button" class="btn btn-danger btn-sm removeItemBtn">Remove</button></td>
    </tr>`;

    $("#itemTableBody").append(newRow);

    // Initialize Select2 for the newly added dropdown
    $('.item-dropdown').last().select2();

    // Set values for the added row
    var newRowInputs = $("#itemTableBody tr:last").find('select');
    newRowInputs.eq(0).val(itemName); // Set the value for the <select> element
    var newRowInputs2 = $("#itemTableBody tr:last").find('input');
    newRowInputs2.eq(0).val(description);
    newRowInputs2.eq(1).val(quantity);
    newRowInputs2.eq(2).val(uom);
    newRowInputs2.eq(3).val(rate);
    newRowInputs2.eq(4).val(amount);
}


        // Populate existing purchase order items when the page loads
        salesInvoiceItems.forEach(item => {
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
        // Event listener for updating description and amount based on the selected item
        $("#itemTableBody").on("change", ".item-dropdown", function() {
            var row = $(this).closest("tr");
            var selectedOption = $(this).find("option:selected");

            // Update the description field based on the selected item
            var description = selectedOption.data("description");
            row.find(".description-field").val(description !== undefined ? description : '');

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
<script>
    // Get the button element
    var printButton = document.getElementById('printButton');

    // Add a click event listener
    printButton.addEventListener('click', function() {
        // Get the invoiceID from the button's data attribute
        var invoiceID = printButton.getAttribute('data-invoice-id');
        
        // Build the URL with the invoiceID and navigate to print_invoice.php
        var url = 'print_invoice?invoiceID=' + invoiceID;
        
        // Redirect to the URL
        window.location.href = url;
    });
</script>
<script>
$(document).ready(function() {
    $('.select2').select2();
});
</script>