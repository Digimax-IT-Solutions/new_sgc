<?php
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('invoice');


$invoices = Invoice::all();
$totalCount = Invoice::getTotalCount();
$unpaidInvoice = Invoice::getUnpaidCount();
$paidInvoice = Invoice::getPaidCount();
$overdueCount = Invoice::getOverdueCount();

$totalAmount = array_sum(array_column($invoices, 'total_amount_due'));
$totalPaid = array_sum(array_column($invoices, 'paid_amount'));
$totalUnpaid = $totalAmount - $totalPaid;
$totalOverdue = array_sum(array_column($invoices, 'overdue_amount'));

$page = 'invoices';
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <main class="content">
        <div class="container-fluid p-0">
            <div class="mb-3">
                <h1 class="h3 d-inline align-middle"><strong>Sales</strong> Invoice</h1>
                <nav aria-label="breadcrumb" class="float-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Sales Invoices</li>
                    </ol>
                </nav>
            </div>

            <?php displayFlashMessage('add_invoice') ?>
            <?php displayFlashMessage('delete_invoice') ?>
            <?php displayFlashMessage('update_invoice') ?>

            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                </div>


                    <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <a href="invoice" class="btn btn-lg btn-outline-secondary me-2 mb-2">
                                    <i class="fas fa-arrow-left  fa-lg me-2"></i> Go Back
                            </a>
                            <a href="create_invoice" class="btn btn-lg btn-outline-success me-2 mb-2">
                                <i class="fas fa-plus fa-lg me-2"></i> Create Invoice
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Invoice No.</th>
                                        <th>Customer</th>
                                        <th>Memo</th>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Balance</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($invoices as $invoice): ?>
                                        <?php if ($invoice->invoice_status == 4): // Only include invoices with status 3 ?>
                                            <tr>
                                                <td><?= $invoice->invoice_number ?></td>
                                                <td><?= $invoice->customer_name ?></td>
                                                <td><?= $invoice->memo ?></td>
                                                <td><?= date('M d, Y', strtotime($invoice->invoice_date)) ?></td>
                                                <td class="text-right">₱<?= number_format($invoice->total_amount_due, 2) ?></td>
                                                <td class="text-center">
                                                    <?php
                                                    switch ($invoice->invoice_status) {
                                                        case 4:
                                                            echo '<span class="badge bg-info text-dark">Draft</span>'; // Changed bg-warning to bg-secondary for better visibility
                                                            break;
                                                        default:
                                                            echo '<span class="badge bg-secondary">Unknown</span>';
                                                    }
                                                    ?>
                                                </td>
                                                <td class="text-right">₱<?= number_format($invoice->balance_due, 2) ?></td>
                                                <td>
                                                    <a href="view_invoice?action=update&id=<?= $invoice->id ?>"
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
            "order": [[3, "desc"]],
            "pageLength": 25,
            "language": {
                "search": "Search:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "paginate": {
                    "first": "First",
                    "last": "Last",
                    "next": "Next",
                    "previous": "Previous"
                }
            },
            "columnDefs": [
                { "orderable": false, "targets": 7 }
            ]
        });
    });
</script>