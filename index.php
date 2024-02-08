<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NEW SGC</title>
    <link rel="shortcut icon" href="images/sgc.png">
    <!-- Add Bootstrap CSS link -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- Add Ionicons CSS link -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" />
    <style>
    body {
        background-color: #f8f9fa;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #login {
        background-color: #ffffff;
        padding: 80px;
        border-radius: 25px;
        box-shadow: 0 0 10px rgb(228,46,45);
    }

    .custom-button {
        background-color: rgb(228,46,45);
        color: #fff;
        border: none;
    }

    .custom-button:hover {
        background-color: #00562e;
        color: white;
    }


    .input-group-text:focus-within {
        border-color: rgb(228,46,45);
        box-shadow: 0 0 0 0.9rem rgb(228,46,45);
    }

    .custom-input:focus {
        border-color: rgb(228,46,45);
        box-shadow: 0 0 0 0.1rem rgb(228,46,45);
    }
    </style>
</head>

<body>
    <div id="login">
        <form action="login.php" method="POST">
            <div class="text-center">
                <img src="images/sgc.png" style="height: 200px;" alt="">
            </div>

            <?php
            // Start session
            session_start();

            // Display error messages if any
            if (isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) > 0) {
                foreach ($_SESSION['ERRMSG_ARR'] as $msg) {
                    echo '<div style="color: red; text-align: center;">', $msg, '</div><br>';
                }
                unset($_SESSION['ERRMSG_ARR']);
            }
            ?>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <!-- Use Bootstrap Icons for the user icon -->
                        <i class="ion-person"></i>
                    </span>
                </div>
                <input type="text" class="form-control custom-input" name="username" placeholder="Username" required>
            </div>

            <div class="input-group mb-3">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <!-- Use Bootstrap Icons for the lock icon -->
                        <i class="ion-locked"></i>
                    </span>
                </div>
                <input type="password" class="form-control custom-input" name="password" placeholder="Password"
                    required>
            </div>

            <div class="text-center">
                <button class="btn btn-lg custom-button" type="submit">
                    <!-- Use Bootstrap Icons for the sign-in icon -->
                    <i class="ion-log-in"></i> Login
                </button>
            </div>
        </form>
    </div>

    <!-- Add Bootstrap JS and Popper.js scripts -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>