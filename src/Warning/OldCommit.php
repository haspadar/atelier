<?php

namespace Atelier\Warning;

use Atelier\Model\Projects;
use Atelier\Project;
use Atelier\Warning;

class OldCommit extends Warning
{
    public function __construct(array $type)
    {
        parent::__construct($type);
        $this->projects = array_map(fn($project) => new Project($project), (new Projects())->getOldCommitProjects());
    }

    public function getProjectProblem(Project $project): string
    {
        return $project->getLastCommit()->format('d.m.Y H:i:s') . ', ветка ' . $project->getLastBranchName();
    }
}