<?php

require_once __DIR__ . '/../_init.php';

Class MaterialIssuance {

    //header
    public $id;
    public $mis_no;
    public $location;
    public $purpose;
    public $date;
    public $status;
    public $print_status;

    // Details
    public $details = [];

    public function __construct($data)
    {
        $this->id = $data['id'] ?? null;
        $this->mis_no = $data['mis_no'] ?? null;
        $this->location = $data['location'] ?? null;
        $this->purpose = $data['purpose'] ?? null;
        $this->date = $data['date'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->print_status = $data['print_status'] ?? null;

        if (isset($data['details'])) {
            foreach ($data['details'] as $detail) {
                $this->details[] = $detail;
            }
        }
    }

    public static function add($data)
    {
        global $connection;

        try {
            $connection->beginTransaction();

            $stmt = $connection->prepare("INSERT INTO material_issuance (mis_no, location, purpose, date)
            VALUES (:mis_no, :location, :purpose, :date)");

            $stmt->execute([
                ':mis_no' => $data['mis_no'],
                ':location' => $data['location'],
                ':purpose' => $data['purpose'],
                ':date' => $data['date']
            ]);

            $mis_id = $connection->lastInsertId();

            if (isset($data['item_data']) && is_array($data['item_data'])) {
                foreach ($data['item_data'] as $item) {
                    self::addItem([
                        'mis_id' => $mis_id,
                        'item_id' => $item['item_id'],
                        'quantity' => $item['quantity'],
                        'cost' => $item['cost'],
                        'amount' => $item['amount']
                    ]);
                }
            }

            $connection->commit();
            return $mis_id;
        } catch (Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    public static function addItem($data)
    {
        global $connection;

        $stmt = $connection->prepare("INSERT INTO material_issuance_details (mis_id, item_id, quantity, cost, amount)
        VALUES (:mis_id, :item_id, :quantity, :cost, :amount)");

        $stmt->execute([
            ':mis_id' => $data['mis_id'],
            ':item_id' => $data['item_id'],
            ':quantity' => $data['quantity'],
            ':cost' => $data['cost'],
            ':amount' => $data['amount']
        ]);
    }

    public static function getLastTransactionId()
    {
        global $connection;

        $stmt = $connection->query("SELECT MAX(id) AS last_transaction_id FROM material_issuance");
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();

        if ($result && isset($result['last_transaction_id'])) {
            return $result['last_transaction_id'];
        }

        return null;
    }

    public static function getLastMisNo()
    {
        global $connection;

        try {
            $stmt = $connection->prepare("
                SELECT mis_no 
                FROM material_issuance 
                WHERE mis_no IS NOT NULL AND mis_no <> '' 
                ORDER BY mis_no DESC 
                LIMIT 1
            ");
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetch();

            if ($result) {
                $latestNo = $result['mis_no'];
                $numericPart = intval(substr($latestNo, 3));  // Corrected index to '3' to properly extract numeric part
                $newNo = $numericPart + 1;
            } else {
                $newNo = 1;
            }

            $newMisNo = 'MIS' . str_pad($newNo, 9, '0', STR_PAD_LEFT);

            return $newMisNo;
        } catch (PDOException $e) {
            // Handle the exception (log it, rethrow it, or return a default value)
            return 'Error: ' . $e->getMessage();
        }
    }

    public static function all()
    {
        global $connection;

        $stmt = $connection->query("SELECT * FROM material_issuance");
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();

        $material_issuances = [];

        if ($result) {
            foreach ($result as $material_issuance) {
                $material_issuances[] = new MaterialIssuance($material_issuance);
            }
        }

        return $material_issuances;
    }

    public static function find($id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT * from material_issuance
            WHERE id = :id
        ');

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        $data['details'] = self::getIssuanceDetails($id);

        return new MaterialIssuance($data);
    }

    public static function getIssuanceDetails($mis_id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT mid.*, um.name, i.item_name, i.item_code, i.item_purchase_description
            FROM material_issuance_details mid
            LEFT JOIN items i ON mid.item_id = i.id
            LEFT JOIN uom um ON i.item_uom_id = um.id
            WHERE mis_id = :mis_id
        ');

        $stmt->bindParam(':mis_id', $mis_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $result = $stmt->fetchAll();

        $details = [];
        foreach ($result as $row) {
            $details[] = $row;
        }

        return $details;
    }
}