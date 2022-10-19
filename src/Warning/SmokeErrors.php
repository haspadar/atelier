<?php

namespace Atelier\Warning;

use Atelier\Model\Projects;
use Atelier\Project;
use Atelier\Warning;

class SmokeErrors extends Warning
{
    public function __construct(array $type)
    {
        parent::__construct($type);
        $this->projects = array_map(
            fn($project) => new Project($project),
            (new Projects())->getSmokeErrorProjects()
        );
    }

    public function getProjectProblem(Project $project): string
    {
        return $project->getSmokeLastTime()->format('d.m.Y H:i:s')
            . '<br>'
            . $project->getSmokeLastReport();
    }
}