<?php
//Guard
require_once '_guards.php';
Guard::purchasingOnly();

$receiveItems = ReceivingReport::all();

// Calculate summary statistics
$totalReceived = array_sum(array_column($receiveItems, 'total_amount'));
$totalPaid = array_sum(array_column($receiveItems, 'paid_amount'));
$totalUnpaid = $totalReceived - $totalPaid;
$totalOverdue = array_sum(array_column($receiveItems, 'overdue_amount'));

$page = 'receive_items';
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/purchasing_sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/purchasing_navbar.php' ?>
    <main class="content">
        <div class="container-fluid p-0">
            <div class="mb-3">
                <h1 class="h3 d-inline align-middle"><strong>Receive</strong> Items List</h1>
                <nav aria-label="breadcrumb" class="float-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Receive Items</li>
                    </ol>
                </nav>
            </div>

            <?php displayFlashMessage('add_received_items') ?>
            <?php displayFlashMessage('delete_payment_method') ?>
            <?php displayFlashMessage('update_payment_method') ?>

            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total
                                        Received Items</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ₱<?= number_format($totalReceived, 2) ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-truck fa-2x text-gray-300"></i>
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
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Paid</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ₱<?= number_format($totalPaid, 2) ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
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
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Unpaid</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ₱<?= number_format($totalUnpaid, 2) ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
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
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Overdue</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        ₱<?= number_format($totalOverdue, 2) ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar-times fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Received Items</h6>
                    <div>
                        <a href="upload" class="btn btn-sm btn-outline-secondary me-2">
                            <i class="fas fa-upload"></i> Upload
                        </a>
                        <a href="purchasing_create_receive_item" class="btn btn-sm btn-primary">
                            <i class="fas fa-file-alt"></i> Receive Items w/ Bill
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Received Items No.</th>
                                    <th>Vendor</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($receiveItems as $receiveItem): ?>
                                    <tr>
                                        <td><?= $receiveItem->receive_no ?></td>
                                        <td><?= $receiveItem->vendor_name ?></td>
                                        <td><?= date('M d, Y', strtotime($receiveItem->receive_date)) ?></td>
                                        <td class="text-right">₱<?= number_format($receiveItem->total_amount, 2) ?></td>
                                        <td class="text-center">
                                            <?php
                                            switch ($receiveItem->receive_status) {
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
                                            <a href="purchasing_view_receive_item?action=update&id=<?= $receiveItem->id ?>"
                                                class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
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
            "ordering": false,
            "pageLength": 25
        });
    });
</script>