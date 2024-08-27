<?php
require_once __DIR__ . '/../../_init.php';

class Term
{
    public $id;
    public $term_name;
    public $term_days_due;
    public $description;

    private static $cache = null;

    public function __construct($term)
    {
        $this->id = $term['id'];
        $this->term_name = $term['term_name'];
        $this->term_days_due = $term['term_days_due'];
        $this->description = $term['description'];
    }

    public function update()
    {
        global $connection;
        
        // Check if name is unique
        $existingTerm = self::findByName($this->term_name);
        if ($existingTerm && $existingTerm->id !== $this->id) {
            throw new Exception('Term name already exists.');
        }

        $stmt = $connection->prepare('UPDATE terms SET term_name=:term_name, term_days_due=:term_days_due, description=:description WHERE id=:id');
        $stmt->bindParam(':term_name', $this->term_name);
        $stmt->bindParam(':term_days_due', $this->term_days_due);
        $stmt->bindParam(':description', $this->description);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
    }

    public function delete()
    {
        global $connection;

        $stmt = $connection->prepare('DELETE FROM terms WHERE id=:id');
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
    }

    public static function all()
    {
        global $connection;

        if (static::$cache !== null) {
            return static::$cache;
        }

        $stmt = $connection->prepare('SELECT * FROM terms');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();
        static::$cache = array_map(function ($item) {
            return new Term($item);
        }, $result);

        return static::$cache;
    }

    public static function add($term_name, $term_days_due, $description)
    {
        global $connection;

        if (self::findByName($term_name)) {
            throw new Exception('Term name already exists.');
        }

        $stmt = $connection->prepare('INSERT INTO terms (term_name, term_days_due, description) VALUES (:term_name, :term_days_due, :description)');
        $stmt->bindParam(':term_name', $term_name);
        $stmt->bindParam(':term_days_due', $term_days_due);
        $stmt->bindParam(':description', $description);
        $stmt->execute();
    }

    public static function findByName($term_name)
    {
        global $connection;

        $stmt = $connection->prepare('SELECT * FROM terms WHERE term_name = :term_name');
        $stmt->bindParam(':term_name', $term_name);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new Term($result[0]);
        }

        return null;
    }

    public static function find($id)
    {
        global $connection;

        $stmt = $connection->prepare('SELECT * FROM terms WHERE id = :id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new Term($result[0]);
        }

        return null;
    }
}
?>
