<?php

require_once __DIR__ . '/../_init.php';
class Payment
{
    // ... other methods ...

    public static function add($customer_id, $payment_date, $payment_method_id, $account_id, $ref_no, $customer_name, $memo, $summary_amount_due, $summary_applied_amount, $selected_invoices, $created_by)
    {
        global $connection;

        try {
            $connection->beginTransaction();

            $transaction_type = 'Payment';

            // Insert main payment record
            $sql = "INSERT INTO payments (customer_id, payment_date, payment_method_id, account_id, ref_no, memo, summary_amount_due, summary_applied_amount) 
                    VALUES (:customer_id, :payment_date, :payment_method_id, :account_id, :ref_no, :memo, :summary_amount_due, :summary_applied_amount)";
            $stmt = $connection->prepare($sql);
            $stmt->execute([
                'customer_id' => $customer_id,
                'payment_date' => $payment_date,
                'payment_method_id' => $payment_method_id,
                'account_id' => $account_id,
                'ref_no' => $ref_no,
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
                $ref_no,
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

                // Update invoice balance and status
                $sql = "UPDATE sales_invoice 
                SET balance_due = balance_due - :amount_applied, 
                    invoice_status = CASE 
                        WHEN balance_due - :amount_applied <= 0 THEN 1 
                        WHEN balance_due - :amount_applied > 0 THEN 2 
                        ELSE invoice_status 
                    END 
                WHERE id = :invoice_id";
                $stmt = $connection->prepare($sql);
                $stmt->execute([
                    'amount_applied' => $invoice['amount_applied'],
                    'invoice_id' => $invoice['invoice_id']
                ]);

                $total_amount_applied += $invoice['amount_applied'];

                // Log Accounts Receivable for this item
                self::logAuditTrail(
                    $payment_id,
                    $transaction_type,
                    $payment_date,
                    $ref_no,
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
}