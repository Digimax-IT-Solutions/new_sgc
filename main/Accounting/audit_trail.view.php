<?php
include __DIR__ . ('../../includes/header.php');
?>
<?php
// Include your database connection file
include 'connect.php';
// Fetch audit trail records from the database
$query = "SELECT * FROM audit_trail ORDER BY timestamp DESC";
$stmt = $db->prepare($query);
$stmt->execute();
$auditTrails = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<style>
    /* Add styles for active status */
    .active {
        color: green;
        /* Change the text color for active status */
    }

    /* Add styles for inactive status */
    .inactive {
        color: red;
        /* Change the text color for inactive status */
    }

    #auditTrailTable {
        border-collapse: collapse;
        width: 100%;
    }

    #auditTrailTable th,
    #auditTrailTable td {
        padding: 1px;

        /* Adjust the padding as needed */
    }

    #auditTrailTable tbody tr:hover {
        color: white;
        background-color: rgb(0, 149, 77);
        /* Set your desired background color here */
    }
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-1" style="font-weight: bold; font-size:30px;">Audit Trail</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="#">Accounting</a></li>
                        <li class="breadcrumb-item active">Audit Trail</li>

                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">

                            <br><br>

                            <table id="auditTrailTable">
                                <thead>
                                    <tr>
                                        <th>AuditTrailID</th>
                                        <th>TableName</th>
                                        <th>RecordID</th>
                                        <th>Action</th>
                                        <th>UserID</th>
                                        <th>Timestamp</th>
                                        <th>Details</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($auditTrails as $auditTrail) : ?>
                                        <tr>
                                            <td><?php echo $auditTrail['auditTrailID']; ?></td>
                                            <td><?php echo $auditTrail['tableName']; ?></td>
                                            <td><?php echo $auditTrail['recordID']; ?></td>
                                            <td><?php echo $auditTrail['action']; ?></td>
                                            <td><?php echo $auditTrail['userID']; ?></td>
                                            <td><?php echo $auditTrail['timestamp']; ?></td>
                                            <td><?php echo $auditTrail['details']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <?php include __DIR__ . ('../../includes/footer.php'); ?>
</div>

<script>
    $(document).ready(function() {
        var table = $('#auditTable').DataTable({
            // Additional DataTables configuration options
            // ...

            // Enable child rows
            rowReorder: {
                selector: 'td:nth-child(2)'
            },
            select: true,
            drawCallback: function(settings) {
                // Initialize child rows
                var api = this.api();
                api.rows().every(function() {
                    var row = this;
                    var data = this.data();
                    var details = detailsFormat(data);
                    $(row.node()).attr('data-details', details);
                });
            }
        });

        // Handle the click event to show/hide child rows
        $('#auditTable tbody').on('click', 'td.details-control', function() {
            var tr = $(this).closest('tr');
            var row = table.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Open this row
                row.child(row.data('details')).show();
                tr.addClass('shown');
            }
        });
    });

    // Function to format the content of the child row
    function detailsFormat(d) {
        // You can customize this function based on your data structure
        return 'Details for record: ' + d.auditTrailID;
    }
</script>