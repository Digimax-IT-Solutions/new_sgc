<?php

require_once __DIR__ . '/../../_init.php';

class OtherNameList
{
    public $id;
    public $other_name;
    public $other_name_code;
    public $account_number;
    public $other_name_address;
    public $contact_number;
    public $email;
    public $terms;
    private static $cache = null;

    public function __construct($other_name)
    {
        $this->id = $other_name['id'];
        $this->other_name = $other_name['other_name'];
        $this->other_name_code = $other_name['other_name_code'];
        $this->account_number = $other_name['account_number'];
        $this->other_name_address = $other_name['other_name_address'];
        $this->contact_number = $other_name['contact_number'];
        $this->email = $other_name['email'];
        $this->terms = $other_name['terms'];
    }

    public function update()
    {
        global $connection;

        // Check if name is unique
        $existingOtherName = self::findByName($this->other_name);
        if ($existingOtherName && $existingOtherName->id !== $this->id) {
            throw new Exception('Other name already exists.');
        }

        // Prepare the SQL query
        $stmt = $connection->prepare('UPDATE `other_name` SET other_name=:other_name, other_name_code=:other_name_code, 
                                     account_number=:account_number, other_name_address=:other_name_address, 
                                     contact_number=:contact_number, email=:email, terms=:terms WHERE id=:id');

        // Bind parameters
        $stmt->bindParam(":other_name", $this->other_name);
        $stmt->bindParam(":other_name_code", $this->other_name_code);
        $stmt->bindParam(":account_number", $this->account_number);
        $stmt->bindParam(":other_name_address", $this->other_name_address);
        $stmt->bindParam(":contact_number", $this->contact_number);
        $stmt->bindParam(":email", $this->email);
        $stmt->bindParam(":terms", $this->terms);
        $stmt->bindParam(":id", $this->id);

        // Execute the query
        $stmt->execute();
    }

    public function delete()
    {
        global $connection;

        $stmt = $connection->prepare('DELETE FROM `other_name` WHERE id=:id');
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
    }

    public static function all()
    {
        global $connection;

        if (static::$cache) {
            return static::$cache;
        }

        $stmt = $connection->prepare('SELECT * FROM `other_name`');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        static::$cache = array_map(function ($item) {
            return new OtherNameList($item);
        }, $result);

        return static::$cache;
    }

    public static function add($other_name, $other_name_code, $account_number, $other_name_address, $contact_number, $email, $terms)
    {
        global $connection;

        // Check if the other name already exists
        if (static::findByName($other_name)) {
            throw new Exception('Other name already exists.');
        }

        // Prepare the SQL query
        $stmt = $connection->prepare('INSERT INTO `other_name` (other_name, other_name_code, account_number, other_name_address, contact_number, email, terms) 
                                     VALUES (:other_name, :other_name_code, :account_number, :other_name_address, :contact_number, :email, :terms)');

        // Bind parameters
        $stmt->bindParam(":other_name", $other_name);
        $stmt->bindParam(":other_name_code", $other_name_code);
        $stmt->bindParam(":account_number", $account_number);
        $stmt->bindParam(":other_name_address", $other_name_address);
        $stmt->bindParam(":contact_number", $contact_number);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":terms", $terms);

        // Execute the query
        $stmt->execute();
    }

    public static function findByName($other_name)
    {
        global $connection;

        $stmt = $connection->prepare("SELECT * FROM `other_name` WHERE other_name=:other_name");
        $stmt->bindParam(":other_name", $other_name);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (!empty($result)) {
            return new OtherNameList($result[0]);
        }

        return null;
    }

    public static function find($id)
    {
        global $connection;

        $stmt = $connection->prepare("SELECT * FROM `other_name` WHERE id=:id");
        $stmt->bindParam(":id", $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new OtherNameList($result[0]);
        }

        return null;
    }
}
?>
