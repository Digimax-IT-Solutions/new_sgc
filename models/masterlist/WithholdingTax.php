<?php

require_once __DIR__ . '/../../_init.php';

class WithholdingTax
{
    public $id;
    public $wtax_name;
    public $wtax_rate;
    public $wtax_description;
    public $wtax_account_id;
    public $wtax_account_description;
    private static $cache = null;

    public function __construct($wtax)
    {
        $this->id = $wtax['id'];
        $this->wtax_name = $wtax['wtax_name'];
        $this->wtax_rate = $wtax['wtax_rate'];
        $this->wtax_description = $wtax['wtax_description'];
        $this->wtax_account_id = $wtax['wtax_account_id'];
        $this->wtax_account_description = $wtax['account_description'] ?? null;
    }

    public function update()
    {
        global $connection;

        // Check if name is unique
        $wtax = self::findByName($this->wtax_name);
        if ($wtax && $wtax->id !== $this->id) {
            throw new Exception('Withholding tax already exists.');
        }

        $stmt = $connection->prepare('UPDATE wtax SET wtax_name=:wtax_name, wtax_rate=:wtax_rate, wtax_description=:wtax_description, wtax_account_id=:wtax_account_id WHERE id=:id');
        $stmt->bindParam('wtax_name', $this->wtax_name);
        $stmt->bindParam('wtax_rate', $this->wtax_rate);
        $stmt->bindParam('wtax_description', $this->wtax_description);
        $stmt->bindParam('wtax_account_id', $this->wtax_account_id);
        $stmt->bindParam('id', $this->id);
        $stmt->execute();
    }

    public function delete()
    {
        global $connection;

        $stmt = $connection->prepare('DELETE FROM `wtax` WHERE id=:id');
        $stmt->bindParam('id', $this->id);
        $stmt->execute();
    }

    public static function all()
    {
        global $connection;

        if (static::$cache) {
            return static::$cache;
        }

        $stmt = $connection->prepare('
            SELECT wtax.*, chart_of_account.account_description
            FROM wtax
            JOIN chart_of_account 
            ON wtax.wtax_account_id = chart_of_account.id
        ');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        static::$cache = array_map(function ($item) {
            return new WithholdingTax($item);
        }, $result);

        return static::$cache;
    }

    public static function add($wtax_name, $wtax_rate, $wtax_description, $wtax_account_id)
    {
        global $connection;

        if (static::findByName($wtax_name)) {
            throw new Exception('Withholding tax already exists');
        }

        $stmt = $connection->prepare('INSERT INTO `wtax` (wtax_name, wtax_rate, wtax_description, wtax_account_id) VALUES (:wtax_name, :wtax_rate, :wtax_description, :wtax_account_id)');
        $stmt->bindParam("wtax_name", $wtax_name);
        $stmt->bindParam("wtax_rate", $wtax_rate);
        $stmt->bindParam("wtax_description", $wtax_description);
        $stmt->bindParam("wtax_account_id", $wtax_account_id);
        $stmt->execute();
    }

    public static function findByName($wtax_name)
    {
        global $connection;

        $stmt = $connection->prepare("SELECT * FROM `wtax` WHERE wtax_name=:wtax_name");
        $stmt->bindParam("wtax_name", $wtax_name);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new WithholdingTax($result[0]);
        }

        return null;
    }

    public static function find($id)
    {
        global $connection;

        $stmt = $connection->prepare("SELECT * FROM `wtax` WHERE id=:id");
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new WithholdingTax($result[0]);
        }

        return null;
    }
}
