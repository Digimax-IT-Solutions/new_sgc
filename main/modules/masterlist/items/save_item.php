<?php
// Include your database connection file
include('../../connect.php');

try {
    // Get form data
    $itemCode = $_POST['itemCode'];
    $itemName = $_POST['itemName'];
    $itemType = $_POST['itemType'];
    $preferredVendor = $_POST['preferredVendor']; // Added this line for the missing field
    $reOrderPoint = empty($_POST['reOrderPoint']) ? 0 : $_POST['reOrderPoint'];
    $itemSalesInfo = $_POST['itemSalesInfo'];
    $itemSrp = empty($_POST['itemSrp']) ? 0 : $_POST['itemSrp'];
    $itemPurchaseInfo = $_POST['itemPurchaseInfo'];
    $itemCost = empty($_POST['itemCost']) ? 0 : $_POST['itemCost'];
    $itemCategory = $_POST['itemCategory'];
    $uom = $_POST['uom'];
    $itemCogsAccount = $_POST['itemCogsAccount'];
    $itemIncomeAccount = $_POST['itemIncomeAccount'];
    $itemAssetsAccount = $_POST['itemAssetsAccount'];

    // Validate required fields
    $errors = array();

    if (empty($itemCode)) {
        $errors[] = "Item Code is required.";
    }

    if (empty($itemName)) {
        $errors[] = "Item Name is required.";
    }

    // Check if item code already exists
    $stmtCheckCode = $db->prepare('SELECT COUNT(*) FROM items WHERE itemCode = :itemCode');
    $stmtCheckCode->bindParam(':itemCode', $itemCode);
    $stmtCheckCode->execute();

    if ($stmtCheckCode->fetchColumn() > 0) {
        $errors[] = "Item Code already exists.";
    }

    // Check if item name already exists
    $stmtCheckName = $db->prepare('SELECT COUNT(*) FROM items WHERE itemName = :itemName');
    $stmtCheckName->bindParam(':itemName', $itemName);
    $stmtCheckName->execute();

    if ($stmtCheckName->fetchColumn() > 0) {
        $errors[] = "Item Name already exists.";
    }

    // If there are validation errors, display them to the user
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "error: " . $error;
        }
    } else {
        // Insert data into the items table
        $query = "INSERT INTO items (itemCode, itemName, itemType, preferredVendor, reOrderPoint, itemSalesInfo, itemSrp, itemPurchaseInfo, itemCost, itemCategory, itemCogsAccount, itemIncomeAccount, itemAssetsAccount, uom) VALUES (:itemCode, :itemName, :itemType, :preferredVendor, :reOrderPoint, :itemSalesInfo, :itemSrp, :itemPurchaseInfo, :itemCost, :itemCategory, :itemCogsAccount, :itemIncomeAccount, :itemAssetsAccount, :uom)";
        $stmt = $db->prepare($query);

        $stmt->bindParam(':itemCode', $itemCode);
        $stmt->bindParam(':itemName', $itemName);
        $stmt->bindParam(':itemType', $itemType);
        $stmt->bindParam(':preferredVendor', $preferredVendor);
        $stmt->bindParam(':reOrderPoint', $reOrderPoint);
        $stmt->bindParam(':itemSalesInfo', $itemSalesInfo);
        $stmt->bindParam(':itemSrp', $itemSrp);
        $stmt->bindParam(':itemPurchaseInfo', $itemPurchaseInfo);
        $stmt->bindParam(':itemCost', $itemCost);
        $stmt->bindParam(':itemCategory', $itemCategory);
        $stmt->bindParam(':uom', $uom);
        $stmt->bindParam(':itemCogsAccount', $itemCogsAccount);
        $stmt->bindParam(':itemIncomeAccount', $itemIncomeAccount);
        $stmt->bindParam(':itemAssetsAccount', $itemAssetsAccount);

        $result = $stmt->execute();

        if ($result) {
            echo "success";
        } else {
            // Retrieve the specific MySQL error message
            $errorMessage = $stmt->errorInfo()[2];

            // Echo the error message to the client
            echo "error: " . $errorMessage;
        }
    }
} catch (PDOException $e) {
    // Log the exception message and SQL error to the error log
    error_log("PDOException: " . $e->getMessage());
    error_log("SQL Error: " . $e->errorInfo[2]);

    // Echo the SQL error message to the client
    echo "error: " . $e->errorInfo[2];
}
?>