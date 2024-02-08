<?php
// Include your database connection file
include('../../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input data
    $itemID = $_POST['editItemID'];
    $itemCode = $_POST['editItemCode'];
    $itemName = $_POST['editItemName'];
    $editPreferredVendor = $_POST['editPreferredVendor'];
    $editReOrderPoint = $_POST['editReOrderPoint'];
    $editItemSalesInfo = $_POST['editItemSalesInfo'];
    $editItemSrp = $_POST['editItemSrp'];
    $editItemPurchaseInfo = $_POST['editItemPurchaseInfo'];
    $editItemCost = $_POST['editItemCost'];
    $editItemCategory = $_POST['editItemCategory'];
    $editUom = $_POST['editUom'];
    $editItemCogsAccount = $_POST['editItemCogsAccount'];
    $editItemIncomeAccount = $_POST['editItemIncomeAccount'];
    $editItemAssetsAccount = $_POST['editItemAssetsAccount'];

    // Validate required fields
    $errors = array();

    if (empty($itemCode)) {
        $errors[] = "Item Code is required.";
    }

    if (empty($itemName)) {
        $errors[] = "Item Name is required.";
    }

    // If there are validation errors, display them to the user
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "ERROR UPDATING: " . $error;
        }
    } else {
        // Update item details in the database
        $query = "UPDATE items 
                  SET itemCode = :itemCode, 
                      itemName = :itemName, 
                      preferredVendor = :editPreferredVendor, 
                      reOrderPoint = :editReOrderPoint, 
                      itemSalesInfo = :editItemSalesInfo, 
                      itemSrp = :editItemSrp, 
                      itemPurchaseInfo = :editItemPurchaseInfo, 
                      itemCost = :editItemCost, 
                      itemCategory = :editItemCategory, 
                      itemCogsAccount = :editItemCogsAccount, 
                      itemIncomeAccount = :editItemIncomeAccount, 
                      itemAssetsAccount = :editItemAssetsAccount,
                      uom = :editUom
                  WHERE itemID = :itemID";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':itemID', $itemID);
        $stmt->bindParam(':itemCode', $itemCode);
        $stmt->bindParam(':itemName', $itemName);
        $stmt->bindParam(':editPreferredVendor', $editPreferredVendor);
        $stmt->bindParam(':editReOrderPoint', $editReOrderPoint);
        $stmt->bindParam(':editItemSalesInfo', $editItemSalesInfo);
        $stmt->bindParam(':editItemSrp', $editItemSrp);
        $stmt->bindParam(':editItemPurchaseInfo', $editItemPurchaseInfo);
        $stmt->bindParam(':editItemCost', $editItemCost);
        $stmt->bindParam(':editItemCategory', $editItemCategory);
        $stmt->bindParam(':editUom', $editUom);
        $stmt->bindParam(':editItemCogsAccount', $editItemCogsAccount);
        $stmt->bindParam(':editItemIncomeAccount', $editItemIncomeAccount);
        $stmt->bindParam(':editItemAssetsAccount', $editItemAssetsAccount);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Error updating item: " . $stmt->errorInfo()[2];
        }
    }
} else {
    echo "Invalid request.";
}
?>
