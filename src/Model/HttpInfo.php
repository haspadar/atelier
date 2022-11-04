<?php

namespace Atelier\Model;

class HttpInfo extends Model
{
    protected string $name = 'http_info';

    public function getForPeriod(int $projectId, string $fromTime): array
    {
        return self::getDb()->query(
            'SELECT * FROM ' . $this->name . ' WHERE project_id = %d AND create_time >= %s',
            $projectId,
            $fromTime
        ) ?: [];
    }

    public function getLast(int $projectId): array
    {
        return self::getDb()->queryFirstRow(
            'SELECT * FROM ' . $this->name . ' WHERE id=(SELECT MAX(id) FROM ' . $this->name . ' WHERE project_id = %d)',
            $projectId
        ) ?: [];
    }

}