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
}