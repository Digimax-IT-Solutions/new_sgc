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

        // Send a message to Discord with the user info
        $discordMessage = "**SYSTEM LOG:** " . $_SESSION['name'] . " has just logged back into the system!\n"; // Bold username and action
        sendToDiscord($discordMessage); // Send the message to Discord


        redirect('../' . $user->getHomePage());
    } catch (Exception $error) {
        flashMessage('login', $error->getMessage(), FLASH_ERROR);
        redirect('../login.php');
    }
}
