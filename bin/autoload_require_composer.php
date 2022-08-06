<?php
$autoloadFile = __DIR__ . '/../vendor/autoload.php';
if (!is_file($autoloadFile)) {
    echo 'Run `composer update` first' . PHP_EOL;

    exit;
}

date_default_timezone_set('Europe/Minsk');
require_once $autoloadFile;