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
        background-color: rgb(0, 149, 77);
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
                        <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="sales_invoice">Vendor Center</a></li>
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
                                        <label for="vendorSelect">VENDOR</label>
                                        <div class="input-group">
                                            <!-- Add this inside the vendor input-group div -->
                                            <!-- Place your vendor list here -->
                                            <select class="form-control" id="vendorSelect" name="vendorSelect" onchange="checkOpenPOs()">
    <option value="">Select vendor</option>
    <?php
    // Fetch vendors with purchase orders having poStatus = 'WAITING FOR DELIVERY' from the database and populate the dropdown in the modal
    $vendorQuery = "SELECT DISTINCT purchase_order.vendor, purchase_order.poID 
                    FROM purchase_order 
                    WHERE purchase_order.poStatus = 'WAITING FOR DELIVERY'";

    try {
        $vendorStmt = $db->prepare($vendorQuery);
        $vendorStmt->execute();

        $vendors = $vendorStmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($vendors as $vendor) {
            echo "<option value='{$vendor['vendor']}' data-poid='{$vendor['poID']}'>{$vendor['vendor']}</option>";
        }
    } catch (PDOException $e) {
        // Handle the exception, e.g., log the error or return an error message
        echo "<option value=''>Error fetching vendors</option>";
    }
    ?>
</select>
                                        </div>
                                        <br><br>
                                        <button type="button" class="btn btn-primary" id="openPOsButton" style="display: none;  background-color: rgb(0, 149, 77);">Open Purchase
                                            Orders</button>

                                    </div>
                                    <div class="form-group col-md-2">
                                    </div>
                                    <div class="form-group col-md-3">
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
                                        <label for="total">TOTAL</label>
                                        <div class="total" style="font-size: 30px;">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="total" name="total" value="" readonly>
                                            </div>
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
                                                <!-- <th>PO NO.</th> -->

                                            </tr>
                                        </thead>
                                        <tbody id="itemTableBody">
                                            <!-- Each row represents a separate item -->
                                            <!-- You can dynamically add rows using JavaScript/jQuery -->
                                        </tbody>
                                    </table>
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

<script>
    // Function to send form data
    function submitForm() {
        var formData = {
            vendorSelect: $('#vendorSelect').val(),
            receiveItemDate: $('#receiveItemDate').val(),
            refNo: $('#refNo').val(),
            total: $('#total').val(),
            memo: $('#memo').val(),
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

                // Display success message with SweetAlert
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Receive items saved successfully',
                }).then((result) => {
                    // Redirect to the sales_invoice page after clicking OK
                    if (result.isConfirmed) {
                        window.location.href = 'received_items';
                    }
                });;

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

    // Call the submitForm function when the 'Save and New' button is clicked
    $('#saveAndNewButton').on('click', function() {
        submitForm();
    });
</script>