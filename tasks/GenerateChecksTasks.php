<?php

use Crunz\Schedule;
use Symfony\Component\Lock\Store\FlockStore;

$schedule = new Schedule();
$task = $schedule->run(PHP_BINARY . ' bin/generate_checks.php');
$task
    ->everyThirtyMinutes()
    ->description('Генерация сообщений об ошибках')
    ->preventOverlapping(new FlockStore(__DIR__ . '/locks'));

return $schedule;