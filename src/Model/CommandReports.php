<?php

namespace Atelier\Model;

class CommandReports extends Model
{
    protected string $name = 'command_reports';

    public function getProjectLast(int $commandId, int $projectId): array
    {
        return self::getDb()->queryFirstRow(
            'SELECT * FROM ' . $this->name . ' WHERE command_id = %d AND project_id = %d ORDER BY id DESC LIMIT 1',
            $commandId,
            $projectId
        ) ?: [];
    }

    public function getMachineLast(int $commandId, int $machineId): array
    {
        return self::getDb()->queryFirstRow(
            'SELECT * FROM ' . $this->name . ' WHERE command_id = %d AND machine_id = %d ORDER BY id DESC LIMIT 1',
            $commandId,
            $machineId
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

    public function getAll(int $projectTypeId, string $period, int $limit, int $offset): array
    {
        $query = 'SELECT r.* FROM ' . $this->name . ' AS r INNER JOIN projects AS p ON r.project_id = p.id';
        $values = [
            'limit' => $limit,
            'offset' => $offset,
            'project_type_id' => $projectTypeId,
            'start_time_from' => $this->getStartTimeFrom($period)?->format('Y-m-d H:i:s'),
            'start_time_to' => $this->getStartTimeTo($period)?->format('Y-m-d H:i:s')
        ];
        if ($projectTypeId && $period) {
            $query .= ' WHERE p.type_id = %d_project_type_id AND start_time >= %s_start_time_from AND start_time <= %s_start_time_to';
        } elseif ($projectTypeId) {
            $query .= ' WHERE p.type_id = %d_project_type_id';
        } elseif ($period) {
            $query .= ' WHERE start_time >= %s_start_time_from AND start_time <= %s_start_time_to';
        }

        return self::getDb()->query($query . ' ORDER BY id DESC LIMIT %d_limit OFFSET %d_offset', $values) ?: [];
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

    private function getStartTimeFrom(string $period): ?\DateTime
    {
        return $this->getStartTime($period)?->setTime(0, 0, 0);
    }

    private function getStartTimeTo(string $period): ?\DateTime
    {
        return (new \DateTime())->setTime(23, 59, 59);
    }

    public function getStartTime(string $period): ?\DateTime
    {
        return match($period) {
            'today' => new \DateTime(),
            'yesterday' => (new \DateTime())->modify('-1 day'),
            'week' => (new \DateTime())->modify('-1 week'),
            'month' => (new \DateTime())->modify('-1 month'),
            default => null
        };
    }

    public function getAllCount(int $projectTypeId, string $period): int
    {
        $query = 'SELECT COUNT(*) FROM ' . $this->name . ' AS r INNER JOIN projects AS p ON r.project_id = p.id';
        $values = [
            'project_type_id' => $projectTypeId,
            'start_time_from' => $this->getStartTimeFrom($period)?->format('Y-m-d H:i:s'),
            'start_time_to' => $this->getStartTimeTo($period)?->format('Y-m-d H:i:s')
        ];
        if ($projectTypeId && $period) {
            $query .= ' WHERE p.type_id = %d_project_type_id AND start_time >= %s_start_time_from AND start_time <= %s_start_time_to';
        } elseif ($projectTypeId) {
            $query .= ' WHERE p.type_id = %d_project_type_id';
        } elseif ($period) {
            $query .= ' WHERE start_time >= %s_start_time_from AND start_time <= %s_start_time_to';
        }

        return self::getDb()->queryFirstField($query, $values) ?: 0;
    }
}