<?php
// Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('credit_memo');

// Fetch data
$accounts = ChartOfAccount::all();
$customers = Customer::all();
$products = Product::all();
$terms = Term::all();
$locations = Location::all();
$payment_methods = PaymentMethod::all();
$credits = CreditMemo::all();

// Statistics
$totalCount = CreditMemo::getTotalCount();
$unpaidInvoice = CreditMemo::getUnpaidCount();
$paidInvoice = CreditMemo::getPaidCount();

$page = 'credit_memo'; // Set the variable corresponding to the current page
?>

<?php require 'views/templates/header.php'; ?>
<?php require 'views/templates/sidebar.php'; ?>
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
    <style>
        .dataTables_wrapper .sorting:after,
        .dataTables_wrapper .sorting:before,
        .dataTables_wrapper .sorting_asc:after,
        .dataTables_wrapper .sorting_desc:after {
            content: "" !important;
        }
    </style>
    <?php require 'views/templates/navbar.php'; ?>
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3"><strong>Credit</strong> Memo</h1>
            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_credit'); ?>
                    <?php displayFlashMessage('delete_payment_method'); ?>
                    <?php displayFlashMessage('update_payment_method'); ?>
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                                    <div>
                                        <a href="draft_credit" class="btn btn-lg btn-outline-secondary me-2 mb-2">
                                            <i class="fab fa-firstdraft fa-lg me-2"></i> Drafts
                                        </a>
                                        <a href="void_credit" class="btn btn-lg btn-outline-danger me-2 mb-2">
                                            <i class="fas fa-file-excel fa-lg me-2"></i> Voids
                                        </a>
                                        <a href="create_credit_memo" class="btn btn-lg btn-outline-success me-2 mb-2">
                                            <i class="fas fa-plus fa-lg me-2"></i> Create Credit Memo
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Credit No</th>
                                                <th>Customer Name</th>
                                                <th>Date</th>
                                                <th>Memo</th>
                                                <th>Credit Amount</th>
                                                <th>Credit Balance</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($credits as $credit): ?>
                                                <?php if ($credit->status != 3 && $credit->status != 4): // Exclude credits with status 3 and 4 
                                                ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($credit->credit_no) ?></td>
                                                        <td><?= htmlspecialchars($credit->customer_name) ?></td>
                                                        <td><?= htmlspecialchars($credit->credit_date) ?></td>
                                                        <td><?= htmlspecialchars($credit->memo) ?></td>
                                                        <td class="text-right">
                                                            ₱<?= number_format($credit->total_amount_due, 2) ?>
                                                        </td>
                                                        <td><?= htmlspecialchars($credit->credit_balance) ?></td>
                                                        <td>
                                                            <a href="view_credit?action=update&id=<?= htmlspecialchars($credit->id) ?>"
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
                </div>
            </div>
        </div>
    </main>
</div>

<?php require 'views/templates/footer.php'; ?>

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
                var dateColumn = data[2]; // Assuming the date is in the 3rd column (index 2)

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
                    filename: 'credit_memo_export',
                    exportOptions: {
                        modifier: {
                            search: 'none'
                        }
                    }
                },
                {
                    extend: 'excel',
                    text: 'Export Excel',
                    filename: 'credit_memo_export',
                    exportOptions: {
                        modifier: {
                            search: 'none'
                        }
                    }
                },
                {
                    extend: 'pdf',
                    text: 'Export PDF',
                    filename: 'credit_memo_export',
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

            var cellWidth = 25;
            var separator = '+' + '-'.repeat(cellWidth * 6 + 6) + '+\n';

            content += separator;
            content += '|' + [
                'Credit No', 'Customer', 'Date', 'Memo',
                'Credit Amount', 'Credit Balance'
            ].map(h => h.padEnd(cellWidth)).join('|') + '|\n';
            content += separator;

            tableData.forEach(function(row) {
                content += '|' + [
                    row[0],
                    row[1].substring(0, cellWidth - 1),
                    row[2].substring(0, cellWidth - 1),
                    row[3].substring(0, cellWidth - 1),
                    row[4].toString().padStart(cellWidth - 1),
                    row[5].toString().padStart(cellWidth - 1)
                ].map(c => c.padEnd(cellWidth)).join('|') + '|\n';
            });

            content += separator;
            var filename = "credit_memo.txt";
            downloadTXT(content, filename);
        }

        function getHeader() {
            return `
                        ██████╗██████╗ ███████╗██╗██████╗ ██╗████████╗
                        ██╔═══╝██╔══██╗╚═██╔═██║██║██╔══██╗╚█║╚══██╔══╝
                        ██║    ██████╔╝  ██║ ██║██║██████╔╝ ██║   ██║   
                        ██║    ██╔══██╗  ██║ ██║██║██╔═══╝  ██║   ██║   
                        ╚██████╗██║  ██║  ██║ ██║██║██║      ██║   ██║   
                        ╚═════╝╚═╝  ╚═╝  ╚═╝ ╚═╝╚═╝╚═╝      ╚═╝   ╚═╝   
                          `.trim() + '\n\n';
        }

        function downloadTXT(content, filename) {
            var blob = new Blob([content], {
                type: "text/plain;charset=utf-8"
            });
            var link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = filename;
            link.click();
        }

        $('#fromDate, #toDate').on('change', function() {
            table.draw();
        });
    });
</script>