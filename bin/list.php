#!/usr/bin/php
<?php

use Atelier\Command;
use Atelier\Commands;
use League\CLImate\CLImate;

require_once __DIR__ . '/autoload_require_composer.php';

$cliMate = new CLImate();
$cliMate->green()->table(array_map(fn(Command $command) => $command->getDescription(), Commands::getCommands()));
