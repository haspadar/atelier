<?php

namespace Atelier\Model;

class Machines extends Model
{
    protected string $name = 'machines';

    public function getAll(array $ids): array
    {
        return self::getDb()->query('SELECT * FROM ' . $this->name . ($ids ? ' WHERE id IN %ld' : ''), $ids) ?: [];
    }
}