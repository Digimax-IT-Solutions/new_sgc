<?php
//Guard
require_once '_guards.php';
Guard::adminOnly();

$locations = Location::all();
$products = Product::all();
$cost_centers = CostCenter::all();

$page = 'view_purchase_request';
?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>

<style>
    .form-label {
        font-size: 0.675rem;
        margin-bottom: 0.25rem;
    }

    .card-body {
        font-size: 0.875rem;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    #itemTable {
        /* Adjust this value based on your table's content */
        table-layout: fixed;
    }

    #itemTable th {
        white-space: nowrap;
    }

    #itemTable tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    #itemTable th,
    #itemTable td {
        padding: 0.5rem;
        vertical-align: middle;
    }

    #itemTable .text-right {
        text-align: right;
    }

    #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }

    #loadingOverlay .spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid #3498db;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        animation: spin 1s linear infinite;
    }

    #loadingOverlay .message {
        color: white;
        margin-top: 10px;
        font-size: 18px;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }
</style>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>

    <!-- Content Wrapper. Contains page content -->
    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3"><strong>Create Purchase Request</strong></h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="purchase_request">Purchase Request</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Create Purchase Request</li>
                            </ol>
                        </nav>
                    </div>
                    <div>
                        <a href="purchase_request" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Purchases List
                        </a>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <?php
                    if (isset($_GET['id'])) {
                        $id = $_GET['id'];

                        $purchase_request = PurchaseRequest::find($id);

                        if ($purchase_request) { ?>
                            <!-- Purchase Order Form -->
                            <form id="purchaseOrderForm" action="api/purchase_request_controller.php?action=update" method="POST">
                                <input type="hidden" name="action" id="modalAction" value="update" />
                                <input type="hidden" name="id" id="itemId" value="" />
                                <input type="hidden" name="item_data" id="item_data" />

                                <div class="row">
                                    <div class="col-12 col-lg-12">
                                        <div class="card h-100">
                                            <div class="card-header">
                                                <h5 class="card-title mb-0">Purchase Request Details</h5>
                                            </div>

                                            <div class="card-body">

                                                <div class="row g-2">
                                                    <!-- PR No -->
                                                    <div class="col-md-3 order-details">
                                                        <div class="form-group">
                                                            <!-- PURCHASE ORDER NO -->
                                                            <label for="pr_no">Purchase Request #:</label>
                                                            <input type="text" class="form-control form-control-sm" id="pr_no"
                                                                name="pr_no" value="<?= $purchase_request->pr_no ?>" readonly>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row g-2">
                                                    <!-- Requesting Section -->
                                                    <div class="col-md-4 order-details">
                                                        <label for="location" class="form-label">Location</label>
                                                        <select class="form-select form-select-sm" id="location" name="location" disabled>
                                                            <?php foreach ($locations as $location): ?>
                                                                <option value="<?= $location->id ?>"
                                                                    <?= $location->id == $purchase_request->location ? 'selected' : '' ?>>
                                                                    <?= $location->name ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-3 order-details">
                                                        <!-- DATE -->
                                                        <div class="form-group">
                                                            <label for="date">Date</label>
                                                            <input type="date" class="form-control form-control-sm" id="date"
                                                                name="date" value="<?= $purchase_request->date ?>" disabled>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3 order-details">
                                                        <!-- DELIVERY DATE -->
                                                        <div class="form-group">
                                                            <label for="required_date">Required Date</label>
                                                            <input type="date" class="form-control form-control-sm"
                                                                id="required_date" name="required_date"
                                                                value="<?= $purchase_request->required_date ?>" disabled>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 order-details"></div>
                                                    <div class="col-md-3 order-details"></div>

                                                    <div class="col-md-8 order-details">
                                                        <!-- MEMO -->
                                                        <label for="memo" class="form-label">Memo/Purpose</label>
                                                        <input type="text" class="form-control form-control-sm" id="memo"
                                                            name="memo" value="<?= $purchase_request->memo ?>" disabled>
                                                    </div>
                                                </div>
                                                <br>
                                                <div class="row g-2">
                                                    <div class="col-md-12 text-center">
                                                        <button type="button" class="btn btn-secondary btn-sm">
                                                            <i class="fas fa-save"></i> Void
                                                        </button>
                                                        <a class="btn btn-success btn-sm" href="#" id="reprintButton">
                                                            <i class="fas fa-print"></i> Reprint
                                                        </a>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>

                                    <!-- Items Table Section -->
                                    <div class="row mt-4">
                                        <div class="col-12">
                                            <div class="card">
                                                <div class="card-header d-flex justify-content-between align-items-center">
                                                    <h5 class="card-title mb-0">Purchase Request Items</h5>
                                                </div>
                                                <div class="card-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-hover" id="itemTable">
                                                            <thead class="bg-light" style="font-size: 12px;">
                                                                <tr>
                                                                    <th>Item</th>
                                                                    <th>Cost Center</th>
                                                                    <th>Description</th>
                                                                    <th style="width: 100px; background-color: #e6f3ff;">Quantity</th>
                                                                    <th style="width: 100px">Unit</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="itemTableBody" style="font-size: 14px;">
                                                            <?php if ($purchase_request): ?>
                                                                <?php foreach ($purchase_request->details as $detail): ?>
                                                                    <tr>
                                                                        <td>
                                                                            <select
                                                                                class="form-control form-control-sm item-dropdown"
                                                                                name="item_id[]" disabled>
                                                                                <?php foreach ($products as $product): ?>
                                                                                    <option value="<?= htmlspecialchars($product->id) ?>"
                                                                                        <?= ($product->id == $detail['item_id']) ? 'selected' : '' ?>>
                                                                                        <?= htmlspecialchars($product->item_name) ?>
                                                                                    </option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <select
                                                                                class="form-control form-control-sm cost-center-dropdown"
                                                                                name="cost_center_id[]" disabled>
                                                                                <?php foreach ($cost_centers as $cost_center): ?>
                                                                                    <option
                                                                                        value="<?= htmlspecialchars($cost_center->id) ?>"
                                                                                        <?= ($cost_center->id == $detail['cost_center_id']) ? 'selected' : '' ?>>
                                                                                        <?= htmlspecialchars($cost_center->particular) ?>
                                                                                    </option>
                                                                                <?php endforeach; ?>
                                                                            </select>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text"
                                                                                class="form-control form-control-sm item_purchase_description"
                                                                                name="item_purchase_description[]"
                                                                                value="<?= htmlspecialchars($detail['item_purchase_description']) ?>"
                                                                                readonly>
                                                                        </td>
                                                                        <td class="text-right" style="background-color: #e6f3ff;">
                                                                            <input type="text" class="form-control form-control-sm quantity text-right" name="quantity[]"
                                                                                value="<?= htmlspecialchars($detail['quantity']) ?>" disabled>
                                                                        </td>
                                                                        <td>
                                                                            <input type="text"
                                                                                class="form-control form-control-sm uom_name"
                                                                                name="uom_name[]"
                                                                                value="<?= htmlspecialchars($detail['name']) ?>"
                                                                                disabled>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <!-- <button type="button" class="btn btn-primary btn-sm" id="addItemBtn">
                                                        <i class="fas fa-plus"></i> Add Item
                                                    </button> -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                    <?php
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
            </div>
        </div>
    </main>
    <div id="loadingOverlay" style="display: none;">
        <div class="spinner"></div>
        <div class="message">Processing Purchase Order</div>
    </div>
</div>

<?php require 'views/templates/footer.php' ?>

<script>
    $(document).ready(function() {
        initializeSelect2();
    });  

    function initializeSelect2() {
        // Other Select2 initializations (if needed)
        $('#location').select2({
            theme: 'classic',
            allowClear: false
        });

        $('.item-dropdown, .cost-center-dropdown').select2({
            theme: 'classic',
            allowClear: false
        });
    }
</script>