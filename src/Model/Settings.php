<?php

namespace Atelier\Model;

class Settings extends Model
{
    protected string $name = 'settings';

    public function getAll(): array
    {
        return self::getDb()->query("SELECT * FROM settings ORDER BY `group`") ?: [];
    }

    public function getByName(string $name)
    {
        return self::getDb()->queryFirstRow("SELECT * FROM settings WHERE name=%s", $name) ?: [];
    }

    public function getByGroup(string $group)
    {
        return self::getDb()->query("SELECT * FROM settings WHERE `group` = %s", $group) ?: [];
    }

    public function updateByName(string $name, string $value)
    {
        self::getDb()->update($this->name, ['value' => $value], 'name = %s', $name);
    }
}