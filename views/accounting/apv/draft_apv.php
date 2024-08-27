<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();

$apv = Apv::all();
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <main class="content">
        <div class="container-fluid p-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h3 mb-0"><strong>Accounts Payable</strong> Voucher</h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Accounts Payable Voucher</li>
                    </ol>
                </nav>
            </div>

            <div class="card shadow mb-4">
                <?php displayFlashMessage('add_apv') ?>
                <?php displayFlashMessage('delete_apv') ?>
                <?php displayFlashMessage('update_apv') ?>
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold text-primary"></h6>
                    <div>
                        <a href="accounts_payable_voucher" class="btn btn-sm btn-secondary">
                            <i class="fas fa-arrow-left"></i> Go Back
                        </a>
                        <a href="create_apv_expense" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Create New
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table table-striped table-hover" id="apvTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Memo</th>
                                <th>Total Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($apv as $ap) : ?>
                                <?php if ($ap->status == 4): // Only include invoices with status 3 ?>
                                <tr>
                                    <td><?= htmlspecialchars($ap->apv_no); ?></td>
                                    <td><?= htmlspecialchars($ap->apv_date); ?></td>
                                    <td><?= htmlspecialchars($ap->memo); ?></td>
                                    <td><b>₱<?= number_format($ap->total_amount_due, 2, '.', ','); ?></b></td>
                                    <td class="text-center">
                                            <?php
                                            switch ($ap->status) {
                                                case 4:
                                                    echo '<span class="badge bg-info text-dark">Draft</span>';
                                                    break;
                                                case 3:
                                                    echo '<span class="badge bg-warning">Void</span>';
                                                    break;
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
                                        <a href="view_apv?action=update&id=<?= htmlspecialchars($ap->id); ?>" class="btn btn-sm btn-info">
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
    </main>
</div>

<?php require 'views/templates/footer.php' ?>


<script>
    $(document).ready(function() {
        $('#apvTable').DataTable({
            "order": [
                [2, "desc"]
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
            }]
        });
    });
</script>