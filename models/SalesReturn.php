<?php
require_once __DIR__ . '/../_init.php';

class SalesReturn
{
    public $id;
    public $sales_return_account_id;
    public $sales_return_number;
    public $customer_po;
    public $so_no;
    public $rep;
    public $sales_return_date;
    public $sales_return_account;
    public $sales_return_due_date;
    public $customer_id;
    public $customer_name;
    public $customer_tin;
    public $customer_email;
    public $shipping_address;
    public $billing_address;
    public $business_style;
    public $payment_method;
    public $location;
    public $terms;
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
    public $sales_return_status;
    public $status;
    public $print_status;
    public $balance_due;
    private $isDraft = false;
    public $details = [];

    private static $cache = null;

    public function __construct($salesReturnData)
    {
        $this->id = $salesReturnData['id'] ?? null;
        $this->sales_return_account_id = $salesReturnData['sales_return_account_id'] ?? null;
        $this->sales_return_number = $salesReturnData['sales_return_number'] ?? null;
        $this->sales_return_date = $salesReturnData['sales_return_date'] ?? null;
        $this->customer_po = $salesReturnData['customer_po'] ?? null;
        $this->so_no = $salesReturnData['so_no'] ?? null;
        $this->rep = $salesReturnData['rep'] ?? null;
        $this->sales_return_account = $salesReturnData['sales_return_account'] ?? null;
        $this->sales_return_due_date = $salesReturnData['sales_return_due_date'] ?? null;
        $this->customer_id = $salesReturnData['customer_id'] ?? null;
        $this->customer_name = $salesReturnData['customer_name'] ?? null;
        $this->customer_tin = $salesReturnData['customer_tin'] ?? null;
        $this->customer_email = $salesReturnData['customer_email'] ?? null;
        $this->shipping_address = $salesReturnData['shipping_address'] ?? null;
        $this->billing_address = $salesReturnData['billing_address'] ?? null;
        $this->business_style = $salesReturnData['business_style'] ?? null;
        $this->payment_method = $salesReturnData['payment_method'] ?? null;
        $this->location = $salesReturnData['location'] ?? null;
        $this->terms = $salesReturnData['terms'] ?? null;
        $this->memo = $salesReturnData['memo'] ?? null;
        $this->gross_amount = $salesReturnData['gross_amount'] ?? null;
        $this->discount_amount = $salesReturnData['sales_return_discount_amount'] ?? null;
        $this->net_amount_due = $salesReturnData['net_amount_due'] ?? null;
        $this->vat_amount = $salesReturnData['vat_amount'] ?? null;
        $this->vatable_amount = $salesReturnData['vatable_amount'] ?? null;
        $this->zero_rated_amount = $salesReturnData['zero_rated_amount'] ?? null;
        $this->vat_exempt_amount = $salesReturnData['vat_exempt_amount'] ?? null;
        $this->tax_withheld_percentage = $salesReturnData['tax_withheld_percentage'] ?? null;
        $this->tax_withheld_amount = $salesReturnData['tax_withheld_amount'] ?? null;
        $this->total_amount_due = $salesReturnData['total_amount_due'] ?? null;
        $this->sales_return_status = $salesReturnData['sales_return_status'] ?? null;
        $this->status = $salesReturnData['status'] ?? null;
        $this->print_status = $salesReturnData['print_status'] ?? null;
        $this->balance_due = $salesReturnData['balance_due'] ?? null;

        // Initialize details as an empty array
        $this->details = [];

        // Populate other properties as before...

        // Optionally, you can populate details if provided in $formData
        if (isset($salesReturnData['details'])) {
            foreach ($salesReturnData['details'] as $detail) {
                // Push each detail to the details array
                $this->details[] = $detail;
            }
        }
    }


    // ==========================================================================
    // WARNING: DO NOT MODIFY THE CODE BELOW!
    // Version: 1.0.0 (First working version)
    // Last Updated: [7/26/2024]
    //
    // This method has been finalized and locked for modifications.
    // This section is critical for the functionality of the sales_return processing.
    // Any changes might cause unexpected behavior or system failures.
    // If changes are absolutely necessary, consult with the lead developer
    // and thoroughly test all affected systems before deployment.
    //
    // Change Log:
    // v1.0.0 - Initial working version implemented and tested
    // ==========================================================================

