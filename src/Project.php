<?php

namespace Atelier;

use Atelier\Project\ProjectType;
use DateTime;

class Project
{

    private Machine $machine;

    public function __construct(private array $project)
    {
        $this->machine = Garage::getMachine($project['machine_id']);
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

    public function getType(): string
    {
        return $this->project['type'];
    }

    public function isPalto(): bool
    {
        return $this->project['type'] == ProjectType::PALTO->name;
    }

    public function extractCommit(Ssh $ssh): string
    {
        $response = $ssh->exec("cd " . $this->getPath() . ' && git log -1 --format=%cd');
        $this->setLastCommitTime(new DateTime($response));
        Logger::info('Updated "' . $this->getName() . '" last_commit_time');

        return $response;
    }

    public function runSmoke(Ssh $ssh): string
    {
        $response = $ssh->exec('cd ' . $this->getPath() . ' && php vendor/bin/phpunit tests');
        $lastLine = $this->getLastLine($response);
        if (str_starts_with($lastLine, 'OK')) {
            Logger::info('Project ' . $this->getName() . ' smoke is OK');
            $this->setSmokeLastReport('OK');
        } else {
            Logger::error('Project ' . $this->getName() . ' smoke is ERROR: ' . $lastLine);
            $this->setSmokeLastReport($lastLine);
        }

        $this->setSmokeLastTime(new DateTime());

        return $response;
    }

    public function extractMigration(Ssh $ssh): string
    {
        $response = $ssh->exec("cd " . $this->getPath() . ' && vendor/bin/phinx status');
        $lastLine = $this->getLastLine($response);
        $words = array_values(array_filter(explode(' ', $lastLine)));
        $this->setLastMigrationName($words[1]);
        Logger::info('Updated "' . $this->getName() . '" last_migration_name');

        return $response;
    }

    private function setLastMigrationName(string $name)
    {
        $this->project['last_migration_name'] = $name;
        (new \Atelier\Model\Projects())->update([
            'last_migration_name' => $this->project['last_migration_name']
        ], $this->getId());
    }

    private function setLastCommitTime(DateTime $time)
    {
        $this->project['last_commit_time'] = $time->format('Y-m-d H:i:s');
        (new \Atelier\Model\Projects())->update([
            'last_commit_time' => $this->project['last_commit_time']
        ], $this->getId());
    }

    public function updateProject(Ssh $ssh): string
    {
        $response = $ssh->exec("cd "
            . $this->getPath()
            . " && git clean -d  -f . && git pull && vendor/bin/phinx migrate -c "
            . $this->getPath()
            . '/phinx.php'
        );
        $this->extractCommit($ssh);
        $this->extractMigration($ssh);

        return $response;
    }

    private function getLastLine(string $response): string
    {
        $lines = array_values(array_filter(explode(PHP_EOL, $response)));

        return $lines[count($lines) - 1];
    }

    private function setSmokeLastReport(string $report)
    {
        $this->project['smoke_last_report'] = $report;
        (new \Atelier\Model\Projects())->update([
            'smoke_last_report' => $this->project['smoke_last_report']
        ], $this->getId());
    }

    private function setSmokeLastTime(DateTime $time)
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