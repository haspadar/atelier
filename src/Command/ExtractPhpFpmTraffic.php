<?php

namespace Atelier\Command;

use Atelier\Cli;
use Atelier\Command;
use Atelier\Debug;
use Atelier\Logger;
use Atelier\Machine;
use Atelier\MachineCommand;
use Atelier\Project;

class ExtractPhpFpmTraffic extends MachineCommand
{
    public function run(Machine $machine): string
    {
        $response = $machine->getSsh()->exec("fpm=$(dpkg -l | grep php | grep fpm | awk '{ print $2 }');service \$fpm status");
        $parsedTraffic = $this->parseTraffic($response);
        $traffic = isset($parsedTraffic['Status']) ? floatval(explode('Traffic:', $parsedTraffic['Status'])[1]) : 0;
        $parsedActiveTime = $this->parseActiveTime($response);
        $time = explode(';', explode('since ', $parsedActiveTime['Active'])[1])[0];
        $activeDateTime = $time ? new \DateTime($time) : null;
        $machine->addPhpFpmTraffic($traffic, $activeDateTime);
        Logger::info('Updated "'
            . $this->getName()
            . '" php_fpm_traffic to '
            . $traffic
            . 'req/sec, php_fpm_active_time to '
            . $activeDateTime->format('Y-m-d H:i:s')
        );

        return $traffic;
    }

    private function parseTraffic(string $result): array
    {
        $response = [];
        foreach (array_filter(explode(PHP_EOL, $result)) as $line) {
            if (str_contains($line, ':')) {
                $parts = explode(':', $line);
                $response[trim($parts[0])] = trim($line);
            }
        }

        return $response;
    }

    private function parseActiveTime(string $result): array
    {
        $response = [];
        foreach (array_filter(explode(PHP_EOL, $result)) as $line) {
            if (str_contains($line, ':')) {
                $parts = explode(':', $line);
                $response[trim($parts[0])] = trim($line);
            }
        }

        return $response;
    }
}