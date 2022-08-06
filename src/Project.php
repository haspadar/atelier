<?php

namespace Atelier;

use Atelier\Project\ProjectType;
use DateTime;

class Project
{

    private Machine $machine;

    public function __construct(private array $project)
    {
        $this->machine = Machines::getMachine($project['machine_id']);
    }

    public function getMachine(): Machine
    {
        return $this->machine;
    }
    
    public function getId(): int
    {
        return $this->project['id'];
    }

    public function getPath(): string
    {
        return $this->project['path'];
    }

    public function getName(): string
    {
        return str_replace('/var/www/', '', $this->getPath());
    }

    public function getTypeId(): int
    {
        return $this->project['type_id'];
    }

    public function getTypeName(): string
    {
        return $this->project['type_name'];
    }

    public function isPalto(): bool
    {
        return $this->project['type'] == ProjectType::PALTO->name;
    }

    public function setLastMigrationName(string $name)
    {
        $this->project['last_migration_name'] = $name;
        (new \Atelier\Model\Projects())->update([
            'last_migration_name' => $this->project['last_migration_name']
        ], $this->getId());
    }

    public function setLastCommitTime(DateTime $time)
    {
        $this->project['last_commit_time'] = $time->format('Y-m-d H:i:s');
        (new \Atelier\Model\Projects())->update([
            'last_commit_time' => $this->project['last_commit_time']
        ], $this->getId());
    }

    public function getLastLine(string $response): string
    {
        $lines = array_values(array_filter(explode(PHP_EOL, $response)));

        return $lines[count($lines) - 1];
    }

    public function setSmokeLastReport(string $report)
    {
        $this->project['smoke_last_report'] = $report;
        (new \Atelier\Model\Projects())->update([
            'smoke_last_report' => $this->project['smoke_last_report']
        ], $this->getId());
    }

    public function setSmokeLastTime(DateTime $time)
    {
        $this->project['smoke_last_report'] = $time->format('Y-m-d H:i:s');
        (new \Atelier\Model\Projects())->update([
            'smoke_last_time' => $this->project['smoke_last_report']
        ], $this->getId());
    }

    public function getLastCommit(): ?DateTime
    {
        return $this->project['last_commit_time']
            ? new DateTime($this->project['last_commit_time'])
            : null;
    }

    public function getLastMigrationName(): string
    {
        return $this->project['last_migration_name'] ?? '';
    }

    public function getSmokeLastTime(): ?DateTime
    {
        return $this->project['smoke_last_time']
            ? new DateTime($this->project['smoke_last_time'])
            : null;
    }

    public function getSmokeLastReport(): string
    {
        return $this->project['smoke_last_report'] ?? '';
    }
}