<?php

namespace Atelier\Command;

use Atelier\Command;
use Atelier\Debug;
use Atelier\Logger;
use Atelier\Machine;
use Atelier\ProjectCommand;
use Atelier\RotatorFragment;
use Atelier\RotatorFragments;
use Atelier\Validator;

class UpdatePaltoSetting extends ProjectCommand
{
    public function run(Machine $project): string
    {
        try {
            $name = $this->options['name'];
            $value = $this->options['value'];
            if ($error = $this->replacePaltoSetting($project, $name, $value)) {
                Logger::error($error);
            } else {
                Logger::info($name . ' changed on ' . $project->getName());
            }

            return $error ?? $value;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}