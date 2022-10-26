<?php

namespace Atelier\Warning;

use Atelier\Model\Projects;
use Atelier\Machine;
use Atelier\Warning;

class OldMigration extends Warning
{
    public function __construct(array $type)
    {
        parent::__construct($type);
        $this->projects = array_map(
            fn($project) => new Machine($project),
            (new Projects())->getOldMigrationProjects()
        );
    }

    public function getProjectProblem(Machine $project): string
    {
        return $project->getLastMigrationName();
    }
}