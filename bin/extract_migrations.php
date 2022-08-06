#!/usr/bin/php
<?php

use Atelier\Command\ExtractMigration;
use Atelier\Commands;
use Atelier\Logger;

require_once __DIR__ . '/autoload_require_composer.php';

Logger::info('Update migrations command started');
Commands::paltoRun(new ExtractMigration());