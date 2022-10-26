<?php

use Crunz\Schedule;
use Symfony\Component\Lock\Store\FlockStore;

$schedule = new Schedule();
$task = $schedule->run(PHP_BINARY . ' bin/clear.php');
$task
    ->daily()
    ->description('Clearing old data')
    ->preventOverlapping(new FlockStore(__DIR__ . '/locks'));

return $schedule;