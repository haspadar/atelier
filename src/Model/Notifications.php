<?php

namespace Atelier\Model;

use Atelier\Check;

class Notifications extends Model
{
    protected string $name = 'notifications';

    /**
     * @param string $contact
     * @param string $fromTime
     * @return Check[]
     */
    public function getActualChecks(string $contact, string $fromTime): array
    {
        return array_map(fn(array $check) => new Check($check), self::getDb()->query(
            "SELECT * FROM checks WHERE id NOT IN(SELECT check_id FROM notifications WHERE contact = %s AND create_time >= %s) AND ignore_time IS NULL",
            $contact,
            $fromTime
        )) ?: [];
    }
}