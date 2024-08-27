<?php

require_once __DIR__ . '/../_init.php';

class CreditMemo
{
    public $id;
    public $credit_no;
    public $credit_date;
    public $customer_id;
    public $customer_tin;
    public $shipping_address;
    public $billing_address;
    public $business_style;
    public $customer_name;
    public $credit_account;
    public $credit_balance;
    public $account_id;
    public $account_name;
    public $account_code;
    public $account_description;
    public $memo;
    public $gross_amount;
    public $discount_amount;
    public $net_amount_due;
    public $vat_percentage_amount;
    public $net_of_vat;
    public $tax_withheld_amount;
    public $total_amount_due;
    public $print_status;
    public $status;
    public $details = [];

    private static $cache = null;

    public function __construct($data)
    {
        $this->id = $data['id'] ?? null;
        $this->credit_no = $data['credit_no'] ?? null;
        $this->credit_date = $data['credit_date'] ?? null;
        $this->customer_id = $data['customer_id'] ?? null;
        $this->customer_name = $data['customer_name'] ?? null;
        $this->customer_tin = $data['customer_tin'] ?? null;
        $this->shipping_address = $data['shipping_address'] ?? null;
        $this->billing_address = $data['billing_address'] ?? null;
        $this->business_style = $data['business_style'] ?? null;
        $this->credit_account = $data['credit_account'] ?? null;
        $this->account_id = $data['account_id'] ?? null;
        $this->account_name = $data['account_name'] ?? null;
        $this->account_code = $data['account_code'] ?? null;
        $this->account_description = $data['account_description'] ?? null;
        $this->memo = $data['memo'] ?? null;
        $this->credit_account = $data['credit_balance'] ?? null;
        $this->gross_amount = $data['gross_amount'] ?? null;
        $this->net_amount_due = $data['net_amount_due'] ?? null;
        $this->vat_percentage_amount = $data['vat_percentage_amount'] ?? null;
        $this->net_of_vat = $data['net_of_vat'] ?? null;
        $this->tax_withheld_amount = $data['tax_withheld_amount'] ?? null;
        $this->total_amount_due = $data['total_amount_due'] ?? null;
        $this->print_status = $data['print_status'] ?? null;
        $this->status = $data['status'] ?? null;

        // Initialize details as an empty array and populate if provided
        if (isset($data['details']) && is_array($data['details'])) {
            foreach ($data['details'] as $detail) {
                $this->details[] = $detail;
            }
        }
    }
    // add/insert credit_memo data
    public static function add($credit_no, $credit_date, $customer_id, $customer_name, $credit_account_id, $memo, $gross_amount, $net_amount_due, $vat_percentage_amount, $net_of_vat, $tax_withheld_amount, $tax_withheld_account_id, $total_amount_due, $items, $created_by)
    {
        global $connection;

        try {
            // Start a transaction
            $connection->beginTransaction();

            $transaction_type = "Credit Memo";

            $stmt = $connection->prepare("INSERT INTO credit_memo (
                credit_no,
                credit_date,
                customer_id,
                credit_account,
                memo,
                gross_amount,
                net_amount_due,
                vat_percentage_amount,
                net_of_vat,
                tax_withheld_amount,
                total_amount_due) VALUES (?,?,?,?,?,?,?,?,?,?,?) ");
    
            $stmt->execute([
                $credit_no,
                $credit_date,
                $customer_id,
                $credit_account_id,
                $memo,
                $gross_amount,
                $net_amount_due,
                $vat_percentage_amount,
                $net_of_vat,
                $tax_withheld_amount,
                $total_amount_due
            ]);

            // Retrieve the ID of the newly inserted credit memo entry
            $credit_memo_id = $connection->lastInsertId();

            foreach ($items as $item) {
                // Insert credit memo details
                self::addCreditItems(
                    $credit_memo_id,
                    (int) $item['account_id'], // Ensure it's an integer
                    $item['cost_center_id'],
                    $item['memo'],
                    $item['amount'],
                    $item['net_amount'],
                    $item['net_amount_before_vat'],
                    $item['vat_percentage'],
                    $item['sales_tax'],
                    $item['sales_tax_account_id']
                );

                // Update account balance
                // Note: Add the appropriate code for updating the account balance here
                // OUTPUT VAT CREDIT ACCOUNT AUDIT LOG
                self::logAuditTrail(
                    $credit_memo_id,
                    $transaction_type,
                    $credit_date,
                    $credit_no,
                    $customer_name,
                    $item['account_id'],
                    $item['net_amount'], // Added discount_amount here
                    0.00,
                    $created_by
                );

                // OUTPUT VAT CREDIT ACCOUNT AUDIT LOG
                self::logAuditTrail(
                    $credit_memo_id,
                    $transaction_type,
                    $credit_date,
                    $credit_no,
                    $customer_name,
                    $item['sales_tax_account_id'],
                    $item['sales_tax'], // Added discount_amount here
                    0.00,
                    $created_by
                );



                // Log into audit trail
                // Note: Add the appropriate code for logging into the audit trail here

                // Get updated balance
                // Note: Add the appropriate code for getting the updated balance here

                // Log into transaction entries
                // Note: Add the appropriate code for logging into the transaction entries here
            }

            // Credit wtax Account Audit Trail
            self::logAuditTrail(
                $credit_memo_id,
                $transaction_type,
                $credit_date,
                $credit_no,
                $customer_name,
                $tax_withheld_account_id,
                0.00,
                $tax_withheld_amount,
                $created_by
            );

            // Credit Account Audit Trail
            self::logAuditTrail(
                $credit_memo_id,
                $transaction_type,
                $credit_date,
                $credit_no,
                $customer_name,
                $credit_account_id,
                0.00,
                $total_amount_due,
                $created_by
            );

            // Commit the transaction
            $connection->commit();
        } catch (PDOException $e) {
            // Rollback the transaction if an error occurs
            $connection->rollback();
            throw $e;
        }
    }
    // add/insert credit_memo_detail data
    public static function addCreditItems($credit_memo_id, $account_id, $cost_center_id, $memo, $amount, $net_amount, $taxable_amount, $vat_percentage, $sales_tax, $sales_tax_account_id)
    {
        global $connection;
    
        // Ensure account_id is not empty and is an integer
        if (empty($account_id) || !is_numeric($account_id)) {
            throw new Exception("Invalid account_id provided");
        }
    
        $account_id = (int) $account_id; // Ensure it's an integer
    
        $stmt = $connection->prepare("INSERT INTO credit_memo_details (credit_memo_id, account_id, cost_center_id, memo, amount, net_amount, taxable_amount, vat_percentage, sales_tax, sales_tax_account_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
        $stmt->execute([
            $credit_memo_id,
            $account_id,
            $cost_center_id,
            $memo,
            $amount,
            $net_amount,
            $taxable_amount,
            $vat_percentage,
            $sales_tax,
            $sales_tax_account_id
        ]);
    }
    
    // ACCOUNTING LOGS
    private static function logAuditTrail($general_journal_id, $transaction_type, $transaction_date, $ref_no, $customer_name, $account_id, $debit, $credit, $created_by)
    {
        global $connection;

        $stmt = $connection->prepare("
                INSERT INTO audit_trail (
                    transaction_id,
                    transaction_type,
                    transaction_date,
                    ref_no,
                    name,
                    account_id,
                    debit,
                    credit,
                    created_by,
                    created_at
                ) VALUES (?,?,?,?,?,?,?,?,?, NOW())
            ");

        $stmt->execute([
            $general_journal_id,
            $transaction_type,
            $transaction_date,
            $ref_no,
            $customer_name,
            $account_id,
            $debit,
            $credit,
            $created_by
        ]);
    }
    // get last transaction id
    public static function getLastTransactionId()
    {
        global $connection;

        $stmt = $connection->query("SELECT MAX(id) AS last_transaction_id FROM credit_memo");
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();

        if ($result && isset($result['last_transaction_id'])) {
            return $result['last_transaction_id'];
        }

        return null;
    }
    // select by finding credit_no 
    public static function findByCreditNo($credit_no)
    {
        global $connection;

        $stmt = $connection->prepare('SELECT * FROM credit_memo WHERE credit_no = :credit_no');
        $stmt->bindParam("credit_no", $credit_no);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        return count($result) >= 1 ? new CreditMemo($result[0]) : null;
    }
    // select all credit_memo data
    public static function all()
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                cm.*, 
                c.customer_name,
                c.customer_tin,
                c.shipping_address,
                c.billing_address,
                c.business_style,
                c.customer_name
            FROM credit_memo cm
            INNER JOIN customers c ON cm.customer_id = c.id
        ');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $credits = [];
        while ($row = $stmt->fetch()) {
            $credit = [
                'id' => $row['id'],
                'credit_no' => $row['credit_no'],
                'credit_date' => $row['credit_date'],
                'customer_id' => $row['customer_id'],
                'customer_name' => $row['customer_name'],
                'customer_tin' => $row['customer_tin'],
                'shipping_address' => $row['shipping_address'],
                'billing_address' => $row['billing_address'],
                'business_style' => $row['business_style'],
                'credit_account' => $row['credit_account'],
                'memo' => $row['memo'],
                'gross_amount' => $row['gross_amount'],
                'net_amount_due' => $row['net_amount_due'],
                'vat_percentage_amount' => $row['vat_percentage_amount'],
                'net_of_vat' => $row['net_of_vat'],
                'tax_withheld_amount' => $row['tax_withheld_amount'],
                'total_amount_due' => $row['total_amount_due'],
                'status' => $row['status']
            ];
            $credits[] = new CreditMemo($credit);
        }

        return $credits;
    }
    // Add entries to transaction_entries table
    public static function addTransactionEntries($transaction_id, $type, $ref_no, $account_id, $debit, $credit)
    {
        global $connection;

        // Prepare statement for inserting into transaction_entries
        $stmt = $connection->prepare("INSERT INTO transaction_entries (transaction_id, type, ref_no, account_id, debit, credit) VALUES (?, ?, ?, ?, ?, ?)");

        // Execute the statement
        $stmt->execute([$transaction_id, $type, $ref_no, $account_id, $debit, $credit]);
    }
    // get credit_memo data columns
    public static function find($id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                cm.id,
                cm.credit_no,
                cm.credit_date,
                cm.memo,
                cm.gross_amount,
                cm.net_amount_due,
                cm.vat_percentage_amount,
                cm.net_of_vat,
                cm.tax_withheld_amount,
                cm.total_amount_due,
                cm.print_status,
                cm.status,
                c.id as customer_id,
                c.customer_name,
                c.customer_tin,
                c.customer_code,
                c.customer_contact,
                c.customer_terms,
                c.customer_email,
                c.shipping_address,
                c.billing_address,
                c.business_style,
                c.credit_balance,
                coas.id AS credit_account,
                coas.account_type_id,
                coas.gl_name,
                coas.account_code,
                coas.account_description
            FROM credit_memo cm
            INNER JOIN customers c ON cm.customer_id = c.id
            INNER JOIN chart_of_account coas ON cm.credit_account = coas.id
            WHERE cm.id = :id
        ');

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $creditMemoData = $stmt->fetch();

        if (!$creditMemoData) {
            return null;
        }

        $creditMemoData['details'] = self::getCreditMemoDetails($id);

        return new CreditMemo($creditMemoData);
    }
    // get credit_memo_details data columns
    public static function getCreditMemoDetails($credit_memo_id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                cmd.id,
                cmd.credit_memo_id,
                cmd.account_id,
                cmd.cost_center_id,
                cmd.memo,
                cmd.amount,
                cmd.net_amount,
                cmd.taxable_amount,
                cmd.vat_percentage,
                cmd.sales_tax,
                coa.account_type_id,
                coa.gl_name,
                coa.account_code,
                coa.account_description
            FROM 
                credit_memo_details cmd
            LEFT JOIN
                chart_of_account coa ON cmd.account_id = coa.id
            LEFT JOIN
                cost_center cc ON cmd.cost_center_id = cc.id
            WHERE 
                cmd.credit_memo_id = :credit_memo_id
        ');

        $stmt->bindParam(':credit_memo_id', $credit_memo_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $details = [];
        while ($row = $stmt->fetch()) {
            $details[] = [
                'id' => $row['id'],
                'credit_memo_id' => $row['credit_memo_id'],
                'account_id' => $row['account_id'],
                'cost_center_id' => $row['cost_center_id'],
                'memo' => $row['memo'],
                'amount' => $row['amount'],
                'net_amount' => $row['net_amount'],
                'taxable_amount' => $row['taxable_amount'],
                'vat_percentage' => $row['vat_percentage'],
                'sales_tax' => $row['sales_tax'],
                'account_type_id' => $row['account_type_id'],
                'gl_name' => $row['gl_name'],
                'account_code' => $row['account_code'],
                'account_description' => $row['account_description'],
            ];
        }

        return $details;
    }
    // update CreditBalance base on customer
    public static function addCreditBalance($total_amount_due, $customer_id)
    {
        global $connection;

        $stmt = $connection->prepare('UPDATE customers SET total_credit_memo = :total_amount_due WHERE id = :customer_id');

        $stmt->bindParam(":total_amount_due", $total_amount_due);
        $stmt->bindParam(":customer_id", $customer_id);

        $stmt->execute();
    }
    // getTotalCounts of Invoice row
    public static function getTotalCount()
    {
        global $connection;

        $stmt = $connection->query('SELECT COUNT(*) as total_count FROM sales_invoice');
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total_count'];
    }
    // getUnpaidCoutns of invoice_status = 0
    public static function getUnpaidCount()
    {
        global $connection;

        $stmt = $connection->prepare('SELECT COUNT(*) as unpaid_count FROM sales_invoice WHERE invoice_status = 0');
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['unpaid_count'];
    }
    // getPaidCounts of invoice_status = 1
    public static function getPaidCount()
    {
        global $connection;

        $stmt = $connection->prepare('SELECT COUNT(*) as paid_count FROM sales_invoice WHERE invoice_status = 1');
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['paid_count'];
    }

    public static function getAvailableCreditsForCustomer($customerId)
    {
        global $connection;
        $stmt = $connection->prepare("
            SELECT id, credit_no, credit_date, total_amount_due as amount, 
                   memo, net_amount_due as remaining_amount
            FROM credit_memo 
            WHERE customer_id = ? AND net_amount_due > 0
            ORDER BY credit_date ASC
        ");
        $stmt->execute([$customerId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // GET LAST CREDIT_NO
    public static function getLastCreditNo() {
        global $connection;
    
        try {
            // Prepare and execute the query
            $stmt = $connection->prepare("
                SELECT credit_no 
                FROM credit_memo 
                WHERE credit_no IS NOT NULL AND credit_no <> '' 
                ORDER BY credit_no DESC 
                LIMIT 1
            ");
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetch();
    
            // Extract the numeric part of the last credit number
            if ($result) {
                $latestNo = $result['credit_no'];
                // Assuming the format is 'CM' followed by digits
                $numericPart = intval(substr($latestNo, 2)); // 'CM' is 2 characters
                $newNo = $numericPart + 1;
            } else {
                // If no valid credit number exists, start with 1
                $newNo = 1;
            }
            // Format the new number with leading zeros
            $newCreditNo = 'CM' . str_pad($newNo, 9, '0', STR_PAD_LEFT);
    
            return $newCreditNo;
        } catch (PDOException $e) {
            // Handle potential exceptions
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }
    

    public static function saveDraft(
        $credit_date, $customer_id, $credit_account_id, $memo, $gross_amount, $net_amount_due,
        $vat_percentage_amount, $net_of_vat, $tax_withheld_amount, $tax_withheld_account_id,
        $total_amount_due, $items
    ) {
        global $connection;
    
        try {
            $connection->beginTransaction();
    
            // Insert into credit_memo table with draft status
            $sql = "INSERT INTO credit_memo (
                credit_date, customer_id, credit_account, memo, gross_amount, net_amount_due,
                vat_percentage_amount, net_of_vat, tax_withheld_amount, total_amount_due, status
            ) VALUES (
                :credit_date, :customer_id, :credit_account, :memo, :gross_amount, :net_amount_due,
                :vat_percentage_amount, :net_of_vat, :tax_withheld_amount, :total_amount_due, :status
            )";
    
            $stmt = $connection->prepare($sql);
            $stmt->bindParam(':credit_date', $credit_date);
            $stmt->bindParam(':customer_id', $customer_id);
            $stmt->bindParam(':credit_account', $credit_account_id);
            $stmt->bindParam(':memo', $memo);
            $stmt->bindParam(':gross_amount', $gross_amount);
            $stmt->bindParam(':net_amount_due', $net_amount_due);
            $stmt->bindParam(':vat_percentage_amount', $vat_percentage_amount);
            $stmt->bindParam(':net_of_vat', $net_of_vat);
            $stmt->bindParam(':tax_withheld_amount', $tax_withheld_amount);
            $stmt->bindParam(':total_amount_due', $total_amount_due);
            $stmt->bindValue(':status', 4, PDO::PARAM_INT); // Set status to 4 for draft
    
            $stmt->execute();
    
            $credit_memo_id = $connection->lastInsertId();
    
            // Ensure $items is an array
            if (is_array($items) && !empty($items)) {
                $itemSql = "INSERT INTO credit_memo_details (
                    credit_memo_id, account_id, cost_center_id, memo, amount, net_amount, taxable_amount, vat_percentage, sales_tax, sales_tax_account_id
                ) VALUES (
                    :credit_memo_id, :account_id, :cost_center_id, :memo, :amount, :net_amount, :taxable_amount, :vat_percentage, :sales_tax, :sales_tax_account_id
                )";
    
                $itemStmt = $connection->prepare($itemSql);
    
                foreach ($items as $item) {
                    $itemStmt->bindParam(':credit_memo_id', $credit_memo_id);
                    $itemStmt->bindParam(':account_id', $item['account_id']);
                    $itemStmt->bindParam(':cost_center_id', $item['cost_center_id']);
                    $itemStmt->bindParam(':memo', $item['memo']);
                    $itemStmt->bindParam(':amount', $item['amount']);
                    $itemStmt->bindParam(':net_amount', $item['net_amount']);
                    $itemStmt->bindParam(':taxable_amount', $item['taxable_amount']);
                    $itemStmt->bindParam(':vat_percentage', $item['vat_percentage']);
                    $itemStmt->bindParam(':sales_tax', $item['sales_tax']);
                    $itemStmt->bindParam(':sales_tax_account_id', $item['sales_tax_account_id']);
                    $itemStmt->execute();
                }
            }
    
            $connection->commit();
            return true;
        } catch (Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    public static function updateDraftDetails($credit_memo_id)
    {
        global $connection;
    
        $stmt = $connection->prepare('
            SELECT 
                cmd.id,
                cmd.credit_memo_id,
                cmd.account_id,
                cmd.cost_center_id,
                cmd.memo,
                cmd.amount,
                cmd.net_amount,
                cmd.taxable_amount,
                cmd.vat_percentage,
                cmd.sales_tax,
                cmd.sales_tax_account_id,
                coa.account_type_id,
                coa.gl_name,
                coa.account_code,
                coa.account_description
            FROM 
                credit_memo_details cmd
            LEFT JOIN
                chart_of_account coa ON cmd.account_id = coa.id
            LEFT JOIN
                cost_center cc ON cmd.cost_center_id = cc.id
            WHERE 
                cmd.credit_memo_id = :credit_memo_id
        ');
    
        $stmt->bindParam(':credit_memo_id', $credit_memo_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
    
        return $stmt->fetchAll(); // Return the fetched data
    }
    
    public static function updateDraft(
        $credit_memo_id, $credit_no, $credit_date, $customer_id, $customer_name, 
        $credit_account_id, $memo, $gross_amount, $net_amount_due, 
        $vat_percentage_amount, $net_of_vat, $tax_withheld_amount, 
        $tax_withheld_account_id, $total_amount_due, $items, $created_by
    ) {
        global $connection;
    
        try {
            // Start a transaction
            $connection->beginTransaction();
    
            $transaction_type = "Credit Memo";
    
            // Fetch existing credit memo details
            $existingDetails = self::updateDraftDetails($credit_memo_id);
    
            // Fetch the credit_account_id and total_amount_due from the database
            $stmt = $connection->prepare("SELECT credit_account, total_amount_due, tax_withheld_amount, credit_date FROM credit_memo WHERE id = ?");
            $stmt->execute([$credit_memo_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $credit_account_id = $result['credit_account'];
                $total_amount_due = $result['total_amount_due'];
                $tax_withheld_amount = $result['tax_withheld_amount'];
                $credit_date = $result['credit_date'];


            } else {
                throw new Exception("Credit memo not found.");
            }
    
            foreach ($existingDetails as $detail) {
                // Log the existing details into the audit trail
                self::logAuditTrail(
                    $credit_memo_id,
                    $transaction_type,
                    $credit_date,
                    $credit_no,
                    $customer_name,
                    $detail['account_id'],
                    $detail['net_amount'], 
                    0.00,
                    $created_by
                );
    
                self::logAuditTrail(
                    $credit_memo_id,
                    $transaction_type,
                    $credit_date,
                    $credit_no,
                    $customer_name,
                    $detail['sales_tax_account_id'],
                    $detail['sales_tax'],
                    0.00,
                    $created_by
                );
            }
    
            // Credit wtax Account Audit Trail
            self::logAuditTrail(
                $credit_memo_id,
                $transaction_type,
                $credit_date,
                $credit_no,
                $customer_name,
                $tax_withheld_account_id,
                0.00,
                $tax_withheld_amount,
                $created_by
            );
    
            // Credit Account Audit Trail
            self::logAuditTrail(
                $credit_memo_id,
                $transaction_type,
                $credit_date,
                $credit_no,
                $customer_name,
                $credit_account_id,  // Use the fetched credit_account_id here
                0.00,
                $total_amount_due,
                $created_by
            );
    
            // Commit the transaction
            $connection->commit();
        } catch (PDOException $e) {
            // Rollback the transaction if an error occurs
            $connection->rollback();
            throw $e;
        } catch (Exception $e) {
            // Rollback the transaction if a general error occurs
            $connection->rollback();
            throw $e;
        }
    }

    public static function void($id)
    {
        global $connection;
    
        try {
            $connection->beginTransaction();
            
            // Update the status to 3 (void) in the sales_invoice table
            $stmt = $connection->prepare("UPDATE credit_memo SET status = 3 WHERE id = :id");
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
    
}
