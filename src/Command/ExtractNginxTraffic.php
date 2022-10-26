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
            $response = $project->getMachine()->getSsh()->exec("cat $accessLog | awk '{print $4}' | uniq -c | sort -rn | head -3");
            $parsed = $this->parse($response);
            $project->addNginxTraffic($parsed);
            if ($parsed) {
                $lastTraffic = array_keys($parsed)[0];
                Logger::info('Updated "' . $this->getName() . '" nginx_traffic to ' . array_keys($parsed)[0] . 'req/sec');
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
                $response[trim($parts[0])] = new \DateTime(
                    implode('-', [$parserDateTime['year'], $parserDateTime['month'], $parserDateTime['day']])
                    . ' '
                    . implode(':', [$parserDateTime['hour'], $parserDateTime['minute'], $parserDateTime['second']])
                );
            }
        }

        return $response;
    }
}