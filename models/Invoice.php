<?php
require_once __DIR__ . '/../_init.php';

class Invoice
{
    public $id;
    public $invoice_account_id;
    public $invoice_number;
    public $customer_po;
    public $so_no;
    public $rep;
    public $invoice_date;
    public $invoice_account;
    public $invoice_due_date;
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
    public $invoice_status;
    public $status;
    public $print_status;
    public $balance_due;
    private $isDraft = false;
    public $details = [];

    private static $cache = null;

    public function __construct($invoiceData)
    {
        $this->id = $invoiceData['id'] ?? null;
        $this->invoice_account_id = $invoiceData['invoice_account_id'] ?? null;
        $this->invoice_number = $invoiceData['invoice_number'] ?? null;
        $this->invoice_date = $invoiceData['invoice_date'] ?? null;
        $this->customer_po = $invoiceData['customer_po'] ?? null;
        $this->so_no = $invoiceData['so_no'] ?? null;
        $this->rep = $invoiceData['rep'] ?? null;
        $this->invoice_account = $invoiceData['invoice_account'] ?? null;
        $this->invoice_due_date = $invoiceData['invoice_due_date'] ?? null;
        $this->customer_id = $invoiceData['customer_id'] ?? null;
        $this->customer_name = $invoiceData['customer_name'] ?? null;
        $this->customer_tin = $invoiceData['customer_tin'] ?? null;
        $this->customer_email = $invoiceData['customer_email'] ?? null;
        $this->shipping_address = $invoiceData['shipping_address'] ?? null;
        $this->billing_address = $invoiceData['billing_address'] ?? null;
        $this->business_style = $invoiceData['business_style'] ?? null;
        $this->payment_method = $invoiceData['payment_method'] ?? null;
        $this->location = $invoiceData['location'] ?? null;
        $this->terms = $invoiceData['terms'] ?? null;
        $this->memo = $invoiceData['memo'] ?? null;
        $this->gross_amount = $invoiceData['gross_amount'] ?? null;
        $this->discount_amount = $invoiceData['invoice_discount_amount'] ?? null;
        $this->net_amount_due = $invoiceData['net_amount_due'] ?? null;
        $this->vat_amount = $invoiceData['vat_amount'] ?? null;
        $this->vatable_amount = $invoiceData['vatable_amount'] ?? null;
        $this->zero_rated_amount = $invoiceData['zero_rated_amount'] ?? null;
        $this->vat_exempt_amount = $invoiceData['vat_exempt_amount'] ?? null;
        $this->tax_withheld_percentage = $invoiceData['tax_withheld_percentage'] ?? null;
        $this->tax_withheld_amount = $invoiceData['tax_withheld_amount'] ?? null;
        $this->total_amount_due = $invoiceData['total_amount_due'] ?? null;
        $this->invoice_status = $invoiceData['invoice_status'] ?? null;
        $this->status = $invoiceData['status'] ?? null;
        $this->print_status = $invoiceData['print_status'] ?? null;
        $this->balance_due = $invoiceData['balance_due'] ?? null;

        // Initialize details as an empty array
        $this->details = [];

        // Populate other properties as before...

        // Optionally, you can populate details if provided in $formData
        if (isset($invoiceData['details'])) {
            foreach ($invoiceData['details'] as $detail) {
                // Push each detail to the details array
                $this->details[] = $detail;
            }
        }
    }

