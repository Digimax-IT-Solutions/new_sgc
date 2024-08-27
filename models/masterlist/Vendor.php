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

        // Check if the vendor name already exists
        if (static::findByName($vendor_name)) {
            throw new Exception('Vendor name already exists');
        }

        // Prepare the SQL query
        $stmt = $connection->prepare('INSERT INTO `vendors` (vendor_name, vendor_code, account_number, vendor_address, contact_number, email, terms, tin, tax_type, tel_no, fax_no, notes, item_type) VALUES (:vendor_name, :vendor_code, :account_number, :vendor_address, :contact_number, :email, :terms, :tin, :tax_type, :tel_no, :fax_no, :notes, :item_type)');

        // Bind parameters
        $stmt->bindParam(":vendor_name", $vendor_name);
        $stmt->bindParam(":vendor_code", $vendor_code);
        $stmt->bindParam(":account_number", $account_number);
        $stmt->bindParam(":vendor_address", $vendor_address);
        $stmt->bindParam(":contact_number", $contact_number);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":terms", $terms);
        $stmt->bindParam(":tin", $tin);
        $stmt->bindParam(":tax_type", $tax_type);
        $stmt->bindParam(":tel_no", $tel_no);
        $stmt->bindParam(":fax_no", $fax_no);
        $stmt->bindParam(":notes", $notes);
        $stmt->bindParam(":item_type", $item_type);

        // Execute the query
        $stmt->execute();
    }

    public static function findByName($name)
    {
        global $connection;

        $stmt = $connection->prepare("SELECT * FROM `vendors` WHERE vendor_name=:name");
        $stmt->bindParam("name", $name);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (!empty($result)) {
            return new Vendor($result[0]);
        }

        return null;
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
}
