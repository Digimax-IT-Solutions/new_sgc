<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('account_types');

$account_types = AccountType::all();

?>
<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3"><strong>Account</strong> Types</h1>
            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_fs_classification') ?>
                    <?php displayFlashMessage('delete_uom') ?>
                    <?php displayFlashMessage('update_uom') ?>
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">

                                    <!-- <div>
                                        <a class="btn btn-sm btn-outline-secondary me-2" id="upload_button">
                                            <i class="fas fa-upload"></i> Upload
                                        </a>
                                        <input type="file" name="excel_file" id="excel_file" accept=".xlsx, .xls"
                                            style="display: none;">
                                        <a href="create_fs_classification" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus"></i> Add New
                                        </a>
                                    </div> -->
                                </div>
                            </div>
                            <br><br>
                            <div class="row">
                                <table id="table" class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>id</th>
                                            <th>Name</th>
                                            <th>Category</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($account_types as $account_type): ?>
                                            <tr>
                                                <td><?= $account_type->id ?></td>
                                                <td><?= $account_type->name ?></td>
                                                <td><?= $account_type->category ?></td>

                                            </tr>
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

<?php require 'views/templates/footer.php' ?>

<script>
    $(function () {
        $("#table").DataTable({
            "responsive": true,
            "lengthChange": true, // Enable length change dropdown
            "autoWidth": false,
            "pageLength": 25, // Set default number of rows per page
            "buttons": [
                {
                    extend: 'copy',
                    exportOptions: {
                        columns: ':not(:last-child)' // Exclude the last column (Action)
                    }
                },
                {
                    extend: 'csv',
                    exportOptions: {
                        columns: ':not(:last-child)' // Exclude the last column (Action)
                    }
                },
                {
                    extend: 'excel',
                    title: 'HISUMCO ACCOUNT TYPES',
                    exportOptions: {
                        columns: ':not(:last-child)' // Exclude the last column (Action)
                    }
                },
                {
                    extend: 'pdf',
                    exportOptions: {
                        columns: ':not(:last-child)' // Exclude the last column (Action)
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: ':not(:last-child)' // Exclude the last column (Action)
                    }
                },
                'colvis'
            ]
        }).buttons().container().appendTo('.card-header');
    });
</script>

<script>
    $(document).ready(function () {

        $('#upload_button').on('click', function () {
            Swal.fire({
                title: 'File Requirements',
                html: 'File must contain at least 1 column: <strong>name</strong>',
                imageUrl: 'photos/fs.png', // Replace with the actual path to your image
                imageWidth: 400,
                imageHeight: 200,
                imageAlt: 'File structure example',
                confirmButtonText: 'Understood, proceed to upload',
                showCancelButton: true,
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#excel_file').click();
                }
            });
        });
        $('#excel_file').on('change', function () {
            if (this.files[0]) {
                // Show loader overlay
                Swal.fire({
                    title: 'Uploading, please wait...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                var formData = new FormData();
                formData.append('excel_file', this.files[0]);
                formData.append('action', 'upload');

                $.ajax({
                    url: 'api/masterlist/fs_classification_controller.php',
                    type: 'POST',
                    data: formData,
                    async: true,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function (response) {
                        // Close loader
                        Swal.close();

                        if (response.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error: ' + response.message
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        // Close loader
                        Swal.close();

                        console.log(xhr.responseText);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred: ' + error
                        });
                    }
                });
            }
        });
    });
</script>