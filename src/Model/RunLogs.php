<?php

namespace Atelier\Model;

class RunLogs extends Model
{
    protected string $name = 'run_logs';

    public function getAll(): array
    {
        return self::getDb()->query('SELECT * FROM ' . $this->name);
    }
}