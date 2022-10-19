<?php

namespace Atelier;

use Atelier\Model\NginxTraffic;
use Atelier\Model\Parser;
use Atelier\Model\ProjectTypes;
use Atelier\Model\ResponseCodes;
use Atelier\Project\Type;
use DateTime;

class Project
{

    private Machine $machine;
    private \Atelier\ProjectType $projectType;

    public function __construct(private array $project)
    {
        $this->machine = Machines::getMachine($project['machine_id']);
        $this->projectType = new \Atelier\ProjectType((new ProjectTypes())->getById($this->project['type_id']));
    }

    public function addNginxTraffic(array $countsWithDates)
    {
        $nginxTraffic = new NginxTraffic();
        foreach ($countsWithDates as $visitsCount => $logDateTime) {
            if (!$nginxTraffic->has($this->getId(), $logDateTime)) {
                $nginxTraffic->add([
                    'project_id' => $this->getId(),
                    'log_time' => $logDateTime->format('Y-m-d H:i:s'),
                    'traffic' => $visitsCount,
                    'create_time' => (new DateTime())->format('Y-m-d H:i:s')
                ]);
            }
        }
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

    public function getAddress(): string
    {
        return 'https://www.' . $this->getName();
    }

    public function getName(): string
    {
        return str_replace('/var/www/', '', $this->getPath());
    }

    public function getType(): \Atelier\ProjectType
    {
        return $this->projectType;
    }

    public function getTypeName(): string
    {
        return $this->project['type_name'];
    }

    public function isPalto(): bool
    {
        return $this->getTypeName() == strtolower(Type::PALTO->name);
    }

    public function setLastMigrationName(string $name)
    {
        $this->project['last_migration_name'] = $name;
        (new \Atelier\Model\Projects())->update([
            'last_migration_name' => $this->project['last_migration_name']
        ], $this->getId());
    }

    public function setLastBranchName(string $name)
    {
        $this->project['last_branch_name'] = $name;
        (new \Atelier\Model\Projects())->update([
            'last_branch_name' => $this->project['last_branch_name']
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

    public function getLastBranchName(): string
    {
        return $this->project['last_branch_name'] ?? '';
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

    public function addResponseCodes(array $parsed): void
    {
        $createTime = new DateTime();
        foreach ($parsed as $code => $count) {
            (new ResponseCodes())->add([
                'project_id' => $this->getId(),
                'code' => intval($code),
                'count' => intval($count),
                'create_time' => $createTime->format('Y-m-d H:i:s')
            ]);
        }
    }

    public function setErrorLog(string $errorLog)
    {
        $this->project['error_log'] = $errorLog;
        (new \Atelier\Model\Projects())->update([
            'error_log' => $this->project['error_log']
        ], $this->getId());
    }

    public function setNginxConfig(string $nginxConfig)
    {
        $this->project['nginx_config'] = $nginxConfig;
        (new \Atelier\Model\Projects())->update([
            'nginx_config' => $this->project['nginx_config']
        ], $this->getId());
    }

    public function setAccessLog(string $accessLog)
    {
        $this->project['access_log'] = $accessLog;
        (new \Atelier\Model\Projects())->update([
            'access_log' => $this->project['access_log']
        ], $this->getId());
    }

    public function getAccessLog(): string
    {
        return $this->project['access_log'];
    }

    public function addParserAdsCount(string $hourAdsCount): void
    {
        (new Parser())->add([
            'project_id' => $this->getId(),
            'hour_ads_count' => $hourAdsCount,
            'create_time' => (new DateTime())->format('Y-m-d H:i:s')
        ]);
    }

    public function setHttpHeaders(int $httpCode, string $cacheHeader, DateTime $createTime): void
    {
        (new \Atelier\Model\Projects())->update([
            'http_code' => $httpCode,
            'cache_header' => $cacheHeader,
            'header_time' => $createTime->format('Y-m-d H:i:s')
        ], $this->getId());
    }
}