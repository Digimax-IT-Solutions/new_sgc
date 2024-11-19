<?php
require_once __DIR__ . '/../_init.php';

class OfficialReceipt
{
    // Properties for or_payments
    public $id;
    public $ci_no;
    public $or_number;
    public $or_date;
    public $or_account_id;
    public $customer_po;
    public $so_no;
    public $rep;
    public $check_no; // Added from the `or_payments` table
    public $customer_id;
    public $customer_name;
    public $customer_tin;
    public $customer_email;
    public $shipping_address;
    public $billing_address;
    public $business_style;
    public $payment_method;
    public $location;
    public $memo;
    public $gross_amount;
    public $discount_amount;
    public $net_amount_due;
    public $vat_amount;
    public $vatable_amount;
    public $zero_rated_amount;
    public $vat_exempt_amount;
    public $tax_withheld_percentage;
    public $tax_withheld_amount;
    public $total_amount_due;
    public $or_status; // Updated from `invoice_status`
    public $status; // Updated from `status`
    public $print_status;
    // Properties for or_payment_details
    public $details = [];

    private static $cache = null;

    public function __construct($formData)
    {
        // Initialize properties for or_payments
        $this->id = $formData['id'] ?? null;
        $this->ci_no = $formData['ci_no'] ?? null;
        $this->or_number = $formData['or_number'] ?? null;
        $this->or_date = $formData['or_date'] ?? null;
        $this->or_account_id = $formData['or_account_id'] ?? null;
        $this->customer_po = $formData['customer_po'] ?? null;
        $this->so_no = $formData['so_no'] ?? null;
        $this->rep = $formData['rep'] ?? null;
        $this->check_no = $formData['check_no'] ?? null; // Added
        $this->customer_id = $formData['customer_id'] ?? null;
        $this->customer_name = $formData['customer_name'] ?? null;
        $this->customer_tin = $formData['customer_tin'] ?? null;
        $this->customer_email = $formData['customer_email'] ?? null;
        $this->shipping_address = $formData['shipping_address'] ?? null;
        $this->billing_address = $formData['billing_address'] ?? null;
        $this->business_style = $formData['business_style'] ?? null;
        $this->payment_method = $formData['payment_method'] ?? null;
        $this->location = $formData['location'] ?? null;
        $this->memo = $formData['memo'] ?? null;
        $this->gross_amount = $formData['gross_amount'] ?? null;
        $this->discount_amount = $formData['discount_amount'] ?? null;
        $this->net_amount_due = $formData['net_amount_due'] ?? null;
        $this->vat_amount = $formData['vat_amount'] ?? null;
        $this->vatable_amount = $formData['vatable_amount'] ?? null;
        $this->zero_rated_amount = $formData['zero_rated_amount'] ?? null;
        $this->vat_exempt_amount = $formData['vat_exempt_amount'] ?? null;
        $this->tax_withheld_percentage = $formData['tax_withheld_percentage'] ?? null;
        $this->tax_withheld_amount = $formData['tax_withheld_amount'] ?? null;
        $this->total_amount_due = $formData['total_amount_due'] ?? null;
        $this->or_status = $formData['or_status'] ?? null; // Updated
        $this->status = $formData['status'] ?? null; // Updated
        $this->print_status = $formData['print_status'] ?? null;

        // Initialize details as an empty array
        $this->details = [];

        // Populate details if provided in $formData
        if (isset($formData['details'])) {
            foreach ($formData['details'] as $detail) {
                // Add each detail to the details array
                $this->details[] = $detail;
            }
        }
    }

