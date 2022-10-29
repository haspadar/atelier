<?php

namespace Atelier;

use DateTime;

class Subscribers
{
    public static function remove(int $chatId)
    {
        $model = new Model\Subscribers();
        $found = $model->getBy('chat_id', $chatId);
        $model->remove($found['id']);
        Logger::warning('Subscriber removed');
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
}