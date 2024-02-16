<?php
// Include your database connection file
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Validate and sanitize input data
    $name = $_POST['name'];
    $position = $_POST['position'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];

    // Insert category details into the database
    $query = "INSERT INTO user (name, position, username, password, email) VALUES (:name, :position, :username, :password, :email)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':position', $position);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':email', $email);

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error saving user: " . $stmt->errorInfo()[2];
    }
} else {
    echo "Invalid request.";
}

