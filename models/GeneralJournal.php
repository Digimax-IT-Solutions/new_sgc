<?php

require_once __DIR__ . '/../_init.php';

class GeneralJournal
{
    public $id;
    public $entry_no;
    public $journal_date;
    public $total_debit;
    public $total_credit;
    public $memo;
    public $location;
    public $print_status;
    public $status;
    public $created_at;
    public $updated_at;
    public $details = [];
    private static $cache = null;

    public function __construct($data)
    {
        $this->id = $data['id'] ?? null;
        $this->entry_no = $data['entry_no'] ?? null;
        $this->journal_date = $data['journal_date'] ?? null;
        $this->total_debit = $data['total_debit'] ?? null;
        $this->total_credit = $data['total_credit'] ?? null;
        $this->memo = $data['memo'] ?? null;
        $this->location = $data['location'] ?? null;
        $this->print_status = $data['print_status'] ?? null;
        $this->status = $data['status'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;

        // Initialize details as an empty array and populate if provided
        if (isset($data['details']) && is_array($data['details'])) {
            foreach ($data['details'] as $detail) {
                $this->details[] = $detail;
            }
        }
    }

    public static function add($entry_no, $journal_date, $total_debit, $total_credit, $created_by, $memo, $location, $details)
    {
        global $connection;

        try {
            // Start a transaction
            $connection->beginTransaction();

            // Insert the main journal entry
            $stmt = $connection->prepare("
                INSERT INTO general_journal (
                    entry_no,
                    journal_date,
                    total_debit,
                    total_credit,
                    memo,
                    location,
                    created_at
                ) VALUES (?,?,?,?,?,?, NOW())
            ");

            $stmt->execute([
                $entry_no,
                $journal_date,
                $total_debit,
                $total_credit,
                $memo,
                $location
            ]);

            // Retrieve the ID of the newly inserted journal entry
            $general_journal_id = $connection->lastInsertId();
            $transaction_type = "General Journal";

            // Insert journal details
            foreach ($details as $detail) {

                $detail['debit'] = is_numeric($detail['debit']) ? $detail['debit'] : 0;
                $detail['credit'] = is_numeric($detail['credit']) ? $detail['credit'] : 0;

                self::addGeneralJournalItems(
                    $general_journal_id,
                    $detail['account_id'],
                    $detail['cost_center_id'],
                    $detail['name'],
                    $detail['debit'],
                    $detail['credit'],
                    $detail['memo']
                );

                // Log audit trail for each detail line
                self::logAuditTrail(
                    $general_journal_id,
                    $transaction_type,
                    $journal_date,
                    $entry_no,
                    $location,
                    $detail['name'],
                    $detail['account_id'],
                    $detail['debit'],
                    $detail['credit'],
                    $created_by
                );
            }

            // Commit the transaction
            $connection->commit();
        } catch (PDOException $e) {
            // Rollback the transaction on error
            $connection->rollBack();
            throw $e;
        }
    }

    public static function addGeneralJournalItems($general_journal_id, $account_id, $cost_center_id, $name, $debit, $credit, $memo)
    {
        global $connection;

        $stmt = $connection->prepare("
            INSERT INTO general_journal_details (
                general_journal_id,
                account_id,
                cost_center_id,
                name,           
                debit,
                credit,
                memo
            ) VALUES (?,?,?,?,?,?,?)
        ");

        $stmt->execute([
            $general_journal_id,
            $account_id,
            $cost_center_id,
            $name,
            $debit,
            $credit,
            $memo,
        ]);
    }

    private static function logAuditTrail($general_journal_id, $transaction_type, $transaction_date, $ref_no, $location, $name, $account_id, $debit, $credit, $created_by)
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
            ) VALUES (?,?,?,?,?,?, ?,?, ?,?, NOW())
        ");

        $stmt->execute([
            $general_journal_id,
            $transaction_type,
            $transaction_date,
            $ref_no,
            $location,
            $name,
            $account_id,
            $debit,
            $credit,
            $created_by
        ]);
    }

    public static function all()
    {
        global $connection;

        $stmt = $connection->prepare('SELECT * FROM general_journal');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $generalJournals = [];
        while ($row = $stmt->fetch()) {
            $generalJournals[] = new GeneralJournal($row);
        }

        return $generalJournals;
    }

    public static function find($id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                gj.id,
                gj.entry_no,
                gj.journal_date,
                gj.total_debit,
                gj.total_credit,
                gj.memo,
                gj.location,
                gj.print_status,
                gj.status
            FROM general_journal gj
            WHERE gj.id = :id
        ');

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $generalData = $stmt->fetch();

        if (!$generalData) {
            return null;
        }

        $generalData['details'] = self::getJournalDetails($id);

        return new GeneralJournal($generalData);
    }

    public static function getJournalDetails($general_journal_id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                gjd.id,
                gjd.general_journal_id,
                gjd.account_id,
                gjd.cost_center_id,
                gjd.debit,
                gjd.credit,
                gjd.memo,
                gjd.name,
                coa.account_code,
                coa.account_description
            FROM general_journal_details gjd
            LEFT JOIN chart_of_account coa ON gjd.account_id = coa.id
            LEFT JOIN cost_center cc ON gjd.cost_center_id = cc.id
            WHERE gjd.general_journal_id = :general_journal_id
        ');

        $stmt->bindParam(':general_journal_id', $general_journal_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $details = [];
        while ($row = $stmt->fetch()) {
            $details[] = [
                'id' => $row['id'],
                'general_journal_id' => $row['general_journal_id'],
                'account_id' => $row['account_id'],
                'cost_center_id' => $row['cost_center_id'],
                'account_code' => $row['account_code'],
                'account_description' => $row['account_description'],
                'debit' => $row['debit'],
                'credit' => $row['credit'],
                'memo' => $row['memo'],
                'name' => $row['name']

            ];
        }

        return $details;
    }

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

        $generalData = $stmt->fetch();

        if (!$generalData) {
            return null;
        }

        $generalData['details'] = self::getJournalDetails($generalData['id']);

        return new GeneralJournal($generalData);
    }

    public static function getLastTransactionId()
    {
        global $connection;

        $stmt = $connection->query("SELECT MAX(id) AS last_transaction_id FROM general_journal");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['last_transaction_id'] ?? null;
    }

    public static function update($id, $entry_no, $journal_date, $total_debit, $total_credit, $memo, $status)
    {
        global $connection;

        $stmt = $connection->prepare("
            UPDATE general_journal 
            SET entry_no = :entry_no, 
                journal_date = :journal_date, 
                total_debit = :total_debit, 
                total_credit = :total_credit, 
                memo = :memo,
                status = :status
            WHERE id = :id
        ");

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':entry_no', $entry_no, PDO::PARAM_STR);
        $stmt->bindParam(':journal_date', $journal_date, PDO::PARAM_STR);
        $stmt->bindParam(':total_debit', $total_debit, PDO::PARAM_STR);
        $stmt->bindParam(':total_credit', $total_credit, PDO::PARAM_STR);
        $stmt->bindParam(':memo', $memo, PDO::PARAM_STR);
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);

        $stmt->execute();
    }

