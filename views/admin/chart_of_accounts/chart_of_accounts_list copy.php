<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('chart_of_accounts_list');

$chart_of_accounts = ChartOfAccount::all();

?>


<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <style>
            .swal-xl {
                width: 80vw !important;
                /* Adjust width as needed */
                max-width: 1000px !important;
                /* Optional max width */
            }
        </style>
        <!-- Add this just after the opening <main class="content"> tag -->
        <div id="loading-spinner"
            style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 9999;">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading accounts...</span>
            </div>
            <p class="mt-2">Loading accounts...</p>
        </div>

        <div class="container-fluid p-0">
            <h1 class="h3 mb-3"><strong>Chart of Accounts</strong></h1>
            <div class="row">
                <div class="col-12">

                    <?php displayFlashMessage('add') ?>
                    <?php displayFlashMessage('delete') ?>
                    <?php displayFlashMessage('update') ?>
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <!-- Upload Button -->
                                        <a href="draft_credit" class="btn btn-lg btn-outline-secondary me-2 mb-2" onclick="document.getElementById('excel_file').click(); return false;">
                                            <i class="fab fa-firstdraft fa-lg me-2"></i> Upload
                                        </a>
                                        <!-- Hidden form for file upload -->
                                        <form method="POST" enctype="multipart/form-data" style="display: none;">
                                            <input type="file" name="excel_file" id="excel_file" accept=".xlsx, .xls" onchange="document.getElementById('upload_button').click();">
                                            <button type="submit" id="upload_button"></button>
                                        </form>

                                        <!-- Export to Excel Button -->
                                        <a href="#" onclick="confirmExport(); return false;" class="btn btn-lg btn-outline-danger me-2 mb-2">
                                            <i class="fas fa-file-excel fa-lg me-2"></i> Export to Excel
                                        </a>

                                        <!-- Create Account Button -->
                                        <a href="add_chart_of_account" class="btn btn-lg btn-outline-success me-2 mb-2">
                                            <i class="fas fa-plus fa-lg me-2"></i> Create Account
                                        </a>
                                    </div>
                                </div>
                            </div>


                            <div class="table-responsive">
                                <table id="chartOfAccountsTable" class="table table-striped table-bordered display compact">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Account Code</th>
                                            <th>Account Type</th>
                                            <th>Sub Account Of</th>
                                            <th>Account Description</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be populated by DataTables -->

                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </main>
</div>
<div id="loading-overlay"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div
        style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 24px;">
        Uploading data...
    </div>
</div>

<!-- Add this loader overlay to your HTML -->
<div id="loader-overlay"
    style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 9999;">
    <div
        style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; color: white;">
        <div class="spinner-border" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p>Exporting accounts. Please wait...</p>
    </div>
</div>

<?php require 'views/templates/footer.php' ?>

<script>
    $(document).ready(function() {
        $('#chartOfAccountsTable').DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": "api/chart_of_account_controller.php",
                "type": "POST"
            },
            "columns": [{
                    "data": "id"
                },
                {
                    "data": "account_code"
                },
                {
                    "data": "account_type"
                },
                {
                    "data": "sub_account_id"
                },
                {
                    "data": "account_description"
                },
                {
                    "data": null,
                    "render": function(data, type, row) {
                        return '<a href="edit_chart_of_account?id=' + row.id + '" class="btn btn-sm btn-primary">View</a> '

                    }
                }
            ],
            "order": [
                [0, "asc"]
            ]
        });
    });
</script>


<script>
    function confirmExport() {
        if (confirm("Export all accounts? This may take a while.")) {
            showLoader();
            exportToExcel();
        }
    }

    function showLoader() {
        document.getElementById('loader-overlay').style.display = 'block';
    }

    function hideLoader() {
        document.getElementById('loader-overlay').style.display = 'none';
    }

    function exportToExcel() {
        fetch('api/export_accounts.php?action=export')
            .then(response => response.json())
            .then(data => {
                hideLoader();
                if (data.op == 'ok') {
                    var $a = $("<a>");
                    $a.attr("href", data.file);
                    $("body").append($a);
                    $a.attr("download", "chart_of_accounts.xlsx");
                    $a[0].click();
                    $a.remove();
                } else {
                    alert('Export failed. Please try again.');
                }
            })
            .catch(error => {
                hideLoader();
                console.error('Error:', error);
                alert('An error occurred during export. Please try again.');
            });
    }
</script>

<script>
    $(document).ready(function() {

        $('#upload_button').on('click', function() {
            Swal.fire({
                title: 'File Requirements',
                html: 'File must contain at least 9 columns: <strong>code, type, gl, gl name, sl, sl name, description, fs classification, fs notes, classification</strong>',
                imageUrl: 'photos/coa.png', // Replace with the actual path to your image
                imageWidth: 1000,
                imageHeight: 100,
                imageAlt: 'File structure example',
                confirmButtonText: 'Understood, proceed to upload',
                showCancelButton: true,
                cancelButtonText: 'Cancel',
                customClass: {
                    popup: 'swal-xl' // Custom class for large size
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#excel_file').click();
                }
            });
        });
        $('#excel_file').on('change', function() {
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
                    url: 'api/upload_chart_of_account.php',
                    type: 'POST',
                    data: formData,
                    async: true,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
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
                    error: function(xhr, status, error) {
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