<?php

namespace Atelier\Command;

use Atelier\Command;
use Atelier\Debug;
use Atelier\Logger;
use Atelier\Machine;
use Atelier\ProjectCommand;

class ExtractGit extends ProjectCommand
{
    public function run(Machine $project): string
    {
        $response = $project->getMachine()->getSsh()->exec("cd " . $project->getPath() . ' && git log -1 --format=%cd && git branch');
        $parsedTime = explode(PHP_EOL, $response)[0];
        if (strtotime($parsedTime)) {
            $project->setLastCommitTime(new \DateTime($parsedTime));
            $project->setLastBranchName(strtr(explode(PHP_EOL, $response)[1], [' ' => '', '*' => '']));
            Logger::info('Updated "' . $this->getName() . '" info');
        } else {
            Logger::warning('Ignored not git project ' . $project->getName());
        }

        return $response ?? '';
    }
}