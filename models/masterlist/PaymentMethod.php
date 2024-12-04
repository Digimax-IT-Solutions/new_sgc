<?php

require_once __DIR__ . '/../../_init.php';

class PaymentMethod
{
    public $id;
    public $payment_method_name;
    public $description;
    private static $cache = null;

    public function __construct($payment_method)
    {
        $this->id = $payment_method['id'];
        $this->payment_method_name = $payment_method['payment_method_name'];
        $this->description = $payment_method['description'];
    }

    public function update()
    {
        global $connection;
        // Check if name is unique
        $payment_method = self::findByName($this->payment_method_name);
        if ($payment_method && $payment_method->id !== $this->id)
            throw new Exception('Payment method already exists.');

        $stmt = $connection->prepare('UPDATE payment_method SET payment_method_name=:payment_method_name, description=:description WHERE id=:id');
        $stmt->bindParam('payment_method_name', $this->payment_method_name);
        $stmt->bindParam('description', $this->description);
        $stmt->bindParam('id', $this->id);
        $stmt->execute();
    }

    public function delete()
    {
        global $connection;

        $stmt = $connection->prepare('DELETE FROM `payment_method` WHERE id=:id');
        $stmt->bindParam('id', $this->id);
        $stmt->execute();
    }

    public static function all()
    {
        global $connection;

        if (static::$cache)
            return static::$cache;

        $stmt = $connection->prepare('SELECT * FROM `payment_method`');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        static::$cache = array_map(function ($item) {
            return new PaymentMethod($item);
        }, $result);

        return static::$cache;
    }

    public static function add($payment_method_name, $description = '')
    {
        global $connection;
    
        if (static::findByName($payment_method_name)) {
            throw new Exception('Payment method already exists');
        }
    
        $stmt = $connection->prepare('INSERT INTO `payment_method`(payment_method_name, description) VALUES (:payment_method_name, :description)');
        $stmt->bindParam(":payment_method_name", $payment_method_name);
        $stmt->bindParam(":description", $description);
        $stmt->execute();
    
        return $connection->lastInsertId(); // Return the last inserted ID
    }
    
    public static function findByName($payment_method_name)
    {
        global $connection;
    
        $stmt = $connection->prepare("SELECT * FROM `payment_method` WHERE payment_method_name = :payment_method_name");
        $stmt->bindParam(":payment_method_name", $payment_method_name);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($result) {
            return new PaymentMethod($result);
        }
    
        return null;
    }
    
    public static function find($id)
    {
        global $connection;
    
        $stmt = $connection->prepare("SELECT * FROM `payment_method` WHERE id = :id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($result) {
            return new PaymentMethod($result);
        }
    
        return null;
    }
}
