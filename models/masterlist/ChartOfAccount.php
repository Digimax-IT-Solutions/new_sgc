<?php

require_once __DIR__ . '/../../_init.php';

class ChartOfAccount
{
    public $id;

    public $account_type;
    public $account_type_id;
    public $account_code;
    public $account_name;
    public $account_description;
    public $sub_account_id;
    public $sub_account_name;

    // New property to hold sub-accounts
    public $sub_accounts = []; // Initialize as an empty array

    private static $cache = null;

    public function __construct($data)
    {
        $this->id = $data['account_id'];
        $this->account_type = $data['account_type'];
        $this->account_type_id = $data['account_type_id'] ?? null;
        $this->account_code = $data['account_code'];
        $this->account_name = $data['account_name'];
        $this->account_description = $data['account_description'];
        $this->sub_account_id = $data['sub_account_id'];
        $this->sub_account_name = $data['sub_account_name'];

        // Ensure sub_accounts is initialized as an empty array
        $this->sub_accounts = [];
    }

    public static function findById($id)
    {
        if (empty($id)) {
            return null;
        }

        global $connection;

        $stmt = $connection->prepare('
            SELECT
                coa.id AS account_id,
                at.id AS account_type_id,
                at.name AS account_type,
                coa.account_code,
                coa.account_name,
                coa.account_description,
                coa.sub_account_id,
                sub.account_name AS sub_account_name
            FROM
                chart_of_account coa
            LEFT JOIN
                account_types at ON coa.account_type_id = at.id
            LEFT JOIN
                chart_of_account sub ON coa.sub_account_id = sub.id
            WHERE
                coa.id = :id');

        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetch();  // Fetch a single row

        if ($result) {
            return new ChartOfAccount($result);
        }

        return null;
    }


    public static function all()
    {
        global $connection;

        if (static::$cache)
            return static::$cache;

        $stmt = $connection->prepare('
            SELECT
                coa.id AS account_id,
                at.name AS account_type,
                coa.account_code,
                coa.account_name,
                coa.account_description,
                coa.sub_account_id,
                sub.account_name AS sub_account_name
            FROM
                chart_of_account coa
            LEFT JOIN
                account_types at ON coa.account_type_id = at.id
            LEFT JOIN
                chart_of_account sub ON coa.sub_account_id = sub.id
            ');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        static::$cache = array_map(function ($item) {
            return new ChartOfAccount($item);
        }, $result);

        return static::$cache;
    }

    public static function add($account_code, $account_type_id, $account_name, $account_description, $sub_account_id)
    {
        global $connection;

        if (static::findByAccountCode($account_code)) {
            throw new Exception('Account with this code already exists');
        }

        $stmt = $connection->prepare('INSERT INTO `chart_of_account` 
                                      (account_code, account_type_id, account_name, account_description, sub_account_id) 
                                      VALUES (:account_code, :account_type_id, :account_name, :account_description, :sub_account_id)');
        $stmt->bindParam("account_code", $account_code);
        $stmt->bindParam("account_type_id", $account_type_id);
        $stmt->bindParam("account_description", $account_description);
        $stmt->bindParam("account_name", $account_name);
        $stmt->bindParam("sub_account_id", $sub_account_id);
        $stmt->execute();
    }





    public function delete()
    {
        global $connection;

        $stmt = $connection->prepare('DELETE FROM `chart_of_account` WHERE id=:id');
        $stmt->bindParam('id', $this->id);
        $stmt->execute();
    }



    public static function findByAccountCode($account_code)
    {
        global $connection;

        $stmt = $connection->prepare("SELECT * FROM `chart_of_account` WHERE account_code=:account_code");
        $stmt->bindParam("account_code", $account_code);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new ChartOfAccount($result[0]);
        }

        return null;
    }







    // public static function allGroupedByCategory()
    // {
    //     global $connection;

    //     if (static::$cache)
    //         return static::$cache;

    //     $stmt = $connection->prepare('
    //         SELECT
    //             coa.id AS account_id,
    //             at.id AS account_type_id,
    //             at.name AS account_type,
    //             at.category,
    //             at.type_order,
    //             coa.account_code,
    //             coa.account_name,
    //             coa.account_description,
    //             coa.sub_account_id,
    //             sub.account_name AS sub_account_name,
    //             CASE
    //                 WHEN at.category = "ASSETS" THEN 1
    //                 WHEN at.category = "LIABILITIES" THEN 2
    //                 WHEN at.category = "EQUITY" THEN 3
    //                 WHEN at.category = "INCOME" THEN 4
    //                 WHEN at.category = "COST OF GOODS SOLD" THEN 5
    //                 WHEN at.category = "EXPENSE" THEN 6
    //                 WHEN at.category = "OTHER INCOME" THEN 7
    //                 WHEN at.category = "OTHER EXPENSE" THEN 8
    //                 ELSE 9
    //             END AS category_order
    //         FROM
    //             chart_of_account coa
    //         LEFT JOIN
    //             account_types at ON coa.account_type_id = at.id
    //         LEFT JOIN
    //             chart_of_account sub ON coa.sub_account_id = sub.id
    //         ORDER BY
    //             category_order, at.type_order, coa.account_code
    //         ');
    //     $stmt->execute();
    //     $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //     $grouped = [];
    //     $categoryOrder = [
    //         'ASSETS',
    //         'LIABILITIES',
    //         'EQUITY',
    //         'INCOME',
    //         'COST OF GOODS SOLD',
    //         'EXPENSE',
    //         'OTHER INCOME',
    //         'OTHER EXPENSE'
    //     ];

