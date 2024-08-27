<?php

require_once __DIR__ . '/../_init.php';


class PurchaseOrder
{

    // Define properties to match your form fields
    public $id;
    public $po_id;
    public $po_no;
    public $date;
    public $po_date;
    public $delivery_date;
    public $vendor_id;
    public $vendor_name;
    public $vendor_address;
    public $tin;
    public $terms;
    public $gross_amount;
    public $discount_amount;
    public $discount_name;
    public $discount_rate;
    public $net_amount_due;
    public $input_vat_amount;
    public $po_input_vat;
    public $vatable_amount;
    public $zero_rated_amount;
    public $vat_exempt;
    public $total_amount;
    public $memo;
    public $po_status;
    public $status;
    public $po_account_id;
    public $cost_center_id;
    public $item;
    public $description;
    public $unit;
    public $qty;
    public $quantity;
    public $received_qty;
    public $cost;
    public $amount;
    public $discount_percentage;
    public $discount;
    public $net;
    public $tax_amount;
    public $tax_type;
    public $vat;
    public $input_vat_percentage;
    public $purchase_order_detail_id;
    public $cost_center_name;
    public $item_id;
    public $pr_no;
    public $print_status;



    public static $cache = null;


    // Define properties for purchase order details
    public $details;
    // Constructor to initialize the object with form data
    public function __construct($formData)
    {
        $this->id = $formData['id'] ?? null;
        $this->po_id = $formData['po_id'] ?? null;
        $this->po_no = $formData['po_no'] ?? null;
        $this->date = $formData['date'] ?? null;
        $this->po_date = $formData['po_date'] ?? null;
        $this->delivery_date = $formData['delivery_date'] ?? null;
        $this->vendor_id = $formData['vendor_id'] ?? null;
        $this->vendor_name = $formData['vendor_name'] ?? null;
        $this->vendor_address = $formData['vendor_address'] ?? null;
        $this->tin = $formData['tin'] ?? null;
        $this->terms = $formData['terms'] ?? null;
        $this->gross_amount = $formData['gross_amount'] ?? null;
        $this->discount_amount = $formData['discount_amount'] ?? null;
        $this->net_amount_due = $formData['po_net_amount'] ?? null; // changed from 'net_amount_due'
        $this->po_input_vat = $formData['po_input_vat'] ?? null;
        $this->vatable_amount = $formData['vatable'] ?? null; // changed from 'vatable_amount'
        $this->zero_rated_amount = $formData['zero_rated'] ?? null; // changed from 'zero_rated_amount'
        $this->vat_exempt = $formData['vat_exempt'] ?? null; // changed from 'zero_rated_amount'
        $this->total_amount = $formData['total_amount'] ?? null;
        $this->memo = $formData['memo'] ?? null;
        $this->po_status = $formData['po_status'] ?? null;
        $this->status = $formData['status'] ?? null;
        $this->po_account_id = $formData['po_account_id'] ?? null;
        $this->cost_center_id = $formData['cost_center_id'] ?? null;
        $this->item = $formData['item'] ?? null;
        $this->description = $formData['description'] ?? null;
        $this->unit = $formData['unit'] ?? null;
        $this->qty = $formData['qty'] ?? null;
        $this->quantity = $formData['quantity'] ?? null;
        $this->received_qty = $formData['received_qty'] ?? null;
        $this->cost = $formData['cost'] ?? null;
        $this->amount = $formData['amount'] ?? null;
        $this->discount_percentage = $formData['discount_percentage'] ?? null;
        $this->discount = $formData['discount'] ?? null;
        $this->net = $formData['net'] ?? null;
        $this->tax_amount = $formData['tax_amount'] ?? null;
        $this->tax_type = $formData['tax_type'] ?? null;
        $this->vat = $formData['vat'] ?? null;
        $this->input_vat_percentage = $formData['input_vat_percentage'] ?? null;
        $this->purchase_order_detail_id = $formData['purchase_order_detail_id'] ?? null;
        $this->cost_center_name = $formData['cost_center_name'] ?? null;
        $this->item_id = $formData['item_id'] ?? null;
        $this->pr_no = $formData['pr_no'] ?? null;
        $this->print_status = $formData['print_status'] ?? null;


        // Initialize details as an empty array
        $this->details = [];

        // Populate other properties as before...

        // Optionally, you can populate details if provided in $formData
        if (isset($formData['details'])) {
            foreach ($formData['details'] as $detail) {
                // Push each detail to the details array
                $this->details[] = $detail;
            }
        }
    }