    public static function add(
        $ci_no,
        $or_number,
        $or_date,
        $or_account_id,
        $customer_po,
        $so_no,
        $check_no,
        $rep,
        $output_vat_ids,
        $payment_method,
        $location,
        $memo,
        $gross_amount,
        $total_discount_amount,
        $net_amount_due,
        $vat_amount,
        $vatable_amount,
        $zero_rated_amount,
        $vat_exempt_amount,
        $tax_withheld_percentage,
        $tax_withheld_amount,
        $tax_withheld_account_id,
        $total_amount_due,
        $items,
        $customer_id,
        $customer_name,
        $created_by
    ) {
        global $connection;

        try {
            // Start a database transaction to ensure data integrity
            $connection->beginTransaction();

            $transaction_type = "Cash Invoice";
            $or_status = 0; // Assuming 0 is the initial status

            // Prepare and execute SQL to check if the ci_no already exists in the database
            $checkStmt = $connection->prepare("SELECT COUNT(*) FROM or_payments WHERE ci_no = ?");
            $checkStmt->execute([$ci_no]);

            // Fetch the result of the check
            $exists = $checkStmt->fetchColumn();

            // If the ci_no exists, throw an exception or return a message
            if ($exists > 0) {
                throw new Exception("This OR has already been saved!.");
            } else {
                // Prepare and execute SQL to insert the main official receipt record
                $stmt = $connection->prepare("INSERT INTO or_payments (
                customer_id, location, customer_po, so_no, check_no, rep, or_number, ci_no, or_date, or_account_id,
                payment_method, memo, gross_amount, discount_amount, net_amount_due, vat_amount, vatable_amount,
                zero_rated_amount, vat_exempt_amount, tax_withheld_percentage,
                tax_withheld_amount, total_amount_due, or_status
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?, ?,
                ?, ?, ?, ?, ?, ?, ?
            )");

                $stmt->execute([
                    $customer_id,
                    $location,
                    $customer_po,
                    $so_no,
                    $check_no,
                    $rep,
                    $or_number,
                    $ci_no,
                    $or_date,
                    $or_account_id,
                    $payment_method,
                    $memo,
                    $gross_amount,
                    $total_discount_amount,
                    $net_amount_due,
                    $vat_amount,
                    $vatable_amount,
                    $zero_rated_amount,
                    $vat_exempt_amount,
                    $tax_withheld_percentage,
                    $tax_withheld_amount,
                    $total_amount_due,
                    $or_status
                ]);
            }

            $or_id = $connection->lastInsertId();

            // Log the main official receipt transaction in the audit trail
            self::logAuditTrail(
                $or_id,
                $transaction_type,
                $or_date,
                $or_number,
                $location,
                $customer_name,
                null,
                null,
                $or_account_id,
                $gross_amount,
                0.00,
                $created_by
            );

            // Log tax withheld transaction
            self::logAuditTrail(
                $or_id,
                $transaction_type,
                $or_date,
                $or_number,
                $location,
                $customer_name,
                null,
                null,
                $tax_withheld_account_id,
                $tax_withheld_amount,
                0.00,
                $created_by
            );

            // Log VAT transaction
            self::logAuditTrail(
                $or_id,
                $transaction_type,
                $or_date,
                $or_number,
                $location,
                $customer_name,
                null,
                null,
                $output_vat_ids,
                0.00,
                $vat_amount,
                $created_by
            );

            $qty_purchased = 0;
            $cost = 0.00;
            $total_cost = 0.00;
            $purchase_discount_rate = 0.00;
            $purchase_discount_per_item = 0.00;
            $purchase_discount_amount = 0.00;
            $net_amount = 0.00;
            $input_vat_rate = 0.00;
            $input_vat = 0.00;
            $taxable_purchased_amount = 0.00;
            $cost_per_unit = 0.00;


            // Process each item in the official receipt
            // Process each item in the official receipt
            foreach ($items as $item) {
                self::addInvoiceItems(
                    $or_id,
                    $item['item_id'],
                    $item['description'],
                    $item['quantity'],
                    $item['cost'],
                    $item['amount'],
                    $item['discount_percentage'],
                    $item['discount_amount'],
                    $item['net_amount_before_sales_tax'],
                    $item['net_amount'],
                    $item['sales_tax_percentage'],
                    $item['sales_tax_amount'],
                    $item['output_vat_id'],
                    $item['discount_account_id'],
                    $item['cogs_account_id'],
                    $item['income_account_id'],
                    $item['asset_account_id']
                );

                // Log discount for this item
                if (!empty($item['discount_account_id'])) {
                    self::logAuditTrail(
                        $or_id,
                        $transaction_type,
                        $or_date,
                        $or_number,
                        $location,
                        $customer_name,
                        $item['item_name'],
                        $item['quantity'],
                        $item['discount_account_id'],
                        $item['discount_amount'],
                        0.00,
                        $created_by
                    );
                }

                // Log income for this item
                if (!empty($item['income_account_id'])) {
                    self::logAuditTrail(
                        $or_id,
                        $transaction_type,
                        $or_date,
                        $or_number,
                        $location,
                        $customer_name,
                        $item['item_name'],
                        $item['quantity'],
                        $item['income_account_id'],
                        0.00,
                        ($item['amount'] - $item['sales_tax_amount']),
                        $created_by
                    );
                }

                // Log Cost of Goods Sold (COGS) for this item
                if (!empty($item['cogs_account_id'])) {
                    self::logAuditTrail(
                        $or_id,
                        $transaction_type,
                        $or_date,
                        $or_number,
                        $location,
                        $customer_name,
                        $item['item_name'],
                        $item['quantity'],
                        $item['cogs_account_id'],
                        $item['cost'] * $item['quantity'],
                        0.00,
                        $created_by
                    );
                }

                // Log asset transaction for this item
                if (!empty($item['asset_account_id'])) {
                    self::logAuditTrail(
                        $or_id,
                        $transaction_type,
                        $or_date,
                        $or_number,
                        $location,
                        $customer_name,
                        $item['item_name'],
                        $item['quantity'],
                        $item['asset_account_id'],
                        0.00,
                        $item['cost'] * $item['quantity'],
                        $created_by
                    );
                }

                // INVENTORY VALUATION: Check if item type is not "Service" before inserting
                if ($item['item_type'] !== 'Service') {
                    self::insert_inventory_valuation(
                        $transaction_type,
                        $or_id,
                        $ci_no,
                        $or_date,
                        $customer_id,
                        $item['item_id'],
                        $qty_purchased,
                        $item['quantity'],
                        $cost,
                        $total_cost,
                        $purchase_discount_rate,
                        $purchase_discount_per_item,
                        $purchase_discount_amount,
                        $net_amount,
                        $input_vat_rate,
                        $input_vat,
                        $taxable_purchased_amount,
                        $cost_per_unit,
                        $item['cost'],
                        $item['amount'],
                        $item['discount_percentage'],
                        $item['discount_amount'],
                        $item['net_amount_before_sales_tax'],
                        $item['sales_tax_percentage'],
                        $item['sales_tax_amount'],
                        $item['net_amount'],
                        $item['net_amount'] / $item['quantity']
                    );
                }
            }

            // Log the main official receipt transaction discount credit
            self::logAuditTrail(
                $or_id,
                $transaction_type,
                $or_date,
                $or_number,
                $location,
                $customer_name,
                null,
                null,
                $or_account_id,
                0.00,
                $total_discount_amount,
                $created_by
            );

            // Log the main official receipt transaction tax withheld credit
            self::logAuditTrail(
                $or_id,
                $transaction_type,
                $or_date,
                $or_number,
                $location,
                $customer_name,
                null,
                null,
                $or_account_id,
                0.00,
                $tax_withheld_amount,
                $created_by
            );

            // Commit the transaction if everything was successful
            $connection->commit();
        } catch (PDOException $e) {
            // If any error occurs, roll back the transaction
            $connection->rollBack();
            // Re-throw the exception for handling at a higher level
            throw $e;
        }
    }

