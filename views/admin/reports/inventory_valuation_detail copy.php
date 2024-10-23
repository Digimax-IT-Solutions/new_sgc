<?php
//Guard
//Guard
require_once '_guards.php';
$currentUser = User::getAuthenticatedUser();
if (!$currentUser) {
    redirect('login.php');
}
Guard::restrictToModule('inventory_report');

$inventories = InventoryValuation::all();

?>

<?php require 'views/templates/header.php' ?>
<?php require 'views/templates/sidebar.php' ?>

<div class="main">
    <?php require 'views/templates/navbar.php' ?>

    <style>
        .scroll-container {
            max-width: 100%;
            overflow-x: auto;
        }

        #table {
            width: 100%;
            min-width: 100px;
            /* Adjust based on your needs */
        }

        th,
        td {
            white-space: nowrap;
        }

        .inventory-header {
            border: 1px solid rgb(13, 128, 64) !important;
            /* Apply yellow border */
        }

        /* .inventory-cell:nth-child(2) {
            border-left: 1px solid rgb(13, 128, 64) !important;
        }


        .inventory-cell:nth-child(5) {
            border-right: 1px solid rgb(13, 128, 64) !important;
        } */

        /* .inventory-cell:nth-child(9) {
            border-right: 1px solid rgb(13, 128, 64) !important;
        } */

        /* .inventory-cell:nth-child(5) {
            border-right: 1px solid rgb(13, 128, 64) !important;
        } */

        .inventory-header:nth-child(1) {
            background-color: rgb(13, 128, 64) !important;
            color: #fff;
            /* Optional: Light yellow background for header */
        }

        .inventory-header:nth-child(4) {
            background-color: rgb(13, 128, 64) !important;
            color: #fff;
            /* Optional: Light yellow background for header */
        }
    </style>


    <main class="content">
        <div class="container-fluid p-0">
            <div class="row mb-4">
                <div class="col-12 d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-2"><strong>Inventory Valuation</strong> Detail</h1>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item" aria-current="page"><a href="dashboard">Reports</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Inventory Valuation Detail</li>
                            </ol>
                        </nav>
                    </div>

                </div>
            </div>

            <div class="card">
                <div class="card-header bg-white text-white">

                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <?php
                        // Initialize totals
                        $total_qty_purchased = 0;
                        $total_qty_sold = 0;
                        $total_total_cost = 0;
                        ?>

                        <table id="table" class="table table-striped table-hover">
                            <thead>
                                <tr>

                                    <th colspan="13" class="inventory-header" style="text-align:center;">INVENTORY
                                        VALUATION DETAIL<br><?= date("Y/m/d"); ?></th>
                                    <!-- Main header for inventory -->


                                </tr>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>RR No</th>
                                    <th>Name</th>
                                    <th>Item Name</th>
                                    <th class="inventory-cell">Qty Purchased</th>
                                    <th class="inventory-cell">Qty on Hand</th>
                                    <th class="inventory-cell">U/M</th>
                                    <th>Cost</th>
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
                                <?php foreach ($inventories as $inventory): ?>
                                    <tr>
                                        <td><?= $inventory->date; ?></td>
                                        <td><?= $inventory->type; ?></td>
                                        <td><?= $inventory->ref_no; ?></td>
                                        <td><?= $inventory->vendor_name; ?></td>
                                        <td><?= $inventory->item_name; ?></td>
                                        <td class="inventory-cell" align="right">
                                            <?= number_format($inventory->qty_purchased, 0); ?>
                                        </td>
                                        <td class="inventory-cell" align="right">
                                            <?= number_format($inventory->qty_on_hand, 0); ?>
                                        </td>
                                        <td class="inventory-cell">ea</td>

                                        <td align="right"><?= number_format($inventory->cost, 2); ?></td>
                                        <td align="right"><?= number_format($inventory->total_cost, 2); ?></td>

                                        <td align="right"><?= $inventory->purchase_discount_rate; ?>%</td>
                                        <td align="right"><?= $inventory->purchase_discount_per_item; ?></td>
                                        <td align="right"><?= $inventory->purchase_discount_amount; ?></td>
                                        <td align="right"><?= $inventory->net_amount; ?></td>
                                        <td align="right"><?= $inventory->input_vat_rate; ?>%</td>
                                        <td align="right"><?= $inventory->input_vat; ?></td>
                                        <td align="right"><?= $inventory->taxable_purchased_amount; ?></td>
                                        <td align="right"><?= $inventory->cost_per_unit; ?></td>
                                        <td align="right"><?= $inventory->selling_price; ?></td>
                                        <td align="right"><?= $inventory->gross_sales; ?></td>
                                        <td align="right"><?= $inventory->sales_discount_rate; ?>%</td>
                                        <td align="right"><?= $inventory->sales_discount_amount; ?></td>
                                        <td align="right"><?= $inventory->net_sales; ?></td>
                                        <td align="right"><?= $inventory->sales_tax; ?></td>
                                        <td align="right"><?= $inventory->output_vat; ?></td>
                                        <td align="right"><?= $inventory->taxable_sales_amount; ?></td>
                                        <td align="right"><?= $inventory->selling_price_per_unit; ?></td>
                                        <td align="right"><?= $inventory->weighted_average_cost; ?></td>
                                        <td align="right"><?= $inventory->asset_value_wa; ?></td>
                                        <td align="right"><?= $inventory->fifo_cost; ?></td>
                                        <td align="right"><?= $inventory->cost_of_goods_sold; ?></td>
                                        <td align="right"><?= $inventory->asset_value_fifo; ?></td>
                                        <td align="right"><?= $inventory->gross_margin; ?></td>
                                    </tr>

                                    <?php
                                    // Calculate totals
                                    $total_qty_purchased += $inventory->qty_purchased;
                                    $total_qty_sold += $inventory->qty_sold;
                                    $total_total_cost += $inventory->total_cost;

                                    ?>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5"><strong>Totals:</strong></td>

                                    <td align="right" class="inventory-cell">
                                        <strong><?= $total_qty_purchased; ?></strong>
                                    </td>
                                    <td align="right" class="inventory-cell"><strong><?= $total_qty_sold; ?></strong>
                                    </td>
                                    <td class="inventory-cell"></td>

                                    <td></td>
                                    <td></td>
                                    <td align="right"><strong><?= number_format($total_total_cost, 2); ?></strong></td>
                                    <td></td>

                                </tr>
                            </tfoot>
                        </table>


                    </div>
                </div>
            </div>
        </div>
        <!-- /.card -->
    </main>

    <?php require 'views/templates/footer.php' ?>
