<?php

namespace Atelier\Command;

use Atelier\Cli;
use Atelier\Command;
use Atelier\Debug;
use Atelier\Logger;
use Atelier\Machine;
use Atelier\MachineCommand;
use Atelier\Machine;

class ExtractPhpVersion extends MachineCommand
{
    public function run(Machine $machine): string
    {
        $response = $machine->getSsh()->exec("php -v | grep PHP | awk '{print $2}'");
        $version = explode('-', explode(PHP_EOL, $response)[0])[0];
        $machine->setPhpVersion($version);
        Logger::info('Updated "' . $this->getName() . '" php_version to ' . $version);

        return $version;
    }
}