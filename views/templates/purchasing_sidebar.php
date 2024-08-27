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
            <span class="align-middle">HISUMCO PURCHASING</span>
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
            <li class="sidebar-item <?php echo getCurrentPage() == 'purchasing_home' ? 'active' : ''; ?>">
                <a class="sidebar-link" href="purchasing_home">
                    <i class="fas fa-home align-middle"></i> <span class="align-middle">Dashboard</span>
                </a>
            </li>

            <!-- PURCHASING -->
            <li
                class="sidebar-item <?php echo getCurrentPage() == 'purchasing_purchase_request' || getCurrentPage() == 'purchase_order' ? 'active' : ''; ?>">
                <a class="sidebar-link" href="purchasing_purchase_request">
                    <i class="fas fa-file-alt align-middle"></i> <span class="align-middle">Purchase Request</span>
                </a>
            </li>
            <li class="sidebar-item <?php echo getCurrentPage() == 'purchasing_purchase_order' ? 'active' : ''; ?>">
                <a class="sidebar-link" href="purchasing_purchase_order">
                    <i class="fas fa-shopping-cart align-middle"></i> <span class="align-middle">Purchase Order</span>
                </a>
            </li>

            <li class="sidebar-item <?php echo getCurrentPage() == 'purchasing_receive_item' ? 'active' : ''; ?>">
                <a class='sidebar-link' href='purchasing_receive_item'>
                    <i class="fas fa-box align-middle"></i> Receive items
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