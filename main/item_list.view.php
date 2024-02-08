<?php include __DIR__ . ('/includes/header.php'); ?>
<?php require 'connect.php'; ?>
<style>

#itemsTable {
    border-collapse: collapse;
    width: 100%;
}

#itemsTable th,
#itemsTable td {
    padding: 2px;
    /* Adjust the padding as needed */
}
#itemsTable tbody tr:hover {
    color: white;
    background-color: maroon; /* Set your desired background color here */
}
</style>
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Item List</h1>
                </div>
            </div>
        </div>
    </div>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-left">
                                        <li class="breadcrumb-item"><a style="color:maroon;" href="dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Master List</li>
                                        <li class="breadcrumb-item active">Item List</li>
                                    </ol>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <button class="btn btn-success" data-toggle="modal" data-target="#addItemModal"
                                            style="background-color: rgb(0, 149, 77); color: white;">
                                            New Item
                                        </button>
                                    </ol>
                                </div>
                            </div>
                            <br><br>
                            <table id="itemsTable" class="tabl table-hover table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <!-- <th>ITEM ID</th> -->
                                        <th>CODE</th>
                                        <th>ITEM NAME</th>
                                        <th>ITEM TYPE</th>
                                        <th>QTY</th>
                                        <th>UOM</th>
                                        <th>AS OF</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Your items data will go here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include('includes/footer.php'); ?>
