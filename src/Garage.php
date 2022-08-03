<?php

namespace Atelier;

use Atelier\Model\Machines;
use Mole\Project;

class Garage
{
    public const ROTATOR_MACHINES = 'ROTATOR_MACHINES';

    public const PALTO_MACHINES = 'PALTO_MACHINES';

    /**
     * @var Machine[]
     */
    private array $machines;

    private string $password;

//    public function __construct(string $machinesOption, string $defaultOptionName = '')
//    {
//        $this->machines = self::generateMachines($this->parseMachinesOption($machinesOption ?: $defaultOptionName));
//        $this->password = $this->promptMachineSudoPassword();
//        foreach ($this->machines as $machine) {
//            $machine->setPassword($this->password);
//        }
//    }

    /**
     * @return Machine[]
     */
    public static function getMachines(): array
    {
        return array_map(fn($machine) => new Machine($machine), (new Machines())->getAll());
    }

//    /**
//     * @return Machine[]
//     */
//    public function getMachines(): array
//    {
//        return $this->machines;
//    }

    /**
     * @param array $names
     * @return Machine[]
     */
    public static function generateMachines(array $names): array
    {
        $machineNames = [];
        $projectNames = [];
        foreach ($names as $machineWithProject) {
            $parts = explode(':', $machineWithProject);
            $machineName = $parts[0];
            $projectName = $parts[1] ?? '';
            if (!in_array($machineName, $machineNames)) {
                $machineNames[] = $machineName;
            }

            if ($projectName && (!isset($projectNames[$machineName]) || !in_array($projectName, $projectNames[$machineName]))) {
                $projectNames[$machineName][] = $projectName;
            }
        }

        $machines = [];
        foreach ($machineNames as $machineName) {
            $machines[] = new Machine($machineName, $projectNames[$machineName] ?? []);
        }

        return $machines;
    }

    public static function parseMachinesOption(string $option): array
    {
        $machineNames = [];
        foreach (explode(',', $option) as $machineWithProjectName) {
            if ($machineWithProjectName && !in_array($machineWithProjectName, $machineNames)) {
                $machineNames[] = $machineWithProjectName;
            }
        }

        return $machineNames;
    }

    public static function getMachine(int $id): Machine
    {
        return new Machine((new Machines())->getById($id));
    }

    public function getOptionProjects(string $option): array
    {
        $projectsNames = [];
        foreach (explode(',', $option) as $machineWithProjectName) {
            $machineName = explode(':', $machineWithProjectName)[0];
            if ($machineName && !in_array($machineName, $projectsNames)) {
                $projectsNames[] = $machineName;
            }
        }

        return $projectsNames;
    }

    public function forEveryProject(callable $projectLogic, bool $isPaltoOnly = false)
    {
        $this->track(function () use ($projectLogic, $isPaltoOnly) {
            foreach ($this->machines as $machineKey => $machine) {
                $logMessage = 'Machine ' . $machine->getHost() . ' (' . ($machineKey + 1) . '/' . count($this->machines) . ')';
                Logger::garageInfo($logMessage);
                $machine->forEveryProject($projectLogic, $isPaltoOnly);
            }
        });
    }

    public function track(callable $logic)
    {
        $executionTime = new ExecutionTime();
        $executionTime->start();
        $logic();
        $executionTime->end();
        Logger::warning('Changed on ' . count($this->machines) . ' machines for ' . $executionTime->get());
    }

    private function promptMachineSudoPassword(): string
    {
        return $this->machines[0]->promptPassword();
    }
}