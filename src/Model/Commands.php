<?php

namespace Atelier\Model;

class Commands extends Model
{
    protected string $name = 'commands';

    public function getByName(string $name): array
    {
        return self::getDb()->queryFirstRow('SELECT * FROM ' . $this->name . ' WHERE name = %s', $name) ?: [];
    }

    public function getAll(): array
    {
        return self::getDb()->query('SELECT * FROM ' . $this->name) ?: [];
    }

    public function removeNotIn(array $names): int
    {
        self::getDb()->delete($this->name, 'name NOT IN %ls', $names);

        return self::getDb()->affectedRows();
    }

    public function getTypeAll(int $typeId)
    {
        return self::getDb()->query('SELECT c.* FROM '
            . $this->name
            . ' AS c INNER JOIN command_types AS ct ON c.id=ct.command_id WHERE ct.project_type_id = %d',
            $typeId
        ) ?: [];
    }
}