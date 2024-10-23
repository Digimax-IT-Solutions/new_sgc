<?php

require_once __DIR__ . '/../_init.php';

// Send a message to Discord with the user info
$discordMessage = "**SYSTEM LOG:** " . $_SESSION['name'] . " has just logged out of the system!!\n"; // Bold username and action
sendToDiscord($discordMessage); // Send the message to Discord

unset($_SESSION['user_id']);


redirect('../login.php');