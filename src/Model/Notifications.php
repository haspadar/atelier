<?php

namespace Atelier\Model;

class Notifications extends Model
{
    protected string $name = 'notifications';

    public function getActualMessages(string $contact, string $fromTime): array
    {
        return self::getDb()->query(
            "SELECT * FROM messages WHERE id NOT IN(SELECT message_id FROM notifications WHERE contact = %s AND create_time >= %s) AND ignore_time IS NULL",
            $contact,
            $fromTime
        ) ?: [];
    }
}