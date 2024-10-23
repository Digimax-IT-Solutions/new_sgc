<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('reports');
?>

<?php require 'views/templates/header.php'; ?>
<?php require 'views/templates/sidebar.php'; ?>
<div class="main">
    <?php require 'views/templates/navbar.php'; ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">

            <h1 class="h3 mb-3"><strong>Balance</strong> Sheet</h1>

            <div class="row">
                <div class="col-12">
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="from_date">From:</label>
                                        <input type="date" class="form-control" id="from_date" name="from_date">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="to_date">To:</label>
                                        <input type="date" class="form-control" id="to_date" name="to_date">
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
                            <center>
                                <h1>BALANCE SHEET</h1><span>All Transactions</span>
                            </center>
                            <br><br>
                            <div class="row">
                                <?php BalanceSheet::displayBalanceSheet(); // Display the balance sheet using the BalanceSheet class ?>
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