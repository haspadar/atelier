<?php

use Atelier\Command\ExtractGit;
use Crunz\Schedule;
use Symfony\Component\Lock\Store\FlockStore;

$command = new \Atelier\Command\ExtractMysqlVersion();
$schedule = new Schedule();
$task = $schedule->run(PHP_BINARY . ' bin/' . $command->getScript());
$task
    ->weekly()
    ->description($command->getComment())
    ->preventOverlapping(new FlockStore(__DIR__ . '/locks'));

return $schedule;