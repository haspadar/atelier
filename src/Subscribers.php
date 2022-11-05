<?php

namespace Atelier;

use Atelier\Check\Type;
use DateTime;

class Subscribers
{
    const CRITICAL_TITLE = 'Срочные';
    const WARNING_TITLE = 'Важные';
    const ALL_TITLE = 'Все';

    public static function remove(int $chatId)
    {
        $model = new Model\Subscribers();
        $found = $model->getBy('chat_id', $chatId);
        if ($found) {
            $model->remove($found['id']);
            Logger::warning('Subscriber removed');
        } else {
            Logger::warning('Subscriber for remove not found');
        }
    }

    public static function add(array $subscriber)
    {
        $model = new Model\Subscribers();
        $subscriber['create_time'] = (new DateTime())->format('Y-m-d H:i:s');
        if ($found = $model->getBy('chat_id', $subscriber['chat_id'])) {
            $model->update($subscriber, $found['id']);
            Logger::warning('Subscriber updated');
        } else {
            $model->add($subscriber);
            Logger::warning('Subscriber added');
        }
    }

    public static function getAll(): array
    {
        return (new Model\Subscribers())->getAll();
    }

    public static function getByChatId(string $chatId)
    {
        return (new Model\Subscribers())->getBy('chat_id', $chatId);
    }

    public static function getCheckTypesTitle(string $checkTypes): string
    {
        if ($checkTypes == Type::CRITICAL->name) {
            return self::CRITICAL_TITLE;
        }

        if ($checkTypes == Type::WARNING->name) {
            return self::WARNING_TITLE;
        }

        if ($checkTypes == Type::INFO->name) {
            return self::ALL_TITLE;
        }

        return '';
    }
}