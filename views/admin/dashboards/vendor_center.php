<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>

<div class="main">
    <?php require 'views/templates/navbar.php' ?>

    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-2"><strong>Vendor Center</strong></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Vendor Center</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Quick Summary Section -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title text-white">Total Purchases</h5>
                            <h3 class="mb-0 text-white">0.00</h3>
                            <small>0% from last month</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title text-white">Total Payables</h5>
                            <h3 class="mb-0 text-white">0.00</h3>
                            <small>0% from last month</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title text-white">Active Vendors</h5>
                            <h3 class="mb-0 text-white">0</h3>
                            <small>This month</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title text-white">Pending Orders</h5>
                            <h3 class="mb-0 text-white">0</h3>
                            <small>As of today</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Vendor Center Functions -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <?php
                                $vendorItems = [
                                    ['icon' => 'shopping-cart', 'title' => 'Purchase Order', 'link' => 'purchase_order', 'description' => 'Create and manage purchase orders'],
                                    ['icon' => 'box', 'title' => 'Receive Items', 'link' => 'receive_items', 'description' => 'Record received items from vendors'],
                                    ['icon' => 'file-text', 'title' => 'AP Voucher', 'link' => 'accounts_payable_voucher', 'description' => 'Manage accounts payable vouchers'],
                                    ['icon' => 'rotate-ccw', 'title' => 'Purchase Return', 'link' => 'purchase_return', 'description' => 'Process purchase returns'],
                                    ['icon' => 'dollar-sign', 'title' => 'Pay Bills', 'link' => 'pay_bills', 'description' => 'Manage and pay vendor bills'],
                                ];

                                foreach ($vendorItems as $item) {
                                    echo '<div class="col-md-4 col-xl-3 mb-4">
                                        <a href="' . $item['link'] . '" class="text-decoration-none">
                                            <div class="card h-100 shadow-sm hover-effect">
                                                <div class="card-body d-flex flex-column">
                                                    <div class="d-flex align-items-center mb-3">
                                                        <div class="flex-shrink-0 icon-circle">
                                                            <i data-feather="' . $item['icon'] . '" class="feather-md text-primary"></i>
                                                        </div>
                                                        <div class="flex-grow-1 ms-3">
                                                            <h5 class="card-title mb-0">' . $item['title'] . '</h5>
                                                        </div>
                                                    </div>
                                                    <p class="card-text text-muted flex-grow-1">' . $item['description'] . '</p>
                                                    <div class="mt-auto text-end">
                                                        <small class="text-primary">Click to access <i data-feather="arrow-right" class="feather-sm"></i></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require 'views/templates/footer.php' ?>
</div>

<style>
    .hover-effect {
        transition: all 0.3s ease;
        border: 1px solid #e0e0e0;
    }

    .hover-effect:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1) !important;
        border-color: #007bff;
        background-color: #f8f9fa;
    }

    .icon-circle {
        background-color: #e8f0fe;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-title {
        color: #333;
        font-weight: 600;
    }

    .text-muted {
        color: #6c757d !important;
    }

    .text-primary {
        color: #007bff !important;
    }

    .card-body {
        padding: 1.5rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Initialize Feather Icons
        feather.replace();
    });
</script>