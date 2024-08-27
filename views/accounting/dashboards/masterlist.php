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
                        <h1 class="h3 mb-2"><strong>Master List</strong></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Master List</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Master List Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Master List</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php
                                $masterListItems = [
                                    ['icon' => 'list', 'title' => 'Chart of Accounts', 'link' => 'chart_of_accounts', 'description' => 'Manage your account structure'],
                                    ['icon' => 'shopping-bag', 'title' => 'Item List', 'link' => 'item_list', 'description' => 'Manage your product inventory'],
                                    ['icon' => 'users', 'title' => 'Vendor List', 'link' => 'vendor_list', 'description' => 'Manage your suppliers'],
                                    ['icon' => 'users', 'title' => 'Other Name List', 'link' => 'other_name', 'description' => 'Manage miscellaneous contacts'],
                                    ['icon' => 'shopping-bag', 'title' => 'Customer List', 'link' => 'customer', 'description' => 'Manage your customer database'],
                                ];

                                foreach ($masterListItems as $item) {
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

            <!-- Other List Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Other List</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php
                                $otherListItems = [
                                    ['icon' => 'map-pin', 'title' => 'Location List', 'link' => 'location', 'description' => 'Manage warehouse locations'],
                                    ['icon' => 'layers', 'title' => 'Unit of Measure', 'link' => 'uom', 'description' => 'Manage units of measurement'],
                                    ['icon' => 'file-text', 'title' => 'Cost Center', 'link' => 'cost_center', 'description' => 'Manage cost centers'],
                                    ['icon' => 'list', 'title' => 'Category List', 'link' => 'category', 'description' => 'Manage product categories'],
                                    ['icon' => 'file-text', 'title' => 'Terms List', 'link' => 'terms', 'description' => 'Manage payment terms'],
                                    ['icon' => 'credit-card', 'title' => 'Payment Methods', 'link' => 'payment_method', 'description' => 'Manage payment methods'],
                                    ['icon' => 'percent', 'title' => 'Discount', 'link' => 'discount', 'description' => 'Manage discount rates'],
                                    ['icon' => 'percent', 'title' => 'Input VAT', 'link' => 'input_vat', 'description' => 'Manage input VAT rates'],
                                    ['icon' => 'percent', 'title' => 'Sales Tax Rate', 'link' => 'sales_tax', 'description' => 'Manage sales tax rates'],
                                    ['icon' => 'percent', 'title' => 'WTax Rate', 'link' => 'wtax', 'description' => 'Manage withholding tax rates'],
                                ];

                                foreach ($otherListItems as $item) {
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