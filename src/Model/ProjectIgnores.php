<?php

namespace Atelier\Model;

use Atelier\Debug;

class ProjectIgnores extends Model
{
    protected string $name = 'project_ignores';

    public function isExists(int $machineId, string $projectPath): bool
    {
        return (bool)self::getDb()->queryFirstRow('SELECT * FROM '
            . $this->name
            . ' WHERE machine_id = %d AND project_path=%s',
            $machineId,
            $projectPath
        );
    }
}