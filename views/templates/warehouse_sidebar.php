<?php
function shouldSidebarBeCollapsed()
{
    $pagesToCollapseSidebar = ['transaction_entries', 'create_invoice', 'view_invoice', 'create_purchase_order', 'view_purchase_order', 'create_receive_item']; // Add more page names as needed
    return in_array(getCurrentPage(), $pagesToCollapseSidebar);
}
?>
<style>
    .sidebar-item.active {

        background: linear-gradient(to right, rgb(13, 128, 64) 100%, rgb(13, 128, 64) 0%);
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
            <span class="align-middle">HISUMCO WRHOUSE</span>
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

            <li class="sidebar-item  <?php echo getCurrentPage() == 'dashboard' ? 'active' : ''; ?>">
                <a class="sidebar-link" href="dashboard">
                    <i class="align-middle" data-feather="home"></i> <span class="align-middle">Dashboard</span>
                </a>
            </li>







            <li class="sidebar-item">
                <a data-bs-target="#purchase" data-bs-toggle="collapse" class="sidebar-link collapsed">
                    <i class="align-middle" data-feather="shopping-cart"></i>
                    <i class="align-middle arrow-icon" data-feather="chevron-right"></i>
                    <span class="align-middle">Purchasing</span>

                </a>
                <ul id="purchase" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                    <li class="sidebar-item">
                        <a class='sidebar-link' href='warehouse_purchase_request'>
                            <i class="align-middle" data-feather="file-text"></i> Purchase Request
                        </a>
                    </li>
                </ul>
            </li>


            <li class="sidebar-item">
                <a data-bs-target="#material" data-bs-toggle="collapse" class="sidebar-link collapsed">
                    <i class="align-middle" data-feather="box"></i>
                    <i class="align-middle arrow-icon" data-feather="chevron-right"></i>
                    <span class="align-middle">Warehouse
                    </span>
                </a>
                <ul id="material" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                    <li
                        class="sidebar-item <?php echo getCurrentPage() == 'warehouse_receive_items' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='warehouse_receive_items'>
                            <i class="align-middle" data-feather="box"></i> Receive items
                        </a>
                    </li>
                    <li class="sidebar-item">
                        <a class='sidebar-link' href='/material-request'>
                            <i class="align-middle" data-feather="file-text"></i> Material Request
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'material_issuance' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='material_issuance'>
                            <i class="align-middle" data-feather="file-text"></i> Material Issuance
                        </a>
                    </li>
                </ul>
            </li>



            <li class="sidebar-item  ">
                <a data-bs-target="#reports" data-bs-toggle="collapse" class="sidebar-link collapsed">
                    <i class="align-middle" data-feather="file-text"></i>
                    <i class="align-middle arrow-icon" data-feather="chevron-right"></i>
                    <span class="align-middle">Reports</span>

                </a>

                <ul id="reports"
                    class="sidebar-dropdown list-unstyled <?php echo in_array(getCurrentPage(), ['profit_loss', 'balance_sheet']) ? '' : 'collapse'; ?> "
                    data-bs-parent="#sidebar">
                    <li class="sidebar-item <?php echo getCurrentPage() == 'profit_loss' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='profit_loss'>
                            <i class="align-middle" data-feather="trending-up"></i> Profit & Loss
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo getCurrentPage() == 'balance_sheet' ? 'active' : ''; ?>">
                        <a class='sidebar-link' href='balance_sheet'>
                            <i class="align-middle" data-feather="bar-chart"></i> Balance Sheet
                        </a>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        feather.replace();
    });

</script>