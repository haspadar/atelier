<?php

use Atelier\Check\Type;
use Atelier\Subscribers;
use Atelier\Telegram;

require_once '../../vendor/autoload.php';
date_default_timezone_set('Europe/Minsk');

$bot = new Telegram();
if ($bot->getClickedInlineButton() == Type::CRITICAL->name) {
    Subscribers::add([
        'chat_id' => $bot->getChatId(),
        'username' => $bot->getUsername(),
        'first_name' => $bot->getFirstName(),
        'message_types' => implode(',', [Type::CRITICAL->name])
    ]);
    $bot->sendMessage('Важные уведомления будут отправляться с 09:00 до 22:00');
} elseif ($bot->getClickedInlineButton() == Type::WARNING->name) {
    Subscribers::add([
        'chat_id' => $bot->getChatId(),
        'username' => $bot->getUsername(),
        'first_name' => $bot->getFirstName(),
        'message_types' => implode(',', [Type::CRITICAL->name, Type::WARNING->name])
    ]);
    $bot->sendMessage('Предупреждения будут отправляться с 09:00 до 22:00');
}  elseif ($bot->getClickedInlineButton() == Type::INFO->name) {
    Subscribers::add([
        'chat_id' => $bot->getChatId(),
        'username' => $bot->getUsername(),
        'first_name' => $bot->getFirstName(),
        'message_types' => implode(',', [Type::CRITICAL->name, Type::WARNING->name, Type::INFO->name])
    ]);
    $bot->sendMessage('Предупреждения будут отправляться с 09:00 до 22:00');
} elseif ($bot->isUnsubscribe($bot->getChatId())) {
    Subscribers::remove($bot->getChatId());
} elseif ($bot->isMessage()) {
    $bot->sendMessageWithInlineButtons(
        'Привет, ' . $bot->getFromFirstName() . '. Какие уведомления хочешь получать?', [
            ['text'=> Type::CRITICAL->name, 'callback_data' => Type::CRITICAL->name],
            ['text'=> implode(',', [Type::CRITICAL->name, Type::WARNING->name]), 'callback_data' => Type::WARNING->name],
            ['text'=> implode(',', [Type::CRITICAL->name, Type::WARNING->name, Type::INFO->name]), 'callback_data' => Type::INFO->name],
        ]
    );
}