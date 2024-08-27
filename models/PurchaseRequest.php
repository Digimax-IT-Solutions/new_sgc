<?php

require_once __DIR__ . '/../_init.php';

class PurchaseRequest
{

    // Header
    public $id;
    public $pr_no;
    public $location;
    public $date;
    public $required_date;
    public $memo;
    public $status;

    // Details
    public $details = [];

    public function __construct($data)
    {
        $this->id = $data['id'] ?? null;
        $this->pr_no = $data['pr_no'] ?? null;
        $this->location = $data['location'] ?? null;
        $this->date = $data['date'] ?? null;
        $this->required_date = $data['required_date'] ?? null;
        $this->memo = $data['memo'] ?? null;
        $this->status = $data['status'] ?? null;

        $this->details = [];

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

            $stmt = $connection->prepare("INSERT INTO purchase_request (pr_no, location, date, required_date, memo)
            VALUES (:pr_no, :location, :date, :required_date, :memo)");

            $stmt->execute([
                ':pr_no' => $data['pr_no'],
                ':location' => $data['location'],
                ':date' => $data['date'],
                ':required_date' => $data['required_date'],
                ':memo' => $data['memo']
            ]);

            $pr_id = $connection->lastInsertId();

            // Handle item data
            if (isset($data['item_data']) && is_array($data['item_data'])) {
                foreach ($data['item_data'] as $item) {
                    self::addItem([
                        'pr_id' => $pr_id,
                        'item_id' => $item['item_id'],
                        'cost_center_id' => $item['cost_center_id'],
                        'quantity' => $item['quantity']
                    ]);
                }
            }

            $connection->commit();
            return $pr_id;
        } catch (PDOException $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    public static function addItem($data)
    {
        global $connection;

        $stmt = $connection->prepare("INSERT INTO purchase_request_details (pr_id, item_id, cost_center_id, quantity, balance_quantity)
        VALUES (:pr_id, :item_id, :cost_center_id, :quantity, :quantity)");

        $stmt->execute([
            ':pr_id' => $data['pr_id'],
            ':item_id' => $data['item_id'],
            ':cost_center_id' => $data['cost_center_id'],
            ':quantity' => $data['quantity']
        ]);
    }

    public static function getLastTransactionId()
    {
        global $connection;

        $stmt = $connection->query("SELECT MAX(id) AS last_transaction_id FROM purchase_request");
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();

        if ($result && isset($result['last_transaction_id'])) {
            return $result['last_transaction_id'];
        }

        return null;
    }

    public static function getLastPrNo()
    {
        global $connection;

        $stmt = $connection->prepare("
            SELECT pr_no 
            FROM purchase_request
            WHERE pr_no IS NOT NULL AND pr_no <> '' 
            ORDER BY pr_no DESC 
            LIMIT 1
        ");
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();
        if ($result) {
            $latestNo = $result['pr_no'];
            $numericPart = intval(substr($latestNo, 2));
            $newNo = $numericPart + 1;
        } else {
            $newNo = 1;
        }

        $newPrNo = 'PR' . str_pad($newNo, 9, '0', STR_PAD_LEFT);

        return $newPrNo;
    }

    public static function all()
    {
        global $connection;

        $stmt = $connection->query("SELECT * FROM purchase_request");
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetchAll();

        $purchaseRequests = [];
        foreach ($result as $row) {
            $purchaseRequests[] = new PurchaseRequest($row);
        }

        return $purchaseRequests;
    }

    public static function find($id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT * from purchase_request
            WHERE id = :id
        ');

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $data = $stmt->fetch();

        if (!$data) {
            return null;
        }

        $data['details'] = self::getRequestDetails($id);

        return new PurchaseRequest($data);
    }

    public static function getRequestDetails($request_id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT prd.*, cc.particular, um.name, i.item_purchase_description
            FROM purchase_request_details prd
            LEFT JOIN items i ON prd.item_id = i.id
            LEFT JOIN cost_center cc ON cc.id = prd.cost_center_id
            LEFT JOIN uom um ON i.item_uom_id = um.id
            WHERE pr_id = :request_id
        ');

        $stmt->bindParam(':request_id', $request_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $details = [];
        while ($row = $stmt->fetch()) {
            $details[] = $row; // Directly use the fetched row as it is already formatted correctly
        }
        return $details;
    }
}
?>
