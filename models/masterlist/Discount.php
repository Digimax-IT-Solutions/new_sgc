<?php

require_once __DIR__ . '/../../_init.php';

class Discount
{
    public $id;
    public $discount_name;
    public $discount_rate;
    public $discount_description;
    public $discount_account_id;
    public $account_description;

    private static $cache = null;

    public function __construct($discount)
    {
        $this->id = $discount['id'];
        $this->discount_name = $discount['discount_name'];
        $this->discount_rate = $discount['discount_rate'];
        $this->discount_description = $discount['discount_description'];
        $this->discount_account_id = $discount['discount_account_id'];
        $this->account_description = $discount['account_description'] ?? null;
 
    }

    public function update()
    {
        global $connection;

        // Check if the name is unique
        $existingDiscount = self::findByName($this->discount_name);
        if ($existingDiscount && $existingDiscount->id !== $this->id) {
            throw new Exception('Discount with this name already exists.');
        }

        $stmt = $connection->prepare('UPDATE discount SET discount_name=:discount_name, discount_rate=:discount_rate, discount_description=:discount_description, discount_account_id=:discount_account_id WHERE id=:id');
        $stmt->bindParam('discount_name', $this->discount_name);
        $stmt->bindParam('discount_rate', $this->discount_rate);
        $stmt->bindParam('discount_description', $this->discount_description);
        $stmt->bindParam('discount_account_id', $this->discount_account_id);
        $stmt->bindParam('id', $this->id);
        $stmt->execute();
    }

    public function delete()
    {
        global $connection;

        $stmt = $connection->prepare('DELETE FROM discount WHERE id=:id');
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
            SELECT discount.*, chart_of_account.account_description, chart_of_account.account_description
            FROM discount
            LEFT JOIN chart_of_account
            ON discount.discount_account_id = chart_of_account.id
        ');
        $stmt->execute();
        $discounts = $stmt->fetchAll();
        $result = [];
        foreach ($discounts as $discount) {
            $result[] = new self($discount);
        }

        static::$cache = $result;
        return $result;
    }

    public static function find($id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT *
            FROM discount
            WHERE id=:id
        ');
        $stmt->bindParam('id', $id);
        $stmt->execute();

        $discount = $stmt->fetch();
        return $discount ? new self($discount) : null;
    }

    public static function add($discount_name, $discount_rate, $discount_description, $discount_account_id)
    {
        global $connection;

        // Check if the name is unique
        if (self::findByName($discount_name)) {
            throw new Exception('Discount with this name already exists.');
        }

        $stmt = $connection->prepare('INSERT INTO discount (discount_name, discount_rate, discount_description, discount_account_id) VALUES (:discount_name, :discount_rate, :discount_description, :discount_account_id)');
        $stmt->bindParam('discount_name', $discount_name);
        $stmt->bindParam('discount_rate', $discount_rate);
        $stmt->bindParam('discount_description', $discount_description);
        $stmt->bindParam('discount_account_id', $discount_account_id);
        $stmt->execute();
    }

    public static function findByName($discount_name)
    {
        global $connection;

        $stmt = $connection->prepare('SELECT * FROM discount WHERE discount_name=:discount_name');
        $stmt->bindParam('discount_name', $discount_name);
        $stmt->execute();

        $discount = $stmt->fetch();
        return $discount ? new self($discount) : null;
    }
}
