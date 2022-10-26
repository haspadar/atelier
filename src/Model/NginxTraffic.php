<?php

namespace Atelier\Model;

class NginxTraffic extends Model
{
    protected string $name = 'nginx_traffic';

    public function has(int $projectId, \DateTime $logTime)
    {
        return (bool)self::getDb()->query(
            'SELECT * FROM ' . $this->name . ' WHERE project_id = %d AND log_time = %s',
            $projectId,
            $logTime->format('Y-m-d H:i:s')
        );
    }

    public function getLastTraffic(int $projectId): string
    {
        return self::getDb()->queryFirstField(
            'SELECT traffic FROM '
            . $this->name
            . ' WHERE id=(SELECT MAX(id) FROM ' . $this->name . ' WHERE project_id = %d)',
            $projectId,
        ) ?? '';
    }

    public function getForDate(int $projectId, string $date): array
    {
        return self::getDb()->queryFirstRow(
            'SELECT MIN(traffic) AS min_traffic, MAX(traffic) AS max_traffic, AVG(traffic) AS avg_traffic FROM '
                . $this->name
                . ' WHERE project_id=%d AND create_time >= %s AND create_time <= %s',
            $projectId,
            $date . ' 00:00:00',
            $date . ' 23:59:59',
        ) ?? [];
    }
}