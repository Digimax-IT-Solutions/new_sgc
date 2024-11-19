<?php

require_once __DIR__ . '/../_init.php';

class GeneralLedger
{
    public function __construct() {}

    public static function getGeneralLedger()
    {
        global $connection;

        // Define the query with recursive CTE
        $query = "
            WITH RECURSIVE account_hierarchy AS (
            -- Base case: Get all parent accounts
            SELECT 
                id,
                account_code,
                account_name,
                sub_account_id,
                1 as level
            FROM chart_of_account
            WHERE sub_account_id IS NULL

            UNION ALL

            -- Recursive case: Get all child accounts
            SELECT 
                c.id,
                c.account_code,
                c.account_name,
                c.sub_account_id,
                h.level + 1
            FROM chart_of_account c
            INNER JOIN account_hierarchy h ON h.id = c.sub_account_id
        )

        SELECT 
            coa.account_code,
            coa.account_name,
            at.category,
            at.name as account_type,
            te.transaction_date,
            te.ref_no,
            te.transaction_type,
            te.name as description,
            te.debit,
            te.credit,
            te.balance as running_balance
        FROM transaction_entries te
        JOIN chart_of_account coa ON te.account_id = coa.id
        JOIN account_types at ON coa.account_type_id = at.id
        ORDER BY 
            coa.account_code,
            te.transaction_date,
            te.id;
        ";

        try {
            // Prepare and execute the query
            $stmt = $connection->prepare($query);
            $stmt->execute();

            // Fetch the results
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $results = $stmt->fetchAll();

            return $results; // Return the general ledger data
        } catch (PDOException $e) {
            // Handle any errors
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
