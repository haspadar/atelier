<?php

namespace Atelier\Model;

class Parser extends Model
{
    protected string $name = 'parser';

    public function getForPeriod(int $projectId, string $fromTime): array
    {
        return self::getDb()->query(
            'SELECT * FROM ' . $this->name . ' WHERE project_id = %d AND create_time >= %s',
            $projectId,
            $fromTime
        ) ?: [];
    }

    public function getWithAdsLastTime(int $projectId): string
    {
        return self::getDb()->queryFirstField(
            'SELECT create_time FROM ' . $this->name . ' WHERE id=(SELECT MAX(id) FROM ' . $this->name . ' WHERE project_id = %d AND hour_ads_count > 0)',
            $projectId
        ) ?: '';
    }
}