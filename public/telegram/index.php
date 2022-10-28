<?php

use Atelier\Model\Model;
use Atelier\Settings;

require_once '../../vendor/autoload.php';
date_default_timezone_set('Europe/Minsk');

try {
    $telegram = new Longman\TelegramBot\Telegram(
        Settings::getByName('telegram_token'),
        Settings::getByName('telegram_bot')
    );
    $telegram->enableMySql([
        'host' => Model::getDb()->host,
        'port' => Model::getDb()->port,
        'user' => Model::getDb()->user,
        'password' => Model::getDb()->password,
        'database' => Model::getDb()->dbName,
    ]);
    $result = $telegram->setWebhook(Settings::getByName('telegram_webhook'));
    if ($result->isOk()) {
        echo $result->getDescription();
    }

    $telegram->handle();
} catch (Longman\TelegramBot\Exception\TelegramException $e) {
    // Log telegram errors
    Longman\TelegramBot\TelegramLog::error($e);
    \Atelier\Logger::error(var_export([
        'host' => Model::getDb()->host,
        'port' => Model::getDb()->port,
        'user' => Model::getDb()->user,
        'password' => Model::getDb()->password,
        'database' => Model::getDb()->dbName,
    ], true));
    \Atelier\Logger::error($e->getMessage());

    // Uncomment this to output any errors (ONLY FOR DEVELOPMENT!)
     echo $e;
}

//
//
//$basicChatData = json_decode(file_get_contents("php://input"), true);
//if (isset($basicChatData['message'])) {
//    $chatId = $basicChatData['message']['chat']['id'];
//    $message = $basicChatData['message']['text'] ?? '';
//    $response = send($message . ': Hello my friend!. debug:' . var_export($basicChatData, true), $chatId);
//}
//
//function send(string $message, string $chatId)
//{
//    $token = Settings::getByName('telegram_token');
//    $ch = curl_init();
//    curl_setopt($ch, CURLOPT_URL, "https://api.telegram.org/bot$token/sendMessage");
//    curl_setopt($ch, CURLOPT_POSTFIELDS, "https://api.telegram.org/bot$token/sendMessage");
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
//    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
//        'text' => $message,
//        'chat_id' => $chatId
//    ]));
//
//    $response = curl_exec($ch);
//    $info = curl_getinfo($ch);
//    \Atelier\Debug::dump($info, '$info');
//
//    return $response;
//}