</div>
<!-- Add Item Modal -->
<div class="modal" id="addItemModal" tabindex="-1" role="dialog" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                <h5 class="modal-title" id="addItemModalLabel"><b>ADD NEW ITEM</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background-color: rgb(0,149,77);">
                <form id="addItemForm">
                    <div class="row" style="padding: 20px; color: white;">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="itemName">ITEM NAME</label>
                                <input type="text" class="form-control" id="itemName" name="itemName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="itemCode">ITEM CODE</label>
                                <input type="text" class="form-control" id="itemCode" name="itemCode" required>
                            </div>
                            <div class="form-group">
                                <label for="itemType">ITEM TYPE</label>
                                <select class="form-control" id="itemType" name="itemType">
                                    <option value=""></option>
                                    <option value="Inventory">Inventory</option>
                                    <option value="Non-inventory">Non-inventory</option>
                                    <option value="Work in Process">Work in Process</option>
                                    <option value="Service">Service</option>
                                    <option value="Finished Goods">Finished Goods</option>
                                    <option value="Subtotal">Subtotal</option>
                                    <option value="Discount">Discount</option>
                                    <option value="Tax Type">Tax Type</option>
                                    <option value="Group">Group</option>
                                    <option value="Payment">Payment</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">

                            <?php
                            try {
                                $stmt = $db->prepare('SELECT vendorName FROM vendors');
                                $stmt->execute();
                                $vendorNames = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage();
                                exit;
                            }
                            ?>
                            <div class="form-group">
                                <label for="preferredVendor">PREFERRED VENDOR</label>
                                <select class="form-control" id="preferredVendor" name="preferredVendor" required>
                                    <option value=""></option>
                                    <?php foreach ($vendorNames as $vendorName) : ?>
                                    <option value="<?= $vendorName['vendorName']; ?>">
                                        <?= $vendorName['vendorName']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="reOrderPoint">RE-ORDER POINT</label>
                                <input type="text" class="form-control" id="reOrderPoint" name="reOrderPoint" required>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="padding: 20px; color: white; border-radius: 10px;">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="itemSalesInfo">SALES DESCRIPTION</label>
                                <input type="text" class="form-control" id="itemSalesInfo" name="itemSalesInfo"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="itemSrp">SELLING PRICE</label>
                                <input type="text" class="form-control" id="itemSrp" name="itemSrp" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="itemPurchaseInfo">PURCHASE DESCRIPTION</label>
                                <input type="text" class="form-control" id="itemPurchaseInfo" name="itemPurchaseInfo"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="itemCost">COST</label>
                                <input type="text" class="form-control" id="itemCost" name="itemCost" required>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <?php
                            try {
                                $stmt = $db->prepare('SELECT category_name FROM categories');
                                $stmt->execute();
                                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage();
                                exit;
                            }
                            ?>
                            <div class="form-group">
                                <label for="itemCategory">CATEGORY</label>
                                <select class="form-control" id="itemCategory" name="itemCategory" required>
                                    <option value=""></option>
                                    <?php foreach ($categories as $category) : ?>
                                    <option value="<?= $category['category_name']; ?>">
                                        <?= $category['category_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php
                            try {
                                $stmt = $db->prepare('SELECT uomName FROM uom');
                                $stmt->execute();
                                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage();
                                exit;
                            }
                            ?>
                            <div class="form-group">
                                <label for="uom">UOM</label>
                                <select class="form-control" id="uom" name="uom" required>
                                    <option value=""></option>
                                    <?php foreach ($categories as $category) : ?>
                                    <option value="<?= $category['uomName']; ?>">
                                        <?= $category['uomName']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <?php

                            try {
                                // Fetch only "Cost of Goods Sold" account types from the charts_of_accounts table
                                $stmt = $db->prepare('SELECT account_name FROM chart_of_accounts WHERE account_type = "Cost of Goods Sold"');
                                $stmt->execute();
                                $cogsAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage();
                                exit;
                            }
                            ?>
                            <!-- Set the retrieved COGS account types in your dropdown -->
                            <div class="form-group">
                                <label for="itemCogsAccount">COGS ACCOUNT</label>
                                <select class="form-control" id="itemCogsAccount" name="itemCogsAccount" required>
                                    <option value=""></option>
                                    <?php foreach ($cogsAccounts as $cogsAccount) : ?>
                                    <option value="<?= $cogsAccount['account_name']; ?>">
                                        <?= $cogsAccount['account_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <?php
                            try {
                                // Fetch only "Cost of Goods Sold" account types from the charts_of_accounts table
                                $stmt = $db->prepare('SELECT account_name FROM chart_of_accounts WHERE account_type = "Income"');
                                $stmt->execute();
                                $incomeAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage();
                                exit;
                            }
                            ?>
                            <!-- Set the retrieved COGS account types in your dropdown -->
                            <div class="form-group">
                                <label for="itemIncomeAccount">INCOME ACCOUNT</label>
                                <select class="form-control" id="itemIncomeAccount" name="itemIncomeAccount" required>
                                    <option value=""></option>
                                    <?php foreach ($incomeAccounts as $incomeAccount) : ?>
                                    <option value="<?= $incomeAccount['account_name']; ?>">
                                        <?= $incomeAccount['account_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">

                            <?php
                            try {
                                // Fetch only "Cost of Goods Sold" account types from the charts_of_accounts table
                                $stmt = $db->prepare('SELECT account_name FROM chart_of_accounts WHERE account_type = "Other Current Assets"');
                                $stmt->execute();
                                $assetAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage();
                                exit;
                            }
                            ?>
                            <!-- Set the retrieved COGS account types in your dropdown -->
                            <div class="form-group">
                                <label for="itemAssetsAccount">ASSETS ACCOUNT</label>
                                <select class="form-control" id="itemAssetsAccount" name="itemAssetsAccount" required>
                                    <option value=""></option>
                                    <?php foreach ($assetAccounts as $assetAccount) : ?>
                                    <option value="<?= $assetAccount['account_name']; ?>">
                                        <?= $assetAccount['account_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="saveItemButton">Save Item</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Edit Location Modal -->
<div class="modal" id="editItemModal" tabindex="-1" role="dialog" aria-labelledby="editItemModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: rgb(0,149,77); color: white;">
                <h5 class="modal-title" id="editItemModalLabel"><b>EDIT ITEM</b></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="background-color: rgb(0,149,77);">
                <!-- Add your form fields for new item here -->
                <form id="editItemForm">
                    <div class="row" style="padding: 20px; color: white;">
                        <input type="text" class="form-control" id="editItemID" name="editItemID" hidden>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="editItemName">ITEM NAME</label>
                                <input type="text" class="form-control" id="editItemName" name="editItemName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="editItemCode">ITEM CODE</label>
                                <input type="text" class="form-control" id="editItemCode" name="editItemCode" required>
                            </div>
                            <div class="form-group">
                                <label for="editItemType">ITEM TYPE</label>
                                <select class="form-control" id="editItemType" name="editItemType">
                                    <option value=""></option>
                                    <option value="Inventory">Inventory</option>
                                    <option value="Non-inventory">Non-inventory</option>
                                    <option value="Work in Process">Work in Process</option>
                                    <option value="Service">Service</option>
                                    <option value="Finished Goods">Finished Goods</option>
                                    <option value="Subtotal">Subtotal</option>
                                    <option value="Discount">Discount</option>
                                    <option value="Tax Type">Tax Type</option>
                                    <option value="Group">Group</option>
                                    <option value="Payment">Payment</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?php
                            try {
                                $stmt = $db->prepare('SELECT vendorName FROM vendors');
                                $stmt->execute();
                                $vendorNames = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage();
                                exit;
                            }
                            ?>
                            <div class="form-group">
                                <label for="editPreferredVendor">PREFERRED VENDOR</label>
                                <select class="form-control" id="editPreferredVendor" name="editPreferredVendor"
                                    required>
                                    <option value=""></option>
                                    <?php foreach ($vendorNames as $vendorName) : ?>
                                    <option value="<?= $vendorName['vendorName']; ?>">
                                        <?= $vendorName['vendorName']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="editReOrderPoint">RE-ORDER POINT</label>
                                <input type="text" class="form-control" id="editReOrderPoint" name="editReOrderPoint"
                                    required>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="padding: 20px; color: white; border-radius: 10px;">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="editItemSalesInfo">SALES DESCRIPTION</label>
                                <input type="text" class="form-control" id="editItemSalesInfo" name="editItemSalesInfo"
                                    required>
                            </div>
                            <div class="form-group">
                                <label for="editItemSrp">SELLING PRICE</label>
                                <input type="text" class="form-control" id="editItemSrp" name="editItemSrp" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="editItemPurchaseInfo">PURCHASE DESCRIPTION</label>
                                <input type="text" class="form-control" id="editItemPurchaseInfo"
                                    name="editItemPurchaseInfo" required>
                            </div>
                            <div class="form-group">
                                <label for="editItemCost">COST</label>
                                <input type="text" class="form-control" id="editItemCost" name="editItemCost" required>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <?php
                            try {
                                $stmt = $db->prepare('SELECT category_name FROM categories');
                                $stmt->execute();
                                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage();
                                exit;
                            }
                            ?>
                            <div class="form-group">
                                <label for="editItemCategory">CATEGORY</label>
                                <select class="form-control" id="editItemCategory" name="editItemCategory" required>
                                    <option value=""></option>
                                    <?php foreach ($categories as $category) : ?>
                                    <option value="<?= $category['category_name']; ?>">
                                        <?= $category['category_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php
                            try {
                                $stmt = $db->prepare('SELECT uomName FROM uom');
                                $stmt->execute();
                                $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage();
                                exit;
                            }
                            ?>
                            <div class="form-group">
                                <label for="editUom">UOM</label>
                                <select class="form-control" id="editUom" name="editUom" required>
                                    <option value=""></option>
                                    <?php foreach ($categories as $category) : ?>
                                    <option value="<?= $category['uomName']; ?>">
                                        <?= $category['uomName']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <?php

                            try {
                                // Fetch only "Cost of Goods Sold" account types from the charts_of_accounts table
                                $stmt = $db->prepare('SELECT account_name FROM chart_of_accounts WHERE account_type = "Cost of Goods Sold"');
                                $stmt->execute();
                                $cogsAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage();
                                exit;
                            }
                            ?>
                            <!-- Set the retrieved COGS account types in your dropdown -->
                            <div class="form-group">
                                <label for="editItemCogsAccount">COGS ACCOUNT</label>
                                <select class="form-control" id="editItemCogsAccount" name="editItemCogsAccount"
                                    required>
                                    <option value=""></option>
                                    <?php foreach ($cogsAccounts as $cogsAccount) : ?>
                                    <option value="<?= $cogsAccount['account_name']; ?>">
                                        <?= $cogsAccount['account_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                        </div>
                        <div class="col-md-4">
                            <?php
                            try {
                                // Fetch only "Cost of Goods Sold" account types from the charts_of_accounts table
                                $stmt = $db->prepare('SELECT account_name FROM chart_of_accounts WHERE account_type = "Income"');
                                $stmt->execute();
                                $incomeAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage();
                                exit;
                            }
                            ?>
                            <!-- Set the retrieved COGS account types in your dropdown -->
                            <div class="form-group">
                                <label for="editItemIncomeAccount">INCOME ACCOUNT</label>
                                <select class="form-control" id="editItemIncomeAccount" name="editItemIncomeAccount"
                                    required>
                                    <option value=""></option>
                                    <?php foreach ($incomeAccounts as $incomeAccount) : ?>
                                    <option value="<?= $incomeAccount['account_name']; ?>">
                                        <?= $incomeAccount['account_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">

                            <?php
                            try {
                                // Fetch only "Cost of Goods Sold" account types from the charts_of_accounts table
                                $stmt = $db->prepare('SELECT account_name FROM chart_of_accounts WHERE account_type = "Other Current Assets"');
                                $stmt->execute();
                                $assetAccounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            } catch (PDOException $e) {
                                echo "Error: " . $e->getMessage();
                                exit;
                            }
                            ?>
                            <!-- Set the retrieved COGS account types in your dropdown -->
                            <div class="form-group">
                                <label for="editItemAssetsAccount">ASSETS ACCOUNT</label>
                                <select class="form-control" id="editItemAssetsAccount" name="editItemAssetsAccount"
                                    required>
                                    <option value=""></option>
                                    <?php foreach ($assetAccounts as $assetAccount) : ?>
                                    <option value="<?= $assetAccount['account_name']; ?>">
                                        <?= $assetAccount['account_name']; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-success" id="saveEditItemButton">Save Changes</button>

                </div>
            </div>
        </div>
    </div>
</div>
<script>
$(document).ready(function() {
    $("#saveItemButton").click(function() {
        // Get form data
        var formData = $("#addItemForm").serialize();

        // AJAX request to store data
        $.ajax({
            type: "POST",
            url: "modules/masterlist/items/save_item.php",
            data: formData,
            success: function(response) {
                // Use SweetAlert2 for displaying success or error message
                if (response === "success") {
                    Swal.fire({
                        icon: 'success',
                        title: 'Item saved successfully!',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        // Reload the browser after SweetAlert2 is closed
                        location.reload();
                    });
                    $("#addItemModal").modal("hide");

                    // Update the table after successfully saving the item
                    populateItemsTable();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'ERROR',
                        text: response, // Display the MySQL error message
                        showConfirmButton: false,
                        timer: 5000 // Adjust the timer as needed
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'ERROR',
                    text: response, // Display the MySQL error message
                    showConfirmButton: false,
                    timer: 5000 // Adjust the timer as needed
                });
            }
        });
    });
    // Variable to check if DataTables is already initialized
    var dataTableInitialized = false;
    // Function to fetch and populate data
    function populateItemsTable() {
        $.ajax({
            type: "GET",
            url: "modules/masterlist/items/get_items.php", // Adjust the URL to the server-side script
            success: function(response) {
                // Parse the JSON response
                var items = JSON.parse(response);

                // Clear existing table rows
                $("#itemsTable tbody").empty();

                // Inside the items.forEach() loop where you populate the table
                items.forEach(function(item) {
                    var row = `<tr>
                    <td>${item.itemCode}</td>
                    <td>${item.itemName}</td>
                    <td>${item.itemType}</td>
                    <td>${item.itemQty}</td>
                    <td>${item.uom || ''}</td>
                    <td>${new Date(item.createdAt).toLocaleDateString()}</td>
                    <td>
                    <button type="button" class="btn btn-primary btn-sm editItemButton" style="background-color: rgb(0, 149, 77); color: white; border: 1px rgb(0, 149, 77);" data-id="${item.itemID}">Edit</button>
                    <button type="button" class="btn btn-danger btn-sm deleteItemButton" data-id="${item.itemID}">Delete</button></td>
                </tr>`;
                    $("#itemsTable tbody").append(row);


                });

                // Initialize DataTables only if it's not already initialized
                if (!dataTableInitialized) {
                    $('#itemsTable').DataTable({
                        "paging": true,
                        "lengthChange": true,
                        "searching": true,
                        "info": true,
                        "autoWidth": true,
                        "lengthMenu": [10, 25, 50, 100],
                        "ordering": false, // Disable sorting for all columns
                        "dom": 'lBfrtip',
                        "buttons": [{
                                extend: 'copy',
                                exportOptions: {
                                    columns: ':not(:last-child)' // Exclude the last column (ACTION)
                                }
                            },
                            {
                                extend: 'csv',
                                exportOptions: {
                                    columns: ':not(:last-child)' // Exclude the last column (ACTION)
                                }
                            },
                            {
                                extend: 'excel',
                                exportOptions: {
                                    columns: ':not(:last-child)' // Exclude the last column (ACTION)
                                }
                            },
                            {
                                extend: 'pdf',
                                exportOptions: {
                                    columns: ':not(:last-child)' // Exclude the last column (ACTION)
                                }
                            },
                            {
                                extend: 'print',
                                exportOptions: {
                                    columns: ':not(:last-child)' // Exclude the last column (ACTION)
                                }
                            },
                        ],
                        "oLanguage": {
                            "sSearch": "Search:",
                            "sLengthMenu": "Show _MENU_ entries",
                            "sInfo": "Showing _START_ to _END_ of _TOTAL_ entries",
                            "sInfoEmpty": "Showing 0 to 0 of 0 entries",
                            "sInfoFiltered": "(filtered from _MAX_ total entries)",
                            "oPaginate": {
                                "sFirst": "First",
                                "sLast": "Last",
                                "sNext": "Next",
                                "sPrevious": "Previous"
                            }
                        }
                    });

                    // Set the flag to indicate DataTables is now initialized
                    dataTableInitialized = true;

                } else {
                    // If DataTables is already initialized, destroy and recreate it
                    $('#itemsTable').DataTable().destroy();
                    $('#itemsTable').DataTable({
                        // Your DataTables options here
                    });
                }
            },
            error: function() {
                console.log("Error fetching data.");
            }
        });
    }
    // Initial population when the page loads
    populateItemsTable();

    // Function to display an error using SweetAlert2
    function displayError(errorMessage) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: errorMessage,
            showConfirmButton: false,
            timer: 5000
        });
    }
});
// Edit Location functionality
$("#itemsTable").on("click", ".editItemButton", function() {
    var itemID = $(this).data("id");

    // Populate the edit modal with location data
    $.ajax({
        type: "GET",
        url: "modules/masterlist/items/get_item_details.php", // Replace with your server-side script
        data: {
            itemID: itemID
        },
        success: function(response) {
            var itemDetails = JSON.parse(response);
            // Populate the edit modal with item details
            $("#editItemID").val(itemDetails.itemID);
            $("#editItemName").val(itemDetails.itemName);
            $("#editItemCode").val(itemDetails.itemCode);
            $("#editItemType").val(itemDetails.itemType);
            $("#editPreferredVendor").val(itemDetails.preferredVendor);
            $("#editReOrderPoint").val(itemDetails.reOrderPoint);
            $("#editItemSalesInfo").val(itemDetails.itemSalesInfo);
            $("#editItemSrp").val(itemDetails.itemSrp);
            $("#editItemPurchaseInfo").val(itemDetails.itemPurchaseInfo);
            $("#editItemCost").val(itemDetails.itemCost);
            $("#editItemCategory").val(itemDetails.itemCategory);
            $("#editUom").val(itemDetails.uom);
            $("#editItemCogsAccount").val(itemDetails.itemCogsAccount);
            $("#editItemIncomeAccount").val(itemDetails.itemIncomeAccount);
            $("#editItemAssetsAccount").val(itemDetails.itemAssetsAccount);
            // Show the edit modal
            $("#editItemModal").modal("show");
        },
        error: function() {
            console.log("Error fetching location details for edit.");
        }
    });
});
// Save Edit Account Changes functionality
$("#saveEditItemButton").click(function() {
    var formData = $("#editItemForm").serialize();

    $.ajax({
        type: "POST",
        url: "modules/masterlist/items/update_item.php", // Replace with your server-side script
        data: formData,
        success: function(response) {
            if (response === "success") {
                Swal.fire({
                    icon: 'success',
                    title: 'Item updated successfully!',
                    showConfirmButton: false,
                    timer: 1500
                }).then(function() {
                    // Reload the browser after SweetAlert2 is closed
                    location.reload();
                });
            } else {
                // Display the error message in SweetAlert2
                displayError(response);
            }
        },
        error: function() {
            displayError("An unexpected error occurred. Please try again.");
        }
    });
});

// Delete Location functionality
$("#itemsTable").on("click", ".deleteItemButton", function() {
    var itemID = $(this).data("id");

    // Display a confirmation dialog before deleting
    Swal.fire({
        title: 'Are you sure?',
        text: 'You won\'t be able to revert this!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Delete the location via AJAX
            $.ajax({
                type: "POST",
                url: "modules/masterlist/items/delete_item.php",
                data: {
                    deleteItemID: itemID
                },
                success: function(response) {
                    if (response === "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Item deleted successfully!',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            // Reload the browser after SweetAlert2 is closed
                            location.reload();
                        });
                        // // Update the table after successfully deleting the location
                        // populateItemsTable();
                    } else {
                        // Display the error message in SweetAlert2
                        displayError(response);
                    }
                },
                error: function() {
                    displayError("An unexpected error occurred. Please try again.");
                }
            });
        }
    });
});
// Function to display an error using SweetAlert2
function displayError(errorMessage) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: errorMessage,
        showConfirmButton: false,
        timer: 5000 // Adjust the timer as needed
    });
}
</script>