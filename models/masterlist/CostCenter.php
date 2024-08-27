<?php

require_once __DIR__ . '/../../_init.php';

class CostCenter
{
    public $id;
    public $code;
    public $particular;

    private static $cache = null;

    public function __construct($cost_center)
    {
        $this->id = $cost_center['id'];
        $this->code = $cost_center['code'];
        $this->particular = $cost_center['particular'];
    }

    public function update()
    {
        global $connection;
        //Check if name is unique
        $cost_center = self::findByName($this->particular);
        if ($cost_center && $cost_center->id !== $this->id)
            throw new Exception('Cost center already exists.');

        $stmt = $connection->prepare('UPDATE cost_center SET particular=:particular, code=:code WHERE id=:id');
        $stmt->bindParam('particular', $this->particular);
        $stmt->bindParam('code', $this->code);
        $stmt->bindParam('id', $this->id);
        $stmt->execute();
    }

    public function delete()
    {
        global $connection;

        $stmt = $connection->prepare('DELETE FROM `cost_center` WHERE id=:id');
        $stmt->bindParam('id', $this->id);
        $stmt->execute();
    }

    public static function all()
    {
        global $connection;

        if (static::$cache)
            return static::$cache;

        $stmt = $connection->prepare('SELECT * FROM `cost_center`');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        static::$cache = array_map(function ($item) {
            return new CostCenter($item);
        }, $result);

        return static::$cache;
    }

    public static function add($code, $particular)
    {
        global $connection;

        if (static::findByName($particular))
            throw new Exception('Cost center exists');

        $stmt = $connection->prepare('INSERT INTO `cost_center`(code, particular) 
        VALUES (:code, :particular)');
        $stmt->bindParam("code", $code);
        $stmt->bindParam("particular", $particular);
        $stmt->execute();
    }

    public static function findByName($particular)
    {
        global $connection;

        $stmt = $connection->prepare("SELECT * FROM `cost_center` WHERE particular=:particular");
        $stmt->bindParam("particular", $particularparticular);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new CostCenter($result[0]);
        }

        return null;
    }

    public static function find($id)
    {
        global $connection;

        $stmt = $connection->prepare("SELECT * FROM `cost_center` WHERE id=:id");
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new CostCenter($result[0]);
        }

        return null;
    }
}