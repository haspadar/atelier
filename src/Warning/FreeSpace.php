<?php

namespace Atelier\Warning;

use Atelier\Machine;
use Atelier\Machines;
use Atelier\Model\Projects;
use Atelier\Project;
use Atelier\Warning;

class FreeSpace extends Warning
{
    /**
     * @var \Atelier\Machine[]
     */
    private array $machines;

    public function __construct(array $type)
    {
        parent::__construct($type);
        $this->machines = Machines::getMachines();
        usort(
            $this->machines,
            fn(Machine $previous, Machine $next) => $previous->getFreeSpace() > $next->getFreeSpace() ? 1 : 0
        );
    }

    /**
     * @return Machine[]
     */
    public function getMachines(): array
    {
        return $this->machines;
    }

    public function getMachineProblem(Machine $machine): string
    {
        return $machine->getFreeSpace() . '%';
    }

    public function getProblemsCount(): int
    {
        return count($this->machines);
    }
}