#!/usr/bin/php
<?php

use Atelier\Command\UpdateRotatorKey;
use Atelier\Command\UpdateRotatorUrl;
use Atelier\Commands;
use League\CLImate\CLImate;

require_once __DIR__ . '/autoload_require_composer.php';

$oldKey = (new CLImate())
    ->yellow()
    ->input('Введите старый ключ:')
    ->prompt();
$newKey = (new CLImate())
    ->yellow()
    ->input('Введите новый ключ:')
    ->prompt();
(new UpdateRotatorKey(['old_key' => $oldKey, 'new_key' => $newKey]))->runForAll();