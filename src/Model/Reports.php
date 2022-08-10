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

    public function getCommandAll(int $commandId, int $limit, int $offset): array
    {
        return self::getDb()->query(
            'SELECT * FROM ' . $this->name . ' WHERE command_id = %d ORDER BY id DESC LIMIT %d OFFSET %d',
            $commandId,
            $limit,
            $offset
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

    public function getRunLogProjects(int $runLogId): array
    {
        return self::getDb()->query('SELECT * FROM projects AS p INNER JOIN '
            . $this->name
            . ' AS r ON p.id=r.project_id WHERE r.run_log_id=%d',
            $runLogId
        ) ?: [];
    }

    public function getRunLogReports(int $runLogId): array
    {
        return self::getDb()->query('SELECT * FROM ' . $this->name . ' WHERE run_log_id = %d', $runLogId);
    }

    public function getRunLogCommands(int $runLogId): array
    {
        return self::getDb()->query('SELECT DISTINCT c.* FROM commands AS c INNER JOIN '
            . $this->name
            . ' AS r ON c.id=r.command_id WHERE r.run_log_id=%d',
            $runLogId
        ) ?: [];
    }
}