</div>

<script>
    $(function() {
        $("#table").DataTable({
            "responsive": false,
            "lengthChange": true,
            "autoWidth": false,
            "searching": false, // Disable search
            "buttons": [{
                    extend: 'copy',
                    exportOptions: {
                        columns: ':not(:last-child)' // Exclude the last column (Action)
                    }
                },
                {
                    extend: 'csv',
                    exportOptions: {
                        columns: ':not(:last-child)' // Exclude the last column (Action)
                    }
                },
                {
                    extend: 'excel',
                    title: 'HISUMCO INVENTORY VALUATION',
                    exportOptions: {
                        columns: ':not(:last-child)' // Exclude the last column (Action)
                    }
                },
                {
                    extend: 'pdf',
                    exportOptions: {
                        columns: ':not(:last-child)' // Exclude the last column (Action)
                    }
                },
                {
                    extend: 'print',
                    exportOptions: {
                        columns: ':not(:last-child)' // Exclude the last column (Action)
                    }
                },
                {
                    extend: 'colvis',
                    collectionLayout: 'fixed two-column',
                    postfixButtons: ['colvisRestore']
                }
            ],
            dom: 'Bfrtip',
            colReorder: true
        }).buttons().container().appendTo('.card-header'); // Append buttons to card header

        // Make the column visibility dropdown scrollable
        $('.dt-button-collection').css({
            'max-height': '300px',
            'overflow-y': 'auto'
        });
    });
</script>