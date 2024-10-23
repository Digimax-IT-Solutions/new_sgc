<?php

//Guard
require_once '_guards.php';
Guard::guestOnly();


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEW SGC</title>
    <link rel="shortcut icon" href="photos/logo.png">
    <!-- Add Bootstrap CSS link -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- Add Ionicons CSS link -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Montserrat', sans-serif;
        }

        body {

            /* Update the path as needed */
            background-size: cover;
            /* Ensures the image covers the entire container */
            background-position: center;
            /* Centers the image */
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            height: 100vh;
            position: relative;
            /* Necessary for the pseudo-element */
            height: 100vh;
            overflow: hidden;
            /* Prevents scroll bars */
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;

            background-size: cover;
            background-position: center;
            filter: blur(8px);
            /* Adjust the blur radius as needed */
            z-index: -1;
            /* Ensures it stays behind other content */
        }

        body>* {
            position: relative;
            /* Ensures child elements are on top of the blurred background */
            z-index: 1;
            /* Stays above the blurred background */
            display: flex;
            /* Retain your layout */
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        .container {
            background-color: #fff;
            border-radius: 30px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.35);
            position: relative;
            overflow: hidden;
            width: 350px;
            max-width: 100%;
            min-height: 480px;
        }




        .container button {
            background-color: #7C0F28;
            color: #fff;
            font-size: 12px;
            padding: 10px 45px;
            border: 1px solid transparent;
            border-radius: 8px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 10px;
            cursor: pointer;
        }



        .container form {
            background-color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            padding: 0 40px;
            height: 100%;
        }

        .container input {
            background-color: #eee;
            border: none;
            margin: 8px 0;
            padding: 10px 15px;
            font-size: 13px;
            border-radius: 8px;
            width: 100%;
            outline: none;
        }
    </style>
</head>

<body>
    <div class="container" id="container">
        <form method="POST" action="api/login_controller.php">
            <img class="image-fluid" src="photos/banner.png" style="height: 200px; width: 250px;" alt="">
            <br>
            <input type="text" name="username" placeholder="Username" required="true" />
            <input type="password" name="password" placeholder="Password" required="true" />

            <button type="submit">Sign In</button>
        </form>
    </div>
    <!-- Add Bootstrap JS and Popper.js scripts -->
    <script src="script.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>