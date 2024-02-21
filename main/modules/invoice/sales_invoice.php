<?php
/*
    Sales Invoice Submission Script

    This script processes the submission of sales invoices in an accounting system. It performs the following tasks:
    - Initiates a session for user authentication.
    - Connects to the database using the included connection script.
    - Validates and sanitizes form data submitted via POST method.
    - Begins a transaction to ensure atomicity and consistency in database operations.

    Sales Invoice Data Insertion:
    - Inserts sales invoice data into the 'sales_invoice' table.
    - Retrieves the inserted sales invoice ID for reference.

    Item Data Insertion:
    - Iterates over each item in the invoice and inserts item data into the 'sales_invoice_items' table.
    - Updates the quantity of items in the 'items' table.

    Database Transaction Commit:
    - Commits the database transaction if all operations are successful.

    Audit Trail Logging:
    - Logs the creation of the sales invoice in the 'audit_trail' table for tracking changes.

    Customer Existence Check and Insertion:
    - Checks if the customer exists in the 'customers' table. If not, inserts customer information.

    Exception Handling:
    - Rolls back the transaction and outputs an error message in case of any exceptions.

    Note: Ensure proper validation and error handling are implemented for production use.
*/

// Start session for user authentication
session_start();

// Include the database connection script
include('../connect.php');

// Check if the form is submitted using POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Start a database transaction
        $db->beginTransaction();

        // Retrieve and sanitize form data
        $invoiceNo = $_POST["invoiceNo"];
        $invoiceDate = $_POST["invoiceDate"];
        $invoiceDueDate = $_POST["invoiceDueDate"];
        $invoiceBusinessStyle = $_POST["invoiceBusinessStyle"];
        $invoiceTin = $_POST["invoiceTin"];
        $invoicePo = $_POST["invoicePo"];
        $customer = $_POST["customer"];
        $customerID = $_POST["existingCustomer"];
        $address = $_POST["address"];
        $shippingAddress = $_POST["shippingAddress"];
        $accountID = $_POST["selectAccount"];
        $termID = $_POST["terms"];
        $locationID = $_POST["location"];
        $memo = $_POST["memo"];
        $paymentMethod = $_POST["paymentMethod"];
        $grossAmount = $_POST["grossAmount"];
        $discountPercentage = isset($_POST["discountPercentage"]) ? $_POST["discountPercentage"] : 0;
        $netAmountDue = $_POST["netAmountDue"];
        $vatPercentage = $_POST["vatPercentage"];
        $netOfVat = $_POST["netOfVat"];
        $taxWithheldPercentage = $_POST["taxWithheldPercentage"];
        $totalAmountDue = $_POST["totalAmountDue"];
        $status = "active";
        // Validate the discountPercentage value (you may add additional validation if needed)
        $discountPercentage = is_numeric($discountPercentage) ? $discountPercentage : 0;

        // Insert sales invoice data into the database
        $query = "INSERT INTO sales_invoice (
            invoiceNo, 
            invoiceDate, 
            invoiceDueDate,
            invoiceBusinessStyle,
            invoiceTin,
            invoicePo, 
            customer, 
            address, 
            shippingAddress,
            account, 
            terms, 
            location, 
            paymentMethod, 
            grossAmount, 
            discountPercentage, 
            netAmountDue, 
            vatPercentage, 
            netOfVat, 
            taxWithheldPercentage, 
            memo,
            totalAmountDue, 
            status,
            created_at
        ) VALUES (
            :invoiceNo, 
            :invoiceDate, 
            :invoiceDueDate,
            :invoiceBusinessStyle,
            :invoiceTin,
            :invoicePo, 
            :customer, 
            :shippingAddress,
            :address, 
            :account, 
            :terms, 
            :location, 
            :paymentMethod, 
            :grossAmount, 
            :discountPercentage, 
            :netAmountDue, 
            :vatPercentage, 
            :netOfVat, 
            :taxWithheldPercentage, 
            :memo,
            :totalAmountDue, 
            :status,
            CURRENT_TIMESTAMP
        )";

        // Prepare the SQL statement
        $stmt = $db->prepare($query);

        // Bind parameters for the sales invoice insertion
        $stmt->bindParam(":invoiceNo", $invoiceNo);
        $stmt->bindParam(":invoiceDate", $invoiceDate);
        $stmt->bindParam(":invoiceDueDate", $invoiceDueDate);
        $stmt->bindParam(":invoiceBusinessStyle", $invoiceBusinessStyle);
        $stmt->bindParam(":invoiceTin", $invoiceTin);
        $stmt->bindParam(":invoicePo", $invoicePo);
        $stmt->bindParam(":customer", $customer);  // <-- Replace :customerID with :customer
        $stmt->bindParam(":address", $address);
        $stmt->bindParam(":shippingAddress", $shippingAddress);
        $stmt->bindParam(":account", $accountID);   // <-- Replace :accountID with :account
        $stmt->bindParam(":terms", $termID);         // <-- Replace :termID with :term
        $stmt->bindParam(":location", $locationID); // <-- Replace :locationID with :location
        $stmt->bindParam(":paymentMethod", $paymentMethod);
        $stmt->bindParam(":grossAmount", $grossAmount);
        $stmt->bindParam(":discountPercentage", $discountPercentage);
        $stmt->bindParam(":netAmountDue", $netAmountDue);
        $stmt->bindParam(":vatPercentage", $vatPercentage);
        $stmt->bindParam(":netOfVat", $netOfVat);
        $stmt->bindParam(":taxWithheldPercentage", $taxWithheldPercentage);
        $stmt->bindParam(":memo", $memo);
        $stmt->bindParam(":totalAmountDue", $totalAmountDue);
        $stmt->bindParam(":status", $status);

        // Execute the sales invoice insertion
        if ($stmt->execute()) {
            $salesInvoiceID = $db->lastInsertId(); // Get the ID of the inserted sales invoice
         
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
                    :salesInvoiceID, 
                    :item, 
                    :description, 
                    :quantity, 
                    :uom,
                    :rate, 
                    :amount
                )";

                // Prepare the SQL statement for item insertion
                $stmt = $db->prepare($query);

                // Bind parameters for the item insertion
                $stmt->bindParam(":salesInvoiceID", $salesInvoiceID);
                $stmt->bindParam(":item", $item);
                $stmt->bindParam(":description", $description);
                $stmt->bindParam(":quantity", $quantity);
                $stmt->bindParam(":uom", $uom);
                $stmt->bindParam(":rate", $rate);
                $stmt->bindParam(":amount", $amount);

                // Execute the item insertion
                if (!$stmt->execute()) {
                    throw new Exception("Error inserting sales invoice item data.");
                }

                // Update item quantity in the items table
                $updateQuantityQuery = "UPDATE items SET itemQty = itemQty - :quantity WHERE itemName = :item";

                // Prepare the SQL statement for updating item quantity
                $updateQuantityStmt = $db->prepare($updateQuantityQuery);

                // Bind parameters for updating item quantity
                $updateQuantityStmt->bindParam(":quantity", $quantity);
                $updateQuantityStmt->bindParam(":item", $item);

                // Execute the update of item quantity
                if (!$updateQuantityStmt->execute()) {
                    throw new Exception("Error updating item quantity in the items table.");
                }
            }

            // Commit the database transaction
            $db->commit();

            // Log the invoice creation in the audit trail
            $logQuery = "INSERT INTO audit_trail (tableName, recordID, action, userID, details) VALUES (:tableName, :recordID, :action, :userID, :details)";
            $logStmt = $db->prepare($logQuery);

            // Define audit trail details
            $tableName = "sales_invoice";
            $recordID = $salesInvoiceID; // Assuming $salesInvoiceID is available
            $action = "create";
            $userID = $_SESSION['SESS_MEMBER_ID'];
            $details = "Sales Invoice created: Invoice No. $invoiceNo";

            // Bind parameters for the audit trail insertion
            $logStmt->bindParam(":tableName", $tableName);
            $logStmt->bindParam(":recordID", $recordID);
            $logStmt->bindParam(":action", $action);
            $logStmt->bindParam(":userID", $userID);
            $logStmt->bindParam(":details", $details);

            // Execute the audit trail insertion
            if (!$logStmt->execute()) {
                throw new Exception("Error logging audit trail.");
            }

            // The sales invoice, its items, and item quantities are successfully updated in the database
            echo "Sales invoice submitted!";
        } else {
            // Handle the case where the insertion failed
            throw new Exception("Error inserting sales invoice data.");
        }

        // Check if the customer exists in the customers table
        $checkCustomerQuery = "SELECT customerID FROM customers WHERE customerID = :customerID";
        $checkCustomerStmt = $db->prepare($checkCustomerQuery);
        $checkCustomerStmt->bindParam(":customerID", $customerID);

        // Execute the customer existence check
        if (!$checkCustomerStmt->execute()) {
            throw new Exception("Error checking customer existence.");
        }

        // If the customer doesn't exist, insert customer information into the customers table
        if ($checkCustomerStmt->rowCount() == 0) {
            $insertCustomerQuery = "INSERT INTO customers (customerName, customerBillingAddress, customerShippingAddress, customerTin) VALUES (:customer, :address, :shippingAddress, :customerTin)";
            $insertCustomerStmt = $db->prepare($insertCustomerQuery);
            $insertCustomerStmt->bindParam(":customer", $customer);
            $insertCustomerStmt->bindParam(":address", $address);
            $insertCustomerStmt->bindParam(":shippingAddress", $shippingAddress);
            $insertCustomerStmt->bindParam(":customerTin", $customerTin);

            // Execute the customer insertion
            if (!$insertCustomerStmt->execute()) {
                throw new Exception("Error inserting customer data.");
            }
        }

        // Retrieve the customer's balance from the database
