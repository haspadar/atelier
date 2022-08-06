<?php

namespace Atelier\Model;

class Reports extends Model
{
    protected string $name = 'reports';

    public function getLast(int $projectId, int $commandId): array
    {
        return self::getDb()->queryFirstRow(
            'SELECT * FROM ' . $this->name . ' WHERE command_id = %d AND project_id = %d ORDER BY id DESC LIMIT 1',
            $commandId,
            $projectId
        ) ?: [];
    }

    public function getAll(int $limit, int $offset): array
    {
        return self::getDb()->query(
            'SELECT * FROM ' . $this->name . ' ORDER BY id DESC LIMIT %d OFFSET %d',
            $limit,
            $offset
        ) ?: [];
    }
}