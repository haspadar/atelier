<?php

namespace Atelier;

use Atelier\Model\ProjectTypes;
use Atelier\Project\Type;
use DateTime;

class CommandReports
{
    public static function add(Command $command, ?Project $project, ?Machine $machine, Run $run)
    {
        $reportId = (new Model\CommandReports())->add([
            'command_id' => $command->getId(),
            'project_id' => $project?->getId(),
            'machine_id' => $machine ? $machine->getId() : $project->getMachine()->getId(),
            'start_time' => (new DateTime())->format('Y-m-d H:i:s'),
            'run_log_id' => $run->getId()
        ]);

        return self::getReport($reportId);
    }

    public static function getReport(int $id)
    {
        return new CommandReport((new Model\CommandReports())->getById($id));
    }

    /**
     * @param Project $project
     * @param Command[] $commands
     * @return CommandReport[]
     */
    public static function getProjectLastReports(Project $project, array $commands)
    {
        $reports = [];
        foreach ($commands as $command) {
            if ($last = (new Model\CommandReports())->getProjectLast($command->getId(), $project->getId())) {
                $reports[$command->getId()] = new CommandReport($last);
            }
        }

        return $reports;
    }

    public static function getReportsCount(int $projectTypeId, string $period): int
    {
        return (new \Atelier\Model\CommandReports())->getAllCount($projectTypeId, $period);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return CommandReport[]
     */
    public static function getReports(int $projectTypeId, string $period, int $limit, int $offset): array
    {
        return array_map(
            fn(array $report) => new CommandReport($report),
            (new \Atelier\Model\CommandReports())->getAll($projectTypeId, $period, $limit, $offset)
        );
    }

    public static function getCommandReports(int $commandId, int $limit, int $offset): array
    {
        return array_map(
            fn(array $report) => new CommandReport($report),
            (new \Atelier\Model\CommandReports())->getCommandAll($commandId, $limit, $offset)
        );
    }

    public static function getProjectLastReport(int $commandId, int $projectId): ?CommandReport
    {
        $report = (new \Atelier\Model\CommandReports())->getProjectLast($commandId, $projectId);

        return $report ? new CommandReport($report) : null;
    }

    public static function getMachineLastReport(int $commandId, int $machineId): ?CommandReport
    {
        $report = (new \Atelier\Model\CommandReports())->getMachineLast($commandId, $machineId);

        return $report ? new CommandReport($report) : null;
    }
}