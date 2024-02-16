<?php
// Start session
session_start();

// Include database connection
require_once 'connect.php';

// Input Validations
$errmsg_arr = array();
$errflag = false;

// Updated clean function for PDO
function clean($pdo, $str)
{
    $stmt = $pdo->prepare("SELECT :str");
    $stmt->bindParam(':str', $str);
    $stmt->execute();
    return $stmt->fetchColumn();
}

// Sanitize the POST values
$login = clean($db, $_POST['username']);
$password = clean($db, $_POST['password']);

// Validate username
if ($login == '') {
    $errmsg_arr[] = 'Username missing';
    $errflag = true;
}

// Validate password
if ($password == '') {
    $errmsg_arr[] = 'Password missing';
    $errflag = true;
}

// If there are input validations, redirect back to the login form
if ($errflag) {
    $_SESSION['ERRMSG_ARR'] = $errmsg_arr;
    session_write_close();
    header("location: index.php");
    exit();
}

// Create and execute query
$qry = "SELECT * FROM user WHERE username='$login' AND password='$password' AND position='$_POST[position]'";
$result = $db->query($qry);

// Check whether the query was successful or not
if ($result) {
    if ($result->rowCount() > 0) {
        // Login Successful
        session_regenerate_id();
        $member = $result->fetch(PDO::FETCH_ASSOC);
        $_SESSION['SESS_MEMBER_ID'] = $member['id'];
        $_SESSION['SESS_FIRST_NAME'] = $member['name'];
        $_SESSION['SESS_POSITION'] = $member['position'];
        session_write_close();
        header("location: main/dashboard");
        exit();
    } else {
        // Login failed
        $_SESSION['ERRMSG_ARR'] = ['Invalid username or password or user type'];
        header("location: index.php");
        exit();
    }
} else {
    $_SESSION['ERRMSG_ARR'] = ['Query failed'];
    header("location: index.php");
    exit();
}
