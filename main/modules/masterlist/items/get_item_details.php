<?php
// Include your database connection file
include('../../connect.php');

try {
    // Check if the itemID parameter is set
    if (isset($_GET['itemID'])) {
        // Fetch details for the specific item
        $itemID = $_GET['itemID'];
        $query = "SELECT * FROM items WHERE itemID = :itemID";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':itemID', $itemID, PDO::PARAM_INT);
        $stmt->execute();
        $itemDetails = $stmt->fetch(PDO::FETCH_ASSOC);

        // Output item details as JSON
        echo json_encode($itemDetails);
    } else {
        // Fetch all items from the items table
        $query = "SELECT * FROM items";
        $stmt = $db->query($query);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Output items as JSON
        echo json_encode($items);
    }
} catch (PDOException $e) {
    // Log the exception message to the error log
    error_log("PDOException: " . $e->getMessage());
    echo json_encode([]); // Return an empty array in case of an error
}
