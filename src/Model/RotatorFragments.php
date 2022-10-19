<?php

namespace Atelier\Model;

class RotatorFragments extends Model
{
    protected string $name = 'rotator_fragments';

    public function getByProjectId(int $projectId): array
    {
        return self::getDb()->query('SELECT * FROM ' . $this->name . ' WHERE project_id = %d', $projectId);
    }

    public function removeByProjectId(int $projectId)
    {
        self::getDb()->delete($this->name, 'project_id = %d', $projectId);
    }
}