<?php

// logout.php

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Clear cookies
setcookie(session_name(), "", time() - 3600, "/");

// Redirect to the login page through the router
header("location: router.php?page=index");
exit();
?>