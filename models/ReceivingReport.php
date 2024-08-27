<?php

require_once __DIR__ . '/../_init.php';

class ReceivingReport
{
    //header
    public $id;
    public $receive_account_id;
    public $po_id;
    public $receive_no;
    public $vendor_id;
    public $vendor_name;
    public $location;
    public $terms;
    public $receive_date;
    public $receive_due_date;
    public $memo;
    public $gross_amount;
    public $discount_amount;
    public $net_amount;
    public $input_vat;
    public $vatable;
    public $zero_rated;
    public $vat_exempt;
    public $total_amount;
    public $receive_status;
    public $po_no;

    //details
    public $receive_id;
    public $cost_center_id;
    public $item;
    public $description;
    public $unit;
    public $quantity;
    public $cost;
    public $amount;
    public $discount_percentage;
    public $discount;
    public $net;
    public $input_vat_percentage;
    public $vatable_amount;
    public $vat_amount;
    public $print_status;
    public $details;

    public static $cache = null;

    public function __construct($formData)
    {
        $this->id = $formData['id'] ?? null;
        $this->receive_account_id = $formData['receive_account_id'] ?? null;
        $this->po_id = $formData['po_id'] ?? null;
        $this->receive_no = $formData['receive_no'] ?? null;
        $this->vendor_id = $formData['vendor_id'] ?? null;
        $this->vendor_name = $formData['vendor_name'] ?? null;
        $this->location = $formData['location'] ?? null;
        $this->terms = $formData['terms'] ?? null;
        $this->receive_date = $formData['receive_date'] ?? null;
        $this->receive_due_date = $formData['receive_due_date'] ?? null;
        $this->memo = $formData['memo'] ?? null;
        $this->gross_amount = $formData['gross_amount'] ?? null;
        $this->discount_amount = $formData['discount_amount'] ?? null;
        $this->net_amount = $formData['net_amount'] ?? null;
        $this->input_vat = $formData['input_vat'] ?? null;
        $this->vatable = $formData['vatable'] ?? null;
        $this->zero_rated = $formData['zero_rated'] ?? null;
        $this->vat_exempt = $formData['vat_exempt'] ?? null;
        $this->total_amount = $formData['total_amount'] ?? null;
        $this->receive_status = $formData['receive_status'] ?? null;
        $this->receive_id = $formData['receive_id'] ?? null;
        $this->cost_center_id = $formData['cost_center_id'] ?? null;
        $this->item = $formData['item'] ?? null;
        $this->description = $formData['description'] ?? null;
        $this->unit = $formData['unit'] ?? null;
        $this->quantity = $formData['quantity'] ?? null;
        $this->cost = $formData['cost'] ?? null;
        $this->amount = $formData['amount'] ?? null;
        $this->discount_percentage = $formData['discount_percentage'] ?? null;
        $this->discount = $formData['discount'] ?? null;
        $this->net = $formData['net'] ?? null;
        $this->input_vat_percentage = $formData['input_vat_percentage'] ?? null;
        $this->vatable_amount = $formData['vatable_amount'] ?? null;
        $this->vat_amount = $formData['vat_amount'] ?? null;
        $this->print_status = $formData['print_status'] ?? null;
        $this->po_no = $formData['po_no'] ?? null;

        $this->details = [];

        if (isset($formData['details'])) {
            foreach ($formData['details'] as $detail) {
                // Push each detail to the details array
                $this->details[] = $detail;
            }
        }
    }

    public static function find($id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                rr.*,
                po.po_no,
                v.vendor_name AS vendor_name,
                coa.account_description AS account_name
            FROM receive_items rr
            LEFT JOIN vendors v ON rr.vendor_id = v.id
            LEFT JOIN receive_item_details rrd ON rr.id = rrd.receive_id
            LEFT JOIN purchase_order po ON rrd.po_id = po.id
            LEFT JOIN chart_of_account coa ON rr.receive_account_id = coa.id
            WHERE rr.id = :id
        ');

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $receiveItemsData = $stmt->fetch();

        if (!$receiveItemsData) {
            return null;
        }

        $receiveItemsData['details'] = self::getReceiveDetails($id);

        return new ReceivingReport($receiveItemsData);
    }

