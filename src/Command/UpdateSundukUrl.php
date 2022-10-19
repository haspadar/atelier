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

class UpdateSundukUrl extends ProjectCommand
{
    public function run(Project $project): string
    {
        try {
            $newUrl = $this->options['url'];
            if (!Validator::isUrlValid($newUrl)) {
                throw new Exception('Ошибка: неверный новый адрес');
            }

            if ($project->isPalto()) {
                if ($error = $this->replacePaltoSetting($project, 'sunduk_url', $newUrl)) {
                    Logger::error($error);
                } else {
                    Logger::info('sunduk_url changed on ' . $project->getName());
                }
            }

            Logger::debug('Ignored not palto project');

            return '';
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}