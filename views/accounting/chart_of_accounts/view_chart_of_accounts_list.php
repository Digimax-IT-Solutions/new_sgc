<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();

//require_once 'api/category_controller.php';

$chart_of_accounts = ChartOfAccount::all();

$chart_of_account = null;
if (get('action') === 'update') {
    $chart_of_account = ChartOfAccount::find(get('id'));
}

$page = 'chart_of_accounts'; // Set the variable corresponding to the current page
?>


<?php require 'templates/header.php' ?>
<?php require 'templates/sidebar.php' ?>
<div class="main">
    <?php require 'templates/navbar.php' ?>
    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">

            <h1 class="h3 mb-3"><strong>DRHE</strong> Dashboard</h1>

            <div class="row">
                <div class="col-12">
                    <?php displayFlashMessage('add_uom') ?>
                    <?php displayFlashMessage('delete_uom') ?>
                    <?php displayFlashMessage('update_uom') ?>
                    <!-- Default box -->
                    <div class="card">
                        <div class="card-body">

                            <?php
                            if (isset($_GET['id'])) {
                                $id = $_GET['id'];
                                $purchase_order = PurchaseOrder::find($id);
                                if ($purchase_order) { ?>
                                    <form method="POST" action="api/masterlist/chart_of_account_controller.php" id="coaForm">
                                        <input type="hidden" name="action" id="modalAction" value="add" />
                                        <input type="hidden" name="id" id="categoryId" value="" />
                                        <div class="row">
                                            <div class="col-md-4">
                                                <!-- Account Type -->

                                                <div class="form-group">
                                                    <label for="accounttype">Account Type</label>
                                                    <select class="form-control" id="accounttype" name="accounttype">
                                                        <option value="">-- Select Account Type --</option>
                                                        <option value="Accounts Payable">ACCOUNTS PAYABLE</option>
                                                        <option value="Accounts Receivable">ACCOUNTS RECEIVABLE</option>
                                                        <option value="Other Current Assets">OTHER CURRENT ASSETS</option>
                                                        <option value="Other Current Liabilities">OTHER CURRENT LIABILITIES
                                                        </option>
                                                        <option value="Other Expense">OTHER EXPENSE</option>
                                                        <option value="Other Income">OTHER INCOME</option>
                                                        <option value="Fixed Assets">FIXED ASSETS</option>
                                                        <option value="Long-term Liabilities">LONG-TERM LIABILITES</option>
                                                        <option value="Cost of Goods Sold">COST OF GOODS SOLD</option>
                                                        <option value="Equity">EQUITY</option>
                                                        <option value="Expenses">EXPENSES</option>
                                                        <option value="Income">INCOME</option>
                                                        <option value="Non-current Liabilities">NON-CURRENT LIABILITIES</option>
                                                        <option value="Bank">BANK</option>
                                                        <option value="Other Non-current Liabilities">OTHER NON-CURRENT
                                                            LIABILITIES
                                                        </option>
                                                    </select>
                                                </div>

                                                <!-- Account Code -->
                                                <div class="form-group">
                                                    <label for="accountCode">Account Code</label>
                                                    <input type="text" class="form-control" id="accountCode" name="accountCode"
                                                        placeholder="Enter Account Code" required>
                                                </div>
                                            </div>
                                            <div class="col-md-4">

                                                <!-- Set the retrieved sub-account IDs in your dropdown -->
                                                <div class="form-group">
                                                    <label for="subaccountof">Sub Account of</label>
                                                    <select class="form-control" id="subaccountof" name="subaccountof">
                                                        <option value="">-- Select Sub Account --</option>
                                                        <?php foreach ($chart_of_accounts as $chart_of_account): ?>
                                                            <option value="<?= $chart_of_account->accountName ?>">
                                                                <?= $chart_of_account->accountName ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <!-- Account Name -->
                                                <div class="form-group">
                                                    <label for="accountname">Account Name</label>
                                                    <input type="text" class="form-control" id="accountname" name="accountname"
                                                        placeholder="Enter Account Name">
                                                </div>
                                            </div>
                                            <div class="col-md-4">

                                                <!-- Description -->
                                                <div class="form-group">
                                                    <label for="description">DESCRIPTION</label>
                                                    <textarea class="form-control" id="description" name="description" rows="3"
                                                        placeholder="Enter Description"></textarea>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-success" id="saveAccountButton">Save
                                                Account</button>
                                        </div>

                                    </form>

                                    <div id="loadingOverlay" class="loading-overlay">
                                        <div class="spinner"></div>
                                    </div>
                                    <?php
                                    // Check found, you can now display the details
                                } else {
                                    // Handle the case where the check is not found
                                    echo "PO not found.";
                                    exit;
                                }
                            } else {
                                // Handle the case where the ID is not provided
                                echo "No ID provided.";
                                exit;
                            }


                            ?>

                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div>
    </main>



    <!-- Modal -->



    <?php require 'templates/footer.php' ?>