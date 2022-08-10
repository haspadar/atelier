<?php

namespace Atelier;

use Atelier\Model\ProjectTypes;
use Atelier\Project\ProjectType;
use DateTime;

class Reports
{
    public static function add(Command $command, Project $project, Run $run)
    {
        $reportId = (new Model\Reports())->add([
            'command_id' => $command->getId(),
            'project_id' => $project->getId(),
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

    /**
     * @param int $limit
     * @param int $offset
     * @return Report[]
     */
    public static function getReports(int $limit, int $offset): array
    {
        return array_map(
            fn(array $report) => new Report($report),
            (new \Atelier\Model\Reports())->getAll($limit, $offset)
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