<?php

require_once __DIR__ . '/../../_init.php';

class FsNotesClassification
{
    public $id;
    public $name;

    private static $cache = null;

    public function __construct($data)
    {
        $this->id = $data['id'];
        $this->name = $data['name'];
    }

    public function update()
    {
        global $connection;
        // Check if name is unique
        $data = self::findByName($this->name);
        if ($data && $data->id !== $this->id) {
            throw new Exception('Fs Classification already exists.');
        }

        $stmt = $connection->prepare('UPDATE fs_notes_classification SET name=:name WHERE id=:id');
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
    }

    public function delete()
    {
        global $connection;

        $stmt = $connection->prepare('DELETE FROM fs_notes_classification WHERE id=:id');
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
    }

    public static function all()
    {
        global $connection;

        if (static::$cache) {
            return static::$cache;
        }

        $stmt = $connection->prepare('SELECT * FROM fs_notes_classification');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        static::$cache = array_map(function ($item) {
            return new FsNotesClassification($item);
        }, $result);

        return static::$cache;
    }

    public static function add($name)
    {
        global $connection;

        if (static::findByName($name)) {
            throw new Exception('Fs Classification already exists');
        }

        $stmt = $connection->prepare('INSERT INTO fs_notes_classification (name) VALUES (:name)');
        $stmt->bindParam(':name', $name);
        $stmt->execute();
    }

    public static function findByName($name)
    {
        global $connection;

        $stmt = $connection->prepare('SELECT * FROM fs_notes_classification WHERE name=:name');
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new FsNotesClassification($result[0]);
        }

        return null;
    }

    public static function find($id)
    {
        global $connection;

        $stmt = $connection->prepare('SELECT * FROM fs_notes_classification WHERE id=:id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new FsNotesClassification($result[0]);
        }

        return null;
    }
}
