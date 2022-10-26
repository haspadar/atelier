<?php

namespace Atelier\Model;

class Machines extends Model
{
    protected string $name = 'machines';

    public function getAll(array $ids): array
    {
        return self::getDb()->query('SELECT * FROM ' . $this->name . ($ids ? ' WHERE id IN %ld' : ''), $ids) ?: [];
    }

    public function getLastPhpVersion(): string
    {
        $id = self::getDb()->queryFirstField("SELECT id FROM machines ORDER BY CAST(REPLACE(SUBSTRING_INDEX(php_version, '-', 1), '.', '') AS INT) DESC LIMIT 1");

        return self::getDb()->queryFirstField('SELECT php_version FROM ' . $this->name . " WHERE id=%d", $id);
    }

    public function getLastMysqlVersion(): string
    {
        $id = self::getDb()->queryFirstField("SELECT id FROM machines ORDER BY CAST(REPLACE(SUBSTRING_INDEX(mysql_version, '-', 1), '.', '') AS INT) DESC LIMIT 1");

        return self::getDb()->queryFirstField('SELECT mysql_version FROM ' . $this->name . " WHERE id=%d", $id);
    }
}