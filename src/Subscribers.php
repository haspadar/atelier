<?php

namespace Atelier;

class Subscribers
{
    public static function remove(int $chatId)
    {
        $model = new \Atelier\Model\Subscribers();
        $found = $model->getBy('chat_id', $chatId);
        $model->remove($found['id']);
        Logger::warning('Subscriber removed');
    }

    public static function add(array $subscriber)
    {
        $model = new \Atelier\Model\Subscribers();
        $subscriber['create_time'] = (new \DateTime())->format('Y-m-d H:i:s');
        if ($found = $model->getBy('chat_id', $subscriber['chat_id'])) {
            $model->update($subscriber, $found['id']);
            Logger::warning('Subscriber updated');
        } else {
            $model->add($subscriber);
            Logger::warning('Subscriber added');
        }
    }
}