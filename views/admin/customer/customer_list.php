<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('customer');
//require_once 'api/category_controller.php';

$customers = Customer::all();
// Example usage to get total number of customers
$totalCustomers = Customer::total();

$customer = null;
if (get('action') === 'update') {
    $customer = Customer::find(get('id'));
}

$page = 'customer_list';
?>


<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <h1 class="h3 mb-3"><strong>Customer</strong> List</h1>
            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_customer') ?>
                    <?php displayFlashMessage('delete_customer') ?>
                    <?php displayFlashMessage('update_customer') ?>
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">
                            <br><br>
                            <!-- CUSTOMER STATS -->
                            <div class="row">
                                <div class="col-sm-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h5 class="card-title">Total Customer</h5>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="stat text-primary">
                                                        <i class="align-middle" data-feather="users"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <h1 class="mt-1 mb-3"><?= $totalCustomers; ?></h1>
                                            <div class="mb-0">
                                                <span class="text-danger"><i class="mdi mdi-arrow-bottom-right"></i>
                                                </span>
                                                <span class="text-muted"> </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h5 class="card-title">Active Customer</h5>
                                                </div>
                                                <div class="col-auto">
                                                    <div class="stat text-primary">
                                                        <i class="align-middle" data-feather="users"></i>
                                                    </div>
                                                </div>
                                            </div>
                                            <h1 class="mt-1 mb-3"><?= $totalCustomers; ?></h1>
                                            <div class="mb-0">
                                                <span class="text-success"><i class="mdi mdi-arrow-bottom-right"></i>
                                                </span>
                                                <span class="text-muted"> </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <div class="col">
                                                    <h5 class="card-title">Inactive</h5>
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
                                    <h6 class="m-0 font-weight-bold text-primary">Customers</h6>
                                    <div class="d-flex justify-content-end">
                                        <a class="btn btn-sm btn-outline-secondary me-2" id="upload_button">
                                            <i class="fas fa-upload"></i> Upload
                                        </a>
                                        <input type="file" name="excel_file" id="excel_file" accept=".xlsx, .xls"
                                            style="display: none;">
                                        <a href="create_customer" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus"></i> New Customer
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <!-- Left column for customer list -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Customer List</h5>
                                            <table id="customerTable" class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>AR Balance</th>
                                                        <th>Credit Memo</th>

                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($customers as $customer): ?>
                                                        <tr>
                                                            <td><?= $customer->customer_name ?></td>
                                                            <td><?= $customer->credit_balance ?></td>
                                                            <td><?= $customer->total_credit_memo ?></td>

                                                            <td>
                                                                <a class="text-primary"
                                                                    href="view_customer?action=update&id=<?= $customer->id ?>">
                                                                    <i class="fas fa-edit"></i> Update
                                                                </a>
                                                                <a class="text-danger ml-2"
                                                                    href="api/masterlist/customer_controller.php?action=delete&id=<?= $customer->id ?>">
                                                                    <i class="fas fa-trash-alt"></i> Delete
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right column for transaction list -->
                                <div class="col-md-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5 class="card-title">Transaction List</h5>
                                            <table id="transactionTable" class="table">
                                                <thead>
                                                    <tr>
                                                        <th>Date</th>
                                                        <th>Amount</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Example row, replace with actual transaction data -->
                                                    <tr>
                                                        <td>2024-06-28</td>
                                                        <td>$100.00</td>
                                                        <td>Payment for services</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
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
<script>
    $(document).ready(function () {

        $('#upload_button').on('click', function () {
            $('#excel_file').click();
        });

        $('#excel_file').on('change', function () {
            if (this.files[0]) {
                var formData = new FormData();
                formData.append('excel_file', this.files[0]);
                formData.append('action', 'upload'); // Add this line to specify the action

                $.ajax({
                    url: 'api/masterlist/customer_controller.php', // Update this path if needed
                    type: 'POST',
                    data: formData,
                    async: true,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json', // Add this line to expect JSON response
                    success: function (response) {
                        if (response.status === 'success') {
                            alert(response.message);
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log(xhr.responseText); // Log the full response for debugging
                        alert('An error occurred: ' + error);
                    }
                });
            }
        });
    });
</script>