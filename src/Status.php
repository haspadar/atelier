<?php
namespace Atelier;

use Atelier\Cli;

class Status
{
    public static function hasPhpProcess(string $name): bool
    {
        foreach (self::getPhpProcesses() as $process) {
            if ($process['name'] == $name) {
                return true;
            }
        }

        return false;
    }

    public static function getPhpProcesses(): array
    {
        $processes = Cli::getPsGrepProcesses('/usr/bin/php');

        return array_values(array_filter($processes, fn(array $process) => $process['command'] != 'sh'));
    }
}