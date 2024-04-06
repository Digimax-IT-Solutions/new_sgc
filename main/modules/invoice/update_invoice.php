<?php

// Start session for user authentication
session_start();


// Include the database connection script
include('../../connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Start a transaction
        $db->beginTransaction();
        $invoiceID = $_POST["invoiceID"]; 
        // Retrieve and sanitize form data
        $invoiceID = isset($_POST["invoiceID"]) ? $_POST["invoiceID"] : null; // Assuming you have a hidden input field for invoiceID in your form

        // Retrieve and sanitize form data
        $invoiceNo = $_POST["invoiceNo"];
        
        // Add a validation check to ensure that the invoiceNo doesn't already exist
        // if ($invoiceID !== null) {
        //     $checkInvoiceNoQuery = "SELECT COUNT(*) FROM sales_invoice WHERE invoiceNo = :invoiceNo AND invoiceID != :invoiceID";
        //     $checkInvoiceNoStmt = $db->prepare($checkInvoiceNoQuery);
        //     $checkInvoiceNoStmt->bindParam(":invoiceNo", $invoiceNo);
        //     $checkInvoiceNoStmt->bindParam(":invoiceID", $invoiceID);
        //     $checkInvoiceNoStmt->execute();

        //     if ($checkInvoiceNoStmt->fetchColumn() > 0) {
        //         throw new Exception("Invoice with the same invoiceNo already exists.");
        //     }
        // }

        $invoiceDate = $_POST["invoiceDate"];
        $invoiceDueDate = $_POST["invoiceDueDate"];
        $invoiceBusinessStyle = $_POST["invoiceBusinessStyle"];
        $invoiceTin = $_POST["invoiceTin"];
        $invoicePo = $_POST["invoicePo"];
        $customer = $_POST["customer"];
        $address = $_POST["address"];
        // $shippingAddress = $_POST["shippingAddress"];
        // $email = $_POST["email"];
        $account = $_POST["account"];
        $paymentMethod = $_POST["paymentMethod"];
        $terms = $_POST["terms"];
        // $location = $_POST["location"];
        $memo = $_POST["memo"];
        $grossAmount = $_POST["grossAmount"];
        $discountPercentage = isset($_POST["discountPercentage"]) ? $_POST["discountPercentage"] : 0;
        $netAmountDue = $_POST["netAmountDue"];
        $vatPercentage = $_POST["vatPercentage"];
        $netOfVat = $_POST["netOfVat"];
        $taxWithheldPercentage = $_POST["taxWithheldPercentage"];
        $totalAmountDue = $_POST["totalAmountDue"];

        // Validate the discountPercentage value (you may add additional validation if needed)
        $discountPercentage = is_numeric($discountPercentage) ? $discountPercentage : 0;

        // Update sales invoice data in the database
        $query = "UPDATE sales_invoice SET
            invoiceNo = :invoiceNo,
            invoiceDate = :invoiceDate,
            invoiceDueDate = :invoiceDueDate,
            invoiceBusinessStyle = :invoiceBusinessStyle,
            invoiceTin = :invoiceTin,
            invoicePo = :invoicePo,
            customer = :customer,
            address = :address,
            -- shippingAddress = :shippingAddress,
            -- email = :email,
            account = :account,
            paymentMethod = :paymentMethod,
            terms = :terms,
            -- location = :location,
            memo = :memo,
            grossAmount = :grossAmount,
            discountPercentage = :discountPercentage,
            netAmountDue = :netAmountDue,
            vatPercentage = :vatPercentage,
            netOfVat = :netOfVat,
            taxWithheldPercentage = :taxWithheldPercentage,
            totalAmountDue = :totalAmountDue
            WHERE invoiceID = :invoiceID";

        $stmt = $db->prepare($query);
        $stmt->bindParam(":invoiceID", $invoiceID);
        $stmt->bindParam(":invoiceNo", $invoiceNo);
        $stmt->bindParam(":invoiceDate", $invoiceDate);
        $stmt->bindParam(":invoiceDueDate", $invoiceDueDate);
        $stmt->bindParam(":invoiceBusinessStyle", $invoiceBusinessStyle);
        $stmt->bindParam(":invoiceTin", $invoiceTin);
        $stmt->bindParam(":invoicePo", $invoicePo);
        $stmt->bindParam(":customer", $customer);
        $stmt->bindParam(":address", $address);
        // $stmt->bindParam(":shippingAddress", $shippingAddress);
        // $stmt->bindParam(":email", $email);
        $stmt->bindParam(":account", $account);
        $stmt->bindParam(":paymentMethod", $paymentMethod);
        $stmt->bindParam(":terms", $terms);
        // $stmt->bindParam(":location", $location);
        $stmt->bindParam(":memo", $memo);
        $stmt->bindParam(":grossAmount", $grossAmount);
        $stmt->bindParam(":discountPercentage", $discountPercentage);
        $stmt->bindParam(":netAmountDue", $netAmountDue);
        $stmt->bindParam(":vatPercentage", $vatPercentage);
        $stmt->bindParam(":netOfVat", $netOfVat);
        $stmt->bindParam(":taxWithheldPercentage", $taxWithheldPercentage);
        $stmt->bindParam(":totalAmountDue", $totalAmountDue);

        if (!$stmt->execute()) {
            throw new Exception("Error updating sales invoice data.");
        }

        // Delete existing sales invoice items
        $deleteItemsQuery = "DELETE FROM sales_invoice_items WHERE salesInvoiceID = :invoiceID";
        $deleteItemsStmt = $db->prepare($deleteItemsQuery);
        $deleteItemsStmt->bindParam(":invoiceID", $invoiceID);

        if (!$deleteItemsStmt->execute()) {
            throw new Exception("Error deleting existing sales invoice items.");
        }

        // Iterate over each item and insert into the database
        foreach ($_POST["item"] as $key => $item) {
            $description = $_POST["description"][$key];
            $quantity = $_POST["quantity"][$key];
            $uom = $_POST["uom"][$key];
            $rate = $_POST["rate"][$key];
            $amount = $_POST["amount"][$key];

            // Insert item data into the database
            $query = "INSERT INTO sales_invoice_items (
                salesInvoiceID, 
                item, 
                description, 
                quantity, 
                uom,
                rate, 
                amount
            ) VALUES (
                :invoiceID, 
                :item, 
                :description, 
                :quantity, 
                :uom,
                :rate, 
                :amount
            )";

            $stmt = $db->prepare($query);
            $stmt->bindParam(":invoiceID", $invoiceID);
            $stmt->bindParam(":item", $item);
            $stmt->bindParam(":description", $description);
            $stmt->bindParam(":quantity", $quantity);
            $stmt->bindParam(":uom", $uom);
            $stmt->bindParam(":rate", $rate);
            $stmt->bindParam(":amount", $amount);

            if (!$stmt->execute()) {
                throw new Exception("Error inserting sales invoice item data.");
            }
        }

        // Log the update in the audit trail
        $logQuery = "INSERT INTO audit_trail (tableName, recordID, action, userID, details) VALUES (:tableName, :recordID, :action, :userID, :details)";
        $logStmt = $db->prepare($logQuery);

        $tableName = "sales_invoice";
        $recordID = $invoiceID;
        $action = "update";
        $userID = $_SESSION['SESS_MEMBER_ID'];
        $details = "Sales Invoice updated: Invoice No. $invoiceNo";

        $logStmt->bindParam(":tableName", $tableName);
        $logStmt->bindParam(":recordID", $recordID);
        $logStmt->bindParam(":action", $action);
        $logStmt->bindParam(":userID", $userID);
        $logStmt->bindParam(":details", $details);

        if (!$logStmt->execute()) {
            throw new Exception("Error logging audit trail.");
        }
        // After the try-catch block
        if ($db->commit()) {
            // The sales invoice and its items are successfully updated in the database
            echo json_encode(["status" => "success", "message" => "Invoice updated!"]);
        } else {
            throw new Exception("Error committing the transaction.");
        }
    } catch (Exception $e) {
        // Rollback the transaction on exception
        $db->rollBack();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
    }
}
?>