    public static function addInvoiceItems(
        $or_id,
        $item_id,
        $description,
        $quantity,
        $cost,
        $amount,
        $discount_percentage,
        $discount_amount,
        $net_amount_before_sales_tax,
        $net_amount,
        $sales_tax_percentage,
        $sales_tax_amount,
        $output_vat_id,
        $discount_account_id,
        $cogs_account_id,
        $income_account_id,
        $asset_account_id
    ) {
        global $connection;

        // Use NULL if the value is an empty string
        $output_vat_id = !empty($output_vat_id) ? $output_vat_id : NULL;
        $discount_account_id = !empty($discount_account_id) ? $discount_account_id : NULL;
        $cogs_account_id = !empty($cogs_account_id) ? $cogs_account_id : NULL;
        $income_account_id = !empty($income_account_id) ? $income_account_id : NULL;
        $asset_account_id = !empty($asset_account_id) ? $asset_account_id : NULL;


        $stmt = $connection->prepare("INSERT INTO or_payment_details (
                or_id, 
                item_id, 
                description,
                quantity, 
                cost, 
                amount, 
                discount_percentage, 
                discount_amount, 
                net_amount_before_sales_tax, 
                net_amount, 
                sales_tax_percentage, 
                sales_tax_amount,
                output_vat_id, 
                discount_account_id, 
                cogs_account_id, 
                income_account_id, 
                asset_account_id
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ");

        $stmt->execute([
            $or_id,
            $item_id,
            $description,
            $quantity,
            $cost,
            $amount,
            $discount_percentage,
            $discount_amount,
            $net_amount_before_sales_tax,
            $net_amount,
            $sales_tax_percentage,
            $sales_tax_amount,
            $output_vat_id,
            $discount_account_id,
            $cogs_account_id,
            $income_account_id,
            $asset_account_id
        ]);
    }

    public static function findByOrNo($ci_no)
    {
        global $connection;

        $stmt = $connection->prepare('SELECT * FROM or_payments WHERE ci_no = :ci_no');
        $stmt->bindParam("ci_no", $ci_no);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new Invoice($result[0]);
        }

        return null;
    }

    // ACCOUNTING LOGS
    private static function logAuditTrail($general_journal_id, $transaction_type, $transaction_date, $ref_no, $location, $customer_name, $item, $qty_sold, $account_id, $debit, $credit, $created_by)
    {
        global $connection;

        $stmt = $connection->prepare("
                INSERT INTO audit_trail (
                    transaction_id,
                    transaction_type,
                    transaction_date,
                    ref_no,
                    location,
                    name,
                    item,
                    qty_sold,
                    account_id,
                    debit,
                    credit,
                    created_by,
                    created_at
                ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?, NOW())
            ");

        $stmt->execute([
            $general_journal_id,
            $transaction_type,
            $transaction_date,
            $ref_no,
            $location,
            $customer_name,
            $item,
            $qty_sold,
            $account_id,
            $debit,
            $credit,
            $created_by
        ]);
    }

    public function updateBalance($amountPaid)
    {
        global $connection;
        $sql = "UPDATE sales_invoice SET balance_due = balance_due - :amount_paid WHERE id = :id";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['amount_paid' => $amountPaid, 'id' => $this->id]);
    }

    public static function getLastTransactionId()
    {
        global $connection;

        $stmt = $connection->query("SELECT MAX(id) AS last_transaction_id FROM or_payments");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['last_transaction_id'] ?? null;
    }

    public static function find($id)
    {
        global $connection;

        try {
            $stmt = $connection->prepare('
            SELECT 
                op.id,
                op.ci_no,
                op.or_number,
                op.or_date,
                op.or_account_id,
                op.customer_po,
                op.so_no,
                op.rep,
                op.check_no,
                op.customer_id,
                op.payment_method,
                op.location,
                op.memo,
                op.gross_amount,
                op.discount_amount,
                op.net_amount_due,
                op.vat_amount,
                op.vatable_amount,
                op.zero_rated_amount,
                op.vat_exempt_amount,
                op.tax_withheld_percentage,
                op.tax_withheld_amount,
                op.total_amount_due,
                op.or_status,
                op.status,
                op.print_status,
                c.id AS customer_id,
                c.customer_name,
                c.customer_tin,
                c.customer_code,
                c.customer_contact,
                c.customer_terms,
                c.customer_email,
                c.shipping_address,
                c.billing_address,
                c.business_style,
                coas.account_type_id,
                coas.account_code,
                coas.account_description
            FROM or_payments op
            INNER JOIN customers c ON op.customer_id = c.id
            INNER JOIN chart_of_account coas ON op.or_account_id = coas.id
            WHERE op.id = :id
        ');

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();
            $Data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$Data) {
                return null;
            }

            // Assuming you still need to fetch related details, otherwise remove this line
            $Data['details'] = self::getDetails($id);

            return new OfficialReceipt($Data);
        } catch (PDOException $e) {
            error_log("Database error in find(): " . $e->getMessage());
            return null;
        }
    }

