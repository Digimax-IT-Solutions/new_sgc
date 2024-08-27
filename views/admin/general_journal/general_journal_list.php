<?php
// Guard to ensure only admins can access
require_once '_guards.php';
Guard::adminOnly();

// Fetch all general journal entries
$journals = GeneralJournal::all();
?>

<?php require 'views/templates/header.php'; ?>
<?php require 'views/templates/sidebar.php'; ?>
<div class="main">
    <?php require 'views/templates/navbar.php'; ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3"><strong>General</strong> Journal</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">General Journal</li>
                </ol>
            </nav>
            <div class="row">
                <div class="col-12">
                    <?php 
                    // Display flash messages for various actions
                    displayFlashMessage('add_general_journal');
                    displayFlashMessage('delete_general_journal');
                    displayFlashMessage('update_general_journal');
                    ?>
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 font-weight-bold text-primary">Journal</h6>
                                    <div>
                                        <a href="draft_general_journal" class="btn btn-sm btn-danger">
                                            <i class="fab fa-firstdraft"></i> Draft
                                        </a>
                                        <a href="upload" class="btn btn-sm btn-outline-secondary me-2">
                                            <i class="fas fa-upload"></i> Upload
                                        </a>
                                        <a href="void_general_journal" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-ban"></i> Void
                                        </a>
                                        <a href="create_general_journal" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus"></i> Create General Journal
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Entry No</th>
                                            <th>Entry Date</th>
                                            <th>Memo</th>
                                            <th>Status</th>
                                            <th>Action</th> <!-- New column for view link -->
                                        </tr>
                                    </thead>

                                    <tbody>
                                        <?php foreach ($journals as $journal): ?>
                                            <?php if ($journal->status != 3 && $journal->status != 4): // Exclude credit with status 3 and 4 ?>
                                            <tr>
                                                <td><?= htmlspecialchars($journal->entry_no) ?></td>
                                                <td><?= htmlspecialchars($journal->journal_date) ?></td>
                                                <td><?= htmlspecialchars($journal->memo) ?></td>
                                                <td class="text-center">
                                                        <?php
                                                        switch ($journal->status) {
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
                                                    <a href="view_journal?id=<?= urlencode($journal->id) ?>" class="btn btn-sm btn-info">
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
            "order": [[0, "desc"]],  // Correct column index for ordering
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
                { "orderable": false, "targets": 3 }  // Correct column index for non-orderable column
            ]
        });
    });
</script>
