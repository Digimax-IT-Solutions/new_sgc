<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();

?>

<?php require 'views/templates/header.php'; ?>
<?php require 'views/templates/sidebar.php'; ?>
<div class="main">
    <?php require 'views/templates/navbar.php'; ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">

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
                                        <input type="date" class="form-control" id="from_date" name="from_date" value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="to_date">To:</label>
                                        <input type="date" class="form-control" id="to_date" name="to_date" value="<?php echo date('Y-m-d'); ?>">
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
                                <?php ProfitAndLoss::displayProfitAndLoss(); ?>
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
        $(document).ready(function () {
            $('#filter_button').click(function () {
                var fromDate = $('#from_date').val();
                var toDate = $('#to_date').val();

                $.ajax({
                    url: 'api/profit_loss.php',
                    method: 'POST',
                    data: {
                        from_date: fromDate,
                        to_date: toDate
                    },
                    success: function (response) {
                        $('#profit-loss-content').html(response);
                    },
                    error: function () {
                        alert('An error occurred while fetching the Profit and Loss statement.');
                    }
                });
            });

            $('#print_button').click(function () {
                window.print();
            });
        });
    </script>