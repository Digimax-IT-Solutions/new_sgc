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
    public $print_status;
    public $cost_center;

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
        $this->print_status = $data['print_status'] ?? null;
        $this->cost_center = $data['cost_center'] ?? null;

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
            SELECT pr.*, prd.cost_center_id, cc.particular as cost_center 
            FROM purchase_request pr    
            JOIN purchase_request_details prd ON pr.id = prd.pr_id
            JOIN cost_center cc ON prd.cost_center_id = cc.id
            WHERE pr.id = :id
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
            SELECT prd.*, cc.particular, um.name, i.item_name, i.item_code, i.item_purchase_description
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

    public static function void($id)
    {
        global $connection;
    
        try {
            $connection->beginTransaction();
            
            // Update the status to 3 (void) in the sales_invoice table
            $stmt = $connection->prepare("UPDATE purchase_request SET status = 3 WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();
            
            if ($result) {
                // Update the state to 2 in the audit_trail table
                $auditStmt = $connection->prepare("UPDATE audit_trail SET state = 2 WHERE transaction_id = :id");
                $auditStmt->bindParam(':id', $id, PDO::PARAM_INT);
                $auditResult = $auditStmt->execute();
                
                if ($auditResult) {
                    // Delete from transaction_entries
                    $deleteStmt = $connection->prepare("DELETE FROM transaction_entries WHERE transaction_id = :id");
                    $deleteStmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $deleteResult = $deleteStmt->execute();
                    
                    if ($deleteResult) {
                        $connection->commit();
                        return true;
                    } else {
                        throw new Exception("Failed to delete transaction entries.");
                    }
                } else {
                    throw new Exception("Failed to update audit trail.");
                }
            } else {
                throw new Exception("Failed to void invoice.");
            }
        } catch (Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    public static function saveDraft(
        $location, $date, $required_date, $memo, $items
    ) {
        global $connection;
    
        try {
            $connection->beginTransaction();
    
            // Insert into credit_memo table with draft status
            $sql = "INSERT INTO purchase_request (
                location, date, required_date, memo, status
            ) VALUES (
                :location, :date, :required_date, :memo, :status
            )";
            
            $stmt = $connection->prepare($sql);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':required_date', $required_date);
            $stmt->bindParam(':memo', $memo);
            $stmt->bindValue(':status', 4, PDO::PARAM_INT); // Set status to 4 for draft
    
            $stmt->execute();
    
            $pr_id = $connection->lastInsertId();
    
            // Ensure $items is an array
            if (is_array($items) && !empty($items)) {
                $itemSql = "INSERT INTO purchase_request_details (
                    pr_id, item_id, cost_center_id, quantity, balance_quantity
                ) VALUES (
                    :pr_id, :item_id, :cost_center_id, :quantity, :quantity
                )";

    
                $itemStmt = $connection->prepare($itemSql);
    
                foreach ($items as $item) {
                    $itemStmt->bindParam(':pr_id', $pr_id);
                    $itemStmt->bindParam(':item_id', $item['item_id']);
                    $itemStmt->bindParam(':cost_center_id', $item['cost_center_id']);
                    $itemStmt->bindParam(':quantity', $item['quantity']);
                    $itemStmt->bindParam(':balance_quantity', $item['balance_quantity']);
                    $itemStmt->execute();
                }
            }
    
            $connection->commit();
            return true;
        } catch (Exception $e) {
            $connection->rollBack();
            throw $e;
        }
    }

    public static function updateDraft(
        $id, $location, $date, $required_date, $memo, $items
    ) {
        global $connection;
    
        try {
            $connection->beginTransaction();
    
            // Update the main purchase_request record
            $stmt = $connection->prepare("
                UPDATE purchase_request 
                SET location = :location,
                    date = :date,
                    required_date = :required_date,
                    memo = :memo,
                    status = 4
                WHERE id = :id
            ");
    
            // Bind the parameters
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':location', $location, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':required_date', $required_date, PDO::PARAM_STR);
            $stmt->bindParam(':memo', $memo, PDO::PARAM_STR);
    
            // Execute the statement
            $result = $stmt->execute();
    
            if ($result) {
                // Delete existing purchase request details
                $stmt = $connection->prepare("DELETE FROM purchase_request_details WHERE pr_id = ?");
                $stmt->execute([$id]);
    
                // Prepare statement for inserting new purchase request details
                $stmt = $connection->prepare("
                    INSERT INTO purchase_request_details (
                        pr_id, item_id, cost_center_id, quantity
                    ) VALUES (
                        :pr_id, :item_id, :cost_center_id, :quantity
                    )
                ");
    
                // Insert new purchase request details
                foreach ($items as $item) {
                    $stmt->execute([
                        ':pr_id' => $id,
                        ':item_id' => $item['item_id'],
                        ':cost_center_id' => $item['cost_center_id'],
                        ':quantity' => $item['quantity']
                    ]);
                }
            }
    
            $connection->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $connection->rollBack();
            throw new Exception("Database error: " . $e->getMessage());
        }
    }

    public static function saveFinal(
        $id, $pr_no, $location, $date, $required_date, $memo, $items
    ) {
        global $connection;
    
        try {
            $connection->beginTransaction();
    
            // Update the main purchase_request record
            $stmt = $connection->prepare("
                UPDATE purchase_request 
                SET pr_no = :pr_no,
                    location = :location,
                    date = :date,
                    required_date = :required_date,
                    memo = :memo,
                    status = 0
                WHERE id = :id
            ");
    
            // Bind the parameters
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':pr_no', $pr_no, PDO::PARAM_STR);
            $stmt->bindParam(':location', $location, PDO::PARAM_STR);
            $stmt->bindParam(':date', $date, PDO::PARAM_STR);
            $stmt->bindParam(':required_date', $required_date, PDO::PARAM_STR);
            $stmt->bindParam(':memo', $memo, PDO::PARAM_STR);
    
            // Execute the statement
            $result = $stmt->execute();
    
            if ($result) {
                // Delete existing purchase request details
                $stmt = $connection->prepare("DELETE FROM purchase_request_details WHERE pr_id = ?");
                $stmt->execute([$id]);
    
                // Prepare statement for inserting new purchase request details
                $stmt = $connection->prepare("
                    INSERT INTO purchase_request_details (
                        pr_id, item_id, cost_center_id, quantity
                    ) VALUES (
                        :pr_id, :item_id, :cost_center_id, :quantity
                    )
                ");
    
                // Insert new purchase request details
                foreach ($items as $item) {
                    $stmt->execute([
                        ':pr_id' => $id,
                        ':item_id' => $item['item_id'],
                        ':cost_center_id' => $item['cost_center_id'],
                        ':quantity' => $item['quantity']
                    ]);
                }
            }
    
            $connection->commit();
            return ['success' => true];
        } catch (Exception $e) {
            $connection->rollBack();
            throw new Exception("Database error: " . $e->getMessage());
        }
    }
    


    
}
?>
