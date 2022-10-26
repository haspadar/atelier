<?php

namespace Atelier\Model;

use Cassandra\Date;

class PhpFpmTraffic extends Model
{
    protected string $name = 'php_fpm_traffic';

    public function getLastTraffic(int $machineId): string
    {
        return self::getDb()->queryFirstField(
            'SELECT traffic FROM '
            . $this->name
            . ' WHERE id=(SELECT MAX(id) FROM ' . $this->name . ' WHERE machine_id = %d)',
            $machineId,
        ) ?: '';
    }

    public function getForDate(int $machineId, string $date)
    {
        return self::getDb()->queryFirstRow(
            'SELECT MIN(traffic) AS min_traffic, MAX(traffic) AS max_traffic, AVG(traffic) AS avg_traffic FROM '
            . $this->name
            . ' WHERE machine_id = %d AND create_time >= %s AND create_time <= %s',
            $machineId,
            $date . ' 00:00:00',
            $date . ' 23:59:59',
        ) ?: [];
    }
}