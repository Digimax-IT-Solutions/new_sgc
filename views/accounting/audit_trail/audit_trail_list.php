<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();

$audit_trails = AuditTrail::all();

// Initialize variables for date range
$title = "AUDIT TRAIL";
$from_date = date('Y-m-d');
$to_date = date('Y-m-d');

// Filter by date range if provided
if (isset($_POST['from_date']) && isset($_POST['to_date'])) {
    $from_date = $_POST['from_date'];
    $to_date = $_POST['to_date'];
    $audit_trails = AuditTrail::filterByDateRange($from_date, $to_date);

    // Update the title with the selected date range
    $title = "<h1 class='display-8'>AUDIT TRAIL</h1> </br>As of $from_date to $to_date";
}
?>

<?php require 'views/templates/header.php'; ?>
<?php require 'views/templates/sidebar.php'; ?>
<div class="main">
    <style>
        .spacer-row {
            height: 20px !important;
        }

        .spacer-row td {
            border: none !important;
        }

        #transaction_table th,
        #transaction_table td {
            white-space: nowrap;
            padding: 2px 2px;
        }

        #transaction_table th.text-end,
        #transaction_table td.text-end {
            text-align: right;
        }

        #transaction_table th.text-start,
        #transaction_table td.text-start {
            text-align: left;
        }
    </style>
    <?php require 'views/templates/navbar.php'; ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row">
                <div class="col-12">
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">

                            <!-- SELECT DATE -->
                            <form method="POST" action="" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="from_date">From:</label>
                                            <input type="date" class="form-control" id="from_date" name="from_date"
                                                value="<?php echo $from_date; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="to_date">To:</label>
                                            <input type="date" class="form-control" id="to_date" name="to_date"
                                                value="<?php echo $to_date; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-3 py-4">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-secondary"
                                                id="filter_button">Filter</button>
                                            <button type="button" class="btn btn-primary"
                                                id="print_button">Print</button>
                                            <button type="button" class="btn btn-success"
                                                id="upload_button">Upload</button>
                                            <input type="file" name="excel_file" id="excel_file" accept=".xlsx, .xls"
                                                style="display: none;">
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <br />
                            <center>
                                <h3 id="date_range"><?php echo $title; ?></h3>
                            </center>
                            <br />
                            <div class="table-responsive">
                                <table id="table" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>Entered</th>
                                            <th>Transaction</th>
                                            <th>Ref No</th>
                                            <th>Name</th>
                                            <th>State</th>
                                            <th>Date</th>
                                            <th>Created By</th>
                                            <th>Account</th>
                                            <th style="text-align: right;">Debit</th>
                                            <th style="text-align: right;">Credit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($audit_trails as $audit_trail): ?>
                                            <tr>
                                                <td><?= $audit_trail->created_at ?></td>
                                                <td><?= $audit_trail->transaction_type ?></td>
                                                <td><?= $audit_trail->ref_no ?></td>
                                                <th><?= $audit_trail->name ?></th>
                                                <td>
                                                    <?php if ($audit_trail->state == 1): ?>
                                                        <span class="badge bg-success">latest</span>
                                                    <?php elseif ($audit_trail->state == 2): ?>
                                                        <span class="badge bg-secondary">void</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= $audit_trail->transaction_date ?></td>
                                                <td><?= $audit_trail->created_by ?></td>
                                                <td><?= $audit_trail->account_description ?></td>
                                                <td style="text-align: right;">
                                                    <?php echo $audit_trail->debit != 0 ? $audit_trail->debit : ''; ?>
                                                </td>
                                                <td style="text-align: right;">
                                                    <?php echo $audit_trail->credit != 0 ? $audit_trail->credit : ''; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
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

    <?php require 'views/templates/footer.php'; ?>

    <script>
        $(document).ready(function () {

            $('#upload_button').on('click', function () {
                $('#excel_file').click();
            });

            $('#excel_file').on('change', function () {
                if (this.files[0]) {
                    var formData = new FormData();
                    formData.append('excel_file', this.files[0]);
                    formData.append('action', 'upload'); // Add this line to specify the action

                    $.ajax({
                        url: 'api/audit_trail_controller.php',
                        type: 'POST',
                        data: formData,
                        async: true,
                        cache: false,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            console.log('Raw response:', response);  // For debugging
                            if (response.status === 'success') {
                                alert(response.message);
                                location.reload();
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function (xhr, status, error) {
                            alert('An error occurred: ' + error);
                        }
                    });
                }
            });

            $('#table').DataTable({
                responsive: true,
                ordering: false,
                paging: false,
                info: false,
                scrollY: '100vh',
                scrollCollapse: true,
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'csv', 'excel', 'pdf', 'print'
                ],
                order: [[0, 'desc']], // Sort by the first column (Entered) in descending order
                pageLength: 25,
                lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                columnDefs: [
                    { targets: [8, 9], className: 'text-right' }, // Right-align debit and credit columns
                    { targets: [0, 5], type: 'date' } // Specify date columns for proper sorting
                ]
            });

            // Handle print button click
            $('#print_button').on('click', function () {
                window.print();
            });
        });
    </script>