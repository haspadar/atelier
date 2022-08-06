#!/usr/bin/php
<?php

require_once __DIR__ . '/autoload_require_composer.php';

use Atelier\Command\UpdateProject;
use Atelier\Commands;
use Atelier\Logger;

Logger::info('Update migrations command started');
Commands::paltoRun(new UpdateProject());