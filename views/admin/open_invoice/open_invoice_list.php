<?php
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('open_invoice');

$invoices = OpenInvoice::all();
$totalCount = OpenInvoice::getTotalCount();
$unpaidInvoice = OpenInvoice::getUnpaidCount();
$paidInvoice = OpenInvoice::getPaidCount();
$overdueCount = OpenInvoice::getOverdueCount();

$totalAmount = array_sum(array_column($invoices, 'total_amount_due'));
$totalPaid = array_sum(array_column($invoices, 'paid_amount'));
$totalUnpaid = $totalAmount - $totalPaid;
$totalOverdue = array_sum(array_column($invoices, 'overdue_amount'));

$page = 'open_invoices';
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<style>
    .btn-lg {

        border-radius: 8px;
    }

    .btn-outline-primary,
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
                <h1 class="h3 d-inline align-middle"><strong>Sales</strong>Open Invoice</h1>
                <nav aria-label="breadcrumb" class="float-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Open Invoices</li>
                    </ol>
                </nav>
            </div>

            <?php displayFlashMessage('add_invoice') ?>
            <?php displayFlashMessage('delete_invoice') ?>
            <?php displayFlashMessage('update_invoice') ?>

            <div class="row">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total
                                    Open Invoices</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $totalCount ?></div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
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
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Paid</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $paidInvoice ?></div>
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
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Unpaid</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $unpaidInvoice ?></div>
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
                                    <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Overdue</div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $overdueCount ?></div>
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
                        <a href="draft_open_invoice" class="btn btn-lg btn-outline-secondary me-2 mb-2">
                            <i class="fab fa-firstdraft fa-lg me-2"></i> Drafts
                        </a>
                        <a href="void_open_invoice" class="btn btn-lg btn-outline-danger me-2 mb-2">
                            <i class="fas fa-file-excel fa-lg me-2"></i> Voids
                        </a>
                        <a href="create_open_invoice" class="btn btn-lg btn-outline-success me-2 mb-2">
                            <i class="fas fa-plus fa-lg me-2"></i> Create Open Invoice
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table table-bordered display compact" id="dataTable" style="min-width: 1000px; width: 100%;"
                            cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="white-space: nowrap;">Open Invoice No.</th>
                                    <th style="white-space: nowrap;">Customer</th>
                                    <th style="white-space: nowrap;">Memo</th>
                                    <th style="white-space: nowrap;">Date</th>
                                    <th style="white-space: nowrap;">Amount</th>
                                    <th style="white-space: nowrap;">Status</th>
                                    <th style="white-space: nowrap;">Balance</th>
                                    <th style="white-space: nowrap;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($invoices as $invoice): ?>
                                    <?php if ($invoice->open_invoice_status != 3 && $invoice->open_invoice_status != 4): ?>
                                        <tr>
                                            <td style="white-space: nowrap;"><?= htmlspecialchars($invoice->open_invoice_number) ?>
                                            </td>
                                            <td style="white-space: nowrap;"><?= htmlspecialchars($invoice->customer_name) ?>
                                            </td>
                                            <td style="white-space: nowrap;"><?= htmlspecialchars($invoice->memo) ?></td>
                                            <td style="white-space: nowrap;">
                                                <?= date('M d, Y', strtotime($invoice->open_invoice_date)) ?>
                                            </td>
                                            <td style="white-space: nowrap;" class="text-right">
                                                ₱<?= number_format($invoice->total_amount_due, 2) ?></td>
                                            <td style="white-space: nowrap;" class="text-center">
                                                <?php
                                                switch ($invoice->open_invoice_status) {
                                                    case 0:
                                                        echo '<span class="badge bg-danger">Unpaid</span>';
                                                        break;
                                                    case 1:
                                                        echo '<span class="badge bg-success">Paid</span>';
                                                        break;
                                                    case 2:
                                                        echo '<span class="badge bg-warning">Partially Paid</span>';
                                                        break;
                                                    default:
                                                        echo '<span class="badge bg-secondary">Unknown</span>';
                                                }
                                                ?>
                                            </td>
                                            <td style="white-space: nowrap;" class="text-right">
                                                ₱<?= number_format($invoice->balance_due, 2) ?>
                                            </td>
                                            <td style="white-space: nowrap;">
                                                <a href="view_invoice?action=update&id=<?= htmlspecialchars($invoice->id) ?>"
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

        $('<div class="dt-buttons date-filters">' +
            '<label for="fromDate">From: <input class="form-control" type="date" id="fromDate"></label>&nbsp' +
            '<label for="toDate">To: <input class="form-control" type="date" id="toDate"></label>' +
            '<br><br>' +
            '</div>'
        ).insertBefore('#dataTable');

        $.fn.dataTable.ext.search.push(
            function(settings, data, dataIndex) {
                var fromDate = $('#fromDate').val();
                var toDate = $('#toDate').val();
                var dateColumn = data[3]; // Assuming the date is in the 4th column (index 3)

                if (fromDate === "" && toDate === "") {
                    return true;
                }

                var dateFrom = Date.parse(fromDate);
                var dateTo = Date.parse(toDate);
                var dateCheck = Date.parse(dateColumn);

                if ((isNaN(dateFrom) && isNaN(dateTo)) ||
                    (isNaN(dateFrom) && dateCheck <= dateTo) ||
                    (dateFrom <= dateCheck && isNaN(dateTo)) ||
                    (dateFrom <= dateCheck && dateCheck <= dateTo)) {
                    return true;
                }
                return false;
            }
        );

        var table = $('#dataTable').DataTable({
            responsive: true,
            ordering: true,
            paging: false,
            info: true,
            scrollY: '60vh',
            scrollCollapse: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search...",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
            },
            lengthMenu: [
                [10, 25, 50, 100, -1],
                [10, 25, 50, 100, "All"]
            ],
            pageLength: 25,
            columnDefs: [{
                    className: "text-start",
                    targets: '_all'
                },
                {
                    width: "120px",
                    targets: 0
                },
                {
                    width: "100px",
                    targets: [1, 2]
                },
                {
                    width: "150px",
                    targets: [3, 4, 5]
                },
                {
                    width: "200px",
                    targets: 6
                },
                {
                    width: "100px",
                    targets: 7
                },
                {
                    orderable: false,
                    targets: 7
                }
            ],
            dom: '<"top"Bf>rt<"bottom"lip><"clear">',
            buttons: [
                'colvis',
                {
                    extend: 'csv',
                    text: 'Export CSV',
                    filename: 'open_invoice_export',
                    exportOptions: {
                        modifier: {
                            search: 'none'
                        }
                    },
                    customize: function(csv) {
                        return getHeader() + csv;
                    }
                },
                {
                    extend: 'excel',
                    text: 'Export Excel',
                    filename: 'open_invoice_export',
                    exportOptions: {
                        modifier: {
                            search: 'none'
                        }
                    }
                },
                {
                    extend: 'pdf',
                    text: 'Export PDF',
                    filename: 'open_invoice_export',
                    exportOptions: {
                        modifier: {
                            search: 'none'
                        }
                    }
                },
                {
                    text: 'Export TXT',
                    action: function(e, dt, button, config) {
                        exportWithDateCheck('txt');
                    }
                }
            ]
        });

        function exportWithDateCheck(type) {
            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();

            if (!fromDate || !toDate) {
                Swal.fire({
                    title: 'Date Range Not Set',
                    text: 'Please select both From and To dates before exporting.',
                    icon: 'warning',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Proceed with export based on type
            switch (type) {
                case 'csv':
                    table.button('.buttons-csv').trigger();
                    break;
                case 'excel':
                    table.button('.buttons-excel').trigger();
                    break;
                case 'pdf':
                    table.button('.buttons-pdf').trigger();
                    break;
                case 'txt':
                    exportTXT();
                    break;
            }
        }

        function exportTXT() {
            var header = getHeader();
            var tableData = table.rows({
                search: 'applied'
            }).data().toArray();
            var content = header;

            var cellWidth = 25; // 100px is approximately 12 characters in monospace font
            var separator = '+' + '-'.repeat(cellWidth * 6 + 6) + '+\n';

            // Add table header
            content += separator;
            content += '|' + [
                'Invoice No.', 'Customer', 'Memo', 'Date',
                'Amount', 'Balance'
            ].map(h => h.padEnd(cellWidth)).join('|') + '|\n';
            content += separator;

            // Add table rows
            tableData.forEach(function(row) {
                content += '|' + [
                    row[0],
                    row[1].substring(0, cellWidth - 1),
                    row[2].substring(0, cellWidth - 1),
                    row[3],
                    row[4],
                    row[6]
                ].map(cell => cell.toString().padEnd(cellWidth)).join('|') + '|\n';
            });

            // Add table footer
            content += separator;

            var blob = new Blob([content], {
                type: 'text/plain;charset=utf-8'
            });
            saveAs(blob, 'open_invoice_export.txt');
        }

        $('#fromDate, #toDate').on('change', function() {
            table.draw();
        });

        function getHeader() {
            var recordCount = table.rows({
                search: 'applied'
            }).count();
            var totalAmount = table.column(4, {
                search: 'applied'
            }).data().reduce(function(sum, value) {
                return sum + parseFloat(value.replace(/[^\d.-]/g, ''));
            }, 0);

            var fromDate = $('#fromDate').val();
            var toDate = $('#toDate').val();

            return 'Taxpayer`s Name: Hideco Sugar Milling Co., Inc.\n' +
                'TIN: 000-123-533-00000\n' +
                'Address: Montebello, Kanaga, Leyte, 6531\n' +
                '\n' +
                'File Name: Invoice\n' +
                'File Type: Text/CSV\n' +
                'Number of Records: ' + recordCount + '\n' +
                'Amount Field Control Total: ₱' + totalAmount.toFixed(2) + '\n' +
                'Period Covered: ' + fromDate + ' to ' + toDate + '\n\n' +
                'Transaction Cutoff: \n' +
                'Extracted by: ' + '<?= $_SESSION['user_name'] ?>' + '\n';
        }
    });
</script>


<script>
    $(document).ready(function() {

        $('#upload_button').on('click', function() {
            $('#excel_file').click();
        });

        $('#excel_file').on('change', function() {
            if (this.files[0]) {
                var formData = new FormData();
                formData.append('excel_file', this.files[0]);
                formData.append('action', 'upload'); // Add this line to specify the action

                $.ajax({
                    url: 'api/open_invoice_controller.php', // Update this path if needed
                    type: 'POST',
                    data: formData,
                    async: true,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json', // Add this line to expect JSON response
                    success: function(response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log(xhr.responseText); // Log the full response for debugging
                        alert('An error occurred: ' + error);
                    }
                });
            }
        });
    });
</script>