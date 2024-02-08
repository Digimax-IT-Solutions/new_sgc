<?php include __DIR__ . ('/includes/header.php'); ?>
<?php include('connect.php');

// Initialize variables
$purchaseOrder = null;
$purchaseOrderItems = [];

// Check if the 'bill_id' parameter is set
if (isset($_GET['bill_id'])) {
    $bill_id = $_GET['bill_id'];

    // Query to retrieve purchase order details
    $query = "SELECT * FROM bills WHERE bill_id = :bill_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':bill_id', $bill_id);
    $stmt->execute();

    // Fetch purchase order details
    $purchaseOrder = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if purchase order details are found
    if ($purchaseOrder) {
        // Fetch purchase order items
        $queryOrderItems = "SELECT * FROM bills_details WHERE bill_id = :bill_id";
        $stmtOrderItems = $db->prepare($queryOrderItems);
        $stmtOrderItems->bindParam(':bill_id', $bill_id);
        $stmtOrderItems->execute();

        $purchaseOrderItems = $stmtOrderItems->fetchAll(PDO::FETCH_ASSOC);
            // Check the status of the purchase order
    } else {
        // Redirect or display an error if purchase order details are not found
        header("Location: index.php"); // Redirect to the main page or display an error message
        exit();
    }
} else {
    // Redirect or display an error if 'bill_id' parameter is not set
    header("Location: index.php"); // Redirect to the main page or display an error message
    exit();
}


?>

<?php

// Fetch product items
$query = "SELECT account, memo, amount FROM bills_details";
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
                    <h1 class="m-1" style="font-weight: bold; font-size:30px;">Bill #:
                        <?php echo $purchaseOrder['bill_id']; ?></h1>
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
                            <form id="billsForm" method="POST">
                                <input type="text" class="form-control" name="bill_id" value="<?php echo $purchaseOrder['bill_id']; ?>" hidden>
                                <div class="form-row">
                                    <div class="form-group col-md-2">
                                        <label for="vendor">A/P Account:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="bankAccountName" name="bankAccountName" placeholder="Select vendor" value="<?php echo $purchaseOrder['bankAccountName']; ?>" readonly>
                                        </div>

                                        <label for="vendor">Vendor Name:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="vendor" name="vendor" placeholder="Select vendor" value="<?php echo $purchaseOrder['vendor']; ?>" readonly>
                                        </div>

                                        <label for="email">Address:</label>
                                        <input type="text" class="form-control" id="email" name="email" value="<?php echo $purchaseOrder['address']; ?>">
                                    </div>
                                   
    

                                    <div class="form-group col-md-2">
                                        <label for="bill_date">Bill Due:</label>
                                        <input type="date" class="form-control" id="bill_date" name="bill_date" value="<?php echo $purchaseOrder['bill_date']; ?>" required>
                                        <label for="bill_due">Bill Discount:</label>
                                        <input type="date" class="form-control" id="bill_due" name="bill_due" value="<?php echo $purchaseOrder['bill_due']; ?>" required>
                                    </div>

                                    <!-- Terms -->
                                    <div class="form-group col-md-2">
                                        <label for="terms">TERMS:</label>
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
                                        <label for="total_amount">Amount Due:</label>
                                        <div class="total" style="font-size: 30px;">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="total_amount" name="total_amount" value="" hidden>
                                                <input type="text" class="form-control" id="display_amount" value="₱<?= $purchaseOrder['total_amount']; ?>" readonly>
                                            </div>
                                        </div>




                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="memo">MEMO:</label>
                                        <textarea class="form-control" id="memo" name="memo" rows="3" cols="50" value="<?php echo $purchaseOrder['memo']; ?>"><?php echo $purchaseOrder['memo']; ?></textarea>
                                        <br><br>
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
                                                <th>ACCOUNT</th>
                                                <th>MEMO</th>
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
                                <!-- <div class="modal-footer">
                                    <div class="summary-details">
                                        <div class="container"> -->
                                            <!-- GORSS AMOUNT -->
                                            <!-- <div class="row">

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
                                            </div> -->
                                            <!-- DISCOUNT -->
                                            <!-- <div class="row">
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
                                            </div> -->
                                            <!-- NET AMOUND DUE -->
                                            <!-- <div class="row">
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
                                            </div> -->
                                            <!-- VAT PERCENTAGE -->
                                            <!-- <div class="row">
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
                                            </div> -->
                                            <!-- NET OF VAT -->
                                            <!-- <div class="row">
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
                                            </div> -->

                                            <br>
                                            <!-- GROSS AMOUNT -->
                                            <!-- <div class="row" style="font-size: 30px">
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

                                </div> -->

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
    });
