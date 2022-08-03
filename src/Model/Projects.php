<?php

namespace Atelier\Model;

class Projects extends Model
{
    protected string $name = 'projects';

    public function getAll(int $machineId = 0, ?string $type = null): array
    {
        return self::getDb()->query(
            'SELECT * FROM ' . $this->name . ' WHERE true '
                . ($machineId ? ' AND machine_id = ' . $machineId : '')
                . ($type ? ' AND type = "' . $type . '"' : '')
        ) ?: [];
    }

    public function removeMachineProjects(int $machineId)
    {
        self::getDb()->delete($this->name, 'machine_id = %d', $machineId);
    }
}