<?php

namespace Atelier\Command;

use Atelier\Command;
use Atelier\Project;
use Atelier\ProjectCommand;

class UpdateProject extends ProjectCommand
{
    public function run(Project $project): string
    {
        $response = $project->getMachine()->getSsh()->exec("cd "
            . $project->getPath()
            . " && git clean -d  -f . && git pull && vendor/bin/phinx migrate -c "
            . $project->getPath()
            . '/phinx.php'
        );
        (new ExtractGit())->run($project);
        (new ExtractMigration())->run($project);

        return $response;
    }
}