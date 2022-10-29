<?php

use Atelier\Logger;
use Atelier\Message\Type;
use Atelier\Settings;
use Atelier\Telegram;
use Longman\TelegramBot\Request;

require_once '../../vendor/autoload.php';
date_default_timezone_set('Europe/Minsk');

$bot = new Telegram(Settings::getByName('telegram_token'));
//Logger::info('Debug: ' . var_export($bot->getUpdate()->getInput(), true));
if ($bot->isMessage()) {
//    $bot->sendMessageWithBaseButtons('Привет, ' . $bot->getFromFirstName() . '. Какие уведомления хочешь получать?', array(
//        array(
//            array(
//                "text" => "888"
//            ),
//            array(
//                "text" => "999"
//            ),
//        )
//    ));
    $bot->sendMessageWithInlineButtons(
        'Привет, ' . $bot->getFromFirstName() . '. Какие уведомления хочешь получать?', [
            ['text'=>'Только важные', 'callback_data' => Type::CRITICAL->name],
            ['text'=>'Предупреждения', 'callback_data' => Type::WARNING->name],
            ['text'=>'Рекомендации', 'callback_data' => Type::INFO->name],
        ]
    );
} elseif ($bot->getClickedInlineButton() == Type::CRITICAL->name) {
    $bot->sendMessage('Важные уведомления отправляются раз в сутки с 09:00 до 22:00');
} elseif ($bot->getClickedInlineButton() == Type::WARNING->name) {
    $bot->sendMessage('Предупреждения отправляются раз в неделею с 09:00 до 22:00');
}  elseif ($bot->getClickedInlineButton() == Type::INFO->name) {
    $bot->sendMessage('Предупреждения отправляются раз в месяц с 09:00 до 22:00');
}

//
//
//Logger::info('Ping');
//try {
//    $telegram = new Longman\TelegramBot\Telegram(
//        Settings::getByName('telegram_token'),
//        Settings::getByName('telegram_bot')
//    );
//    $result = $telegram->setWebhook(Settings::getByName('telegram_webhook'));
//    if ($result->isOk()) {
//        $input = Request::getInput();
//        $post = json_decode($input, true);
//        Logger::info('Json: ' . $input);
//    }
//} catch (Longman\TelegramBot\Exception\TelegramException $e) {
//    Logger::error($e->getMessage());
//}

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