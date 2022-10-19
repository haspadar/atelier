#!/usr/bin/php
<?php

use Atelier\Command\UpdateSundukUrl;
use League\CLImate\CLImate;

require_once __DIR__ . '/autoload_require_composer.php';

$newUrl = (new CLImate())
    ->yellow()
    ->input('Введите новый адрес Сундука:')
    ->prompt();
(new UpdateSundukUrl(['url' => $newUrl]))->runForAll();