<?php 

require_once __DIR__ . '/../_init.php';

class Apv
{
    public $id;
    public $account_id;
    public $account_name;
    public $apv_no;
    public $ref_no;
    public $po_no;
    public $rr_no;
    public $vendor_id;
    public $vendor_tin;
    public $vendor_name;
    public $apv_date;
    public $apv_due_date;
    public $terms_id;
    public $term_name;
    public $memo;
    public $gross_amount;   
    public $discount_amount;
    public $net_amount_due;
    public $vat_percentage_amount;
    public $net_of_vat;
    public $tax_withheld_amount;
    public $total_amount_due;
    public $status;
    public $print_status;
    public $details;
    
    public static $cache = null;

    public function __construct($formData) {
        $this->id = $formData['id'] ?? null;
        $this->account_id = $formData['account_id'] ?? null;
        $this->account_name = $formData['account_name'] ?? null;
        $this->apv_no = $formData['apv_no'] ?? null;
        $this->ref_no = $formData['ref_no'] ?? null;
        $this->po_no = $formData['po_no'] ?? null;
        $this->rr_no = $formData['rr_no'] ?? null;
        $this->vendor_id = $formData['vendor_id'] ?? null;
        $this->vendor_tin = $formData['vendor_tin'] ?? null;
        $this->vendor_name = $formData['vendor_name'] ?? null;
        $this->apv_date = $formData['apv_date'] ?? null;
        $this->apv_due_date = $formData['apv_due_date'] ?? null;
        $this->terms_id = $formData['terms_id'] ?? null;
        $this->term_name = $formData['term_name'] ?? null;
        $this->memo = $formData['memo'] ?? null;
        $this->gross_amount = $formData['gross_amount'] ?? null;
        $this->discount_amount = $formData['discount_amount'] ?? null;
        $this->net_amount_due = $formData['net_amount_due'] ?? null;
        $this->vat_percentage_amount = $formData['vat_percentage_amount'] ?? null;
        $this->net_of_vat = $formData['net_of_vat'] ?? null;
        $this->tax_withheld_amount = $formData['tax_withheld_amount'] ?? null;
        $this->total_amount_due = $formData['total_amount_due'] ?? null;
        $this->print_status = $formData['print_status'] ?? null;
        $this->status = $formData['status'] ?? null;
        $this->details = $formData['details'] ?? null;

        $this->details = [];

        if (isset($formData['details'])) {
            foreach ($formData['details'] as $detail) {
                // Push each detail to the details array
                $this->details[] = $detail;
            }
        }
    }

