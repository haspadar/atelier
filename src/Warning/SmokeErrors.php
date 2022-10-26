<?php

namespace Atelier\Warning;

use Atelier\Model\Projects;
use Atelier\Machine;
use Atelier\Warning;

class SmokeErrors extends Warning
{
    public function __construct(array $type)
    {
        parent::__construct($type);
        $this->projects = array_map(
            fn($project) => new Machine($project),
            (new Projects())->getSmokeErrorProjects()
        );
    }

    public function getProjectProblem(Machine $project): string
    {
        return $project->getSmokeLastTime()->format('d.m.Y H:i:s')
            . '<br>'
            . $project->getSmokeLastReport();
    }
}