    public static function getReceiveDetails($receive_id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                rrd.*,
                cc.particular,
                po.date,
                po.delivery_date,
                pod.qty,
                pod.received_qty,
                po.po_status,
                po.po_no,
                i.item_name,
                i.item_code,
                i.item_type,
                i.item_reorder_point,
                i.item_sales_description,
                i.item_selling_price,
                i.item_purchase_description,
                i.item_cost_price,
                i.item_quantity,
                um.name AS uom_name
            FROM receive_item_details rrd
            LEFT JOIN items i ON rrd.item_id = i.id
            LEFT JOIN uom um ON i.item_uom_id = um.id
            LEFT JOIN purchase_order po ON rrd.po_id = po.id
            LEFT JOIN cost_center cc ON rrd.cost_center_id = cc.id
            LEFT JOIN purchase_order_details pod ON rrd.po_id = pod.po_id AND rrd.item_id = pod.item_id
            WHERE rrd.receive_id = :receive_id
        ');

        $stmt->bindParam(':receive_id', $receive_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $details = [];
        while ($row = $stmt->fetch()) {
            $details[] = $row; // Directly use the fetched row as it is already formatted correctly
        }

        return $details;
    }

    // Adding in database, Receiving Report
    public static function add($receive_account_id, $receive_no, $vendor_id, $location, $terms, $receive_date, $receive_due_date, $memo, $gross_amount, $discount_amount, $net_amount, $input_vat, $vatable, $zero_rated, $vat_exempt, $total_amount, $details)
    {
        global $connection;

        try {
            $stmt = $connection->prepare("
            INSERT INTO receive_items (
                receive_account_id, receive_no, vendor_id, location, terms,
                receive_date, receive_due_date, memo, gross_amount,
                discount_amount, net_amount, input_vat, vatable,
                zero_rated, vat_exempt, total_amount
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ");

            $result = $stmt->execute([
                $receive_account_id,
                $receive_no,
                $vendor_id,
                $location,
                $terms,
                $receive_date,
                $receive_due_date,
                $memo,
                $gross_amount,
                $discount_amount,
                $net_amount,
                $input_vat,
                $vatable,
                $zero_rated,
                $vat_exempt,
                $total_amount
            ]);

            if (!$result) {
                error_log("SQL Error in ReceivingReport::add: " . implode(", ", $stmt->errorInfo()));
                throw new Exception("Failed to insert receiving report.");
            }
        } catch (PDOException $e) {
            error_log("PDO Exception in ReceivingReport::add: " . $e->getMessage());
            throw new Exception("Database error while inserting receiving report.");
        }
    }

    // Adding Details
    public static function addItem($transaction_id, $po_id, $item_id, $cost_center_id, $quantity, $cost, $amount, $discount_percentage, $discount_amount, $net_amount_before_input_vat, $net_amount, $input_vat_percentage, $input_vat_amount)
    {
        global $connection;

        try {
            $stmt = $connection->prepare("
        INSERT INTO receive_item_details (
            receive_id, po_id, item_id, cost_center_id, quantity,
            cost, amount, discount_percentage, discount,
            net_amount_before_input_vat, net_amount,
            input_vat_percentage, input_vat_amount
        ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)
    ");

            $params = [
                $transaction_id,
                $po_id,
                $item_id,
                $cost_center_id,
                $quantity,
                $cost,
                $amount,
                $discount_percentage,
                $discount_amount,
                $net_amount_before_input_vat,
                $net_amount,
                $input_vat_percentage,
                $input_vat_amount
            ];

            error_log("Executing SQL with params: " . print_r($params, true));

            $result = $stmt->execute($params);

            if (!$result) {
                error_log("SQL Error in ReceivingReport::addItem: " . implode(", ", $stmt->errorInfo()));
                throw new Exception("Failed to insert receive item detail. SQL Error: " . implode(", ", $stmt->errorInfo()));
            }
        } catch (PDOException $e) {
            error_log("PDO Exception in ReceivingReport::addItem: " . $e->getMessage());
            throw new Exception("Database error while inserting receive item detail: " . $e->getMessage());
        }
    }

    public static function all()
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                ri.*, 
                v.vendor_name 
            FROM receive_items ri
            INNER JOIN vendors v ON ri.vendor_id = v.id
        ');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $receiveItems = [];
        while ($row = $stmt->fetch()) {
            $receiveItem = [
                'id' => $row['id'],
                'receive_no' => $row['receive_no'],
                'receive_date' => $row['receive_date'],
                'vendor_name' => $row['vendor_name'],
                'total_amount' => $row['total_amount'],
                'receive_status' => $row['receive_status']
            ];
            $receiveItems[] = new ReceivingReport($receiveItem);
        }

        return $receiveItems;
    }

    // Get Last Inserted Transaction ID
    public static function getLastTransactionId()
    {
        global $connection;

        $stmt = $connection->query("SELECT MAX(id) AS last_transaction_id FROM receive_items");
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();

        if ($result && isset($result['last_transaction_id'])) {
            return $result['last_transaction_id'];
        }

        return null;
    }

    //Update Item Quantity + in items
    public static function updateItemQuantity($item_id, $quantity)
    {
        global $connection;

        try {
            $stmt = $connection->prepare("
                UPDATE items 
                SET item_quantity = item_quantity + :quantity 
                WHERE id = :item_id
            ");

            $result = $stmt->execute([
                ':quantity' => $quantity,
                ':item_id' => $item_id
            ]);

            if (!$result) {
                error_log("SQL Error in ReceivingReport::updateItemQuantity: " . implode(", ", $stmt->errorInfo()));
                throw new Exception("Failed to update item quantity.");
            }
        } catch (PDOException $e) {
            error_log("PDO Exception in ReceivingReport::updateItemQuantity: " . $e->getMessage());
            throw new Exception("Database error while updating item quantity.");
        }
    }

    // Update Purchase Order Received Quantity
    public static function updatePurchaseOrderDetails($po_id, $item_id, $received_qty, $receive_id)
    {
        global $connection;

        try {
            $connection->beginTransaction();

            // First, get the current received_qty from purchase_order_details
            $stmt = $connection->prepare("
            SELECT received_qty 
            FROM purchase_order_details
            WHERE po_id = :po_id AND item_id = :item_id
        ");
            $stmt->execute([
                ':po_id' => $po_id,
                ':item_id' => $item_id
            ]);
            $current_received_qty = $stmt->fetchColumn();

            // Update purchase_order_details
            $stmt = $connection->prepare("
            UPDATE purchase_order_details
            SET received_qty = received_qty + :received_qty,
                balance_qty = qty - :received_qty
            WHERE po_id = :po_id AND item_id = :item_id
        ");

            $result = $stmt->execute([
                ':received_qty' => $received_qty,
                ':po_id' => $po_id,
                ':item_id' => $item_id
            ]);

            if (!$result) {
                throw new Exception("Failed to update purchase order details.");
            }

            // Update receive_item_details with last_received_qty
            $stmt = $connection->prepare("
            UPDATE receive_item_details
            SET last_received_qty = :last_received_qty
            WHERE receive_id = :receive_id AND item_id = :item_id
        ");

            $result = $stmt->execute([
                ':last_received_qty' => $current_received_qty,
                ':receive_id' => $receive_id,
                ':item_id' => $item_id
            ]);

            if (!$result) {
                throw new Exception("Failed to update receive item details.");
            }

            // Check if all items for this PO are fully received
            $stmt = $connection->prepare("
            SELECT COUNT(*) as total_items,
                   SUM(CASE WHEN qty = received_qty THEN 1 ELSE 0 END) as fully_received_items
            FROM purchase_order_details
            WHERE po_id = :po_id
        ");
            $stmt->execute([':po_id' => $po_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            // Determine new status
            $new_status = ($result['total_items'] == $result['fully_received_items']) ? 1 : 2;

            // Update PO status
            $stmt = $connection->prepare("
            UPDATE purchase_order
            SET po_status = :status
            WHERE id = :po_id
        ");
            $stmt->execute([
                ':status' => $new_status,
                ':po_id' => $po_id
            ]);

            // Update receive_items status
            $stmt = $connection->prepare("
            UPDATE receive_items
            SET receive_status = :status
            WHERE id = :receive_id
        ");
            $stmt->execute([
                ':status' => $new_status,
                ':receive_id' => $receive_id
            ]);

            $connection->commit();
        } catch (Exception $e) {
            $connection->rollBack();
            error_log("Error in ReceivingReport::updatePurchaseOrderDetails: " . $e->getMessage());
            throw new Exception("Database error while updating purchase order and receive items details.");
        }
    }

    // GET LAST RR_NO 
    public static function getLastRRNo()
    {
        global $connection;

        // Prepare and execute the query
        $stmt = $connection->prepare("SELECT receive_no FROM receive_items ORDER BY id DESC LIMIT 1");
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();

        // Check if there is a result and fetch the latest AP voucher number
        if ($result) {
            $latestNo = $result['receive_no'];
        } else {
            $latestNo = null;
        }

        // Extract the numeric part and increment it
        if ($latestNo) {
            $numericPart = intval(substr($latestNo, 2));
            $newNo = $numericPart + 1;
        } else {
            $newNo = 1;
        }

        // Format the new number with leading zeros
        $newRRNo = 'RR' . str_pad($newNo, 9, '0', STR_PAD_LEFT);

        return $newRRNo;
    }

}


