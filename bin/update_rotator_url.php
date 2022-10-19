#!/usr/bin/php
<?php

use Atelier\Command\ExtractRotatorFragments;
use Atelier\Command\UpdateRotatorUrl;
use Atelier\Commands;
use League\CLImate\CLImate;

require_once __DIR__ . '/autoload_require_composer.php';

$oldUrl = (new CLImate())
    ->yellow()
    ->input('Введите старый адрес (без ключа):')
    ->prompt();
$newUrl = (new CLImate())
    ->yellow()
    ->input('Введите новый адрес (без ключа):')
    ->prompt();
(new UpdateRotatorUrl(['old_url' => $oldUrl, 'new_url' => $newUrl]))->runForAll();