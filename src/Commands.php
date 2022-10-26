<?php

namespace Atelier;

use Atelier\Command\Exception;
use Atelier\Command\ExtractGit;
use Atelier\Model\Reports;
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
     * @param Machine $project
     * @return Command[]
     */
    public static function getProjectCommands(Machine $project): array
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
}