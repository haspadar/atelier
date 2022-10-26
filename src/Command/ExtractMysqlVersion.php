<?php

namespace Atelier\Command;

use Atelier\Cli;
use Atelier\Command;
use Atelier\Debug;
use Atelier\Logger;
use Atelier\Machine;
use Atelier\MachineCommand;
use Atelier\Machine;

class ExtractMysqlVersion extends MachineCommand
{
    public function run(Machine $machine): string
    {
        $response = $machine->getSsh()->exec("mysql -V");
        $firstPart = explode(',', $response)[0];
        $version = explode(' ', $firstPart)[5] ?? '';
        $machine->setMysqlVersion($version);
        Logger::info('Updated "' . $this->getName() . '" mysql_version to ' . $version);

        return $version;
    }
}