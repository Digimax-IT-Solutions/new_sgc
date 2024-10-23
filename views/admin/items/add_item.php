<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('item_list');
$categories = Category::all();
$vendors = Vendor::all();
$uoms = Uom::all();
$accounts = ChartOfAccount::all();

// Define the list of item types
$itemTypes = [
    "Inventory",
    "Non-inventory",
    "Work in Process",
    "Service",
    "Finished Goods",
    "Subtotal",
    "Discount",
    "Tax Type",
    "Group",
    "Payment"
];

// Sort the item types alphabetically
sort($itemTypes);

?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">

            <h1 class="h3 mb-3"><strong>Add New</strong>&nbsp;Item</h1>

            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_product') ?>
                    <?php displayFlashMessage('delete_product') ?>
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <form method="POST" action="api/masterlist/product_controller.php?action=add">
                                <input type="hidden" name="action" id="modalAction" value="add" />
                                <div class="row mb-3">
                                    <label for="item_name" class="col-sm-2 col-form-label">Item Name</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="item_name" name="item_name"
                                            placeholder="Enter Item Name" required>
                                    </div>
                                    <label for="item_code" class="col-sm-2 col-form-label">Item Code</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="item_code" name="item_code"
                                            placeholder="Enter Item Code">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="item_type" class="col-sm-2 col-form-label">Item Type</label>
                                    <div class="col-sm-4">
                                        <select class="form-control" id="item_type" name="item_type">
                                            <option value="">Select Item Type</option>
                                            <?php foreach ($itemTypes as $type): ?>
                                                <option value="<?php echo $type; ?>"><?php echo $type; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <label for="item_vendor_id" class="col-sm-2 col-form-label">Preferred
                                        Vendor</label>
                                    <div class="col-sm-4">
                                        <select id="item_vendor_id" class="form-control" name="item_vendor_id">
                                            <option value="">Select Vendor</option>
                                            <?php foreach ($vendors as $vendor): ?>
                                                <option value="<?= $vendor->id ?>"><?= $vendor->vendor_name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="item_uom_id" class="col-sm-2 col-form-label">Unit of Measurement</label>
                                    <div class="col-sm-4">
                                        <select id="item_uom_id" class="form-control" name="item_uom_id">
                                            <option value="">Select Uom</option>
                                            <?php foreach ($uoms as $uom): ?>
                                                <option value="<?= $uom->id ?>"><?= $uom->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <label for="item_reorder_point" class="col-sm-2 col-form-label">Reorder
                                        Point</label>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" id="item_reorder_point"
                                            name="item_reorder_point" value="0">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="item_category_id" class="col-sm-2 col-form-label">Category</label>
                                    <div class="col-sm-4">
                                        <select id="item_category_id" class="form-control" name="item_category_id">
                                            <?php foreach ($categories as $category): ?>
                                                <option value="<?= $category->id ?>"><?= $category->name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <label for="item_quantity" class="col-sm-2 col-form-label">Initial Quantity</label>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" id="item_quantity"
                                            name="item_quantity" min="0" value="0" readonly>
                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <label for="item_sales_description" class="col-sm-2 col-form-label">Sales
                                        Description</label>
                                    <div class="col-sm-4">
                                        <input class="form-control" id="item_sales_description"
                                            name="item_sales_description" placeholder="Enter Sales Description"></input>
                                    </div>
                                    <label for="item_purchase_description" class="col-sm-2 col-form-label">Purchase
                                        Description</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="item_purchase_description"
                                            name="item_purchase_description" placeholder="Enter Purchase Description">
                                    </div>
                                </div>


                                <div class="row mb-3">
                                    <label for="item_selling_price" class="col-sm-2 col-form-label">Selling
                                        Price</label>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" id="item_selling_price"
                                            name="item_selling_price" step=".25">
                                    </div>
                                    <label for="item_cost_price" class="col-sm-2 col-form-label">Cost Price</label>
                                    <div class="col-sm-4">
                                        <input type="number" class="form-control" id="item_cost_price"
                                            name="item_cost_price" step=".25">
                                    </div>
                                </div>


                                <!-- --- -->

                                <br><br><br>

                                <div class="row mb-3">
                                    <label for="item_cogs_account_id" class="col-sm-2 col-form-label">Cost of Goods
                                        Sold
                                        Account</label>
                                    <div class="col-sm-4">
                                        <select class="form-control form-control-sm select2" id="item_cogs_account_id"
                                            name="item_cogs_account_id">
                                            <option value="0"></option>
                                            <?php foreach ($accounts as $account): ?>

                                                <option value="<?= $account->id ?>">
                                                    <?= $account->account_code ?>-<?= $account->account_description ?>
                                                </option>

                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="item_income_account_id" class="col-sm-2 col-form-label">Income
                                        Account</label>
                                    <div class="col-sm-4">
                                        <select class="form-control form-control-sm select2" id="item_income_account_id"
                                            name="item_income_account_id">
                                            <option value="0"></option>
                                            <?php foreach ($accounts as $account): ?>

                                                <option value="<?= $account->id ?>">
                                                    <?= $account->account_code ?>-<?= $account->account_description ?>
                                                </option>

                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <label for="item_asset_account_id" class="col-sm-2 col-form-label">Asset
                                        Account</label>
                                    <div class="col-sm-4">
                                        <select class="form-control form-control-sm select2" id="item_asset_account_id"
                                            name="item_asset_account_id">
                                            <option value="0"></option>
                                            <?php foreach ($accounts as $account): ?>

                                                <option value="<?= $account->id ?>">
                                                    <?= $account->account_code ?>-<?= $account->account_description ?>
                                                </option>

                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="mt-16">
                                    <button class="btn btn-primary w-full" type="submit">Add Product</button>
                                </div>
                            </form>
                        </div>

                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
</div>
</main>

<?php require 'views/templates/footer.php' ?>



<script>
    $(document).ready(function() {

        $('#item_cogs_account_id', '#item_income_account_id', '#item_asset_account_id').select2({
            theme: 'classic',
            width: '100%',
            placeholder: 'Select Account',
            allowClear: false
        });

    });
</script>