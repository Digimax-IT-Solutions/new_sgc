<?php

function dd($data)
{
    // header('Content-type: application/json');
    echo json_encode($data);
    die();
}
function get($key)
{
    if (isset($_GET[$key]))
        return trim($_GET[$key]);
    return "";
}
function post($key)
{
    if (isset($_POST[$key])) {
        return trim($_POST[$key]);
    }
    return "";
}
function redirect($location)
{
    header("Location: $location");
    die();
}
function flashMessage($name, $message, $type)
{

    // remove existing message with the name
    if (isset($_SESSION[FLASH][$name])) {
        unset($_SESSION[FLASH][$name]);
    }

    $_SESSION[FLASH][$name] = ['message' => $message, 'type' => $type];
}
function formattedFlashMessage($flashMessage)
{
    return sprintf(
        "<div class='alert alert-%s'>%s</div>",
        $flashMessage['type'],
        $flashMessage['message']
    );
}
function displayFlashMessage($name)
{

    if (!isset($_SESSION[FLASH][$name]))
        return;

    $flashMessage = $_SESSION[FLASH][$name];

    unset($_SESSION[FLASH][$name]);

    echo formattedFlashMessage($flashMessage);
}
// Function to format a number as Philippine Peso (PHP)
function formatAsPhilippinePeso($value)
{
    return '₱ ' . number_format($value, 2);
}
// Function to send data to Discord Webhook
function sendToDiscord($data)
{
    $webhook_url = DISCORD_WEBHOOK_URL;
    $headers = ['Content-Type: application/json; charset=utf-8'];

    // Split message if it's too long
    $messages = str_split($data, 1950); // Discord's limit is 2000, we leave some room

    foreach ($messages as $index => $message) {
        if ($index > 0) {
            $message = "\n" . $message; // Add newline for continuation messages
        }
        if ($index < count($messages) - 1) {
            $message .= "\n"; // Add newline for messages that will be continued
        }

        $POST = ['content' => $message];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $webhook_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($POST));
        $response = curl_exec($ch);

        if ($response === false) {
            error_log("Failed to send part of the message to Discord");
            return false;
        }

        // Add a small delay to avoid rate limiting
        usleep(500000); // 0.5 second delay
    }

    return true;
}
