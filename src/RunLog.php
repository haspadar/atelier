<?php

namespace Atelier;

class RunLog
{
    /**
     * @var Machine[]
     */
    private array $projects;

    /**
     * @var Command[]
     */
    private array $commands;
    /**
     * @var CommandReport[]
     */
    private array $reports;

    public function __construct(private readonly array $runLog)
    {
        $this->projects = array_map(
            fn($project) => new Project($project),
            (new \Atelier\Model\CommandReports())->getRunLogProjects($this->runLog['id'])
        );
        $this->commands = array_map(
            fn($command) => Commands::createCommand($command),
            (new \Atelier\Model\CommandReports())->getRunLogCommands($this->runLog['id'])
        );
        $this->reports = array_map(
            fn($report) => new CommandReport($report),
            (new \Atelier\Model\CommandReports())->getRunLogReports($this->runLog['id'])
        );
    }

    public function getId(): int
    {
        return $this->runLog['id'];
    }

    public function getCommands(): array
    {
        return $this->commands;
    }

    public function getReports(): array
    {
        return $this->reports;
    }

    public function getProjects(): array
    {
        return $this->projects;
    }

    public function getStartTime(): \DateTime
    {
        return new \DateTime($this->runLog['start_time']);
    }

    public function getFinishTime(): ?\DateTime
    {
        return $this->runLog['finish_time'] ? new \DateTime($this->runLog['finish_time']) : null;
    }

    public function getPingTime(): ?\DateTime
    {
        return $this->runLog['ping_time']
            ? new \DateTime($this->runLog['ping_time'])
            : null;
    }

    public function getMemory(): string
    {
        return $this->runLog['memory'];
    }

    public function getMemoryHuman(): string
    {
        return $this->runLog['memory_human'];
    }

    public function getUser(): string
    {
        return $this->runLog['user'];
    }

    public function getScript(): string
    {
        return $this->runLog['script'];
    }

    public function getPid(): int
    {
        return $this->runLog['pid'];
    }

    public function isCron(): bool
    {
        return $this->runLog['is_cron'] == 1;
    }

    public function isCli(): bool
    {
        return $this->runLog['is_cli'] == 1;
    }
}