    public static function add($sales_return_number, $sales_return_date, $sales_return_account_id, $customer_po, $so_no, $rep, $discount_account_ids, $output_vat_ids, $sales_return_due_date, $customer_id, $customer_name, $payment_method, $location, $terms, $memo, $gross_amount, $total_discount_amount, $net_amount_due, $vat_amount, $vatable_amount, $zero_rated_amount, $vat_exempt_amount, $tax_withheld_percentage, $tax_withheld_amount, $tax_withheld_account_id, $total_amount_due, $items, $created_by)
    {
        global $connection;

        try {
            global $connection;


            // Start a database transaction to ensure data integrity
            $connection->beginTransaction();

            $transaction_type = "Sales Return";

            // Check if the sales_return account is an Accounts Receivable or Undeposited Funds type
            $stmt = $connection->prepare("
                    SELECT at.name
                    FROM chart_of_account coa
                    JOIN account_types at ON coa.account_type_id = at.id
                    WHERE coa.id = ?
                ");
            $stmt->execute([$sales_return_account_id]);
            $account_type_name = $stmt->fetchColumn();

            // Determine the sales_return status and balance due based on the account type
            $status = 0;
            $balance_due = $total_amount_due;
            if ($account_type_name == 'Bank' || $account_type_name == 'Other Current Assets') {
                $status = 1;
                $balance_due = 0;
            }
            


            // If it's an Accounts Receivable, update the customer's balance
            if ($account_type_name == 'Accounts Receivable') {
                $stmt = $connection->prepare("UPDATE customers SET credit_balance = credit_balance + ?, total_sales_returnd = total_sales_returnd + ? WHERE id = ?");
                $stmt->execute([$total_amount_due, $total_amount_due, $customer_id]);
            }

            // Prepare and execute SQL to insert the main sales_return record
            $stmt = $connection->prepare("INSERT INTO sales_return (
                sales_return_number, sales_return_date, sales_return_account_id, sales_return_due_date,
                customer_po, so_no, rep, 
                customer_id, payment_method, location, terms, memo, gross_amount,
                discount_amount, net_amount_due, vat_amount, vatable_amount,
                zero_rated_amount, vat_exempt_amount, tax_withheld_percentage,
                tax_withheld_amount, total_amount_due, status, balance_due
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

            $stmt->execute([
                $sales_return_number,
                $sales_return_date,
                $sales_return_account_id,
                $sales_return_due_date,
                $customer_po,
                $so_no,
                $rep,
                $customer_id,
                $payment_method,
                $location,
                $terms,
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
                $status,
                $balance_due
            ]);

            // Get the ID of the newly inserted sales_return
            $sales_return_id = $connection->lastInsertId();



            // Log the main sales_return transaction in the audit trail
            self::logAuditTrail(
                $sales_return_id,
                $transaction_type,
                $sales_return_date,
                $sales_return_number,
                $location,
                $customer_name,
                null,
                null,
                $sales_return_account_id,
                0.00,
                $gross_amount,
                $created_by
            );

            // Log tax withheld transaction
            self::logAuditTrail(
                $sales_return_id,
                $transaction_type,
                $sales_return_date,
                $sales_return_number,
                $location,
                $customer_name,
                null,
                null,
                $tax_withheld_account_id,
                0.00,
                $tax_withheld_amount,
                $created_by
            );

            // Log VAT transaction
            self::logAuditTrail(
                $sales_return_id,
                $transaction_type,
                $sales_return_date,
                $sales_return_number,
                $location,
                $customer_name,
                null,
                null,
                $output_vat_ids,
                $vat_amount,
                0.00,
                $created_by
            );

            // Process each item in the sales_return
            foreach ($items as $item) {
                // Add the individual sales_return item to the database
                self::addSalesReturnItems(
                    $sales_return_id,
                    $item['item_id'],
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
                self::logAuditTrail(
                    $sales_return_id,
                    $transaction_type,
                    $sales_return_date,
                    $sales_return_number,
                    $location,
                    $customer_name,
                    $item['item_name'],
                    $item['quantity'],
                    $item['discount_account_id'],
                    0.00,
                    $item['discount_amount'],
                    $created_by
                );

                // Log income for this item
                self::logAuditTrail(
                    $sales_return_id,
                    $transaction_type,
                    $sales_return_date,
                    $sales_return_number,
                    $location,
                    $customer_name,
                    $item['item_name'],
                    $item['quantity'],
                    $item['income_account_id'],
                    ($item['amount'] - $item['sales_tax_amount']),
                    0.00,
                    $created_by
                );

                // Log Cost of Goods Sold (COGS) for this item
                self::logAuditTrail(
                    $sales_return_id,
                    $transaction_type,
                    $sales_return_date,
                    $sales_return_number,
                    $location,
                    $customer_name,
                    $item['item_name'],
                    $item['quantity'],
                    $item['cogs_account_id'],
                    0.00,
                    $item['cost'] * $item['quantity'],
                    $created_by
                );

                // Log asset transaction for this item
                self::logAuditTrail(
                    $sales_return_id,
                    $transaction_type,
                    $sales_return_date,
                    $sales_return_number,
                    $location,
                    $customer_name,
                    $item['item_name'],
                    $item['quantity'],
                    $item['asset_account_id'],
                    $item['cost'] * $item['quantity'],
                    0.00,
                    $created_by
                );
            }

            // Log the main sales_return transaction discount credit
            self::logAuditTrail(
                $sales_return_id,
                $transaction_type,
                $sales_return_date,
                $sales_return_number,
                $location,
                $customer_name,
                null,
                null,
                $sales_return_account_id,
                $total_discount_amount,
                0.00,    
                $created_by
            );

            // Log the main sales_return transaction wtax credit
            self::logAuditTrail(
                $sales_return_id,
                $transaction_type,
                $sales_return_date,
                $sales_return_number,
                $location,
                $customer_name,
                null,
                null,
                $sales_return_account_id,
                $tax_withheld_amount,
                0.00,
                $created_by
            );

            // Commit the transaction if everything was successful
            $connection->commit();
        } catch (PDOException $e) {
            // If any error occurs, roll back the transaction
            $connection->rollback();
            // Re-throw the exception for handling at a higher level
            throw $e;
        }
    }

    public static function addSalesReturnItems($sales_return_id, $item_id, $quantity, $cost, $amount, $discount_percentage, $discount_amount, $net_amount_before_sales_tax, $net_amount, $sales_tax_percentage, $sales_tax_amount,$output_vat_id, $discount_account_id, $cogs_account_id, $income_account_id, $asset_account_id)
    {
        global $connection;

        $stmt = $connection->prepare("INSERT INTO sales_return_details (
                sales_return_id, 
                item_id, 
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
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ");

        $stmt->execute([
            $sales_return_id,
            $item_id,
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

    public static function findBysales_returnNo($sales_return_number)
    {
        global $connection;

        $stmt = $connection->prepare('SELECT * FROM sales_return WHERE sales_return_number = :sales_return_number');
        $stmt->bindParam("sales_return_number", $sales_return_number);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new SalesReturn($result[0]);
        }

        return null;
    }

    // ACCOUNTING LOGS
    private static function logAuditTrail($general_journal_id, $transaction_type, $transaction_date, $ref_no, $location, $customer_name, $item, $qty_sold, $account_id, $debit, $credit, $created_by)
    {
        global $connection;

        $negative_qty_sold = $qty_sold !== null ? -abs($qty_sold) : null;
        
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
            $negative_qty_sold,
            $account_id,
            $debit,
            $credit,
            $created_by
        ]);
    }

    public function updateBalance($amountPaid)
    {
        global $connection;
        $sql = "UPDATE sales_return SET balance_due = balance_due - :amount_paid WHERE id = :id";
        $stmt = $connection->prepare($sql);
        $stmt->execute(['amount_paid' => $amountPaid, 'id' => $this->id]);
    }


    // ==========================================================================
    // WARNING: DO NOT MODIFY THE CODE ABOVE!
    // This method has been finalized and locked for modifications.
    // This section is critical for the functionality of the sales_return processing.
    // Any changes might cause unexpected behavior or system failures.
    // If changes are absolutely necessary, consult with the lead developer
    // and thoroughly test all affected systems before deployment.
    // ==========================================================================



    public static function getLastTransactionId()
    {
        global $connection;

        $stmt = $connection->query("SELECT MAX(id) AS last_transaction_id FROM sales_return");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['last_transaction_id'] ?? null;
    }

    public static function all()
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                sr.*, 
                c.customer_name, 
                c.billing_address
            FROM sales_return sr
            INNER JOIN customers c ON sr.customer_id = c.id
        ');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $sales_returns = [];
        while ($row = $stmt->fetch()) {
            $sales_return = [
                'id' => $row['id'],
                'sales_return_number' => $row['sales_return_number'],
                'sales_return_date' => $row['sales_return_date'],
                'customer_name' => $row['customer_name'],
                'billing_address' => $row['billing_address'],
                'memo' => $row['memo'],
                'location' => $row['location'],
                'total_amount_due' => $row['total_amount_due'],
                'sales_return_status' => $row['sales_return_status'],
                'status' => $row['status'],
                'balance_due' => $row['balance_due']
            ];
            $sales_returns[] = new SalesReturn($sales_return);
        }

        return $sales_returns;
    }

    public static function find($id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                sr.id,
                sr.sales_return_number,
                sr.sales_return_date,
                sr.sales_return_account_id,
                sr.customer_po,
                sr.so_no,
                sr.rep,
                sr.sales_return_due_date,
                sr.payment_method,
                sr.location,
                sr.terms,
                sr.memo,
                sr.gross_amount,
                sr.discount_amount as sales_return_discount_amount,
                sr.net_amount_due,
                sr.vat_amount,
                sr.vatable_amount,
                sr.zero_rated_amount,
                sr.vat_exempt_amount,
                sr.tax_withheld_percentage,
                sr.tax_withheld_amount,
                sr.total_amount_due,
                sr.sales_return_status,
                sr.status,
                sr.print_status,
                c.id as customer_id,
                c.customer_name,
                c.customer_tin,
                c.customer_code,
                c.customer_contact,
                c.customer_terms,
                c.customer_email,
                c.shipping_address,
                c.billing_address,
                c.business_style
            FROM sales_return sr
            INNER JOIN customers c ON sr.customer_id = c.id
            WHERE sr.id = :id
        ');

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $salesReturnData = $stmt->fetch();

        if (!$salesReturnData) {
            return null;
        }

        $salesReturnData['details'] = self::getsales_returnDetails($id);

        return new SalesReturn($salesReturnData);
    }

    public static function getsales_returnDetails($sales_return_id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                srd.id,
                srd.sales_return_id,
                srd.quantity,
                srd.cost,
                srd.amount,
                srd.discount_percentage,
                srd.discount_amount,
                srd.net_amount_before_sales_tax,
                srd.net_amount,
                srd.sales_tax_percentage,
                srd.sales_tax_amount,
                srd.output_vat_id,
                srd.discount_account_id,
                srd.cogs_account_id,
                srd.income_account_id,
                srd.asset_account_id,
                i.id AS item_id,
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
            FROM sales_return_details srd
            LEFT JOIN items i ON srd.item_id = i.id
            LEFT JOIN uom um ON i.item_uom_id = um.id
            WHERE srd.sales_return_id = :sales_return_id
        ');

        $stmt->bindParam(':sales_return_id', $sales_return_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $details = [];
        while ($row = $stmt->fetch()) {
            $details[] = $row; // Directly use the fetched row as it is already formatted correctly
        }

        return $details;
    }

    public static function getTotalCount()
    {
        global $connection;

        $stmt = $connection->query('SELECT COUNT(*) as total_count FROM sales_return');
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total_count'];
    }

    public static function getUnpaidCount()
    {
        global $connection;

        $stmt = $connection->prepare('SELECT COUNT(*) as unpaid_count FROM sales_return WHERE status = 0');
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['unpaid_count'];
    }

    public static function getPaidCount()
    {
        global $connection;

        $stmt = $connection->prepare('SELECT COUNT(*) as paid_count FROM sales_return WHERE status = 1');
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['paid_count'];
    }

    public static function getOverdueCount()
    {
        global $connection;

        $currentDate = date('Y-m-d'); // Get current date in 'YYYY-MM-DD' format

        $stmt = $connection->prepare('
        SELECT COUNT(*) as overdue_count 
        FROM sales_return 
        WHERE sales_return_due_date < :currentDate 
        AND status = 0 
        AND balance_due > 0
    ');

        $stmt->bindParam(':currentDate', $currentDate);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['overdue_count'];
    }

    public static function getByCustomerId($customer_id)
    {
        global $connection;

        $stmt = $connection->prepare('
        SELECT si.*, c.customer_name
        FROM sales_return si
        INNER JOIN customers c ON si.customer_id = c.id
        WHERE si.customer_id = :customer_id
    ');
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $stmt->execute();

        $sales_returns = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sales_returns[] = new SalesReturn($row);
        }

        return $sales_returns;
    }

    public static function getCashSalesByCustomerId($customer_id, $sales_return_account = null)
    {
        global $connection;

        // Prepare the base SQL query
        $sql = '
                  SELECT si.*, c.customer_name
        FROM sales_return si
        INNER JOIN customers c ON si.customer_id = c.id
        INNER JOIN chart_of_account coa ON si.sales_return_account_id = coa.id
        INNER JOIN account_types at ON coa.account_type_id = at.id
        WHERE si.customer_id = :customer_id
        AND si.status = 0 
        AND si.status = 1
        AND at.name = "Accounts Receivable"
        ';

        // If sales_return_account is provided, add it to the WHERE clause
        if ($sales_return_account !== null) {
            $sql .= ' AND si.sales_return_account = :sales_return_account';
        }

        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);

        // Bind sales_return_account parameter if provided
        if ($sales_return_account !== null) {
            $stmt->bindParam(':sales_return_account', $sales_return_account, PDO::PARAM_STR);
        }

        $stmt->execute();

        $sales_returns = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $sales_returns[] = new SalesReturn($row);
        }

        return $sales_returns;
    }

    // GET LAST sales_return_NO 
    public static function getLastsales_returnNo() {
        global $connection;
    
        try {
            // Prepare and execute the query to get the highest sales_return number, ignoring null or empty values
            $stmt = $connection->prepare("
                SELECT sales_return_number 
                FROM sales_return 
                WHERE sales_return_number IS NOT NULL AND sales_return_number <> '' 
                ORDER BY sales_return_number DESC 
                LIMIT 1
            ");
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetch();
    
            // Extract the numeric part of the last sales_return number
            if ($result) {
                $latestNo = $result['sales_return_number'];
                // Assuming the format is 'INV' followed by digits
                $numericPart = intval(substr($latestNo, 2)); // 'SI' is 3 characters
                $newNo = $numericPart + 1;
            } else {
                // If no valid sales_return number exists, start with 1
                $newNo = 1;
            }
    
            // Format the new number with leading zeros
            $newsales_returnNo = 'SR' . str_pad($newNo, 9, '0', STR_PAD_LEFT);
    
            return $newsales_returnNo;
        } catch (PDOException $e) {
            // Handle potential exceptions
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }

    public static function addDraft(
        $sales_return_date,
        $sales_return_account_id,
        $customer_po,
        $so_no,
        $rep,
        $sales_return_due_date,
        $customer_id,
        $payment_method,
        $location,
        $terms,
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
            $connection->beginTransaction();
    
            // Insert into sales_return table
            $sql = "INSERT INTO sales_return (
                sales_return_date, sales_return_account_id, customer_po, so_no, rep, sales_return_due_date, customer_id,
                payment_method, location, terms, memo, gross_amount, discount_amount,
                net_amount_due, vat_amount, vatable_amount, zero_rated_amount, vat_exempt_amount,
                tax_withheld_percentage, tax_withheld_amount,
                total_amount_due, status
            ) VALUES (
                :sales_return_date, :sales_return_account_id, :customer_po, :so_no, :rep, :sales_return_due_date, :customer_id,
                :payment_method, :location, :terms, :memo, :gross_amount, :discount_amount,
                :net_amount_due, :vat_amount, :vatable_amount, :zero_rated_amount, :vat_exempt_amount,
                :tax_withheld_percentage, :tax_withheld_amount,
                :total_amount_due, :status
            )";
    
            $stmt = $connection->prepare($sql);
            $stmt->bindParam(':sales_return_date', $sales_return_date);
            $stmt->bindParam(':sales_return_account_id', $sales_return_account_id);
            $stmt->bindParam(':customer_po', $customer_po);
            $stmt->bindParam(':so_no', $so_no);
            $stmt->bindParam(':rep', $rep);
            $stmt->bindParam(':sales_return_due_date', $sales_return_due_date);
            $stmt->bindParam(':customer_id', $customer_id);
            $stmt->bindParam(':payment_method', $payment_method);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':terms', $terms);
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
            $stmt->bindValue(':status', 4, PDO::PARAM_INT); // Set status to 0 for draft
    
            $stmt->execute();
    
            // Retrieve the last inserted ID
            $sales_return_id = $connection->lastInsertId();
    
            // Insert sales_return items
            if (!empty($items)) {
                $itemSql = "INSERT INTO sales_return_details (
                    sales_return_id, item_id, quantity, cost, amount, discount_percentage, discount_amount, net_amount_before_sales_tax, net_amount, sales_tax_percentage, sales_tax_amount, output_vat_id,  discount_account_id,  cogs_account_id,  income_account_id,  asset_account_id
                ) VALUES (
                    :sales_return_id, :item_id, :quantity, :cost, :amount, :discount_percentage, :discount_amount, :net_amount_before_sales_tax, :net_amount, :sales_tax_percentage, :sales_tax_amount, :output_vat_id, :discount_account_id, :cogs_account_id, :income_account_id, :asset_account_id
                )";
    
                $itemStmt = $connection->prepare($itemSql);
    
                foreach ($items as $item) {
                    $itemStmt->bindParam(':sales_return_id', $sales_return_id);
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
    
            $connection->commit();
            return true;
    
        } catch (Exception $ex) {
            $connection->rollBack();
            error_log('Error in addDraft: ' . $ex->getMessage());
            throw $ex;
        }
    }

    public static function void($id)
    {
        global $connection;
    
        try {
            $connection->beginTransaction();
            
            // Update the status to 3 (void) in the sales_invoice table
            $stmt = $connection->prepare("UPDATE sales_return SET status = 3 WHERE id = :id");
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
    
    public static function updateDraft(
        $id,  $sales_return_number,  $sales_return_account_id,   $customer_name,  $customer_id,  $tax_withheld_account_id,  $tax_withheld_amount,  $total_amount_due,  $gross_amount,  $sales_return_date, $sales_return_due_date, $customer_po, $so_no, $rep, $payment_method, $location, $terms, $memo, $output_vat_ids, $vat_amount, $zero_rated_amount, $vat_exempt_amount, $vatable_amount, $tax_withheld_percentage, $discount_amount, $created_by, $items
    ) {
        global $connection;
    
        try {
            $connection->beginTransaction();
    
            $stmt = $connection->prepare("
                UPDATE sales_return 
                SET sales_return_number = :sales_return_number,
                    sales_return_date = :sales_return_date,
                    sales_return_account_id = :sales_return_account_id,
                    sales_return_due_date = :sales_return_due_date,
                    customer_po = :customer_po,
                    so_no = :so_no,
                    rep = :rep,
                    customer_id = :customer_id,
                    payment_method = :payment_method,
                    location = :location,
                    terms = :terms,
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
            $stmt->bindParam(':sales_return_number', $sales_return_number, PDO::PARAM_STR);
            $stmt->bindParam(':sales_return_date', $sales_return_date, PDO::PARAM_STR);
            $stmt->bindParam(':sales_return_account_id', $sales_return_account_id, PDO::PARAM_INT);
            $stmt->bindParam(':sales_return_due_date', $sales_return_due_date, PDO::PARAM_STR);
            $stmt->bindParam(':customer_po', $customer_po, PDO::PARAM_STR);
            $stmt->bindParam(':so_no', $so_no, PDO::PARAM_STR);
            $stmt->bindParam(':rep', $rep, PDO::PARAM_STR);
            $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
            $stmt->bindParam(':payment_method', $payment_method, PDO::PARAM_STR);
            $stmt->bindParam(':location', $location, PDO::PARAM_STR);
            $stmt->bindParam(':terms', $terms, PDO::PARAM_STR);
            $stmt->bindParam(':memo', $memo, PDO::PARAM_STR);
            $stmt->bindParam(':gross_amount', $gross_amount, PDO::PARAM_STR);
            $stmt->bindParam(':discount_amount', $discount_amount, PDO::PARAM_STR);
            $stmt->bindParam(':net_amount_due', $total_amount_due, PDO::PARAM_STR);
            $stmt->bindParam(':vat_amount', $vat_amount, PDO::PARAM_STR);
            $stmt->bindParam(':vatable_amount', $vatable_amount, PDO::PARAM_STR);
            $stmt->bindParam(':zero_rated_amount', $zero_rated_amount, PDO::PARAM_STR);
            $stmt->bindParam(':vat_exempt_amount', $vat_exempt_amount, PDO::PARAM_STR);
            $stmt->bindParam(':tax_withheld_percentage', $tax_withheld_percentage, PDO::PARAM_STR);
            $stmt->bindParam(':tax_withheld_amount', $tax_withheld_amount, PDO::PARAM_STR);
            $stmt->bindParam(':total_amount_due', $total_amount_due, PDO::PARAM_STR);
            
            // Execute the statement
            $result = $stmt->execute();
    
            if ($result) {
    
                // Delete existing sales return details for the given sales_return_id
                $stmt = $connection->prepare("DELETE FROM sales_return_details WHERE sales_return_id = ?");
                $stmt->execute([$id]);
    
                // Prepare SQL for inserting new sales return details
                $stmt = $connection->prepare("
                    INSERT INTO sales_return_details (
                        sales_return_id, item_id, quantity, cost, amount, discount_percentage, discount_amount,
                        net_amount_before_sales_tax, net_amount, sales_tax_percentage, sales_tax_amount,
                        output_vat_id, discount_account_id, cogs_account_id, income_account_id, asset_account_id
                    ) VALUES (
                        :sales_return_id, :item_id, :quantity, :cost, :amount, :discount_percentage, :discount_amount,
                        :net_amount_before_sales_tax, :net_amount, :sales_tax_percentage, :sales_tax_amount,
                        :output_vat_id, :discount_account_id, :cogs_account_id, :income_account_id, :asset_account_id
                    )
                ");
    
                // Insert each item into the sales_return_details table
                foreach ($items as $item) {
                    $stmt->execute([
                        ':sales_return_id' => $id,
                        ':item_id' => $item['item_id'],
                        ':quantity' => $item['quantity'],
                        ':cost' => $item['cost'],
                        ':amount' => $item['amount'],
                        ':discount_percentage' => $item['discount_percentage'],
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
                return [
                    'success' => true,
                    'invoiceId' => $id
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
        $id,  $sales_return_number,  $sales_return_account_id,   $customer_name,  $customer_id,  $tax_withheld_account_id,  $tax_withheld_amount,  $total_amount_due,  $gross_amount,  $sales_return_date, $sales_return_due_date, $customer_po, $so_no, $rep, $payment_method, $location, $terms, $memo, $output_vat_ids, $vat_amount, $zero_rated_amount, $vat_exempt_amount, $vatable_amount, $tax_withheld_percentage, $discount_amount, $created_by, $items
    ) {
        global $connection;
    
        try {
            $connection->beginTransaction();
    
            // Check if the sales return account is of the type 'Bank' or 'Other Current Assets'
            $stmt = $connection->prepare("
                SELECT at.name
                FROM chart_of_account coa
                JOIN account_types at ON coa.account_type_id = at.id
                WHERE coa.id = ?
            ");
            $stmt->execute([$sales_return_account_id]);
            $account_type_name = $stmt->fetchColumn();
    
            // Determine the invoice status and balance due based on the account type
            $status = 0;
            $balance_due = $total_amount_due;
            if ($account_type_name == 'Bank' || $account_type_name == 'Other Current Assets') {
                $status = 1;
                $balance_due = 0;
            }
    
            $stmt = $connection->prepare("
                UPDATE sales_return 
                SET sales_return_number = :sales_return_number,
                    sales_return_date = :sales_return_date,
                    sales_return_account_id = :sales_return_account_id,
                    sales_return_due_date = :sales_return_due_date,
                    customer_po = :customer_po,
                    so_no = :so_no,
                    rep = :rep,
                    customer_id = :customer_id,
                    payment_method = :payment_method,
                    location = :location,
                    terms = :terms,
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
                    status = :status,
                    balance_due = :balance_due
                WHERE id = :id
            ");
            
            // Bind the parameters
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':sales_return_number', $sales_return_number, PDO::PARAM_STR);
            $stmt->bindParam(':sales_return_date', $sales_return_date, PDO::PARAM_STR);
            $stmt->bindParam(':sales_return_account_id', $sales_return_account_id, PDO::PARAM_INT);
            $stmt->bindParam(':sales_return_due_date', $sales_return_due_date, PDO::PARAM_STR);
            $stmt->bindParam(':customer_po', $customer_po, PDO::PARAM_STR);
            $stmt->bindParam(':so_no', $so_no, PDO::PARAM_STR);
            $stmt->bindParam(':rep', $rep, PDO::PARAM_STR);
            $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
            $stmt->bindParam(':payment_method', $payment_method, PDO::PARAM_STR);
            $stmt->bindParam(':location', $location, PDO::PARAM_STR);
            $stmt->bindParam(':terms', $terms, PDO::PARAM_STR);
            $stmt->bindParam(':memo', $memo, PDO::PARAM_STR);
            $stmt->bindParam(':gross_amount', $gross_amount, PDO::PARAM_STR);
            $stmt->bindParam(':discount_amount', $discount_amount, PDO::PARAM_STR);
            $stmt->bindParam(':net_amount_due', $total_amount_due, PDO::PARAM_STR);
            $stmt->bindParam(':vat_amount', $vat_amount, PDO::PARAM_STR);
            $stmt->bindParam(':vatable_amount', $vatable_amount, PDO::PARAM_STR);
            $stmt->bindParam(':zero_rated_amount', $zero_rated_amount, PDO::PARAM_STR);
            $stmt->bindParam(':vat_exempt_amount', $vat_exempt_amount, PDO::PARAM_STR);
            $stmt->bindParam(':tax_withheld_percentage', $tax_withheld_percentage, PDO::PARAM_STR);
            $stmt->bindParam(':tax_withheld_amount', $tax_withheld_amount, PDO::PARAM_STR);
            $stmt->bindParam(':total_amount_due', $total_amount_due, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->bindParam(':balance_due', $balance_due, PDO::PARAM_STR);
            
            // Execute the statement
            $result = $stmt->execute();
    
            if ($result) {
    
                // Delete existing sales return details for the given sales_return_id
                $stmt = $connection->prepare("DELETE FROM sales_return_details WHERE sales_return_id = ?");
                $stmt->execute([$id]);
    
                // Prepare SQL for inserting new sales return details
                $stmt = $connection->prepare("
                    INSERT INTO sales_return_details (
                        sales_return_id, item_id, quantity, cost, amount, discount_percentage, discount_amount,
                        net_amount_before_sales_tax, net_amount, sales_tax_percentage, sales_tax_amount,
                        output_vat_id, discount_account_id, cogs_account_id, income_account_id, asset_account_id
                    ) VALUES (
                        :sales_return_id, :item_id, :quantity, :cost, :amount, :discount_percentage, :discount_amount,
                        :net_amount_before_sales_tax, :net_amount, :sales_tax_percentage, :sales_tax_amount,
                        :output_vat_id, :discount_account_id, :cogs_account_id, :income_account_id, :asset_account_id
                    )
                ");
    
                // Insert each item into the sales_return_details table
                foreach ($items as $item) {
                    $stmt->execute([
                        ':sales_return_id' => $id,
                        ':item_id' => $item['item_id'],
                        ':quantity' => $item['quantity'],
                        ':cost' => $item['cost'],
                        ':amount' => $item['amount'],
                        ':discount_percentage' => $item['discount_percentage'],
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
    
                $transaction_type = "Sales Return";


                // Fetch sales return details
                $stmt = $connection->prepare("
                    SELECT 
                        srd.*,
                        i.item_name
                    FROM 
                        sales_return_details srd
                    JOIN 
                        items i ON srd.item_id = i.id
                    WHERE 
                        srd.sales_return_id = :sales_return_id
                ");
                $stmt->bindParam(':sales_return_id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $invoiceDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

                // Fetch wtax_account_id based on tax_withheld_percentage
                $stmt = $connection->prepare("
                    SELECT wtax_account_id 
                    FROM wtax 
                    WHERE id = :id
                ");
                $stmt->bindParam(':id', $tax_withheld_percentage);
                $stmt->execute();
                $wtax_account_id = $stmt->fetchColumn();

                if (!$wtax_account_id) {
                    throw new Exception("WTax account ID not found for percentage: $tax_withheld_percentage.");
                }

               
    
                // Log the main sales return transaction
                self::logAuditTrail(
                    $id,
                    $transaction_type,
                    self::getCurrentDate(),
                    $sales_return_number,
                    $location,
                    $customer_name,
                    null,
                    null,
                    $sales_return_account_id,
                    $gross_amount,
                    0.00,
                    $created_by
                );
    
                // Log tax withheld transaction
                self::logAuditTrail(
                    $id,
                    $transaction_type,
                    self::getCurrentDate(),
                    $sales_return_number,
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
                foreach ($invoiceDetails as $item) {
                    // Log VAT for this item
                    if (!empty($item['output_vat_id']) && $item['sales_tax_amount'] > 0) {
                        self::logAuditTrail(
                            $id,
                            $transaction_type,
                            self::getCurrentDate(),
                            $sales_return_number,
                            $location,
                            $customer_name,
                            $item['item_name'],
                            $item['quantity'],
                            $item['output_vat_id'],
                            0.00,
                            $item['sales_tax_amount'],
                            $created_by
                        );
                    }
                    // Log discount for this item
                    if (!empty($item['discount_account_id']) && $item['discount_amount'] > 0) {
                        self::logAuditTrail(
                            $id,
                            $transaction_type,
                            self::getCurrentDate(),
                            $sales_return_number,
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
                            $id,
                            $transaction_type,
                            self::getCurrentDate(),
                            $sales_return_number,
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
                            $id,
                            $transaction_type,
                            self::getCurrentDate(),
                            $sales_return_number,
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
                            $id,
                            $transaction_type,
                            self::getCurrentDate(),
                            $sales_return_number,
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
                }
    
                // Log the main invoice transaction discount credit
                self::logAuditTrail(
                    $id,
                    $transaction_type,
                    self::getCurrentDate(),
                    $sales_return_number,
                    $location,
                    $customer_name,
                    null,
                    null,
                    $sales_return_account_id,
                    0.00,
                    $discount_amount,
                    $created_by
                );

                // Log the main invoice transaction wtax credit
                self::logAuditTrail(
                    $id,
                    $transaction_type,
                    self::getCurrentDate(),
                    $sales_return_number,
                    $location,
                    $customer_name,
                    null,
                    null,
                    $sales_return_account_id,
                    0.00,
                    $tax_withheld_amount,
                    $created_by
                );
    
                // // If it's an Accounts Receivable, update the customer's balance
                // if ($account_type_name == 'Accounts Receivable') {
                //     $stmt = $connection->prepare("UPDATE customers SET credit_balance = credit_balance + ?, total_invoiced = total_invoiced + ? WHERE id = ?");
                //     $stmt->execute([$total_amount_due, $total_amount_due, $customer_id]);
                // }
    
                $connection->commit();
                return [
                    'success' => true,
                    'invoiceId' => $id
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
    // Method to get the current date
    private static function getCurrentDate() {
        return date('Y-m-d');
    }
    
}