    // ADD APV
    public static function add($apv_no, $ref_no, $po_no, $rr_no, $apv_date, $apv_due_date, $terms_id, $account_id, $vendor_id, $vendor_name, $vendor_tin, $memo, $gross_amount, $discount_amount, $net_amount_due, $vat_percentage_amount, $net_of_vat, $tax_withheld_amount, $total_amount_due, $created_by, $items, $wtax_account_id)
    {
        global $connection;

        try {
            // Start a transaction
            $connection->beginTransaction();

            $transaction_type = "APVoucher";

            $stmt = $connection->prepare("INSERT INTO apv (
            apv_no,
            ref_no,
            po_no,
            rr_no,
            apv_date,
            apv_due_date,
            terms_id,
            account_id,
            vendor_id,
            vendor_tin,
            memo,
            gross_amount,
            discount_amount,
            net_amount_due,
            vat_percentage_amount,
            net_of_vat,
            tax_withheld_amount,
            wtax_account_id,
            total_amount_due,
            created_by) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $stmt->execute([
                $apv_no,
                $ref_no,
                $po_no,
                $rr_no,
                $apv_date,
                $apv_due_date,
                $terms_id,
                $account_id,
                $vendor_id,
                $vendor_tin,
                $memo,
                $gross_amount,
                $discount_amount,
                $net_amount_due,
                $vat_percentage_amount,
                $net_of_vat,
                $tax_withheld_amount,
                $total_amount_due,
                $wtax_account_id,
                $created_by
            ]);

            // Retrieve the ID of the newly inserted APV entry
            $apv_id = $connection->lastInsertId();

            $total_discount = 0;
            $total_input_vat = 0;

            self::logAuditTrail(
                $apv_id,
                $transaction_type,
                $apv_date,
                $apv_no,
                $vendor_name,
                $account_id,
                0.00,
                $total_amount_due,
                $created_by
            );

            foreach ($items as $item) {
                // Insert APV details
                self::addItem(
                    $apv_id,
                    $item['account_id'],
                    $item['cost_center_id'],
                    $item['po_no'],
                    $item['rr_no'],
                    $item['memo'],
                    $item['amount'],
                    $item['discount_percentage'],
                    $item['discount_amount'],
                    $item['net_amount_before_vat'],
                    $item['net_amount'],
                    $item['vat_percentage'],
                    $item['input_vat'],
                    $item['discount_account_id'],
                    $item['input_vat_account_id']
                );

                // Audit APV Accounts
                self::logAuditTrail(
                    $apv_id,
                    $transaction_type,
                    $apv_date,
                    $apv_no,
                    $vendor_name,
                    $item['account_id'],
                    $item['net_amount'] + $item['discount_amount'],
                    0.00,
                    $created_by
                );

                // Accumulate total discount and input VAT
                $total_discount += $item['discount_amount'];
                $total_input_vat += $item['input_vat'];
            }

            // Audit APV Discount Account (single entry for total discount)
            self::logAuditTrail(
                $apv_id,
                $transaction_type,
                $apv_date,
                $apv_no,
                $vendor_name,
                $items[0]['discount_account_id'],
                0.00,
                $total_discount,
                $created_by
            );

            // Audit APV Input VAT Account (single entry for total input VAT)
            self::logAuditTrail(
                $apv_id,
                $transaction_type,
                $apv_date,
                $apv_no,
                $vendor_name,
                $items[0]['input_vat_account_id'],
                $total_input_vat,
                0.00,
                $created_by
            );

            // Audit Trail Wtax Account
            self::logAuditTrail(
                $apv_id,
                $transaction_type,
                $apv_date,
                $apv_no,
                $vendor_name,
                $wtax_account_id,
                0.00,
                $tax_withheld_amount,
                $created_by
            );

            // Commit the transaction
            $connection->commit();
        } catch (PDOException $e) {
            $connection->rollback();
            throw $e;
        }
    }

