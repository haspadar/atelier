<?php

namespace Atelier\Command;

use Atelier\Command;
use Atelier\Logger;
use Atelier\Project;
use Atelier\ProjectCommand;

class ExtractSmoke extends ProjectCommand
{

    public function run(Project $project): string
    {
        $response = $project->getMachine()->getSsh()->exec(
            'cd ' . $project->getPath() . ' && php vendor/bin/phpunit tests'
        );
        $lastLine = $project->getLastLine($response);
        if (str_starts_with($lastLine, 'OK')) {
            Logger::info('Project ' . $this->getName() . ' smoke is OK');
            $project->setSmokeLastReport('OK');
        } else {
            Logger::error('Project ' . $this->getName() . ' smoke is ERROR: ' . $lastLine);
            $project->setSmokeLastReport($lastLine);
        }

        $project->setSmokeLastTime(new \DateTime());

        return $response;
    }
}