<?php

namespace Atelier\Model;

class Rotator extends Model
{
    protected string $name = 'rotator';

    public function getFirst()
    {
        return self::getDb()->queryFirstRow('SELECT * FROM ' . $this->name);
    }
}