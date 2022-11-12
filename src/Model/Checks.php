<?php

namespace Atelier\Model;

class Checks extends Model
{
    protected string $name = 'checks';

    public function find(?int $machineId, ?int $projectId, string $type, string $groupTitle): array
    {
        $sql = 'SELECT * FROM '
            . $this->name
            . ' WHERE ' . ($machineId ? "machine_id = $machineId" : 'machine_id IS NULL')
            . ' AND ' . ($projectId ? "project_id = $projectId" : 'project_id IS NULL')
            . ' AND type = %s AND group_title = %s';

        return self::getDb()->query(
            $sql,
            $type,
            $groupTitle,
        ) ?: [];
    }

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

    public function getAllCount(string $type): int
    {
        return self::getDb()->queryFirstField('SELECT COUNT(*) FROM ' . $this->name . ' WHERE type=%s AND ignore_time IS NULL', $type);
    }

    public function getById(int $id): array
    {
        return self::getDb()->queryFirstRow('SELECT c.*, mc.host AS machine_host, p.path AS project_path FROM checks AS c INNER JOIN machines AS mc ON c.machine_id=mc.id LEFT JOIN projects AS p ON c.project_id=p.id WHERE c.id=%d LIMIT 1', $id) ?: [];
    }

    public function getAll(string $type, int $limit, int $offset): array
    {
        return self::getDb()->query('SELECT c.*, mc.host AS machine_host, p.path AS project_path FROM checks AS c INNER JOIN machines AS mc ON c.machine_id=mc.id LEFT JOIN projects AS p ON c.project_id=p.id WHERE c.type=%s AND c.ignore_time IS NULL ORDER BY c.id DESC'
            . ($limit ? ' LIMIT ' . $limit : '')
            . ($offset ? ' OFFSET ' . $offset : ''),
            $type
        );
    }
}