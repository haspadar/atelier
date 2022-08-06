<?php

namespace Atelier\Model;

class Projects extends Model
{
    protected string $name = 'projects';

    public function getById(int $id): array
    {
        return self::getDb()->queryFirstRow(
            'SELECT p.*, pt.name as type_name FROM '
            . $this->name
            . ' AS p INNER JOIN project_types AS pt ON p.type_id=pt.id WHERE p.id = %d ',
            $id) ?: [];
    }

    public function getAll(int $machineId = 0, ?int $typeId = null): array
    {
        return self::getDb()->query(
            'SELECT p.*, pt.name as type_name FROM ' . $this->name . ' AS p INNER JOIN project_types AS pt ON p.type_id=pt.id WHERE true '
                . ($machineId ? ' AND p.machine_id = ' . $machineId : '')
                . ($typeId ? ' AND p.type_id = "' . $typeId . '"' : '')
        ) ?: [];
    }

    public function removeMachineProjects(int $machineId)
    {
        self::getDb()->delete($this->name, 'machine_id = %d', $machineId);
    }
}