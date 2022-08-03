#!/usr/bin/php
<?php

use Atelier\Commands;
use Atelier\Logger;

require_once __DIR__ . '/autoload_require_composer.php';

Logger::info('Update smokes command started');
Commands::runSmokes();