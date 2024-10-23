<?php
// index.php

// Include necessary files
require_once '_init.php';
require_once '_guards.php';
require_once 'models/User.php';

// Check if user is authenticated
$currentUser = User::getAuthenticatedUser();

if (!$currentUser) {
    // If no user is authenticated, redirect to login page
    redirect('login.php');
} else {
    // User is authenticated, redirect to their home page
    $homePage = $currentUser->getHomePage();
    redirect($homePage);
}

// // Helper function for redirection
// function redirect($page)
// {
//     header("Location: $page");
//     exit();
// }