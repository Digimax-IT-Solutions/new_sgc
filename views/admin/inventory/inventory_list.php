<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('inventory');
$products = Product::all();

?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">


            <h1 class="h3 mb-3"><strong>MATERIALS AND SUPPLIES</strong> INVENTORY
                <small class="text-muted">as of <?= date('F j, Y') ?></small>
            </h1>
            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add') ?>
                    <?php displayFlashMessage('delete') ?>
                    <?php displayFlashMessage('update_product') ?>
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">

                            <br /><br /><br />
                            <table id="itemListTable" class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Product/Inventory Code</th>
                                        <th>Item Description</th>
                                        <th>Location</th>
                                        <th>Inventory Valuation Method</th>
                                        <th>Unit Price</th>
                                        <th>Qty in Stocks</th>
                                        <th>U/M</th>
                                        <th>Total Cost</th>

                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><?= $product->item_code ?></td>
                                            <td><?= $product->item_purchase_description ?></td>
                                            <th>MONTEBELLO, KANANGA, LEYTE</th>
                                            <th>FIFO</th>
                                            <td>₱<?= number_format($product->item_cost_price, 2, '.', ',') ?></td>
                                            <td><?= $product->item_quantity ?></td>
                                            <td><?= $product->uom_name ?></td>
                                            <td>₱<?= number_format($product->item_selling_price * $product->item_quantity, 2, '.', ',') ?>
                                            </td>

                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </main>
    <?php require 'views/templates/footer.php' ?>