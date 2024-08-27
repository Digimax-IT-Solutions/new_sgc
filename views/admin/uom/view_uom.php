<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();

//require_once 'api/category_controller.php';

$uoms = Uom::all();

$uom = null;
if (get('action') === 'update') {
    $uom = Uom::find(get('id'));
}

$page = 'enter_bills'; // Set the variable corresponding to the current page
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <style>
        .table-sm .form-control {
            border: none;
            /* Remove border */
            padding: 0;
            /* Remove default padding */
            background-color: transparent;
            /* Make background transparent */
            box-shadow: none;
            /* Remove box shadow */
            height: auto;
            /* Auto height to fit content */
            line-height: inherit;
            /* Inherit line-height from the table */
            font-size: inherit;
            /* Inherit font-size from the table */
        }

        .select2-no-border .select2-selection {
            border: none !important;
            padding: 0 !important;
            box-shadow: none !important;
        }

        .select2-no-border .select2-selection__rendered {
            padding: 0 !important;
            /* Adjust if necessary */
        }
    </style>
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-sm-12 d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3 mb-3"><strong>Unit</strong> of Measure</h1>
                    <div class="d-flex justify-content-end">
                        <a href="uom" class="btn btn-secondary">
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
                                $uom = Uom::find($id);
                                if ($uom): ?>
                            <form method="POST" action="api/masterlist/uom_controller.php" id="uomForm">
                                <input type="hidden" name="action" id="modalAction" value="update" />
                                <input type="hidden" name="id" id="itemId" value="<?= htmlspecialchars($uom->id) ?>" />
                                <input type="hidden" name="item_data" id="item_data" />

                                <div class="row mb-3">
                                    <label for="uomName" class="col-sm-1 col-form-label">UOM</label>
                                    <div class="col-sm-4">
                                        <input type="text" class="form-control" id="uomName" name="name"
                                            placeholder="Enter UOM here" value="<?= htmlspecialchars($uom->name) ?>" required />
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="row">
                                    <div class="col-md-10 d-inline-block">
                                        <button type="submit" class="btn btn-primary">Submit</button>
                                    </div>
                                </div>

                                <br><br>
                            </form>
                            <?php else: ?>
                                <p>UOM not found.</p>
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

    <?php require 'views/templates/footer.php' ?>
</div>
