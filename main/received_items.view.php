<?php include __DIR__ . ('/includes/header.php'); ?>
<?php include('connect.php'); ?>



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
        padding: 3px;
        /* Adjust the padding as needed */
    }

    #itemTable th {
        text-align: center;
    }

    #itemTable tbody tr:hover {

        color: white;
        background-color: rgb(0, 149, 77);
        /* Set your desired background color here */
    }

    #poTable {
        border-collapse: collapse;
        width: 100%;
    }

    #poTable th,
    #poTable td {

        padding: 2px;
        /* Adjust the padding as needed */
    }

    #poTable tbody tr:hover {

        color: white;
        background-color: maroon;
        /* Set your desired background color here */
    }
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-1" style="font-weight: bold; font-size:30px;">Receive Items</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a style="color:maroon;" href="dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a style="color:maroon;" href="sales_invoice">Vendor Center</a></li>
                        <li class="breadcrumb-item active">Receive Items</li>

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
                            <!-- RR Form -->
                            <form id="receiveItemForm" action="" method="POST">
                                <div class="form-row">
                                    <div class="form-group col-md-3">
                                        <label for="chartOfAccountSelect">ACCOUNTS PAYABLE ACCOUNT</label>
                                        <div class="input-group">
                                            <!-- Add this inside the vendor input-group div -->
                                            <!-- Place your vendor list here -->
                                            <!-- Add this inside the vendor input-group div -->
                                            <!-- Place your vendor list here -->
                                            <select class="form-control" id="chartOfAccountSelect" name="chartOfAccountSelect">
                                                <!-- <option value="">Select Account</option> -->
                                                <?php
                                                // Fetch accounts with account_type = 'Accounts Payable' from the database and populate the dropdown
                                                $coaQuery = "SELECT account_name FROM chart_of_accounts WHERE account_type = 'Accounts Payable'";

                                                try {
                                                    $coaStmt = $db->prepare($coaQuery);
                                                    $coaStmt->execute();

                                                    $accounts = $coaStmt->fetchAll(PDO::FETCH_ASSOC);

                                                    foreach ($accounts as $account) {
                                                        echo "<option value='{$account['account_name']}'>{$account['account_name']}</option>";
                                                    }
                                                } catch (PDOException $e) {
                                                    // Handle the exception, e.g., log the error or return an error message
                                                    echo "<option value=''>Error fetching accounts payable</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <label for="vendorSelect">VENDOR</label>
                                        <div class="input-group">
                                            <!-- Add this inside the vendor input-group div -->
                                            <!-- Place your vendor list here -->
                                            <?php
                                            // Fetch vendors with purchase orders having poStatus = 'WAITING FOR DELIVERY' from the database and populate the dropdown in the modal
                                            $vendorQuery = "SELECT purchase_order.vendor, MIN(purchase_order.poNo) AS poNo, GROUP_CONCAT(purchase_order.poID) AS poIDs
                    FROM purchase_order 
                    WHERE purchase_order.poStatus = 'WAITING FOR DELIVERY'
                    GROUP BY purchase_order.vendor";

                                            try {
                                                $vendorStmt = $db->prepare($vendorQuery);
                                                $vendorStmt->execute();

                                                $vendors = $vendorStmt->fetchAll(PDO::FETCH_ASSOC);

                                                if (count($vendors) > 0) {
                                                    echo '<select class="form-control" id="vendorSelect" name="vendorSelect" onchange="checkOpenPOs()">';
                                                    echo '<option value="">Select vendor</option>';

                                                    foreach ($vendors as $vendor) {
                                                        echo "<option value='{$vendor['vendor']}' data-poid='{$vendor['poNo']}'>{$vendor['vendor']}</option>";
                                                    }

                                                    echo '</select>';
                                                } else {
                                                    echo '<select class="form-control" id="vendorSelect" name="vendorSelect" onchange="checkOpenPOs()">';
                                                    echo "<option >No purchase order to receive</option>";

                                                    echo '</select>';
                                                }
                                            } catch (PDOException $e) {
                                                // Handle the exception, log the error, or return an error message with MySQL error information
                                                $errorInfo = $vendorStmt->errorInfo();
                                                $errorMessage = "Error fetching vendors: " . $errorInfo[2]; // MySQL error message
                                                echo "<p>$errorMessage</p>";
                                            }
                                            ?>
                                        </div>
                                        <br><br>
                                        <button type="button" class="btn btn-primary" id="openPOsButton" style="display: none;  background-color: rgb(0, 149, 77);">Open Purchase
                                            Orders</button>

                                    </div>
                                    <div class="form-group col-md-2 offset-md-2">
                                        <!-- Empty div with offset -->
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="terms">TERMS | <span><a href="terms_list">Add New
                                                    Terms</a></span></label>
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
                                        <label for="receiveItemDueDate">DUE DATE</label>
                                        <input type="date" class="form-control" id="receiveItemDueDate" name="receiveItemDueDate" required>
                                        <!-- LOCATION -->
                                        <label for="location">LOCATION | <span><a href="location_list">Add New
                                                    Location</a></span></label>
                                        <select class="form-control" id="location" name="location" required>
                                            <option value="">Select Location</option>
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
                                        <label for="receiveItemDate">DATE</label>
                                        <div class="input-group">
                                            <input type="date" class="form-control" id="receiveItemDate" name="receiveItemDate">
                                        </div>
                                        <label for="refNo">REFERENCE NO.</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="refNo" name="refNo" placeholder="REFERENCE NO">
                                        </div>
                     
                                    </div>
                                    <div class="form-group col-md-2">
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <label for="memo">MEMO:</label>
                                        <textarea class="form-control" id="memo" name="memo" rows="2" cols="40" value="">Received items (bill to follow)</textarea>
                                    </div>
                                </div>

                                <!-- <div class="form-row">
                                    <div class="form-group col-md-6">
                                        <button type="button" class="btn btn-success" id="addItemBtn">Add Item</button>
                                    </div>
                                </div> -->
                                <!-- Select Product Item -->
                                <div class="table-responsive">
                                    <table class="table table-condensed table-border" id="itemTable">
                                        <thead>
                                            <tr>
                                                <th>ITEM</th>
                                                <th>DESCRIPTION</th>
                                                <th>QTY</th>
                                                <th>UOM</th>
                                                <th>RATE</th>
                                                <th>AMOUNT</th>
                                                <th>PO #</th>
                                                <th></th>
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
                                                        <input type="text" class="form-control" name="grossAmount" id="grossAmount" readonly>
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
                                                        <input type="text" class="form-control" name="netAmountDue" id="netAmountDue" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- VAT PERCENTAGE -->
                                            <div class="row">
                                                <div class="col-md-4 d-inline-block text-right">
                                                    <label for="vatPercentage">Input VAT (%):</label>
                                                </div>
                                                <div class="col-md-3 d-inline-block">
                                                    <select class="form-control" id="vatPercentage" name="vatPercentage" required>
                                                        <?php
                                                        $query = "SELECT salesTaxRate, salesTaxName FROM sales_tax";
                                                        $result = $db->query($query);

                                                        if ($result) {
                                                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                                                echo "<option value='{$row['salesTaxName']}' data-tax-name='{$row['salesTaxRate']}'>{$row['salesTaxName']}</option>";
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
                                                        <input type="text" class="form-control" name="vatPercentageAmount" id="vatPercentageAmount" readonly>
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
                                                        <input type="text" class="form-control" name="netOfVat" id="netOfVat" readonly>
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
                                                        <input type="text" class="form-control" name="totalAmountDue" id="totalAmountDue" readonly>
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
                                        <!-- <button type="button" class="btn btn-info" id="saveAndCloseButton">Save and
                                            Close</button>
                                        <button type="button" class="btn btn-warning" id="clearButton">Clear</button>
                                        <button type="button" class="btn btn-danger" id="deleteButton">Delete</button>
                                        <button type="button" class="btn btn-primary" id="submitFormButton">Submit Form</button> -->
                                    </div>
                                </div>


                            </form>
                            <!-- End RR Form -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal" id="openPOsModal" tabindex="-1" role="dialog" aria-labelledby="openPOsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="openPOsModalLabel">Open Purchase Orders</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered" id="poTable">
                        <thead>
                            <tr>
                                <th></th>
                                <th>PO #</th>
                                <th>VENDOR</th>
                                <th>Amount</th>
                                <th>PO Date</th>
                                <th>Memo</th>
                            </tr>
                        </thead>
                        <tbody id="openPOsTableBody">
                            <!-- Dynamic content will be added here -->
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <!-- <button type="button" class="btn btn-success" onclick="processSelectedPOs()">Ok</button> -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('includes/footer.php'); ?>
<script src="modules/vendor_center/receive_items/receive_Items.js"></script>


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
                var currentDate = new Date($("#receiveItemDate").val());
                currentDate.setDate(currentDate.getDate() + parseInt(daysDue));
                setFormattedDate($("#receiveItemDueDate"), currentDate);
            } else {
                $("#receiveItemDueDate").val("");
            }
        }

        // Initialize receiveItemDate with the current date
        var currentDate = new Date();
        setFormattedDate($("#receiveItemDate"), currentDate);

        // Event listener for when the receiveItemDate input changes
        $("#receiveItemDate").on("change", function() {
            updateDueDate();
        });

        // Event listener for when the terms dropdown changes
        $("#terms").on("change", function() {
            updateDueDate();
        });

    });