    //     foreach ($categoryOrder as $category) {
    //         $grouped[$category] = [];
    //     }

    //     foreach ($result as $row) {
    //         $category = $row['category'];
    //         $typeId = $row['account_type_id'];

    //         if (!isset($grouped[$category][$typeId])) {
    //             $grouped[$category][$typeId] = [
    //                 'type_name' => $row['account_type'],
    //                 'accounts' => []
    //             ];
    //         }

    //         $grouped[$category][$typeId]['accounts'][] = new ChartOfAccount($row);
    //     }

    //     // Remove any empty categories
    //     $grouped = array_filter($grouped);

    //     static::$cache = $grouped;
    //     return static::$cache;
    // }


    public static function allGroupedByCategory()
    {
        global $connection;

        if (static::$cache) {
            return static::$cache;
        }

        $stmt = $connection->prepare('
        SELECT
            coa.id AS account_id,
            at.id AS account_type_id,
            at.name AS account_type,
            at.category,
            at.type_order,
            coa.account_code,
            coa.account_name,
            coa.account_description,
            coa.sub_account_id,
            sub.account_name AS sub_account_name,
            sub.id AS sub_account_id,
            CASE
                WHEN at.category = "ASSETS" THEN 1
                WHEN at.category = "LIABILITIES" THEN 2
                WHEN at.category = "EQUITY" THEN 3
                WHEN at.category = "INCOME" THEN 4
                WHEN at.category = "COST OF GOODS SOLD" THEN 5
                WHEN at.category = "EXPENSE" THEN 6
                WHEN at.category = "OTHER INCOME" THEN 7
                WHEN at.category = "OTHER EXPENSE" THEN 8
                ELSE 9
            END AS category_order
        FROM
            chart_of_account coa
        LEFT JOIN
            account_types at ON coa.account_type_id = at.id
        LEFT JOIN
            chart_of_account sub ON coa.sub_account_id = sub.id
        ORDER BY
            category_order, at.type_order, coa.account_code
    ');
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [];
        $categoryOrder = [
            'ASSETS',
            'LIABILITIES',
            'EQUITY',
            'INCOME',
            'COST OF GOODS SOLD',
            'EXPENSE',
            'OTHER INCOME',
            'OTHER EXPENSE'
        ];

        foreach ($categoryOrder as $category) {
            $grouped[$category] = [];
        }

        // Group by category, account type, and handle sub-accounts
        foreach ($result as $row) {
            $category = $row['category'];
            $typeId = $row['account_type_id'];
            $accountId = $row['account_id'];

            if (!isset($grouped[$category][$typeId])) {
                $grouped[$category][$typeId] = [
                    'type_name' => $row['account_type'],
                    'accounts' => []
                ];
            }

            // If it's a sub-account, push it to its parent's sub_accounts array
            if ($row['sub_account_id']) {
                if (!isset($grouped[$category][$typeId]['accounts'][$row['sub_account_id']])) {
                    // Create the parent account if not already created
                    $grouped[$category][$typeId]['accounts'][$row['sub_account_id']] = new ChartOfAccount([
                        'account_id' => $row['sub_account_id'],
                        'account_type' => $row['account_type'],
                        'account_code' => $row['account_code'],
                        'account_name' => $row['sub_account_name'],
                        'account_description' => $row['account_description']
                    ]);
                }

                // Add the sub-account to its parent account
                $grouped[$category][$typeId]['accounts'][$row['sub_account_id']]->sub_accounts[] = new ChartOfAccount($row);
            } else {
                // If it's not a sub-account, add it normally
                $grouped[$category][$typeId]['accounts'][$accountId] = new ChartOfAccount($row);
            }
        }

        static::$cache = $grouped;
        return static::$cache;
    }



    public function update($account_code, $account_type_id, $account_name, $account_description, $sub_account_id)
    {
        global $connection;

        // Prepare the SQL statement for updating the account
        $stmt = $connection->prepare('
            UPDATE chart_of_account SET
                account_code = :account_code,
                account_type_id = :account_type_id,
                account_name = :account_name,
                account_description = :account_description,
                sub_account_id = :sub_account_id
            WHERE id = :id
        ');

        // Bind parameters
        $stmt->bindParam(':account_code', $account_code);
        $stmt->bindParam(':account_type_id', $account_type_id);
        $stmt->bindParam(':account_name', $account_name);
        $stmt->bindParam(':account_description', $account_description);
        $stmt->bindParam(':sub_account_id', $sub_account_id);
        $stmt->bindParam(':id', $this->id);

        // Execute the statement
        if (!$stmt->execute()) {
            throw new Exception('Failed to update account details');
        }
    }
}
