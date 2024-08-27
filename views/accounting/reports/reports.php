<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>

<div class="main">
    <?php require 'views/templates/navbar.php' ?>

    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-2"><strong>Reports Dashboard</strong></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Reports</li>
                            </ol>
                        </nav>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#reportModal">
                        <i data-feather="file-text" class="feather-sm me-1"></i> Generate Custom Report
                    </button>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Financial Reports</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <?php
                                $financialReports = [
                                    ['icon' => 'trending-up', 'title' => 'Profit and Loss', 'link' => 'profit_loss'],
                                    ['icon' => 'bar-chart-2', 'title' => 'Balance Sheet', 'link' => 'balance_sheet'],
                                    ['icon' => 'dollar-sign', 'title' => 'Cash Flow', 'link' => 'cash_flow'],
                                ];

                                foreach ($financialReports as $report) {
                                    echo '<div class="col-md-4 col-sm-6">
                                        <a href="' . $report['link'] . '" class="text-decoration-none">
                                            <div class="card bg-light h-100 shadow-sm hover-effect">
                                                <div class="card-body text-center d-flex flex-column justify-content-center">
                                                    <i data-feather="' . $report['icon'] . '" class="feather-lg mb-3"></i>
                                                    <h6 class="card-title">' . $report['title'] . '</h6>
                                                </div>
                                            </div>
                                        </a>
                                    </div>';
                                }
                                ?>
                            </div>
                            <div class="mt-4">
                                <h6 class="mb-3">Book of Accounts</h6>
                                <div class="row g-2">
                                    <?php
                                    $bookOfAccounts = [
                                        'Sales Book',
                                        'Purchase Book',
                                        'Inventory Book',
                                        'Disbursement Book',
                                        'Cash Receipt Book',
                                        'General Ledger',
                                        'General Journal'
                                    ];

                                    foreach ($bookOfAccounts as $book) {
                                        $link = strtolower(str_replace(' ', '_', $book)) . '.php';
                                        echo '<div class="col-md-4 col-sm-6">
                                            <a href="' . $link . '" class="btn btn-outline-primary btn-sm w-100 text-start">
                                                <i data-feather="book" class="feather-sm me-2"></i>' . $book . '
                                            </a>
                                        </div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Management Reports</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <?php
                                $managementReports = [
                                    [
                                        'icon' => 'shopping-cart',
                                        'title' => 'Sales and Receivables',
                                        'link' => 'sales_receivables.php',
                                        'subItems' => [
                                            'Sales by Item Details',
                                            'Sales by Customer Details',
                                            'Sales by Item Summary',
                                            'Sales by Customer Summary',
                                            'Sales by Rep Summary',
                                            'Accounts Receivable Details',
                                            'Accounts Receivable Summary',
                                            'Outstanding Sales Invoice'
                                        ]
                                    ],
                                    [
                                        'icon' => 'shopping-bag',
                                        'title' => 'Purchases and Payables',
                                        'link' => 'purchases_payables.php',
                                        'subItems' => [
                                            'Purchases by Item Details',
                                            'Purchases by Item Summary',
                                            'Purchases by Vendor Details',
                                            'Purchases by Vendor Summary',
                                            'Accounts Payable Details',
                                            'Accounts Payable Summary',
                                            'Unpaid Purchases'
                                        ]
                                    ]
                                ];

                                foreach ($managementReports as $report) {
                                    echo '<div class="col-md-6">
                                        <div class="card bg-light mb-3 shadow-sm hover-effect">
                                            <div class="card-body text-center">
                                                <i data-feather="' . $report['icon'] . '" class="feather-lg mb-3"></i>
                                                <h6 class="card-title">' . $report['title'] . '</h6>                                        
                                            </div>
                                        </div>
                                        <div class="list-group list-group-flush shadow-sm">
                                            ' . implode('', array_map(function ($item) {
                                        $link = strtolower(str_replace(' ', '_', $item)) . '.php';
                                        return '<a href="' . $link . '" class="list-group-item list-group-item-action">
                                                    <i data-feather="file-text" class="feather-sm me-2"></i>' . $item . '
                                                </a>';
                                    }, $report['subItems'])) . '
                                        </div>
                                    </div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require 'views/templates/footer.php' ?>
</div>

<!-- Custom Report Modal -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reportModalLabel">Generate Custom Report</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="customReportForm">
                    <div class="mb-3">
                        <label for="reportType" class="form-label">Report Type</label>
                        <select class="form-select" id="reportType" required>
                            <option value="">Select a report type</option>
                            <option value="financial">Financial Report</option>
                            <option value="sales">Sales Report</option>
                            <option value="inventory">Inventory Report</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="dateRange" class="form-label">Date Range</label>
                        <input type="text" class="form-control" id="dateRange" required>
                    </div>
                    <div class="mb-3">
                        <label for="format" class="form-label">Format</label>
                        <select class="form-select" id="format" required>
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                            <option value="csv">CSV</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="generateReport">Generate Report</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Feather Icons
        feather.replace();

        // Initialize Flatpickr for date range picker
        flatpickr("#dateRange", {
            mode: "range",
            dateFormat: "Y-m-d"
        });

        // Custom report generation
        document.getElementById('generateReport').addEventListener('click', function () {
            const form = document.getElementById('customReportForm');
            if (form.checkValidity()) {
                // Here you would typically send an AJAX request to generate the report
                alert('Generating report... This is where you would implement the actual report generation.');
                $('#reportModal').modal('hide');
            } else {
                form.reportValidity();
            }
        });

        // Add hover effect to cards
        document.querySelectorAll('.hover-effect').forEach(card => {
            card.addEventListener('mouseenter', function () {
                this.classList.add('shadow');
            });
            card.addEventListener('mouseleave', function () {
                this.classList.remove('shadow');
            });
        });
    });
</script>