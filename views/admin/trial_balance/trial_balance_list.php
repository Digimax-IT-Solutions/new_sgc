<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('trial_balance');
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
                        sendToDiscord(data, fromDate, toDate); // Send data to Discord
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

        function sendToDiscord(data, fromDate, toDate) {
            const webhookUrl = 'https://discord.com/api/webhooks/1281078575428145203/fULygPZyyVGZ8voXOaxr3x9B9UHB0gu7pPSjZM5G3CsPxDz6fRl36gG0YjznEoq-0-bt';



            let discordMessage = "**Trial Balance Report**\n";
            discordMessage += `Date Range: From ${formatDate(fromDate)} To ${formatDate(toDate)}\n\n`;
            discordMessage += "```\n";

            // Define column widths
            const codeWidth = 15;
            const descWidth = 40;
            const numberWidth = 15;

            // Helper function for padding
            function pad(str, length, char = ' ') {
                return str.padEnd(length, char);
            }

            // Header
            discordMessage += `${pad('Code', codeWidth)} | ${pad('Description', descWidth)} | ${pad('Beg. Balance', numberWidth)} | ${pad('Transaction', numberWidth)} | ${pad('End. Balance', numberWidth)}\n`;
            discordMessage += `${'-'.repeat(codeWidth)}-|-${'-'.repeat(descWidth)}-|-${'-'.repeat(numberWidth)}-|-${'-'.repeat(numberWidth)}-|-${'-'.repeat(numberWidth)}\n`;

            // Data rows
            data.forEach(item => {
                discordMessage += `${pad(item.account_code, codeWidth)} | ${pad(item.account_description, descWidth)} | ${formatNumber(item.beginning_balance).padStart(numberWidth)} | ${formatNumber(item.total_trial_balance).padStart(numberWidth)} | ${formatNumber(item.ending_balance).padStart(numberWidth)}\n`;
            });

            discordMessage += "```\n";

            const message = { content: discordMessage };

            fetch(webhookUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(message)
            })
                .then(response => {
                    if (response.ok) {
                        console.log('Data sent to Discord successfully.');
                    } else {
                        console.error('Failed to send data to Discord.');
                    }
                })
                .catch(error => {
                    console.error('Error sending data to Discord:', error);
                });
        }

        // Helper function to format dates (assuming you have one)
        function formatDate(dateString) {
            const [year, month, day] = dateString.split('-');
            return `${day}/${month}/${year}`;
        }

        // Helper function to repeat a string (assuming you have one)
        function str_repeat(string, length) {
            return Array(length + 1).join(string);
        }

        // Helper function for sprintf (assuming you have one)
        function sprintf(format, ...args) {
            return format.replace(/%(-?\d+)?s/g, (match, width) => {
                const str = args.shift() || '';
                if (width) {
                    const padding = ' '.repeat(Math.max(0, width - str.length));
                    return width > 0 ? padding + str : str + padding;
                }
                return str;
            });
        }

        function formatNumber(number) {
            return parseFloat(number).toLocaleString('en-US', { style: 'decimal', minimumFractionDigits: 2, maximumFractionDigits: 2 });
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