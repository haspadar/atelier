<?php

namespace Atelier;

class Machines
{
    /**
     * @return Machine[]
     */
    public static function getMachines(array $ids = []): array
    {
        return array_map(fn($machine) => new Machine($machine), (new \Atelier\Model\Machines())->getAll($ids));
    }

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

    public static function updateMachine(string $host, string $ip, int $id): void
    {
        (new \Atelier\Model\Machines())->update([
            'host' => $host,
            'ip' => $ip,
        ], $id);
    }

    public static function delete(int $id): void
    {
        (new \Atelier\Model\Machines())->remove($id);
    }

    public static function addMachine(string $host, string $ip): Machine
    {
        $id = (new \Atelier\Model\Machines())->add([
            'host' => $host,
            'ip' => $ip,
            'create_time' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);

        return self::getMachine($id);
    }

    public static function getMachineByHost(string $host): ?Machine
    {
        $machine = (new \Atelier\Model\Machines())->getBy('host', $host);

        return $machine ? new Machine($machine) : null;
    }

    public static function getMachineByIp(string $ip): ?Machine
    {
        $machine = (new \Atelier\Model\Machines())->getBy('ip', $ip);

        return $machine ? new Machine($machine) : null;
    }

    public static function getMachine(int $id): Machine
    {
        return new Machine((new \Atelier\Model\Machines())->getById($id));
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

    public function track(callable $logic)
    {
        $executionTime = new ExecutionTime();
        $executionTime->start();
        $logic();
        $executionTime->end();
        Logger::warning('Changed on ' . count($this->machines) . ' machines for ' . $executionTime->get());
    }
}