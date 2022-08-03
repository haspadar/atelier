<?php

namespace Atelier\Model;

class Machines extends Model
{
    protected string $name = 'machines';

    public function getAll(): array
    {
        return self::getDb()->query('SELECT * FROM ' . $this->name) ?: [];
    }
}