    public static function getDetails($or_id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                opd.id,
                opd.or_id,
                opd.item_id,
                opd.quantity,
                opd.cost,
                opd.amount,
                opd.discount_percentage,
                opd.discount_amount,
                opd.net_amount_before_sales_tax,
                opd.net_amount,
                opd.sales_tax_percentage,
                opd.sales_tax_amount,
                opd.created_at,
                opd.updated_at,
                opd.output_vat_id,
                opd.discount_account_id,
                opd.cogs_account_id,
                opd.income_account_id,
                opd.asset_account_id,
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
            FROM or_payment_details opd
            LEFT JOIN items i ON opd.item_id = i.id
            LEFT JOIN uom um ON i.item_uom_id = um.id
            WHERE opd.or_id = :or_id
        ');

        $stmt->bindParam(':or_id', $or_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $details = [];
        while ($row = $stmt->fetch()) {
            $details[] = $row;
        }

        return $details;
    }

    // GET LAST OR_NO 
    public static function getLastOrNo()
    {
        global $connection;
        try {
            // Prepare and execute the query to get the highest invoice number, ignoring null or empty values
            $stmt = $connection->prepare("
                SELECT ci_no 
                FROM or_payments 
                WHERE ci_no IS NOT NULL AND ci_no <> '' 
                ORDER BY ci_no DESC 
                LIMIT 1
            ");
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetch();

            // Extract the numeric part of the last invoice number
            if ($result) {
                $latestNo = $result['ci_no'];
                // Assuming the format is 'OR' followed by digits
                $numericPart = intval(substr($latestNo, 2)); // 'OR' is 2 characters
                $newNo = $numericPart + 1;
            } else {
                // If no valid invoice number exists, start with 1
                $newNo = 1;
            }

            // Format the new number with leading zeros
            $neworNo = '' . str_pad($newNo, 6, '0', STR_PAD_LEFT);

            return $neworNo;
        } catch (PDOException $e) {
            // Handle potential exceptions
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }


    public static function addDraft(
        $or_number,
        $or_date,
        $or_account_id,
        $customer_po,
        $so_no,
        $check_no,
        $rep,
        $customer_id,
        $payment_method,
        $location,
        $memo,
        $gross_amount,
        $discount_amount,
        $net_amount_due,
        $vat_amount,
        $vatable_amount,
        $zero_rated_amount,
        $vat_exempt_amount,
        $tax_withheld_percentage,
        $tax_withheld_amount,
        $total_amount_due,
        $items
    ) {
        global $connection;

        try {
            // Start a database transaction
            $connection->beginTransaction();

            // Insert into or_payments table
            $sql = "INSERT INTO or_payments (
                or_number, or_date, or_account_id, customer_po, so_no, check_no, rep, customer_id,
                payment_method, location, memo, gross_amount, discount_amount,
                net_amount_due, vat_amount, vatable_amount, zero_rated_amount, vat_exempt_amount,
                tax_withheld_percentage, tax_withheld_amount,
                total_amount_due, status
            ) VALUES (
                :or_number, :or_date, :or_account_id, :customer_po, :so_no, :check_no, :rep, :customer_id,
                :payment_method, :location, :memo, :gross_amount, :discount_amount,
                :net_amount_due, :vat_amount, :vatable_amount, :zero_rated_amount, :vat_exempt_amount,
                :tax_withheld_percentage, :tax_withheld_amount,
                :total_amount_due, :status
            )";

            $stmt = $connection->prepare($sql);
            $stmt->bindParam(':or_number', $or_number);
            $stmt->bindParam(':or_date', $or_date);
            $stmt->bindParam(':or_account_id', $or_account_id);
            $stmt->bindParam(':customer_po', $customer_po);
            $stmt->bindParam(':so_no', $so_no);
            $stmt->bindParam(':check_no', $check_no);
            $stmt->bindParam(':rep', $rep);
            $stmt->bindParam(':customer_id', $customer_id);
            $stmt->bindParam(':payment_method', $payment_method);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':memo', $memo);
            $stmt->bindParam(':gross_amount', $gross_amount);
            $stmt->bindParam(':discount_amount', $discount_amount);
            $stmt->bindParam(':net_amount_due', $net_amount_due);
            $stmt->bindParam(':vat_amount', $vat_amount);
            $stmt->bindParam(':vatable_amount', $vatable_amount);
            $stmt->bindParam(':zero_rated_amount', $zero_rated_amount);
            $stmt->bindParam(':vat_exempt_amount', $vat_exempt_amount);
            $stmt->bindParam(':tax_withheld_percentage', $tax_withheld_percentage);
            $stmt->bindParam(':tax_withheld_amount', $tax_withheld_amount);
            $stmt->bindParam(':total_amount_due', $total_amount_due);
            $stmt->bindValue(':status', 4, PDO::PARAM_INT); // Set status to 4 for draft

            $stmt->execute();

            // Retrieve the last inserted ID
            $or_id = $connection->lastInsertId();

            // Insert official receipt items
            if (!empty($items)) {
                $itemSql = "INSERT INTO or_payment_details (
                    or_id, item_id, quantity, cost, amount, discount_percentage, discount_amount, net_amount_before_sales_tax, net_amount, sales_tax_percentage, sales_tax_amount, output_vat_id, discount_account_id, cogs_account_id, income_account_id, asset_account_id
                ) VALUES (
                    :or_id, :item_id, :quantity, :cost, :amount, :discount_percentage, :discount_amount, :net_amount_before_sales_tax, :net_amount, :sales_tax_percentage, :sales_tax_amount, :output_vat_id, :discount_account_id, :cogs_account_id, :income_account_id, :asset_account_id
                )";

                $itemStmt = $connection->prepare($itemSql);

                foreach ($items as $item) {
                    $itemStmt->bindParam(':or_id', $or_id);
                    $itemStmt->bindParam(':item_id', $item['item_id']);
                    $itemStmt->bindParam(':quantity', $item['quantity']);
                    $itemStmt->bindParam(':cost', $item['cost']);
                    $itemStmt->bindParam(':amount', $item['amount']);
                    $itemStmt->bindParam(':discount_percentage', $item['discount_percentage']);
                    $itemStmt->bindParam(':discount_amount', $item['discount_amount']);
                    $itemStmt->bindParam(':net_amount_before_sales_tax', $item['net_amount_before_sales_tax']);
                    $itemStmt->bindParam(':net_amount', $item['net_amount']);
                    $itemStmt->bindParam(':sales_tax_percentage', $item['sales_tax_percentage']);
                    $itemStmt->bindParam(':sales_tax_amount', $item['sales_tax_amount']);

                    // Handle potential empty values for the following fields
                    $outputVatId = !empty($item['output_vat_id']) ? $item['output_vat_id'] : null;
                    $discountAccountId = !empty($item['discount_account_id']) ? $item['discount_account_id'] : null;
                    $cogsAccountId = !empty($item['cogs_account_id']) ? $item['cogs_account_id'] : null;
                    $incomeAccountId = !empty($item['income_account_id']) ? $item['income_account_id'] : null;
                    $assetAccountId = !empty($item['asset_account_id']) ? $item['asset_account_id'] : null;

                    $itemStmt->bindParam(':output_vat_id', $outputVatId, PDO::PARAM_INT);
                    $itemStmt->bindParam(':discount_account_id', $discountAccountId, PDO::PARAM_INT);
                    $itemStmt->bindParam(':cogs_account_id', $cogsAccountId, PDO::PARAM_INT);
                    $itemStmt->bindParam(':income_account_id', $incomeAccountId, PDO::PARAM_INT);
                    $itemStmt->bindParam(':asset_account_id', $assetAccountId, PDO::PARAM_INT);
                    $itemStmt->execute();
                }
            }

            // Commit the transaction
            $connection->commit();
            return true;
        } catch (Exception $ex) {
            // Rollback transaction if any error occurs
            $connection->rollBack();
            error_log('Error in addDraft: ' . $ex->getMessage());
            throw $ex;
        }
    }


