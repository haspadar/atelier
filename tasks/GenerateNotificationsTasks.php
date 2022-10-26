<?php

use Crunz\Schedule;
use Symfony\Component\Lock\Store\FlockStore;

$schedule = new Schedule();
$task = $schedule->run(PHP_BINARY . ' bin/generate_messages.php');
$task
    ->everyTenMinutes()
    ->between('09:00', '22:00')
    ->description('Генерация уведомлений')
    ->preventOverlapping(new FlockStore(__DIR__ . '/locks'));

return $schedule;