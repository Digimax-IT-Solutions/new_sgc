<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('purchase_request');
$purchase_requests = PurchaseRequest::all();
$locations = Location::all();


$page = 'purchase_request';
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <main class="content">
        <div class="container-fluid p-0">
            <div class="mb-3">
                <h1 class="h3 d-inline align-middle"><strong>Void Purchase</strong> Requests</h1>
                <nav aria-label="breadcrumb" class="float-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Purchase Requests</li>
                    </ol>
                </nav>
            </div>

            <?php displayFlashMessage('add_purchase_request') ?>
            <?php displayFlashMessage('delete_purchase_request') ?>
            <?php displayFlashMessage('update_purchase_request') ?>



            <div class="card shadow mb-4">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <a href="purchase_request" class="btn btn-lg btn-outline-secondary me-2 mb-2">
                                    <i class="fas fa-arrow-left  fa-lg me-2"></i> Go Back
                            </a>
                            <a href="create_purchase_request" class="btn btn-lg btn-outline-success me-2 mb-2">
                                <i class="fas fa-plus fa-lg me-2"></i> Create Purchase Request
                            </a>
                        </div>
                    </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Purchase Request #</th>
                                    <th>Location</th>
                                    <th>Required Date</th>
                                    <th>Purpose/Memo</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($purchase_requests as $request): ?>
                                    <?php if ($request->status == 3): // Only include invoices with status 3 ?>
                                    <tr>
                                        <td><?= $request->pr_no ?></td>
                                        <td>
                                            <?php
                                            // Find the location name by comparing the location ID
                                            foreach ($locations as $location) {
                                                if ($location->id == $request->location) {
                                                    echo htmlspecialchars($location->name); // Output the location name
                                                    break; // Exit loop once found
                                                }
                                            }
                                            ?>
                                        </td>
                                        <td><?= date('M d, Y', strtotime($request->required_date)) ?></td>
                                        <td><?= $request->memo ?></td>
                                        <td class="text-center">
                                            <?php
                                            switch ($request->status) {
                                                case 3:
                                                    echo '<span class="badge bg-secondary">Void</span>'; // Changed bg-warning to bg-secondary for better visibility
                                                    break;
                                                default:
                                                    echo '<span class="badge bg-secondary">Unknown</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="view_purchase_request?action=update&id=<?= $request->id ?>"
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