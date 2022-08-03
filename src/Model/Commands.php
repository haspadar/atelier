<?php

namespace Atelier\Model;

class Commands extends Model
{
    protected string $name = 'commands';

    public function getAll(): array
    {
        return self::getDb()->query('SELECT * FROM ' . $this->name) ?: [];
    }
}