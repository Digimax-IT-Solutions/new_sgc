<?php
include __DIR__ . ('../../includes/header.php');
include('connect.php');
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

    .input-group-text {
        font-size: 50%;

    }

    .form-control {
        font-size: 80%;

    }

    .form-group label {
        font-size: 60%;
    }

    #reference_number_group,
    #check_number_group {
        display: none;
    }

    .icon-select {
        display: flex;
        padding: 15px;
    }

    .icon-option {
        cursor: pointer;
        margin: 2px;
        padding: 15px;
        border-radius: 10px;

        background-color: #f0f0f0;

    }

    .icon-option:hover {
        color: white;
        background-color: green;
    }

    .icon-option.selected {
        color: white;
        background-color: green;
    }

    label {
        color: grey;
    }
</style>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0" style="font-weight: bold; font-size:40px;">Customer Payment</h1>
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
                            <form action="process_payment.php" method="post">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="ar_account">A/R ACCOUNT</label>
                                        <select name="ar_account" id="ar_account" class="form-control" required>
                                            <?php
                                            // Fetch vendors with purchase orders having poStatus = 'WAITING FOR DELIVERY' from the database and populate the dropdown in the modal
                                            $vendorQuery = "SELECT * FROM chart_of_accounts where account_type = 'Accounts Receivable'";

                                            try {
                                                $vendorStmt = $db->prepare($vendorQuery);
                                                $vendorStmt->execute();

                                                $vendors = $vendorStmt->fetchAll(PDO::FETCH_ASSOC);

                                                foreach ($vendors as $vendor) {
                                                    echo "<option value='" . htmlspecialchars($vendor['account_name'], ENT_QUOTES) . "' data-poid='{$vendor['account_id']}'>" . htmlspecialchars($vendor['account_code'] . ' ' . $vendor['account_name'], ENT_QUOTES) . "</option>";
                                                }
                                            } catch (PDOException $e) {
                                                // Handle the exception, log the error or return an error message with MySQL error information
                                                $errorInfo = $vendorStmt->errorInfo();
                                                $errorMessage = "Error fetching vendors: " . $errorInfo[2]; // MySQL error message
                                                echo "<option value=''>$errorMessage</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="customer_balance">CUSTOMER BALANCE</label>
                                        <input type="text" class="form-control" id="customer_balance" name="customer_balance" readonly>

                                    </div>
                                </div>


                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="received_from">RECEIVED FROM</label>
                                        <select class="form-control" id="received_from" name="received_from">
                                            <option value="">Select Customer</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="payment_amount">PAYMENT AMOUNT</label>
                                        <input type="text" class="form-control" id="payment_amount" name="payment_amount">
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="date">DATE</label>
                                        <input type="date" class="form-control" id="date" name="date">
                                    </div>
                                </div>

                                <div class="form-row ">
                                    <div class="icon-select col-md-2">
                                        <div class="form-group">


                                            <div class="icon-option " data-value="cash">
                                                <i class="fas fa-money-bill-wave"></i>CASH
                                            </div>

                                        </div>
                                        <div class="form-group">
                                            <div class="icon-option" data-value="check">
                                                <i class="fas fa-money-check"></i>CHECK
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2" id="reference_number_group">
                                        <label for="reference_number">REFERENCE #</label>
                                        <input type="text" class="form-control" id="reference_number" name="reference_number">
                                    </div>
                                    <div class="form-group col-md-2" id="check_number_group">
                                        <label for="check_number">CHECK #</label>
                                        <input type="text" class="form-control" id="check_number" name="check_number">
                                    </div>
                                    <div class="form-group">
                                        <label for="memo">Memo</label>
                                        <textarea class="form-control" id="memo" name="memo"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-group">
                                        <!-- Populate invoices from invoice table -->
                                        <table id="invoice_table" class="table">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Number</th>
                                                    <th>Original Amount</th>
                                                    <th>Amount Due</th>
                                                    <th>Payment</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div class="summary-details">
                                        <div class="container">

                                            <div class="row">
                                                <div class="col-md-5 d-inline-block text-right">
                                                    <label>Amount Due:</label>
                                                </div>
                                                <div class="col-md-7 d-inline-block">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">&#8369;</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="amount_due" id="amount_due" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-5 d-inline-block text-right">
                                                    <label>Applied:</label>
                                                </div>
                                                <div class="col-md-7 d-inline-block">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">&#8369;</span>
                                                        </div>
                                                        <input type="text" class="form-control" name="applied" id="applied" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>



    <?php include __DIR__ . ('../../includes/footer.php'); ?>
</div>


<script>
    $(document).ready(function() {
        var customerBalanceInput = $('#customer_balance'); // Move the variable declaration here
        $.ajax({
            url: 'modules/customer_center/get_customers.php',
            type: 'GET',
            success: function(response) {
                var customers = JSON.parse(response);
                var select = $('#received_from');
                customers.forEach(function(customer) {
                    var option = '<option value="' + customer.customerName + '">' + customer
                        .customerName + '</option>';
                    select.append(option);

                    select.change(function() {
                        var selectedCustomerName = $(this).val();
                        var selectedCustomer = customers.find(function(customer) {
                            return customer.customerName ===
                                selectedCustomerName;
                        });
                        if (selectedCustomer) {
                            customerBalanceInput.val(selectedCustomer.customerBalance);
                        } else {
                            customerBalanceInput.val('');
                        }
                    });
                });
            }
        });
        $('#received_from').change(function() {
            var customerName = $(this).val();
            console.log(customerName); // Log the response to the console
            $.ajax({
                url: 'modules/customer_center/get_unpaid_invoices.php',
                type: 'POST',
                data: {
                    customerName: customerName
                },
                success: function(response) {
                    console.log(response); // Log the response to the console
                    var invoices = JSON.parse(response);
                    var tableBody = $('#invoice_table tbody');
                    tableBody.empty();
                    if (invoices.length === 0) {
                        tableBody.append(
                            '<tr><td colspan="5">There are no unpaid invoices for this customer</td></tr>'
                        );
                    } else {
                        invoices.forEach(function(invoice) {
                            var row = '<tr>' +
                                '<td>' + invoice.invoiceDate + '</td>' +
                                '<td>' + invoice.invoiceNo + '</td>' +
                                '<td>' + invoice.totalAmountDue + '</td>' +
                                '<td>' + invoice.totalAmountDue + '</td>' +
                                '<td></td>' +
                                '</tr>';
                            tableBody.append(row);
                        });
                    }
                }
            });
        });
        $(document).ready(function() {
            $('.icon-option').click(function() {
                $('.icon-option').removeClass('selected');
                $(this).addClass('selected');
                var value = $(this).data('value');
                if (value === 'cash') {
                    $('#reference_number_group').show();
                    $('#check_number_group').hide();
                } else {
                    $('#reference_number_group').hide();
                    $('#check_number_group').show();
                }
            });
        });

    });
</script>