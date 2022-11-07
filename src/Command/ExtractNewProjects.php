<?php

namespace Atelier\Command;

use Atelier\Command;
use Atelier\Debug;
use Atelier\Logger;
use Atelier\Machine;
use Atelier\MachineCommand;
use Atelier\Project;
use Atelier\ProjectCommand;
use Atelier\Projects;
use Atelier\RotatorFragment;
use Atelier\RotatorFragments;
use http\Url;
use PHPUnit\Event\Runtime\PHP;

class ExtractNewProjects extends MachineCommand
{
    public function run(Machine $machine): string
    {
        $directories = array_filter(
            $machine->scanProjectDirectories(),
            fn ($directory) => count(explode('.', $directory)) > 1
        );
        if ($newDirectories = $machine->getNewDirectories($directories)) {
            $machine->addProjects($newDirectories);
            $addReport = count($newDirectories) > 1
                ? 'Добавлены проекты ' . implode(', ', $newDirectories)
                : 'Добавлен проект ' . $newDirectories[0];
        } else {
            $addReport = 'Новые проекты не найдены';
            Logger::warning($addReport);
        }

        $allProjects = $machine->getProjects();
        $paths = array_map(fn(Project $project) => $project->getPath(), $allProjects);
        if ($notFoundDirectories = array_diff($paths, $directories)) {
            $forDeleteProjects = $this->filterProjectsByPaths($allProjects, $notFoundDirectories);
            Projects::deleteProjects($forDeleteProjects);
            $deleteReport = 'Удалены из базы проекты '
                . implode(', ', array_map(fn(Project $project) => $project->getName(), $forDeleteProjects));
            Logger::warning($deleteReport);
        }

        return $addReport . (isset($deleteReport) ? '. ' . $deleteReport : '');
    }

    /**
     * @param Project[] $allProjects
     * @param array $notFoundDirectories
     * @return Project[]
     */
    private function filterProjectsByPaths(array $allProjects, array $notFoundDirectories): array
    {
        $forDeleteProjects = [];
        foreach ($allProjects as $project) {
            if (in_array($project->getPath(), $notFoundDirectories)) {
                $forDeleteProjects[] = $project;
            }
        }

        return $forDeleteProjects;
    }
}