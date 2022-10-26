<?php

namespace Atelier\Command;

use Atelier\Command;
use Atelier\Machine;
use Atelier\ProjectCommand;
use Atelier\RotatorFragment;
use Atelier\RotatorFragments;

class ExtractRotatorFragments extends ProjectCommand
{
    public function run(Machine $project): string
    {
        $ssh = $project->getMachine()->getSsh();
        RotatorFragments::removeProject($project);
        $rotatorFragments = RotatorFragments::findDirectoryFragments($ssh, $project->getPath());
        foreach ($rotatorFragments as $fragment) {
            RotatorFragments::add($fragment, $project);
        }

        return implode(
            PHP_EOL . PHP_EOL,
            array_map(fn(RotatorFragment $fragment) => $fragment->getFragment(), $rotatorFragments)
        );
    }
}