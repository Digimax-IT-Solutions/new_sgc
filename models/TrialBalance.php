<?php

require_once __DIR__ . '/../_init.php';

class TrialBalance
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
    }

    public function getTrialBalance($fromDate, $toDate)
    {
        $sql = "SELECT 
                    coa.account_code,
                    coa.account_name,
                    COALESCE(SUM(te.debit), 0) as total_debit,
                    COALESCE(SUM(te.credit), 0) as total_credit
                FROM chart_of_account coa
                LEFT JOIN transaction_entries te ON coa.id = te.account_code
                WHERE te.created_at BETWEEN :from_date AND :to_date
                GROUP BY coa.account_name
                ORDER BY coa.id";

        $stmt = $this->connection->prepare($sql);
        $stmt->bindParam(':from_date', $fromDate, PDO::PARAM_STR);
        $stmt->bindParam(':to_date', $toDate, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

