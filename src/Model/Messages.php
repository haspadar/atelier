<?php

namespace Atelier\Model;

class Messages extends Model
{
    protected string $name = 'messages';

    public function getToday(?int $machineId, ?int $projectId, string $type, string $title)
    {
        $sql = 'SELECT * FROM '
            . $this->name
            . ' WHERE ' . ($machineId ? "machine_id = $machineId" : 'machine_id IS NULL')
            . ' AND ' . ($projectId ? "project_id = $projectId" : 'project_id IS NULL')
            . ' AND type = %s AND title = %s AND create_time >= %s AND create_time <= %s';
        $now = new \DateTime();

        return self::getDb()->query(
            $sql,
            $type,
            $title,
            $now->format('Y-m-d') . ' 00:00:00',
            $now->format('Y-m-d') . ' 23:59:59'
        );
    }
}