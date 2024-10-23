<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('estimate');
$accounts = ChartOfAccount::all();
$customers = Customer::all();
$products = Product::all();
$terms = Term::all();
$locations = Location::all();
$payment_methods = PaymentMethod::all();

$page = 'estimate'; // Set the variable corresponding to the current page
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3"><strong>Estimates</strong></h1>
            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_payment_method') ?>
                    <?php displayFlashMessage('delete_payment_method') ?>
                    <?php displayFlashMessage('update_payment_method') ?>
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <div class="dropdown">
                                <button class="btn bg-transparent btn-outline-secondary dropdown-toggle" type="button"
                                    id="dateDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <span id="selectedDate">Select Date</span>
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="dateDropdown">
                                    <li><a class="dropdown-item" href="#" onclick="selectDate('19 Nov 2023')">19 Nov
                                            2023</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="selectDate('20 Nov 2023')">20 Nov
                                            2023</a></li>
                                    <!-- Add more dates as needed -->
                                </ul>
                            </div>

                            <br><br>
                            <!-- INVOICE STATS -->
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h5 class="card-title">Total Invoices</h5>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="stat text-primary">
                                                        <i class="align-middle" data-feather="truck"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <h1 class="mt-1 mb-3">2.382</h1>
                                            <div class="mb-0">
                                                <span class="text-danger"><i class="mdi mdi-arrow-bottom-right"></i>
                                                    -3.65% </span>
                                                <span class="text-muted">Since last week</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h5 class="card-title">Paid</h5>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="stat text-primary">
                                                        <i class="align-middle" data-feather="users"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <h1 class="mt-1 mb-3">14.212</h1>
                                            <div class="mb-0">
                                                <span class="text-success"><i class="mdi mdi-arrow-bottom-right"></i>
                                                    5.25% </span>
                                                <span class="text-muted">Since last week</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h5 class="card-title">Unpaid</h5>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="stat text-primary">
                                                        <i class="align-middle" data-feather="users"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <h1 class="mt-1 mb-3">14.212</h1>
                                            <div class="mb-0">
                                                <span class="text-success"><i class="mdi mdi-arrow-bottom-right"></i>
                                                    5.25% </span>
                                                <span class="text-muted">Since last week</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h5 class="card-title">Overdue</h5>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="stat text-primary">
                                                        <i class="align-middle" data-feather="users"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <h1 class="mt-1 mb-3">14.212</h1>
                                            <div class="mb-0">
                                                <span class="text-success"><i class="mdi mdi-arrow-bottom-right"></i>
                                                    5.25% </span>
                                                <span class="text-muted">Since last week</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 d-flex justify-content-between align-items-center mb-4">
                                    <h1 class="h3 mb-3"><strong>Invoices</strong></h1>
                                    <div class="d-flex justify-content-end">
                                        <a href="upload" class="btn bg-transparent btn-outline-secondary me-2">
                                            <i class="align-middle" data-feather="upload"></i> Upload
                                        </a>
                                        <a href="create_estimate" class="btn btn-secondary">
                                            <i class="align-middle" data-feather="file-text"></i> Estimate
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Invoice ID</th>
                                                <th>Client</th>
                                                <th>Date</th>
                                                <th>Amount</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Replace with dynamic content from backend or server-side -->
                                            <tr>
                                                <td>INV-001</td>
                                                <td>Client A</td>
                                                <td>2024-06-18</td>
                                                <td>P500.00</td>
                                                <td>Paid</td>
                                            </tr>
                                            <tr>
                                                <td>INV-002</td>
                                                <td>Client B</td>
                                                <td>2024-06-17</td>
                                                <td>P750.00</td>
                                                <td>Unpaid</td>
                                            </tr>
                                            <!-- Add more rows as needed -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div>
</div>
</main>





<?php require 'views/templates/footer.php' ?>

<script>
    function selectDate(date) {
        document.getElementById('selectedDate').innerText = date;
    }
</script>