    // ACCOUNTING LOGS
    private static function logAuditTrail($general_journal_id, $transaction_type, $transaction_date, $ref_no, $vendor_name, $account_id, $debit, $credit,  $created_by)
    {
        global $connection;

        $stmt = $connection->prepare("
                INSERT INTO audit_trail (
                    transaction_id,
                    transaction_type,
                    transaction_date,
                    ref_no,
                    name,
                    account_id,
                    debit,
                    credit,
                    created_by,
                    created_at
                ) VALUES (?,?,?,?,?,?,?,?,?, NOW())
            ");

        $stmt->execute([
            $general_journal_id,
            $transaction_type,
            $transaction_date,
            $ref_no,
            $vendor_name,
            $account_id,
            $debit,
            $credit,
            $created_by
        ]);
    }

    // Add ITEM Details APV
    public static function addItem($transaction_id, $account_id, $cost_center_id, $po_no, $rr_no, $memo, $amount, $discount_percentage, $discount_amount, $net_amount_before_vat, $net_amount, $vat_percentage, $input_vat, $discount_account_id,
    $input_vat_account_id)
    {
        global $connection;
        $stmt = $connection->prepare("INSERT INTO apv_details (
            apv_id, 
            account_id, 
            cost_center_id,
            po_no,
            rr_no,
            memo, 
            amount, 
            discount_percentage,
            discount_amount, 
            net_amount_before_vat,
            net_amount, 
            vat_percentage, 
            input_vat,
            discount_account_id,
            input_vat_account_id
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(
            [
                $transaction_id,
                $account_id,
                $cost_center_id,
                $po_no,
                $rr_no,
                $memo,
                $amount,
                $discount_percentage,
                $discount_amount,
                $net_amount_before_vat,
                $net_amount,
                $vat_percentage,
                $input_vat,
                $discount_account_id,
                $input_vat_account_id
            ]
        );
    }

    // GET ALL APV
    public static function all()
    {
        global $connection;

        $stmt = $connection->prepare('SELECT * FROM apv');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $apv = [];
        while ($row = $stmt->fetch()) {
            $apv[] = new apv($row);
        }

        return $apv;
    }
    // GET LAST APV_NO 
    public static function getLastApvNo() {
        global $connection;
        try {
            $stmt = $connection->prepare("
                SELECT apv_no 
                FROM apv 
                WHERE apv_no IS NOT NULL AND apv_no <> '' 
                ORDER BY apv_no DESC 
                LIMIT 1
            ");
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetch();
    
            // Extract the numeric part of the last AP voucher number
            if ($result) {
                $latestNo = $result['apv_no'];
                // Assuming the format is 'AP' followed by digits
                $numericPart = intval(substr($latestNo, 2)); // 'AP' is 2 characters
                $newNo = $numericPart + 1;
            } else {
                // If no valid AP voucher number exists, start with 1
                $newNo = 1;
            }
            // Format the new number with leading zeros
            $newAPVoucherNo = 'AP' . str_pad($newNo, 9, '0', STR_PAD_LEFT);
    
            return $newAPVoucherNo;
        } catch (PDOException $e) {
            // Handle potential exceptions
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }
    
    // GET LAST TRANSACTION ID
    public static function getLastTransactionId()
    {
        global $connection;

        $stmt = $connection->query("SELECT MAX(id) AS last_transaction_id FROM apv");
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();

        if ($result && isset($result['last_transaction_id'])) {
            return $result['last_transaction_id'];
        }

        return null;
    }

    public static function find($id)
    {
        global $connection;
        try {
            $stmt = $connection->prepare('
                SELECT 
                a.*,
                v.vendor_name,
                coa.account_description
                FROM apv a
                LEFT JOIN vendors v ON a.vendor_id = v.id
                LEFT JOIN chart_of_account coa ON a.account_id = coa.id
                WHERE a.id = :id
            ');

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            $stmt->execute();
            $apvData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$apvData) {
                return null;
            }

            $apvData['details'] = self::getApvDetails($id);
            return new Apv($apvData);
        } catch (PDOException $e) {
            error_log("Database error in find(): " . $e->getMessage());
            return null;
        }
    }

    public static function getApvDetails($apv_id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                ad.id,
                ad.apv_id,
                ad.account_id,
                ad.cost_center_id,
                ad.amount,
                ad.memo,
                ad.discount_percentage,
                ad.discount_amount,
                ad.net_amount_before_vat,
                ad.net_amount,
                ad.vat_percentage,
                ad.input_vat,
                coa.account_description
            FROM apv_details ad
            LEFT JOIN chart_of_account coa ON ad.account_id = coa.id
            LEFT JOIN cost_center cc ON ad.cost_center_id = cc.id
            WHERE ad.apv_id = :apv_id
        ');

        $stmt->bindParam(':apv_id', $apv_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $details = [];
        while ($row = $stmt->fetch()) {
            $details[] = [
                'id' => $row['id'],
                'apv_id' => $row['apv_id'],
                'account_id' => $row['account_id'],
                'cost_center_id' => $row['cost_center_id'],
                'memo' => $row['memo'],
                'amount' => $row['amount'],
                'discount_percentage' => $row['discount_percentage'],
                'discount_amount' => $row['discount_amount'],
                'net_amount_before_vat' => $row['net_amount_before_vat'],
                'net_amount' => $row['net_amount'],
                'vat_percentage' => $row['vat_percentage'],
                'input_vat' => $row['input_vat']
            ];
        }

        return $details;
    }

    public static function findTransactionEntries($apvId)
    {
        global $connection;
        try {
            $stmt = $connection->prepare('
            SELECT 
                te.*,
                coa.account_code,
                coa.account_description
            FROM transaction_entries te
            JOIN chart_of_account coa ON te.account_id = coa.id
            WHERE te.transaction_id = :apvId
            AND te.transaction_type = "APVoucher"
            ORDER BY te.id ASC
        ');

            $stmt->bindParam(':apvId', $apvId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in findTransactionEntries(): " . $e->getMessage());
            return [];
        }
    }

    public static function saveDraft(
        $ref_no,
        $po_no,
        $rr_no,
        $apv_date,
        $apv_due_date,
        $terms_id,
        $account_id,
        $vendor_id,
        $vendor_name,
        $vendor_tin,
        $memo,
        $gross_amount,
        $discount_amount,
        $net_amount_due,
        $vat_percentage_amount,
        $net_of_vat,
        $tax_withheld_amount,
        $total_amount_due,
        $created_by,
        $items,
        $wtax_account_id
    ) {
        global $connection;
    
        try {
            $connection->beginTransaction();
    
            // Insert into apv table
            $sql = "INSERT INTO apv (
                ref_no, rr_no, po_no, apv_date, apv_due_date, terms_id, account_id, vendor_id,  vendor_tin, memo,
                gross_amount, discount_amount, net_amount_due, vat_percentage_amount, net_of_vat,
                tax_withheld_amount, total_amount_due, created_by, status, wtax_account_id
            ) VALUES (
                :ref_no, :rr_no, :po_no, :apv_date, :apv_due_date, :terms_id, :account_id, :vendor_id, :vendor_tin, :memo,
                :gross_amount, :discount_amount, :net_amount_due, :vat_percentage_amount, :net_of_vat,
                :tax_withheld_amount, :total_amount_due, :created_by, :status, :wtax_account_id
            )";
    
            $stmt = $connection->prepare($sql);
            $stmt->bindParam(':ref_no', $ref_no);
            $stmt->bindParam(':rr_no', $rr_no);
            $stmt->bindParam(':po_no', $po_no);
            $stmt->bindParam(':apv_date', $apv_date);
            $stmt->bindParam(':apv_due_date', $apv_due_date);
            $stmt->bindParam(':terms_id', $terms_id);
            $stmt->bindParam(':account_id', $account_id);
            $stmt->bindParam(':vendor_id', $vendor_id);
            $stmt->bindParam(':vendor_tin', $vendor_tin);
            $stmt->bindParam(':memo', $memo);
            $stmt->bindParam(':gross_amount', $gross_amount);
            $stmt->bindParam(':discount_amount', $discount_amount);
            $stmt->bindParam(':net_amount_due', $net_amount_due);
            $stmt->bindParam(':vat_percentage_amount', $vat_percentage_amount);
            $stmt->bindParam(':net_of_vat', $net_of_vat);
            $stmt->bindParam(':tax_withheld_amount', $tax_withheld_amount);
            $stmt->bindParam(':total_amount_due', $total_amount_due);
            $stmt->bindParam(':created_by', $created_by);
            $stmt->bindValue(':status', 4, PDO::PARAM_INT); // Set status to 4 for draft
            $stmt->bindParam(':wtax_account_id', $wtax_account_id);

    
            $stmt->execute();
    
            // Retrieve the last inserted ID
            $apv_id = $connection->lastInsertId();
    
            // Insert draft items
            if (!empty($items)) {
                $itemSql = "INSERT INTO apv_details (
                    apv_id, account_id, cost_center_id, memo, amount, discount_percentage, discount_amount,
                    net_amount_before_vat, net_amount, vat_percentage, input_vat, discount_account_id, input_vat_account_id
                ) VALUES (
                    :apv_id, :account_id, :cost_center_id, :memo, :amount, :discount_percentage, :discount_amount,
                    :net_amount_before_vat, :net_amount, :vat_percentage, :input_vat, :discount_account_id, :input_vat_account_id	
                )";
    
                $itemStmt = $connection->prepare($itemSql);
    
                foreach ($items as $item) {
                    $itemStmt->bindParam(':apv_id', $apv_id);
                    $itemStmt->bindParam(':account_id', $item['account_id']);
                    $itemStmt->bindParam(':cost_center_id', $item['cost_center_id']);
                    $itemStmt->bindParam(':memo', $item['memo']);
                    $itemStmt->bindParam(':amount', $item['amount']);
                    $itemStmt->bindParam(':discount_percentage', $item['discount_percentage']);
                    $itemStmt->bindParam(':discount_amount', $item['discount_amount']);
                    $itemStmt->bindParam(':net_amount_before_vat', $item['net_amount_before_vat']);
                    $itemStmt->bindParam(':net_amount', $item['net_amount']);
                    $itemStmt->bindParam(':vat_percentage', $item['vat_percentage']);
                    $itemStmt->bindParam(':input_vat', $item['input_vat']);
                    $itemStmt->bindParam(':discount_account_id', $item['discount_account_id']);
                    $itemStmt->bindParam(':input_vat_account_id', $item['input_vat_account_id']);

                    $itemStmt->execute();
                }
            }
    
            // Commit the transaction
            $connection->commit();
            return true;
    
        } catch (Exception $ex) {
            $connection->rollBack();
            error_log('Error in saveDraft: ' . $ex->getMessage());
            throw $ex;
        }
    }

    
    public static function updateDraftDetails($apv_id)
    {
        global $connection;
    
        $stmt = $connection->prepare('
              SELECT 
                ad.id,
                ad.apv_id,
                ad.account_id,
                ad.cost_center_id,
                ad.amount,
                ad.memo,
                ad.discount_percentage,
                ad.discount_amount,
                ad.net_amount_before_vat,
                ad.net_amount,
                ad.vat_percentage,
                ad.input_vat,
                ad.discount_account_id AS apv_discount_account_id,
                ad.input_vat_account_id AS apv_input_vat_account_id,
                coa.account_description
            FROM apv_details ad
            INNER JOIN chart_of_account coa ON ad.account_id = coa.id
            INNER JOIN cost_center cc ON ad.cost_center_id = cc.id
            WHERE ad.apv_id = :apv_id
        ');
    
        $stmt->bindParam(':apv_id', $apv_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
    
        $result = $stmt->fetchAll();

        $apvDetails = [
            'details' => $result,
            'discount_account_id' => $result[0]['apv_discount_account_id'] ?? null,
            'input_vat_account_id' => $result[0]['apv_input_vat_account_id'] ?? null
        ];

        return $apvDetails;
    }
    
    public static function updateDraft(
        $apv_id, // The ID of the draft to update
        $apv_no,
        $ref_no,
        $po_no,
        $rr_no,
        $apv_date,
        $apv_due_date,
        $terms_id,
        $account_id,
        $vendor_id,
        $vendor_name,
        $vendor_tin,
        $memo,
        $gross_amount,
        $discount_amount,
        $net_amount_due,
        $vat_percentage_amount,
        $net_of_vat,
        $tax_withheld_amount,
        $total_amount_due,
        $created_by,
        $items,
        $wtax_account_id
    ) {
        global $connection;
    
        try {
            // Start a transaction
            $connection->beginTransaction();
    
            $transaction_type = "APVoucher";

            // Fetch existing credit memo details
            $apvDetails = self::updateDraftDetails($apv_id);
            $existingDetails = $apvDetails['details'];
            $discount_account_id = $apvDetails['discount_account_id'];
            $input_vat_account_id = $apvDetails['input_vat_account_id'];

            // Fetch the relevant data from the database
            $stmt = $connection->prepare("
            SELECT
                apv_no,
                apv_date,
                account_id,
                tax_withheld_amount,
                wtax_account_id,
                total_amount_due
            FROM apv
            WHERE id = ?
            ");
            $stmt->execute([$apv_id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
            $apv_no = $result['apv_no'];
            $apv_date = $result['apv_date'];
            $account_id = $result['account_id'];
            $tax_withheld_amount = $result['tax_withheld_amount'];
            $wtax_account_id = $result['wtax_account_id'];
            $total_amount_due = $result['total_amount_due'];
            } else {
            throw new Exception("APV not found.");
            }

    
            $total_discount = 0;
            $total_input_vat = 0;
    
            self::logAuditTrail(
                $apv_id,
                $transaction_type,
                $apv_date,
                $apv_no,
                $vendor_name,
                $account_id,
                0.00,
                $total_amount_due,
                $created_by
            );
    
            foreach ($existingDetails as $item) {
    
                // Audit updated APV Accounts
                self::logAuditTrail(
                    $apv_id,
                    $transaction_type,
                    $apv_date,
                    $apv_no,
                    $vendor_name,
                    $item['account_id'],
                    $item['net_amount'] + $item['discount_amount'],
                    0.00,
                    $created_by
                );
    
                // Accumulate total discount and input VAT
                $total_discount += $item['discount_amount'];
                $total_input_vat += $item['input_vat'];
            }
    
            // Audit updated APV Discount Account (single entry for total discount)
            self::logAuditTrail(
                $apv_id,
                $transaction_type,
                $apv_date,
                $apv_no,
                $vendor_name,
                $discount_account_id,
                0.00,
                $total_discount,
                $created_by
            );
    
            // Audit updated APV Input VAT Account (single entry for total input VAT)
            self::logAuditTrail(
                $apv_id,
                $transaction_type,
                $apv_date,
                $apv_no,
                $vendor_name,
                $input_vat_account_id,
                $total_input_vat,
                0.00,
                $created_by
            );
    
            // Audit Trail Wtax Account
            self::logAuditTrail(
                $apv_id,
                $transaction_type,
                $apv_date,
                $apv_no,
                $vendor_name,
                $wtax_account_id,
                0.00,
                $tax_withheld_amount,
                $created_by
            );
    
            // Commit the transaction
            $connection->commit();
            return true;
    
        } catch (PDOException $e) {
            $connection->rollback();
            throw $e;
        }
    }
    
    public static function void($id)
    {
        global $connection;
    
        try {
            $connection->beginTransaction();
            
            // Update the status to 3 (void) in the sales_invoice table
            $stmt = $connection->prepare("UPDATE apv SET status = 3 WHERE id = :id");
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
    
}
?>