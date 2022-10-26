<?php

namespace Atelier\Command;

use Atelier\Debug;
use Atelier\Logger;
use Atelier\Machine;
use Atelier\ProjectCommand;

class ExtractRotator extends ProjectCommand
{
    public function run(Machine $project): string
    {
        $credentials = $this->extractDbCredentials($project);
        $timeResponse = $project->getMachine()->getSsh()->execMysql(
            "SELECT MAX(expire_time) FROM proxies WHERE is_enabled=1",
            $credentials
        );
        $expireTime = $this->extractLastLine($timeResponse);
        if (strtotime($expireTime)) {
            $countResponse = $project->getMachine()->getSsh()->execMysql(
                "SELECT COUNT(*) FROM proxies WHERE expire_time=\\\"$expireTime\\\" AND is_enabled=1",
                $credentials
            );
            $count = intval($this->extractLastLine($countResponse));
            $project->setRotatorInfo(new \DateTime($expireTime), $count);
            Logger::info('Updated "' . $this->getName() . '" info');
        } else {
            Logger::warning('Ignored not rotator project ' . $project->getName());
        }

        return $expireTime ?? '';
    }
}