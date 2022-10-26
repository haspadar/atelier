<?php

namespace Atelier\Command;

use Atelier\Cli;
use Atelier\Command;
use Atelier\Logger;
use Atelier\Machine;
use Atelier\MachineCommand;

class ExtractFreeSpace extends MachineCommand
{
    public function run(Machine $machine): string
    {
        $response = $machine->getSsh()->exec("df -Ph /");
        $percent = $this->extractPercent($response);
        $machine->setFreeSpace($percent);
        Logger::info('Updated "' . $this->getName() . '" free_space to ' . $percent . '%');

        return $percent;
    }

    private function extractPercent(string $result): int
    {
        $lines = array_filter(explode(PHP_EOL, $result));
        $values = explode(' ', $lines[1]);
        foreach ($values as $value) {
            if (str_contains($value, '%')) {
                return 100 - intval(str_replace('%', '', $value));
            }
        }

        return 100;
    }
}