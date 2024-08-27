<?php
require_once '_guards.php';
Guard::adminOnly();
$page = 'transaction_entries_list';
require 'views/templates/header.php';
require 'views/templates/sidebar.php';
?>

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
    <?php require 'views/templates/navbar.php' ?>
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3"><strong>Transaction</strong> Entries</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>

                    <li class="breadcrumb-item active" aria-current="page">Transaction Entries</li>
                </ol>
            </nav>
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="from_date" class="form-label">From:</label>
                            <input type="date" class="form-control" id="from_date" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-3">
                            <label for="to_date" class="form-label">To:</label>
                            <input type="date" class="form-control" id="to_date" value="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="col-md-3 d-flex align-items-end">
                            <button class="btn btn-secondary me-2" id="filter_button">Filter</button>
                            <button class="btn btn-primary" id="print_button">Print</button>
                        </div>
                    </div>
                    <h4 id="date_range" class="text-center mb-3"></h4>

                    <div class="table-responsive">
                        <table id="transaction_table" class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Trans</th>
                                    <th>Ref No</th>
                                    <th>Name</th>
                                    <th>Item</th>
                                    <th>Qty Sold</th>
                                    <th>Qty Purch</th>
                                    <th>Ave. Cost</th>
                                    <th>Cost</th>
                                    <th>Selling</th>
                                    <th>COGS</th>
                                    <th>Amt Sold</th>
                                    <th>Acct</th>
                                    <th>Debit</th>
                                    <th>Credit</th>
                                    <th>Amt</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
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



        var table = $('#transaction_table').DataTable({
            responsive: true,
            ordering: false,
            paging: false,
            info: false,
            scrollY: '100vh',
            scrollCollapse: true,
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search..."
            },
            columnDefs: [
                { className: "text-end", targets: [5, 6, 7, 8, 9, 10, 11, 13, 14, 15] },
                { className: "text-start", targets: [0, 1, 2, 3, 4, 12] },
                { width: "120px", targets: 0 },
                { width: "100px", targets: [1, 2] },
                { width: "150px", targets: [3, 12] },
                { width: "200px", targets: 4 },
                { width: "80px", targets: [5, 6, 7, 8, 9, 10, 11] },
                { width: "100px", targets: [13, 14, 15] },
                // Set visibility for default columns
                { visible: true, targets: [0, 1, 2, 3, 12, 13, 14, 15] }, // Default visible columns
                { visible: false, targets: [4, 5, 6, 7, 8, 9, 10, 11] } // Hidden columns
            ],
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'colvis',
                    text: 'Show/Hide Columns',
                    className: 'btn btn-primary'
                },
                {
                    extend: 'csv',
                    text: 'Export CSV',
                    filename: 'transaction_entries_export',
                    exportOptions: {
                        modifier: {
                            search: 'none'
                        }
                    }
                },
                {
                    extend: 'excel',
                    text: 'Export Excel',
                    filename: 'transaction_entries_export',
                    exportOptions: {
                        modifier: {
                            search: 'none'
                        }
                    }
                },
                {
                    extend: 'pdf',
                    text: 'Export PDF',
                    filename: 'transaction_entries_export',
                    exportOptions: {
                        modifier: {
                            search: 'none'
                        }
                    }
                }
            ]
        });




        $('#filter_button').click(function () {
            var fromDate = $('#from_date').val();
            var toDate = $('#to_date').val();

            $.ajax({
                url: 'api/transaction_entries_controller.php',
                method: 'POST',
                data: { action: 'filter', from_date: fromDate, to_date: toDate },
                success: function (response) {
                    table.clear();
                    $('#date_range').text('Transaction Entries: ' + formatDate(fromDate) + ' - ' + formatDate(toDate));

                    if (response.length === 0) {
                        table.row.add(['No transaction entries found', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '']).draw();
                    } else {
                        var groupedEntries = groupEntriesByRefNo(response);
                        renderTableData(table, groupedEntries);
                    }
                },
                error: function (xhr, status, error) {
                    console.error(error);
                    alert('An error occurred while fetching transaction entries.');
                }
            });
        });

        $('#print_button').click(function () {
            window.print();
        });
    });

    function groupEntriesByRefNo(entries) {
        return entries.reduce((groups, entry) => {
            (groups[entry.ref_no] = groups[entry.ref_no] || []).push(entry);
            return groups;
        }, {});
    }

    function renderTableData(table, groupedEntries) {
        Object.entries(groupedEntries).forEach(([refNo, entries]) => {
            let totalDebit = 0, totalCredit = 0;
            let hasNonZeroEntries = false;

            entries.forEach(entry => {
                const debit = parseFloat(entry.debit) || 0;
                const credit = parseFloat(entry.credit) || 0;

                if (debit !== 0 || credit !== 0) {
                    hasNonZeroEntries = true;
                    table.row.add([
                        formatDate(entry.transaction_date),
                        entry.transaction_type,
                        entry.ref_no,
                        entry.name || '',
                        entry.item_name || '',
                        entry.qty_sold || '',
                        formatNumber(entry.qty_purchased),
                        formatNumber(entry.ave_cost),
                        formatNumber(entry.cost),
                        formatNumber(entry.selling_price),
                        formatNumber(entry.cogs),
                        formatNumber(entry.amount_sold),
                        entry.account_description,
                        formatNumber(debit),
                        formatNumber(credit),
                        formatNumber(entry.balance)
                    ]);
                    totalDebit += debit;
                    totalCredit += credit;
                }
            });

            if (hasNonZeroEntries) {
                table.row.add([
                    'Total', '', '', '', '', '', '', '', '', '', '', '',
                    '',
                    formatNumber(totalDebit),
                    formatNumber(totalCredit),
                    ''
                ]).nodes().to$().addClass('table-secondary fw-bold');
            }
        });
        table.draw();
    }

    function formatDate(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric', month: 'long', day: 'numeric'
        });
    }

    function formatNumber(number) {
        return number ? parseFloat(number).toLocaleString('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }) : '';
    }
</script>