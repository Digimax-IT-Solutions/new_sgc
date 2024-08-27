<?php
//Guard
require_once '_guards.php';
Guard::warehouseOnly();

$page = 'material_issuance';
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/warehouse_sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/warehouse_navbar.php' ?>
    <main class="content">
        <div class="container-fluid p-0">
            <div class="mb-3">
                <h1 class="h3 d-inline align-middle"><strong>Material</strong> Issuance</h1>
                <nav aria-label="breadcrumb" class="float-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Material Issuance</li>
                    </ol>
                </nav>
            </div>

            <?php displayFlashMessage('add_material_issuance') ?>

            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total
                                        Material Issuance</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                                        0.00</div>
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
                                       0.00</div>
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
                                        0.00</div>
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
                                        0.00</div>
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
                    <h6 class="m-0 font-weight-bold text-primary">Purchase Requests</h6>
                    <div>
                        <a href="void_material_issuance" class="btn btn-sm btn-secondary">
                            <i class="fas fa-ban"></i> Void
                        </a>
                        <a href="create_material_issuance" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> New Purchase Request
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
                                <?php foreach ($material_issuances as $request): ?>
                                    <tr>
                                        <td><?= $request->pr_no ?></td>
                                        <td><?= $request->location ?></td>
                                        <td><?= date('M d, Y', strtotime($request->required_date)) ?></td>
                                        <td><?= $request->memo ?></td>
                                        <td class="text-center">
                                            <?php
                                            switch ($request->status) {
                                                case 0:
                                                    echo '<span class="badge bg-warning">Waiting for Approval</span>';
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
                                            <a href="view_material_issuance?action=update&id=<?= $request->id ?>"
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
            "order": [[2, "desc"]],
            "pageLength": 25
        });
    });
</script>