<?php
include __DIR__ . '../../includes/header.php'; // Ensure correct path and file inclusion
include 'connect.php'; // Include the connection script

$salesInvoice = null;

// Validate and sanitize the 'ID' parameter from the URL
if (isset($_GET['ID']) && is_numeric($_GET['ID'])) {
    $ID = $_GET['ID'];

    try {
        // Query to retrieve sales invoice details
        $queryInvoice = "SELECT * FROM receive_payment WHERE ID = :ID";
        $stmtInvoice = $db->prepare($queryInvoice);
        $stmtInvoice->bindParam(':ID', $ID, PDO::PARAM_INT); // Bind parameter as integer
        $stmtInvoice->execute();

        // Fetch the invoice details if needed
        $salesInvoice = $stmtInvoice->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // Handle the exception, log the error, or return an error message
        echo "Error fetching invoice: " . $e->getMessage();
        exit(); // Exit after encountering an error
    }

} else {
    // Output JavaScript alert for an invalid invoice ID
    echo "<script>alert('Invalid Invoice ID');</script>";
    exit();
}

try {
    // Query to retrieve customer names with credit balances
    $query = "SELECT customerName, creditAmount FROM credits WHERE status = 'active' AND creditAmount > 0";
    $statement = $db->prepare($query);
    $statement->execute();

    // Fetch all rows as an associative array
    $credits = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Create an array to store customer names with credit balances
    $customersWithCredit = array_column($credits, 'customerName');

} catch (PDOException $e) {
    // Handle the exception, log the error, or return an error message
    echo "Error fetching credits: " . $e->getMessage();
    exit(); // Exit after encountering an error
}
?>

<style>
    .discount_credit_button {
        display: none;
    }

    .invalid-border {
        border: 2px solid red;
        /* You can adjust the border properties as needed */
    }

    font {
        color: black;
    }

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

    /* Media query for smaller screens */
