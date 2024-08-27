<?php

require_once __DIR__ . '/../_init.php';

class TransactionEntry
{
    private static $cache = null;

    public $id;
    public $transaction_id;
    public $transaction_type;
    public $transaction_date;
    public $customer_name;
    public $name;
    public $item_name;
    public $qty_sold;
    public $ref_no;
    public $account_id;
    public $debit;
    public $credit;
    public $balance;
    public $account_description;
    public $account_category;

    public function __construct($entry)
    {
        $this->id = $entry['id'];
        $this->transaction_id = $entry['transaction_id'];
        $this->transaction_type = $entry['transaction_type'];
        $this->transaction_date = $entry['transaction_date'];
        $this->customer_name = $entry['customer_name'] ?? '';
        $this->name = $entry['name'] ?? '';
        $this->item_name = $entry['item'] ?? '';
        $this->qty_sold = $entry['qty_sold'] ?? '';
        $this->ref_no = $entry['ref_no'];
        $this->account_id = $entry['account_id'];
        $this->debit = $entry['debit'];
        $this->credit = $entry['credit'];
        $this->balance = $entry['balance'];
        $this->account_description = $entry['account_description'];
        $this->account_category = $entry['category'];
    }

    public static function all()
    {
        global $connection;

        if (static::$cache) {
            return static::$cache;
        }

        $stmt = $connection->prepare('
            SELECT te.*, coa.account_description, at.category
            FROM transaction_entries te
            JOIN chart_of_account coa ON te.account_id = coa.id
            JOIN account_types at ON coa.account_type_id = at.id
            ORDER BY te.transaction_date DESC, te.id DESC
        ');
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        static::$cache = array_map(function ($entry) {
            return new TransactionEntry($entry);
        }, $result);

        return static::$cache;
    }

    // public static function filterByDateRange($fromDate, $toDate)
    // {
    //     global $connection;

    //     $stmt = $connection->prepare('
    //         SELECT te.*, coa.account_description, at.category
    //         FROM transaction_entries te
    //         JOIN chart_of_account coa ON te.account_id = coa.id
    //         JOIN account_types at ON coa.account_type_id = at.id
    //         WHERE DATE(te.transaction_date) BETWEEN :from_date AND :to_date
    //         ORDER BY te.transaction_date DESC, te.id DESC
    //     ');
    //     $stmt->bindParam(':from_date', $fromDate);
    //     $stmt->bindParam(':to_date', $toDate);
    //     $stmt->execute();
    //     $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //     return array_map(function ($entry) {
    //         return new TransactionEntry($entry);
    //     }, $result);
    // }

    // public static function filterByDateRange($fromDate, $toDate)
    // {
    //     global $connection;

    //     $stmt = $connection->prepare('
    //     SELECT te.*, coa.account_description, at.category
    //     FROM transaction_entries te
    //     JOIN chart_of_account coa ON te.account_id = coa.id
    //     JOIN account_types at ON coa.account_type_id = at.id
    //     WHERE DATE(te.transaction_date) BETWEEN :from_date AND :to_date
    //     ORDER BY te.transaction_date DESC, te.id DESC
    //     ');
    //     $stmt->bindParam(':from_date', $fromDate);
    //     $stmt->bindParam(':to_date', $toDate);
    //     $stmt->execute();
    //     $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //     return array_map(function ($entry) {
    //         return new TransactionEntry($entry);
    //     }, $result);
    // }

    public static function filterByDateRange($fromDate, $toDate)
    {
        global $connection;

        $stmt = $connection->prepare('
        SELECT te.*, coa.account_description, at.category
        FROM transaction_entries te
        JOIN chart_of_account coa ON te.account_id = coa.id
        JOIN account_types at ON coa.account_type_id = at.id
        WHERE DATE(te.transaction_date) BETWEEN :from_date AND :to_date
        ORDER BY te.transaction_date DESC, te.id DESC
        ');
        $stmt->bindParam(':from_date', $fromDate);
        $stmt->bindParam(':to_date', $toDate);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($entry) {
            return new TransactionEntry($entry);
        }, $result);
    }

    // public static function filterByDateRange($fromDate, $toDate)
    // {
    //     global $connection;

    //     $stmt = $connection->prepare('
    //     SELECT 
    //         te.*, 
    //         coa.account_description,
    //         CASE
    //             WHEN te.transaction_type = "Invoice" THEN si.customer_id
    //             WHEN te.transaction_type = "Payment" THEN p.customer_id
    //             WHEN te.transaction_type = "Credit Memo" THEN cm.customer_id
    //             ELSE NULL
    //         END AS customer_id,
    //         CASE
    //             WHEN te.transaction_type = "Invoice" THEN c1.customer_name
    //             WHEN te.transaction_type = "Payment" THEN c2.customer_name
    //             WHEN te.transaction_type = "Credit Memo" THEN c3.customer_name
    //             ELSE NULL
    //         END AS customer_name,
    //         CASE
    //             WHEN te.transaction_type = "Invoice" THEN sid.item_name
    //             WHEN te.transaction_type = "Payment" THEN pid.item_name
    //             WHEN te.transaction_type = "Credit Memo" THEN cmd.item_name
    //             ELSE NULL
    //         END AS item_name
    //     FROM transaction_entries te
    //     JOIN chart_of_account coa ON te.account_id = coa.id
    //     LEFT JOIN sales_invoice si ON te.transaction_id = si.id AND te.transaction_type = "Invoice"
    //     LEFT JOIN sales_invoice_details sid ON si.id = sid.invoice_id
    //     LEFT JOIN customers c1 ON si.customer_id = c1.id
    //     LEFT JOIN payments p ON te.transaction_id = p.id AND te.transaction_type = "Payment"
    //     LEFT JOIN payments_details pid ON p.id = pid.payment_id
    //     LEFT JOIN customers c2 ON p.customer_id = c2.id
    //     LEFT JOIN credit_memo cm ON te.transaction_id = cm.id AND te.transaction_type = "Credit Memo"
    //     LEFT JOIN credit_memo_details cmd ON cm.id = cmd.credit_memo_id
    //     LEFT JOIN customers c3 ON cm.customer_id = c3.id
    //     WHERE DATE(te.transaction_date) BETWEEN :from_date AND :to_date
    //     ORDER BY te.transaction_date DESC, te.id DESC
    //     ');
    //     $stmt->bindParam(':from_date', $fromDate);
    //     $stmt->bindParam(':to_date', $toDate);
    //     $stmt->execute();
    //     $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //     $entries = [];
    //     foreach ($result as $row) {
    //         $entries[] = [
    //             'id' => $row['id'],
    //             'transaction_id' => $row['transaction_id'],
    //             'transaction_type' => $row['transaction_type'],
    //             'transaction_date' => $row['transaction_date'],
    //             'ref_no' => $row['ref_no'],
    //             'account_id' => $row['account_id'],
    //             'account_description' => $row['account_description'],
    //             'debit' => $row['debit'],
    //             'credit' => $row['credit'],
    //             'balance' => $row['balance'],
    //             'customer_name' => $row['customer_name'],
    //             'item_name' => $row['item_name']
    //         ];
    //     }

    //     return $entries;
    // }

    // private static function getAdditionalInfo($row)
    // {
    //     switch ($row['transaction_type']) {
    //         case 'Invoice':
    //             return [
    //                 'customer_id' => $row['invoice_customer_id'],
    //                 'customer_name' => $row['invoice_customer_name']
    //             ];
    //         case 'Payment':
    //             return [
    //                 'customer_id' => $row['payment_customer_id'],
    //                 'customer_name' => $row['payment_customer_name']
    //             ];
    //         case 'Credit Memo':
    //             return [
    //                 'customer_id' => $row['credit_memo_customer_id'],
    //                 'customer_name' => $row['credit_memo_customer_name']
    //             ];
    //         case 'General Journal':
    //             return ['description' => $row['general_journal_description']];
    //         case 'Check Expense':
    //             return ['payee' => $row['check_payee']];
    //         default:
    //             return [];
    //     }
    // }


    // private static function formatTransaction($transaction)
    // {
    //     $formattedEntries = [];
    //     $totalDebit = 0;
    //     $totalCredit = 0;

    //     foreach ($transaction['entries'] as $entry) {
    //         $formattedEntries[] = [
    //             $entry['category'],
    //             $entry['account_description'],
    //             $entry['debit'],
    //             $entry['credit']
    //         ];
    //         $totalDebit += $entry['debit'];
    //         $totalCredit += $entry['credit'];
    //     }

    //     // Add total row
    //     $formattedEntries[] = ['Total', '', $totalDebit, $totalCredit];

    //     $formatted = [
    //         'type' => $transaction['type'],
    //         'date' => $transaction['date'],
    //         'ref_no' => $transaction['ref_no'],
    //         'entries' => $formattedEntries,
    //         'additional_info' => $transaction['additional_info']
    //     ];

    //     return $formatted;
    // }

    public static function getById($id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT te.*, coa.account_description, at.category
            FROM transaction_entries te
            JOIN chart_of_account coa ON te.account_id = coa.id
            JOIN account_types at ON coa.account_type_id = at.id
            WHERE te.id = :id
        ');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ? new TransactionEntry($result) : null;
    }
}