</script>

<script>
    // Function to send form data
    function submitForm() {
        var formData = {
            vendorSelect: $('#vendorSelect').val(),
            chartOfAccountSelect: $('#chartOfAccountSelect').val(), // Include the selected chart of account
            poNo: $('#vendorSelect option:selected').data('poid'), // Add the poNo to formData
            receiveItemDate: $('#receiveItemDate').val(),
            location: $('#location').val(),
            terms: $('#terms').val(),
            receiveItemDueDate: $('#receiveItemDueDate').val(),
            refNo: $('#refNo').val(),
            totalAmountDue: $('#totalAmountDue').val(),
            memo: $('#memo').val(),
            grossAmount: $('#grossAmount').val(),
            discountPercentage: $('#discountPercentage').val(),
        
            netAmountDue: $('#netAmountDue').val(),
            vatPercentageAmount: $('#vatPercentageAmount').val(),
            netOfVat: $('#netOfVat').val(),
            items: [] // Add the array to hold items
        };

        // Iterate through the item rows and add them to the formData array
        $('#itemTableBody tr').each(function() {
            var item = {
                item: $(this).find('td:eq(0) input').val(),
                description: $(this).find('td:eq(1) input').val(),
                quantity: $(this).find('td:eq(2) input').val(),
                uom: $(this).find('td:eq(3) input').val(),
                rate: $(this).find('td:eq(4) input').val(),
                amount: $(this).find('td:eq(5) input').val(),
                poItemID: $(this).find('td:eq(6) input').val()
            };
            formData.items.push(item);
        });

        // Send the form data to the server
        $.ajax({
            type: 'POST',
            url: 'modules/vendor_center/receive_items/submit_receive_items.php',
            data: formData,
            dataType: 'json',
            success: function(response) {
                // Handle the response from the server
                console.log(response);

                if (response.status === 'success') {
                    // Display success message with SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Receive items saved successfully',
                    }).then((result) => {
                        // Redirect to the received_items page after clicking OK
                        if (result.isConfirmed) {
                            window.location.href = 'received_items';
                        }
                    });
                } else {
                    // Display error message with SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Error saving receive items: ' + response.message,
                    });
                }
            },
            error: function(error) {
                // Handle errors

                // Display error message with SweetAlert
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Error saving receive items',
                });

                console.error(error);
            }
        });
    }

    // Call the submitForm function when the 'Save and New' button is clicke
</script>
<script>
    $(document).ready(function() {
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

            function isFormValid() {
                var isValid = true;

                // Add validation logic for each required field
                if ($("#refNo").val() === '') {
                    isValid = false;
                    highlightInvalidField($("#refNo"));
                } else {
                    resetInvalidField($("#refNo"));
                }

                // Add validation logic for each required field
                if ($("#receiveItemDate").val() === '') {
                    isValid = false;
                    highlightInvalidField($("#receiveItemDate"));
                } else {
                    resetInvalidField($("#receiveItemDate"));
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


            submitForm();

            // Placeholder for the submitForm function
            // function submitForm() {
            //     // Add logic to submit the form
            //     console.log('Form submitted!');
            // }
        });
    });
</script>