    public static function updateDetails($general_journal_id, $item_data)
    {
        // Get database connection
        global $connection;

        // Start transaction
        $connection->beginTransaction();

        try {
            // Delete existing details for the given invoice_id
            $stmt = $connection->prepare("DELETE FROM general_journal_details WHERE general_journal_id = :general_journal_id");
            $stmt->execute([':general_journal_id' => $general_journal_id]);

            // Insert new details into sales_invoice_details
            foreach ($item_data as $item) {
                $stmt = $connection->prepare("
                    INSERT INTO general_journal_details (general_journal_id, account_id, cost_center_id, name, debit, credit, memo)
                    VALUES (:general_journal_id, :account_id, :cost_center_id, :name, :debit, :credit, :memo)
                ");
                $stmt->execute([
                    ':general_journal_id' => $general_journal_id,
                    ':account_id' => $item['account_id'],
                    ':cost_center_id' => $item['cost_center_id'],
                    ':name' => $item['name'],
                    ':debit' => $item['debit'],
                    ':credit' => $item['credit'],
                    ':memo' => $item['memo']
                ]);
            }

            // Commit transaction
            $connection->commit();
        } catch (Exception $e) {
            // Rollback transaction if something failed
            $connection->rollBack();
            error_log("Update details failed: " . $e->getMessage()); // Log error message
            throw $e;
        }
    }

    // GET LAST ENTRY_NO 
    public static function getLastEntryNo()
    {
        global $connection;

        // Prepare and execute the query
        $stmt = $connection->prepare("
        SELECT entry_no 
        FROM general_journal 
        WHERE entry_no IS NOT NULL AND entry_no <> '' 
        ORDER BY entry_no DESC 
        LIMIT 1
    ");
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $result = $stmt->fetch();

        // Check if there is a result and fetch the latest AP voucher number
        if ($result) {
            $latestNo = $result['entry_no'];
            // Assuming the format is 'CM' followed by digits
            $numericPart = intval(substr($latestNo, 2)); // 'CM' is 2 characters
            $newNo = $numericPart + 1;
        } else {
            // If no valid credit number exists, start with 1
            $newNo = 1;
        }

        // Format the new number with leading zeros
        $newEntryNo = 'GJ' . str_pad($newNo, 9, '0', STR_PAD_LEFT);

        return $newEntryNo;
    }

    public static function saveDraft(
        $journal_date,
        $total_debit,
        $total_credit,
        $memo,
        $location,
        $items
    ) {
        global $connection;

        try {
            $connection->beginTransaction();

            // Insert into general_journal table with draft status
            $sql = "INSERT INTO general_journal (
                    journal_date, total_debit, total_credit, memo, location, status
                ) VALUES (
                    :journal_date, :total_debit, :total_credit, :memo, :location, :status
                )";

            $stmt = $connection->prepare($sql);
            $stmt->bindParam(':journal_date', $journal_date);
            $stmt->bindParam(':total_debit', $total_debit);
            $stmt->bindParam(':total_credit', $total_credit);
            $stmt->bindParam(':memo', $memo);
            $stmt->bindParam(':location', $location);
            $stmt->bindValue(':status', 4, PDO::PARAM_INT); // Set status to 4 for draft

            $stmt->execute();

            $general_journal_id = $connection->lastInsertId();

            // Ensure $items is an array
            if (is_array($items) && !empty($items)) {
                $itemSql = "INSERT INTO general_journal_details (
                    general_journal_id, account_id, cost_center_id, name, debit, credit, memo
                ) VALUES (
                    :general_journal_id, :account_id, :cost_center_id, :name, :debit, :credit, :memo
                )";

                $itemStmt = $connection->prepare($itemSql);

                foreach ($items as $item) {
                    $itemStmt->bindParam(':general_journal_id', $general_journal_id);
                    $itemStmt->bindParam(':account_id', $item['account_id']);
                    $itemStmt->bindParam(':cost_center_id', $item['cost_center_id']);
                    $itemStmt->bindParam(':name', $item['name']);
                    $itemStmt->bindParam(':debit', $item['debit']);
                    $itemStmt->bindParam(':credit', $item['credit']);
                    $itemStmt->bindParam(':memo', $item['memo']);
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
    public static function void($id)
    {
        global $connection;

        try {
            $connection->beginTransaction();

            // Update the status to 3 (void) in the sales_invoice table
            $stmt = $connection->prepare("UPDATE general_journal SET status = 3 WHERE id = :id");
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

    public static function draftDetails($general_journal_id)
    {
        global $connection;

        $stmt = $connection->prepare('
            SELECT 
                gjd.id,
                gjd.general_journal_id,
                gjd.account_id,
                gjd.cost_center_id,
                gjd.memo,
                gjd.debit,
                gjd.credit,
                gjd.name,
                coa.gl_name,
                coa.account_code,
                coa.account_description,
                cc.particular
            FROM 
                general_journal_details gjd
            LEFT JOIN
                chart_of_account coa ON gjd.account_id = coa.id
            LEFT JOIN
                cost_center cc ON gjd.cost_center_id = cc.id
            WHERE 
                gjd.general_journal_id = :general_journal_id
        ');

        $stmt->bindParam(':general_journal_id', $general_journal_id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        return $stmt->fetchAll(); // Return the fetched data
    }

    public static function updateDraft(
        $general_journal_id,
        $journal_date,
        $total_debit,
        $total_credit,
        $memo,
        $location,
        $details
    ) {
        global $connection;

        try {
            // Start the transaction
            $connection->beginTransaction();

            // Update the general_journal record
            $stmt = $connection->prepare("
                UPDATE general_journal 
                SET journal_date = :journal_date,
                    total_debit = :total_debit,
                    total_credit = :total_credit,
                    memo = :memo,
                    location = :location,
                    status = 4
                WHERE id = :general_journal_id
            ");

            // Bind the parameters
            $stmt->bindParam(':general_journal_id', $general_journal_id, PDO::PARAM_INT);
            $stmt->bindParam(':entry_no', $entry_no, PDO::PARAM_STR);
            $stmt->bindParam(':journal_date', $journal_date, PDO::PARAM_STR);
            $stmt->bindParam(':total_debit', $total_debit, PDO::PARAM_STR);
            $stmt->bindParam(':total_credit', $total_credit, PDO::PARAM_STR);
            $stmt->bindParam(':memo', $memo, PDO::PARAM_STR);
            $stmt->bindParam(':location', $location, PDO::PARAM_STR);

            // Execute the statement
            $result = $stmt->execute();

            if ($result) {
                // Delete existing general_journal_details before inserting new ones
                $stmt = $connection->prepare("DELETE FROM general_journal_details WHERE general_journal_id = ?");
                $stmt->execute([$general_journal_id]);

                // Prepare the statement for inserting new journal details
                $stmt = $connection->prepare("
                    INSERT INTO general_journal_details (
                        general_journal_id, account_id, cost_center_id, name, memo, debit, credit
                    ) VALUES (
                        :general_journal_id, :account_id, :cost_center_id, :name, :memo, :debit, :credit
                    )
                ");

                // Insert new journal details
                foreach ($details as $detail) {
                    $stmt->execute([
                        ':general_journal_id' => $general_journal_id,
                        ':account_id' => $detail['account_id'],
                        ':cost_center_id' => $detail['cost_center_id'],
                        ':name' => $detail['name'],
                        ':memo' => $detail['memo'],
                        ':debit' => $detail['debit'],
                        ':credit' => $detail['credit']
                    ]);
                }

                // Commit the transaction
                $connection->commit();
                return [
                    'success' => true,
                    'journalId' => $general_journal_id
                ];
            } else {
                throw new Exception("Failed to update journal.");
            }
        } catch (Exception $ex) {
            // Rollback the transaction in case of an error
            $connection->rollback();
            error_log('Error updating draft journal: ' . $ex->getMessage());
            return [
                'success' => false,
                'message' => 'Error: ' . $ex->getMessage()
            ];
        }
    }


    public static function saveFinal(
        $general_journal_id,
        $entry_no,
        $journal_date,
        $total_debit,
        $total_credit,
        $memo,
        $location,
        $details,
        $created_by
    ) {
        global $connection;

        try {
            // Start a transaction
            $connection->beginTransaction();

            $transaction_type = "General Journal";

            // Update general_journal table
            $stmt = $connection->prepare("
                UPDATE general_journal 
                SET entry_no = :entry_no,
                    journal_date = :journal_date,
                    total_debit = :total_debit,
                    total_credit = :total_credit,
                    memo = :memo,
                    location = :location,
                    status = 1
                WHERE id = :general_journal_id
            ");

            $stmt->execute([
                ':general_journal_id' => $general_journal_id,
                ':entry_no' => $entry_no,
                ':journal_date' => $journal_date,
                ':total_debit' => $total_debit,
                ':total_credit' => $total_credit,
                ':memo' => $memo,
                ':location' => $location
            ]);

            // Delete existing journal details
            $stmt = $connection->prepare("DELETE FROM general_journal_details WHERE general_journal_id = ?");
            $stmt->execute([$general_journal_id]);

            // Insert new journal details
            $stmt = $connection->prepare("
                INSERT INTO general_journal_details (
                    general_journal_id, account_id, cost_center_id, name, memo, debit, credit
                ) VALUES (
                    :general_journal_id, :account_id, :cost_center_id, :name, :memo, :debit, :credit
                )
            ");

            foreach ($details as $detail) {
                $stmt->execute([
                    ':general_journal_id' => $general_journal_id,
                    ':account_id' => $detail['account_id'],
                    ':cost_center_id' => $detail['cost_center_id'],
                    ':name' => $detail['name'],
                    ':memo' => $detail['memo'],
                    ':debit' => $detail['debit'],
                    ':credit' => $detail['credit']
                ]);

                // Log audit trail for each detail line
                self::logAuditTrail(
                    $general_journal_id,
                    $transaction_type,
                    $journal_date,
                    $entry_no,
                    $location,
                    $detail['name'],
                    $detail['account_id'],
                    $detail['debit'],
                    $detail['credit'],
                    $created_by
                );
            }

            // Commit the transaction
            $connection->commit();
            return true;
        } catch (PDOException $e) {
            $connection->rollBack();
            throw new Exception("Database error: " . $e->getMessage());
        } catch (Exception $e) {
            $connection->rollBack();
            throw new Exception("Error: " . $e->getMessage());
        }
    }
}
