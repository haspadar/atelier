<?php

namespace Atelier\Model;

class CheckIgnores extends Model
{
    protected string $name = 'check_ignores';

    public function find(string $commandName, ?int $projectId, ?int $machineId): array
    {
        if ($projectId || $machineId) {
            return self::getDb()->queryFirstRow("SELECT * FROM " . $this->name . " WHERE command_name=%s AND "
                . ($projectId ? "project_id=$projectId" : "machine_id=$machineId"),
                $commandName
            ) ?: [];
        }

        return [];
    }
}