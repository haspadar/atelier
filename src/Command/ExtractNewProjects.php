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
        $directories = $machine->scanProjectDirectories();
        if ($newDirectories = $machine->getNewDirectories($directories)) {
            $machine->addProjects($newDirectories);
            $report = count($newDirectories) > 1
                ? 'Добавлены проекты ' . implode(', ', $newDirectories)
                : 'Добавлен проект ' . $newDirectories[0];
        } else {
            $report = 'Новые проекты не найдены';
        }

        return $report;
    }
}