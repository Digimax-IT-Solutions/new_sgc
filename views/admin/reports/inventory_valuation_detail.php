<?php
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('inventory_report');

$groupedInventories = InventoryValuation::getAllGroupedByItem();

?>
<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>
<style>
    #inventoryTable {
        font-size: 0.8rem;
    }

    #inventoryTable td,
    #inventoryTable th {
        padding: 0.4rem;
    }

    .collapse table {
        font-size: 0.8rem;
    }

    .collapse td,
    .collapse th {
        padding: 0.3rem;
    }

    .btn-xs {
        padding: 0.1rem 0.1rem;
        font-size: 0.75rem;
        line-height: 0.5;
    }

    .table-sm {
        margin-bottom: 0;
    }

    .expandable-row {
        cursor: pointer;
    }

    .expandable-row:hover {
        background-color: #f8f9fa;
    }
</style>
<div class="main">
    <?php require 'views/templates/navbar.php' ?>

    <main class="content">
        <div class="container-fluid p-0">
            <div class="card">
                <div class="card-header">
                    <h1 class="h3 mb-2"><strong>Inventory Valuation Report</strong></h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="dashboard">Reports</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Inventory Valuation</li>
                        </ol>
                    </nav>

                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="inventoryTable" class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th class="text-end">Total Qty on Hand</th>
                                    <th class="text-end">Total Asset Value</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($groupedInventories as $itemId => $groupedInventory): ?>
                                    <tr class="expandable-row" data-bs-toggle="collapse" data-bs-target="#collapseItem<?= $itemId ?>" aria-expanded="false" aria-controls="collapseItem<?= $itemId ?>">
                                        <td><?= htmlspecialchars($groupedInventory['item_name']) ?></td>
                                        <td class="text-end"><?= number_format($groupedInventory['total_qty_on_hand'], 0) ?></td>
                                        <td class="text-end"><?= number_format($groupedInventory['total_asset_value_wa'], 2) ?></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="p-0">
                                            <div id="collapseItem<?= $itemId ?>" class="collapse">
                                                <!--  -->
                                                <div class="table-responsive">
                                                    <table class="table table-sm table-bordered table-striped small mb-0">
                                                        <thead>
                                                            <tr>
                                                                <th>Date</th>
                                                                <th>Type</th>
                                                                <th>RR No</th>
                                                                <th>Vendor</th>
                                                                <th class="text-end">Qty Purch.</th>
                                                                <th class="inventory-cell">Qty Sold</th>
                                                                <th class="text-end">Qty on Hand</th>
                                                                <th class="text-end">Cost</th>
                                                                <th class="text-end">Total Cost</th>
                                                                <th>Purchase Discount Rate</th>
                                                                <th>Purchase Discount Per Item</th>
                                                                <th>Purchase Discount Amount</th>
                                                                <th>Net Amount</th>
                                                                <th>Input VAT Rate</th>
                                                                <th>Input VAT</th>
                                                                <th>Taxable Purchased Amount</th>
                                                                <th>Cost per Unit</th>
                                                                <th>Selling Price</th>
                                                                <th>Gross Sales</th>
                                                                <th>Sales Discount Rate</th>
                                                                <th>Sales Discount Amount</th>
                                                                <th>Net Sales</th>
                                                                <th>Sales Tax</th>
                                                                <th>Output VAT</th>
                                                                <th>Taxable Sales Amount</th>
                                                                <th>Selling Price per Unit</th>
                                                                <th>Weighted Average Cost</th>
                                                                <th>Asset Value (WA)</th>
                                                                <th>FIFO Cost</th>
                                                                <th>Cost of Goods Sold</th>
                                                                <th>Asset Value (FIFO)</th>
                                                                <th>Gross Margin</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($groupedInventory['entries'] as $inventory): ?>
                                                                <tr>
                                                                    <td><?= htmlspecialchars($inventory->date) ?></td>
                                                                    <td><?= htmlspecialchars($inventory->type) ?></td>
                                                                    <td><?= htmlspecialchars($inventory->ref_no) ?></td>
                                                                    <td><?= htmlspecialchars($inventory->vendor_name) ?></td>
                                                                    <td class="text-end"><?= number_format($inventory->qty_purchased, 0) ?></td>
                                                                    <td class="inventory-cell" align="right"><?= number_format($inventory->qty_sold, 0); ?></td>
                                                                    <td class="text-end"><?= number_format($inventory->qty_on_hand, 0) ?></td>
                                                                    <td class="text-end"><?= number_format($inventory->cost, 2); ?></td>
                                                                    <td class="text-end"><?= number_format($inventory->total_cost, 2); ?></td>
                                                                    <td class="text-end"><?= $inventory->purchase_discount_rate; ?>%</td>
                                                                    <td class="text-end"><?= $inventory->purchase_discount_per_item; ?></td>
                                                                    <td class="text-end"><?= $inventory->purchase_discount_amount; ?></td>
                                                                    <td class="text-end"><?= $inventory->net_amount; ?></td>
                                                                    <td class="text-end"><?= $inventory->input_vat_rate; ?>%</td>
                                                                    <td class="text-end"><?= $inventory->input_vat; ?></td>
                                                                    <td class="text-end"><?= $inventory->taxable_purchased_amount; ?></td>
                                                                    <td class="text-end"><?= $inventory->cost_per_unit; ?></td>
                                                                    <td class="text-end"><?= $inventory->selling_price; ?></td>
                                                                    <td class="text-end"><?= $inventory->gross_sales; ?></td>
                                                                    <td class="text-end"><?= $inventory->sales_discount_rate; ?>%</td>
                                                                    <td class="text-end"><?= $inventory->sales_discount_amount; ?></td>
                                                                    <td class="text-end"><?= $inventory->net_sales; ?></td>
                                                                    <td class="text-end"><?= $inventory->sales_tax; ?></td>
                                                                    <td class="text-end"><?= $inventory->output_vat; ?></td>
                                                                    <td class="text-end"><?= $inventory->taxable_sales_amount; ?></td>
                                                                    <td class="text-end"><?= $inventory->selling_price_per_unit; ?></td>
                                                                    <td class="text-end"><?= $inventory->weighted_average_cost; ?></td>
                                                                    <td class="text-end"><?= $inventory->asset_value_wa; ?></td>
                                                                    <td class="text-end"><?= $inventory->fifo_cost; ?></td>
                                                                    <td class="text-end"><?= $inventory->cost_of_goods_sold; ?></td>
                                                                    <td class="text-end"><?= $inventory->asset_value_fifo; ?></td>
                                                                    <td align="right"><?= $inventory->gross_margin; ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                        <tfoot>
                                                            <tr class="table-primary">
                                                                <th colspan="4">Totals</th>
                                                                <th class="text-end"><?= number_format($groupedInventory['total_qty_purchased'], 0) ?></th>
                                                                <th class="text-end"><?= number_format($groupedInventory['total_qty_sold'], 0) ?></th>
                                                                <th></th>
                                                                <th></th>
                                                                <th class="text-end"><?= number_format($groupedInventory['total_cost'], 2) ?></th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                                <!--  -->
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php require 'views/templates/footer.php' ?>
</div>

<script>
    $(document).ready(function() {
        var table = $('#inventoryTable').DataTable(); // Initialize DataTable

        // Handle row click to expand/collapse
        $('#inventoryTable tbody').on('click', 'tr.expandable-row', function() {
            var tr = $(this);
            var row = table.row(tr);

            if (row.child.isShown()) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Open this row
                var collapseDiv = tr.next().find('div.collapse').clone();
                row.child(collapseDiv).show();
                collapseDiv.collapse('show');
                tr.addClass('shown');
            }
        });

        // Date range buttons
        $('#dateToday, #dateWeek, #dateMonth, #dateCustom').click(function() {
            // TODO: Implement date range filtering
            alert('Date range filtering to be implemented for: ' + $(this).text());
        });

        $('#exportPDF').click(function() {
            // TODO: Implement PDF export functionality
            alert('PDF export functionality to be implemented');
        });

        $('#exportExcel').click(function() {
            // TODO: Implement Excel export functionality
            alert('Excel export functionality to be implemented');
        });
    });
</script>