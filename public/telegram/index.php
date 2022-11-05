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
        'check_types' => implode(',', [Type::CRITICAL->name])
    ]);
    $bot->sendText('Важные уведомления будут отправляться с 09:00 до 22:00');
} elseif ($bot->getClickedInlineButton() == Type::WARNING->name) {
    Subscribers::add([
        'chat_id' => $bot->getChatId(),
        'username' => $bot->getUsername(),
        'first_name' => $bot->getFirstName(),
        'check_types' => implode(',', [Type::CRITICAL->name, Type::WARNING->name])
    ]);
    $bot->sendText('Предупреждения будут отправляться с 09:00 до 22:00');
}  elseif ($bot->getClickedInlineButton() == Type::INFO->name) {
    Subscribers::add([
        'chat_id' => $bot->getChatId(),
        'username' => $bot->getUsername(),
        'first_name' => $bot->getFirstName(),
        'check_types' => implode(',', [Type::CRITICAL->name, Type::WARNING->name, Type::INFO->name])
    ]);
    $bot->sendText('Предупреждения будут отправляться с 09:00 до 22:00');
} elseif ($bot->isUnsubscribe($bot->getChatId())) {
    Subscribers::remove($bot->getChatId());
} elseif ($bot->isMessage()) {
    $bot->sendMessageWithInlineButtons(
        'Привет, ' . $bot->getFromFirstName() . '. Какие уведомления хочешь получать?', [
            ['text'=> '🔴 Срочные', 'callback_data' => Type::CRITICAL->name],
            ['text'=> '🔵 Важные', 'callback_data' => Type::WARNING->name],
            ['text'=> '⚪ Все', 'callback_data' => Type::INFO->name],
        ]
    );
}