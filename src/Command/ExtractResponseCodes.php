<?php

namespace Atelier\Command;

use Atelier\Command;
use Atelier\Debug;
use Atelier\Logger;
use Atelier\Machine;
use Atelier\MachineCommand;
use Atelier\Machine;
use Atelier\ProjectCommand;
use Atelier\RotatorFragment;
use Atelier\RotatorFragments;
use PHPUnit\Event\Runtime\PHP;

class ExtractResponseCodes extends ProjectCommand
{
    /**
     * sudo vim /etc/logrotate.d/nginx, change to 0655
     *
     * @param Machine $machine
     * @return string
     * @throws \Exception
     */
    public function run(Machine $project): string
    {
        $response = $project->getMachine()->getSsh()->exec("cat /var/log/nginx/8_access.log | cut -d '\"' -f3 | cut -d ' ' -f2 | sort | uniq -c | sort -r");
        $parsed = $this->parse($response);
        $project->addResponseCodes($parsed);
        Logger::info('Added "' . $this->getName() . '" ' . count($parsed) . ' response codes');

        return array_sum($parsed);
    }

    private function parse(string $response): array
    {
        $parsed = [];
        Debug::dump($response);
        foreach (explode(PHP_EOL, $response) as $line) {
            $parts = explode(' ', trim($line));
            $parsed[$parts[1] ?? ''] = $parts[0] ?? '';
        }

        return array_filter($parsed);
    }
}