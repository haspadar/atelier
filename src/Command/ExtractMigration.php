<?php

namespace Atelier\Command;

use Atelier\Command;
use Atelier\Logger;
use Atelier\Project;

class ExtractMigration extends Command
{
    public function run(Project $project): string
    {
        $response = $project->getMachine()->getSsh()->exec(
            "cd " . $project->getPath() . ' && vendor/bin/phinx status'
        );
        $lastLine = $project->getLastLine($response);
        $words = array_values(array_filter(explode(' ', $lastLine)));
        $project->setLastMigrationName($words[1]);
        Logger::info('Updated "' . $this->getName() . '" last_migration_name');

        return $response ?? '';
    }
}