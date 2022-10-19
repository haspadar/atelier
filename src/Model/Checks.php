<?php

namespace Atelier\Model;

class Checks extends Model
{
    protected string $name = 'checks';

    public function getByName(string $name): array
    {
        return self::getDb()->queryFirstRow('SELECT * FROM ' . $this->name . ' WHERE name = %s', $name) ?: [];
    }

    public function getAll(): array
    {
        return self::getDb()->query('SELECT * FROM ' . $this->name) ?: [];
    }
}