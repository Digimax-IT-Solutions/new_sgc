<?php

require_once __DIR__ . '/../_init.php';

class AuditTrail
{
    public $id;
    public $transaction_type;
    public $transaction_date;
    public $ref_no;
    public $name;
    public $account_id;
    public $account_description;
    public $debit;
    public $credit;
    public $created_at;
    public $created_by;
    public $state;
    private static $cache = null;

    public function __construct($detail)
    {
        $this->id = $detail['id'];
        $this->transaction_type = $detail['transaction_type'];
        $this->transaction_date = $detail['transaction_date'];
        $this->ref_no = $detail['ref_no'];
        $this->name = $detail['name'];
        $this->account_id = $detail['account_id'];
        $this->account_description = $detail['account_description'];
        $this->debit = $detail['debit'];
        $this->credit = $detail['credit'];
        $this->created_at = $detail['created_at'];
        $this->created_by = $detail['created_by'];
        $this->state = $detail['state'];
    }

    public static function all()
    {
        global $connection;

        if (static::$cache)
            return static::$cache;

        $stmt = $connection->prepare('
            SELECT at.*, coa.account_description
            FROM `audit_trail` at
            INNER JOIN `chart_of_account` coa ON at.account_id = coa.id
            ORDER BY at.created_at DESC
        ');

        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        static::$cache = array_map(function ($item) {
            return new AuditTrail($item);
        }, $result);

        return static::$cache;
    }

    public static function filterByDateRange($startDate, $endDate)
    {
        global $connection;

        $sql = 'SELECT at.*, coa.account_description FROM `audit_trail` at ';
        $sql .= 'INNER JOIN `chart_of_account` coa ON at.account_id = coa.id ';
        $sql .= 'WHERE DATE(at.created_at) BETWEEN :start_date AND :end_date ';
        $sql .= 'ORDER BY at.created_at DESC';

        $stmt = $connection->prepare($sql);
        $stmt->bindParam(':start_date', $startDate, PDO::PARAM_STR);
        $stmt->bindParam(':end_date', $endDate, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Assuming AuditTrail is a class representing each row, you can map the result to objects
        return array_map(function ($item) {
            return new AuditTrail($item);
        }, $result);
    }

}

?>