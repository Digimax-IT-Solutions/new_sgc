<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();

$chart_of_accounts = ChartOfAccount::all();

?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
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
                    <style>
                        .dataTables_wrapper .sorting:after,
                        .dataTables_wrapper .sorting:before,
                        .dataTables_wrapper .sorting_asc:after,
                        .dataTables_wrapper .sorting_desc:after {
                            content: "" !important;
                        }
                    </style>
                    <?php displayFlashMessage('add') ?>
                    <?php displayFlashMessage('delete') ?>
                    <?php displayFlashMessage('update') ?>
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-12 d-flex justify-content-between align-items-center mb-4">
                                    <h1 class="h3 mb-3"><strong></strong></h1>
                                    <div class="d-flex justify-content-end">
                                        <form method="POST" enctype="multipart/form-data">
                                            <button type="button" class="btn btn-secondary me-2"
                                                id="upload_button">Upload</button>
                                            <input type="file" name="excel_file" id="excel_file" accept=".xlsx, .xls"
                                                style="display: none;">
                                        </form>

                                        <a href="add_chart_of_account" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus"></i> New Account
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table id="chartOfAccountsTable" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Code</th>
                                            <th>Type</th>
                                            <th>GL</th>
                                            <th>GL NAME</th>
                                            <th>SL</th>
                                            <th>SL NAME</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
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

    <div id="loading-overlay"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
        <div
            style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); color: white; font-size: 24px;">
            Uploading data...
        </div>
    </div>


    <?php require 'views/templates/footer.php' ?>


    <script>
        $(document).ready(function () {
            // Show loading spinner
            $('#loading-spinner').show();


            function initializeDataTable() {
                var table = $('#chartOfAccountsTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: 'api/chart_of_account_controller.php',
                        type: 'POST',
                        dataSrc: function (json) {
                            $('#loading-spinner').hide();
                            $('#chartOfAccountsTable').show();
                            return json.data;
                        },
                        error: function (xhr, error, thrown) {
                            $('#loading-spinner').hide();
                            console.error('DataTables error:', error, thrown);
                            alert('An error occurred while loading the data. Please check the console for more information.');
                        }
                    },
                    columnDefs: [
                        { targets: 0, data: 'id' },
                        { targets: 1, data: 'account_code' },
                        { targets: 2, data: 'account_type' },
                        { targets: 3, data: 'gl_code' },
                        { targets: 4, data: 'gl_name' },
                        { targets: 5, data: 'sl_code' },
                        { targets: 6, data: 'sl_name' },
                        { targets: 7, data: 'account_description' },
                        {
                            targets: 8,
                            data: 'id',
                            render: function (data, type, row) {
                                return '<a href="edit_chart_of_account?id=' + data + '" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> View</a>';
                            }
                        }
                    ],
                    responsive: true,
                    ordering: true,
                    paging: true,
                    info: true,
                    scrollY: '60vh',
                    scrollCollapse: true,
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "Search...",
                        lengthMenu: "Show _MENU_ entries",
                        info: "Showing _START_ to _END_ of _TOTAL_ entries",
                        processing: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Processing...</span></div>'
                    },
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                    pageLength: 25,
                    dom: '<"top"Bf>rt<"bottom"lip><"clear">',
                    buttons: [
                        'colvis',
                        {
                            extend: 'csv',
                            text: 'Export CSV',
                            filename: 'chart_of_accounts_export',
                            exportOptions: {
                                modifier: {
                                    search: 'none'
                                }
                            }
                        },
                        {
                            extend: 'excel',
                            text: 'Export Excel',
                            filename: 'chart_of_accounts_export',
                            exportOptions: {
                                modifier: {
                                    search: 'none'
                                }
                            }
                        },
                        {
                            extend: 'pdf',
                            text: 'Export PDF',
                            filename: 'chart_of_accounts_export',
                            exportOptions: {
                                modifier: {
                                    search: 'none'
                                }
                            }
                        }
                    ]
                });
            }

            // Initialize DataTable immediately
            initializeDataTable();

            // File upload handling
            $('#upload_button').on('click', function () {
                $('#excel_file').click();
            });

            $('#excel_file').on('change', function () {
                if (this.files[0]) {
                    var formData = new FormData();
                    formData.append('excel_file', this.files[0]);
                    formData.append('action', 'upload');

                    // Show loading overlay
                    $('#loading-overlay').show();

                    $.ajax({
                        url: 'api/chart_of_account_controller.php',
                        type: 'POST',
                        data: formData,
                        async: true,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            // Hide loading overlay
                            $('#loading-overlay').hide();

                            console.log('Raw response:', response);
                            if (response.status === 'success') {
                                alert(response.message);
                                location.reload();
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function (xhr, status, error) {
                            // Hide loading overlay
                            $('#loading-overlay').hide();

                            alert('An error occurred: ' + error);
                        }
                    });
                }
            });
        });
    </script>