    public static function all()
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                op.*, 
                c.customer_name, 
                c.billing_address
            FROM or_payments op
            INNER JOIN customers c ON op.customer_id = c.id
            ORDER BY op.created_at DESC
        ');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $payments = [];
        while ($row = $stmt->fetch()) {
            $payment = [
                'id' => $row['id'],
                'ci_no' => $row['ci_no'],
                'or_number' => $row['or_number'],
                'check_no' => $row['check_no'],
                'or_date' => $row['or_date'],
                'customer_name' => $row['customer_name'],
                'payment_method' => $row['payment_method'],
                'billing_address' => $row['billing_address'],
                'memo' => $row['memo'],
                'total_amount_due' => $row['total_amount_due'],
                'print_status' => $row['print_status'],
                'status' => $row['status'],
            ];
            $payments[] = new OfficialReceipt($payment);  // Assuming there's a 'Payment' class
        }

        return $payments;
    }


    public static function void($id)
    {
        global $connection;

        try {
            $connection->beginTransaction();

            // Update the status to 3 (void) in the sales_invoice table
            $stmt = $connection->prepare("UPDATE or_payments SET status = 3 WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();

            if ($result) {
                // Update the state to 2 in the audit_trail table
                $auditStmt = $connection->prepare("UPDATE audit_trail SET state = 2 WHERE transaction_id = :id");
                $auditStmt->bindParam(':id', $id, PDO::PARAM_INT);
                $auditResult = $auditStmt->execute();

                if ($auditResult) {
                    // Delete from transaction_entries
                    $deleteStmt = $connection->prepare("DELETE FROM transaction_entries WHERE transaction_id = :id");
                    $deleteStmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $deleteResult = $deleteStmt->execute();

                    if ($deleteResult) {
                        $connection->commit();
                        return true;
                    } else {
                        throw new Exception("Failed to delete transaction entries.");
                    }
                } else {
                    throw new Exception("Failed to update audit trail.");
                }
            } else {
                throw new Exception("Failed to void invoice.");
            }
        } catch (Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    public static function draftDetails($or_id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                opd.id,
                opd.or_id,
                opd.item_id,
                opd.quantity,
                opd.cost,
                opd.amount,
                opd.discount_percentage,
                opd.discount_amount,
                opd.net_amount_before_sales_tax,
                opd.net_amount,
                opd.sales_tax_percentage,
                opd.sales_tax_amount,
                opd.created_at,
                opd.updated_at,
                opd.output_vat_id,
                opd.discount_account_id,
                opd.cogs_account_id,
                opd.income_account_id,
                opd.asset_account_id,
                i.item_name,
                coa.account_type_id,
                coa.gl_name,
                coa.account_code,
                coa.account_description
            FROM 
                or_payment_details opd
            LEFT JOIN
                chart_of_account coa ON opd.discount_account_id = coa.id
            LEFT JOIN
                items i ON opd.item_id = i.id
            WHERE 
                opd.or_id = :or_id
        ');

        $stmt->bindParam(':or_id', $or_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        return $stmt->fetchAll(); // Return the fetched data
    }


    public static function updateDraft(
        $id,
        $or_number,
        $or_date,
        $or_account_id,
        $customer_po,
        $so_no,
        $check_no,
        $rep,
        $payment_method,
        $location,
        $memo,
        $gross_amount,
        $total_discount_amount,
        $net_amount_due,
        $vat_amount,
        $vatable_amount,
        $zero_rated_amount,
        $vat_exempt_amount,
        $tax_withheld_percentage,
        $tax_withheld_amount,
        $total_amount_due,
        $items,
        $customer_id,
        $customer_name
    ) {
        global $connection;

        try {
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $connection->beginTransaction();

            error_log("Starting updateDraft for OR ID: $id");

            $stmt = $connection->prepare("
                UPDATE or_payments 
                SET 
                    or_number = :or_number,
                    or_date = :or_date,
                    or_account_id = :or_account_id,
                    customer_po = :customer_po,
                    so_no = :so_no,
                    rep = :rep,
                    check_no = :check_no,
                    customer_id = :customer_id,
                    payment_method = :payment_method,
                    location = :location,
                    memo = :memo,
                    gross_amount = :gross_amount,
                    discount_amount = :discount_amount,
                    net_amount_due = :net_amount_due,
                    vat_amount = :vat_amount,
                    vatable_amount = :vatable_amount,
                    zero_rated_amount = :zero_rated_amount,
                    vat_exempt_amount = :vat_exempt_amount,
                    tax_withheld_percentage = :tax_withheld_percentage,
                    tax_withheld_amount = :tax_withheld_amount,
                    total_amount_due = :total_amount_due,
                    status = 4
                WHERE id = :id
            ");

            // Bind the parameters
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':or_number', $or_number, PDO::PARAM_STR);
            $stmt->bindParam(':or_date', $or_date, PDO::PARAM_STR);
            $stmt->bindParam(':or_account_id', $or_account_id, PDO::PARAM_INT);
            $stmt->bindParam(':customer_po', $customer_po, PDO::PARAM_STR);
            $stmt->bindParam(':so_no', $so_no, PDO::PARAM_STR);
            $stmt->bindParam(':rep', $rep, PDO::PARAM_STR);
            $stmt->bindParam(':check_no', $check_no, PDO::PARAM_STR);
            $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
            $stmt->bindParam(':payment_method', $payment_method, PDO::PARAM_STR);
            $stmt->bindParam(':location', $location, PDO::PARAM_STR);
            $stmt->bindParam(':memo', $memo, PDO::PARAM_STR);
            $stmt->bindParam(':gross_amount', $gross_amount, PDO::PARAM_STR);
            $stmt->bindParam(':discount_amount', $total_discount_amount, PDO::PARAM_STR);
            $stmt->bindParam(':net_amount_due', $net_amount_due, PDO::PARAM_STR);
            $stmt->bindParam(':vat_amount', $vat_amount, PDO::PARAM_STR);
            $stmt->bindParam(':vatable_amount', $vatable_amount, PDO::PARAM_STR);
            $stmt->bindParam(':zero_rated_amount', $zero_rated_amount, PDO::PARAM_STR);
            $stmt->bindParam(':vat_exempt_amount', $vat_exempt_amount, PDO::PARAM_STR);
            $stmt->bindParam(':tax_withheld_percentage', $tax_withheld_percentage, PDO::PARAM_STR);
            $stmt->bindParam(':tax_withheld_amount', $tax_withheld_amount, PDO::PARAM_STR);
            $stmt->bindParam(':total_amount_due', $total_amount_due, PDO::PARAM_STR);

            $result = $stmt->execute();
            error_log("Update query executed. Result: " . ($result ? "true" : "false"));

            if ($result) {
                error_log("Deleting old payment details for OR ID: $id");
                $stmt = $connection->prepare("DELETE FROM or_payment_details WHERE or_id = ?");
                $deleteResult = $stmt->execute([$id]);
                error_log("Delete query executed. Result: " . ($deleteResult ? "true" : "false"));

                error_log("Inserting new payment details for OR ID: $id");
                $stmt = $connection->prepare("
                    INSERT INTO or_payment_details (
                        or_id, item_id, quantity, cost, amount, discount_percentage, discount_amount,
                        net_amount_before_sales_tax, net_amount, sales_tax_percentage, sales_tax_amount,
                        output_vat_id, discount_account_id, cogs_account_id, income_account_id, asset_account_id
                    ) VALUES (
                        :or_id, :item_id, :quantity, :cost, :amount, :discount_percentage, :discount_amount,
                        :net_amount_before_sales_tax, :net_amount, :sales_tax_percentage, :sales_tax_amount,
                        :output_vat_id, :discount_account_id, :cogs_account_id, :income_account_id, :asset_account_id
                    )
                ");

                // Process each item in the invoice
                foreach ($items as $item) {
                    $stmt->execute([
                        ':or_id' => $id,
                        ':item_id' => $item['item_id'],
                        ':quantity' => $item['quantity'],
                        ':cost' => $item['cost'],
                        ':amount' => $item['amount'],
                        ':discount_percentage' => $item['discount_percentage'], // Now this will be inserted
                        ':discount_amount' => $item['discount_amount'],
                        ':net_amount_before_sales_tax' => $item['net_amount_before_sales_tax'],
                        ':net_amount' => $item['net_amount'],
                        ':sales_tax_percentage' => $item['sales_tax_percentage'],
                        ':sales_tax_amount' => $item['sales_tax_amount'],
                        ':output_vat_id' => !empty($item['output_vat_id']) ? $item['output_vat_id'] : null,
                        ':discount_account_id' => !empty($item['discount_account_id']) ? $item['discount_account_id'] : null,
                        ':cogs_account_id' => !empty($item['cogs_account_id']) ? $item['cogs_account_id'] : null,
                        ':income_account_id' => !empty($item['income_account_id']) ? $item['income_account_id'] : null,
                        ':asset_account_id' => !empty($item['asset_account_id']) ? $item['asset_account_id'] : null
                    ]);
                }

                $connection->commit();
                error_log("Transaction committed successfully");
                return [
                    'success' => true,
                    'id' => $id
                ];
            } else {
                throw new Exception("Failed to update invoice.");
            }
        } catch (Exception $ex) {
            $connection->rollback();
            error_log('Error updating draft invoice: ' . $ex->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $ex->getMessage()
            ];
        }
    }


    public static function saveFinal(
        $id,
        $or_number,
        $or_date,
        $or_account_id,
        $customer_po,
        $so_no,
        $check_no,
        $rep,
        $payment_method,
        $location,
        $memo,
        $gross_amount,
        $total_discount_amount,
        $net_amount_due,
        $vat_amount,
        $vatable_amount,
        $zero_rated_amount,
        $vat_exempt_amount,
        $tax_withheld_percentage,
        $tax_withheld_amount,
        $total_amount_due,
        $items,
        $customer_id,
        $customer_name,
        $ci_no,
        $created_by
    ) {
        global $connection;

        try {
            $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $connection->beginTransaction();

            error_log("Starting updateDraft for OR ID: $id");

            $stmt = $connection->prepare("
                UPDATE or_payments 
                SET 
                    or_number = :or_number,
                    ci_no = :ci_no,
                    or_date = :or_date,
                    or_account_id = :or_account_id,
                    customer_po = :customer_po,
                    so_no = :so_no,
                    rep = :rep,
                    check_no = :check_no,
                    customer_id = :customer_id,
                    payment_method = :payment_method,
                    location = :location,
                    memo = :memo,
                    gross_amount = :gross_amount,
                    discount_amount = :discount_amount,
                    net_amount_due = :net_amount_due,
                    vat_amount = :vat_amount,
                    vatable_amount = :vatable_amount,
                    zero_rated_amount = :zero_rated_amount,
                    vat_exempt_amount = :vat_exempt_amount,
                    tax_withheld_percentage = :tax_withheld_percentage,
                    tax_withheld_amount = :tax_withheld_amount,
                    total_amount_due = :total_amount_due,
                    status = 0
                WHERE id = :id
            ");

            // Bind the parameters
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':or_number', $or_number, PDO::PARAM_STR);
            $stmt->bindParam(':ci_no', $ci_no, PDO::PARAM_STR);
            $stmt->bindParam(':or_date', $or_date, PDO::PARAM_STR);
            $stmt->bindParam(':or_account_id', $or_account_id, PDO::PARAM_INT);
            $stmt->bindParam(':customer_po', $customer_po, PDO::PARAM_STR);
            $stmt->bindParam(':so_no', $so_no, PDO::PARAM_STR);
            $stmt->bindParam(':rep', $rep, PDO::PARAM_STR);
            $stmt->bindParam(':check_no', $check_no, PDO::PARAM_STR);
            $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
            $stmt->bindParam(':payment_method', $payment_method, PDO::PARAM_STR);
            $stmt->bindParam(':location', $location, PDO::PARAM_STR);
            $stmt->bindParam(':memo', $memo, PDO::PARAM_STR);
            $stmt->bindParam(':gross_amount', $gross_amount, PDO::PARAM_STR);
            $stmt->bindParam(':discount_amount', $total_discount_amount, PDO::PARAM_STR);
            $stmt->bindParam(':net_amount_due', $net_amount_due, PDO::PARAM_STR);
            $stmt->bindParam(':vat_amount', $vat_amount, PDO::PARAM_STR);
            $stmt->bindParam(':vatable_amount', $vatable_amount, PDO::PARAM_STR);
            $stmt->bindParam(':zero_rated_amount', $zero_rated_amount, PDO::PARAM_STR);
            $stmt->bindParam(':vat_exempt_amount', $vat_exempt_amount, PDO::PARAM_STR);
            $stmt->bindParam(':tax_withheld_percentage', $tax_withheld_percentage, PDO::PARAM_STR);
            $stmt->bindParam(':tax_withheld_amount', $tax_withheld_amount, PDO::PARAM_STR);
            $stmt->bindParam(':total_amount_due', $total_amount_due, PDO::PARAM_STR);

            $result = $stmt->execute();
            error_log("Update query executed. Result: " . ($result ? "true" : "false"));

            if ($result) {
                error_log("Deleting old payment details for OR ID: $id");
                $stmt = $connection->prepare("DELETE FROM or_payment_details WHERE or_id = ?");
                $deleteResult = $stmt->execute([$id]);
                error_log("Delete query executed. Result: " . ($deleteResult ? "true" : "false"));

                error_log("Inserting new payment details for OR ID: $id");
                $stmt = $connection->prepare("
                INSERT INTO or_payment_details (
                    or_id, item_id, quantity, cost, amount, discount_percentage, discount_amount,
                    net_amount_before_sales_tax, net_amount, sales_tax_percentage, sales_tax_amount,
                    output_vat_id, discount_account_id, cogs_account_id, income_account_id, asset_account_id
                ) VALUES (
                    :or_id, :item_id, :quantity, :cost, :amount, :discount_percentage, :discount_amount,
                    :net_amount_before_sales_tax, :net_amount, :sales_tax_percentage, :sales_tax_amount,
                    :output_vat_id, :discount_account_id, :cogs_account_id, :income_account_id, :asset_account_id
                )
                ");

                if (!is_array($items)) {
                    throw new Exception('$items is not an array. Content: ' . print_r($items, true));
                }

                // Prepare statement for fetching item name
                $stmtItemName = $connection->prepare("SELECT item_name FROM items WHERE id = :item_id");

                $transaction_type = " Cash Invoice";

                // Fetch wtax_account_id based on tax_withheld_percentage
                $wtax_stmt = $connection->prepare("
                    SELECT wtax_account_id 
                    FROM wtax 
                    WHERE id = :id
                ");
                $wtax_stmt->bindParam(':id', $tax_withheld_percentage);
                $wtax_stmt->execute();
                $wtax_account_id = $wtax_stmt->fetchColumn();

                if (!$wtax_account_id) {
                    throw new Exception("WTax account ID not found for percentage: $tax_withheld_percentage.");
                }

                // Log audit trails
                self::logAuditTrail(
                    $id,
                    $transaction_type,
                    $or_date,
                    $or_number,
                    $location,
                    $customer_name,
                    null,
                    null,
                    $or_account_id,
                    $gross_amount,
                    0.00,
                    $created_by
                );

                self::logAuditTrail(
                    $id,
                    $transaction_type,
                    $or_date,
                    $or_number,
                    $location,
                    $customer_name,
                    null,
                    null,
                    $wtax_account_id,
                    $tax_withheld_amount,
                    0.00,
                    $created_by
                );

                // Process each item in the invoice
                foreach ($items as $item) {
                    // Fetch the item_name before executing the main statement
                    $stmtItemName->execute([':item_id' => $item['item_id']]);
                    $item_name = $stmtItemName->fetchColumn();

                    $stmt->execute([
                        ':or_id' => $id,
                        ':item_id' => $item['item_id'],
                        ':quantity' => $item['quantity'],
                        ':cost' => $item['cost'],
                        ':amount' => $item['amount'],
                        ':discount_percentage' => $item['discount_percentage'], // Now this will be inserted
                        ':discount_amount' => $item['discount_amount'],
                        ':net_amount_before_sales_tax' => $item['net_amount_before_sales_tax'],
                        ':net_amount' => $item['net_amount'],
                        ':sales_tax_percentage' => $item['sales_tax_percentage'],
                        ':sales_tax_amount' => $item['sales_tax_amount'],
                        ':output_vat_id' => !empty($item['output_vat_id']) ? $item['output_vat_id'] : null,
                        ':discount_account_id' => !empty($item['discount_account_id']) ? $item['discount_account_id'] : null,
                        ':cogs_account_id' => !empty($item['cogs_account_id']) ? $item['cogs_account_id'] : null,
                        ':income_account_id' => !empty($item['income_account_id']) ? $item['income_account_id'] : null,
                        ':asset_account_id' => !empty($item['asset_account_id']) ? $item['asset_account_id'] : null
                    ]);

                    if (!empty($item['discount_account_id'])) {
                        self::logAuditTrail(
                            $id,
                            $transaction_type,
                            $or_date,
                            $or_number,
                            $location,
                            $customer_name,
                            $item_name,
                            $item['quantity'],
                            $item['discount_account_id'],
                            $item['discount_amount'],
                            0.00,
                            $created_by
                        );
                    }

                    if (!empty($item['income_account_id'])) {
                        self::logAuditTrail(
                            $id,
                            $transaction_type,
                            $or_date,
                            $or_number,
                            $location,
                            $customer_name,
                            $item_name,
                            $item['quantity'],
                            $item['income_account_id'],
                            0.00,
                            ($item['amount'] - $item['sales_tax_amount']),
                            $created_by
                        );
                    }

                    if (!empty($item['cogs_account_id'])) {
                        self::logAuditTrail(
                            $id,
                            $transaction_type,
                            $or_date,
                            $or_number,
                            $location,
                            $customer_name,
                            $item_name,
                            $item['quantity'],
                            $item['cogs_account_id'],
                            $item['cost'] * $item['quantity'],
                            0.00,
                            $created_by
                        );
                    }

                    if (!empty($item['asset_account_id'])) {
                        self::logAuditTrail(
                            $id,
                            $transaction_type,
                            $or_date,
                            $or_number,
                            $location,
                            $customer_name,
                            $item_name,
                            $item['quantity'],
                            $item['asset_account_id'],
                            0.00,
                            $item['cost'] * $item['quantity'],
                            $created_by
                        );
                    }
                }

                // Log output VAT audit trail outside the loop
                if (!empty($items[0]['output_vat_id'])) {
                    self::logAuditTrail(
                        $id,
                        $transaction_type,
                        $or_date,
                        $or_number,
                        $location,
                        $customer_name,
                        null,
                        null,
                        $items[0]['output_vat_id'],
                        0.00,
                        $vat_amount,
                        $created_by
                    );
                }


                // Log final audit trails
                self::logAuditTrail(
                    $id,
                    $transaction_type,
                    $or_date,
                    $or_number,
                    $location,
                    $customer_name,
                    null,
                    null,
                    $or_account_id,
                    0.00,
                    $total_discount_amount,
                    $created_by
                );

                self::logAuditTrail(
                    $id,
                    $transaction_type,
                    $or_date,
                    $or_number,
                    $location,
                    $customer_name,
                    null,
                    null,
                    $or_account_id,
                    0.00,
                    $tax_withheld_amount,
                    $created_by
                );

                $connection->commit();
                error_log("Transaction committed successfully");
                return [
                    'success' => true,
                    'id' => $id
                ];
            } else {
                throw new Exception("Failed to update invoice.");
            }
        } catch (Exception $ex) {
            $connection->rollback();
            error_log('Error updating draft invoice: ' . $ex->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $ex->getMessage()
            ];
        }
    }


    // #######################################################################
    public static function insert_inventory_valuation(
        $type,
        $transaction_id,
        $ref_no,
        $date,
        $name,
        $item_id,
        $qty_purchased = 0.00,
        $qty_sold,
        $cost = 0.00,
        $total_cost = 0.00,
        $purchase_discount_rate = 0.00,
        $purchase_discount_per_item = 0.00,
        $purchase_discount_amount = 0.00,
        $net_amount = 0.00,
        $input_vat_rate = 0.00,
        $input_vat = 0.00,
        $taxable_purchased_amount = 0.00,
        $cost_per_unit = 0.00,
        $selling_price = 0.00,
        $gross_sales = 0.00,
        $sales_discount_rate = 0.00,
        $sales_discount_amount = 0.00,
        $net_sales = 0.00,
        $sales_tax = 0.00,
        $output_vat = 0.00,
        $taxable_sales_amount = 0.00,
        $selling_price_per_unit = 0.00
    ) {
        global $connection;  // Access the global PDO connection

        try {
            // Prepare the SQL statement
            $stmt = $connection->prepare("
            CALL insert_inventory_valuation(
                :type,
                :transaction_id,
                :ref_no,
                :date,
                :name,
                :item_id,
                :qty_purchased,
                :qty_sold,
                :cost,
                :total_cost,
                :purchase_discount_rate,
                :purchase_discount_per_item,
                :purchase_discount_amount,
                :net_amount,
                :input_vat_rate,
                :input_vat,
                :taxable_purchased_amount,
                :cost_per_unit,
                :selling_price,
                :gross_sales,
                :sales_discount_rate,
                :sales_discount_amount,
                :net_sales,
                :sales_tax,
                :output_vat,
                :taxable_sales_amount,
                :selling_price_per_unit
            )
        ");

            // Bind parameters
            $stmt->bindParam(':type', $type);
            $stmt->bindParam(':transaction_id', $transaction_id);
            $stmt->bindParam(
                ':ref_no',
                $ref_no
            );
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(
                ':item_id',
                $item_id
            );
            $stmt->bindParam(':qty_purchased', $qty_purchased);
            $stmt->bindParam(':qty_sold', $qty_sold);
            $stmt->bindParam(':cost', $cost);
            $stmt->bindParam(':total_cost', $total_cost);
            $stmt->bindParam(':purchase_discount_rate', $purchase_discount_rate);
            $stmt->bindParam(':purchase_discount_per_item', $purchase_discount_per_item);
            $stmt->bindParam(':purchase_discount_amount', $purchase_discount_amount);
            $stmt->bindParam(':net_amount', $net_amount);
            $stmt->bindParam(':input_vat_rate', $input_vat_rate);
            $stmt->bindParam(':input_vat', $input_vat);
            $stmt->bindParam(':taxable_purchased_amount', $taxable_purchased_amount);
            $stmt->bindParam(':cost_per_unit', $cost_per_unit);
            $stmt->bindParam(':selling_price', $selling_price);
            $stmt->bindParam(':gross_sales', $gross_sales);
            $stmt->bindParam(':sales_discount_rate', $sales_discount_rate);
            $stmt->bindParam(':sales_discount_amount', $sales_discount_amount);
            $stmt->bindParam(':net_sales', $net_sales);
            $stmt->bindParam(':sales_tax', $sales_tax);
            $stmt->bindParam(':output_vat', $output_vat);
            $stmt->bindParam(':taxable_sales_amount', $taxable_sales_amount);
            $stmt->bindParam(':selling_price_per_unit', $selling_price_per_unit);

            // Execute the statement
            $stmt->execute();

            // Check if the procedure was successful
            return true;
        } catch (PDOException $e) {
            error_log("Error calling insert_inventory_valuation: " . $e->getMessage());
            return false;
        }
    }
}
