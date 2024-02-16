<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Validate and sanitize input data
    $id = $_POST['id'];
    $name = $_POST['name'];
    $position = $_POST['position'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Update category details in the database
    $query = "UPDATE user 
                SET name = :name,
                position = :position, 
                username = :username,
                password = :password,
                email = :email
                WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':position', $position);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':email', $email);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error updating uom method: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}
?>
