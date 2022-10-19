<?php

namespace Atelier;

use Atelier\Model\ProjectTypes;
use Atelier\Project\Type;
use DateTime;

class Reports
{
    public static function add(Command $command, ?Project $project, ?Machine $machine, Run $run)
    {
        $reportId = (new Model\Reports())->add([
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
        return new Report((new Model\Reports())->getById($id));
    }

    /**
     * @param Project $project
     * @param Command[] $commands
     * @return Report[]
     */
    public static function getProjectLastReports(Project $project, array $commands)
    {
        $reports = [];
        foreach ($commands as $command) {
            if ($last = (new Model\Reports())->getLast($project->getId(), $command->getId())) {
                $reports[$command->getId()] = new Report($last);
            }
        }

        return $reports;
    }

    public static function getReportsCount(int $projectTypeId, string $period): int
    {
        return (new \Atelier\Model\Reports())->getAllCount($projectTypeId, $period);
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return Report[]
     */
    public static function getReports(int $projectTypeId, string $period, int $limit, int $offset): array
    {
        return array_map(
            fn(array $report) => new Report($report),
            (new \Atelier\Model\Reports())->getAll($projectTypeId, $period, $limit, $offset)
        );
    }

    public static function getCommandReports(int $commandId, int $limit, int $offset): array
    {
        return array_map(
            fn(array $report) => new Report($report),
            (new \Atelier\Model\Reports())->getCommandAll($commandId, $limit, $offset)
        );
    }
}