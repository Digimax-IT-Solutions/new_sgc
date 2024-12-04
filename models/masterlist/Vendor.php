<?php

require_once __DIR__ . '/../../_init.php';

class Vendor
{
    public $id;
    public $vendor_name;
    public $vendor_code;
    public $account_number;
    public $vendor_address;
    public $contact_number;
    public $email;
    public $terms;
    public $tin;
    public $tax_type;
    public $tel_no;
    public $fax_no;
    public $notes;
    public $item_type;
    private static $cache = null;

    public function __construct($vendor)
    {
        $this->id = $vendor['id'];
        $this->vendor_name = $vendor['vendor_name'];
        $this->vendor_code = $vendor['vendor_code'];
        $this->account_number = $vendor['account_number'];
        $this->vendor_address = $vendor['vendor_address'];
        $this->contact_number = $vendor['contact_number'];
        $this->email = $vendor['email'];
        $this->terms = $vendor['terms'];
        $this->tin = $vendor['tin'];
        $this->tax_type = $vendor['tax_type'];
        $this->tel_no = $vendor['tel_no'];
        $this->fax_no = $vendor['fax_no'];
        $this->notes = $vendor['notes'];
        $this->item_type = $vendor['item_type'];
    }

    public function update()
    {
        global $connection;

        $stmt = $connection->prepare('UPDATE `vendors` SET vendor_name=:vendor_name, vendor_code=:vendor_code, account_number=:account_number, vendor_address=:vendor_address, contact_number=:contact_number, email=:email, terms=:terms, tin=:tin, tax_type=:tax_type, tel_no=:tel_no, fax_no=:fax_no, notes=:notes, item_type=:item_type WHERE id=:id');

        $stmt->bindParam(':vendor_name', $this->vendor_name);
        $stmt->bindParam(':vendor_code', $this->vendor_code);
        $stmt->bindParam(':account_number', $this->account_number);
        $stmt->bindParam(':vendor_address', $this->vendor_address);
        $stmt->bindParam(':contact_number', $this->contact_number);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':terms', $this->terms);
        $stmt->bindParam(":tin", $this->tin);
        $stmt->bindParam(":tax_type", $this->tax_type);
        $stmt->bindParam(":tel_no", $this->tel_no);
        $stmt->bindParam(":fax_no", $this->fax_no);
        $stmt->bindParam(":notes", $this->notes);
        $stmt->bindParam(":item_type", $this->item_type);
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
    }

    public function delete()
    {
        global $connection;

        $stmt = $connection->prepare('DELETE FROM `vendors` WHERE id=:id');
        $stmt->bindParam('id', $this->id);
        $stmt->execute();
    }

    public static function all()
    {
        global $connection;

        if (static::$cache) {
            return static::$cache;
        }

        $stmt = $connection->prepare('SELECT * FROM `vendors`');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        static::$cache = array_map(function ($item) {
            return new Vendor($item);
        }, $result);

        return static::$cache;
    }
    
    public static function add($vendor_name, $vendor_code, $account_number, $vendor_address, $contact_number, $email, $terms, $tin, $tax_type, $tel_no, $fax_no, $notes, $item_type)
    {
        global $connection;
    
        // More robust validation
        if (empty(trim($vendor_name))) {
            throw new Exception('Vendor name cannot be empty.');
        }
    
        // Check for existing vendor using case-insensitive comparison
        $existingVendor = self::findByName($vendor_name);
        if ($existingVendor) {
            throw new Exception('Vendor with this name already exists.');
        }
    
        try {
            $connection->beginTransaction(); // Start a transaction
    
            $stmt = $connection->prepare('
                INSERT INTO `vendors` (
                    vendor_name, vendor_code, account_number, vendor_address, 
                    contact_number, email, terms, tin, tax_type, tel_no, 
                    fax_no, notes, item_type
                ) VALUES (
                    :vendor_name, :vendor_code, :account_number, :vendor_address, 
                    :contact_number, :email, :terms, :tin, :tax_type, :tel_no, 
                    :fax_no, :notes, :item_type
                )
            ');
    
            // Use null coalescing and trimming for optional fields
            $stmt->bindValue(":vendor_name", trim($vendor_name), PDO::PARAM_STR);
            $stmt->bindValue(":vendor_code", trim($vendor_code) ?: null, PDO::PARAM_STR);
            $stmt->bindValue(":account_number", trim($account_number) ?: null, PDO::PARAM_STR);
            $stmt->bindValue(":vendor_address", trim($vendor_address) ?: null, PDO::PARAM_STR);
            $stmt->bindValue(":contact_number", trim($contact_number) ?: null, PDO::PARAM_STR);
            $stmt->bindValue(":email", trim($email) ?: null, PDO::PARAM_STR);
            $stmt->bindValue(":terms", trim($terms) ?: null, PDO::PARAM_STR);
            $stmt->bindValue(":tin", trim($tin) ?: null, PDO::PARAM_STR);
            $stmt->bindValue(":tax_type", trim($tax_type) ?: null, PDO::PARAM_STR);
            $stmt->bindValue(":tel_no", trim($tel_no) ?: null, PDO::PARAM_STR);
            $stmt->bindValue(":fax_no", trim($fax_no) ?: null, PDO::PARAM_STR);
            $stmt->bindValue(":notes", trim($notes) ?: null, PDO::PARAM_STR);
            $stmt->bindValue(":item_type", trim($item_type) ?: null, PDO::PARAM_STR);
    
            $stmt->execute();
            $lastInsertId = $connection->lastInsertId();
    
            $connection->commit(); // Commit the transaction
    
            return $lastInsertId;
        } catch (PDOException $e) {
            $connection->rollBack(); // Rollback in case of error
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }
    
    public static function findByName($name)
    {
        global $connection;
    
        $stmt = $connection->prepare("SELECT * FROM `vendors` WHERE LOWER(vendor_name) = LOWER(:name)");
        $stmt->bindValue(':name', trim($name), PDO::PARAM_STR);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result ? new Vendor($result) : null;
    }

    public static function find($id)
    {
        global $connection;

        $stmt = $connection->prepare("SELECT * FROM `vendors` WHERE id=:id");
        $stmt->bindParam("id", $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new Vendor($result[0]);
        }

        return null;
    }

    public static function getLastId()
    {
        global $connection;

        $stmt = $connection->query("SELECT MAX(id) AS last_transaction_id FROM vendors");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['last_transaction_id'] ?? null;
    }

    public static function findByCodeOrName($vendor_name)
    {
        global $connection;

        $stmt = $connection->prepare("
                SELECT id, vendor_name 
                FROM vendors 
                WHERE LOWER(vendor_name) = LOWER(:vendor_name)
                LIMIT 1
            ");


        $stmt->bindParam(':vendor_name', $vendor_name, PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null; // Returns the customer record or null if not found
    }
}
