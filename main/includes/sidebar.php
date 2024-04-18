<?php
$userPosition = $_SESSION['SESS_POSITION'];


?>

<style>
/* Style for the submenu */
.submenu {
    display: none; /* Hide the submenu by default */
    position: absolute;
    z-index: 2; /* Make z-index: 1 important */
    list-style: none; /* Remove the bullets from the submenu items */
    padding: 0px; /* Remove any default padding */
    margin: 0; /* Remove any default margin */
    left: 80%;
    top: 70%;
    background-color: #00954d; /* Background color for the submenu */
}

/* Show the submenu when the parent li is hovered */
.nav-item:hover .submenu {
    display: inline-block;
}



/* Optional: Adjust the position of the submenu items */
.submenu li a {
    display: block;
    padding: 10px; /* Adjust the padding as needed */
    color: #333; /* Text color for the submenu items */
    text-decoration: none; /* Remove underline from links */
}

/* Optional: Highlight the hovered submenu item */
.submenu li a:hover {
    background-color: #ddd;
    color: #fff;
}

</style>
<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-1 fixed" style="background-color: maroon;">
    <!-- Brand Logo -->
    <a href="dashboard" class="brand-link">
        <img src="../images/sgc.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-2" style="
        opacity: .9;    
        background:white;">
        <span class="brand-text font-weight-bold">NEW SGC</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar" >
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pill nav-sidebar flex-column nav-treeview" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="dashboard" class="nav-link">
                        <i class="nav-icon ion-home"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>
                <li class="nav-item has-treeview  <?php echo ($page == 'main/sales_invoice' || $page == 'main/sales_return' || $page == 'main/receive_payments' || $page == 'main/create_invoice' || $page == 'main/create_receive_payments') ? 'menu-open' : ''; ?>"">
                    <a href=" #" class="nav-link">
                    <i class="nav-icon fas fa-user-circle"></i>
                    <p>
                        Customer Center
                        <i class="right fas fa-angle-left"></i>
                    </p>
                    </a>

                    <ul class="nav nav-treeview" style="margin-left: 20px;">
                        <li class="nav-item">
                            <a href="sales_invoice" class="nav-link <?php echo ($page == 'main/sales_invoice' || $page == 'main/create_invoice') ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                <p>Sales Invoice</p>
                            </a>
                        </li>
                        <!-- <li class="nav-item">
                            <a href="sales_return" class="nav-link <?php echo ($page == 'main/sales_return') ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-undo"></i>
                                <p>Sales Returns</p>
                            </a>
                        </li> -->
                        <li class="nav-item">
                        <a href="receive_payments" class="nav-link <?php echo ($page == 'main/receive_payments' || $page == 'main/create_receive_payments') ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-money-check-alt"></i>
                                <p>Receive Payment</p>
                            </a>
                        </li>
                        <!-- <li class="nav-item">
                            <a href="statement_of_account" class="nav-link <?php echo ($page == 'main/statement_of_account') ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-file-alt"></i>
                                <p>Statement of Account</p>
                            </a>
                        </li> -->
                    </ul>

                </li>
                <li class="nav-item has-treeview <?php echo ($page == 'main/purchase_order' || $page == 'main/received_items_list' || $page == 'main/enter_bills' || $page == 'main/purchase_return' ||  $page == 'main/create_purchase_order') ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-store"></i>
                        <p>
                            Vendor Center
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview" style="margin-left: 20px; font-size: 15px;">
                        <li class="nav-item">
                            <a href="purchase_order" class="nav-link <?php echo ($page == 'main/purchase_order' || $page == 'main/create_purchase_order') ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                <p>Purchase Order</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="received_items_list" class="nav-link  <?php echo ($page == 'main/received_items_list') ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-box"></i>
                                <p>Received Items</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="enter_bills" class="nav-link <?php echo ($page == 'main/enter_bills') ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-file-invoice"></i>
                                <p>Enter Bills</p>
                            </a>
                        </li>
                        <!-- <li class="nav-item">
                            <a href="" class="nav-link">
                                <i class="nav-icon fas fa-undo"></i>
                                <p>Purchase Returns</p>
                            </a>
                        </li> -->
                        <!-- <li class="nav-item">
                            <a href="" class="nav-link">
                                <i class="nav-icon fas fa-money-check-alt"></i>
                                <p>Pay Bills</p>
                            </a>
                        </li> -->
                    </ul>
                </li>
                <!-- BANKING -->
                <li class="nav-item has-treeview <?php echo ($page == 'main/write_check' || $page == 'main/make_deposit' || $page == 'main/expenses') ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-university"></i>
                        <p>
                            Banking
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview" style="margin-left: 20px;">
                        <li class="nav-item">
                            <a href="write_check" class="nav-link <?php echo ($page == 'main/write_check') ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-file-invoice-dollar"></i>
                                <p>Write Check</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="make_deposit" class="nav-link <?php echo ($page == 'main/make_deposit') ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-coins"></i>
                                <p>Make Deposit</p>
                            </a>
                        </li>
                        <!-- <li class="nav-item">
                            <a href="fund_transfer" class="nav-link <?php echo ($page == 'main/fund_transfer') ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-exchange-alt"></i>
                                <p>Fund Transfer</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="reconcile" class="nav-link <?php echo ($page == 'main/reconcile') ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-balance-scale"></i>
                                <p>Reconcile</p>
                            </a>
                        </li> -->
                    </ul>
                </li>
                <!--  -->
                <!-- INVENTORY CENTER -->
                <!-- <li class="nav-item has-treevie">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-cubes"></i>
                        <p>
                            Inventory Center
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview" style="margin-left: 20px;">
                        <li class="nav-item">
                            <a href="" class="nav-link">
                                <i class="nav-icon fas fa-box"></i>
                                <p>Received Items</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="stock_transfer" class="nav-link <?php echo ($page == 'main/stock_transfer') ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-truck-moving"></i>
                                <p>Stock Transfer</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="sales_invoice" class="nav-link <?php echo ($page == 'main/sales_invoice') ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-file-invoice"></i>
                                <p>Sales Invoice</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="stock_card" class="nav-link <?php echo ($page == 'main/stock_card') ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-clipboard-list"></i>
                                <p>Stock Card</p>
                            </a>
                        </li>
                    </ul>
                </li> -->
                <!-- Accounting CENTER -->
                <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-calculator"></i>
                        <p>
                            Accounting
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview" style="margin-left: 20px;">
                        <li class="nav-item">
                            <a href="general_journal" class="nav-link <?php echo ($page == 'main/create_general_journal') ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-book"></i>
                                <p>Create General Journal</p>
                            </a>
                        </li>
                        <!-- <li class="nav-item">
                            <a href="audit_trail" class="nav-link <?php echo ($page == 'main/audit_trail') ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-history"></i>
                                <p>Audit Trail</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="transaction_entries" class="nav-link <?php echo ($page == 'main/transaction_entries') ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-list-alt"></i>
                                <p>Transaction Entries</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="general_ledger" class="nav-link <?php echo ($page == 'main/general_ledger') ? 'active' : ''; ?>">
                                <i class="nav-icon fas fa-book-open"></i>
                                <p>General Ledger</p>
                            </a>
                        </li> -->
                    </ul>
                </li>


                
                <!-- <li class="nav-item has-treeview">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-store"></i>
                        <p>
                            Reports
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>

                    <ul class="nav nav-treeview" style="margin-left: 1px; font-size: 13px;">
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-file"></i>
                            <p>Sales</p>
                        </a>
                    </li>
                    <ul class="submenu">
                        <li><a href="#">
                            <i class="nav-icon fas fa-file"></i>Submenu Item 1</a></li>
                        <li><a href="#">
                            <i class="nav-icon fas fa-file"></i>Submenu Item 2</a>
                        </li>
                        
                    </ul>
                    </ul>
                </li>  -->

                
                <?php if ($userPosition == 'admin' || $userPosition == 'staff') : ?>
                    <!-- MASTERLIST -->
                    <li class="nav-item has-treeview <?php echo ($page == 'main/chart_of_accounts' || $page == 'main/item_list' || $page == 'main/customer_list' || $page == 'main/vendor_list' || $page == 'main/other_names_list' || $page == 'main/location_list' || $page == 'main/category_list' || $page == 'main/terms_list' || $page == 'main/payment_method_list' || $page == 'main/vat_rate_list' || $page == 'main/sales_tax_list' || $page == 'main/wtax_list' || $page == 'main/uom_list') ? 'menu-open' : ''; ?>">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-th"></i>
                            <p>
                                Master List
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" style="margin-left: 20px;">
                            <li class="nav-item">
                                <a href="chart_of_accounts" class="nav-link <?php echo ($page == 'main/chart_of_accounts') ? 'active' : ''; ?>">
                                    <i class="nav-icon fas fa-book"></i>
                                    <p>Chart of Accounts</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="item_list" class="nav-link <?php echo ($page == 'main/item_list') ? 'active' : ''; ?>">
                                    <i class="nav-icon fas fa-folder"></i>
                                    <p>Item List</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="customer_list" class="nav-link <?php echo ($page == 'main/customer_list') ? 'active' : ''; ?>">
                                    <i class="nav-icon fas fa-user"></i>
                                    <p>Customer List</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="vendor_list" class="nav-link <?php echo ($page == 'main/vendor_list') ? 'active' : ''; ?>">
                                    <i class="nav-icon fas fa-truck"></i>
                                    <p>Vendor List</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="other_names_list" class="nav-link <?php echo ($page == 'main/other_names_list') ? 'active' : ''; ?>">
                                    <i class="nav-icon fas fa-user"></i>
                                    <p>Other Names List</p>
                                </a>
                            </li>

                            <!-- MASTERLIST -->
                            <li class="nav-item has-treeview <?php echo ($page == 'main/sales_tax_list' || $page == 'main/wtax_list' || $page == 'main/category_list' || $page == 'main/location_list' || $page == 'main/terms_list' || $page == 'main/payment_method_list' || $page == 'main/uom_list') ? 'menu-open' : ''; ?>">
                                <a href="#" class="nav-link">
                                    <i class="nav-icon fas fa-list"></i>
                                    <p>
                                        Other List
                                        <i class="right fas fa-angle-left"></i>
                                    </p>
                                </a>
                                <ul class="nav nav-treeview" style="margin-left: 20px;">
                                    <li class="nav-item">
                                        <a href="location_list" class="nav-link <?php echo ($page == 'main/location_list') ? 'active' : ''; ?>">
                                            <i class="nav-icon fas fa-map"></i>
                                            <p>Location List</p>
                                        </a>
                                    </li>

                                    <li class="nav-item">
                                        <a href="category_list" class="nav-link <?php echo ($page == 'main/category_list') ? 'active' : ''; ?>">
                                            <i class="nav-icon fas fa-list-alt"></i>
                                            <p>Category List</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="terms_list" class="nav-link <?php echo ($page == 'main/terms_list') ? 'active' : ''; ?>">
                                            <i class="nav-icon fas fa-star"></i>
                                            <p>Terms List</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="payment_method_list" class="nav-link <?php echo ($page == 'main/payment_method_list') ? 'active' : ''; ?>">
                                            <i class="nav-icon fas fa-credit-card"></i>
                                            <p>Payment Methods</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="sales_tax_list" class="nav-link <?php echo ($page == 'main/sales_tax_list') ? 'active' : ''; ?>">
                                            <i class="nav-icon fas fa-percent"></i>
                                            <p>Sales Tax Rate</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="wtax_list" class="nav-link <?php echo ($page == 'main/wtax_list') ? 'active' : ''; ?>">
                                            <i class="nav-icon fas fa-percent"></i>
                                            <p>WTax Rate</p>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a href="uom_list" class="nav-link <?php echo ($page == 'main/uom_list') ? 'active' : ''; ?>">
                                            <i class="nav-icon fas fa-percent"></i>
                                            <p>UOM</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>

                    </li>
                    <!-- MAINTENANCE -->
                    <?php
                    if ($userPosition !== 'staff') {
                        ?>
                    <li class="nav-item has-treeview <?php echo ($page == 'main/user_list' || $page == 'main/settings') ? 'menu-open' : ''; ?>">
                        <a href="#" class="nav-link">
                            <i class="nav-icon fas fa-cog"></i>
                            <p>
                                Maintenance
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>
                        <ul class="nav nav-treeview" style="margin-left: 20px;">
                            <li class="nav-item">
                                <a href="user_list" class="nav-link <?php echo ($page == 'main/user_list') ? 'active' : ''; ?>">
                                    <i class="nav-icon fas fa-user"></i>
                                    <p>User List</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="settings" class="nav-link <?php echo ($page == 'main/settings') ? 'active' : ''; ?>">
                                    <i class="nav-icon fas fa-cog"></i>
                                    <p>Settings</p>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <?php
                    }
                    ?>
                <?php endif; ?>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>

    <!-- /.sidebar -->
</aside>