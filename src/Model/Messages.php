<?php

namespace Atelier\Model;

class Messages extends Model
{
    protected string $name = 'messages';

    public function getBetween(string $from, string $to, ?int $machineId, ?int $projectId, string $type, string $title): array
    {
        $sql = 'SELECT * FROM '
            . $this->name
            . ' WHERE ' . ($machineId ? "machine_id = $machineId" : 'machine_id IS NULL')
            . ' AND ' . ($projectId ? "project_id = $projectId" : 'project_id IS NULL')
            . ' AND type = %s AND title = %s AND create_time >= %s AND create_time <= %s';

        return self::getDb()->query(
            $sql,
            $type,
            $title,
            $from,
            $to
        ) ?: [];
    }
}