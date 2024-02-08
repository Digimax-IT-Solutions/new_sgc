<?php

// auth.php

function checkSession() {
    // Start session
    session_start();

    // Check whether the session variable SESS_MEMBER_ID is present or not
    if (!isset($_SESSION['SESS_MEMBER_ID']) || (trim($_SESSION['SESS_MEMBER_ID']) == '')) {
        // Redirect to the login page through the router
        echo "<script>alert('Please log in.'); window.location.href='../router.php?page=index';</script>";
        exit();
    }
}


?>
