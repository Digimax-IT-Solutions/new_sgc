<?php
// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get the credit updates from the POST data
    $creditUpdates = json_decode($_POST['creditUpdates'], true);

    // Include your database connection file
    include('../connect.php');

    // Begin a transaction
    $db->beginTransaction();

    try {
        // Update the credits table
        foreach ($creditUpdates as $update) {
            $creditID = $update['id'];
            $creditUsed = $update['used'];

            // Perform the update operation (replace "credits" with your actual table name)
            $query = "UPDATE credits 
                      SET creditUsed = creditUsed + :creditUsed,
                          creditBalance = creditBalance - :creditUsed,
                          status = CASE 
                                      WHEN creditBalance <= 0 THEN 'deactivated' 
                                      ELSE status 
                                  END
                      WHERE ID = :creditID";

            // Prepare and execute the query
            $stmt = $db->prepare($query);
            $stmt->bindParam(':creditUsed', $creditUsed, PDO::PARAM_INT);
            $stmt->bindParam(':creditID', $creditID, PDO::PARAM_INT);
            $stmt->execute();
        }

        // Commit the transaction
        $db->commit();
        echo "Credits updated successfully";
    } catch (PDOException $e) {
        // Rollback the transaction in case of an error
        $db->rollback();
        echo "Error: " . $e->getMessage();
    } catch (Exception $ex) {
        // Rollback the transaction in case of an error
        $db->rollback();
        echo "Error: " . $ex->getMessage();
    }
} else {
    // If the request method is not POST, return an error message
    echo "Invalid request.";
}
?>
