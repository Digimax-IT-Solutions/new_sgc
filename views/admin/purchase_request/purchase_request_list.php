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

    <style>
        .btn-lg {

            border-radius: 8px;
        }

        .btn-outline-success,
        .btn-outline-danger,
        .btn-outline-secondary {
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-outline-success:hover,
        .btn-outline-danger:hover,
        .btn-outline-secondary:hover {
            color: #fff !important;
            box-shadow: 0px 4px 12px rgba(0, 123, 255, 0.3);
        }
    </style>
    <div class="main">
        <?php require 'views/templates/navbar.php' ?>
        <main class="content">
            <div class="container-fluid p-0">
                <div class="mb-3">
                    <h1 class="h3 d-inline align-middle"><strong>Purchase</strong> Requests</h1>
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

                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total
                                            Purchase Requests</div>
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
                        <div>
                            <a href="draft_purchase_request" class="btn btn-lg btn-outline-secondary me-2 mb-2">
                                <i class="fab fa-firstdraft fa-lg me-2"></i> Drafts
                            </a>
                            <a href="void_purchase_request" class="btn btn-lg btn-outline-danger me-2 mb-2">
                                <i class="fas fa-file-excel fa-lg me-2"></i> Voids
                            </a>
                            <a href="create_purchase_request" class="btn btn-lg btn-outline-success me-2 mb-2">
                                <i class="fas fa-plus fa-lg me-2"></i> Create Purchase Request
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
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
                                        <?php if ($request->status != 3 && $request->status != 4): ?>
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
                                                        case 0:
                                                            echo '<span class="badge bg-warning">Open</span>';
                                                            break;
                                                        case 1:
                                                            echo '<span class="badge bg-success">Closed</span>';
                                                            break;
                                                        case 2:
                                                            echo '<span class="badge bg-info">Partially Ordered</span>';
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
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "order": [
                    [0, "desc"]
                ], // Set the column index to 0 for pr_no and order to ascending
                "pageLength": 25
            });
        });
    </script>