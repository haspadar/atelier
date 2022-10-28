<?php
$basicChatData = json_decode(file_get_contents("php://input"),true);
$chatId = $basicChatData['message']['chat']['id'];
$response = send('Hello my friend!', $chatId);
\Atelier\Debug::dump($response, '$response');

function send(string $message, string $chatId) {
    $token = '5734498019:AAF3N-QeaLrmmumryE3_z8IzjhlcOvWfggQ';
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