<?php include __DIR__ . ('/includes/header.php'); ?>
<?php include('connect.php');

// Initialize variables
$purchaseOrder = null;
$purchaseOrderItems = [];

// Check if the 'checkID' parameter is set
if (isset($_GET['checkID'])) {
    $checkID = $_GET['checkID'];

    // Query to retrieve purchase order details
    $query = "SELECT * FROM checks WHERE checkID = :checkID";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':checkID', $checkID);
    $stmt->execute();

    // Fetch purchase order details
    $purchaseOrder = $stmt->fetch(PDO::FETCH_ASSOC);
    // Check if purchase order details are found
    if ($purchaseOrder) {
        // Fetch purchase order items
        $queryOrderItems = "SELECT * FROM check_items WHERE checkID = :checkID";
        $stmtOrderItems = $db->prepare($queryOrderItems);
        $stmtOrderItems->bindParam(':checkID', $checkID);
        $stmtOrderItems->execute();

        $purchaseOrderItems = $stmtOrderItems->fetchAll(PDO::FETCH_ASSOC);
            // Check the status of the purchase order
    
    } else {
        // Redirect or display an error if purchase order details are not found
        header("Location: index.php"); // Redirect to the main page or display an error message
        exit();
    }
} else {
    // Redirect or display an error if 'checkID' parameter is not set
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
                    <h1 class="m-1" style="font-weight: bold; font-size:30px;">Write Check #:
                        <?php echo $purchaseOrder['checkID']; ?></h1>
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
                    <div class="card">
                        <div class="card-body">
                            <form id="writeCheckForm" action="" method="POST">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="bankAccountName">Bank:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="bankAccountName" name="bankAccountName" placeholder="Select vendor" value="<?php echo $purchaseOrder['bankAccountName']; ?>" readonly>
                                        </div>
                                    
                                        <label for="payee_name">Payee Name:</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="payeeName" name="payeeName" placeholder="Select vendor" value="<?php echo $purchaseOrder['payeeName']; ?>" readonly>
                                        </div>

                                   
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
                                                <input type="text" class="form-control" id="display_amount" value="<?php echo $purchaseOrder['total_amount']; ?>" readonly>
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
    // Assuming you have the checkID and purchaseOrderStatus available
    var checkID = <?php echo $purchaseOrder['checkID']; ?>;
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
                        checkID: checkID
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
        function addNewItemRow(account, memo, amount) {

            // Create a dropdown for selecting items
            var itemOptions = items.map(item =>
                `<option value="${item.account}">${item.account}</option>`
            ).join('');

            var newRow = `<tr>
            <td>
                <select class="form-control item-dropdown" name="item[]" required>
                    <option value="" selected disabled>Select an Item</option>
                    ${itemOptions}
                </select>
            </td>
            <td><input type="text" class="form-control" name="memo[]" required></td>
            <td><input type="number" class="form-control amount-input" name="amount[]" readonly></td>
            <td><button type="button" class="btn btn-danger btn-sm removeItemBtn">Remove</button></td>
        </tr>`;

            $("#itemTableBody").append(newRow);

            // Set values for the added row
            var newRowInputs = $("#itemTableBody tr:last").find('select');
            newRowInputs.eq(0).val(account); // Set the value for the <select> element
            var newRowInputs2 = $("#itemTableBody tr:last").find('input');
            newRowInputs2.eq(0).val(memo);
            newRowInputs2.eq(1).val(amount);
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
</script>