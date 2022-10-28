<?php
require_once '../../vendor/autoload.php';
date_default_timezone_set('Europe/Minsk');

$basicChatData = json_decode(file_get_contents("php://input"),true);
$chatId = $basicChatData['message']['chat']['id'];
$message = $basicChatData['message']['text'] ?? '';
$response = send($message . ': Hello my friend!. debug:' . var_export($basicChatData, true), $chatId);

function send(string $message, string $chatId) {
    $token = \Atelier\Settings::getByName('telegram_token');
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot$token/sendMessage");
    curl_setopt($ch, CURLOPT_POSTFIELDS, "https://api.telegram.org/bot$token/sendMessage");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        'text' => $message,
        'chat_id' => $chatId
    ]));

    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    \Atelier\Debug::dump($info, '$info');

    return $response;
}