<?php

namespace Atelier;

use Atelier\Model\ProjectTypes;
use Atelier\Project\Type;

class Projects
{

    /**
     * @param int $machineId
     * @param ProjectType[] $types
     * @return Project[]
     */
    public static function getProjects(int $machineId = 0, array $types = []): array
    {
        $typeIds = array_map(fn(ProjectType $type) => $type->getId(), $types);

        return array_map(fn(array $project) => new Project($project), (new Model\Projects())->getAll($machineId, $typeIds));
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
        if (in_array($directory, [
            '/var/www/doshka.org',
            '/var/www/doska3.ru',
            '/var/www/indexus.ru',
        ])) {
            return self::getIndexusType();
        }

        if (str_contains($directory, 'autode.net')) {
            return self::getAutodeType();
        }

        if (self::isPaltoProject($ssh, $directory)) {
            return self::getPaltoType();
        }

        if (RotatorFragments::findDirectoryFragments($ssh, $directory)) {
            return self::getRotatorType();
        }

        return self::getUndefinedType();
    }

    private static function isPaltoProject(Ssh $ssh, string $directory): bool
    {
        $response = $ssh->exec('cat ' . $directory . '/composer.json');

        return mb_strpos($response, '"name": "haspadar/palto"') !== false;
    }

    public static function getUndefinedProjects(): array
    {
        return self::getProjects(0, [self::getUndefinedType()]);
    }

    public static function getPaltoProjects(): array
    {
        return self::getProjects(0, [self::getPaltoType()]);
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
            fn($type) => new ProjectType($type),
            (new ProjectTypes())->getAll()
        );
    }

    private static function getPaltoType(): ProjectType
    {
        return new ProjectType((new ProjectTypes())->getByName(strtolower(Type::PALTO->name)));
    }

    private static function getAutodeType(): ProjectType
    {
        return new ProjectType((new ProjectTypes())->getByName(strtolower(Type::AUTODE->name)));
    }

    private static function getIndexusType(): ProjectType
    {
        return new ProjectType((new ProjectTypes())->getByName(strtolower(Type::INDEXUS->name)));
    }

    private static function getRotatorType(): ProjectType
    {
        return new ProjectType((new ProjectTypes())->getByName(strtolower(Type::ROTATOR->name)));
    }

    private static function getUndefinedType(): ProjectType
    {
        return new ProjectType((new ProjectTypes())->getByName(strtolower(Type::UNDEFINED->name)));
    }
}