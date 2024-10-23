<?php
// Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('sales_return');
// Fetch all salesReturn
$salesReturn = SalesReturn::all();
?>

<?php require 'views/templates/header.php'; ?>
<?php require 'views/templates/sidebar.php'; ?>

<div class="main">
    <?php require 'views/templates/navbar.php'; ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3"><strong>Sales</strong> Return</h1>
            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_sales_return'); ?>
                    <?php displayFlashMessage('delete_payment_method'); ?>
                    <?php displayFlashMessage('update_write_check'); ?>

                    <!-- Default box -->
                    <div class="card">
                    <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="sales_return" class="btn btn-lg btn-outline-secondary me-2 mb-2">
                                            <i class="fas fa-arrow-left  fa-lg me-2"></i> Go Back
                                    </a>
                                    <a href="create_sales_return" class="btn btn-lg btn-outline-success me-2 mb-2">
                                        <i class="fas fa-plus fa-lg me-2"></i> Create Sales Return
                                    </a>
                                </div>
                            </div>

                            <div class="row">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Date</th>
                                                    <th>Customer Name</th>
                                                    <th>Total Amount Due</th>
                                                    <th>Status</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($salesReturn as $salesReturn): ?>
                                                    <?php if ($salesReturn->status == 3): // Only include invoices with status 3 ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($salesReturn->sales_return_number); ?></td>
                                                        <td><?= htmlspecialchars($salesReturn->sales_return_date); ?></td>
                                                        <td><?= htmlspecialchars($salesReturn->customer_name); ?></td>
                                                        <td><b>₱<?= number_format($salesReturn->total_amount_due, 2, '.', ','); ?></b></td>
                                                        <td class="text-center">
                                                            <?php
                                                            switch ($salesReturn->status) {
                                                                case 3:
                                                                    echo '<span class="badge bg-danger">Unpaid</span>';
                                                                    break;
                                                                case 4:
                                                                    echo '<span class="badge bg-danger">Unpaid</span>';
                                                                    break;
                                                                case 0:
                                                                    echo '<span class="badge bg-danger">Unpaid</span>';
                                                                    break;
                                                                case 1:
                                                                    echo '<span class="badge bg-success">Paid</span>';
                                                                    break;
                                                                case 2:
                                                                    echo '<span class="badge bg-warning">Partially Paid</span>';
                                                                    break;
                                                                default:
                                                                    echo '<span class="badge bg-secondary">Unknown</span>';
                                                            }
                                                            ?>
                                                        </td>
                                                        <td>
                                                            <a href="view_sales_return?action=update&id=<?= htmlspecialchars($salesReturn->id); ?>" class="btn btn-sm btn-info">
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
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
    </main>
</div>

<?php require 'views/templates/footer.php'; ?>

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
                { "orderable": false, "targets": 4 } // Adjusted index to match existing columns
            ]
        });
    });

    function selectDate(date) {
        document.getElementById('selectedDate').innerText = date;
    }
</script>
