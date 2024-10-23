<?php

require_once __DIR__ . '/../../_init.php';

class ChartOfAccount
{
    public $id;

    public $account_type;
    public $account_type_id;
    public $account_code;
    public $gl_code;
    public $gl_name;
    public $sl_code;
    public $sl_name;
    public $account_description;
    public $fs_classification;
    public $fs_notes_classification;
    public $fs_classification_name;
    public $fs_notes_classification_name;

    private static $cache = null;

    public function __construct($data)
    {
        $this->id = $data['account_id'];

        $this->account_type = $data['account_type'];
        $this->account_type_id = $data['account_type_id'] ?? null;
        $this->account_code = $data['account_code'];
        $this->gl_code = $data['gl_code'];
        $this->gl_name = $data['gl_name'];
        $this->sl_code = $data['sl_code'];
        $this->sl_name = $data['sl_name'];
        $this->account_description = $data['account_description'];
        $this->fs_classification = $data['fs_classification'];
        $this->fs_notes_classification = $data['fs_notes_classification'];
        $this->fs_classification_name = $data['fs_classification_name'];
        $this->fs_notes_classification_name = $data['fs_notes_classification_name'];

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
            coa.gl_code,
            coa.gl_name,
            coa.sl_code,
            coa.sl_name,
            coa.account_code,
            coa.account_description,
            coa.fs_classification,
            coa.fs_notes_classification,
            fc.name AS fs_classification_name,
            fnc.name AS fs_notes_classification_name
        FROM
            chart_of_account coa
        LEFT JOIN
            account_types at ON coa.account_type_id = at.id
        LEFT JOIN
            fs_classification fc ON coa.fs_classification = fc.id
        LEFT JOIN
            fs_notes_classification fnc ON coa.fs_notes_classification = fnc.id
        ');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        static::$cache = array_map(function ($item) {
            return new ChartOfAccount($item);
        }, $result);

        return static::$cache;
    }


    // public function update() 
    // {
    //     global $connection;
    //     //Check if name is unique
    //     $category = self::findByName($this->name);
    //     if ($category && $category->id !== $this->id) throw new Exception('Name already exists.');

    //     $stmt = $connection->prepare('UPDATE categories SET name=:name WHERE id=:id');
    //     $stmt->bindParam('name', $this->name);
    //     $stmt->bindParam('id', $this->id);
    //     $stmt->execute();
    // }

    public function delete()
    {
        global $connection;

        $stmt = $connection->prepare('DELETE FROM `chart_of_account` WHERE id=:id');
        $stmt->bindParam('id', $this->id);
        $stmt->execute();
    }



    public static function add($account_code, $account_type_id, $gl_code, $gl_name, $sl_code, $sl_name, $account_description, $fs_classification, $fs_notes_classification)
    {
        global $connection;

        if (static::findByAccountCode($account_code)) {
            throw new Exception('Account with this code already exists');
        }

        $stmt = $connection->prepare('INSERT INTO `chart_of_account` 
                                      (account_code, account_type_id, gl_code, gl_name, sl_code, sl_name,  account_description, fs_classification, fs_notes_classification) 
                                      VALUES (:account_code, :account_type_id, :gl_code, :gl_name, :sl_code, :sl_name, :account_description, :fs_classification, :fs_notes_classification)');
        $stmt->bindParam("account_code", $account_code);
        $stmt->bindParam("account_type_id", $account_type_id);
        $stmt->bindParam("gl_code", $gl_code);
        $stmt->bindParam("gl_name", $gl_name);
        $stmt->bindParam("sl_code", $sl_code);
        $stmt->bindParam("sl_name", $sl_name);
        $stmt->bindParam("account_description", $account_description);
        $stmt->bindParam("fs_classification", $fs_classification);
        $stmt->bindParam("fs_notes_classification", $fs_notes_classification);
        $stmt->execute();
    }


    public function update($account_code, $account_type_id, $gl_code, $gl_name, $sl_code, $sl_name, $account_description, $fs_classification, $fs_notes_classification)
    {
        global $connection;

        try {
            $stmt = $connection->prepare('
            UPDATE `chart_of_account` SET
                account_code = :account_code,
                account_type_id = :account_type_id,
                gl_code = :gl_code,
                gl_name = :gl_name,
                sl_code = :sl_code,
                sl_name = :sl_name,
                account_description = :account_description,
                fs_classification = :fs_classification,
                fs_notes_classification = :fs_notes_classification
            WHERE
                id = :id
        ');

            // Bind parameters
            $stmt->bindParam(":account_code", $account_code);
            $stmt->bindParam(":account_type_id", $account_type_id);
            $stmt->bindParam(":gl_code", $gl_code);
            $stmt->bindParam(":gl_name", $gl_name);
            $stmt->bindParam(":sl_code", $sl_code);
            $stmt->bindParam(":sl_name", $sl_name);
            $stmt->bindParam(":account_description", $account_description);
            $stmt->bindParam(":fs_classification", $fs_classification);
            $stmt->bindParam(":fs_notes_classification", $fs_notes_classification);
            $stmt->bindParam(":id", $this->id);

            // Execute the update query
            $stmt->execute();

            // Update the instance properties
            $this->account_code = $account_code;
            $this->account_type_id = $account_type_id;
            $this->gl_code = $gl_code;
            $this->gl_name = $gl_name;
            $this->sl_code = $sl_code;
            $this->sl_name = $sl_name;
            $this->account_description = $account_description;

        } catch (PDOException $ex) {
            // You may log or handle the exception as needed
            throw new Exception('Error updating account: ' . $ex->getMessage());
        }
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

    public static function findById($id)
    {
        if (empty($id)) {
            return null;
        }

        global $connection;

        $stmt = $connection->prepare('
       SELECT
            coa.id AS account_id,
            at.name AS account_type,
            coa.gl_code,
            coa.gl_name,
            coa.sl_code,
            coa.sl_name,
            coa.account_code,
            coa.account_description,
            coa.fs_classification,
            coa.fs_notes_classification,
            fc.name AS fs_classification_name,
            fnc.name AS fs_notes_classification_name
        FROM
            chart_of_account coa
        LEFT JOIN
            account_types at ON coa.account_type_id = at.id
        LEFT JOIN
            fs_classification fc ON coa.fs_classification = fc.id
        LEFT JOIN
            fs_notes_classification fnc ON coa.fs_notes_classification = fnc.id
        
                WHERE
                    coa.id = :id
            ');

        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetch();  // Fetch a single row

        if ($result) {
            return new ChartOfAccount($result);
        }

        return null;
    }

}