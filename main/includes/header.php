<?php



//Start session
session_start();

//Check whether the session variable SESS_MEMBER_ID is present or not
if (!isset($_SESSION['SESS_MEMBER_ID']) || (trim($_SESSION['SESS_MEMBER_ID']) == '')) {
    // Redirect to the login page through the router
    echo "<script>alert('Please log in.'); window.location.href='../router.php?page=index';</script>";
    exit();
}


?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NEW SGC</title>
    <link rel="icon" type="image/x-icon" href="../images/sgc.png">
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" />
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="../plugins/fontawesome-free/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="../dist/css/adminlte.min.css">
    <!-- SWEET ALERT 2 -->
    <link rel="stylesheet" href="../plugins/sweetalert2/sweetalert2.min.css">
    <script src="../plugins/sweetalert2/sweetalert2.all.min.js"></script>
    <!-- DATA TABLES -->
    <link rel="stylesheet" href="../plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
    <link rel="stylesheet" href="../plugins/datatables-buttons/css/buttons.bootstrap4.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Include Select2 CSS -->
    <link rel="stylesheet" href="../plugins/select2/css/select2.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">


    <script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.3.0/papaparse.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
        }

        a {
            color: rgb(0, 149, 77);
        }

        .breadcrumb {
            background-color: white;
        }

        .form-control:focus {
            border-color: rgb(228,46,45);
            box-shadow: inset 0 1px 1px rgba(228, 46, 45, 0.5), 0 0 8px rgba(228, 46, 45, 0.5), ;
        }

        th {

            background-color: grey;
            color: white;
        }
    </style>
</head>

<body class="hold-transition layout-fixed sidebar-mini">
    <div class="wrapper">

        <?php include 'navbar.php'; ?>
        <?php include 'sidebar.php'; ?>