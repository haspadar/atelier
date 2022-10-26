#!/usr/bin/php
<?php

use Atelier\Logger;

require_once __DIR__ . '/autoload_require_composer.php';
$createTimeTables = [
    'http_info',
    'nginx_traffic',
    'parser',
    'php_fpm_traffic',
    'response_codes'
];
foreach ($createTimeTables as $createTimeTable) {
    Logger::warning('Clearing table ' . $createTimeTable . '...');
    (new \Atelier\Model\Commands())->getDb()->delete(
        $createTimeTable,
        'create_time <= %s',
        (new DateTime())->modify('-6 MONTH')->format('Y-m-d H:i:s')
    );
}

Logger::warning('Clearing table run_logs...');
(new \Atelier\Model\RunLogs())->getDb()->delete(
    'run_logs',
    'start_time <= %s',
    (new DateTime())->modify('-3 MONTH')->format('Y-m-d H:i:s')
);
Logger::warning('Clearing table reports...');
(new \Atelier\Model\Reports())->getDb()->delete(
    'reports',
    'run_log_id IS NULL'
);
Logger::info('Done.');