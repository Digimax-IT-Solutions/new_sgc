<?php
// Include your database connection file
include('../../connect.php');


// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        // Start a database transaction
        $db->beginTransaction();

        // Retrieve form data
        $bankAccountName = $_POST['bankAccountName'];
        $vendor = $_POST['vendor'];
        $address = $_POST['address'];
        $reference_no = $_POST['reference_no'];
        $memos = $_POST['memos'];
        $terms = $_POST['terms'];
        $bill_date = $_POST['bill_date'];
        $bill_due = $_POST['bill_due'];
        $total_amount = $_POST['total_amount'];

        // Validate and sanitize your data as needed

        // Insert data into the 'bills' table
        $checkQuery = "INSERT INTO bills (
            bankAccountName, 
            vendor, 
            address, 
            reference_no, 
            memo,
            terms,
            bill_date,
            bill_due,
            total_amount
        ) VALUES (
            :bankAccountName, 
            :vendor, 
            :address, 
            :reference_no, 
            :memos,
            :terms,
            :bill_date,
            :bill_due,
            :total_amount
        )";

        $checkStmt = $db->prepare($checkQuery);
        $checkStmt->bindParam(':bankAccountName', $bankAccountName);
        $checkStmt->bindParam(':vendor', $vendor);
        $checkStmt->bindParam(':address', $address);
        $checkStmt->bindParam(':reference_no', $reference_no);
        $checkStmt->bindParam(':memos', $memos);
        $checkStmt->bindParam(':terms', $terms);
        $checkStmt->bindParam(':bill_date', $bill_date);
        $checkStmt->bindParam(':bill_due', $bill_due);
        $checkStmt->bindParam(':total_amount', $total_amount);

        if ($checkStmt->execute()) {
            $bill_id = $db->lastInsertId();

            // Handle expense data
            $accounts = $_POST["account"];
            $amounts = $_POST["amount"];
            $memo = $_POST["memo"];

            foreach ($accounts as $key => $account) {
                // Insert data into the 'bills_details' table
                $expensesQuery = "INSERT INTO bills_details (bill_id, account, amount, memo) VALUES (:bill_id, :account, :amount, :memo)";
                $expensesStmt = $db->prepare($expensesQuery);
                $expensesStmt->bindParam(":bill_id", $bill_id);
                $expensesStmt->bindParam(":account", $account);
                $expensesStmt->bindParam(":memo", $memo[$key]); // Fix the field name
                $expensesStmt->bindParam(":amount", $amounts[$key]);

                if (!$expensesStmt->execute()) {
                    throw new Exception("Error inserting bill expenses data: " . implode(", ", $expensesStmt->errorInfo()));
                }
            }

            // Commit the transaction
            $db->commit();

            // Return a success response
            $response = ['status' => 'success', 'message' => 'Bill saved successfully', 'bill_id' => $bill_id];
            echo json_encode($response);
        } else {
            // Handle the case where the insertion into the bills table failed
            throw new Exception("Error inserting bill data: " . implode(", ", $checkStmt->errorInfo()));
        }
    } catch (Exception $e) {
        // An error occurred, rollback the transaction
        $db->rollBack();

        // Log the detailed error message for debugging
        $errorDetails = implode(", ", $checkStmt->errorInfo());
        error_log('Error saving bill: ' . $e->getMessage() . ' - ' . $errorDetails);

        // Return a response with MySQL error details (for development/debugging purposes)
        $response = ['status' => 'error', 'message' => 'Error saving bill. Please try again later.', 'error_details' => $errorDetails];
        echo json_encode($response);
    }
} else {
      // An error occurred, rollback the transaction
      $db->rollBack();

      // Log the detailed error message for debugging
      $errorDetails = implode(", ", $checkStmt->errorInfo());
      error_log('Error saving bill: ' . $e->getMessage() . ' - ' . $errorDetails);
  
      // Return a response with MySQL error details (for development/debugging purposes)
      $response = ['status' => 'error', 'message' => 'Error saving bill. Please try again later.', 'error_details' => $errorDetails];
      echo json_encode($response);
}
