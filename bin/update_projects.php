#!/usr/bin/php
<?php

require_once __DIR__ . '/autoload_require_composer.php';

use Atelier\Commands;
use Atelier\Logger;

Logger::info('Update migrations command started');
Commands::updateProjects();