<?php

use Atelier\Command\ExtractGit;
use Crunz\Schedule;
use Symfony\Component\Lock\Store\FlockStore;

$command = new ExtractGit();
$schedule = new Schedule();
$task = $schedule->run(PHP_BINARY . ' bin/' . $command->getScript());
$task
    ->daily()
    ->description($command->getComment())
    ->preventOverlapping(new FlockStore(__DIR__ . '/locks'));

return $schedule;