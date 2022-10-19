<?php

namespace Atelier;

class Checks
{
    public static function run()
    {
        $notMasterProjects = self::getNotMasterProjects();
        $oldCommitProjects = self::getOldCommitProjects();
        $oldMigrationProjects = self::getOldMigrationProjects();
        $smokeErrorProjects = self::getSmokeErrorsProjects();

//        last_branch_name=master
//        last_commit_time-разный, сортировка по возрастанию, сравниваю с последним
//        last_migration_name-группировка,сортировка по количеству, сравниваю крайние
//        smoke_last_time-OK
    }

    /**
     * @param Model\Projects $projects
     * @return Project[]
     */
    public static function getNotMasterProjects(): array
    {
        return array_map(fn($project) => new Project($project), (new \Atelier\Model\Projects())->getNotMasterProjects());
    }

    /**
     * @return Project[]
     */
    public static function getOldCommitProjects(): array
    {
        return array_map(fn($project) => new Project($project), (new \Atelier\Model\Projects())->getOldCommitProjects());
    }

    /**
     * @return Project[]
     */
    public static function getOldMigrationProjects(): array
    {
        return array_map(fn($project) => new Project($project), (new \Atelier\Model\Projects())->getOldMigrationProjects());
    }

    /**
     * @return Project[]
     */
    public static function getSmokeErrorsProjects(): array
    {
        return array_map(fn($project) => new Project($project), (new \Atelier\Model\Projects())->getSmokeErrorProjects());
    }

    /**
     * @return Warning[]
     */
//    public static function getWarnings(): array
//    {
//        $types = (new Fittings())->getAll();
//        $warnings = [];
//        foreach ($types as $type) {
//            $class = '\Atelier\Warning\\' . Names::snakeToCamel($type['name']);
//            $warnings[] = new $class($type);
//        }
//
//        return $warnings;
//    }

//    public static function getFittingWarning(int $fittingId): Warning
//    {
//        $type = (new Fittings())->getById($fittingId);
//        $class = '\Atelier\Warning\\' . Names::snakeToCamel($type['name']);
//
//        return new $class($type);
//    }

//    public static function getFitting(int $id): array
//    {
//        return (new Fittings())->getById($id) ?: [];
//    }
}