$getCustomerBalanceQuery = "SELECT customerBalance FROM customers WHERE customerID = :customerID";

// Prepare the SQL statement for retrieving the customer's balance
$getCustomerBalanceStmt = $db->prepare($getCustomerBalanceQuery);

// Bind parameters for retrieving the customer's balance
$getCustomerBalanceStmt->bindParam(":customerID", $customerID);

// Execute the retrieval of the customer's balance
if (!$getCustomerBalanceStmt->execute()) {
    throw new Exception("Error retrieving customer balance from the customers table.");
}

// Fetch the customer's balance
$customerBalance = $getCustomerBalanceStmt->fetchColumn();

// Calculate the new balance
$newBalance = $totalAmountDue + $customerBalance;

// Update the customer's balance in the customers table
$updateCustomerBalanceQuery = "UPDATE customers SET customerBalance = :newBalance WHERE customerID = :customerID";

// Prepare the SQL statement for updating the customer's balance
$updateCustomerBalanceStmt = $db->prepare($updateCustomerBalanceQuery);

// Bind parameters for updating the customer's balance
$updateCustomerBalanceStmt->bindParam(":newBalance", $newBalance);
$updateCustomerBalanceStmt->bindParam(":customerID", $customerID);

// Execute the update of the customer's balance
if (!$updateCustomerBalanceStmt->execute()) {
    throw new Exception("Error updating customer balance in the customers table.");
}

    } catch (Exception $e) {
        // Rollback the database transaction on exception
        $db->rollBack();

        // Output the error message
        echo "Transaction failed: " . $e->getMessage();
    }
}
?>
