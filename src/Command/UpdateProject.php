<?php

namespace Atelier\Command;

use Atelier\Command;
use Atelier\Project;

class UpdateProject extends Command
{
    public function run(Project $project): string
    {
        $response = $project->getMachine()->getSsh()->exec("cd "
            . $project->getPath()
            . " && git clean -d  -f . && git pull && vendor/bin/phinx migrate -c "
            . $project->getPath()
            . '/phinx.php'
        );
        (new ExtractCommit())->run($project);
        (new ExtractMigration())->run($project);

        return $response;
    }
}