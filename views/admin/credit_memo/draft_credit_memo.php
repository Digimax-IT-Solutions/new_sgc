<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('credit_memo');
$accounts = ChartOfAccount::all();
$customers = Customer::all();
$products = Product::all();
$terms = Term::all();
$locations = Location::all();
$payment_methods = PaymentMethod::all();
$credits = CreditMemo::all();

$totalCount = CreditMemo::getTotalCount();
$unpaidInvoice = CreditMemo::getUnpaidCount();
$paidInvoice = CreditMemo::getPaidCount();

$page = 'credit_memo'; // Set the variable corresponding to the current page
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3"><strong>Credit</strong> Memo</h1>
            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_credit') ?>
                    <?php displayFlashMessage('delete_payment_method') ?>
                    <?php displayFlashMessage('update_payment_method') ?>
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">

                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="credit_memo" class="btn btn-lg btn-outline-secondary me-2 mb-2">
                                            <i class="fas fa-arrow-left  fa-lg me-2"></i> Go Back
                                    </a>
                                    <a href="create_credit_memo" class="btn btn-lg btn-outline-success me-2 mb-2">
                                        <i class="fas fa-plus fa-lg me-2"></i> Create Memo
                                    </a>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Credit No</th>
                                                <th>Customer Name</th>
                                                <th>Date</th>
                                                <th>Memo</th>
                                                <th>Credit Amount</th>
                                                <th>Credit Balance</th>
                                                <th>Credit Status</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Replace with dynamic content from backend or server-side -->
                                            <?php foreach ($credits as $credit): ?>
                                                <?php if ($credit->status == 4): // Only include credits with status 4 ?>
                                                    <tr>
                                                        <td><?= $credit->credit_no ?></td>
                                                        <td><?= $credit->customer_name ?></td>
                                                        <td><?= $credit->credit_date ?></td>
                                                        <td><?= $credit->memo ?></td>
                                                        <td class="text-right">
                                                            ₱<?= number_format($credit->total_amount_due, 2) ?></td>
                                                        <td><?= $credit->credit_date ?></td>
                                                        <td class="text-center">
                                                            <?php
                                                            switch ($credit->status) {
                                                                case 4:
                                                                    echo '<span class="badge bg-info text-dark">Draft</span>';
                                                                    break;
                                                                case 0:
                                                                    echo '<span class="badge bg-warning text-dark">Pending</span>';
                                                                    break;
                                                                case 1:
                                                                    echo '<span class="badge bg-success">Approved</span>';
                                                                    break;
                                                                case 2:
                                                                    echo '<span class="badge bg-danger">Rejected</span>';
                                                                    break;
                                                                default:
                                                                    echo '<span class="badge bg-secondary">Unknown</span>';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <a href="view_credit?action=update&id=<?= $credit->id ?>"
                                                                class="btn btn-sm btn-info">
                                                                <i class="fas fa-eye"></i> View
                                                            </a>
                                                        </td>
                                                    </tr>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <!-- Add more rows as needed -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
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
<script>
    function selectDate(date) {
        document.getElementById('selectedDate').innerText = date;
    }
</script>