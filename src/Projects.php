<?php

namespace Atelier;

use Atelier\Model\ProjectTypes;
use Atelier\Project\ProjectType;

class Projects
{
    /**
     * @param int $machineId
     * @param int|null $typeId
     * @return Project[]
     */
    public static function getProjects(int $machineId = 0, ?int $typeId = null): array
    {
        return array_map(fn(array $project) => new Project($project), (new Model\Projects())->getAll($machineId, $typeId));
    }

    public static function getProject(int $id): Project
    {
        return new Project((new Model\Projects())->getById($id));
    }

    public static function deleteProject(int $id): void
    {
        (new Model\Projects())->remove($id);
    }

    public static function getType(Ssh $ssh, string $directory): ProjectType
    {
        if (self::isPaltoProject($ssh, $directory)) {
            return ProjectType::PALTO;
        }

        if (self::getRotatorFiles($ssh, $directory)) {
            return ProjectType::ROTATOR;
        }

        return ProjectType::UNDEFINED;
    }

    private static function isPaltoProject(Ssh $ssh, string $directory): bool
    {
        $response = $ssh->exec('cat ' . $directory . '/composer.json');

        return mb_strpos($response, '"name": "haspadar/palto"') !== false;
    }

    private static function getRotatorFiles(Ssh $ssh, string $directory): array
    {
        $response = $ssh->exec("grep -R --include=*.{env,php} --exclude-dir={vendor,logs,db,sphinx,\*sitemaps} \"ROTATOR_KEY\|ROTATOR_URL\" "
            . $directory);
        $files = [];
        foreach (explode(PHP_EOL, $response ?? '') as $responseLine) {
            if (($file = explode(':', $responseLine)[0] ?? '') && !in_array($file, $files)) {
                $files[] = $file;
            }
        }

        return $files;
    }

    public static function getUndefinedProjects(): array
    {
        return self::getProjects(0, self::getUndefinedTypeId());
    }

    public static function getPaltoProjects(): array
    {
        return self::getProjects(0, self::getPaltoTypeId());
    }

    public static function getRotatorProjects(): array
    {
        return self::getProjects(0, self::getRotatorTypeId());
    }

    public static function getUndefinedTypeId(): int
    {
        return (new ProjectTypes())->getUndefinedTypeId();
    }

    public static function getRotatorTypeId(): int
    {
        return (new ProjectTypes())->getRotatorTypeId();
    }

    public static function getPaltoTypeId(): int
    {
        return (new ProjectTypes())->getPaltoTypeId();
    }

    public static function getGroupedProjects(): array
    {
        $projects = self::getProjects();
        $grouped = [];
        foreach ($projects as $project) {
            $grouped[$project->getTypeName()][] = $project;
        }

        return $grouped;
    }

    public static function getTypes(): array
    {
        return array_map(
            fn($type) => new \Atelier\ProjectType($type),
            (new ProjectTypes())->getAll()
        );
    }
}