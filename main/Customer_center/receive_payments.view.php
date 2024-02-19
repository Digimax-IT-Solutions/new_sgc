<?php
include __DIR__ . ('../../includes/header.php');

?>

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

    /* Add a hover effect to the dropdown items */
    .dropdown-item:hover {
        background-color: rgb(0, 149, 77) !important;
        /* Change the background color on hover */
        color: white;
        /* Change the text color on hover */
    }

    #writeCheckTable {
        border-collapse: collapse;
        width: 100%;
    }

    #writeCheckTable th,
    #writeCheckTable td {
        padding: 2px;
        /* Adjust the padding as needed */
    }

    #writeCheckTable tbody tr:hover {

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
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Received Payments</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="#">Dashboard</a></li>
                        <li class="breadcrumb-item active">Received Payments</li>
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
                                            Receive Payment
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="newTransactionDropdown">
                                            <a class="dropdown-item" href="create_receive_payments" style="background-color: white;">Receive Payment</a>
                                      
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <br><br>
                            <table id="writeCheckTable" class="table table-hover table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>REF #</th>
                                        <th>ACCOUNT</th>
                                        <th>PAYEE NAME</th>
                                        <th>ADDRESS</th>
                                        <th>CHECK DATE</th>
                                        <th>MEMO</th>
                                        <th>TOTAL AMOUNT</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Your purchase order data will go here -->
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


<script>

</script>