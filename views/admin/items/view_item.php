<?php
// Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('item_list');
// Fetch data
$categories = Category::all();
$vendors = Vendor::all();
$uoms = Uom::all();
$accounts = ChartOfAccount::all();

$page = 'item_list';

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
            <h1 class="h3 mb-3"><strong></strong>&nbsp;Item</h1>

            <div class="row">
                <div class="col-12">
                    <!-- Display flash messages -->
                    <?php displayFlashMessage('add_product') ?>
                    <?php displayFlashMessage('delete_product') ?>

                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <?php if (isset($_GET['id'])) {
                                $id = $_GET['id'];

                                $item = Product::find($id);

                                if ($item) { ?>
                                    <form method="POST" action="api/masterlist/product_controller.php?action=update">
                                        <input type="hidden" name="action" value="update" />
                                        <input type="hidden" name="id" value="<?= $item ? $item->id : '' ?>" />

                                        <div class="row mb-3">
                                            <label for="item_name" class="col-sm-2 col-form-label">Item Name</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" id="item_name" name="item_name"
                                                    placeholder="Enter Item Name"
                                                    value="<?= $item ? htmlspecialchars($item->item_name) : '' ?>" required>
                                            </div>
                                            <label for="item_code" class="col-sm-2 col-form-label">Item Code</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" id="item_code" name="item_code"
                                                    placeholder="Enter Item Code"
                                                    value="<?= $item ? htmlspecialchars($item->item_code) : '' ?>" required>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="item_type" class="col-sm-2 col-form-label">Item Type</label>
                                            <div class="col-sm-4">
                                                <select class="form-control" id="item_type" name="item_type" required>
                                                    <option value="">Select Item Type</option>
                                                    <?php foreach ($itemTypes as $item_type): ?>
                                                        <option value="<?= $item_type ?>" <?= ($item && $item->item_type == $item_type) ? 'selected' : '' ?>>
                                                            <?= $item_type ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <label for="item_vendor_id" class="col-sm-2 col-form-label">Preferred Vendor</label>
                                            <div class="col-sm-4">
                                                <select id="item_vendor_id" class="form-control" name="item_vendor_id">
                                                    <option value="">Select Vendor</option>
                                                    <?php foreach ($vendors as $vendor): ?>
                                                        <option value="<?= $vendor->id ?>" <?= ($item && $item->item_vendor_id == $vendor->id) ? 'selected' : '' ?>>
                                                            <?= $vendor->vendor_name ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="item_uom_id" class="col-sm-2 col-form-label">Unit of Measurement</label>
                                            <div class="col-sm-4">
                                                <select id="item_uom_id" class="form-control" name="item_uom_id" required>
                                                    <option value="">Select Uom</option>
                                                    <?php foreach ($uoms as $uom): ?>
                                                        <option value="<?= $uom->id ?>" <?= ($item && $item->item_uom_id == $uom->id) ? 'selected' : '' ?>>
                                                            <?= $uom->name ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <label for="item_reorder_point" class="col-sm-2 col-form-label">Reorder
                                                Point</label>
                                            <div class="col-sm-4">
                                                <input type="number" class="form-control" id="item_reorder_point"
                                                    name="item_reorder_point"
                                                    value="<?= $item ? $item->item_reorder_point : '0' ?>">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="item_category_id" class="col-sm-2 col-form-label">Category</label>
                                            <div class="col-sm-4">
                                                <select id="item_category_id" class="form-control" name="item_category_id"
                                                    required>
                                                    <option value="">Select Category</option>
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?= $category->id ?>" <?= ($item && $item->item_category_id == $category->id) ? 'selected' : '' ?>>
                                                            <?= $category->name ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <label for="item_quantity" class="col-sm-2 col-form-label">Quantity on hand</label>
                                            <div class="col-sm-4">
                                                <input type="number" class="form-control" id="item_quantity"
                                                    name="item_quantity" required min="0"
                                                    value="<?= $item ? $item->item_quantity : '1' ?>" disabled>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="item_sales_description" class="col-sm-2 col-form-label">Sales
                                                Description</label>
                                            <div class="col-sm-4">
                                                <input class="form-control" id="item_sales_description"
                                                    name="item_sales_description" placeholder="Enter Sales Description"
                                                    value="<?= $item ? htmlspecialchars($item->item_sales_description) : '' ?>">
                                            </div>
                                            <label for="item_purchase_description" class="col-sm-2 col-form-label">Purchase
                                                Description</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" id="item_purchase_description"
                                                    name="item_purchase_description" placeholder="Enter Purchase Description"
                                                    value="<?= $item ? htmlspecialchars($item->item_purchase_description) : '' ?>">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="item_selling_price" class="col-sm-2 col-form-label">Selling
                                                Price</label>
                                            <div class="col-sm-4">
                                                <input type="number" class="form-control" id="item_selling_price"
                                                    name="item_selling_price" step=".25"
                                                    value="<?= $item ? $item->item_selling_price : '' ?>">
                                            </div>
                                            <label for="item_cost_price" class="col-sm-2 col-form-label">Cost Price</label>
                                            <div class="col-sm-4">
                                                <input type="number" class="form-control" id="item_cost_price"
                                                    name="item_cost_price" step=".25"
                                                    value="<?= $item ? $item->item_cost_price : '' ?>">
                                            </div>
                                        </div>

                                        <br><br><br>


                                        <div class="row mb-3">
                                            <label for="item_cogs_account_id" class="col-sm-2 col-form-label">Cost of Goods Sold Account</label>
                                            <div class="col-sm-4">
                                                <select class="form-control form-control-sm select2" id="item_cogs_account_id" name="item_cogs_account_id">
                                                    <option value=""></option>
                                                    <?php foreach ($accounts as $acc): ?>

                                                        <option
                                                            value="<?= htmlspecialchars($acc->id) ?>"
                                                            <?= $acc->id == $item->item_cogs_account_id ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($acc->account_code . ' - ' . $acc->account_description) ?>
                                                        </option>

                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="item_income_account_id" class="col-sm-2 col-form-label">Income Account</label>
                                            <div class="col-sm-4">
                                                <select class="form-control form-control-sm select2" id="item_income_account_id" name="item_income_account_id">
                                                    <option value=""></option>
                                                    <?php foreach ($accounts as $account): ?>

                                                        <option
                                                            value="<?= htmlspecialchars($account->id) ?>"
                                                            <?= ($item && $item->item_income_account_id == $account->id) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($account->account_code) ?> - <?= htmlspecialchars($account->account_description) ?>
                                                        </option>

                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="item_asset_account_id" class="col-sm-2 col-form-label">Asset Account</label>
                                            <div class="col-sm-4">
                                                <select class="form-control form-control-sm select2" id="item_asset_account_id" name="item_asset_account_id">
                                                    <option value=""></option>
                                                    <?php foreach ($accounts as $account): ?>
                                                        <option
                                                            value="<?= htmlspecialchars($account->id) ?>"
                                                            <?= ($item && $item->item_asset_account_id == $account->id) ? 'selected' : '' ?>>
                                                            <?= htmlspecialchars($account->account_code) ?> - <?= htmlspecialchars($account->account_description) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>


                                        <div class="mt-3">
                                            <button class="btn btn-primary" type="submit">Submit</button>
                                        </div>
                                    </form>
                            <?php
                                    // Invoice found, you can now display the details
                                } else {
                                    // Handle the case where the invoice is not found
                                    echo "Receive_items not found.";
                                    exit;
                                }
                            } else {
                                // Handle the case where the ID is not provided
                                echo "No ID provided.";
                                exit;
                            }
                            ?>
                        </div>

                    </div>

                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </main>
    <!-- /.content -->
</div>
<!-- /.main -->

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