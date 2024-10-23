<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('reports');

$purchases = ReceivingReport::getPurchasesByItemDetails();

?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>

<div class="main">
    <?php require 'views/templates/navbar.php' ?>

    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-2"><strong>Reports Dashboard</strong></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page"><a href="dashboard">Reports</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Purchases By Item Details</li>
                            </ol>
                        </nav>
                    </div>

                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white text-white">
                    <h3 class="card-title mb-0">Purchases by Item Details</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="">
                                <tr>
                                    <th>Receive Date</th>
                                    <th>RR no</th>
                                    <th>Item Name</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Cost</th>
                                    <th>Gross Amount</th>
                                    <th>Discount</th>
                                    <th>Net Amount</th>
                                    <th>Input VAT</th>
                                    <th>Taxable Amount</th>
                                    <th>Cost Per Unit</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($purchases as $purchase): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($purchase['receive_date']) ?></td>
                                        <td><?= htmlspecialchars($purchase['receive_no']) ?></td>
                                        <td><?= htmlspecialchars($purchase['item_name']) ?></td>
                                        <td><?= htmlspecialchars($purchase['item_purchase_description']) ?></td>
                                        <td><?= htmlspecialchars($purchase['qty']) ?></td>
                                        <td><?= htmlspecialchars(number_format($purchase['cost'], 2)) ?></td>
                                        <td><?= htmlspecialchars(number_format($purchase['amount'], 2)) ?></td>
                                        <td><?= htmlspecialchars(number_format($purchase['discount_amount'], 2)) ?></td>
                                        <td><?= htmlspecialchars(number_format($purchase['net_amount_before_input_vat'], 2)) ?></td>
                                        <td><?= htmlspecialchars(number_format($purchase['input_vat_amount'], 2)) ?></td>
                                        <td><?= htmlspecialchars(number_format($purchase['net_amount'], 2)) ?></td>
                                        <td><?= htmlspecialchars(number_format($purchase['cost_per_unit'], 2)) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.card -->
    </main>

    <?php require 'views/templates/footer.php' ?>
</div>

<script>
    $(document).ready(function () {
        $('#purchasesTable').DataTable({
            "pageLength": 25,
            "order": [[0, "desc"]]
        });
    });
</script>