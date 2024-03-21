<?php
// Include connect.php to establish database connection
include('../connect.php');

// Assuming $_POST['customerName'] contains the selected customer's name
$customerName = $_POST['customerName'];

// Prepare the SQL query to fetch credits for the selected customer
$query = "SELECT * FROM credits WHERE customerName = :customerName AND status = 'active'";

try {
    // Prepare and execute the statement
    $statement = $db->prepare($query);  
    $statement->bindParam(':customerName', $customerName);
    $statement->execute();

    // Fetch all rows as an associative array
    $credits = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Return the credits data as JSON
    echo json_encode($credits);
} catch (PDOException $e) {
    // Handle the exception, log the error, or return an error message with MySQL error information
    echo json_encode(['error' => $e->getMessage()]);
}
?>