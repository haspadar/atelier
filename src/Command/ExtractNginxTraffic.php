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

class ExtractNginxTraffic extends ProjectCommand
{
    public function run(Project $project): string
    {
        $accessLog = $project->getAccessLog();
        if ($accessLog) {
            $lastMinute = (new \DateTime());
            $response = $project->getMachine()->getSsh()->exec("cat $accessLog | grep '"
                . $lastMinute->format('d/M/Y:H:i')
                . "' | awk '{print $4}' | uniq -c"
            );
            $parsed = $this->parse($response);
            $emptyValues = $this->generateEmptyValues($lastMinute);
            $fullHour = array_merge($emptyValues, $parsed);
            $project->addNginxTraffic($fullHour);
            if ($parsed) {
                $lastTraffic = array_keys($fullHour)[0];
                $traffic = bcdiv(array_sum(array_values($fullHour)), 60, 2);
                Logger::info('Updated "' . $this->getName() . '" nginx_traffic to ' . $traffic . 'req/sec');
            } else {
                Logger::warning('Ignored response for ' . $project->getName());
            }

            return $lastTraffic ?? $response;
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