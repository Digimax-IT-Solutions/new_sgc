<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}


?>

<?php require 'views/templates/header.php'; ?>
<?php require 'views/templates/sidebar.php'; ?>
<div class="main">
    <?php require 'views/templates/navbar.php'; ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <style>
                .profit-loss-statement {
                    font-family: Arial, sans-serif;
                    max-width: 800px;
                    margin: 0 auto;
                    padding: 20px;
                    background-color: #fff;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }

                .profit-loss-statement h1 {
                    color: #0D8040;
                    text-align: center;
                    margin-bottom: 5px;
                }

                .profit-loss-statement h2 {
                    color: #444;
                    text-align: center;
                    margin-top: 0;
                    margin-bottom: 5px;
                }

                .profit-loss-statement h3 {
                    color: #666;
                    text-align: center;
                    font-weight: normal;
                    margin-top: 0;
                    margin-bottom: 20px;
                }

                .report-body {
                    border-top: 2px solid #ddd;
                    padding-top: 20px;
                }

                .report-section {
                    margin-bottom: 20px;
                }

                .section-header {
                    color: #0D8040;
                    border-bottom: 1px solid #ddd;
                    padding-bottom: 5px;
                    margin-bottom: 10px;
                }

                .report-item {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 10px;
                }

                .report-item .item-label {
                    padding-left: 40px;
                }

                .report-item.total {
                    border-top: 1px solid #ddd;
                    padding-top: 5px;
                    font-weight: bold;
                }

                .report-item.bold {
                    font-size: 1.5em;
                    border-top: 2px solid #0D8040;
                    border-bottom: 2px solid #0D8040;
                    padding: 5px 0;
                }

                .item-label {
                    color: #444;
                }

                .item-amount {
                    color: #000;
                }
            </style>

            <h1 class="h3 mb-3"><strong>Profit</strong> Loss</h1>

            <div class="row">
                <div class="col-12">
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="from_date">From:</label>
                                        <input type="date" class="form-control" id="from_date" name="from_date"
                                            value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="to_date">To:</label>
                                        <input type="date" class="form-control" id="to_date" name="to_date"
                                            value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3 py-4">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-secondary"
                                            id="filter_button">Filter</button>
                                        <button type="button" class="btn btn-primary" id="print_button">Print</button>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <div id="profit-loss-content">
                                <!-- We'll load the content here using JavaScript -->
                                <div id="loading">Loading...</div>
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
        document.addEventListener('DOMContentLoaded', function() {
            function updateProfitLossContent(data) {
                const loadingElement = document.getElementById('loading');
                const contentElement = document.getElementById('profit-loss-content');

                if (loadingElement) {
                    loadingElement.style.display = 'none';
                }

                if (contentElement) {
                    contentElement.innerHTML = data;
                } else {
                    console.error('Profit and loss content element not found');
                }
            }

            function showLoading() {
                const loadingElement = document.getElementById('loading');
                const contentElement = document.getElementById('profit-loss-content');

                if (loadingElement) {
                    loadingElement.style.display = 'block';
                }

                if (contentElement) {
                    contentElement.innerHTML = '';
                }
            }

            function showError(message) {
                const loadingElement = document.getElementById('loading');
                if (loadingElement) {
                    loadingElement.textContent = message;
                } else {
                    console.error(message);
                }
            }

            function loadProfitLoss(fromDate = null, toDate = null) {
                let url = 'api/profit_loss.php';
                let method = 'GET';
                let body = null;

                if (fromDate && toDate) {
                    method = 'POST';
                    body = new URLSearchParams({
                        from_date: fromDate,
                        to_date: toDate
                    });
                }

                showLoading();

                fetch(url, {
                        method: method,
                        body: body
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                    })
                    .then(data => {
                        updateProfitLossContent(data);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showError('An error occurred while loading the Profit and Loss statement.');
                    });
            }

            // Initial load
            loadProfitLoss();

            // Filter button click handler
            const filterButton = document.getElementById('filter_button');
            if (filterButton) {
                filterButton.addEventListener('click', function() {
                    const fromDate = document.getElementById('from_date');
                    const toDate = document.getElementById('to_date');
                    if (fromDate && toDate) {
                        loadProfitLoss(fromDate.value, toDate.value);
                    } else {
                        console.error('Date input elements not found');
                    }
                });
            } else {
                console.error('Filter button not found');
            }

            // Print button click handler
            const printButton = document.getElementById('print_button');
            if (printButton) {
                printButton.addEventListener('click', function() {
                    window.print();
                });
            } else {
                console.error('Print button not found');
            }
        });
    </script>