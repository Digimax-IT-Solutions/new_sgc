<?php
// Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('receive_payment');


$accounts = ChartOfAccount::all();
$customers = Customer::all();
$products = Product::all();
$terms = Term::all();
$locations = Location::all();
$payment_methods = PaymentMethod::all();

$payments = Payment::all();
$orPayments = OfficialReceipt::all();

$page = 'sales_invoice'; // Set the variable corresponding to the current page
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3"><strong> Cash </strong>Invoice</h1>
            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_payment_method') ?>
                    <?php displayFlashMessage('delete_payment_method') ?>
                    <?php displayFlashMessage('update_payment_method') ?>
                    <!-- Default box -->
                    <div class="card">
                    <div class="card shadow mb-4">
                            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                <div>
                                <a href="draft_or" class="btn btn-lg btn-outline-secondary me-2 mb-2">
                                    <i class="fab fa-firstdraft fa-lg me-2"></i> Drafts
                                </a>
                                <a href="void_or" class="btn btn-lg btn-outline-danger me-2 mb-2">
                                    <i class="fas fa-file-excel fa-lg me-2"></i> Voids
                                </a>

                                <a href="#" class="btn btn-lg btn-outline-success me-2 mb-2" id="apvDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-plus fa-lg me-2"></i> Create Receive Payment
                                    </a>
                                <ul class="dropdown-menu" aria-labelledby="apvDropdown">
                                    <li><a class="dropdown-item" href="official_receipt">Cash Invoice</a></li>
                                    <li><a class="dropdown-item" href="customer_payment">Collection Receipt</a></li>
                                </ul>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive" style="overflow-x: auto;">
                                <table class="table table-bordered" id="dataTable"
                                    style="min-width: 1000px; width: 100%;" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th style="white-space: nowrap;">CI No.</th>
                                            <th style="white-space: nowrap;">Customer Name</th>
                                            <th style="white-space: nowrap;">Payment Date</th>
                                            <th style="white-space: nowrap;">Payment Method</th>
                                            <th style="white-space: nowrap;">Paid Amount</th>
                                            <th style="white-space: nowrap;">Ref. No./ Check No.</th>
                                            <th style="white-space: nowrap;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orPayments as $orPayment): ?>
                                            <?php if ($orPayment->status != 3 && $orPayment->status != 4): ?>
                                                <tr>
                                                    <td style="white-space: nowrap;">
                                                        <?= htmlspecialchars($orPayment->ci_no) ?></td>
                                                    <td style="white-space: nowrap;">
                                                        <?= htmlspecialchars($orPayment->customer_name) ?>
                                                    </td>
                                                    <td style="white-space: nowrap;">
                                                        <?= htmlspecialchars($orPayment->or_date) ?>
                                                    </td>
                                                    <td style="white-space: nowrap;">
                                                        <?= htmlspecialchars($orPayment->payment_method) ?>
                                                    </td>
                                                    <td style="white-space: nowrap;">
                                                        <b>₱<?= number_format($orPayment->total_amount_due, 2, '.', ','); ?></b>
                                                    </td>
                                                    <td style="white-space: nowrap;">
                                                        <?= htmlspecialchars($orPayment->check_no) ?>
                                                    </td>
                                                    <td style="white-space: nowrap;">
                                                        <a href="view_or?action=view&id=<?= htmlspecialchars($orPayment->id) ?>"
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
                <!-- /.card -->
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
            columnDefs: [
                { className: "text-start", targets: '_all' },
                { width: "120px", targets: 0 },
                { width: "150px", targets: [1, 2, 3, 4, 5] },
                { width: "200px", targets: 6 },
                { orderable: false, targets: 6 }
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
                        var tableData = dt.rows().data().toArray();
                        var content = header;

                        var cellWidth = 25; // Adjusted width
                        var separator = '+' + '-'.repeat(cellWidth * 6 + 6) + '+\n';

                        // Add table header
                        content += separator;
                        content += '|' + [
                            'Payment ID', 'Customer Name', 'Payment Date', 'Payment Method',
                            'Paid Amount', 'Ref. No.'
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
                                row[5]
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

            var dates = table.column(2).data().map(function (d) {
                return new Date(d);
            }).filter(function (d) {
                return !isNaN(d.getTime());
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