<?php include __DIR__ . ('/includes/header.php'); ?>
<style>
/* Add styles for active status */
.active {
    color: green;
    /* Change the text color for active status */
}

/* Add styles for inactive status */
.inactive {
    color: red;
    /* Change the text color for inactive status */
}
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Received Payments</h1>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <!-- /.content-header -->

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-left">
                                        <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                        <li class="breadcrumb-item active">Customer Center</li>
                                        <li class="breadcrumb-item active">Received Payments</li>
                                    </ol>
                                </div>
                                <div class="col-sm-6">
                                    <ol class="breadcrumb float-sm-right">
                                        <button class="btn btn-success" data-toggle="modal" data-target="#addItemModal"
                                            style="background-color: rgb(0, 149, 77); color: white;">
                                            New Entry
                                        </button>
                                    </ol>
                                </div><!-- /.col -->
                            </div><!-- /.row -->

                            <br><br>
                            <table id="receivingItemsTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>DATE</th>
                                        <th>CUSTOMER NAME</th>
                                        <th>DELIVERY DATE</th>
                                        <th>TOTAL AMOUNT DUE</th>
                                        <th>ADDRESS</th>
                                        <th>MEMO</th>
                                        <th>STATUS</th>
                                        <th>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Your location data will go here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <?php include('includes/footer.php'); ?>
</div>

