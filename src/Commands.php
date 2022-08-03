<?php

namespace Atelier;

use Atelier\Project\ProjectType;

class Commands
{
    /**
     * @return Command[]
     */
    public static function getCommands(): array
    {
        return array_map(
            fn(array $command) => new Command($command),
            (new Model\Commands())->getAll()
        );
    }

    public static function runSmokes()
    {
        self::runForEveryPalto('runSmoke');
    }

    public static function updateProjects()
    {
        self::runForEveryPalto('updateProject');
    }

    public static function extractMigrations()
    {
        self::runForEveryPalto('extractMigration');
    }

    public static function extractCommits()
    {
        self::runForEveryPalto('extractCommit');
    }

    private static function runForEveryPalto(string $methodName)
    {
        self::runForEveryType(ProjectType::PALTO, $methodName);
    }

    private static function runForEveryType(ProjectType $type, string $methodName)
    {
        declare(ticks = 10) {
            $run = new Run($methodName);
            register_tick_function([$run, 'ping']);
            foreach (Garage::getMachines() as $machine) {
                $ssh = $machine->createSsh();
                if (!$ssh->getError()) {
                    foreach ($machine->getProjects($type) as $project) {
                        $project->$methodName($ssh);
                    }
                } else {
                    Logger::error($machine->getHost() . ': ' . $ssh->getError());
                }
            }

            $run->finish();
        }
    }
}