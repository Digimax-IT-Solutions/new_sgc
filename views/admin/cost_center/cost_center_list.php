<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();

$cost_centers = CostCenter::all();

?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3"><strong>Cost Center</strong></h1>
            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_cost_center') ?>
                    <?php displayFlashMessage('delete_cost_center') ?>
                    <?php displayFlashMessage('update_cost_center') ?>
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12 d-flex justify-content-between align-items-center mb-4">
                                    <h6 class="m-0 font-weight-bold text-primary">Cost Center</h6>
                                    <div class="d-flex justify-content-end">
                                        <form method="post" action="api/masterlist/chart_of_account_controller.php"
                                            enctype="multipart/form-data">
                                            <input type="hidden" name="upload" id="upload" value="upload" />
                                            <input type="file" class="form-control-file mr-1" id="excelFile"
                                                name="excelFile">
                                                <a href="upload" class="btn btn-sm btn-outline-secondary me-2">
                                                    <i class="fas fa-upload"></i> Upload
                                                </a>
                                        </form>
                                        <a href="create_cost_center" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus"></i> New Cost Center
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <br><br><br>
                            <table id="costCenterTable" class="table">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Particular</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cost_centers as $cost_center): ?>
                                        <tr>
                                            <td><?= $cost_center->code ?></td>
                                            <td><?= $cost_center->particular ?></td>
                                            <td>
                                                <a class="text-primary" href="view_cost_center?action=update&id=<?= $cost_center->id ?>">
                                                    <i class="fas fa-edit"></i> Update
                                                </a>
                                                <a class="text-danger ml-2"
                                                    href="api/masterlist/cost_center_controller.php?action=delete&id=<?= $cost_center->id ?>">
                                                    <i class="fas fa-trash-alt"></i> Delete
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </main>
</div>

<?php require 'views/templates/footer.php' ?>

<script>
    $(function () {
        $("#costCenterTable").DataTable({
            "responsive": true, "lengthChange": false, "autoWidth": false,
            "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
        }).buttons().container().appendTo('#costCenterTable_wrapper .col-md-6:eq(0)');
    });
</script>