</script>
<!-- SELECT vendor -->
<script>
    $(document).ready(function() {
        // Attach a click event handler to the clear button
        $("#clearButton").on("click", function() {
            // Reset all form fields to their default values
            $("#billsForm")[0].reset();

            // Additional steps may be required depending on your specific form elements
            // Clearing the item table body
            $("#itemTableBody").empty();
        });

        // Attach a click event handler to the delete button
// Attach a click event handler to the delete button
        $("#deleteButton").on("click", function() {
    // Assuming you have the bill_id and purchaseOrderStatus available
        var bill_id = <?php echo $purchaseOrder['bill_id']; ?>;

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
                // Use AJAX to send a request to delete_bills.php
                $.ajax({
                    url: 'modules/vendor_center/enter_bills/delete_bills.php',
                    method: 'POST',
                    data: {
                        bill_id: bill_id
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
                                // Redirect to the bills page after clicking OK
                                if (result.isConfirmed) {
                                    window.location.href = 'bills';
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
                            text: 'Error deleting Bills Details. Please try again.',
                        });
                        console.error('Error deleting Purchase Order: ' + error);
                    }
                    });
                }
            });
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
                url: 'modules/vendor_center/bills/update_bills.php',
                method: 'POST',
                data: $("#billsForm").serialize(),
                success: function(response) {
                    // Handle the success response using SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response,
                    }).then((result) => {
                        // Redirect to the sales_invoice page after clicking OK
                        if (result.isConfirmed) {
                            window.location.href = 'create_bills';
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
                url: 'modules/vendor_center/bills/update_bills.php',
                method: 'POST',
                data: $("#billsForm").serialize(),
                dataType: 'json', // Expect JSON response from the server
                success: function(response) {
                    if (response.status === 'success') {
                        // Handle the success response using SweetAlert
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response.message,
                        }).then((result) => {
                            // Redirect to the bills page after clicking OK
                            if (result.isConfirmed) {
                                window.location.href = 'bills';
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
            return isValid;;
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
$(document).ready(function() {
    
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

        $("#billsForm").on("keyup", "input.amount-input", function() {
            // Recalculate the gross amount when an "Amount" input changes
            calculateGrossAmount();
        });
    // Initialize variables
    var purchaseOrderItems = <?php echo json_encode($purchaseOrderItems); ?>;
    var productItems = <?php echo json_encode($productItems); ?>;

    // Function to add a new row for an item
    function addNewItemRow(item) {
        var newRow = `
            <tr>
                <td>
                    <select class="form-control item-dropdown" name="item[]" required>
                        <option value="" selected disabled>Select an Item</option>
                        ${generateItemOptions(productItems)}
                    </select>
                </td>
                <td><input type="text" class="form-control" name="memo[]" required></td>
                <td><input type="number" class="form-control amount-input" name="amount[]" value="${item.amount || 0}"></td>
                <td><button type="button" class="btn btn-danger btn-sm removeItemBtn">Remove</button></td>
            </tr>`;

        $("#itemTableBody").append(newRow);
        // Set selected item in the dropdown
        $("#itemTableBody tr:last .item-dropdown").val(item.account);
    }

    // Function to generate options for item dropdown
    function generateItemOptions(items) {
        return items.map(item =>
            `<option value="${item.account}">${item.account}</option>`
        ).join('');
    }

    // Populate existing purchase order items
    purchaseOrderItems.forEach(addNewItemRow);

    // Event listener for adding a new item
    $("#addItemBtn").on("click", function() {
        var selectedItem = $("#selectProduct option:selected");
        var memo = selectedItem.data("memo");
        var account = selectedItem.val();
        var amount = selectedItem.data("amount");
    

        addNewItemRow({ account: account, memo: memo, amount: amount });
    });

    // Event listener for removing an item
    $("#itemTableBody").on("click", ".removeItemBtn", function() {
        $(this).closest("tr").remove();
    });
});

</script>