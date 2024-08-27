<?php

require_once __DIR__ . '/../../_init.php';

class Customer
{
    public $id;
    public $customer_name;
    public $customer_code;
    public $customer_contact;
    public $shipping_address;
    public $billing_address;
    public $business_style;
    public $customer_terms;
    public $customer_tin;
    public $customer_email;
    public $credit_balance;
    public $total_credit_memo;
    public $total_invoiced;
    public $total_paid;
    public $balance_due;
    private static $cache = null;

    public function __construct($customer)
    {
        $this->id = $customer['id'];
        $this->customer_name = $customer['customer_name'];
        $this->customer_code = $customer['customer_code'];
        $this->customer_contact = $customer['customer_contact'];
        $this->shipping_address = $customer['shipping_address'];
        $this->billing_address = $customer['billing_address'];
        $this->business_style = $customer['business_style'];
        $this->customer_terms = $customer['customer_terms'];
        $this->customer_tin = $customer['customer_tin'];
        $this->customer_email = $customer['customer_email'];
        $this->credit_balance = $customer['credit_balance'];
        $this->total_credit_memo = $customer['total_credit_memo'];
        $this->total_invoiced = $customer['total_invoiced'];
        $this->total_paid = $customer['total_paid'];
        $this->balance_due = $customer['balance_due'];
    }

    public function delete()
    {
        global $connection;

        $stmt = $connection->prepare('DELETE FROM `customers` WHERE id=:id');
        $stmt->bindParam(':id', $this->id);
        $stmt->execute();
    }

    public function update()
    {
        global $connection;

        $stmt = $connection->prepare('UPDATE `customers` SET 
            customer_name = :customer_name,
            customer_code = :customer_code,
            customer_contact = :customer_contact,
            shipping_address = :shipping_address,
            billing_address = :billing_address,
            business_style = :business_style,
            customer_terms = :customer_terms,
            customer_tin = :customer_tin,
            customer_email = :customer_email
            WHERE id = :id');

        $stmt->bindParam(':id', $this->id);
        $stmt->bindParam(':customer_name', $this->customer_name);
        $stmt->bindParam(':customer_code', $this->customer_code);
        $stmt->bindParam(':customer_contact', $this->customer_contact);
        $stmt->bindParam(':shipping_address', $this->shipping_address);
        $stmt->bindParam(':billing_address', $this->billing_address);
        $stmt->bindParam(':business_style', $this->business_style);
        $stmt->bindParam(':customer_terms', $this->customer_terms);
        $stmt->bindParam(':customer_tin', $this->customer_tin);
        $stmt->bindParam(':customer_email', $this->customer_email);

        $stmt->execute();
    }

    public static function all()
    {
        global $connection;

        if (static::$cache) {
            return static::$cache;
        }

        $stmt = $connection->prepare('SELECT * FROM `customers`');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        static::$cache = array_map(function ($item) {
            return new Customer($item);
        }, $result);

        return static::$cache;
    }

    public static function add($customer_name, $customer_code, $customer_contact, $shipping_address, $billing_address, $business_style, $customer_terms, $customer_tin, $customer_email)
    {
        global $connection;

        $stmt = $connection->prepare('INSERT INTO `customers` 
            (customer_name, customer_code, customer_contact, shipping_address, billing_address, business_style, customer_terms, customer_tin, customer_email)
            VALUES
            (:customer_name, :customer_code, :customer_contact, :shipping_address, :billing_address, :business_style, :customer_terms, :customer_tin, :customer_email)');

        $stmt->bindParam(':customer_name', $customer_name);
        $stmt->bindParam(':customer_code', $customer_code);
        $stmt->bindParam(':customer_contact', $customer_contact);
        $stmt->bindParam(':shipping_address', $shipping_address);
        $stmt->bindParam(':billing_address', $billing_address);
        $stmt->bindParam(':business_style', $business_style);
        $stmt->bindParam(':customer_terms', $customer_terms);
        $stmt->bindParam(':customer_tin', $customer_tin);
        $stmt->bindParam(':customer_email', $customer_email);

        $stmt->execute();
    }

    public static function findByName($name)
    {
        global $connection;

        $stmt = $connection->prepare('SELECT * FROM `customers` WHERE customer_name=:name');
        $stmt->bindParam(':name', $name);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetch();

        if ($result) {
            return new Customer($result);
        }

        return null;
    }

    public static function find($id)
    {
        global $connection;

        $stmt = $connection->prepare('SELECT * FROM `customers` WHERE id=:id');
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetch();

        if ($result) {
            return new Customer($result);
        }

        return null;
    }

    public static function total()
    {
        global $connection;

        $stmt = $connection->prepare('SELECT COUNT(*) as total FROM `customers`');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetch();

        if ($result && isset($result['total'])) {
            return $result['total'];
        }

        return 0;
    }
}

