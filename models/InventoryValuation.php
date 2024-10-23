<?php


require_once __DIR__ . '/../_init.php';


class InventoryValuation
{
    private static $cache = null;

    // Properties for the class, adjust as needed
    public $type;
    public $transaction_id;
    public $ref_no;
    public $date;
    public $name;
    public $vendor_name;
    public $item_id;
    public $item_name;
    public $qty_purchased;
    public $qty_sold;
    public $qty_on_hand;
    public $cost;
    public $total_cost;
    public $purchase_discount_rate;
    public $purchase_discount_per_item;
    public $purchase_discount_amount;
    public $net_amount;
    public $input_vat_rate;
    public $input_vat;
    public $taxable_purchased_amount;
    public $cost_per_unit;
    public $selling_price;
    public $gross_sales;
    public $sales_discount_rate;
    public $sales_discount_amount;
    public $net_sales;
    public $sales_tax;
    public $output_vat;
    public $taxable_sales_amount;
    public $selling_price_per_unit;
    public $weighted_average_cost;
    public $asset_value_wa;
    public $fifo_cost;
    public $cost_of_goods_sold;
    public $asset_value_fifo;
    public $gross_margin;

    // Constructor to accept associative array
    public function __construct($data)
    {
        $this->type = $data['type'] ?? null;
        $this->transaction_id = $data['transaction_id'] ?? null;
        $this->ref_no = $data['ref_no'] ?? null;
        $this->date = $data['date'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->vendor_name = $data['vendor_name'] ?? null;
        $this->item_id = $data['item_id'] ?? null;
        $this->item_name = $data['item_name'] ?? null;
        $this->qty_purchased = $data['qty_purchased'] ?? null;
        $this->qty_on_hand = $data['qty_on_hand'] ?? null;
        $this->qty_sold = $data['qty_sold'] ?? null;
        $this->cost = $data['cost'] ?? null;
        $this->total_cost = $data['total_cost'] ?? null;
        $this->purchase_discount_rate = $data['purchase_discount_rate'] ?? null;
        $this->purchase_discount_per_item = $data['purchase_discount_per_item'] ?? null;
        $this->purchase_discount_amount = $data['purchase_discount_amount'] ?? null;
        $this->net_amount = $data['net_amount'] ?? null;
        $this->input_vat_rate = $data['input_vat_rate'] ?? null;
        $this->input_vat = $data['input_vat'] ?? null;
        $this->taxable_purchased_amount = $data['taxable_purchased_amount'] ?? null;
        $this->cost_per_unit = $data['cost_per_unit'] ?? null;
        $this->selling_price = $data['selling_price'] ?? null;
        $this->gross_sales = $data['gross_sales'] ?? null;
        $this->sales_discount_rate = $data['sales_discount_rate'] ?? null;
        $this->sales_discount_amount = $data['sales_discount_amount'] ?? null;
        $this->net_sales = $data['net_sales'] ?? null;
        $this->sales_tax = $data['sales_tax'] ?? null;
        $this->output_vat = $data['output_vat'] ?? null;
        $this->taxable_sales_amount = $data['taxable_sales_amount'] ?? null;
        $this->selling_price_per_unit = $data['selling_price_per_unit'] ?? null;
        $this->weighted_average_cost = $data['weighted_average_cost'] ?? null;
        $this->asset_value_wa = $data['asset_value_wa'] ?? null;
        $this->fifo_cost = $data['fifo_cost'] ?? null;
        $this->cost_of_goods_sold = $data['cost_of_goods_sold'] ?? null;
        $this->asset_value_fifo = $data['asset_value_fifo'] ?? null;
        $this->gross_margin = $data['gross_margin'] ?? null;
    }

    public static function all()
    {
        global $connection;

        if (static::$cache !== null) {
            return static::$cache;
        }

        // Modify the SQL query to join with both vendors and items tables
        $stmt = $connection->prepare('
            SELECT 
                inventory_valuation.*, 
                vendors.vendor_name, 
                items.item_name 
            FROM inventory_valuation
            LEFT JOIN vendors ON inventory_valuation.name = vendors.id
            LEFT JOIN items ON inventory_valuation.item_id = items.id
        ');

        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();
        static::$cache = array_map(function ($item) {
            return new InventoryValuation($item);
        }, $result);

        return static::$cache;
    }

    public static function getAllGroupedByItem()
    {
        $allInventory = self::all();
        $groupedInventory = [];

        foreach ($allInventory as $inventory) {
            $itemId = $inventory->item_id;
            if (!isset($groupedInventory[$itemId])) {
                $groupedInventory[$itemId] = [
                    'item_name' => $inventory->item_name,
                    'entries' => [],
                    'total_qty_purchased' => 0,
                    'total_qty_sold' => 0,
                    'total_qty_on_hand' => 0,
                    'total_cost' => 0,
                    'total_asset_value_wa' => 0
                ];
            }
            $groupedInventory[$itemId]['entries'][] = $inventory;
            $groupedInventory[$itemId]['total_qty_purchased'] += $inventory->qty_purchased;
            $groupedInventory[$itemId]['total_qty_sold'] += $inventory->qty_sold;
            $groupedInventory[$itemId]['total_qty_on_hand'] += $inventory->qty_on_hand;
            $groupedInventory[$itemId]['total_cost'] += $inventory->total_cost;
            $groupedInventory[$itemId]['total_asset_value_wa'] += $inventory->asset_value_wa;
        }

        return $groupedInventory;
    }
}
