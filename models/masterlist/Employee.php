<?php

require_once __DIR__ . '/../../_init.php';

class Employee
{
    public $id;
    public $employee_code;
    public $employment_status;
    public $first_name;
    public $middle_name;
    public $last_name;
    public $ext_name;
    public $co_name;
    public $tin;
    public $terms;
    public $house_lot_number;
    public $street;
    public $barangay;
    public $town;
    public $city;
    public $zip;
    public $sss;
    public $philhealth;
    public $pagibig;
    private static $cache = null;

    public function __construct($employee)
    {
        $this->id = $employee['id'];
        $this->employee_code = $employee['employee_code'];
        $this->employment_status = $employee['employment_status'];
        $this->first_name = $employee['first_name'];
        $this->middle_name = $employee['middle_name'];
        $this->last_name = $employee['last_name'];
        $this->ext_name = $employee['ext_name'];
        $this->co_name = $employee['co_name'];
        $this->tin = $employee['tin'];
        $this->terms = $employee['terms'];
        $this->house_lot_number = $employee['house_lot_number'];
        $this->street = $employee['street'];
        $this->barangay = $employee['barangay'];
        $this->town = $employee['town'];
        $this->city = $employee['city'];
        $this->zip = $employee['zip'];
        $this->sss = $employee['sss'];
        $this->philhealth = $employee['philhealth'];
        $this->pagibig = $employee['pagibig'];
    }

    public function delete()
    {
        global $connection;

        $stmt = $connection->prepare('DELETE FROM `employee` WHERE id=:id');
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
    }

    public static function all()
    {
        global $connection;

        if (static::$cache) {
            return static::$cache;
        }

        $stmt = $connection->prepare('SELECT * FROM `employee`');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        static::$cache = array_map(function ($item) {
            return new Employee($item);
        }, $result);

        return static::$cache;
    }

    public static function add($employee_code, $employment_status, $first_name, $middle_name, $last_name, $ext_name, $co_name, $tin, $terms, $house_lot_number, $street, $barangay, $town, $city, $zip, $sss, $philhealth, $pagibig)
    {
        global $connection;

        if (static::findByName($first_name)) {
            throw new Exception('Employee name already exists');
        }

        $stmt = $connection->prepare('INSERT INTO `employee` (employee_code, employment_status, first_name, middle_name, last_name, ext_name, co_name, tin, terms, house_lot_number, street, barangay, town, city, zip, sss, philhealth, pagibig) 
        VALUES (:employee_code, :employment_status, :first_name, :middle_name, :last_name, :ext_name, :co_name, :tin, :terms, :house_lot_number, :street, :barangay, :town, :city, :zip, :sss, :philhealth, :pagibig)');

        $stmt->bindParam(':employee_code', $employee_code);
        $stmt->bindParam(':employment_status', $employment_status);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':middle_name', $middle_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':ext_name', $ext_name);
        $stmt->bindParam(':co_name', $co_name);
        $stmt->bindParam(':tin', $tin);
        $stmt->bindParam(':terms', $terms);
        $stmt->bindParam(':house_lot_number', $house_lot_number);
        $stmt->bindParam(':street', $street);
        $stmt->bindParam(':barangay', $barangay);
        $stmt->bindParam(':town', $town);
        $stmt->bindParam(':city', $city);
        $stmt->bindParam(':zip', $zip);
        $stmt->bindParam(':sss', $sss);
        $stmt->bindParam(':philhealth', $philhealth);
        $stmt->bindParam(':pagibig', $pagibig);
        
        $stmt->execute();
    }

    public static function findByName($last_name)
    {
        global $connection;

        $stmt = $connection->prepare("SELECT * FROM `employee` WHERE last_name=:last_name");
        $stmt->bindParam(":last_name", $last_name);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (!empty($result)) {
            return new Employee($result[0]);
        }

        return null;
    }

    public static function find($employee_code)
    {
        global $connection;

        $stmt = $connection->prepare("SELECT * FROM `employee` WHERE employee_code=:employee_code");
        $stmt->bindParam(":employee_code", $employee_code);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        if (count($result) >= 1) {
            return new Employee($result[0]);
        }

        return null;
    }
}
