<?php

require_once __DIR__ . '/../_init.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = post('username');
    $password = post('password');

    try {
        $user = User::login($username, $password);
        $_SESSION['user_id'] = $user->id;
        $_SESSION['name'] = $user->name;
        $_SESSION['user_name'] = $user->username;
        redirect('../' . $user->getHomePage());
    } catch (Exception $error) {
        flashMessage('login', $error->getMessage(), FLASH_ERROR);
        redirect('../login.php');
    }
}
