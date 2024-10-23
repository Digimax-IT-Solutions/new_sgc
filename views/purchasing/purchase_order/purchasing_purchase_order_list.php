<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('purchasing_purchase_order');
$purchase_orders = PurchaseOrder::all();

// Calculate summary statistics
$totalPurchaseOrders = array_sum(array_column($purchase_orders, 'total_amount'));
$totalReceived = array_sum(array_column($purchase_orders, 'received_amount'));
$totalWaiting = array_sum(array_column($purchase_orders, 'waiting_amount'));
$totalPastDue = array_sum(array_column($purchase_orders, 'past_due_amount'));

$page = 'purchase_order';
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <main class="content">
        <div class="container-fluid p-0">
            <div class="mb-3">
                <h1 class="h3 d-inline align-middle"><strong>Purchase</strong> Orders</h1>
                <nav aria-label="breadcrumb" class="float-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Purchase Orders</li>
                    </ol>
                </nav>
            </div>

            <?php displayFlashMessage('add_purchase_order') ?>
            <?php displayFlashMessage('delete_purchase_order') ?>
            <?php displayFlashMessage('update_purchase_order') ?>

            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total
                                        Purchase Orders</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ₱<?= number_format($totalPurchaseOrders, 2) ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Received
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ₱<?= number_format($totalReceived, 2) ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Waiting For
                                        Delivery</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ₱<?= number_format($totalWaiting, 2) ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-danger shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Past Due</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ₱<?= number_format($totalPastDue, 2) ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Purchase Orders</h6>
                    <div>
                        <a href="purchasing_draft_purchase_order" class="btn btn-sm btn-danger">
                            <i class="fab fa-firstdraft"></i> Draft
                        </a>
                        <a href="purchasing_void_purchase_order" class="btn btn-sm btn-secondary">
                            <i class="fas fa-ban"></i> Void
                        </a>
                        <a href="purchasing_create_purchase_order" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> New Purchase Order
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Purchase ID</th>
                                    <th>Vendor</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($purchase_orders as $order): ?>
                                    <?php if ($order->po_status != 3 && $order->po_status != 4): // Exclude invoices with status 3 and 4 
                                                ?>
                                        <tr>
                                            <td><?= $order->po_no ?></td>
                                            <td><?= $order->vendor_name ?></td>
                                            <td><?= date('M d, Y', strtotime($order->date)) ?></td>
                                            <td class="text-right">₱<?= number_format($order->total_amount, 2) ?></td>
                                            <td class="text-center">
                                                <?php
                                                switch ($order->po_status) {
                                                    case 0:
                                                        echo '<span class="badge bg-warning">Waiting for delivery</span>';
                                                        break;
                                                    case 1:
                                                        echo '<span class="badge bg-success">Received</span>';
                                                        break;
                                                    case 2:
                                                        echo '<span class="badge bg-info">Partially Received</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="badge bg-secondary">Unknown</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <a href="purchasing_view_purchase_order?action=update&id=<?= $order->id ?>"
                                                    class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require 'views/templates/footer.php' ?>

<script>
    $(document).ready(function () {
        $('#dataTable').DataTable({
            "order": [[2, "desc"]],
            "pageLength": 25
        });
    });
</script>