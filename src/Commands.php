<?php

namespace Atelier;

use Atelier\Command\Exception;
use Atelier\Command\ExtractGit;
use Atelier\Model\CommandTypes;
use Atelier\Model\ProjectTypes;
use Atelier\Model\CommandReports;
use Atelier\Project\Type;

class Commands
{
    /**
     * @return Command[]
     */
    public static function getCommands(): array
    {
        return array_map(
            fn(array $command) => self::createCommand($command),
            (new Model\Commands())->getAll()
        );
    }

    /**
     * @param Project $project
     * @return Command[]
     */
    public static function getProjectCommands(Project $project): array
    {
        return array_map(
            fn ($command) => self::createCommand($command),
            (new Model\Commands())->getTypeAll($project->getType()->getId())
        );
    }

    public static function getCommand(int $id): Command
    {
        $command = (new Model\Commands())->getById($id);

        return self::createCommand($command);
    }

    public static function getCommandByName(string $name): Command
    {
        $command = (new Model\Commands())->getByName($name);

        return self::createCommand($command);
    }

    public static function createCommand(array $command): Command
    {
        $className = 'Atelier\\Command\\' . ucfirst($command['name']);

        return new $className($command);
    }

    public static function getCommandProjectTypes(Command $command): array
    {
        return array_map(
            fn($commandType) => Projects::getTypeByName($commandType['name']),
            (new CommandTypes())->getCommandTypes($command->getId())
        );
    }

    public static function updateCommandTypes(int $commandId, array $projectTypeIds): void
    {
        (new CommandTypes())->removeCommand($commandId);
        self::addCommandTypes($commandId, $projectTypeIds);
    }

    public static function addCommandTypes(int $commandId, array $projectTypeIds): void
    {
        foreach ($projectTypeIds as $projectTypeId) {
            (new CommandTypes())->add([
                'command_id' => $commandId,
                'project_type_id' => $projectTypeId
            ]);
        }
    }
}