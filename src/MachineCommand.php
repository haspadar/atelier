<?php

namespace Atelier;

abstract class MachineCommand extends Command
{
    /**
     * @var Machine[]
     */
    protected array $machines;

    public function __construct(protected array $options = [])
    {
        parent::__construct($this->options);
        $this->machines = Machines::getMachines();
    }

    public function getMachines(): array
    {
        return $this->machines;
    }

    abstract public function run(Machine $machine): string;

    public function getDescription(): array
    {
        return array_merge(parent::getDescription(), [
            'Машины' => implode(',', array_map(fn(Machine $machine) => $machine->getHost(), Machines::getMachines()))
        ]);
    }

    /**
     * @param Machine[] $machines
     * @return CommandReport|null
     */
    public function runForAll(array $machines = []): ?CommandReport
    {
        try {
            $time = new ExecutionTime();
            $time->start();
            Logger::info('Command ' . $this->getName() . ' started for every machine');
            $machines = $machines ?: Machines::getMachines();
            $report = self::runForMachines($machines);
            $time->end();
            Logger::info('Command '
                . $this->getName()
                . ' processed '
                . count($machines)
                . ' machines and finished for '
                . $time->get()
            );
        } catch (\Exception $e) {
            Logger::error($e->getMessage());
            Logger::error($e->getTraceAsString());
        }

        return $report ?? null;
    }

    /**
     * @param Machine[] $machines
     */
    private function runForMachines(array $machines): ?CommandReport
    {
        declare(ticks = 10) {
            $run = new Run();
            register_tick_function([$run, 'ping']);
            foreach ($machines as $machine) {
                $ssh = $machine->createSsh();
                if (!$ssh->getError()) {
                    $report = CommandReports::add($this, null, $machine, $run);
                    Logger::debug('Run for ' . $machine->getHost() . '...');
                    try {
                        $response = $this->run($machine);
                    } catch (\phpseclib3\Exception\InvalidArgumentException $e) {
                        $response = 'Can\'t connect to ' . $machine->getHost() . ' via ssh – ignored!';
                        Logger::error($response);
                    }

                    $report->finish($response);
                } else {
                    Logger::error($machine->getHost() . ': ' . $ssh->getError());
                }
            }

            $run->finish();
        }

        return $report ?? null;
    }
}