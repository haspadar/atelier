<?php

namespace Atelier\Command;

use Atelier\Command;
use Atelier\Debug;
use Atelier\Logger;
use Atelier\Project;
use Atelier\ProjectCommand;
use Atelier\RotatorFragment;
use Atelier\RotatorFragments;
use Atelier\Validator;

class UpdateRotatorUrl extends ProjectCommand
{
    public function run(Project $project): string
    {
        try {
            $oldUrl = $this->options['old_url'];
            if (!Validator::isUrlValid($oldUrl)) {
                throw new Exception('Ошибка: неверный старый адрес');
            }

            $newUrl = $this->options['new_url'];
            if (!Validator::isUrlValid($newUrl)) {
                throw new Exception('Ошибка: неверный новый адрес');
            }

            return $this->replaceFragments($project, $oldUrl, $newUrl);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}