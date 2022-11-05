<?php

namespace Atelier\Model;

class CommandTypes extends Model
{
    protected string $name = 'command_types';

    public function getCommandTypes(int $commandId): array
    {
        return self::getDb()->query('SELECT pt.* FROM ' . $this->name . ' AS ct INNER JOIN project_types AS pt ON ct.project_type_id = pt.id WHERE ct.command_id = %d', $commandId);
    }

    public function removeCommand(int $commandId)
    {
        return self::getDb()->delete($this->name, 'command_id = %d', $commandId);
    }
}