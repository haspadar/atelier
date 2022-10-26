<?php

namespace Atelier\Warning;

use Atelier\Model\Projects;
use Atelier\Machine;
use Atelier\Project;
use Atelier\Warning;

class NotMaster extends Warning
{
    public function __construct(array $type)
    {
        parent::__construct($type);
        $this->projects = array_map(
            fn($project) => new Project($project),
            (new Projects())->getNotMasterProjects()
        );
    }

    public function getProjectProblem(Project $project): string
    {
        return $project->getLastBranchName();
    }
}