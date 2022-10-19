<?php

use Crunz\Schedule;
use Palto\Sitemap;
use Symfony\Component\Lock\Store\FlockStore;

$schedule = new Schedule();

$task = $schedule->run(PHP_BINARY . ' bin/extract_commit.php');
$task
    ->daily()
    ->description('Extract last git commit')
    ->preventOverlapping(new FlockStore(__DIR__ . '/locks'));

return $schedule;