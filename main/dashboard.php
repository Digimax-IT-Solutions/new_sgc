<?php include __DIR__ . ('/includes/header.php'); ?>

<style>
/* Your existing styles */

.info-card {
    background-color: rgb(0, 149, 77);
    border-radius: 20px;
    padding: 20px;
    margin: 10px;
    text-align: center;
    color: white;
}

.info-card i {
    font-size: 2em;
    color: #007bff;
    /* Adjust the icon color */
}
</style>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <!-- ... Your existing content header ... -->

    <section class="content">
        <div class="container-fluid">
            <br><br>
            <center>
                <img src="../images/conogas.png" style="height: 250px;" alt="">
            </center>
            <br><br>

            <!-- Example Information Card -->
            <div class="row">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-users"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text"><a href="customer_list">Total Customers</a></span>
                            <span class="info-box-number" id="totalCustomers">Loading...</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-truck"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text"><a href="vendor_list">Total Suppliers</a></span>
                            <span class="info-box-number" id="totalVendors">Loading...</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success elevation-1"><i class="fas fa-cubes"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text"><a href="item_list">Total Items</a></span>
                            <span class="info-box-number" id="totalItems">Loading...</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>
                    <!-- /.info-box -->
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="info-box mb-3">
                        <span class="info-box-icon bg-success elevation-1"><i
                                class="fas fa-file-invoice-dollar"></i></span>

                        <div class="info-box-content">
                            <span class="info-box-text">Total Purchase Order</span>
                            <span class="info-box-number" id="totalPurchase">0</span>
                        </div>
                        <!-- /.info-box-content -->
                    </div>

                    <!-- /.info-box -->
                </div>

            </div>
        </div>
        <!-- /.Invoice Chart -->
        <div class="row">
            <section class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Invoice Donut Graph
                        </h3>
                        <div class="card-tools">
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content p-1">
                            <div class="chart tab-pane active" id="combined-chart" style="position: relative; height: 250px;">
                                <canvas id="combined-chart-canvas" height="300" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <!-- /.Purchase order Chart -->
            <section class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            <i class="fas fa-chart-pie mr-1"></i>
                            Purchase Order Received
                        </h3>
                        <div class="card-tools">
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="tab-content p-0">
                            <div class="chart tab-pane active" id="combined-chart" style="position: relative; height: 250px;">
                                <canvas id="combineded-chart-canvas" height="300" style="height: 300px;"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
        </div>
    </section>
    <!-- /.content -->

  
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
    function fetchData() {
        fetch('modules/purchase/data_chart.php')
            .then(response => response.json())
            .then(data => {
                updateChart('combined-chart-canvas', data);
            })
            .catch(error => console.error('Error fetching data:', error));
    }

    function updateChart(chartId, data) {
        var ctx = document.getElementById(chartId).getContext('2d');

        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Unpaid', 'Paid'],
                datasets: [{
                    data: [data.unpaidCount, data.paidCount],
                    backgroundColor: ['rgba(255, 99, 132, 0.2)', 'rgba(40, 167, 69, 0.2)'],
                    borderColor: ['rgba(255, 99, 132, 1)', 'rgba(40, 167, 69, 1)'],
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '50%', // Adjust as needed
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom' // You can adjust the position
                    },
                    title: {
                        display: true,
                        text: 'Invoice Status'
                    }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        fetchData();
    });
</script>

<script>
    function fetchDataPurchase() {
        fetch('modules/purchase/purchase_chart.php')
            .then(response => response.json())
            .then(data => {
                updateChartPurchase('combineded-chart-canvas', data);
            })
            .catch(error => console.error('Error fetching purchase data:', error));
    }

    function updateChartPurchase(chartId, data) {
        var ctx = document.getElementById(chartId).getContext('2d');

        var myChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Received', 'Waiting for delivery','Partially received'],
                datasets: [{
                    data: [data.receivedCount, data.waitingCount, data.partiallyCount],
                    backgroundColor: ['rgba(40, 167, 69, 0.2)', 'rgba(255, 99, 132, 0.2)', 'rgba(254, 229, 123, 0.2)'],
                    borderColor: ['rgba(40, 167, 69, 1)', 'rgba(255, 99, 132, 1)', 'rgba(254, 229, 123, 1)'],
                    borderWidth: 1,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '50%', // Adjust as needed
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom' // You can adjust the position
                    },
                    title: {
                        display: true,
                        text: 'Purchase Order Status'
                    }
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        fetchDataPurchase();
    });
</script>

    <script>
    $(function() {
        $.ajax({
            url: 'modules/masterlist/items/get_total_items.php',
            dataType: 'json',
            success: function(data) {

                var totalItems = data.totalItems || 0;
                $('#totalItems').text(totalItems);
            },
            error: function() {
                $('#totalItems').text('Error fetching data');
            }
        });
    });
    </script>
    <script>
    $(function() {
        $.ajax({
          url: 'modules/customers/get_total_customer.php',
          dataType: 'json',
            // your existing AJAX configuration
            success: function(response) {
             
                if (response) {
                    // Update the total counts for customers and vendors
                    $('#totalCustomers').text(response.totalCustomers);
                    $('#totalVendors').text(response.totalVendors);
                } else {
                    console.error('Invalid response format.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching totals:', error);
            }
        });
    });
    </script>
        <script>
    $(function() {
        $.ajax({
          url: 'modules/vendors/get_total_vendor.php',
          dataType: 'json',
            // your existing AJAX configuration
            success: function(response) {
             
                if (response) {

                    $('#totalVendors').text(response.totalVendors);
                } else {
                    console.error('Invalid response format.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching totals:', error);
            }
        });
    });
    </script>
    <script>
    $(function() {
        $.ajax({
            url: 'modules/purchase/get_total_purchase.php',
            dataType: 'json',
            success: function(data) {

                var totalPurchase = data.totalPurchase || 0;
                $('#totalPurchase').text(totalPurchase);
            },
            error: function() {
                $('#totalPurchase').text('Error fetching data');
            }
        });
    });
    </script>


    <?php include('includes/footer.php'); ?>