<?php

require_once __DIR__ . '/../../_init.php';

class Uom
{
    public $id;
    public $name;

    private static $cache = null;

    public function __construct($uom)
    {
        $this->id = $uom['id'];
        $this->name = $uom['name'];
    }

    public function update()
    {
        global $connection;
        // Check if name is unique
        $uom = self::findByName($this->name);
        if ($uom && $uom->id !== $this->id) {
            throw new Exception('Uom already exists.');
        }

        $stmt = $connection->prepare('UPDATE uom SET name=:name WHERE id=:id');
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
    }

    public function delete()
    {
        global $connection;

        $stmt = $connection->prepare('DELETE FROM uom WHERE id=:id');
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
    }

    public static function all()
    {
        global $connection;

        if (static::$cache) {
            return static::$cache;
        }

        $stmt = $connection->prepare('SELECT * FROM uom');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        static::$cache = array_map(function ($item) {
            return new Uom($item);
        }, $result);

        return static::$cache;
    }

    public static function add($name)
    {
        global $connection;

        if (static::findByName($name)) {
            throw new Exception('Uom already exists');
        }

        $stmt = $connection->prepare('INSERT INTO uom (name) VALUES (:name)');
        $stmt->bindParam(':name', $name);
        $stmt->execute();
    }

    public static function findByName($name)
    {
        global $connection;

        $stmt = $connection->prepare('SELECT * FROM uom WHERE name=:name');
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new Uom($result[0]);
        }

        return null;
    }

    public static function find($id)
    {
        global $connection;

        $stmt = $connection->prepare('SELECT * FROM uom WHERE id=:id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new Uom($result[0]);
        }

        return null;
    }
}
