<?php

require_once __DIR__ . '/../_init.php';
class Payment
{
    // ... other methods ...
    public $id;
    public $customer_name;
    public $customer_tin;
    public $customer_id;
    public $account_id;
    public $billing_address;
    public $payment_method_id;
    public $payment_method_name;
    public $payment_date;
    public $ref_no;
    public $account_description;
    public $account_type_name;
    public $summary_applied_amount;
    public $summary_amount_due;
    public $balance_due;
    public $credit_balance;
    public $memo;
    public $total_amount_due;
    public $cr_no;
    public $status;
    public $print_status;
    public $details = [];

    private static $cache = null;

    public function __construct($data)
    {
        $this->id = $data['id'] ?? null;
        $this->customer_name = $data['customer_name'] ?? null;
        $this->customer_tin = $data['customer_tin'] ?? null;
        $this->customer_id = $data['customer_id'] ?? null;
        $this->billing_address = $data['billing_address'] ?? null;
        $this->payment_method_id = $data['payment_method_id'] ?? null;
        $this->payment_method_name = $data['payment_method_name'] ?? null;
        $this->payment_date = $data['payment_date'] ?? null;
        $this->ref_no = $data['ref_no'] ?? null;
        $this->account_description = $data['account_description'] ?? null;
        $this->account_type_name = $data['account_type_name'] ?? null;
        $this->summary_applied_amount = $data['summary_applied_amount'] ?? null;
        $this->balance_due = $data['balance_due'] ?? null;
        $this->credit_balance = $data['credit_balance'] ?? null;
        $this->account_id = $data['account_id'] ?? null;
        $this->memo = $data['memo'] ?? null;
        $this->summary_amount_due = $data['summary_amount_due'] ?? null;
        $this->total_amount_due = $data['total_amount_due'] ?? null;
        $this->cr_no = $data['cr_no'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->print_status = $data['print_status'] ?? null;


        // Initialize details as an empty array
        $this->details = [];

        // Populate other properties as before...

        // Optionally, you can populate details if provided in $formData
        if (isset($data['details'])) {
            foreach ($data['details'] as $detail) {
                // Push each detail to the details array
                $this->details[] = $detail;
            }
        }

    }


    public static function add($customer_id, $payment_date, $payment_method_id, $account_id, $ref_no, $cr_no, $customer_name, $memo, $summary_amount_due, $summary_applied_amount, $selected_invoices, $created_by)
    {
        global $connection;
    
        try {
            $connection->beginTransaction();
    
            $transaction_type = 'Payment';
    
            // Insert main payment record
            $sql = "INSERT INTO payments (customer_id, payment_date, payment_method_id, account_id, ref_no, cr_no, memo, summary_amount_due, summary_applied_amount) 
                    VALUES (:customer_id, :payment_date, :payment_method_id, :account_id, :ref_no, :cr_no, :memo, :summary_amount_due, :summary_applied_amount)";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'customer_id' => $customer_id,
                'payment_date' => $payment_date,
                'payment_method_id' => $payment_method_id,
                'account_id' => $account_id,
                'ref_no' => $ref_no,
                'cr_no' => $cr_no,
                'memo' => $memo,
                'summary_amount_due' => $summary_amount_due,
                'summary_applied_amount' => $summary_applied_amount
            ]);
    
            // GET LAST INSERT PAYMENT ID
            $payment_id = $connection->lastInsertId();
    
            // LOG AUDIT TRAIL
            // Log Undeposited funds for this item
            self::logAuditTrail(
                $payment_id,
                $transaction_type,
                $payment_date,
                $cr_no,
                $customer_name,
                $account_id,
                $summary_applied_amount,
                0.00,
                $created_by
            );
    
            $total_amount_applied = 0;
    
            // Insert payment details and update invoice balances
            foreach ($selected_invoices as $invoice) {
                // Insert payment detail
                $sql = "INSERT INTO payment_details (payment_id, invoice_id, amount_applied, discount_amount, credit_amount) 
                        VALUES (:payment_id, :invoice_id, :amount_applied, :discount_amount, :credit_amount)";
                $stmt = $connection->prepare($sql);
                $stmt->execute([
                    'payment_id' => $payment_id,
                    'invoice_id' => $invoice['invoice_id'],
                    'amount_applied' => $invoice['amount_applied'],
                    'discount_amount' => $invoice['discount_amount'],
                    'credit_amount' => $invoice['credit_amount']
                ]);
    
                // First update the balance_due
                $sql1 = "UPDATE sales_invoice
                         SET balance_due = balance_due - :amount_applied
                         WHERE id = :invoice_id";
                $stmt1 = $connection->prepare($sql1);
                $stmt1->execute([
                    'amount_applied' => $invoice['amount_applied'],
                    'invoice_id' => $invoice['invoice_id']
                ]);
    
                // Then update the invoice_status based on the new balance_due
                $sql2 = "UPDATE sales_invoice
                         SET invoice_status = CASE
                             WHEN balance_due <= 0 THEN 1
                             WHEN balance_due > 0 THEN 2
                             ELSE invoice_status
                         END
                         WHERE id = :invoice_id";
                $stmt2 = $connection->prepare($sql2);
                $stmt2->execute([
                    'invoice_id' => $invoice['invoice_id']
                ]);
    
                $total_amount_applied += $invoice['amount_applied'];
    
                // Log Accounts Receivable for this item
                self::logAuditTrail(
                    $payment_id,
                    $transaction_type,
                    $payment_date,
                    $cr_no,
                    $customer_name,
                    $invoice['invoice_account_id'],
                    0.00,
                    $invoice['amount_applied'],
                    $created_by
                );
            }
    
            // Update customer's credit balance
            $sql = "UPDATE customers 
                    SET credit_balance = credit_balance - :total_amount_applied 
                    WHERE id = :customer_id";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'total_amount_applied' => $total_amount_applied,
                'customer_id' => $customer_id
            ]);
    
            $connection->commit();
            return $payment_id;
        } catch (Exception $e) {
            $connection->rollBack();
            throw $e;
        }
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
                    ) VALUES (?,?,?,?,?,?, ?, ?, ?, NOW())
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

    
    public static function all()
    {
        global $connection;
    
        try {
            $stmt = $connection->prepare('
                SELECT 
                    p.*, 
                    c.customer_name, 
                    c.billing_address,
                    pm.payment_method_name, 
                    pm.description as payment_description, 
                    coa.account_code,
                    coa.account_description,
                    at.name as account_type_name
                FROM payments p
                INNER JOIN customers c ON p.customer_id = c.id
                INNER JOIN payment_method pm ON p.payment_method_id = pm.id
                INNER JOIN chart_of_account coa ON p.account_id = coa.id
                LEFT JOIN account_types at ON coa.account_type_id = at.id
            ');
    
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
    
            $payments = [];
            while ($row = $stmt->fetch()) {
                $payment = [
                    'id' => $row['id'],
                    'customer_name' => $row['customer_name'],
                    'billing_address' => $row['billing_address'],
                    'summary_applied_amount' => $row['summary_applied_amount'],
                    'payment_date' => $row['payment_date'],
                    'ref_no' => $row['ref_no'],
                    'cr_no' => $row['cr_no'],

                    'payment_method_name' => $row['payment_method_name'],
                    'payment_description' => $row['payment_description'],
                    'account_code' => $row['account_code'],
                    'account_description' => $row['account_description'],
                    'account_type_name' => $row['account_type_name'],
                    'status' => $row['status']
                ];
                $payments[] = new Payment($payment);
            }
    
            return $payments;
    
        } catch (PDOException $e) {
            // Handle and log the error
            error_log('Database error: ' . $e->getMessage());
            return [];
        }
    }
    
    public static function getPaymentDetails($payment_id)
    {
        global $connection;
    
        try {
            $stmt = $connection->prepare('
                SELECT 
                    pd.id,
                    pd.payment_id,
                    pd.invoice_id,
                    pd.amount_applied,
                    pd.discount_amount,
                    pd.credit_amount,
                    si.invoice_number,
                    si.total_amount_due,
                    si.invoice_date,
                    si.invoice_account_id,
                    si.balance_due
                FROM payment_details pd
                LEFT JOIN sales_invoice si ON pd.invoice_id = si.id
                WHERE pd.payment_id = :payment_id
            ');
    
            $stmt->bindParam(':payment_id', $payment_id, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
    
            $details = [];
            while ($row = $stmt->fetch()) {
                $details[] = $row; // Directly use the fetched row as it is already formatted correctly
            }
    
            return $details;
    
        } catch (PDOException $e) {
            // Handle and log the error
            error_log('Database error: ' . $e->getMessage());
            return [];
        }
    }
    
    public static function find($id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                p.id,
                p.customer_id,
                p.payment_date,
                p.payment_method_id,
                p.account_id,
                p.ref_no,
                p.memo,
                p.summary_amount_due,
                p.summary_applied_amount,
                p.cr_no,
                p.status,
                p.print_status,
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
                coas.account_description,
                pm.payment_method_name,
                pm.description
            FROM payments p
            INNER JOIN customers c ON p.customer_id = c.id
            INNER JOIN chart_of_account coas ON p.account_id = coas.id
            INNER JOIN payment_method pm ON p.payment_method_id = pm.id
            WHERE p.id = :id
        ');

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        $data['details'] = self::getDetails($id);

        return new Payment($data);
    }

    public static function getDetails($payment_id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                pmd.id,
                pmd.payment_id,
                pmd.invoice_id,
                pmd.amount_applied,
                pmd.discount_amount,
                pmd.credit_amount,
                si.invoice_number,
                si.invoice_date,
                si.invoice_account_id,
                si.invoice_due_date,
                si.customer_po,
                si.so_no,
                si.rep,
                si.customer_id,
                si.payment_method,
                si.location,
                si.terms,
                si.memo,
                si.total_amount_due,
                si.invoice_status,
                si.status,
                si.balance_due,
                si.total_paid,
                si.print_status,
                cm.customer_name
            FROM 
                payment_details pmd
            LEFT JOIN
                sales_invoice si ON pmd.invoice_id = si.id
            LEFT JOIN
                customers cm ON si.customer_id = cm.id
            WHERE 
                pmd.payment_id = :payment_id
        ');

        $stmt->bindParam(':payment_id', $payment_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $details = [];
        while ($row = $stmt->fetch()) {
            $details[] = [
                'id' => $row['id'],
                'payment_id' => $row['payment_id'],
                'invoice_id' => $row['invoice_id'],
                'amount_applied' => $row['amount_applied'],
                'discount_amount' => $row['discount_amount'],
                'credit_amount' => $row['credit_amount'],

                'invoice_number' => $row['invoice_number'],
                'invoice_date' => $row['invoice_date'],
                'invoice_account_id' => $row['invoice_account_id'],
                'invoice_due_date' => $row['invoice_due_date'],
                'customer_po' => $row['customer_po'],
                'so_no' => $row['so_no'],
                'rep' => $row['rep'],

                'customer_name' => $row['customer_name'],
                'customer_id' => $row['customer_id'],
                'payment_method' => $row['payment_method'],
                'location' => $row['location'],
                'terms' => $row['terms'],
                'memo' => $row['memo'],
                'total_amount_due' => $row['total_amount_due'],
                'invoice_status' => $row['invoice_status'],

                'status' => $row['status'],
                'balance_due' => $row['balance_due'],
                'total_paid' => $row['total_paid'],
                'print_status' => $row['print_status']
            ];
        }

        return $details;
    }

    // GET LAST CR_NO 
    public static function getLastCrNo()
    {
        global $connection;
    
        try {
            // Prepare and execute the query to get the highest CR number, ignoring null or empty values
            $stmt = $connection->prepare("
                SELECT cr_no 
                FROM payments 
                WHERE cr_no IS NOT NULL AND cr_no <> '' 
                ORDER BY cr_no DESC 
                LIMIT 1
            ");
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetch();
    
            // Extract the numeric part of the last CR number
            if ($result) {
                $latestNo = $result['cr_no'];
                // Assuming the format is 'CR' followed by digits
                $numericPart = intval(substr($latestNo, 2)); // 'CR' is 2 characters
                $newNo = $numericPart + 1;
            } else {
                // If no valid CR number exists, start with 1
                $newNo = 1;
            }
    
            // Format the new number with leading zeros
            $newCrNo = 'CR' . str_pad($newNo, 9, '0', STR_PAD_LEFT);
    
            return $newCrNo;
        } catch (PDOException $e) {
            // Handle potential exceptions
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }

    public static function addDraft($customer_id, $payment_date, $payment_method_id, $account_id, $ref_no,  $memo, $summary_amount_due, $summary_applied_amount, $selected_invoices, $created_by)
    {
        global $connection;
    
        try {
            $connection->beginTransaction();
    
            // Insert main payment record as draft
            $sql = "INSERT INTO payments (customer_id, payment_date, payment_method_id, account_id, ref_no, memo, summary_amount_due, summary_applied_amount, status) 
                    VALUES (:customer_id, :payment_date, :payment_method_id, :account_id, :ref_no, :memo, :summary_amount_due, :summary_applied_amount, :status)";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'customer_id' => $customer_id,
                'payment_date' => $payment_date,
                'payment_method_id' => $payment_method_id,
                'account_id' => $account_id,
                'ref_no' => $ref_no,
                'memo' => $memo,
                'summary_amount_due' => $summary_amount_due,
                'summary_applied_amount' => $summary_applied_amount,
                'status' => '4'
            ]);
    
            $payment_id = $connection->lastInsertId();
    
            // Insert payment details
            foreach ($selected_invoices as $invoice) {
                $sql = "INSERT INTO payment_details (payment_id, invoice_id, amount_applied, discount_amount, credit_amount) 
                        VALUES (:payment_id, :invoice_id, :amount_applied, :discount_amount, :credit_amount)";
                $stmt = $connection->prepare($sql);
                $stmt->execute([
                    'payment_id' => $payment_id,
                    'invoice_id' => $invoice['invoice_id'],
                    'amount_applied' => $invoice['amount_applied'],
                    'discount_amount' => $invoice['discount_amount'],
                    'credit_amount' => $invoice['credit_amount']
                ]);
            }
    
            $connection->commit();
            return $payment_id;
        } catch (Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    public static function updateDraftDetails($payment_id)
    {
        global $connection;
    
        $stmt = $connection->prepare('
             SELECT 
                pmd.id,
                pmd.payment_id,
                pmd.invoice_id,
                pmd.amount_applied,
                pmd.discount_amount,
                pmd.credit_amount,
                si.invoice_number,
                si.invoice_date,
                si.invoice_account_id,
                si.invoice_due_date,
                si.customer_po,
                si.so_no,
                si.rep,
                si.customer_id,
                si.payment_method,
                si.location,
                si.terms,
                si.memo,
                si.total_amount_due,
                si.invoice_status,
                si.status,
                si.balance_due,
                si.total_paid,
                si.print_status,
                cm.customer_name
            FROM 
                payment_details pmd
            LEFT JOIN
                sales_invoice si ON pmd.invoice_id = si.id
            LEFT JOIN
                customers cm ON si.customer_id = cm.id
            WHERE 
                pmd.payment_id = :payment_id
        ');
    
        $stmt->bindParam(':payment_id', $payment_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
    
        return $stmt->fetchAll(); // Return the fetched data
    }
    
    public static function updateDraft($payment_id, $customer_id, $payment_date, $payment_method_id, $account_id, $ref_no, $cr_no, $customer_name, $memo, $summary_amount_due, $summary_applied_amount, $selected_invoices, $created_by)
    {
        global $connection;


        try {
            $connection->beginTransaction();

            $transaction_type = 'Payment';

            // Fetch existing credit memo details
            $existingDetails = self::updateDraftDetails($payment_id);

            // Fetch the credit_account_id and total_amount_due from the database
            $stmt = $connection->prepare("SELECT payment_date, cr_no, account_id FROM payments WHERE id = ?");
            $stmt->execute([$payment_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                $payment_date = $result['payment_date'];
                $cr_no = $result['cr_no'];
                $account_id = $result['account_id'];

            } else {
                throw new Exception("Credit memo not found.");
            }


            $total_amount_applied = 0;

            // Insert updated payment details and update invoice balances
            foreach ($existingDetails as $invoice) {
                // First update the balance_due
                $sql1 = "UPDATE sales_invoice
                        SET balance_due = balance_due - :amount_applied
                        WHERE id = :invoice_id";
                $stmt1 = $connection->prepare($sql1);
                $stmt1->execute([
                    'amount_applied' => $invoice['amount_applied'],
                    'invoice_id' => $invoice['invoice_id']
                ]);

                self::logAuditTrail(
                    $payment_id,
                    $transaction_type,
                    $payment_date,
                    $cr_no,
                    $customer_name,
                    $account_id,
                    $summary_applied_amount,
                    0.00,
                    $created_by
                );
                
                // Then update the invoice_status based on the new balance_due
                $sql2 = "UPDATE sales_invoice
                        SET invoice_status = CASE
                            WHEN balance_due <= 0 THEN 1
                            WHEN balance_due > 0 THEN 2
                            ELSE invoice_status
                        END
                        WHERE id = :invoice_id";
                $stmt2 = $connection->prepare($sql2);
                $stmt2->execute([
                    'invoice_id' => $invoice['invoice_id']
                ]);

                $total_amount_applied += $invoice['amount_applied'];

                // Log Accounts Receivable for this item
                self::logAuditTrail(
                    $payment_id,
                    $transaction_type,
                    $payment_date,
                    $cr_no,
                    $customer_name,
                    $account_id,
                    0.00,
                    $invoice['amount_applied'],
                    $created_by
                );
            }

            // Update customer's credit balance
            $sql = "UPDATE customers 
                    SET credit_balance = credit_balance - :total_amount_applied 
                    WHERE id = :customer_id";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'total_amount_applied' => $total_amount_applied,
                'customer_id' => $customer_id
            ]);

            $connection->commit();
            return $payment_id;
        } catch (Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    public static function void($id)
    {
        global $connection;
    
        try {
            $connection->beginTransaction();
            
            // Update the status to 3 (void) in the sales_invoice table
            $stmt = $connection->prepare("UPDATE payments SET status = 3 WHERE id = :id");
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