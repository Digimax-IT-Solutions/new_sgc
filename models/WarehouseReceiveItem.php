<?php

require_once __DIR__ . '/../_init.php';

class WarehouseReceiveItem
{

    public function __construct()
    {

    }

    public static function add(
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
    ) {
        global $connection;

        $stmt = $connection->prepare("
            INSERT INTO receive_items (
                receive_account_id, receive_no, vendor_id, location, terms,
                receive_date, receive_due_date, memo, gross_amount,
                discount_amount, net_amount, input_vat, vatable, zero_rated,
                vat_exempt, total_amount
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
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
            error_log("SQL Error: " . implode(", ", $stmt->errorInfo()));
            throw new Exception("Failed to submit receiving report.");
        }

        return $connection->lastInsertId();  // Return the ID of the newly inserted record
    }

    public static function addItems()
    {

    }

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

}