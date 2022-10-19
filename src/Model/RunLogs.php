<?php

namespace Atelier\Model;

class RunLogs extends Model
{
    protected string $name = 'run_logs';

    public function getAll(int $limit, int $offset): array
    {
        return self::getDb()->query('SELECT * FROM ' . $this->name . ' ORDER BY id DESC LIMIT %d OFFSET %d ', $limit, $offset);
    }

    public function getAllCount(): int
    {
        return self::getDb()->queryFirstField('SELECT COUNT(*) FROM ' . $this->name);
    }
}