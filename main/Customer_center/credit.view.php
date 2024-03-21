<?php include __DIR__ . ('../../includes/header.php'); ?>
<?php include('connect.php'); ?>

<style>
    .dropdown-toggle:hover,
    .dropdown-toggle:focus {
        background-color: rgb(0, 149, 77);
        color: white;
    }
</style>

<style>
    #salesTable {
        border-collapse: collapse;
        width: 100%;
        table-layout: fixed;
    }

    #salesTable th,
    #salesTable td {

        padding: 1px;
        white-space: nowrap;
        overflow: hidden; /* Hides any overflowing content */
        text-overflow: ellipsis;
    }

    #salesTable tbody tr:hover {

        color: white;
        background-color: rgb(0, 149, 77);
        /* Set your desired background color here */
    }
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Create Credit/Refunds Memo</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                        <li class="breadcrumb-item active">Credit/Refunds</li>
                    </ol>
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
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" id="newTransactionDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: rgb(0, 149, 77); color: white;">
                                            New Transactions
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="newTransactionDropdown">
                                            <a class="dropdown-item" href="create_credit" style="color: rgb(0, 149, 77); background-color: white;">Create Credit</a>
                                            <a class="dropdown-item" href="#" style="color: rgb(0, 149, 77); background-color: white;">Refund</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br><br>
                            <table id="salesTable" class="table table-hover table-bordered table-striped">

                                <!-- Your sales data will go here -->
                                <tbody>

                                </tbody>

                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php include __DIR__ . ('../../includes/footer.php'); ?>
</div>


