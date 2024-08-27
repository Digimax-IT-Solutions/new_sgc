<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();

$invoices = Invoice::all();
$totalCount = Invoice::getTotalCount();
$unpaidInvoice = Invoice::getUnpaidCount();
$paidInvoice = Invoice::getPaidCount();
$overdueCount = Invoice::getOverdueCount();

$totalAmount = array_sum(array_column($invoices, 'total_amount_due'));
$totalPaid = array_sum(array_column($invoices, 'paid_amount'));
$totalUnpaid = $totalAmount - $totalPaid;
$totalOverdue = array_sum(array_column($invoices, 'overdue_amount'));

$page = 'invoices';
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <main class="content">
        <div class="container-fluid p-0">
            <div class="mb-3">
                <h1 class="h3 d-inline align-middle"><strong>Sales</strong> Invoice</h1>
                <nav aria-label="breadcrumb" class="float-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Sales Invoices</li>
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
                                        Invoices</div>
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
                    <h6 class="m-0 font-weight-bold text-primary">Invoices</h6>
                    <div>
                        <a href="draft_invoice" class="btn btn-sm btn-danger">
                            <i class="fab fa-firstdraft"></i> Draft
                        </a>
                        <a href="void_invoice" class="btn btn-sm btn-secondary">
                            <i class="fas fa-ban"></i> Void
                        </a>
                        <a class="btn btn-sm btn-outline-secondary me-2" id="upload_button">
                            <i class="fas fa-upload"></i> Upload
                        </a>
                        <input type="file" name="excel_file" id="excel_file" accept=".xlsx, .xls"
                            style="display: none;">
                        <a href="create_invoice" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> New Invoice
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="overflow-x: auto;">
                        <table class="table table-bordered" id="dataTable" style="min-width: 1000px; width: 100%;"
                            cellspacing="0">
                            <thead>
                                <tr>
                                    <th style="white-space: nowrap;">Invoice No.</th>
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
                                    <?php if ($invoice->invoice_status != 3 && $invoice->invoice_status != 4): ?>
                                        <tr>
                                            <td style="white-space: nowrap;"><?= htmlspecialchars($invoice->invoice_number) ?>
                                            </td>
                                            <td style="white-space: nowrap;"><?= htmlspecialchars($invoice->customer_name) ?>
                                            </td>
                                            <td style="white-space: nowrap;"><?= htmlspecialchars($invoice->memo) ?></td>
                                            <td style="white-space: nowrap;">
                                                <?= date('M d, Y', strtotime($invoice->invoice_date)) ?>
                                            </td>
                                            <td style="white-space: nowrap;" class="text-right">
                                                ₱<?= number_format($invoice->total_amount_due, 2) ?></td>
                                            <td style="white-space: nowrap;" class="text-center">
                                                <?php
                                                switch ($invoice->invoice_status) {
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
    $(document).ready(function () {
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
                    filename: 'invoice_export',
                    exportOptions: {
                        modifier: {
                            search: 'none'
                        }
                    },
                    customize: function (csv) {
                        return getHeader() + csv;
                    }
                },
                {
                    extend: 'excel',
                    text: 'Export Excel',
                    filename: 'invoice_export',
                    exportOptions: {
                        modifier: {
                            search: 'none'
                        }
                    }
                },
                {
                    extend: 'pdf',
                    text: 'Export PDF',
                    filename: 'invoice_export',
                    exportOptions: {
                        modifier: {
                            search: 'none'
                        }
                    }
                },
                {
                    text: 'Export TXT',
                    action: function (e, dt, button, config) {
                        var header = getHeader();
                        var tableData = dt.data().toArray();
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
                        tableData.forEach(function (row) {
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
                        saveAs(blob, 'invoice_export.txt');
                    }
                }
            ]
        });

        function getHeader() {
            var recordCount = table.rows().count();
            var totalAmount = table.column(4).data().reduce(function (sum, value) {
                return sum + parseFloat(value.replace(/[^\d.-]/g, ''));
            }, 0);

            var dates = table.column(3).data().map(function (d) {
                var parts = d.split(', ');
                if (parts.length === 2) {
                    var datePart = parts[1];
                    return new Date(datePart);
                }
                return null;
            }).filter(function (d) {
                return d !== null;
            });

            var minDate = new Date(Math.min.apply(null, dates));
            var maxDate = new Date(Math.max.apply(null, dates));

            var formatDate = function (date) {
                return date.toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit'
                });
            };

            return 'Taxpayer`s Name: Hideco Sugar Milling Co., Inc.\n' +
                'TIN: 000-123-533-00000\n' +
                'Address: Montebello, Kanaga, Leyte, 6531\n' +
                '\n' +
                'File Name: Invoice\n' +
                'File Type: Text/CSV\n' +
                'Number of Records: ' + recordCount + '\n' +
                'Amount Field Control Total: ₱' + totalAmount.toFixed(2) + '\n' +
                'Period Covered: ' + formatDate(minDate) + ' to ' + formatDate(maxDate) + '\n\n' +
                'Transaction Cutoff: \n' +
                'Extracted by: ' + '<?= $_SESSION['user_name'] ?>' + '\n';
        }
    });
</script>
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
                    url: 'api/invoice_controller.php', // Update this path if needed
                    type: 'POST',
                    data: formData,
                    async: true,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json', // Add this line to expect JSON response
                    success: function (response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log(xhr.responseText); // Log the full response for debugging
                        alert('An error occurred: ' + error);
                    }
                });
            }
        });
    });
</script>