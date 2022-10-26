#!/usr/bin/php
<?php

use Atelier\Command\UpdatePaltoSetting;
use League\CLImate\CLImate;

require_once __DIR__ . '/autoload_require_composer.php';

$settingName = (new CLImate())
    ->yellow()
    ->input('Введите название настройки:')
    ->prompt();
$settingValue = (new CLImate())
    ->yellow()
    ->input('Введите новое значение:')
    ->prompt();
(new UpdatePaltoSetting(['name' => $settingName, 'value' => $settingValue]))->runForAll();