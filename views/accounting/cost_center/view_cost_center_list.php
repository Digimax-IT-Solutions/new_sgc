<?php

// Guard
require_once '_guards.php';
Guard::adminOnly();

$cost_centers = CostCenter::all();
?>

<?php require 'views/templates/header.php'; ?>
<?php require 'views/templates/sidebar.php'; ?>

<div class="main">
    <?php require 'views/templates/navbar.php'; ?>

    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12 d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-3"><strong>Cost</strong> Center</h1>
                    <div class="d-flex justify-content-end">
                        <a href="wtax" class="btn btn-secondary">
                            <i class="align-middle" data-feather="arrow-left-circle"></i> Go Back
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <?php if (isset($_GET['id'])): 
                                $id = $_GET['id'];
                                $cost_center = CostCenter::find($id);
                                if ($cost_center): ?>
                                    <form method="POST" action="api/masterlist/cost_center_controller.php">
                                        <input type="hidden" name="action" value="update" />
                                        <input type="hidden" name="id" value="<?= htmlspecialchars($cost_center->id, ENT_QUOTES, 'UTF-8') ?>" />
                                        
                                        <div class="row mb-3">
                                            <label for="code" class="col-sm-2 col-form-label">Code</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" id="code" name="code"
                                                    placeholder="Enter Code"
                                                    value="<?= htmlspecialchars($cost_center->code, ENT_QUOTES, 'UTF-8') ?>">
                                            </div>
                                        </div>

                                        <div class="row mb-3">
                                            <label for="particular" class="col-sm-2 col-form-label">Particular</label>
                                            <div class="col-sm-4">
                                                <input type="text" class="form-control" id="particular" name="particular"
                                                    placeholder="Enter Particular"
                                                    value="<?= htmlspecialchars($cost_center->particular, ENT_QUOTES, 'UTF-8') ?>">
                                            </div>
                                        </div>

                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </form>
                                <?php else: ?>
                                    <p>Cost Center not found.</p>
                                <?php endif; 
                            else: ?>
                                <p>No ID provided.</p>
                            <?php endif; ?>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </main>

    <?php require 'views/templates/footer.php'; ?>
</div>
