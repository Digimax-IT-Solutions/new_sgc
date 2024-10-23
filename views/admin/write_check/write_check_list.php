<?php
// Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('write_check');

// Fetch all checks
$checks = WriteCheck::all();
?>

<?php require 'views/templates/header.php'; ?>
<?php require 'views/templates/sidebar.php'; ?>

<div class="main">
    <?php require 'views/templates/navbar.php'; ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3"><strong>Write</strong> Check</h1>
            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_write_check'); ?>
                    <?php displayFlashMessage('delete_payment_method'); ?>
                    <?php displayFlashMessage('update_write_check'); ?>

                    <!-- Default box -->
                    <div class="card">
                        <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <a href="draft_check" class="btn btn-lg btn-outline-secondary me-2 mb-2">
                                        <i class="fab fa-firstdraft fa-lg me-2"></i> Drafts
                                    </a>
                                    <a href="void_check" class="btn btn-lg btn-outline-danger me-2 mb-2">
                                        <i class="fas fa-file-excel fa-lg me-2"></i> Voids
                                    </a>

                                    <a href="#" class="btn btn-lg btn-outline-success me-2 mb-2" id="apvDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-plus fa-lg me-2"></i> Create Check Voucher
                                    </a>
                                    <ul class="dropdown-menu" aria-labelledby="apvDropdown">
                                        <li><a class="dropdown-item" href="create_check">Expense</a></li>
                                        <li><a class="dropdown-item" href="customer_payment">Pay Bills</a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="row">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered display compact" id="dataTable" width="100%" cellspacing="0">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Date</th>
                                                    <th>Check No</th>
                                                    <th>Total Amount Due</th>

                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($checks as $check): ?>
                                                    <?php if ($check->status != 3 && $check->status != 4): // Exclude invoices with status 3 and 4  
                                                    ?>
                                                        <tr>
                                                            <td><?= htmlspecialchars($check->cv_no); ?></td>
                                                            <td><?= htmlspecialchars($check->check_date); ?></td>
                                                            <td><?= htmlspecialchars($check->check_no); ?></td>
                                                            <td><b>₱<?= number_format($check->total_amount_due, 2, '.', ','); ?></b>
                                                            </td>

                                                            <td>
                                                                <a href="view_check?action=update&id=<?= htmlspecialchars($check->id); ?>"
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
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "order": [
                [0, "desc"]
            ],
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
            "columnDefs": [{
                    "orderable": false,
                    "targets": 4
                } // Adjusted index to match existing columns
            ]
        });
    });

    function selectDate(date) {
        document.getElementById('selectedDate').innerText = date;
    }
</script>