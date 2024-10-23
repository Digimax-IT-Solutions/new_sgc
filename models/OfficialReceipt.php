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

    // ==========================================================================
    // WARNING: DO NOT MODIFY THE CODE BELOW!
    // Version: 1.0.0 (First working version)
    // Last Updated: [7/26/2024]
    //
    // This method has been finalized and locked for modifications.
    // This section is critical for the functionality of the invoice processing.
    // Any changes might cause unexpected behavior or system failures.
    // If changes are absolutely necessary, consult with the lead developer
    // and thoroughly test all affected systems before deployment.
    //
    // Change Log:
    // v1.0.0 - Initial working version implemented and tested
    // =============    =============================================================

    // public static function add($data)
    // {
    //     global $connection;

    //     try {
    //         // Start a database transaction to ensure data integrity
    //         $connection->beginTransaction();

    //         $transaction_type = " Cash Invoice";
    //         $or_status = 0; // Assuming 0 is the initial status

    //         // Prepare and execute SQL to insert the main official receipt record
    //         $stmt = $connection->prepare("INSERT INTO or_payments (
    //             customer_id, location, customer_po, so_no, rep, or_number, or_date, or_account_id,
    //             payment_method, check_no, memo, gross_amount, discount_amount, net_amount_due, vat_amount, vatable_amount,
    //             zero_rated_amount, vat_exempt_amount, tax_withheld_percentage,
    //             tax_withheld_amount, total_amount_due, or_status
    //         ) VALUES (
    //             :customer_id, :location, :customer_po, :so_no, :rep, :or_number, :or_date, :or_account_id,
    //             :payment_method, :check_no, :memo, :gross_amount, :total_discount_amount,
    //             :net_amount_due, :total_vat_amount, :vatable_amount,
    //             :zero_rated_amount, :vat_exempt_amount, :tax_withheld_percentage,
    //             :tax_withheld_amount, :total_amount_due, :or_status
    //         )");

    //         $stmt->execute([
    //             ':customer_id' => $data['customer_id'],
    //             ':location' => $data['location'],
    //             ':customer_po' => $data['customer_po'],
    //             ':so_no' => $data['so_no'],
    //             ':rep' => $data['rep'],
    //             ':or_number' => $data['or_number'],
    //             ':or_date' => $data['or_date'],
    //             ':or_account_id' => $data['or_account_id'],
    //             ':payment_method' => $data['payment_method'],
    //             ':check_no' => $data['check_no'],
    //             ':memo' => $data['memo'],
    //             ':gross_amount' => $data['gross_amount'],
    //             ':total_discount_amount' => $data['total_discount_amount'],
    //             ':net_amount_due' => $data['net_amount_due'],
    //             ':total_vat_amount' => $data['total_vat_amount'],
    //             ':vatable_amount' => $data['vatable_amount'],
    //             ':zero_rated_amount' => $data['zero_rated_amount'],
    //             ':vat_exempt_amount' => $data['vat_exempt_amount'],
    //             ':tax_withheld_percentage' => $data['tax_withheld_percentage'],
    //             ':tax_withheld_amount' => $data['tax_withheld_amount'],
    //             ':total_amount_due' => $data['total_amount_due'],
    //             ':or_status' => $or_status
    //         ]);



    //         $or_id = $connection->lastInsertId();


    //         // Log the main invoice transaction in the audit trail
    //         self::logAuditTrail(
    //             $or_id,
    //             $transaction_type,
    //             $data['or_date'],
    //             $data['or_number'],
    //             $data['customer_name'],
    //             null,
    //             null,
    //             $data['or_account_id'],
    //             $data['gross_amount'],
    //             0.00,
    //             $data['created_by'],
    //         );

    //         // Log tax withheld transaction
    //         self::logAuditTrail(
    //             $or_id,
    //             $transaction_type,
    //             $data['or_date'],
    //             $data['or_number'],
    //             $data['customer_name'],
    //             null,
    //             null,
    //             $data['tax_withheld_account_id'],
    //             $data['tax_withheld_amount'],
    //             0.00,
    //             $data['created_by'],
    //         );

    //         self::logAuditTrail(
    //             $or_id,
    //             $transaction_type,
    //             $data['or_date'],
    //             $data['or_number'],
    //             $data['customer_name'],
    //             null,
    //             null,
    //             $data['output_vat_ids'],
    //             0.00,
    //             $data['total_vat_amount'],
    //             $data['created_by'],
    //         );


    //         // Process each item in the invoice
    //         foreach ($data['items'] as $item) {
    //             self::addInvoiceItems(
    //                 $or_id,
    //                 $item['item_id'],
    //                 $item['quantity'],
    //                 $item['cost'],
    //                 $item['amount'],
    //                 $item['discount_percentage'],
    //                 $item['discount_amount'],
    //                 $item['net_amount_before_sales_tax'],
    //                 $item['net_amount'],
    //                 $item['sales_tax_percentage'],
    //                 $item['sales_tax_amount'],
    //                 $item['output_vat_id'],
    //                 $item['discount_account_id'],
    //                 $item['cogs_account_id'],
    //                 $item['income_account_id'],
    //                 $item['asset_account_id']
    //             );

    //             // Log discount for this item
    //             if (!empty($item['discount_account_id'])) {
    //                 self::logAuditTrail(
    //                     $or_id,
    //                     $transaction_type,
    //                     $data['or_date'],
    //                     $data['or_number'],
    //                     $data['customer_name'],
    //                     $item['item_name'],
    //                     $item['quantity'],
    //                     $item['discount_account_id'],
    //                     $item['discount_amount'],
    //                     0.00,
    //                     $data['created_by'],
    //                 );
    //             }

    //             if (!empty($item['income_account_id'])) {
    //                 self::logAuditTrail(
    //                     $or_id,
    //                     $transaction_type,
    //                     $data['or_date'],
    //                     $data['or_number'],
    //                     $data['customer_name'],
    //                     $item['item_name'],
    //                     $item['quantity'],
    //                     $item['income_account_id'],
    //                     0.00,
    //                     ($item['amount'] - $item['sales_tax_amount']),
    //                     $data['created_by'],
    //                 );
    //             }

    //             if (!empty($item['cogs_account_id'])) {
    //                 self::logAuditTrail(
    //                     $or_id,
    //                     $transaction_type,
    //                     $data['or_date'],
    //                     $data['or_number'],
    //                     $data['customer_name'],
    //                     $item['item_name'],
    //                     $item['quantity'],
    //                     $item['cogs_account_id'],
    //                     $item['cost'] * $item['quantity'],
    //                     0.00,
    //                     $data['created_by'],
    //                 );
    //             }

    //             if (!empty($item['asset_account_id'])) {
    //                 self::logAuditTrail(
    //                     $or_id,
    //                     $transaction_type,
    //                     $data['or_date'],
    //                     $data['or_number'],
    //                     $data['customer_name'],
    //                     $item['item_name'],
    //                     $item['quantity'],
    //                     $item['asset_account_id'],
    //                     0.00,
    //                     $item['cost'] * $item['quantity'],
    //                     $data['created_by'],
    //                 );
    //             }
    //         }

    //         self::logAuditTrail(
    //             $or_id,
    //             $transaction_type,
    //             $data['or_date'],
    //             $data['or_number'],
    //             $data['customer_name'],
    //             null,
    //             null,
    //             $data['or_account_id'],
    //             0.00,
    //             $data['total_discount_amount'],
    //             $data['created_by'],
    //         );

    //         self::logAuditTrail(
    //             $or_id,
    //             $transaction_type,
    //             $data['or_date'],
    //             $data['or_number'],
    //             $data['customer_name'],
    //             null,
    //             null,
    //             $data['or_account_id'],
    //             0.00,
    //             $data['tax_withheld_amount'],
    //             $data['created_by'],
    //         );

    //         // You might want to add additional logic here, such as:
    //         // - Updating customer balance
    //         // - Recording the transaction in a separate transactions table
    //         // - Handling any related records (like line items if they exist)

    //         // Commit the transaction if everything was successful
    //         $connection->commit();


    //     } catch (PDOException $e) {
    //         // If any error occurs, roll back the transaction
    //         $connection->rollBack();
    //         // Re-throw the exception for handling at a higher level
    //         throw $e;
    //     }
    // }

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

            $transaction_type = " Cash Invoice";
            $or_status = 0; // Assuming 0 is the initial status

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

            // Process each item in the official receipt
            foreach ($items as $item) {
                self::addInvoiceItems(
                    $or_id,
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
            $or_id,
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


    // ==========================================================================
    // WARNING: DO NOT MODIFY THE CODE ABOVE!
    // This method has been finalized and locked for modifications.
    // This section is critical for the functionality of the invoice processing.
    // Any changes might cause unexpected behavior or system failures.
    // If changes are absolutely necessary, consult with the lead developer
    // and thoroughly test all affected systems before deployment.
    // ==========================================================================



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
                coas.gl_name,
                coas.account_code,
                coas.account_description
            FROM or_payments op
            INNER JOIN customers c ON op.customer_id = c.id
            INNER JOIN chart_of_account coas ON op.or_account_id = coas.id
            WHERE op.id = :id
        ');

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $Data = $stmt->fetch();

        if (!$Data) {
            return null;
        }

        // Assuming you still need to fetch related details, otherwise remove this line
        $Data['details'] = self::getDetails($id);

        return new OfficialReceipt($Data);
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
        $id, $or_number, $or_date, $or_account_id, $customer_po, $so_no, $check_no, $rep,
        $payment_method, $location, $memo, $gross_amount, $total_discount_amount,
        $net_amount_due, $vat_amount, $vatable_amount, $zero_rated_amount, $vat_exempt_amount,
        $tax_withheld_percentage, $tax_withheld_amount, $total_amount_due, $items,
        $customer_id, $customer_name
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
        $id, $or_number, $or_date, $or_account_id, $customer_po, $so_no, $check_no, $rep,
        $payment_method, $location, $memo, $gross_amount, $total_discount_amount,
        $net_amount_due, $vat_amount, $vatable_amount, $zero_rated_amount, $vat_exempt_amount,
        $tax_withheld_percentage, $tax_withheld_amount, $total_amount_due, $items,
        $customer_id, $customer_name, $ci_no, $created_by
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

    // public static function find($id)
    // {
    //     global $connection;

    //     $stmt = $connection->prepare('
    //         SELECT 
    //             si.id,
    //             si.invoice_number,
    //             si.invoice_date,
    //             si.invoice_account_id,
    //             si.customer_po,
    //             si.so_no,
    //             si.rep,
    //             si.invoice_due_date,
    //             si.payment_method,
    //             si.location,
    //             si.terms,
    //             si.memo,
    //             si.gross_amount,
    //             si.discount_amount as invoice_discount_amount,
    //             si.net_amount_due,
    //             si.vat_amount,
    //             si.vatable_amount,
    //             si.zero_rated_amount,
    //             si.vat_exempt_amount,
    //             si.tax_withheld_percentage,
    //             si.tax_withheld_amount,
    //             si.total_amount_due,
    //             si.invoice_status,
    //             si.status as invoice_status_code,
    //             si.print_status,
    //             c.id as customer_id,
    //             c.customer_name,
    //             c.customer_tin,
    //             c.customer_code,
    //             c.customer_contact,
    //             c.customer_terms,
    //             c.customer_email,
    //             c.shipping_address,
    //             c.billing_address,
    //             c.business_style
    //         FROM sales_invoice si
    //         INNER JOIN customers c ON si.customer_id = c.id
    //         WHERE si.id = :id
    //     ');

    //     $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    //     $stmt->execute();
    //     $stmt->setFetchMode(PDO::FETCH_ASSOC);

    //     $invoiceData = $stmt->fetch();

    //     if (!$invoiceData) {
    //         return null;
    //     }

    //     $invoiceData['details'] = self::getInvoiceDetails($id);

    //     return new Invoice($invoiceData);
    // }

    // public static function getInvoiceDetails($invoice_id)
    // {
    //     global $connection;

    //     $stmt = $connection->prepare('
    //         SELECT 
    //             sid.id,
    //             sid.invoice_id,
    //             sid.quantity,
    //             sid.cost,
    //             sid.amount,
    //             sid.discount_percentage,
    //             sid.discount_amount,
    //             sid.net_amount_before_sales_tax,
    //             sid.net_amount,
    //             sid.sales_tax_percentage,
    //             sid.sales_tax_amount,
    //             i.id AS item_id,
    //             i.item_name,
    //             i.item_code,
    //             i.item_type,
    //             i.item_reorder_point,
    //             i.item_sales_description,
    //             i.item_selling_price,
    //             i.item_purchase_description,
    //             i.item_cost_price,
    //             i.item_quantity,
    //             um.name AS uom_name
    //         FROM sales_invoice_details sid
    //         LEFT JOIN items i ON sid.item_id = i.id
    //         LEFT JOIN uom um ON i.item_uom_id = um.id
    //         WHERE sid.invoice_id = :invoice_id
    //     ');

    //     $stmt->bindParam(':invoice_id', $invoice_id, PDO::PARAM_INT);
    //     $stmt->execute();
    //     $stmt->setFetchMode(PDO::FETCH_ASSOC);

    //     $details = [];
    //     while ($row = $stmt->fetch()) {
    //         $details[] = $row; // Directly use the fetched row as it is already formatted correctly
    //     }

    //     return $details;
    // }

    // public static function getTotalCount()
    // {
    //     global $connection;

    //     $stmt = $connection->query('SELECT COUNT(*) as total_count FROM sales_invoice');
    //     $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //     return $result['total_count'];
    // }

    // public static function getUnpaidCount()
    // {
    //     global $connection;

    //     $stmt = $connection->prepare('SELECT COUNT(*) as unpaid_count FROM sales_invoice WHERE invoice_status = 0');
    //     $stmt->execute();
    //     $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //     return $result['unpaid_count'];
    // }

    // public static function getPaidCount()
    // {
    //     global $connection;

    //     $stmt = $connection->prepare('SELECT COUNT(*) as paid_count FROM sales_invoice WHERE invoice_status = 1');
    //     $stmt->execute();
    //     $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //     return $result['paid_count'];
    // }

    // public static function getOverdueCount()
    // {
    //     global $connection;

    //     $currentDate = date('Y-m-d'); // Get current date in 'YYYY-MM-DD' format

    //     $stmt = $connection->prepare('
    //     SELECT COUNT(*) as overdue_count 
    //     FROM sales_invoice 
    //     WHERE invoice_due_date < :currentDate 
    //     AND invoice_status = 0 
    //     AND balance_due > 0
    // ');

    //     $stmt->bindParam(':currentDate', $currentDate);
    //     $stmt->execute();
    //     $result = $stmt->fetch(PDO::FETCH_ASSOC);

    //     return $result['overdue_count'];
    // }

    // public static function getByCustomerId($customer_id)
    // {
    //     global $connection;

    //     $stmt = $connection->prepare('
    //     SELECT si.*, c.customer_name
    //     FROM sales_invoice si
    //     INNER JOIN customers c ON si.customer_id = c.id
    //     WHERE si.customer_id = :customer_id
    // ');
    //     $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);
    //     $stmt->execute();

    //     $invoices = [];
    //     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    //         $invoices[] = new Invoice($row);
    //     }

    //     return $invoices;
    // }

    // public static function getCashSalesByCustomerId($customer_id, $invoice_account = null)
    // {
    //     global $connection;

    //     // Prepare the base SQL query
    //     $sql = '
    //               SELECT si.*, c.customer_name
    //     FROM sales_invoice si
    //     INNER JOIN customers c ON si.customer_id = c.id
    //     INNER JOIN chart_of_account coa ON si.invoice_account_id = coa.id
    //     INNER JOIN account_types at ON coa.account_type_id = at.id
    //     WHERE si.customer_id = :customer_id
    //     AND (si.invoice_status = 0 OR si.invoice_status = 2)
    //     AND si.status = 1
    //     AND at.name = "Accounts Receivable"
    //     ';

    //     // If invoice_account is provided, add it to the WHERE clause
    //     if ($invoice_account !== null) {
    //         $sql .= ' AND si.invoice_account = :invoice_account';
    //     }

    //     $stmt = $connection->prepare($sql);
    //     $stmt->bindParam(':customer_id', $customer_id, PDO::PARAM_INT);

    //     // Bind invoice_account parameter if provided
    //     if ($invoice_account !== null) {
    //         $stmt->bindParam(':invoice_account', $invoice_account, PDO::PARAM_STR);
    //     }

    //     $stmt->execute();

    //     $invoices = [];
    //     while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    //         $invoices[] = new Invoice($row);
    //     }

    //     return $invoices;
    // }


    // public static function update(
    //     $invoice_id,
    //     $invoice_number,
    //     $invoice_date,
    //     $invoice_account_id,
    //     $invoice_due_date,
    //     $customer_id,
    //     $payment_method,
    //     $location,
    //     $terms,
    //     $memo,
    //     $gross_amount,
    //     $discount_amount,
    //     $net_amount_due,
    //     $vat_amount,
    //     $vatable_amount,
    //     $zero_rated_amount,
    //     $vat_exempt_amount,
    //     $tax_withheld_percentage,
    //     $tax_withheld_amount,
    //     $total_amount_due
    // ) {
    //     // Get database connection
    //     global $connection;

    //     // Start transaction
    //     $connection->beginTransaction();

    //     try {
    //         // Prepare and execute the update statement
    //         $stmt = $connection->prepare("
    //             UPDATE sales_invoice
    //             SET 
    //                 invoice_number = :invoice_number,
    //                 invoice_date = :invoice_date,
    //                 invoice_account_id = :invoice_account_id,
    //                 invoice_due_date = :invoice_due_date,
    //                 customer_id = :customer_id,
    //                 payment_method = :payment_method,
    //                 location = :location,
    //                 terms = :terms,
    //                 memo = :memo,
    //                 gross_amount = :gross_amount,
    //                 discount_amount = :discount_amount,
    //                 net_amount_due = :net_amount_due,
    //                 vat_amount = :vat_amount,
    //                 vatable_amount = :vatable_amount,
    //                 zero_rated_amount = :zero_rated_amount,
    //                 vat_exempt_amount = :vat_exempt_amount,
    //                 tax_withheld_percentage = :tax_withheld_percentage,
    //                 tax_withheld_amount = :tax_withheld_amount,
    //                 total_amount_due = :total_amount_due
    //             WHERE id = :invoice_id
    //         ");
    //         $stmt->execute([
    //             ':invoice_id' => $invoice_id,
    //             ':invoice_number' => $invoice_number,
    //             ':invoice_date' => $invoice_date,
    //             ':invoice_account_id' => $invoice_account_id,
    //             ':invoice_due_date' => $invoice_due_date,
    //             ':customer_id' => $customer_id,
    //             ':payment_method' => $payment_method,
    //             ':location' => $location,
    //             ':terms' => $terms,
    //             ':memo' => $memo,
    //             ':gross_amount' => $gross_amount,
    //             ':discount_amount' => $discount_amount,
    //             ':net_amount_due' => $net_amount_due,
    //             ':vat_amount' => $vat_amount,
    //             ':vatable_amount' => $vatable_amount,
    //             ':zero_rated_amount' => $zero_rated_amount,
    //             ':vat_exempt_amount' => $vat_exempt_amount,
    //             ':tax_withheld_percentage' => $tax_withheld_percentage,
    //             ':tax_withheld_amount' => $tax_withheld_amount,
    //             ':total_amount_due' => $total_amount_due,
    //         ]);

    //         // Commit transaction
    //         $connection->commit();
    //     } catch (Exception $e) {
    //         // Rollback transaction if something failed
    //         $connection->rollBack();
    //         error_log("Update failed: " . $e->getMessage()); // Log error message
    //         throw $e;
    //     }
    // }

    // public static function updateDetails($invoice_id, $item_data)
    // {
    //     // Get database connection
    //     global $connection;

    //     // Start transaction
    //     $connection->beginTransaction();

    //     try {
    //         // Delete existing details for the given invoice_id
    //         $stmt = $connection->prepare("DELETE FROM sales_invoice_details WHERE invoice_id = :invoice_id");
    //         $stmt->execute([':invoice_id' => $invoice_id]);

    //         // Insert new details into sales_invoice_details
    //         foreach ($item_data as $item) {
    //             $stmt = $connection->prepare("
    //                 INSERT INTO sales_invoice_details (invoice_id, item_id, quantity, cost, amount, discount_percentage, discount_amount, net_amount_before_sales_tax, net_amount, sales_tax_percentage, sales_tax_amount)
    //                 VALUES (:invoice_id, :item_id, :quantity, :cost, :amount, :discount_percentage, :discount_amount, :net_amount_before_sales_tax, :net_amount, :sales_tax_percentage, :sales_tax_amount)
    //             ");
    //             $stmt->execute([
    //                 ':invoice_id' => $invoice_id,
    //                 ':item_id' => $item['item_id'],
    //                 ':quantity' => $item['quantity'],
    //                 ':cost' => $item['cost'],
    //                 ':amount' => $item['amount'],
    //                 ':discount_percentage' => $item['discount_percentage'],
    //                 ':discount_amount' => $item['discount_amount'],
    //                 ':net_amount_before_sales_tax' => $item['net_amount_before_sales_tax'],
    //                 ':net_amount' => $item['net_amount'],
    //                 ':sales_tax_percentage' => $item['sales_tax_percentage'],
    //                 ':sales_tax_amount' => $item['sales_tax_amount']
    //             ]);
    //         }

    //         // Commit transaction
    //         $connection->commit();
    //     } catch (Exception $e) {
    //         // Rollback transaction if something failed
    //         $connection->rollBack();
    //         error_log("Update details failed: " . $e->getMessage()); // Log error message
    //         throw $e;
    //     }
    // }

    // // GET LAST INVOICE_NO 
    // public static function getLastInvoiceNo()
    // {
    //     global $connection;

    //     try {
    //         // Prepare and execute the query to get the highest invoice number, ignoring null or empty values
    //         $stmt = $connection->prepare("
    //             SELECT invoice_number 
    //             FROM sales_invoice 
    //             WHERE invoice_number IS NOT NULL AND invoice_number <> '' 
    //             ORDER BY invoice_number DESC 
    //             LIMIT 1
    //         ");
    //         $stmt->execute();
    //         $stmt->setFetchMode(PDO::FETCH_ASSOC);
    //         $result = $stmt->fetch();

    //         // Extract the numeric part of the last invoice number
    //         if ($result) {
    //             $latestNo = $result['invoice_number'];
    //             // Assuming the format is 'INV' followed by digits
    //             $numericPart = intval(substr($latestNo, 2)); // 'SI' is 3 characters
    //             $newNo = $numericPart + 1;
    //         } else {
    //             // If no valid invoice number exists, start with 1
    //             $newNo = 1;
    //         }

    //         // Format the new number with leading zeros
    //         $newInvoiceNo = 'SI' . str_pad($newNo, 9, '0', STR_PAD_LEFT);

    //         return $newInvoiceNo;
    //     } catch (PDOException $e) {
    //         // Handle potential exceptions
    //         error_log('Database error: ' . $e->getMessage());
    //         return null;
    //     }
    // }



    // public static function addDraft(
    //     $invoice_date,
    //     $invoice_account_id,
    //     $customer_po,
    //     $so_no,
    //     $rep,
    //     $invoice_due_date,
    //     $customer_id,
    //     $payment_method,
    //     $location,
    //     $terms,
    //     $memo,
    //     $gross_amount,
    //     $discount_amount,
    //     $net_amount_due,
    //     $vat_amount,
    //     $vatable_amount,
    //     $zero_rated_amount,
    //     $vat_exempt_amount,
    //     $tax_withheld_percentage,
    //     $tax_withheld_amount,
    //     $total_amount_due,
    //     $items
    // ) {
    //     global $connection;

    //     try {
    //         $connection->beginTransaction();

    //         // Insert into sales_invoice table
    //         $sql = "INSERT INTO sales_invoice (
    //             invoice_date, invoice_account_id, customer_po, so_no, rep, invoice_due_date, customer_id,
    //             payment_method, location, terms, memo, gross_amount, discount_amount,
    //             net_amount_due, vat_amount, vatable_amount, zero_rated_amount, vat_exempt_amount,
    //             tax_withheld_percentage, tax_withheld_amount,
    //             total_amount_due, invoice_status 
    //         ) VALUES (
    //             :invoice_date, :invoice_account_id, :customer_po, :so_no, :rep, :invoice_due_date, :customer_id,
    //             :payment_method, :location, :terms, :memo, :gross_amount, :discount_amount,
    //             :net_amount_due, :vat_amount, :vatable_amount, :zero_rated_amount, :vat_exempt_amount,
    //             :tax_withheld_percentage, :tax_withheld_amount,
    //             :total_amount_due, :invoice_status
    //         )";

    //         $stmt = $connection->prepare($sql);
    //         $stmt->bindParam(':invoice_date', $invoice_date);
    //         $stmt->bindParam(':invoice_account_id', $invoice_account_id);
    //         $stmt->bindParam(':customer_po', $customer_po);
    //         $stmt->bindParam(':so_no', $so_no);
    //         $stmt->bindParam(':rep', $rep);
    //         $stmt->bindParam(':invoice_due_date', $invoice_due_date);
    //         $stmt->bindParam(':customer_id', $customer_id);
    //         $stmt->bindParam(':payment_method', $payment_method);
    //         $stmt->bindParam(':location', $location);
    //         $stmt->bindParam(':terms', $terms);
    //         $stmt->bindParam(':memo', $memo);
    //         $stmt->bindParam(':gross_amount', $gross_amount);
    //         $stmt->bindParam(':discount_amount', $discount_amount);
    //         $stmt->bindParam(':net_amount_due', $net_amount_due);
    //         $stmt->bindParam(':vat_amount', $vat_amount);
    //         $stmt->bindParam(':vatable_amount', $vatable_amount);
    //         $stmt->bindParam(':zero_rated_amount', $zero_rated_amount);
    //         $stmt->bindParam(':vat_exempt_amount', $vat_exempt_amount);
    //         $stmt->bindParam(':tax_withheld_percentage', $tax_withheld_percentage);
    //         $stmt->bindParam(':tax_withheld_amount', $tax_withheld_amount);
    //         $stmt->bindParam(':total_amount_due', $total_amount_due);
    //         $stmt->bindValue(':invoice_status', 4, PDO::PARAM_INT); // Set status to 0 for draft


    //         $stmt->execute();

    //         // Retrieve the last inserted ID
    //         $invoice_id = $connection->lastInsertId();

    //         // Insert invoice items
    //         if (!empty($items)) {
    //             $itemSql = "INSERT INTO sales_invoice_details (
    //                 invoice_id, item_id, quantity, cost, amount, discount_percentage, discount_amount, net_amount_before_sales_tax, net_amount, sales_tax_percentage, sales_tax_amount,  output_vat_id,  discount_account_id,  cogs_account_id,  income_account_id,  asset_account_id
    //             ) VALUES (
    //                 :invoice_id, :item_id, :quantity, :cost, :amount, :discount_percentage, :discount_amount, :net_amount_before_sales_tax, :net_amount, :sales_tax_percentage, :sales_tax_amount, :output_vat_id, :discount_account_id, :cogs_account_id, :income_account_id, :asset_account_id
    //             )";

    //             $itemStmt = $connection->prepare($itemSql);

    //             foreach ($items as $item) {
    //                 $itemStmt->bindParam(':invoice_id', $invoice_id);
    //                 $itemStmt->bindParam(':item_id', $item['item_id']);
    //                 $itemStmt->bindParam(':quantity', $item['quantity']);
    //                 $itemStmt->bindParam(':cost', $item['cost']);
    //                 $itemStmt->bindParam(':amount', $item['amount']);
    //                 $itemStmt->bindParam(':discount_percentage', $item['discount_percentage']);

    //                 $itemStmt->bindParam(':discount_amount', $item['discount_amount']);
    //                 $itemStmt->bindParam(':net_amount_before_sales_tax', $item['net_amount_before_sales_tax']);
    //                 $itemStmt->bindParam(':net_amount', $item['net_amount']);
    //                 $itemStmt->bindParam(':sales_tax_percentage', $item['sales_tax_percentage']);
    //                 $itemStmt->bindParam(':sales_tax_amount', $item['sales_tax_amount']);

    //                 // Handle potential empty values for the following fields
    //                 $outputVatId = !empty($item['output_vat_id']) ? $item['output_vat_id'] : null;
    //                 $discountAccountId = !empty($item['discount_account_id']) ? $item['discount_account_id'] : null;
    //                 $cogsAccountId = !empty($item['cogs_account_id']) ? $item['cogs_account_id'] : null;
    //                 $incomeAccountId = !empty($item['income_account_id']) ? $item['income_account_id'] : null;
    //                 $assetAccountId = !empty($item['asset_account_id']) ? $item['asset_account_id'] : null;

    //                 $itemStmt->bindParam(':output_vat_id', $outputVatId, PDO::PARAM_INT);
    //                 $itemStmt->bindParam(':discount_account_id', $discountAccountId, PDO::PARAM_INT);
    //                 $itemStmt->bindParam(':cogs_account_id', $cogsAccountId, PDO::PARAM_INT);
    //                 $itemStmt->bindParam(':income_account_id', $incomeAccountId, PDO::PARAM_INT);
    //                 $itemStmt->bindParam(':asset_account_id', $assetAccountId, PDO::PARAM_INT);
    //                 $itemStmt->execute();
    //             }
    //         }

    //         $connection->commit();
    //         return true;

    //     } catch (Exception $ex) {
    //         $connection->rollBack();
    //         error_log('Error in addDraft: ' . $ex->getMessage());
    //         throw $ex;
    //     }
    // }


    // public static function updateDraftInvoice($id, $invoice_number, $invoice_account_id, $customer_name, $customer_id, $tax_withheld_account_id, $tax_withheld_amount, $total_amount_due, $gross_amount, $invoice_date, $output_vat_ids, $vat_amount, $discount_amount, $created_by, $items)
    // {
    //     global $connection;

    //     try {
    //         $connection->beginTransaction();

    //         // Check if the invoice account is an Accounts Receivable or Undeposited Funds type
    //         $stmt = $connection->prepare("
    //             SELECT at.name
    //             FROM chart_of_account coa
    //             JOIN account_types at ON coa.account_type_id = at.id
    //             WHERE coa.id = ?
    //         ");
    //         $stmt->execute([$invoice_account_id]);
    //         $account_type_name = $stmt->fetchColumn();

    //         // Determine the invoice status and balance due based on the account type
    //         $invoice_status = 0;
    //         $balance_due = $total_amount_due;
    //         if ($account_type_name == 'Bank' || $account_type_name == 'Other Current Assets') {
    //             $invoice_status = 1;
    //             $balance_due = 0;
    //         }

    //         // Update the invoice in the database
    //         $stmt = $connection->prepare("
    //             UPDATE sales_invoice 
    //             SET invoice_status = :invoice_status, 
    //                 invoice_number = :invoice_number,
    //                 balance_due = :balance_due,
    //                 total_amount_due = :total_amount_due
    //             WHERE id = :id
    //         ");
    //         $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    //         $stmt->bindParam(':invoice_number', $invoice_number, PDO::PARAM_STR);
    //         $stmt->bindParam(':invoice_status', $invoice_status, PDO::PARAM_INT);
    //         $stmt->bindParam(':balance_due', $balance_due, PDO::PARAM_STR);
    //         $stmt->bindParam(':total_amount_due', $total_amount_due, PDO::PARAM_STR);
    //         $result = $stmt->execute();

    //         if ($result) {
    //             $transaction_type = "Invoice";

    //             // Fetch invoice details from sales_invoice_details
    //             $stmt = $connection->prepare("
    //                 SELECT 
    //                     sid.*,
    //                     i.item_name
    //                 FROM 
    //                     sales_invoice_details sid
    //                 JOIN 
    //                     items i ON sid.item_id = i.id
    //                 WHERE 
    //                     sid.invoice_id = :invoice_id
    //             ");
    //             $stmt->bindParam(':invoice_id', $id, PDO::PARAM_INT);
    //             $stmt->execute();
    //             $invoiceDetails = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //             // Log the main invoice transaction
    //             self::logAuditTrail(
    //                 $id,
    //                 $transaction_type,
    //                 self::getCurrentDate(),
    //                 $invoice_number,
    //                 $customer_name,
    //                 null,
    //                 null,
    //                 $invoice_account_id,
    //                 $gross_amount,
    //                 0.00,
    //                 $created_by
    //             );

    //             // Log tax withheld transaction
    //             self::logAuditTrail(
    //                 $id,
    //                 $transaction_type,
    //                 self::getCurrentDate(),
    //                 $invoice_number,
    //                 $customer_name,
    //                 null,
    //                 null,
    //                 $tax_withheld_account_id,
    //                 $tax_withheld_amount,
    //                 0.00,
    //                 $created_by
    //             );

    //             // Process each item in the invoice
    //             foreach ($invoiceDetails as $item) {
    //                 // Log VAT for this item
    //                 if (!empty($item['output_vat_id']) && $item['sales_tax_amount'] > 0) {
    //                     self::logAuditTrail(
    //                         $id,
    //                         $transaction_type,
    //                         self::getCurrentDate(),
    //                         $invoice_number,
    //                         $customer_name,
    //                         $item['item_name'],
    //                         $item['quantity'],
    //                         $item['output_vat_id'],
    //                         0.00,
    //                         $item['sales_tax_amount'],
    //                         $created_by
    //                     );
    //                 }

    //                 // Log discount for this item
    //                 if (!empty($item['discount_account_id']) && $item['discount_amount'] > 0) {
    //                     self::logAuditTrail(
    //                         $id,
    //                         $transaction_type,
    //                         self::getCurrentDate(),
    //                         $invoice_number,
    //                         $customer_name,
    //                         $item['item_name'],
    //                         $item['quantity'],
    //                         $item['discount_account_id'],
    //                         $item['discount_amount'],
    //                         0.00,
    //                         $created_by
    //                     );
    //                 }

    //                 // Log income for this item
    //                 if (!empty($item['income_account_id'])) {
    //                     self::logAuditTrail(
    //                         $id,
    //                         $transaction_type,
    //                         self::getCurrentDate(),
    //                         $invoice_number,
    //                         $customer_name,
    //                         $item['item_name'],
    //                         $item['quantity'],
    //                         $item['income_account_id'],
    //                         0.00,
    //                         ($item['amount'] - $item['sales_tax_amount']),
    //                         $created_by
    //                     );
    //                 }

    //                 // Log Cost of Goods Sold (COGS) for this item
    //                 if (!empty($item['cogs_account_id'])) {
    //                     self::logAuditTrail(
    //                         $id,
    //                         $transaction_type,
    //                         self::getCurrentDate(),
    //                         $invoice_number,
    //                         $customer_name,
    //                         $item['item_name'],
    //                         $item['quantity'],
    //                         $item['cogs_account_id'],
    //                         $item['cost'] * $item['quantity'],
    //                         0.00,
    //                         $created_by
    //                     );
    //                 }

    //                 // Log asset transaction for this item
    //                 if (!empty($item['asset_account_id'])) {
    //                     self::logAuditTrail(
    //                         $id,
    //                         $transaction_type,
    //                         self::getCurrentDate(),
    //                         $invoice_number,
    //                         $customer_name,
    //                         $item['item_name'],
    //                         $item['quantity'],
    //                         $item['asset_account_id'],
    //                         0.00,
    //                         $item['cost'] * $item['quantity'],
    //                         $created_by
    //                     );
    //                 }
    //             }

    //             // Log the main invoice transaction discount credit
    //             self::logAuditTrail(
    //                 $id,
    //                 $transaction_type,
    //                 self::getCurrentDate(),
    //                 $invoice_number,
    //                 $customer_name,
    //                 null,
    //                 null,
    //                 $invoice_account_id,
    //                 0.00,
    //                 $discount_amount,
    //                 $created_by
    //             );

    //             // Log the main invoice transaction wtax credit
    //             self::logAuditTrail(
    //                 $id,
    //                 $transaction_type,
    //                 self::getCurrentDate(),
    //                 $invoice_number,
    //                 $customer_name,
    //                 null,
    //                 null,
    //                 $invoice_account_id,
    //                 0.00,
    //                 $tax_withheld_amount,
    //                 $created_by
    //             );

    //             // If it's an Accounts Receivable, update the customer's balance
    //             if ($account_type_name == 'Accounts Receivable') {
    //                 $stmt = $connection->prepare("UPDATE customers SET credit_balance = credit_balance + ?, total_invoiced = total_invoiced + ? WHERE id = ?");
    //                 $stmt->execute([$total_amount_due, $total_amount_due, $customer_id]);
    //             }

    //             $connection->commit();
    //             return [
    //                 'success' => true,
    //                 'invoiceId' => $id
    //             ];
    //         } else {
    //             throw new Exception("Failed to update invoice.");
    //         }
    //     } catch (Exception $ex) {
    //         $connection->rollback();
    //         error_log('Error updating draft invoice: ' . $ex->getMessage());
    //         return [
    //             'success' => false,
    //             'message' => 'Error: ' . $ex->getMessage()
    //         ];
    //     }
    // }

    // // Method to get the current date
    // private static function getCurrentDate()
    // {
    //     return date('Y-m-d');
    // }

    // public static function voidInvoice($id)
    // {
    //     global $connection;

    //     try {
    //         $connection->beginTransaction();

    //         // Update the status to 3 (void) in the sales_invoice table
    //         $stmt = $connection->prepare("UPDATE sales_invoice SET invoice_status = 3 WHERE id = :id");
    //         $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    //         $result = $stmt->execute();

    //         if ($result) {
    //             // Update the state to 2 in the audit_trail table
    //             $auditStmt = $connection->prepare("UPDATE audit_trail SET state = 2 WHERE transaction_id = :id");
    //             $auditStmt->bindParam(':id', $id, PDO::PARAM_INT);
    //             $auditResult = $auditStmt->execute();

    //             if ($auditResult) {
    //                 // Delete from transaction_entries
    //                 $deleteStmt = $connection->prepare("DELETE FROM transaction_entries WHERE transaction_id = :id");
    //                 $deleteStmt->bindParam(':id', $id, PDO::PARAM_INT);
    //                 $deleteResult = $deleteStmt->execute();

    //                 if ($deleteResult) {
    //                     $connection->commit();
    //                     return true;
    //                 } else {
    //                     throw new Exception("Failed to delete transaction entries.");
    //                 }
    //             } else {
    //                 throw new Exception("Failed to update audit trail.");
    //             }
    //         } else {
    //             throw new Exception("Failed to void invoice.");
    //         }
    //     } catch (Exception $e) {
    //         $connection->rollBack();
    //         throw $e;
    //     }
    // }


}

