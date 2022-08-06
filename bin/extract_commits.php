#!/usr/bin/php
<?php

require_once __DIR__ . '/autoload_require_composer.php';

use Atelier\Command\ExtractCommit;
use Atelier\Commands;
use Atelier\Logger;
use Atelier\Projects;

Logger::info('Update commits command started');
Commands::paltoRun(new ExtractCommit());