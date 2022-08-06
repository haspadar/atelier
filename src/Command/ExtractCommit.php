<?php

namespace Atelier\Command;

use Atelier\Command;
use Atelier\Logger;
use Atelier\Project;

class ExtractCommit extends Command
{
    public function run(Project $project): string
    {
        $response = $project->getMachine()->getSsh()->exec("cd " . $project->getPath() . ' && git log -1 --format=%cd');
        $project->setLastCommitTime(new \DateTime($response));
        Logger::info('Updated "' . $this->getName() . '" last_commit_time');

        return $response ?? '';
    }
}