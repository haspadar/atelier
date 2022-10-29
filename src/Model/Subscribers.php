<?php

namespace Atelier\Model;

class Subscribers extends Model
{
    protected string $name = 'subscribers';

    public function getBy(string $field, string $value): array
    {
        return self::getDb()->queryFirstRow('SELECT * FROM ' . $this->name . ' WHERE `' . $field . '`="' . $value . '"') ?: [];
    }

    public function getAll(): array
    {
        return self::getDb()->query('SELECT * FROM ' . $this->name) ?: [];
    }

}