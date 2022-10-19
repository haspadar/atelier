<?php

namespace Atelier\Command;

use Atelier\Cli;
use Atelier\Command;
use Atelier\Debug;
use Atelier\Project;
use Atelier\ProjectCommand;
use Atelier\RotatorFragment;
use Atelier\RotatorFragments;
use League\CLImate\CLImate;

class UpdateRotatorKey extends ProjectCommand
{
    public function run(Project $project): string
    {
        $oldKey = $this->options['old_key'];
        $newKey = $this->options['new_key'];

        return $this->replaceFragments($project, $oldKey, $newKey);
    }

}