    public static function add(
        $invoice_number,
        $invoice_date,
        $invoice_account_id,
        $customer_po,
        $so_no,
        $rep,
        $discount_account_ids,
        $output_vat_ids,
        $invoice_due_date,
        $customer_id,
        $customer_name,
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
        $tax_withheld_account_id,
        $total_amount_due,
        $items,
        $created_by
    ) {
        global $connection;

        try {
            global $connection;

            // Start a database transaction to ensure data integrity
            $connection->beginTransaction();

            $transaction_type = "Invoice";

            // Check if the invoice account is an Accounts Receivable or Undeposited Funds type
            $stmt = $connection->prepare("
                    SELECT at.name
                    FROM chart_of_account coa
                    JOIN account_types at ON coa.account_type_id = at.id
                    WHERE coa.id = ?
                ");
            $stmt->execute([$invoice_account_id]);
            $account_type_name = $stmt->fetchColumn();

            // Determine the invoice status and balance due based on the account type
            $invoice_status = 0;
            $balance_due = $total_amount_due;
            if ($account_type_name == 'Bank' || $account_type_name == 'Other Current Assets') {
                $invoice_status = 1;
                $balance_due = 0;
            }

            // If it's an Accounts Receivable, update the customer's balance
            if ($account_type_name == 'Accounts Receivable') {
                $stmt = $connection->prepare("UPDATE customers SET credit_balance = credit_balance + ?, total_invoiced = total_invoiced + ? WHERE id = ?");
                $stmt->execute([$total_amount_due, $total_amount_due, $customer_id]);
            }

            // Prepare and execute SQL to insert the main invoice record
            $stmt = $connection->prepare("INSERT INTO sales_invoice (
                invoice_number, invoice_date, invoice_account_id, invoice_due_date,
                customer_po, so_no, rep, 
                customer_id, payment_method, location, terms, memo, gross_amount,
                discount_amount, net_amount_due, vat_amount, vatable_amount,
                zero_rated_amount, vat_exempt_amount, tax_withheld_percentage,
                tax_withheld_amount, total_amount_due, invoice_status, balance_due
            ) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");

            $stmt->execute([
                $invoice_number,
                $invoice_date,
                $invoice_account_id,
                $invoice_due_date,
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
                $invoice_status,
                $balance_due
            ]);

            // Get the ID of the newly inserted invoice
            $invoice_id = $connection->lastInsertId();

            // Log the main invoice transaction in the audit trail
            self::logAuditTrail(
                $invoice_id,
                $transaction_type,
                $invoice_date,
                $invoice_number,
                $location,
                $customer_name,
                null,
                null,
                $invoice_account_id,
                $gross_amount,
                0.00,
                $created_by
            );

            // Log tax withheld transaction
            self::logAuditTrail(
                $invoice_id,
                $transaction_type,
                $invoice_date,
                $invoice_number,
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
                $invoice_id,
                $transaction_type,
                $invoice_date,
                $invoice_number,
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


            // Process each item in the invoice
            foreach ($items as $item) {
                // Add the individual invoice item to the database
                self::addInvoiceItems(
                    $invoice_id,
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
                if (!empty($item['discount_account_id'])) {
                    self::logAuditTrail(
                        $invoice_id,
                        $transaction_type,
                        $invoice_date,
                        $invoice_number,
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
                        $invoice_id,
                        $transaction_type,
                        $invoice_date,
                        $invoice_number,
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
                        $invoice_id,
                        $transaction_type,
                        $invoice_date,
                        $invoice_number,
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
                        $invoice_id,
                        $transaction_type,
                        $invoice_date,
                        $invoice_number,
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

                // IINVENTORY VALUATION
                self::insert_inventory_valuation(
                    $transaction_type,
                    $invoice_id,
                    $invoice_number,
                    $invoice_date,
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

            // Log the main invoice transaction discount credit
            self::logAuditTrail(
                $invoice_id,
                $transaction_type,
                $invoice_date,
                $invoice_number,
                $location,
                $customer_name,
                null,
                null,
                $invoice_account_id,
                0.00,
                $total_discount_amount,
                $created_by
            );

            // Log the main invoice transaction wtax credit
            self::logAuditTrail(
                $invoice_id,
                $transaction_type,
                $invoice_date,
                $invoice_number,
                $location,
                $customer_name,
                null,
                null,
                $invoice_account_id,
                0.00,
                $tax_withheld_amount,
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

    public static function addInvoiceItems(
        $invoice_id,
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
    ) {
        global $connection;

        // Use NULL if the value is an empty string
        $output_vat_id = !empty($output_vat_id) ? $output_vat_id : NULL;
        $discount_account_id = !empty($discount_account_id) ? $discount_account_id : NULL;
        $cogs_account_id = !empty($cogs_account_id) ? $cogs_account_id : NULL;
        $income_account_id = !empty($income_account_id) ? $income_account_id : NULL;
        $asset_account_id = !empty($asset_account_id) ? $asset_account_id : NULL;


        $stmt = $connection->prepare("INSERT INTO sales_invoice_details (
                invoice_id, 
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
            $invoice_id,
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

    public static function findByInvoiceNo($invoice_number)
    {
        global $connection;

        $stmt = $connection->prepare('SELECT * FROM sales_invoice WHERE invoice_number = :invoice_number');
        $stmt->bindParam("invoice_number", $invoice_number);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new Invoice($result[0]);
        }

        return null;
    }

    public static function findInvoiceById($id)
    {
        global $connection;

        $stmt = $connection->prepare('SELECT * FROM sales_invoice WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);  // Use ':id' as the placeholder, and bind it as an integer
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetch();  // Use fetch() instead of fetchAll() since you expect a single row

        if ($result) {
            return new Invoice($result);  // Assuming the Invoice class constructor can handle the array
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

        $stmt = $connection->query("SELECT MAX(id) AS last_transaction_id FROM sales_invoice");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['last_transaction_id'] ?? null;
    }


    public static function all()
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                si.*, 
                c.customer_name, 
                c.billing_address
            FROM sales_invoice si
            INNER JOIN customers c ON si.customer_id = c.id
        ');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $invoices = [];
        while ($row = $stmt->fetch()) {
            $invoice = [
                'id' => $row['id'],
                'invoice_number' => $row['invoice_number'],
                'invoice_date' => $row['invoice_date'],
                'customer_name' => $row['customer_name'],
                'billing_address' => $row['billing_address'],
                'memo' => $row['memo'],
                'total_amount_due' => $row['total_amount_due'],
                'invoice_status' => $row['invoice_status'],
                'status' => $row['status'],
                'balance_due' => $row['balance_due']
            ];
            $invoices[] = new Invoice($invoice);
        }

        return $invoices;
    }

    public static function find($id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                si.id,
                si.invoice_number,
                si.invoice_date,
                si.invoice_account_id,
                si.customer_po,
                si.so_no,
                si.rep,
                si.invoice_due_date,
                si.payment_method,
                si.location,
                si.terms,
                si.memo,
                si.gross_amount,
                si.discount_amount as invoice_discount_amount,
                si.net_amount_due,
                si.vat_amount,
                si.vatable_amount,
                si.zero_rated_amount,
                si.vat_exempt_amount,
                si.tax_withheld_percentage,
                si.tax_withheld_amount,
                si.total_amount_due,
                si.invoice_status,
                si.status as invoice_status_code,
                si.print_status,
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
            FROM sales_invoice si
            INNER JOIN customers c ON si.customer_id = c.id
            WHERE si.id = :id
        ');

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $invoiceData = $stmt->fetch();

        if (!$invoiceData) {
            return null;
        }

        $invoiceData['details'] = self::getInvoiceDetails($id);

        return new Invoice($invoiceData);
    }

    public static function getInvoiceDetails($invoice_id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                sid.id,
                sid.invoice_id,
                sid.quantity,
                sid.cost,
                sid.amount,
                sid.discount_percentage,
                sid.discount_amount,
                sid.net_amount_before_sales_tax,
                sid.net_amount,
                sid.sales_tax_percentage,
                sid.sales_tax_amount,
                sid.output_vat_id,
                sid.cogs_account_id,
                sid.income_account_id,
                sid.asset_account_id,
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
            FROM sales_invoice_details sid
            LEFT JOIN items i ON sid.item_id = i.id
            LEFT JOIN uom um ON i.item_uom_id = um.id
            WHERE sid.invoice_id = :invoice_id
        ');

        $stmt->bindParam(':invoice_id', $invoice_id, PDO::PARAM_INT);
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

        $stmt = $connection->query('SELECT COUNT(*) as total_count FROM sales_invoice');
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total_count'];
    }

    public static function getUnpaidCount()
    {
        global $connection;

        $stmt = $connection->prepare('SELECT COUNT(*) as unpaid_count FROM sales_invoice WHERE invoice_status = 0');
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['unpaid_count'];
    }

    public static function getPaidCount()
    {
        global $connection;

        $stmt = $connection->prepare('SELECT COUNT(*) as paid_count FROM sales_invoice WHERE invoice_status = 1');
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
        FROM sales_invoice 
        WHERE invoice_due_date < :currentDate 
        AND invoice_status = 0 
        AND balance_due > 0');

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
        FROM sales_invoice si
        INNER JOIN customers c ON si.customer_id = c.id
        WHERE si.customer_id = :customer_id');
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
        $stmt->execute();

        $invoices = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $invoices[] = new Invoice($row);
        }

        return $invoices;
    }

    public static function getCashSalesByCustomerId($customer_id, $invoice_account = null)
    {
        global $connection;

        // Prepare the base SQL query
        $sql = '
                  SELECT si.*, c.customer_name
        FROM sales_invoice si
        INNER JOIN customers c ON si.customer_id = c.id
        INNER JOIN chart_of_account coa ON si.invoice_account_id = coa.id
        INNER JOIN account_types at ON coa.account_type_id = at.id
        WHERE si.customer_id = :customer_id
        AND (si.invoice_status = 0 OR si.invoice_status = 2)
        AND si.status = 1
        AND at.name = "Accounts Receivable"
        ';

        // If invoice_account is provided, add it to the WHERE clause
        if ($invoice_account !== null) {
            $sql .= ' AND si.invoice_account = :invoice_account';
        }

        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);

        // Bind invoice_account parameter if provided
        if ($invoice_account !== null) {
            $stmt->bindParam(':invoice_account', $invoice_account, PDO::PARAM_STR);
        }

        $stmt->execute();

        $invoices = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $invoices[] = new Invoice($row);
        }

        return $invoices;
    }


    public static function update(
        $invoice_id,
        $invoice_number,
        $invoice_date,
        $invoice_account_id,
        $invoice_due_date,
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
        $total_amount_due
    ) {
        // Get database connection
        global $connection;

        // Start transaction
        $connection->beginTransaction();

        try {
            // Prepare and execute the update statement
            $stmt = $connection->prepare("
                UPDATE sales_invoice
                SET 
                    invoice_number = :invoice_number,
                    invoice_date = :invoice_date,
                    invoice_account_id = :invoice_account_id,
                    invoice_due_date = :invoice_due_date,
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
                    total_amount_due = :total_amount_due
                WHERE id = :invoice_id
            ");
            $stmt->execute([
                ':invoice_id' => $invoice_id,
                ':invoice_number' => $invoice_number,
                ':invoice_date' => $invoice_date,
                ':invoice_account_id' => $invoice_account_id,
                ':invoice_due_date' => $invoice_due_date,
                ':customer_id' => $customer_id,
                ':payment_method' => $payment_method,
                ':location' => $location,
                ':terms' => $terms,
                ':memo' => $memo,
                ':gross_amount' => $gross_amount,
                ':discount_amount' => $discount_amount,
                ':net_amount_due' => $net_amount_due,
                ':vat_amount' => $vat_amount,
                ':vatable_amount' => $vatable_amount,
                ':zero_rated_amount' => $zero_rated_amount,
                ':vat_exempt_amount' => $vat_exempt_amount,
                ':tax_withheld_percentage' => $tax_withheld_percentage,
                ':tax_withheld_amount' => $tax_withheld_amount,
                ':total_amount_due' => $total_amount_due,
            ]);

            // Commit transaction
            $connection->commit();
        } catch (Exception $e) {
            // Rollback transaction if something failed
            $connection->rollBack();
            error_log("Update failed: " . $e->getMessage()); // Log error message
            throw $e;
        }
    }

    public static function updateDetails($invoice_id, $item_data)
    {
        // Get database connection
        global $connection;

        // Start transaction
        $connection->beginTransaction();

        try {
            // Delete existing details for the given invoice_id
            $stmt = $connection->prepare("DELETE FROM sales_invoice_details WHERE invoice_id = :invoice_id");
            $stmt->execute([':invoice_id' => $invoice_id]);

            // Insert new details into sales_invoice_details
            foreach ($item_data as $item) {
                $stmt = $connection->prepare("
                    INSERT INTO sales_invoice_details (invoice_id, item_id, quantity, cost, amount, discount_percentage, discount_amount, net_amount_before_sales_tax, net_amount, sales_tax_percentage, sales_tax_amount)
                    VALUES (:invoice_id, :item_id, :quantity, :cost, :amount, :discount_percentage, :discount_amount, :net_amount_before_sales_tax, :net_amount, :sales_tax_percentage, :sales_tax_amount)
                ");
                $stmt->execute([
                    ':invoice_id' => $invoice_id,
                    ':item_id' => $item['item_id'],
                    ':quantity' => $item['quantity'],
                    ':cost' => $item['cost'],
                    ':amount' => $item['amount'],
                    ':discount_percentage' => $item['discount_percentage'],
                    ':discount_amount' => $item['discount_amount'],
                    ':net_amount_before_sales_tax' => $item['net_amount_before_sales_tax'],
                    ':net_amount' => $item['net_amount'],
                    ':sales_tax_percentage' => $item['sales_tax_percentage'],
                    ':sales_tax_amount' => $item['sales_tax_amount']
                ]);
            }

            // Commit transaction
            $connection->commit();
        } catch (Exception $e) {
            // Rollback transaction if something failed
            $connection->rollBack();
            error_log("Update details failed: " . $e->getMessage()); // Log error message
            throw $e;
        }
    }

    // GET LAST INVOICE_NO 
    public static function getLastInvoiceNo()
    {
        global $connection;

        try {
            // Prepare and execute the query to get the highest invoice number, ignoring null or empty values
            $stmt = $connection->prepare("
                SELECT invoice_number 
                FROM sales_invoice 
                WHERE invoice_number IS NOT NULL AND invoice_number <> '' 
                ORDER BY invoice_number DESC 
                LIMIT 1
            ");
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetch();

            // Extract the numeric part of the last invoice number
            if ($result) {
                $latestNo = $result['invoice_number'];
                // Assuming the format is 'INV' followed by digits
                $numericPart = intval(substr($latestNo, 2)); // 'SI' is 3 characters
                $newNo = $numericPart + 1;
            } else {
                // If no valid invoice number exists, start with 1
                $newNo = 1;
            }

            // Format the new number with leading zeros
            $newInvoiceNo = 'SI' . str_pad($newNo, 9, '0', STR_PAD_LEFT);

            return $newInvoiceNo;
        } catch (PDOException $e) {
            // Handle potential exceptions
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }

    public static function addDraft(
        $invoice_date,
        $invoice_account_id,
        $customer_po,
        $so_no,
        $rep,
        $invoice_due_date,
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

            // Insert into sales_invoice table
            $sql = "INSERT INTO sales_invoice (
                invoice_date, invoice_account_id, customer_po, so_no, rep, invoice_due_date, customer_id,
                payment_method, location, terms, memo, gross_amount, discount_amount,
                net_amount_due, vat_amount, vatable_amount, zero_rated_amount, vat_exempt_amount,
                tax_withheld_percentage, tax_withheld_amount,
                total_amount_due, invoice_status 
            ) VALUES (
                :invoice_date, :invoice_account_id, :customer_po, :so_no, :rep, :invoice_due_date, :customer_id,
                :payment_method, :location, :terms, :memo, :gross_amount, :discount_amount,
                :net_amount_due, :vat_amount, :vatable_amount, :zero_rated_amount, :vat_exempt_amount,
                :tax_withheld_percentage, :tax_withheld_amount,
                :total_amount_due, :invoice_status
            )";

            $stmt = $connection->prepare($sql);
            $stmt->bindParam(':invoice_date', $invoice_date);
            $stmt->bindParam(':invoice_account_id', $invoice_account_id);
            $stmt->bindParam(':customer_po', $customer_po);
            $stmt->bindParam(':so_no', $so_no);
            $stmt->bindParam(':rep', $rep);
            $stmt->bindParam(':invoice_due_date', $invoice_due_date);
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
            $stmt->bindValue(':invoice_status', 4, PDO::PARAM_INT); // Set status to 0 for draft


            $stmt->execute();

            // Retrieve the last inserted ID
            $invoice_id = $connection->lastInsertId();

            // Insert invoice items
            if (!empty($items)) {
                $itemSql = "INSERT INTO sales_invoice_details (
                    invoice_id, item_id, quantity, cost, amount, discount_percentage, discount_amount, net_amount_before_sales_tax, net_amount, sales_tax_percentage, sales_tax_amount,  output_vat_id,  discount_account_id,  cogs_account_id,  income_account_id,  asset_account_id
                ) VALUES (
                    :invoice_id, :item_id, :quantity, :cost, :amount, :discount_percentage, :discount_amount, :net_amount_before_sales_tax, :net_amount, :sales_tax_percentage, :sales_tax_amount, :output_vat_id, :discount_account_id, :cogs_account_id, :income_account_id, :asset_account_id
                )";

                $itemStmt = $connection->prepare($itemSql);

                foreach ($items as $item) {
                    $itemStmt->bindParam(':invoice_id', $invoice_id);
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

    public static function updateItems(
        $invoice_id,
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
    ) {
        global $connection;

        // Use NULL if the value is an empty string
        $output_vat_id = !empty($output_vat_id) ? $output_vat_id : NULL;
        $discount_account_id = !empty($discount_account_id) ? $discount_account_id : NULL;
        $cogs_account_id = !empty($cogs_account_id) ? $cogs_account_id : NULL;
        $income_account_id = !empty($income_account_id) ? $income_account_id : NULL;
        $asset_account_id = !empty($asset_account_id) ? $asset_account_id : NULL;

        try {
            // Prepare and execute SQL to update the invoice item record
            $stmt = $connection->prepare("
                UPDATE sales_invoice_details 
                SET 
                    quantity = ?, 
                    cost = ?, 
                    amount = ?, 
                    discount_percentage = ?, 
                    discount_amount = ?, 
                    net_amount_before_sales_tax = ?, 
                    net_amount = ?, 
                    sales_tax_percentage = ?, 
                    sales_tax_amount = ?, 
                    output_vat_id = ?, 
                    discount_account_id = ?, 
                    cogs_account_id = ?, 
                    income_account_id = ?, 
                    asset_account_id = ?
                WHERE 
                    invoice_id = ? 
                    AND item_id = ?
            ");

            $stmt->execute([
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
                $asset_account_id,
                $invoice_id,
                $item_id
            ]);
        } catch (PDOException $e) {
            // Handle any errors that occur during the update process
            throw $e;
        }
    }


    public static function saveFinal($id, $invoice_number, $invoice_date, $invoice_account_id, $customer_po, $so_no, $rep, $discount_account_ids, $output_vat_ids, $invoice_due_date, $customer_id, $customer_name, $payment_method, $location, $terms, $memo, $gross_amount, $discount_amount, $net_amount_due, $vat_amount, $vatable_amount, $zero_rated_amount, $vat_exempt_amount, $tax_withheld_percentage, $tax_withheld_amount, $tax_withheld_account_id, $total_amount_due, $items, $created_by)
    {
        global $connection;

        try {
            $connection->beginTransaction();

            // Check if the invoice account is an Accounts Receivable or Undeposited Funds type
            $stmt = $connection->prepare("
                SELECT at.name
                FROM chart_of_account coa
                JOIN account_types at ON coa.account_type_id = at.id
                WHERE coa.id = ?
            ");
            $stmt->execute([$invoice_account_id]);
            $account_type_name = $stmt->fetchColumn();

            // Determine the invoice status and balance due based on the account type
            $invoice_status = 0;
            $balance_due = $total_amount_due;
            if ($account_type_name == 'Bank' || $account_type_name == 'Other Current Assets') {
                $invoice_status = 1;
                $balance_due = 0;
            }

            // Update the main invoice record
            $stmt = $connection->prepare("
                UPDATE sales_invoice 
                SET invoice_number = :invoice_number,
                    invoice_date = :invoice_date,
                    invoice_account_id = :invoice_account_id,
                    invoice_due_date = :invoice_due_date,
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
                    invoice_status = :invoice_status,
                    balance_due = :balance_due
                WHERE id = :id
            ");

            // Bind the parameters
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':invoice_number', $invoice_number, PDO::PARAM_STR);
            $stmt->bindParam(':invoice_date', $invoice_date, PDO::PARAM_STR);
            $stmt->bindParam(':invoice_account_id', $invoice_account_id, PDO::PARAM_INT);
            $stmt->bindParam(':invoice_due_date', $invoice_due_date, PDO::PARAM_STR);
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
            $stmt->bindParam(':net_amount_due', $net_amount_due, PDO::PARAM_STR);
            $stmt->bindParam(':vat_amount', $vat_amount, PDO::PARAM_STR);
            $stmt->bindParam(':vatable_amount', $vatable_amount, PDO::PARAM_STR);
            $stmt->bindParam(':zero_rated_amount', $zero_rated_amount, PDO::PARAM_STR);
            $stmt->bindParam(':vat_exempt_amount', $vat_exempt_amount, PDO::PARAM_STR);
            $stmt->bindParam(':tax_withheld_percentage', $tax_withheld_percentage, PDO::PARAM_STR);
            $stmt->bindParam(':tax_withheld_amount', $tax_withheld_amount, PDO::PARAM_STR);
            $stmt->bindParam(':total_amount_due', $total_amount_due, PDO::PARAM_STR);
            $stmt->bindParam(':invoice_status', $invoice_status, PDO::PARAM_INT);
            $stmt->bindParam(':balance_due', $balance_due, PDO::PARAM_STR);

            // Execute the statement
            $result = $stmt->execute();

            if ($result) {
                // Delete existing invoice details
                $stmt = $connection->prepare("DELETE FROM sales_invoice_details WHERE invoice_id = ?");
                $stmt->execute([$id]);

                // Prepare statement for inserting new invoice details
                $stmt = $connection->prepare("
                    INSERT INTO sales_invoice_details (
                        invoice_id, item_id, quantity, cost, amount, discount_percentage, discount_amount,
                        net_amount_before_sales_tax, net_amount, sales_tax_percentage, sales_tax_amount,
                        output_vat_id, discount_account_id, cogs_account_id, income_account_id, asset_account_id
                    ) VALUES (
                        :invoice_id, :item_id, :quantity, :cost, :amount, :discount_percentage, :discount_amount,
                        :net_amount_before_sales_tax, :net_amount, :sales_tax_percentage, :sales_tax_amount,
                        :output_vat_id, :discount_account_id, :cogs_account_id, :income_account_id, :asset_account_id
                    )
                ");

                // Prepare statement for fetching item name
                $stmtItemName = $connection->prepare("SELECT item_name FROM items WHERE id = :item_id");

                $transaction_type = "Invoice";

                // Log the main invoice transaction
                self::logAuditTrail(
                    $id,
                    $transaction_type,
                    self::getCurrentDate(),
                    $invoice_number,
                    $location,
                    $customer_name,
                    null,
                    null,
                    $invoice_account_id,
                    $gross_amount, // debit
                    0.00, // credit
                    $created_by
                );

                // Log tax withheld transaction
                self::logAuditTrail(
                    $id,
                    $transaction_type,
                    self::getCurrentDate(),
                    $invoice_number,
                    $location,
                    $customer_name,
                    null,
                    null,
                    $tax_withheld_account_id,
                    $tax_withheld_amount,
                    0.00,
                    $created_by
                );

                // Insert new invoice details and process audit trail in the same loop
                foreach ($items as $item) {
                    // Insert the item
                    $stmt->execute([
                        ':invoice_id' => $id,
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
                        ':output_vat_id' => $item['output_vat_id'],
                        ':discount_account_id' => $item['discount_account_id'],
                        ':cogs_account_id' => $item['cogs_account_id'],
                        ':income_account_id' => $item['income_account_id'],
                        ':asset_account_id' => $item['asset_account_id']
                    ]);

                    // Fetch the item_name
                    $stmtItemName->execute([':item_id' => $item['item_id']]);
                    $item_name = $stmtItemName->fetchColumn();


                    // Log VAT for this item
                    if (!empty($item['output_vat_id']) && $item['sales_tax_amount'] > 0) {
                        self::logAuditTrail(
                            $id,
                            $transaction_type,
                            self::getCurrentDate(),
                            $invoice_number,
                            $location,
                            $customer_name,
                            $item_name,
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
                            $invoice_number,
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

                    // Log income for this item
                    if (!empty($item['income_account_id'])) {
                        self::logAuditTrail(
                            $id,
                            $transaction_type,
                            self::getCurrentDate(),
                            $invoice_number,
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

                    // Log Cost of Goods Sold (COGS) for this item
                    if (!empty($item['cogs_account_id'])) {
                        self::logAuditTrail(
                            $id,
                            $transaction_type,
                            self::getCurrentDate(),
                            $invoice_number,
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

                    // Log asset transaction for this item
                    if (!empty($item['asset_account_id'])) {
                        self::logAuditTrail(
                            $id,
                            $transaction_type,
                            self::getCurrentDate(),
                            $invoice_number,
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

                // Log the main invoice transaction discount credit
                self::logAuditTrail(
                    $id,
                    $transaction_type,
                    self::getCurrentDate(),
                    $invoice_number,
                    $location,
                    $customer_name,
                    null,
                    null,
                    $invoice_account_id,
                    0.00,
                    $discount_amount,
                    $created_by
                );

                // Log the main invoice transaction wtax credit
                self::logAuditTrail(
                    $id,
                    $transaction_type,
                    self::getCurrentDate(),
                    $invoice_number,
                    $location,
                    $customer_name,
                    null,
                    null,
                    $invoice_account_id,
                    0.00,
                    $tax_withheld_amount,
                    $created_by
                );

                // If it's an Accounts Receivable, update the customer's balance
                if ($account_type_name == 'Accounts Receivable') {
                    $stmt = $connection->prepare("UPDATE customers SET credit_balance = credit_balance + ?, total_invoiced = total_invoiced + ? WHERE id = ?");
                    $stmt->execute([$total_amount_due, $total_amount_due, $customer_id]);
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

    // Method to get the current date
    private static function getCurrentDate()
    {
        return date('Y-m-d');
    }

    public static function voidInvoice($id)
    {
        global $connection;

        try {
            $connection->beginTransaction();

            // Update the status to 3 (void) in the sales_invoice table
            $stmt = $connection->prepare("UPDATE sales_invoice SET invoice_status = 3 WHERE id = :id");
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

    public static function updateDraft($id, $invoice_date, $invoice_account_id, $customer_po, $so_no, $rep, $discount_account_ids, $output_vat_ids, $invoice_due_date, $customer_id, $customer_name, $payment_method, $location, $terms, $memo, $gross_amount, $discount_amount, $net_amount_due, $vat_amount, $vatable_amount, $zero_rated_amount, $vat_exempt_amount, $tax_withheld_percentage, $tax_withheld_amount, $tax_withheld_account_id, $total_amount_due, $items, $created_by)
    {
        global $connection;

        try {
            $connection->beginTransaction();

            // Update the main invoice record
            $stmt = $connection->prepare("
                UPDATE sales_invoice 
                SET invoice_date = :invoice_date,
                    invoice_account_id = :invoice_account_id,
                    invoice_due_date = :invoice_due_date,
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
                    invoice_status = 4
                WHERE id = :id
            ");

            // Bind the parameters
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':invoice_date', $invoice_date, PDO::PARAM_STR);
            $stmt->bindParam(':invoice_account_id', $invoice_account_id, PDO::PARAM_INT);
            $stmt->bindParam(':invoice_due_date', $invoice_due_date, PDO::PARAM_STR);
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
            $stmt->bindParam(':net_amount_due', $net_amount_due, PDO::PARAM_STR);
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
                // Delete existing invoice details
                $stmt = $connection->prepare("DELETE FROM sales_invoice_details WHERE invoice_id = ?");
                $stmt->execute([$id]);

                // Prepare statement for inserting new invoice details
                $stmt = $connection->prepare("
                    INSERT INTO sales_invoice_details (
                        invoice_id, item_id, quantity, cost, amount, discount_percentage, discount_amount,
                        net_amount_before_sales_tax, net_amount, sales_tax_percentage, sales_tax_amount,
                        output_vat_id, discount_account_id, cogs_account_id, income_account_id, asset_account_id
                    ) VALUES (
                        :invoice_id, :item_id, :quantity, :cost, :amount, :discount_percentage, :discount_amount,
                        :net_amount_before_sales_tax, :net_amount, :sales_tax_percentage, :sales_tax_amount,
                        :output_vat_id, :discount_account_id, :cogs_account_id, :income_account_id, :asset_account_id
                    )
                ");

                // Insert new invoice details
                foreach ($items as $item) {
                    $stmt->execute([
                        ':invoice_id' => $id,
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
                        ':output_vat_id' => $item['output_vat_id'],
                        ':discount_account_id' => $item['discount_account_id'],
                        ':cogs_account_id' => $item['cogs_account_id'],
                        ':income_account_id' => $item['income_account_id'],
                        ':asset_account_id' => $item['asset_account_id']
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
            $stmt->bindParam(':ref_no', $ref_no);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':item_id', $item_id);
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
