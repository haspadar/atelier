<?php

namespace Atelier\Command;

use Atelier\Cli;
use Atelier\Command;
use Atelier\Debug;
use Atelier\Machine;
use Atelier\ProjectCommand;
use Atelier\RotatorFragment;
use Atelier\RotatorFragments;
use League\CLImate\CLImate;

class UpdateRotatorKey extends ProjectCommand
{
    public function run(Machine $project): string
    {
        $oldKey = $this->options['old_key'];
        $newKey = $this->options['new_key'];

        return $this->replaceFragments($project, $oldKey, $newKey);
    }

}