<?php

use Atelier\Command\ExtractGit;
use Crunz\Schedule;
use Symfony\Component\Lock\Store\FlockStore;

$command = new \Atelier\Command\ExtractParserAds();
$schedule = new Schedule();
$task = $schedule->run(PHP_BINARY . ' bin/' . $command->getScript());
$task
    ->hourly()
    ->description($command->getComment())
    ->preventOverlapping(new FlockStore(__DIR__ . '/locks'));

return $schedule;