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

    public function getIgnoredAll(): array
    {
        return self::getDb()->query('SELECT * FROM messages WHERE ignore_time IS NOT NULL');
    }

    public function getAllCount(string $type): int
    {
        return self::getDb()->queryFirstField('SELECT COUNT(*) FROM messages WHERE type=%s AND ignore_time IS NULL', $type);
    }

    public function getById(int $id): array
    {
        return self::getDb()->queryFirstRow('SELECT m.*, mc.host AS machine_host, p.path AS project_path FROM messages AS m INNER JOIN machines AS mc ON m.machine_id=mc.id LEFT JOIN projects AS p ON m.project_id=p.id WHERE m.id=%d LIMIT 1', $id) ?: [];
    }

    public function getAll(string $type, int $limit, int $offset): array
    {
        return self::getDb()->query('SELECT m.*, mc.host AS machine_host, p.path AS project_path FROM messages AS m INNER JOIN machines AS mc ON m.machine_id=mc.id LEFT JOIN projects AS p ON m.project_id=p.id WHERE m.type=%s AND m.ignore_time IS NULL'
            . ($limit ? ' LIMIT ' . $limit : '')
            . ($offset ? ' OFFSET ' . $offset : ''),
            $type
        );
    }
}