@media screen and (max-width: 768px) {
    .icon-select {
        padding: 5px; /* Reduce padding for smaller screens */
    }

    .icon-option {
        margin: 3px; /* Adjust margin for smaller screens */
        min-width: 80px; /* Adjust minimum width for smaller screens */
    }
}

    label {
        color: grey;
    }

    #creditMemoTable {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    #creditMemoTable tbody {
        color: black;
    }

    #creditMemoTable tbody td:not(.first-column) {
        padding: 1px;
        white-space: nowrap;
        overflow: hidden;
        /* Hides any overflowing content */
        text-overflow: ellipsis;
        width: 100px;
        /* Adjust the width as needed */
    }

    #creditMemoTable tbody .first-column {
        width: 20px;
    }

    .updated-amount {
        text-align: right;
    }

    #invoice_table {
        border-collapse: collapse;
        width: 100%;
        table-layout: fixed;
    }

    #invoice_table th,
    #invoice_table td {
        text-align: center;
        padding: 1px;
        white-space: nowrap;
        overflow: hidden;
        /* Hides any overflowing content */
        text-overflow: ellipsis;
    }

    #invoice_table tbody tr:hover {
        color: white;
        background-color: rgb(0, 149, 77);
        /* Set your desired background color here */
    }

    #invoice_table th:first-child,
    #invoice_table td:first-child {
        width: 20px;
    }

    .discount-label {
        font-size: 20px;
        width: 250px;
        display: inline-block;
    }

    .discount-value {
        color: black;
        display: inline-block;
        width: 200px;
    }

    .discount-input {
        width: 200px;
        display: inline-block;
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
                            <form id="paymentForm" method="post">
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="ar_account">A/R ACCOUNT</label>
                                        <select name="ar_account" id="ar_account" class="form-control" required>
                                            <?php
                                            // Fetch chartsOfAccounts with purchase orders having poStatus = 'WAITING FOR DELIVERY' from the database and populate the dropdown in the modal
                                            $chartsOfAccountQuery = "SELECT * FROM chart_of_accounts where account_type = 'Accounts Receivable'";

                                            try {
                                                $chartsOfAccountStmt = $db->prepare($chartsOfAccountQuery);
                                                $chartsOfAccountStmt->execute();

                                                $chartsOfAccounts = $chartsOfAccountStmt->fetchAll(PDO::FETCH_ASSOC);

                                                foreach ($chartsOfAccounts as $chartsOfAccount) {
                                                    echo "<option value='" . htmlspecialchars($chartsOfAccount['account_name'], ENT_QUOTES) . "' data-poid='{$chartsOfAccount['account_id']}'>" . htmlspecialchars($chartsOfAccount['account_code'] . ' ' . $chartsOfAccount['account_name'], ENT_QUOTES) . "</option>";
                                                }
                                            } catch (PDOException $e) {
                                                // Handle the exception, log the error or return an error message with MySQL error information
                                                $errorInfo = $chartsOfAccountStmt->errorInfo();
                                                $errorMessage = "Error fetching chartsOfAccounts: " . $errorInfo[2]; // MySQL error message
                                                echo "<option value=''>$errorMessage</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="customer_balance">CUSTOMER BALANCE</label>
                                        <input type="text" class="form-control" id="customer_balance" name="customer_balance" readonly>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="receivedDate">DATE</label>
                                        <input type="date" class="form-control" id="receivedDate" name="receivedDate"
                                        value="<?php echo $salesInvoice['receivedDate']; ?>">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="customerName">RECEIVED FROM</label>
                                        <select class="form-control" id="customerName" name="customerName">
                                            <option <?php echo $salesInvoice['customerName']; ?>>
                                                    <?php echo $salesInvoice['customerName']; ?></option>
                                            <?php
                                            // Fetch customers with purchase orders having poStatus = 'WAITING FOR DELIVERY' from the database and populate the dropdown in the modal
                                            $customerQuery = "SELECT * FROM customers";

                                            try {
                                                $customerStmt = $db->prepare($customerQuery);
                                                $customerStmt->execute();

                                                $customers = $customerStmt->fetchAll(PDO::FETCH_ASSOC);

                                                foreach ($customers as $customer) {
                                                    echo "<option value='{$customer['customerName']}' data-balance='{$customer['customerBalance']}'>{$customer['customerName']}</option>";
                                                }
                                            } catch (PDOException $e) {
                                                // Handle the exception, log the error or return an error message with MySQL error information
                                                $errorInfo = $customerStmt->errorInfo();
                                                $errorMessage = "Error fetching customers: " . $errorInfo[2]; // MySQL error message
                                                echo "<option value=''>$errorMessage</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="payment_amount">PAYMENT AMOUNT</label>
                                        <input type="text" class="form-control" id="payment_amount" name="payment_amount" value="<?php echo $salesInvoice['payment_amount']; ?>" readonly>
                                    </div>
                                    <div class="form-group col-md-2" id="reference_number_group">
                                        <label for="RefNo">REFERENCE #</label>
                                        <input type="text" class="form-control" id="RefNo" name="RefNo" value="<?php echo $salesInvoice['RefNo']; ?>">
                                    </div>
                                    <div class="form-group col-md-2" id="check_number_group" style="display: none;">
                                        <label for="checkNo">CHECK #</label>
                                        <input type="text" class="form-control" id="checkNo" name="RefNo">
                                        <!-- Same name as the reference number input -->
                                    </div>
                                    <div class="form-group col-md-4" hidden>
                                        <label for="excessAmount" hidden>EXCESS CREDIT</label>
                                        <input type="text" class="form-control" id="excessAmount" name="excessAmount" readonly>
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="icon-select col-md-6">
                                        <div class="form-group">
                                            <div class="icon-option" data-value="cash">
                                                <i class="fas fa-money-bill-wave"></i>CASH
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="icon-option" data-value="check">
                                                <i class="fas fa-money-check"></i>CHECK
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="icon-option" data-value="bank transfer">
                                                <i class="fas fa-university"></i>BANK TRANSFER
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="icon-option" data-value="gift card">
                                                <i class="fas fa-money-check-alt"></i>GIFT CARD
                                            </div>
                                        </div>                                        
                                    </div>
                                    <input type="hidden" id="paymentType" name="paymentType" value="<?php echo $salesInvoice['paymentType']; ?>">
                                    <div class="form-group">
                                        <label for="memo">Memo</label>
                                        <textarea class="form-control" id="memo" name="memo"></textarea>
                                    </div>
                                    <div id="creditCheckboxIDsContainer" hidden>
                                        <?php
                                        // Loop through each credit checkbox and echo its ID if checked
                                        foreach ($credits as $credit) {
                                            if (isset($_POST['credit']) && in_array($credit['ID'], $_POST['credit'])) {
                                                echo $credit['ID'] . ', ';
                                            }
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="form-group">
                                        <!-- Populate invoices from invoice table -->
                                        <table id="invoice_table" class="table">
                                            <thead>
                                                <tr>
                                                    <th>✔</th>
                                                    <th>Date</th>
                                                    <th>Number</th>
                                                    <th>Original Amount</th>
                                                    <th>Discount & Credit</th>
                                                    <th>Discount</th>
                                                    <th>Credit</th>
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
                                                        <input type="text" class="form-control font-weight-bold" name="amount_due" id="amount_due" readonly>
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
                                                        <input type="text" class="form-control font-weight-bold" name="applied" id="applied" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-5 d-inline-block text-right">
                                                    <label>Discount and Credits Applied:</label>
                                                </div>
                                                <div class="col-md-7 d-inline-block">
                                                    <div class="input-group">
                                                        <div class="input-group-prepend">
                                                            <span class="input-group-text">&#8369;</span>
                                                        </div>
                                                        <input type="text" class="form-control font-weight-bold" name="discCredapplied" id="discCredapplied" readonly>
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

    <!-- add discount & credit modal -->
    <div class="modal" id="addDiscCred" tabindex="-1" role="dialog" aria-labelledby="addDiscCredLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width: 700px;">
            <div class="modal-content">
                <div class="modal-header" style="color: green;">
                    <h5 class="modal-title" id="addDiscCredLabel"><b>Add Discount & Credit</b></h5>
                    <button type="buttonsaveTermButton" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="color: white;">
                    <form id="addDiscCredForm" style="padding: 20px; color: white; border-radius: 10px;">
                        <div class="form-row">
                            <!-- Left column -->
                            <div class="col-md-6">
                                <div class="form-group" hidden>
                                    <label for="DiscCredID">ID</label>
                                    <font id="DiscCredID"></font>
                                </div>
                                <div class="form-group">
                                    <label for="DiscCredName">Customer</label>
                                    <font id="DiscCredName"></font>
                                </div>
                                <div class="form-group">
                                    <label for="DiscCredDate">Date</label>
                                    <font id="DiscCredDate"></font>
                                </div>
                                <div class="form-group">
                                    <label for="DiscCredNumber">Number</label>
                                    <font id="DiscCredNumber"></font>
                                </div>
                                <div class="form-group">
                                    <label for="DiscCredOrigAmt">Original Amount</label>
                                    <font id="DiscCredOrigAmt"></font>
                                </div>
                            </div>
                            <!-- Right column -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="DiscCredAmtDue">Amount Due</label>
                                    <font id="DiscCredAmtDue"></font>
                                </div>
                                <div class="form-group">
                                    <label for="DiscCredDiscountUsed">Discount Used</label>
                                    <font id="DiscCredDiscountUsed"><strong>0.00</strong></font>
                                </div>
                                <div class="form-group">
                                    <label for="DiscCredCredUsed">Credit Used</label>
                                    <font class="font-weight-bold" id="DiscCredCredUsed"><strong></strong></font>
                                </div>
                                <div class="form-group">
                                    <label for="DiscCredBalanceDue">Balance Due</label>
                                    <font id="DiscCredBalanceDue"></font>
                                </div>
                            </div>
                        </div>
                    </form>
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="credits-tab" data-toggle="tab" href="#credits" role="tab">Credits</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="discount-tab" data-toggle="tab" href="#discount" role="tab">Discount</a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane show active" id="credits" role="tabpanel">
                            <table id="creditMemoTable">
                                <thead>
                                    <tr>
                                        <th>✔</th>
                                        <th>Credit No</th>
                                        <th>Customer Name</th>
                                        <th>Credit Amount</th>
                                        <th>Amount</th>
                                        <th>CreditBalance</th>
                                    </tr>
                                <tbody>
                                </tbody>
                                </thead>
                            </table>
                        </div>
                        <div class="tab-pane" id="discount" role="tabpanel">
                            <br>
                            <div class="form-group">
                                <label for="discount_date" style="font-size: 20px" class="discount-label">Discount Date:</label>
                            </div>
                            <div class="form-group">
                                <label for="discount_terms" style="font-size: 20px" class="discount-label">Terms:</label>
                            </div>
                            <div class="form-group">
                                <label for="" class="discount-label" style="font-size: 20px">Suggested Discount:</label>
                                <p class="discount-value">0.00</p>
                            </div>
                            <div class="form-group">
                                <label for="discount_amount" style="font-size: 20px" class="discount-label">Amount of Discount:</label>
                                <input type="text" class="form-control discount-input" name="discount_amount" id="discount_amount">
                            </div>
                            <div class="form-group">
                                <label for="disc_account" class="discount-label" style="font-size: 20px">Discount Account:</label>
                                <select name="disc_account" id="disc_account" class="form-control discount-input">
                                    <option value="" disabled selected>Select Account</option>
                                    <?php
                                    // Fetch chartsOfAccounts with purchase orders having poStatus = 'WAITING FOR DELIVERY' from the database and populate the dropdown in the modal
                                    $chartsOfAccountQuery = "SELECT * FROM chart_of_accounts";

                                    try {
                                        $chartsOfAccountStmt = $db->prepare($chartsOfAccountQuery);
                                        $chartsOfAccountStmt->execute();

                                        $chartsOfAccounts = $chartsOfAccountStmt->fetchAll(PDO::FETCH_ASSOC);

                                        foreach ($chartsOfAccounts as $chartsOfAccount) {
                                            echo "<option value='" . htmlspecialchars($chartsOfAccount['account_name'], ENT_QUOTES) . "' data-poid='{$chartsOfAccount['account_id']}'>" . htmlspecialchars($chartsOfAccount['account_code'] . ' ' . $chartsOfAccount['account_name'], ENT_QUOTES) . "</option>";
                                        }
                                    } catch (PDOException $e) {
                                        // Handle the exception, log the error or return an error message with MySQL error information
                                        $errorInfo = $chartsOfAccountStmt->errorInfo();
                                        $errorMessage = "Error fetching chartsOfAccounts: " . $errorInfo[2]; // MySQL error message
                                        echo "<option value=''>$errorMessage</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button id="saveModalDataBtn" type="button" class="btn btn-success">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /end discount & credit modal -->


    <?php include __DIR__ . ('../../includes/footer.php'); ?>
</div>

<script>
$(document).ready(function() {
    // Get the paymentType value
    var paymentType = $('#paymentType').val();
    
    // Remove 'selected' class from all icon-options
    $('.icon-option').removeClass('selected');
    
    // Add 'selected' class to the icon-option corresponding to the paymentType
    $('.icon-option[data-value="' + paymentType + '"]').addClass('selected');
});

    $('.icon-option').click(function() {
        $('.icon-option').removeClass('active'); // Remove active class from all options
        $(this).addClass('active'); // Add active class to the clicked option
    });
</script>
<?php include('receive_payment_js.php'); ?>