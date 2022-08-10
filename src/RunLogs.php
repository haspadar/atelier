<?php

namespace Atelier;

use Atelier\Command\ExtractCommit;
use Atelier\Model\Reports;
use Atelier\Project\ProjectType;

class RunLogs
{
    public static function getRunLog(int $id): RunLog
    {
        return new RunLog((new Model\RunLogs())->getById($id));
    }

    /**
     * @return Command[]
     */
    public static function getRunLogs(): array
    {
        return array_map(
            fn(array $runLog) => new RunLog($runLog),
            (new Model\RunLogs())->getAll()
        );
    }

    public static function paltoRun(Command $command)
    {
        self::run($command, Projects::getPaltoProjects());
    }

    /**
     * @param Command $command
     * @param Project[] $projects
     * @return void
     */
    public static function run(Command $command, array $projects): ?Report
    {
        declare(ticks=10) {
            $run = new Run();
            register_tick_function([$run, 'ping']);
            foreach ($projects as $project) {
                $ssh = $project->getMachine()->createSsh();
                if (!$ssh->getError()) {
                    $report = \Atelier\Reports::add($command, $project, $run);
                    $response = $command->run($project);
                    $report->finish($response);
                } else {
                    Logger::error($project->getMachine()->getHost() . ': ' . $ssh->getError());
                }
            }

            $run->finish();
        }

        return $report ?? null;
    }

    public static function getProjectCommands(Project $project)
    {
        return array_map(
            fn($command) => self::createCommand($command),
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