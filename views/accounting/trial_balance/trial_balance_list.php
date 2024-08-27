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
            <h1 class="h3 mb-3">Trial Balance</h1>
            <div class="card">
                <div class="card-body">
                    <form id="filter-form" class="mb-4">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="from_date" class="form-label">From:</label>
                                <input type="date" class="form-control" id="from_date" name="from_date"
                                    value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-4">
                                <label for="to_date" class="form-label">To:</label>
                                <input type="date" class="form-control" id="to_date" name="to_date"
                                    value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
                            </div>
                        </div>
                    </form>

                    <div id="results-container" class="d-none">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4 id="date-range" class="mb-0"></h4>

                        </div>
                        <div class="table-container">
                            <table id="trial-balance-table" class="table table-sm table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Account Code</th>
                                        <th>Account Description</th>
                                        <th>Beg. Balance</th>
                                        <th>Transaction</th>
                                        <th>End. Balance</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<?php require 'views/templates/footer.php' ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('filter-form');
        const resultsContainer = document.getElementById('results-container');
        const dateRange = document.getElementById('date-range');
        const printButton = document.getElementById('print-button');

        let table;

        filterForm.addEventListener('submit', function (e) {
            e.preventDefault();
            const fromDate = document.getElementById('from_date').value;
            const toDate = document.getElementById('to_date').value;

            fetch('api/trial_balance_controller.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `from_date=${formatDate(fromDate)}&to_date=${formatDate(toDate)}`
            })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert('Error: ' + data.error);
                    } else {
                        resultsContainer.classList.remove('d-none');
                        dateRange.textContent = `From: ${formatDate(fromDate)} To: ${formatDate(toDate)}`;
                        renderDataTable(data);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while fetching data.');
                });
        });

        printButton.addEventListener('click', function () {
            window.print();
        });

        function formatDate(dateString) {
            const [year, month, day] = dateString.split('-');
            return `${day}/${month}/${year}`;
        }

        function renderDataTable(data) {
            if (table) {
                table.destroy();
            }

            table = $('#trial-balance-table').DataTable({
                data: data,
                columns: [
                    { data: 'account_code' },
                    { data: 'account_description' },
                    {
                        data: 'beginning_balance',
                        render: $.fn.dataTable.render.number(',', '.', 2),
                        className: 'text-end'
                    },
                    {
                        data: 'total_trial_balance',
                        render: $.fn.dataTable.render.number(',', '.', 2),
                        className: 'text-end'
                    },
                    {
                        data: 'ending_balance',
                        render: $.fn.dataTable.render.number(',', '.', 2),
                        className: 'text-end'
                    }
                ],
                responsive: true,
                ordering: false,
                paging: false,
                info: false,
                scrollY: '50vh',
                scrollCollapse: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search..."
                },
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
                        filename: 'trial_balance_export',
                        exportOptions: {
                            modifier: {
                                search: 'none'
                            }
                        }
                    },
                    {
                        extend: 'excel',
                        text: 'Export Excel',
                        filename: 'trial_balance_export',
                        exportOptions: {
                            modifier: {
                                search: 'none'
                            }
                        }
                    },
                    {
                        extend: 'pdf',
                        text: 'Export PDF',
                        filename: 'trial_balance_export',
                        exportOptions: {
                            modifier: {
                                search: 'none'
                            }
                        }
                    }
                ]
            });
        }
    });
</script>

<style>
    .dataTables_wrapper .dataTables_filter {
        float: right;
        text-align: right;
    }

    .dataTables_wrapper .dataTables_length {
        float: left;
    }

    .dt-buttons {
        margin-bottom: 10px;
    }

    .dt-button {
        padding: 5px 10px;
        border-radius: 2px;
        border: 1px solid #ccc;
        background-color: #f8f9fa;
    }

    .dt-button:hover {
        background-color: #e9ecef;
    }

    @media print {

        .dt-buttons,
        .dataTables_filter,
        .dataTables_info,
        .dataTables_paginate {
            display: none !important;
        }
    }
</style>