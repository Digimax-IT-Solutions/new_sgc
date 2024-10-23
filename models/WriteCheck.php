<?php

require_once __DIR__ . '/../_init.php';


class WriteCheck
{
    public $id;
    public $cv_no;
    public $ref_no;
    public $check_date;
    public $check_no;
    public $payee_type;
    public $payee_id;
    public $payee_name;
    public $payee_address;
    public $account_name;
    public $account_id;
    public $account_description;
    public $memo;
    public $location;
    public $gross_amount;
    public $discount_amount;
    public $net_amount_due;
    public $vat_percentage_amount;
    public $net_of_vat;
    public $tax_withheld_amount;
    public $tax_withheld_percentage;
    public $tax_withheld_account_id;

    public $total_amount_due;
    public $print_status;
    public $status;

    public $details;

    public function __construct($data)
    {
        $this->id = $data['id'] ?? null;
        $this->cv_no = $data['cv_no'] ?? null;
        $this->ref_no = $data['ref_no'] ?? null;
        $this->check_date = $data['check_date'] ?? null;
        $this->check_no = $data['check_no'] ?? null;
        $this->payee_type = $data['payee_type'] ?? null;
        $this->payee_id = $data['payee_id'] ?? null;
        $this->payee_name = $data['payee_name'] ?? null;
        $this->payee_address = $data['payee_address'] ?? null;
        $this->account_id = $data['account_id'] ?? null;
        $this->account_name = $data['account_name'] ?? null;
        $this->account_id = $data['account_id'] ?? null;
        $this->account_description = $data['account_description'] ?? null;
        $this->memo = $data['memo'] ?? null;
        $this->location = $data['location'] ?? null;
        $this->gross_amount = $data['gross_amount'] ?? null;
        $this->discount_amount = $data['discount_amount'] ?? null;
        $this->net_amount_due = $data['net_amount_due'] ?? null;
        $this->vat_percentage_amount = $data['vat_percentage_amount'] ?? null;
        $this->net_of_vat = $data['net_of_vat'] ?? null;
        $this->tax_withheld_amount = $data['tax_withheld_amount'] ?? null;
        $this->tax_withheld_percentage = $data['tax_withheld_percentage'] ?? null;
        $this->tax_withheld_account_id = $data['tax_withheld_account_id'] ?? null;
        $this->total_amount_due = $data['total_amount_due'] ?? null;
        $this->print_status = $data['print_status'] ?? null;
        $this->status = $data['status'] ?? null;



        // Initialize details as an empty array
        $this->details = [];

        // Optionally, you can populate details if provided in $formData
        if (isset($data['details'])) {
            foreach ($data['details'] as $detail) {
                // Push each detail to the details array
                $this->details[] = $detail;
            }
        }
    }
    // add/insert wchecks data 
    public static function add($cv_no, $check_no, $ref_no, $check_date, $bank_account_id, $payee_type, $payee_id, $payee_name, $memo, $location, $gross_amount, $discount_amount, $net_amount_due, $vat_percentage_amount, $net_of_vat, $tax_withheld_amount, $total_amount_due, $created_by, $items, $wtax_account_id)
    {
        global $connection;
    
        try {
            // Start a transaction
            $connection->beginTransaction();
    
            // Get the next transaction ID
            $nextId = self::getNextTransactionId();
            $transaction_type = "Check Expense";
    
            $stmt = $connection->prepare("INSERT INTO wchecks (
                id, cv_no, check_no, ref_no, check_date, account_id, payee_type, payee_id,
                memo, location, gross_amount, discount_amount, net_amount_due, vat_percentage_amount,
                net_of_vat, tax_withheld_amount, total_amount_due, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
            $stmt->execute([
                $nextId,
                $cv_no,
                $check_no,
                $ref_no,
                $check_date,
                $bank_account_id,
                $payee_type,
                $payee_id,
                $memo,
                $location,
                $gross_amount,
                $discount_amount,
                $net_amount_due,
                $vat_percentage_amount,
                $net_of_vat,
                $tax_withheld_amount,
                $total_amount_due,
                $created_by
            ]);
    
            // Retrieve the ID of the newly inserted write check entry
            $wcheck_id = $connection->lastInsertId();
    
            $total_discount = 0;
            $total_input_vat = 0;
    
            // Ensure $items is an array before proceeding
            if (!is_array($items)) {
                throw new Exception('Items must be an array');
            }
    
            foreach ($items as $item) {
                // Ensure that the amount is a number and remove any commas
                $amount = isset($item['amount']) ? preg_replace('/[^\d.]/', '', $item['amount']) : 0;
    
                // Ensure numeric values are cast to floats before calculations
                $item['discount_amount'] = (float)$item['discount_amount'];
                $item['net_amount'] = (float)$item['net_amount'];
                $item['input_vat'] = (float)$item['input_vat'];
    
                // Insert check details
                self::addItem(
                    $wcheck_id,
                    $item['account_id'],
                    $item['cost_center_id'],
                    $item['memo'],
                    $amount, // Cleaned numeric amount
                    $item['discount_percentage'],
                    $item['discount_amount'],
                    $item['net_amount_before_vat'],
                    $item['net_amount'],
                    $item['vat_percentage'],
                    $item['input_vat']
                );
    
                // Audit Check Accounts Account
                self::logAuditTrail(
                    $wcheck_id,
                    $transaction_type,
                    $check_date,
                    $cv_no, // previous ref_no
                    $location,
                    $payee_name,
                    $item['account_id'],
                    $item['net_amount'] + $item['discount_amount'], // Added discount_amount here
                    0.00,
                    $created_by
                );
    
                // Accumulate total discount and input VAT
                $total_discount += $item['discount_amount'];
                $total_input_vat += $item['input_vat'];
            }
    
            // Ensure we have at least one item before accessing array index 0
            if (!empty($items)) {
                // Audit Check Discount Account (single entry for total discount)
                self::logAuditTrail(
                    $wcheck_id,
                    $transaction_type,
                    $check_date,
                    $cv_no, // previous ref_no
                    $location,
                    $payee_name,
                    $items[0]['discount_account_id'], // Assuming all items have the same discount account
                    0.00,
                    $total_discount,
                    $created_by
                );
    
                // Audit Check Input VAT Account (single entry for total input VAT)
                self::logAuditTrail(
                    $wcheck_id,
                    $transaction_type,
                    $check_date,
                    $cv_no, // previous ref_no
                    $location,
                    $payee_name,
                    $items[0]['input_vat_account_id'], // Assuming all items have the same input VAT account
                    $total_input_vat,
                    0.00,
                    $created_by
                );
            }
    
            // Audit Trail Wtax Account
            self::logAuditTrail(
                $wcheck_id,
                $transaction_type,
                $check_date,
                $cv_no, // previous ref_no
                $location,
                $payee_name,
                $wtax_account_id,
                0.00,
                $tax_withheld_amount,
                $created_by
            );
    
            // Audit Trail Bank Account
            self::logAuditTrail(
                $wcheck_id,
                $transaction_type,
                $check_date,
                $cv_no, // previous ref_no
                $location,
                $payee_name,
                $bank_account_id,
                0.00,
                $total_amount_due,
                $created_by
            );
    
            // Commit the transaction
            $connection->commit();
        } catch (Exception $e) {
            $connection->rollback();
            throw $e;
        }
    }
    // ACCOUNTING LOGS
    private static function logAuditTrail($general_journal_id, $transaction_type, $transaction_date, $ref_no, $location, $customer_name, $account_id, $debit, $credit, $created_by)
    {
        global $connection;

        $stmt = $connection->prepare("
                INSERT INTO audit_trail (
                    transaction_id,
                    transaction_type,
                    transaction_date,
                    ref_no,
                    location,
                    name,
                    account_id,
                    debit,
                    credit,
                    created_by,
                    created_at
                ) VALUES (?,?,?,?,?,?,?,?,?,?, NOW())
            ");

        $stmt->execute([
            $general_journal_id,
            $transaction_type,
            $transaction_date,
            $ref_no,
            $location,
            $customer_name,
            $account_id,
            $debit,
            $credit,
            $created_by
        ]);
    }

    // add/insert data to wchecks_detail
    public static function addItem($transaction_id, $account_id, $cost_center_id, $memo, $amount, $discount_percentage, $discount_amount, $net_amount_before_vat, $net_amount, $vat_percentage, $input_vat)
    {
        global $connection;
        $stmt = $connection->prepare("INSERT INTO wchecks_details (
            wcheck_id, 
            account_id, 
            cost_center_id,
            memo, 
            amount, 
            discount_percentage,
            discount_amount, 
            net_amount_before_vat,
            net_amount, 
            vat_percentage, 
            input_vat) VALUES (?, ?,?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(
            [
                $transaction_id,
                $account_id,
                $cost_center_id,
                $memo,
                $amount,
                $discount_percentage,
                $discount_amount,
                $net_amount_before_vat,
                $net_amount,
                $vat_percentage,
                $input_vat
            ]
        );
    }
    // select all columns in wchecks
    public static function all()
    {
        global $connection;

        $stmt = $connection->prepare('SELECT * FROM wchecks');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $write_check = [];
        while ($row = $stmt->fetch()) {
            $write_check[] = new WriteCheck($row);
        }

        return $write_check;
    }
    public static function find($id)
    {
        global $connection;
        try {
            $stmt = $connection->prepare('
                SELECT 
                    wc.*,
                    COALESCE(vendors.vendor_name, customers.customer_name, other_name.other_name) AS payee_name,
                    CASE 
                        WHEN wc.payee_type = :vendor_type THEN vendors.vendor_address
                        WHEN wc.payee_type = :customer_type THEN customers.shipping_address
                        WHEN wc.payee_type = :other_name_type THEN other_name.other_name_address
                    END AS payee_address
                FROM wchecks wc
                LEFT JOIN vendors ON wc.payee_type = :vendor_type AND wc.payee_id = vendors.id
                LEFT JOIN customers ON wc.payee_type = :customer_type AND wc.payee_id = customers.id
                LEFT JOIN other_name ON wc.payee_type = :other_name_type AND wc.payee_id = other_name.id
                WHERE wc.id = :id
            ');

            $vendorType = 'vendors';
            $customerType = 'customers';
            $otherNameType = 'other_name';

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindParam(':vendor_type', $vendorType, PDO::PARAM_STR);
            $stmt->bindParam(':customer_type', $customerType, PDO::PARAM_STR);
            $stmt->bindParam(':other_name_type', $otherNameType, PDO::PARAM_STR);

            $stmt->execute();
            $writeData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$writeData) {
                return null;
            }

            $writeData['details'] = self::getWriteDetails($id);

            return new WriteCheck($writeData);
        } catch (PDOException $e) {
            error_log("Database error in find(): " . $e->getMessage());
            return null;
        }
    }

    public static function getWriteDetails($wcheck_id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                wcd.id,
                wcd.wcheck_id,
                wcd.account_id,
                wcd.cost_center_id,
                wcd.amount,
                wcd.discount_percentage,
                wcd.discount_amount,
                wcd.net_amount_before_vat,
                wcd.net_amount,
                wcd.vat_percentage,
                wcd.input_vat,
                coa.account_description
            FROM wchecks_details wcd
            LEFT JOIN chart_of_account coa ON wcd.account_id = coa.id
            LEFT JOIN cost_center cc ON wcd.cost_center_id = cc.id
            WHERE wcd.wcheck_id = :wcheck_id
        ');

        $stmt->bindParam(':wcheck_id', $wcheck_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $details = [];
        while ($row = $stmt->fetch()) {
            $details[] = [
                'id' => $row['id'],
                'wcheck_id' => $row['wcheck_id'],
                'account_id' => $row['account_id'],
                'cost_center_id' => $row['cost_center_id'],
                'account_description' => $row['account_description'],
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

    // get wchecks_detail data columns
    public static function findByEntryNo($entry_no)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                gj.id,
                gj.entry_no,
                gj.journal_date,
                gj.total_debit,
                gj.total_credit
            FROM general_journal gj
            WHERE gj.entry_no = :entry_no
        ');

        $stmt->bindParam(':entry_no', $entry_no);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $writeData = $stmt->fetch();

        if (!$writeData) {
            return null;
        }

        $writeData['details'] = self::getWriteDetails($writeData['id']);

        return new WriteCheck($writeData);
    }
    // get last transaction id from wchecks 
    public static function getNextTransactionId()
    {
        global $connection;

        $stmt = $connection->query("SELECT MAX(id) AS last_transaction_id FROM wchecks");
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();

        if ($result && isset($result['last_transaction_id'])) {
            return $result['last_transaction_id'] + 1;
        }

        return 1; // Start from 1 if no records found
    }

    public static function getLastTransactionId()
    {
        global $connection;

        $stmt = $connection->query("SELECT MAX(id) AS last_transaction_id FROM wchecks");
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();

        if ($result && isset($result['last_transaction_id'])) {
            return $result['last_transaction_id'];
        }

        return null;
    }

    public static function findTransactionEntries($checkId)
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
            WHERE te.transaction_id = :checkId
            AND te.transaction_type = "Check Expense"
            ORDER BY te.id ASC
        ');

            $stmt->bindParam(':checkId', $checkId, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in findTransactionEntries(): " . $e->getMessage());
            return [];
        }
    }

    public static function update(
        $id,
        $check_no,
        $ref_no,
        $check_date,
        $bank_account_id,
        $payee_type,
        $payee_id,
        $memo,
        $gross_amount,
        $discount_amount,
        $net_amount_due,
        $vat_percentage_amount,
        $net_of_vat,
        $tax_withheld_amount,
        $total_amount_due
    ) {
        // Get database connection
        global $connection;

        // Start transaction
        $connection->beginTransaction();

        try {
            // Prepare and execute the update statement
            $stmt = $connection->prepare("
                UPDATE wchecks
                SET check_no = :check_no,
                    ref_no = :ref_no,
                    check_date = :check_date,
                    account_id = :account_id,  -- Corrected parameter name
                    payee_type = :payee_type,
                    payee_id = :payee_id,
                    memo = :memo,
                    gross_amount = :gross_amount,
                    discount_amount = :discount_amount,
                    net_amount_due = :net_amount_due,
                    vat_percentage_amount = :vat_percentage_amount,
                    net_of_vat = :net_of_vat,
                    tax_withheld_amount = :tax_withheld_amount,
                    total_amount_due = :total_amount_due
                WHERE id = :id
            ");
            $stmt->execute([
                ':id' => $id,
                ':check_no' => $check_no,
                ':ref_no' => $ref_no,
                ':check_date' => $check_date,
                ':account_id' => $bank_account_id, // Corrected parameter value
                ':payee_type' => $payee_type,
                ':payee_id' => $payee_id,
                ':memo' => $memo,
                ':gross_amount' => $gross_amount,
                ':discount_amount' => $discount_amount,
                ':net_amount_due' => $net_amount_due,
                ':vat_percentage_amount' => $vat_percentage_amount,
                ':net_of_vat' => $net_of_vat,
                ':tax_withheld_amount' => $tax_withheld_amount,
                ':total_amount_due' => $total_amount_due,
            ]);

            // Commit transaction
            $connection->commit();
        } catch (Exception $e) {
            // Rollback transaction if something failed
            $connection->rollBack();
            error_log("Update failed: " . $e->getMessage()); // Log error message
            throw $e;
        }
    }


    public static function updateDetails($wcheck_id, $item_data)
    {
        // Get database connection
        global $connection;

        // Start transaction
        $connection->beginTransaction();

        try {
            // Delete existing details for the given wcheck_id
            $stmt = $connection->prepare("DELETE FROM wchecks_details WHERE wcheck_id = :wcheck_id");
            $stmt->execute([':wcheck_id' => $wcheck_id]);

            // Insert new details into wchecks_details
            foreach ($item_data as $item) {
                $stmt = $connection->prepare("
                    INSERT INTO wchecks_details (wcheck_id, account_id, cost_center_id, memo, amount, discount_percentage, discount_amount, net_amount_before_vat, net_amount, vat_percentage, input_vat)
                    VALUES (:wcheck_id, :account_id, :cost_center_id, :memo, :amount, :discount_percentage, :discount_amount, :net_amount_before_vat, :net_amount, :vat_percentage, :input_vat)
                ");
                $stmt->execute([
                    ':wcheck_id' => $wcheck_id,
                    ':account_id' => $item['account_id'],
                    ':cost_center_id' => $item['cost_center_id'],
                    ':memo' => $item['memo'],
                    ':amount' => $item['amount'],
                    ':discount_percentage' => $item['discount_percentage'],
                    ':discount_amount' => $item['discount_amount'],
                    ':net_amount_before_vat' => $item['net_amount_before_vat'],
                    ':net_amount' => $item['net_amount'],
                    ':vat_percentage' => $item['vat_percentage'],
                    ':input_vat' => $item['input_vat'],
                ]);
            }

            // Commit transaction
            $connection->commit();
        } catch (Exception $e) {
            // Rollback transaction if something failed
            $connection->rollBack();
            throw $e;
        }
    }

    public static function getLastCheckNo()
    {
        global $connection;

        try {
            // Prepare and execute the query to get the highest check number, ignoring null or empty values
            $stmt = $connection->prepare("
                SELECT cv_no 
                FROM wchecks 
                WHERE cv_no IS NOT NULL AND cv_no <> '' 
                ORDER BY cv_no DESC 
                LIMIT 1
            ");
            $stmt->execute();
            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $result = $stmt->fetch();

            if ($result) {
                $latestNo = $result['cv_no'];
                // Assuming the format is 'WC' followed by digits
                $numericPart = intval(substr($latestNo, 2)); // 'WC' is 2 characters
                $newNo = $numericPart + 1;
            } else {
                // If no valid check number exists, start with 1
                $newNo = 1;
            }

            // Format the new number with leading zeros
            $newWriteCvNo = 'CV' . str_pad($newNo, 9, '0', STR_PAD_LEFT);

            return $newWriteCvNo;
        } catch (PDOException $e) {
            // Handle potential exceptions
            error_log('Database error: ' . $e->getMessage());
            return null;
        }
    }


    public static function voidCheck($id)
    {
        global $connection;

        try {
            $stmt = $connection->prepare("UPDATE wchecks SET status = 3 WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $result = $stmt->execute();

            if (!$result) {
                throw new Exception("Failed to void check.");
            }

            return true;
        } catch (Exception $e) {
            error_log("Error in voidCheck(): " . $e->getMessage());
            throw $e;
        }
    }


    public static function saveDraft(
        $check_no,
        $check_date,
        $bank_account_id,
        $ref_no,
        $payee_type,
        $payee_id,
        $memo,
        $location,
        $gross_amount,
        $discount_amount,
        $net_amount_due,
        $vat_percentage_amount,
        $net_of_vat,
        $tax_withheld_amount,
        $tax_withheld_percentage,
        $total_amount_due,
        $discount_account_id,
        $tax_withheld_account_id,
        $input_vat_account_id,
        $created_by,
        $items
    ) {
        global $connection;
        try {
            $connection->beginTransaction();

            // Corrected SQL query
            $sql = "INSERT INTO wchecks (
                check_no, check_date, account_id, ref_no, payee_type, payee_id, memo, location,
                gross_amount, discount_amount, net_amount_due, vat_percentage_amount, net_of_vat, 
                tax_withheld_amount, tax_withheld_percentage, total_amount_due, discount_account_id, input_vat_account_id, 
                tax_withheld_account_id, status, created_by
            ) VALUES (
                :check_no, :check_date, :account_id, :ref_no, :payee_type, :payee_id, :memo, :location,
                :gross_amount, :discount_amount, :net_amount_due, :vat_percentage_amount, :net_of_vat, 
                :tax_withheld_amount, :tax_withheld_percentage, :total_amount_due, :discount_account_id, :input_vat_account_id, 
                :tax_withheld_account_id, :status, :created_by
            )";

            $stmt = $connection->prepare($sql);

            // Bind parameters
            $stmt->bindParam(':check_no', $check_no);
            $stmt->bindParam(':check_date', $check_date);
            $stmt->bindParam(':account_id', $bank_account_id);
            $stmt->bindParam(':ref_no', $ref_no);
            $stmt->bindParam(':payee_type', $payee_type);
            $stmt->bindParam(':payee_id', $payee_id);
            $stmt->bindParam(':memo', $memo);
            $stmt->bindParam(':location', $location);
            $stmt->bindParam(':gross_amount', $gross_amount);
            $stmt->bindParam(':discount_amount', $discount_amount);
            $stmt->bindParam(':net_amount_due', $net_amount_due);
            $stmt->bindParam(':vat_percentage_amount', $vat_percentage_amount);
            $stmt->bindParam(':net_of_vat', $net_of_vat);
            $stmt->bindParam(':tax_withheld_amount', $tax_withheld_amount);
            $stmt->bindParam(':tax_withheld_percentage', $tax_withheld_percentage);
            $stmt->bindParam(':total_amount_due', $total_amount_due);
            $stmt->bindParam(':discount_account_id', $discount_account_id);

            // Handle array parameters
            $stmt->bindValue(':input_vat_account_id', is_array($input_vat_account_id) ? json_encode($input_vat_account_id) : $input_vat_account_id, PDO::PARAM_STR);
            $stmt->bindValue(':tax_withheld_account_id', is_array($tax_withheld_account_id) ? json_encode($tax_withheld_account_id) : $tax_withheld_account_id, PDO::PARAM_STR);

            $stmt->bindValue(':status', 4, PDO::PARAM_INT); // Set status to 4 for draft
            $stmt->bindParam(':created_by', $created_by);

            $stmt->execute();

            // Retrieve the ID of the newly inserted draft
            $wcheck_id = $connection->lastInsertId();

            if (!empty($items)) {
                $itemSql = "INSERT INTO wchecks_details (
                    wcheck_id, account_id, cost_center_id, memo, amount, discount_percentage, 
                    discount_amount, net_amount_before_vat, net_amount, vat_percentage, input_vat
                ) VALUES (
                    :wcheck_id, :account_id, :cost_center_id, :memo, :amount, :discount_percentage, 
                    :discount_amount, :net_amount_before_vat, :net_amount, :vat_percentage, :input_vat
                )";

                $itemStmt = $connection->prepare($itemSql);

                foreach ($items as $item) {
                    $itemStmt->bindParam(':wcheck_id', $wcheck_id);
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
                    $itemStmt->execute();
                }
            }

            // Commit the transaction
            $connection->commit();
            return ['success' => true, 'message' => 'Write Check saved as draft successfully'];
        } catch (PDOException $e) {
            $connection->rollback();
            error_log('Error in saveDraft: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }


    public static function getDraftDetails($wcheck_id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                wcd.id,
                wcd.wcheck_id,
                wcd.account_id,
                wcd.cost_center_id,
                wcd.memo,
                wcd.amount,
                wcd.discount_percentage,
                wcd.discount_amount,
                wcd.net_amount_before_vat,
                wcd.net_amount,
                wcd.vat_percentage,
                wcd.input_vat,
                coa.account_type_id,
                coa.gl_name,
                coa.account_code,
                coa.account_description,
                cc.code,
                cc.particular
            FROM 
                wchecks_details wcd
            LEFT JOIN
                chart_of_account coa ON wcd.account_id = coa.id
            LEFT JOIN
                cost_center cc ON wcd.cost_center_id = cc.id
            WHERE 
                wcd.wcheck_id = :wcheck_id
        ');

        $stmt->bindParam(':wcheck_id', $wcheck_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        return $stmt->fetchAll(); // Return the fetched data
    }

    public static function updateDraft(
        $wcheck_id,
        $cv_no,
        $check_no,
        $ref_no,
        $check_date,
        $bank_account_id,
        $payee_type,
        $payee_id,
        $memo,
        $location,
        $gross_amount,
        $discount_amount,
        $net_amount_due,
        $vat_percentage_amount,
        $net_of_vat,
        $tax_withheld_amount,
        $tax_withheld_percentage,
        $total_amount_due,
        $items,
        $created_by,
        $discount_account_id,
        $tax_withheld_account_id,
        $input_vat_account_id
    ) {
        global $connection;
    
        try {
            $connection->beginTransaction();
    
            // Update the main wcheck record
            $stmt = $connection->prepare("
                UPDATE wchecks 
                SET cv_no = :cv_no,
                    check_no = :check_no,
                    ref_no = :ref_no,
                    check_date = :check_date,
                    account_id = :bank_account_id,
                    payee_type = :payee_type,
                    payee_id = :payee_id,
                    memo = :memo,
                    location = :location,
                    gross_amount = :gross_amount,
                    discount_amount = :discount_amount,
                    net_amount_due = :net_amount_due,
                    vat_percentage_amount = :vat_percentage_amount,
                    net_of_vat = :net_of_vat,
                    tax_withheld_amount = :tax_withheld_amount,
                    tax_withheld_percentage = :tax_withheld_percentage,
                    total_amount_due = :total_amount_due,
                    status = 4
                WHERE id = :wcheck_id
            ");
    
            // Bind the parameters
            $stmt->bindParam(':wcheck_id', $wcheck_id, PDO::PARAM_INT);
            $stmt->bindParam(':cv_no', $cv_no, PDO::PARAM_STR);
            $stmt->bindParam(':check_no', $check_no, PDO::PARAM_STR);
            $stmt->bindParam(':ref_no', $ref_no, PDO::PARAM_STR);
            $stmt->bindParam(':check_date', $check_date, PDO::PARAM_STR);
            $stmt->bindParam(':bank_account_id', $bank_account_id, PDO::PARAM_INT);
            $stmt->bindParam(':payee_type', $payee_type, PDO::PARAM_STR);
            $stmt->bindParam(':payee_id', $payee_id, PDO::PARAM_INT);
            $stmt->bindParam(':memo', $memo, PDO::PARAM_STR);
            $stmt->bindParam(':location', $location, PDO::PARAM_STR);
            $stmt->bindParam(':gross_amount', $gross_amount, PDO::PARAM_STR);
            $stmt->bindParam(':discount_amount', $discount_amount, PDO::PARAM_STR);
            $stmt->bindParam(':net_amount_due', $net_amount_due, PDO::PARAM_STR);
            $stmt->bindParam(':vat_percentage_amount', $vat_percentage_amount, PDO::PARAM_STR);
            $stmt->bindParam(':net_of_vat', $net_of_vat, PDO::PARAM_STR);
            $stmt->bindParam(':tax_withheld_amount', $tax_withheld_amount, PDO::PARAM_STR);
            $stmt->bindParam(':tax_withheld_percentage', $tax_withheld_percentage, PDO::PARAM_STR);
            $stmt->bindParam(':total_amount_due', $total_amount_due, PDO::PARAM_STR);
    
            // Execute the statement
            $result = $stmt->execute();
    
            if (!$result) {
                throw new Exception("Failed to update wcheck. " . implode(", ", $stmt->errorInfo()));
            }
    
            // Delete existing check details
            $stmt = $connection->prepare("DELETE FROM wchecks_details WHERE wcheck_id = ?");
            $stmt->execute([$wcheck_id]);
    
            // Check if $items is an array before proceeding
            if (!is_array($items)) {
                throw new Exception("Items must be an array.");
            }
    
            // Prepare statement for inserting new check details
            $stmt = $connection->prepare("
                INSERT INTO wchecks_details (
                    wcheck_id, account_id, cost_center_id, memo, amount, discount_percentage, 
                    discount_amount, net_amount_before_vat, net_amount, vat_percentage, input_vat
                ) VALUES (
                    :wcheck_id, :account_id, :cost_center_id, :memo, :amount, :discount_percentage, 
                    :discount_amount, :net_amount_before_vat, :net_amount, :vat_percentage, :input_vat
                )
            ");
    
            // Insert new check details
            foreach ($items as $item) {
                $stmt->execute([
                    ':wcheck_id' => $wcheck_id,
                    ':account_id' => $item['account_id'],
                    ':cost_center_id' => $item['cost_center_id'],
                    ':memo' => $item['memo'],
                    ':amount' => $item['amount'],
                    ':discount_percentage' => isset($item['discount_percentage']) ? $item['discount_percentage'] : 0,
                    ':discount_amount' => isset($item['discount_amount']) ? $item['discount_amount'] : 0,
                    ':net_amount_before_vat' => isset($item['net_amount_before_vat']) ? $item['net_amount_before_vat'] : 0,
                    ':net_amount' => $item['net_amount'],
                    ':vat_percentage' => isset($item['vat_percentage']) ? $item['vat_percentage'] : 0,
                    ':input_vat' => isset($item['input_vat']) ? $item['input_vat'] : 0
                ]);
            }
    
            // Commit the transaction
            $connection->commit();
    
            return [
                'success' => true,
                'wcheckId' => $wcheck_id
            ];
        } catch (Exception $ex) {
            // Rollback transaction in case of an error
            $connection->rollback();
            error_log('Error updating draft wcheck: ' . $ex->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $ex->getMessage()
            ];
        }
    }
    
    public static function saveFinal(
        $wcheck_id,
        $cv_no,
        $check_no,
        $ref_no,
        $check_date,
        $bank_account_id,
        $payee_type,
        $payee_id,
        $payee_name,
        $memo,
        $location,
        $gross_amount,
        $discount_amount,
        $net_amount_due,
        $vat_percentage_amount,
        $net_of_vat,
        $tax_withheld_amount,
        $tax_withheld_percentage,
        $total_amount_due,
        $items,
        $created_by,
        $discount_account_id,
        $tax_withheld_account_id,
        $input_vat_account_id
    ) {
        global $connection;

        try {
            // Start a transaction
            $connection->beginTransaction();

            $transaction_type = "Check Expense";

            // Fetch existing draft details
            $existingDetails = self::getDraftDetails($wcheck_id);

            // Prepare the update statement for wchecks
            $stmt = $connection->prepare("
                UPDATE wchecks 
                SET cv_no = :cv_no,
                    check_no = :check_no,
                    ref_no = :ref_no,
                    check_date = :check_date,
                    account_id = :account_id,
                    payee_type = :payee_type,
                    payee_id = :payee_id,
                    memo = :memo,
                    location = :location,
                    gross_amount = :gross_amount,
                    discount_amount = :discount_amount,
                    net_amount_due = :net_amount_due,
                    vat_percentage_amount = :vat_percentage_amount,
                    net_of_vat = :net_of_vat,
                    tax_withheld_amount = :tax_withheld_amount,
                    tax_withheld_percentage = :tax_withheld_percentage,
                    total_amount_due = :total_amount_due
                WHERE id = :wcheck_id
            ");

            // Bind the parameters
            $stmt->bindParam(':wcheck_id', $wcheck_id, PDO::PARAM_INT);
            $stmt->bindParam(':cv_no', $cv_no, PDO::PARAM_STR);
            $stmt->bindParam(':check_no', $check_no, PDO::PARAM_STR);
            $stmt->bindParam(':ref_no', $ref_no, PDO::PARAM_STR);
            $stmt->bindParam(':check_date', $check_date, PDO::PARAM_STR);
            $stmt->bindParam(':account_id', $bank_account_id, PDO::PARAM_INT);
            $stmt->bindParam(':payee_type', $payee_type, PDO::PARAM_STR);
            $stmt->bindParam(':payee_id', $payee_id, PDO::PARAM_INT);
            $stmt->bindParam(':memo', $memo, PDO::PARAM_STR);
            $stmt->bindParam(':location', $location, PDO::PARAM_STR);
            $stmt->bindParam(':gross_amount', $gross_amount, PDO::PARAM_STR);
            $stmt->bindParam(':discount_amount', $discount_amount, PDO::PARAM_STR);
            $stmt->bindParam(':net_amount_due', $net_amount_due, PDO::PARAM_STR);
            $stmt->bindParam(':vat_percentage_amount', $vat_percentage_amount, PDO::PARAM_STR);
            $stmt->bindParam(':net_of_vat', $net_of_vat, PDO::PARAM_STR);
            $stmt->bindParam(':tax_withheld_amount', $tax_withheld_amount, PDO::PARAM_STR);
            $stmt->bindParam(':tax_withheld_percentage', $tax_withheld_percentage, PDO::PARAM_STR);
            $stmt->bindParam(':total_amount_due', $total_amount_due, PDO::PARAM_STR);
            // $stmt->bindParam(':discount_account_id', $discount_account_id, PDO::PARAM_INT);

            // Handle array parameters (encoding arrays to JSON if necessary)
            // $stmt->bindValue(':input_vat_account_id', is_array($input_vat_account_id) ? json_encode($input_vat_account_id) : $input_vat_account_id, PDO::PARAM_STR);
            // $stmt->bindValue(':tax_withheld_account_id', is_array($tax_withheld_account_id) ? json_encode($tax_withheld_account_id) : $tax_withheld_account_id, PDO::PARAM_STR);

            // Execute the update statement
            $stmt->execute();

            // Fetch the updated wcheck details for audit trail purposes
            $stmt = $connection->prepare("
                SELECT account_id, total_amount_due, tax_withheld_amount, tax_withheld_account_id, 
                    check_date, input_vat_account_id, discount_account_id, vat_percentage_amount, 
                    discount_amount 
                FROM wchecks 
                WHERE id = :wcheck_id
            ");
            $stmt->bindParam(':wcheck_id', $wcheck_id, PDO::PARAM_INT);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$result) {
                throw new Exception("Updated check record not found.");
            }

            // Set variables for further use based on the latest data
            $bank_account_id = $result['account_id'];
            $total_amount_due = $result['total_amount_due'];
            $tax_withheld_amount = $result['tax_withheld_amount'];
            $wtax_account_id = $result['tax_withheld_account_id'];
            $check_date = $result['check_date'];
            $input_vat_account_id = $result['input_vat_account_id'];
            $vat_percentage_amount = $result['vat_percentage_amount'];
            $discount_account_id = $result['discount_account_id'];
            $discount_amount = $result['discount_amount'];


            // Delete existing check details before adding new ones
            $stmt = $connection->prepare("DELETE FROM wchecks_details WHERE wcheck_id = ?");
            $stmt->execute([$wcheck_id]);

            // Insert new check details
            $stmt = $connection->prepare("
                INSERT INTO wchecks_details (
                    wcheck_id, account_id, cost_center_id, memo, amount, discount_percentage, 
                    discount_amount, net_amount_before_vat, net_amount, vat_percentage, input_vat
                ) VALUES (
                    :wcheck_id, :account_id, :cost_center_id, :memo, :amount, :discount_percentage, 
                    :discount_amount, :net_amount_before_vat, :net_amount, :vat_percentage, :input_vat
                )
            ");

            foreach ($items as $item) {
                // Set default values if optional fields are not provided
                $discount_percentage = isset($item['discount_percentage']) ? $item['discount_percentage'] : 0;
                $discount_amount = isset($item['discount_amount']) ? $item['discount_amount'] : 0;
                $net_amount_before_vat = isset($item['net_amount_before_vat']) ? $item['net_amount_before_vat'] : 0;
                $vat_percentage = isset($item['vat_percentage']) ? $item['vat_percentage'] : 0;
                $input_vat = isset($item['input_vat']) ? $item['input_vat'] : 0;

                // Execute insert for each item
                $stmt->execute([
                    ':wcheck_id' => $wcheck_id,
                    ':account_id' => $item['account_id'],
                    ':cost_center_id' => $item['cost_center_id'],
                    ':memo' => $item['memo'],
                    ':amount' => $item['amount'],
                    ':discount_percentage' => $discount_percentage,
                    ':discount_amount' => $discount_amount,
                    ':net_amount_before_vat' => $net_amount_before_vat,
                    ':net_amount' => $item['net_amount'],
                    ':vat_percentage' => $vat_percentage,
                    ':input_vat' => $input_vat
                ]);
            }


            // Initialize variables for total discount and VAT
            $total_discount = 0.00;
            $total_input_vat = 0.00;

            foreach ($existingDetails as $detail) {
                // Log the existing details into the audit trail
                self::logAuditTrail(
                    $wcheck_id,
                    $transaction_type,
                    $check_date,
                    $cv_no, // previous ref_no
                    $location,
                    $payee_name,
                    $detail['account_id'],
                    $detail['net_amount'] + $detail['discount_amount'], // Added discount_amount here
                    0.00,
                    $created_by
                );

                // Accumulate total discount and input VAT
                $total_discount += $detail['discount_amount'];
                $total_input_vat += $detail['input_vat'];
            }

            // Audit Check Discount Account (single entry for total discount)
            if ($discount_account_id !== null) {
                self::logAuditTrail(
                    $wcheck_id,
                    $transaction_type,
                    $check_date,
                    $cv_no,
                    $location,
                    $payee_name,
                    $discount_account_id,
                    0.00,
                    $discount_amount,
                    $created_by
                );
            }

            // Audit Check Input VAT Account (single entry for total input VAT)
            if ($input_vat_account_id !== null) {
                self::logAuditTrail(
                    $wcheck_id,
                    $transaction_type,
                    $check_date,
                    $cv_no,
                    $location,
                    $payee_name,
                    $input_vat_account_id,
                    $total_input_vat,
                    0.00,
                    $created_by
                );
            }

            // Audit Trail Wtax Account
            self::logAuditTrail(
                $wcheck_id,
                $transaction_type,
                $check_date,
                $cv_no,
                $location,
                $payee_name,
                $wtax_account_id,
                0.00,
                $tax_withheld_amount,
                $created_by
            );

            // Audit Trail Bank Account
            self::logAuditTrail(
                $wcheck_id,
                $transaction_type,
                $check_date,
                $cv_no,
                $location,
                $payee_name,
                $bank_account_id,
                0.00,
                $total_amount_due,
                $created_by
            );

            // Commit the transaction after successful updates
            $connection->commit();
        } catch (PDOException $e) {
            // Rollback transaction in case of a PDO error
            $connection->rollBack();
            throw new Exception("Database error: " . $e->getMessage());
        } catch (Exception $e) {
            // Rollback transaction in case of a general error
            $connection->rollBack();
            throw new Exception("Error: " . $e->getMessage());
        }
    }

    


    public static function void($id)
    {
        global $connection;

        try {
            $connection->beginTransaction();

            // Update the status to 3 (void) in the sales_invoice table
            $stmt = $connection->prepare("UPDATE wchecks SET status = 3 WHERE id = :id");
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
