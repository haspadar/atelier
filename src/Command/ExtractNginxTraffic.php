<?php

namespace Atelier\Command;

use Atelier\Cli;
use Atelier\Command;
use Atelier\Debug;
use Atelier\Logger;
use Atelier\Machine;
use Atelier\MachineCommand;
use Atelier\Project;
use Atelier\ProjectCommand;
use Atelier\Projects;

class ExtractNginxTraffic extends ProjectCommand
{
    public function run(Project $project): string
    {
        $accessLog = $project->getAccessLog();
        if ($accessLog) {
            $lastMinute = (new \DateTime())->modify('-1 MINUTE');
            $command = "cat $accessLog | grep '"
                . $lastMinute->format('d/M/Y:H:i')
                . "' | awk '{print $4}' | uniq -c";
            $response = $project->getMachine()->getSsh()->exec($command);
            $parsed = [$lastMinute->format('Y-m-d H:i') => array_sum($this->parse($response))];
            $project->addNginxTraffic($parsed);
            if ($parsed) {
                $traffic = bcdiv(array_sum(array_values($parsed)), 60, 2);
                Logger::info('Added "' . $this->getName() . '" nginx_traffic: ' . $traffic . ' req/sec');
            } else {
                Logger::warning('Ignored response for ' . $project->getName() . ', command: "' . $command . '"');
            }

            return $traffic ?? $response;
        } else {
            Logger::warning('Ignored empty access_log for project ' . $this->getName());

            return '';
        }
    }

    private function parse(string $result): array
    {
        $response = [];
        foreach (array_filter(explode(PHP_EOL, $result)) as $line) {
            if (str_contains($line, '[')) {
                $parts = explode('[', $line);
                $parserDateTime = date_parse_from_format('d/M/Y:H:i:s', trim($parts[1]));
                $dateTime = implode('-', [$parserDateTime['year'], $parserDateTime['month'], $parserDateTime['day']])
                    . ' '
                    . implode(':', [
                        $this->addZero($parserDateTime['hour']),
                        $this->addZero($parserDateTime['minute']),
                        $this->addZero($parserDateTime['second'])
                    ]);
                $response[$dateTime] = trim($parts[0]);
            }
        }

        return $response;
    }

    private function addZero(string $number): string
    {
        if ($number < 10) {
            return '0' . intval($number);
        }

        return $number;
    }

    private function generateEmptyValues(\DateTime $lastMinute): array
    {
        $emptyValues = [];
        for ($seconds = 0; $seconds <= 59; $seconds++) {
            $emptyValues[$lastMinute->format('Y-m-d H:i') . ':' . $this->addZero($seconds)] = 0;
        }

        return $emptyValues;
    }

}