<?php
function shouldSidebarBeCollapsed()
{
    $pagesToCollapseSidebar = []; // Add more page names as needed
    return in_array(getCurrentPage(), $pagesToCollapseSidebar);
}
?>
<style>
    .sidebar-item.active {

        background: linear-gradient(to right, rgb(255, 102, 102) 100%, rgb(255, 102, 102) 0%);
    }

    /* Sidebar CSS */
    .sidebar-link.collapsed .arrow-icon {
        transform: rotate(0deg);
        transition: transform 0.3s;
    }

    .sidebar-link:not(.collapsed) .arrow-icon {
        transform: rotate(90deg);
        transition: transform 0.3s;
    }

    /* Indent dropdown list */
    .sidebar-dropdown {
        padding-left: 10px;
        /* Adjust the value as needed */
    }

    .sidebar-dropdown .sidebar-item {
        margin-left: 10px;
        /* Adjust the value as needed */
    }
</style>



<nav id="sidebar" class="sidebar js-sidebar <?php echo shouldSidebarBeCollapsed() ? 'collapsed' : ''; ?>">
    <div class="sidebar-content js-simplebar" id="sidebar-content">
        <a class="sidebar-brand" href="dashboard">
            <span class="align-middle">NEW SGC CAS</span>
        </a>
        <!-- Sidebar user panel -->
        <div class="user-panel text-center">
            <div class="image">
                <img src="photos/logo.png" class="img-circle img-fluid rounded" style="width: 70px" alt="User Image">
            </div>
            <div class="info">
                <p></p>
                <a href="#"><i class="fa fa-circle text-success"></i> Admin</a>
            </div>
        </div>
        <br>
        <ul class="sidebar-nav">
            <!-- DASHBOARD -->
            <li class="sidebar-item <?php echo getCurrentPage() == 'dashboard' ? 'active' : ''; ?>">
                <a class="sidebar-link" href="dashboard">
                    <i class="fas fa-home align-middle"></i> <span class="align-middle">Dashboard</span>
                </a>
            </li>
            <!-- CUSTOMER CENTER -->
            <li class="sidebar-item">
                <a data-bs-target="#customer" data-bs-toggle="collapse" class="sidebar-link collapsed">
                    <i class="fas fa-user align-middle"></i>
                    <i class="fas fa-chevron-right align-middle arrow-icon"></i>
                    <span class="align-middle"><b>Customer Center</b></span>
                </a>
                <ul id="customer" class="sidebar-dropdown list-unstyled <?php echo in_array(getCurrentPage(), [
                    'estimate',
                    'sales_order',
                    'invoice',
                    'create_invoice',
                    'receive_payment',
                    'customer_payment',
                    'billing_statement',
                    'credit_memo',
                    'sales_return'
                ]) ? '' : 'collapse'; ?>" data-bs-parent="#sidebar">

                    <li
                        class="sidebar-item <?php echo getCurrentPage() == 'create_invoice' ? 'active' : ''; ?> <?php echo getCurrentPage() == 'invoice' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='invoice'>
                            <i class="fas fa-clipboard align-middle"></i> Sales Invoice
                        </a>
                    </li>
                    <li
                        class="sidebar-item <?php echo getCurrentPage() == 'customer_payment' ? 'active' : ''; ?> <?php echo getCurrentPage() == 'receive_payment' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='receive_payment'>
                            <i class="fas fa-dollar-sign align-middle"></i> Receive Payment
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'credit_memo' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='credit_memo'>
                            <i class="fas fa-credit-card align-middle"></i> Credit Memo
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo (getCurrentPage() == 'sales_return') ? 'active' : ''; ?>">
                        <a class="sidebar-link" href="sales_return.php">
                            <i class="fas fa-arrow-left-circle"></i> <span>Sales Return</span>
                        </a>
                    </li>
                </ul>
            </li>



            <!-- VENDOR CENTER -->
            <li class="sidebar-item">
                <a data-bs-target="#vendor" data-bs-toggle="collapse" class="sidebar-link collapsed">
                    <i class="fas fa-users align-middle"></i>
                    <i class="fas fa-chevron-right align-middle arrow-icon"></i>
                    <span class="align-middle"><b>Vendor Center</b></span>
                </a>
                <ul id="vendor"
                    class="sidebar-dropdown list-unstyled <?php echo in_array(getCurrentPage(), ['purchase_order', 'receive_items', 'accounts_payable_voucher', 'purchase_return', 'pay_bills']) ? '' : 'collapse'; ?>"
                    data-bs-parent="#sidebar">
                    <li class="sidebar-item <?php echo getCurrentPage() == 'purchase_order' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='purchase_order'>
                            <i class="fas fa-shopping-cart align-middle"></i> Purchase Order
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'receive_items' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='receive_items'>
                            <i class="fas fa-box align-middle"></i> Receive items
                        </a>
                    </li>
                    <li
                        class="sidebar-item <?php echo getCurrentPage() == 'accounts_payable_voucher' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='accounts_payable_voucher'>
                            <i class="fas fa-file-alt align-middle"></i> AP Voucher
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'purchase_return' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='purchase_return'>
                            <i class="fas fa-undo align-middle"></i> Purchase Return
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'pay_bills' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='pay_bills'>
                            <i class="fas fa-dollar-sign align-middle"></i> Pay Bills
                        </a>
                    </li>
                </ul>
            </li>
            <!-- BANKING -->
            <li class="sidebar-item">
                <a data-bs-target="#banking" data-bs-toggle="collapse" class="sidebar-link collapsed">
                    <i class="fas fa-credit-card align-middle"></i>
                    <i class="fas fa-chevron-right align-middle arrow-icon"></i>
                    <span class="align-middle"><b>Banking</b></span>
                </a>
                <ul id="banking"
                    class="sidebar-dropdown list-unstyled <?php echo in_array(getCurrentPage(), ['write_check', 'make_deposit', 'bank_transfer', 'reconcile']) ? '' : 'collapse'; ?>"
                    data-bs-parent="#sidebar">
                    <li class="sidebar-item <?php echo getCurrentPage() == 'write_check' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='write_check'>
                            <i class="fas fa-file-alt align-middle"></i> Write Check
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'make_deposit' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='make_deposit'>
                            <i class="fas fa-arrow-circle-down align-middle"></i> Make Deposit
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'bank_transfer' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='bank_transfer'>
                            <i class="fas fa-sync-alt align-middle"></i> Bank Transfer
                        </a>
                    </li>
                    <!-- <li class="sidebar-item <?php echo getCurrentPage() == 'reconcile' ? 'active' : ''; ?>">
            <a class='sidebar-link' href='reconcile'>
                <i class="fas fa-check-circle align-middle"></i> Reconcile
            </a>
        </li> -->
                </ul>
            </li>

            <!-- PURCHASING -->
            <li class="sidebar-item">
                <a data-bs-target="#purchase" data-bs-toggle="collapse" class="sidebar-link collapsed">
                    <i class="fas fa-shopping-cart align-middle"></i>
                    <i class="fas fa-chevron-right align-middle arrow-icon"></i>
                    <span class="align-middle"><b>Purchasing</b></span>
                </a>
                <ul id="purchase" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a class='sidebar-link' href='purchase_request'>
                            <i class="fas fa-file-alt align-middle"></i> Purchase Request
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'purchase_order' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='purchase_order'>
                            <i class="fas fa-shopping-cart align-middle"></i> Purchase Order
                        </a>
                    </li>
                </ul>
            </li>

            <!-- ACCOUNTING -->
            <li class="sidebar-item">
                <a data-bs-target="#accouting" data-bs-toggle="collapse"
                    class="sidebar-link <?php echo getCurrentPage() == 'transaction_entries' ? '' : 'collapsed'; ?>">
                    <i class="fas fa-user align-middle"></i>
                    <i class="fas fa-chevron-right align-middle arrow-icon"></i>
                    <span class="align-middle"><b>Accounting</b></span>
                </a>
                <ul id="accouting"
                    class="sidebar-dropdown list-unstyled <?php echo in_array(getCurrentPage(), ['chart_of_accounts_list', 'general_journal', 'transaction_entries', 'trial_balance', 'audit_trail']) ? 'show' : 'collapse'; ?>"
                    data-bs-parent="#sidebar">
                    <li
                        class="sidebar-item <?php echo getCurrentPage() == 'chart_of_accounts_list' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='chart_of_accounts_list'>
                            <i class="fas fa-list align-middle"></i> Chart of Accounts
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'general_journal' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='general_journal'>
                            <i class="fas fa-file-alt align-middle"></i> General Journal
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'transaction_entries' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='transaction_entries'>
                            <i class="fas fa-file-alt align-middle"></i> Transaction Entries
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'trial_balance' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='trial_balance'>
                            <i class="fas fa-chart-bar align-middle"></i> Trial Balance
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'audit_trail' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='audit_trail'>
                            <i class="fas fa-clipboard align-middle"></i> Audit Trail
                        </a>
                    </li>
                </ul>
            </li>


            <!-- REPORTS -->
            <li class="sidebar-item <?php echo getCurrentPage() == 'reports' ? 'active' : ''; ?>">
                <a class="sidebar-link" href="reports">
                    <i class="align-middle fas fa-chart-bar"></i> <span class="align-middle">Reports</span>
                </a>
            </li>
            <!-- MASTERLIST -->
            <li class="sidebar-item ">
                <a data-bs-target="#master" data-bs-toggle="collapse" class="sidebar-link collapsed">
                    <i class="align-middle fas fa-users"></i>
                    <i class="align-middle arrow-icon fas fa-chevron-right"></i>
                    <span class="align-middle"><b>Master List</b></span>
                </a>

                <ul id="master"
                    class="sidebar-dropdown list-unstyled <?php echo in_array(getCurrentPage(), ['chart_of_accounts', 'item_list', 'vendor_list', 'customer', 'other_name']) ? '' : 'collapse'; ?>"
                    data-bs-parent="#sidebar">
                    <li class="sidebar-item <?php echo getCurrentPage() == 'chart_of_accounts' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='chart_of_accounts'>
                            <i class="align-middle fas fa-list"></i> Chart of Accounts
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'item_list' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='item_list'>
                            <i class="align-middle fas fa-shopping-bag"></i> Item List
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'vendor_list' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='vendor_list'>
                            <i class="align-middle fas fa-users"></i> Vendor List
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'other_name' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='other_name'>
                            <i class="align-middle fas fa-users"></i> Other Name List
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'customer' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='customer'>
                            <i class="align-middle fas fa-shopping-bag"></i> Customer List
                        </a>
                    </li>
                </ul>
            </li>

            <li class="sidebar-item  ">
                <a data-bs-target="#other" data-bs-toggle="collapse" class="sidebar-link collapsed">
                    <i class="align-middle fas fa-check-circle"></i>
                    <i class="align-middle arrow-icon fas fa-chevron-right"></i>
                    <span class="align-middle"><b>Other List</b></span>
                </a>

                <ul id="other"
                    class="sidebar-dropdown list-unstyled <?php echo in_array(getCurrentPage(), ['location', 'uom', 'cost_center', 'category', 'terms', 'payment_method', 'discount', 'input_vat', 'sales_tax', 'wtax']) ? '' : 'collapse'; ?>"
                    data-bs-parent="#sidebar">
                    <li class="sidebar-item <?php echo getCurrentPage() == 'location' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='location'>
                            <i class="align-middle fas fa-map-marker-alt"></i> Location List
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'uom' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='uom'>
                            <i class="align-middle fas fa-layer-group"></i> Unit of Measure
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'cost_center' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='cost_center'>
                            <i class="align-middle fas fa-file-alt"></i> Cost Center
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'category' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='category'>
                            <i class="align-middle fas fa-list"></i> Category List
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'terms' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='terms'>
                            <i class="align-middle fas fa-file-alt"></i> Terms List
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'payment_method' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='payment_method'>
                            <i class="align-middle fas fa-credit-card"></i> Payment Methods
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'discount' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='discount'>
                            <i class="align-middle fas fa-percent"></i> Discount
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'input_vat' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='input_vat'>
                            <i class="align-middle fas fa-percent"></i> Input VAT
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'sales_tax' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='sales_tax'>
                            <i class="align-middle fas fa-percent"></i> Sales Tax Rate
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'wtax' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='wtax'>
                            <i class="align-middle fas fa-percent"></i> WTax Rate
                        </a>
                    </li>
                </ul>
            </li>
            <!-- SYSTEM MAINTENANCE -->
            <li class="sidebar-header">
                System Maintenance
            </li>
            <li class="sidebar-item <?php echo getCurrentPage() == 'user_list' ? 'active' : ''; ?>">
                <a class="sidebar-link" href="user_list">
                    <i class="align-middle fas fa-user"></i> <span class="align-middle">Users</span>
                </a>
            </li>
        </ul>


    </div>
</nav>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        feather.replace();
    });
</script>