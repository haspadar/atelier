<?php

namespace Atelier\Model;

use Atelier\ProjectType;

class ProjectTypes extends Model
{
    protected string $name = 'project_types';

    public function getAll(int $machineId = 0, ?int $typeId = null): array
    {
        return self::getDb()->query('SELECT * FROM ' . $this->name);
    }

    public function getUndefinedTypeId(): int
    {
        return intval(self::getDb()->queryFirstColumn('SELECT id FROM ' . $this->name . ' WHERE name="' . self::UNDEFINED . '"'));
    }

    public function getRotatorTypeId(): int
    {
        return intval(self::getDb()->queryFirstColumn('SELECT id FROM ' . $this->name . ' WHERE name="' . self::ROTATOR . '"'));
    }

    public function getPaltoTypeId(): int
    {
        return intval(self::getDb()->queryFirstColumn('SELECT id FROM ' . $this->name . ' WHERE name="' . self::PALTO . '"'));
    }

    public function getByName(string $name): array
    {
        return self::getDb()->queryFirstRow('SELECT * FROM ' . $this->name . ' WHERE name=%s', $name);
    }
}