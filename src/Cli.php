<?php

namespace Atelier;

class Cli
{
    public static function isCli(): bool
    {
        return php_sapi_name() === 'cli';
    }

    public static function isSudo(): bool
    {
        return \posix_getuid() == 0;
    }

    public static function isCron(): bool
    {
        return self::isCli() && !isset($_SERVER['TERM']);
    }

    public static function getPsGrepProcesses(string $grepPattern): array
    {
        $commands = self::getPsProcesses("ps -eo pid,lstart,etime,cmd | grep \"$grepPattern\"");
        $parsed = [];
        foreach ($commands as $command) {
            $parsed[] = [
                'pid' => $command[0],
                'command' => $command[7],
                'name' => $command[count($command) - 1],
                'run_time' => new \DateTime(implode(' ', [$command[2], $command[3], $command[5], $command[4]])),
                'work_time' => $command[6],
            ];
        }

        return $parsed;
    }

    public static function getPsProcesses(string $psCommand): array
    {
        $response = `$psCommand`;
        $lines = array_values(array_filter(explode(PHP_EOL, $response ?? '')));
        $processes = array_map(fn(string $line) => array_values(array_filter(explode(' ', $line))), $lines);
        array_pop($processes);
        array_pop($processes);

        return $processes;
    }

    /**
     * @return false|int
     */
    public static function getPid(): int|false
    {
        return \getmypid();
    }
}