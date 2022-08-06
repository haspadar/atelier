<?php

namespace Atelier\Model;

class ProjectTypes extends Model
{
    protected string $name = 'project_types';

    public function getAll(int $machineId = 0, ?int $typeId = null): array
    {
        return self::getDb()->query('SELECT * FROM ' . $this->name);
    }

    public function getUndefinedTypeId(): int
    {
        return intval(self::getDb()->queryFirstColumn('SELECT id FROM ' . $this->name . ' WHERE name="undefined"'));
    }

    public function getRotatorTypeId(): int
    {
        return intval(self::getDb()->queryFirstColumn('SELECT id FROM ' . $this->name . ' WHERE name="rotator"'));
    }

    public function getPaltoTypeId(): int
    {
        return intval(self::getDb()->queryFirstColumn('SELECT id FROM ' . $this->name . ' WHERE name="palto"'));
    }
}