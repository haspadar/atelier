<?php

namespace Atelier\Model;

use Atelier\Debug;

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

    public function getAll(int $machineId = 0, array $typeIds = []): array
    {
        $values = [
            'machine_id' => $machineId,
            'type_ids' => $typeIds
        ];
        return self::getDb()->query(
            'SELECT p.*, pt.name as type_name FROM ' . $this->name . ' AS p INNER JOIN project_types AS pt ON p.type_id=pt.id WHERE true '
                . ($machineId ? ' AND p.machine_id = %d_machine_id' : '')
                . ($typeIds ? ' AND p.type_id IN %ld_type_ids' : ''),
            $values
        ) ?: [];
    }

    public function removeMachineProjects(int $machineId)
    {
        self::getDb()->delete($this->name, 'machine_id = %d', $machineId);
    }

    public function getSmokeErrorProjects(): array
    {
        return self::getDb()->query('SELECT * FROM ' . $this->name . ' WHERE smoke_last_report <> "" AND smoke_last_report <> "OK"');
    }

    public function getOldMigrationProjects(): array
    {
        $grouped = self::getDb()->query('SELECT *, COUNT(*) AS count FROM ' . $this->name . ' WHERE last_migration_name <> "" GROUP BY last_migration_name ORDER BY count DESC');
        unset($grouped[0]);

        return array_values($grouped);
    }

    public function getOldCommitProjects(): array
    {
        $newestCommit = self::getDb()->queryFirstField('SELECT MAX(last_commit_time) FROM ' . $this->name);

        return self::getDb()->query('SELECT * FROM ' . $this->name . ' WHERE last_commit_time < %s AND last_commit_time IS NOT NULL', $newestCommit);
    }

    public function getNotMasterProjects(): array
    {
        return self::getDb()->query('SELECT * FROM ' . $this->name . ' WHERE last_branch_name <> %s AND last_branch_name <> ""', 'master');
    }

    public function getLastMigrationName(): string
    {
        return self::getDb()->queryFirstField('SELECT MAX(last_migration_name) FROM ' . $this->name);
    }

    public function getCommitTime(): string
    {
        return self::getDb()->queryFirstField('SELECT MAX(last_commit_time) FROM ' . $this->name);
    }
}