    // Static method to find a purchase order by ID
    public static function find($id)
    {
        global $connection;

        $stmt = $connection->prepare('
        SELECT 
            po.id AS po_id,
            po.po_no,
            po.date AS po_date,
            po.delivery_date,
            po.vendor_id,
            v.vendor_name,
            v.vendor_address,
            v.tin,
            v.account_number,
            po.terms,
            po.gross_amount,
            po.discount_amount,
            po.net_amount AS po_net_amount,
            po.input_vat AS po_input_vat,
            po.vatable,
            po.zero_rated,
            po.vat_exempt,
            po.total_amount,
            po.memo,
            po.po_status,
            po.status,
            po.print_status,
            po.created_at AS po_created_at,
            pod.id AS pod_id,
            pod.pr_no,
            pod.item_id,
            pod.cost_center_id,
            pod.qty,
            pod.cost,
            pod.amount,
            pod.discount_percentage,
            pod.last_ordered_qty,
            d.discount_name,
            d.discount_rate,
            pod.discount,
            pod.net_amount AS pod_net_amount,
            pod.taxable_amount,
            pod.input_vat_percentage,
            te.input_vat_name,
            te.input_vat_rate,
            pod.input_vat AS pod_input_vat,
            pod.created_at AS pod_created_at,
            cc.particular AS cost_center_id,
            i.item_name,
            i.item_purchase_description,
            uom.name AS uom_name,
            pr.pr_no AS related_pr_no,
            prd.quantity AS related_quantity
        FROM purchase_order po
        INNER JOIN purchase_order_details pod ON po.id = pod.po_id
        INNER JOIN vendors v ON po.vendor_id = v.id
        LEFT JOIN discount d ON pod.discount_percentage = d.id
        LEFT JOIN input_vat te ON pod.input_vat_percentage = te.id
        INNER JOIN items i ON pod.item_id = i.id
        LEFT JOIN uom ON i.item_uom_id = uom.id
        LEFT JOIN cost_center cc ON pod.cost_center_id = cc.id
        LEFT JOIN purchase_request pr ON CONVERT(pod.pr_no USING utf8mb4) COLLATE utf8mb4_unicode_ci = CONVERT(pr.pr_no USING utf8mb4) COLLATE utf8mb4_unicode_ci
        LEFT JOIN purchase_request_details prd ON prd.pr_id = pr.id AND prd.item_id = pod.item_id
        WHERE po.id = :id
    ');

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (empty($result)) {
            return null;
        }

        // Initialize purchase order with the first row
        $firstRow = $result[0];
        $purchaseOrder = [
            'po_id' => $firstRow['po_id'],
            'po_no' => $firstRow['po_no'],
            'po_date' => $firstRow['po_date'],
            'delivery_date' => $firstRow['delivery_date'],
            'vendor_id' => $firstRow['vendor_id'],
            'vendor_name' => $firstRow['vendor_name'],
            'vendor_address' => $firstRow['vendor_address'],
            'tin' => $firstRow['tin'],
            'terms' => $firstRow['terms'],
            'gross_amount' => $firstRow['gross_amount'],
            'discount_amount' => $firstRow['discount_amount'],
            'po_net_amount' => $firstRow['po_net_amount'],
            'po_input_vat' => $firstRow['po_input_vat'],
            'vatable' => $firstRow['vatable'],
            'zero_rated' => $firstRow['zero_rated'],
            'vat_exempt' => $firstRow['vat_exempt'],
            'total_amount' => $firstRow['total_amount'],
            'memo' => $firstRow['memo'],
            'po_status' => $firstRow['po_status'],
            'status' => $firstRow['status'],
            'print_status' => $firstRow['print_status'],
            'created_at' => $firstRow['po_created_at'],
            'details' => []
        ];

        // Populate details with PR and PRD info
        foreach ($result as $row) {
            $purchaseOrder['details'][] = [
                'pod_id' => $row['pod_id'],
                'pr_no' => $row['pr_no'],
                'item_id' => $row['item_id'],
                'cost_center_id' => $row['cost_center_id'],
                'item_name' => $row['item_name'],
                'item_purchase_description' => $row['item_purchase_description'],
                'uom_name' => $row['uom_name'],
                'qty' => $row['qty'],
                'cost' => $row['cost'],
                'amount' => $row['amount'],
                'discount_percentage' => $row['discount_percentage'],
                'discount_name' => $row['discount_name'],
                'discount_rate' => $row['discount_rate'],
                'discount' => $row['discount'],
                'net_amount' => $row['pod_net_amount'],
                'taxable_amount' => $row['taxable_amount'],
                'input_vat_percentage' => $row['input_vat_percentage'],
                'input_vat' => $row['pod_input_vat'],
                'input_vat_name' => $row['input_vat_name'],
                'input_vat_rate' => $row['input_vat_rate'],
                'created_at' => $row['pod_created_at'],
                'related_pr_no' => $row['related_pr_no'],
                'related_quantity' => $row['related_quantity'],
                'last_ordered_qty' => $row['last_ordered_qty']
            ];
        }

        return new PurchaseOrder($purchaseOrder);
    }




    public static function add($po_no, $po_date, $delivery_date, $vendor_id, $terms, $gross_amount, $discount_amount, $net_amount_due, $input_vat_amount, $vatable_amount, $zero_rated_amount, $vat_exempt_amount, $total_amount_due, $memo, $items)
    {
        global $connection;

        try {
            // Start a database transaction to ensure data integrity
            $connection->beginTransaction();

            $stmt = $connection->prepare("
                INSERT INTO purchase_order (
                    po_no, date, delivery_date, vendor_id, terms, gross_amount,
                    discount_amount, net_amount, input_vat, vatable, zero_rated,
                    vat_exempt, total_amount, memo
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $po_no,
                $po_date,
                $delivery_date,
                $vendor_id,
                $terms,
                $gross_amount,
                $discount_amount,
                $net_amount_due,
                $input_vat_amount,
                $vatable_amount,
                $zero_rated_amount,
                $vat_exempt_amount,
                $total_amount_due,
                $memo
            ]);

            // Get the ID of the newly inserted purchase order
            $po_id = $connection->lastInsertId();

            // Process each item in the purchase order
            foreach ($items as $item) {
                // Add the individual purchase order item to the database
                self::addItem(
                    $po_id,
                    $item['pr_no'],
                    $item['item_id'],
                    $item['cost_center_id'],
                    $item['qty'],
                    $item['cost'],
                    $item['amount'],
                    $item['discount_percentage'],
                    $item['discount'],
                    $item['net_amount'],
                    $item['taxable_amount'],
                    $item['input_vat_percentage'],
                    $item['input_vat'],
                    $item['discount_account_id'],
                    $item['input_vat_account_id']
                );

                $stmt = $connection->prepare("
                SELECT pr.id as pr_id, prd.id as prd_id, prd.quantity, prd.balance_quantity
                FROM purchase_request pr
                JOIN purchase_request_details prd ON pr.id = prd.pr_id
                WHERE pr.pr_no = :pr_no AND prd.item_id = :item_id
            ");
                $stmt = $connection->prepare("
                SELECT pr.id as pr_id, prd.id as prd_id, prd.quantity, prd.balance_quantity
                FROM purchase_request pr
                JOIN purchase_request_details prd ON pr.id = prd.pr_id
                WHERE pr.pr_no = :pr_no AND prd.item_id = :item_id
            ");
                $stmt->execute([':pr_no' => $item['pr_no'], ':item_id' => $item['item_id']]);
                $pr_data = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($pr_data) {
                    $new_balance = $pr_data['balance_quantity'] - $item['qty'];
                    $status = 2; // Default status

                    if ($item['qty'] == $pr_data['quantity'] || $new_balance == 0) {
                        $status = 1;
                    }

                    // Update purchase_request
                    $stmt = $connection->prepare("
                    UPDATE purchase_request
                    SET status = :status
                    WHERE id = :pr_id
                ");
                    $stmt->execute([':status' => $status, ':pr_id' => $pr_data['pr_id']]);

                    // Update purchase_request_details
                    $stmt = $connection->prepare("
                    UPDATE purchase_request_details
                    SET ordered_quantity = ordered_quantity + :quantity,
                        balance_quantity = :new_balance,
                        status = :status
                    WHERE id = :prd_id
                ");
                    $stmt->execute([
                        ':quantity' => $item['qty'],
                        ':new_balance' => $new_balance,
                        ':status' => $status,
                        ':prd_id' => $pr_data['prd_id']
                    ]);
                }
            }

            $connection->commit();
            return true;
        } catch (Exception $e) {
            $connection->rollBack();
            error_log('Error in PurchaseOrder::add: ' . $e->getMessage());
            throw $e;
        }
    }

    public static function addItem($po_id, $pr_no, $item_id, $cost_center_id, $quantity, $cost, $amount, $discount_percentage, $discount_amount, $net_amount_before_input_vat, $net_amount, $input_vat_percentage, $input_vat_amount, $discount_account_id, $input_vat_account_id)
    {
        global $connection;

        // Default value for last_ordered_qty
        $last_ordered_qty = 0;

        // Fetch the balance_qty from purchase_request_details
        $stmt = $connection->prepare("
        SELECT balance_qty
        FROM purchase_order_details 
        WHERE pr_no = :pr_no AND item_id = :item_id
        LIMIT 1
    ");

        $stmt->execute([
            ':pr_no' => $pr_no,
            ':item_id' => $item_id
        ]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // If balance_quantity exists, assign it to last_ordered_qty
        if ($result && isset($result['balance_qty'])) {
            $last_ordered_qty = $result['balance_qty'];
        }

        // Insert the item into purchase_order_details with the calculated last_ordered_qty
        $stmt = $connection->prepare("
        INSERT INTO purchase_order_details (
            po_id, pr_no, item_id, cost_center_id, qty, cost, amount, discount_percentage,
            discount, net_amount, taxable_amount, input_vat_percentage, input_vat, balance_qty, last_ordered_qty, discount_type_id, tax_type_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

        $stmt->execute([
            $po_id,
            $pr_no,
            $item_id,
            $cost_center_id,
            $quantity ?? 0,
            $cost ?? 0.00,
            $amount ?? 0.00,
            $discount_percentage ?? 0.00,
            $discount_amount ?? 0.00,
            $net_amount_before_input_vat ?? 0.00,
            $net_amount ?? 0.00,
            $input_vat_percentage ?? 0.00,
            $input_vat_amount ?? 0.00,
            $quantity ?? 0,
            $last_ordered_qty ?? 0, // Store the last_ordered_qty here
            $discount_account_id ?? 0,
            $input_vat_account_id ?? 0
        ]);
    }


    public static function findByPoNo($po_no)
    {
        global $connection;

        $stmt = $connection->prepare('SELECT * FROM purchase_order WHERE po_no = :po_no');
        $stmt->bindParam("po_no", $po_no);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new PurchaseOrder($result[0]);
        }

        return null;
    }

    public static function getPurchaseOrderByVendorId($vendor_id)
    {
        global $connection;

        // Prepare the base SQL query
        $sql = '
                SELECT 
                po.id AS po_id,
                po.po_no,
                po.po_account_id,
                po.date,
                po.delivery_date,
                pod.id AS purchase_order_detail_id,
                pod.item_id,
                cc.id AS cost_center_id,
                cc.particular as cost_center_name,
                i.item_name AS item,
                i.item_purchase_description AS description,
                uom.name AS unit,
                pod.balance_qty AS quantity,
                pod.received_qty as received_qty,
                pod.cost,
                pod.amount,
                pod.qty AS qty,
                pod.discount_percentage AS discount_percentage,
                pod.discount AS discount,
                pod.net_amount AS net,
                pod.input_vat AS tax_amount,
                pod.input_vat_percentage as input_vat_percentage,
                pod.input_vat AS vat
                FROM 
                    purchase_order po
                INNER JOIN 
                    purchase_order_details pod ON po.id = pod.po_id
                INNER JOIN
                    cost_center cc ON pod.cost_center_id = cc.id
                INNER JOIN 
                    items i ON pod.item_id = i.id
                LEFT JOIN 
                    uom ON i.item_uom_id = uom.id
                LEFT JOIN 
                    discount d ON pod.discount_percentage = d.id
                LEFT JOIN 
                    input_vat iv ON pod.input_vat_percentage = iv.id
                WHERE 
                    (po.po_status = 0 OR po.po_status = 2) AND po.vendor_id = :vendor_id
                ORDER BY 
                po.id DESC';

        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':vendor_id', $vendor_id, PDO::PARAM_INT);

        $stmt->execute();

        $purchases = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $purchases[] = new PurchaseOrder($row);
        }

        return $purchases;
    }

    // Method to get the last transaction_id
    public static function getLastTransactionId()
    {
        global $connection;

        $stmt = $connection->query("SELECT MAX(id) AS last_transaction_id FROM purchase_order");
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();

        if ($result && isset($result['last_transaction_id'])) {
            return $result['last_transaction_id'];
        }

        return null;
    }


    // Static method to get all purchase orders with details
    public static function all()
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                po.*, 
                v.vendor_name 
            FROM purchase_order po
            INNER JOIN vendors v ON po.vendor_id = v.id
        ');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $orders = [];
        while ($row = $stmt->fetch()) {
            $order = [
                'id' => $row['id'],
                'po_no' => $row['po_no'],
                'date' => $row['date'],
                'vendor_name' => $row['vendor_name'],
                'total_amount' => $row['total_amount'],
                'po_status' => $row['po_status']
            ];
            $orders[] = new PurchaseOrder($order);
        }

        return $orders;
    }

    public static function update(
        $po_id,
        $po_no,
        $date,
        $delivery_date,
        $vendor_id,
        $terms,
        $gross_amount,
        $discount_amount,
        $net_amount_due,
        $input_vat_amount,
        $vatable_amount,
        $zero_rated_amount,
        $vat_exempt_amount,
        $total_amount_due,
        $memo
    ) {
        global $connection;

        $stmt = $connection->prepare("
        UPDATE purchase_order
        SET po_no = ?, date = ?, delivery_date = ?, vendor_id = ?, terms = ?,
        gross_amount = ?, discount_amount = ?, net_amount = ?, input_vat = ?,
        vatable = ?, zero_rated = ?, vat_exempt = ?, total_amount = ?, memo = ?
        WHERE id = ?
        ");

        $stmt->execute([
            $po_no,
            $date,
            $delivery_date,
            $vendor_id,
            $terms,
            $gross_amount,
            $discount_amount,
            $net_amount_due,
            $input_vat_amount,
            $vatable_amount,
            $zero_rated_amount,
            $vat_exempt_amount,
            $total_amount_due,
            $memo,
            $po_id
        ]);
    }

    public static function updateDetails($po_id, $items)
    {
        global $connection;

        // First, delete existing details for this PO
        $stmt = $connection->prepare("DELETE FROM purchase_order_details WHERE po_id = ?");
        $stmt->execute([$po_id]);

        // Now insert the new details
        $stmt = $connection->prepare("
                INSERT INTO purchase_order_details 
                (po_id, item_id, qty, cost, amount, discount_percentage, discount, net_amount, taxable_amount, input_vat_percentage, input_vat)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

        foreach ($items as $item) {
            $stmt->execute([
                $po_id,
                $item['item_id'],
                $item['quantity'],
                $item['cost'],
                $item['amount'],
                $item['discount_percentage'],
                $item['discount_amount'],
                $item['net_amount'],
                $item['net_amount_before_input_vat'],
                $item['input_vat_percentage'],
                $item['input_vat_amount']
            ]);
        }
    }

    // GET LAST PO_NO 
    public static function getLastPoNo()
    {
        global $connection;

        // Prepare and execute the query
        $stmt = $connection->prepare("
            SELECT po_no 
            FROM purchase_order 
            WHERE po_no IS NOT NULL AND po_no <> '' 
            ORDER BY po_no DESC 
            LIMIT 1
        ");
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();

        // Extract the numeric part of the last purchase order number
        if ($result) {
            $latestNo = $result['po_no'];
            // Assuming the format is 'PO' followed by digits
            $numericPart = intval(substr($latestNo, 2)); // 'PO' is 2 characters
            $newNo = $numericPart + 1;
        } else {
            // If no valid purchase order number exists, start with 1
            $newNo = 1;
        }

        // Format the new number with leading zeros
        $newPoNo = 'PO' . str_pad($newNo, 9, '0', STR_PAD_LEFT);

        return $newPoNo;
    }

    public static function saveDraft(
        $po_date,
        $delivery_date,
        $vendor_id,
        $terms,
        $gross_amount,
        $discount_amount,
        $net_amount_due,
        $input_vat_amount,
        $vatable_amount,
        $zero_rated_amount,
        $vat_exempt_amount,
        $total_amount_due,
        $memo,
        $items
    ) {
        global $connection;

        try {
            $connection->beginTransaction();

            // Insert into purchase_order table with draft status
            $sql = "INSERT INTO purchase_order (
                date, delivery_date, vendor_id, terms, gross_amount,
                discount_amount, net_amount, input_vat, vatable, zero_rated,
                vat_exempt, total_amount, memo, po_status
            ) VALUES (
                :po_date, :delivery_date, :vendor_id, :terms, :gross_amount,
                :discount_amount, :net_amount_due, :input_vat_amount, :vatable_amount, :zero_rated_amount,
                :vat_exempt_amount, :total_amount_due, :memo, :po_status
            )";

            $stmt = $connection->prepare($sql);
            $stmt->bindParam(':po_date', $po_date);
            $stmt->bindParam(':delivery_date', $delivery_date);
            $stmt->bindParam(':vendor_id', $vendor_id);
            $stmt->bindParam(':terms', $terms);
            $stmt->bindParam(':gross_amount', $gross_amount);
            $stmt->bindParam(':discount_amount', $discount_amount);
            $stmt->bindParam(':net_amount_due', $net_amount_due);
            $stmt->bindParam(':input_vat_amount', $input_vat_amount);
            $stmt->bindParam(':vatable_amount', $vatable_amount);
            $stmt->bindParam(':zero_rated_amount', $zero_rated_amount);
            $stmt->bindParam(':vat_exempt_amount', $vat_exempt_amount);
            $stmt->bindParam(':total_amount_due', $total_amount_due);
            $stmt->bindParam(':memo', $memo);
            $stmt->bindValue(':po_status', 4, PDO::PARAM_INT); // Set status to 4 for draft

            $stmt->execute();

            $po_id = $connection->lastInsertId();

            // Insert purchase order items
            if (!empty($items)) {
                $itemSql = "INSERT INTO purchase_order_details (
                    po_id, item_id, cost_center_id, qty, cost, amount, discount_percentage,
                    discount, net_amount, taxable_amount, input_vat_percentage, input_vat
                ) VALUES (
                    :po_id, :item_id, :cost_center_id, :qty, :cost, :amount, :discount_percentage,
                    :discount, :net_amount, :taxable_amount, :input_vat_percentage, :input_vat
                )";

                $itemStmt = $connection->prepare($itemSql);

                foreach ($items as $item) {
                    $itemStmt->bindParam(':po_id', $po_id);
                    $itemStmt->bindParam(':item_id', $item['item_id']);
                    $itemStmt->bindParam(':cost_center_id', $item['cost_center_id']);
                    $itemStmt->bindParam(':qty', $item['qty']);
                    $itemStmt->bindParam(':cost', $item['cost']);
                    $itemStmt->bindParam(':amount', $item['amount']);
                    $itemStmt->bindParam(':discount_percentage', $item['discount_percentage']);
                    $itemStmt->bindParam(':discount', $item['discount']);
                    $itemStmt->bindParam(':net_amount', $item['net_amount']);
                    $itemStmt->bindParam(':taxable_amount', $item['taxable_amount']);
                    $itemStmt->bindParam(':input_vat_percentage', $item['input_vat_percentage']);
                    $itemStmt->bindParam(':input_vat', $item['input_vat']);
                    $itemStmt->execute();
                }
            }

            $connection->commit();
            return true;
        } catch (Exception $ex) {
            $connection->rollBack();
            error_log('Error in saveDraft: ' . $ex->getMessage());
            throw $ex;
        }
    }

    public static function getPurchaseRequestInfo($item_id)
{
    global $connection;

    // Query adjusted to avoid fetching rows where prd.pr_id = pr.id
    $sql = "SELECT pr.pr_no 
            FROM purchase_request_details prd 
            JOIN purchase_request pr ON prd.pr_id = pr.id 
            WHERE prd.item_id = :item_id 
            AND prd.status != 1"; // Filter to exclude rows where status is 0

    $stmt = $connection->prepare($sql);
    $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

    public static function getPRQuantity($pr_no, $item_id)
    {
        global $connection;

        try {
            $sql = "SELECT prd.balance_quantity 
                FROM purchase_request_details prd 
                WHERE prd.pr_id = (SELECT id FROM purchase_request WHERE pr_no = :pr_no)
                AND prd.item_id = :item_id";

            $stmt = $connection->prepare($sql);
            $stmt->bindParam(':pr_no', $pr_no, PDO::PARAM_STR);
            $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                return $stmt->fetchColumn();
            } else {
                error_log("SQL error: " . implode(", ", $stmt->errorInfo()));
                return null;
            }
        } catch (PDOException $pdoEx) {
            // Log PDO-specific errors
            error_log("PDOException: " . $pdoEx->getMessage());
            error_log("File: " . $pdoEx->getFile());
            error_log("Line: " . $pdoEx->getLine());

            // Return a null or error value
            return null;
        } catch (Exception $ex) {
            // Log general exceptions
            error_log("Exception: " . $ex->getMessage());
            error_log("File: " . $ex->getFile());
            error_log("Line: " . $ex->getLine());

            // Return a null or error value
            return null;
        }
    }
}
