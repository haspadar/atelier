<?php

namespace Atelier\Command;

use Atelier\Command;
use Atelier\Debug;
use Atelier\Logger;
use Atelier\Machine;
use Atelier\MachineCommand;
use Atelier\Machine;
use Atelier\ProjectCommand;
use Atelier\Projects;
use Atelier\RotatorFragment;
use Atelier\RotatorFragments;
use http\Url;
use PHPUnit\Event\Runtime\PHP;

class ExtractLogNames extends ProjectCommand
{
    /**
     * sudo vim /etc/logrotate.d/nginx, change to 0655
     *
     * @param Machine $project
     * @return string
     */
    public function run(Machine $project): string
    {
        if ($nginxConfig = $this->getNginxConfig($project)) {
            $project->setNginxConfig($nginxConfig);
            Logger::info('Set "' . $this->getName() . '" nginx_config to ' . $nginxConfig);
            $response = $project->getMachine()->getSsh()->exec("cat $nginxConfig");
            $accessLog = $this->parse($response, 'access_log') ?: '/var/log/nginx/access.log';
            $project->setAccessLog($accessLog);
            Logger::info('Set "' . $this->getName() . '" access_log to ' . $accessLog);
            $errorLog = $this->parse($response, 'error_log') ?: '/var/log/nginx/access.log';
            $project->setErrorLog($accessLog);
            Logger::info('Set "' . $this->getName() . '" error_log to ' . $errorLog);
        } else {
            Logger::warning('Domain nginx file not found for project ' . $project->getName());
        }

        return $nginxConfig;
    }

    private function parse(string $response, string $logKey): string
    {
        $lines = explode(PHP_EOL, $response);
        foreach ($lines as $line) {
            $parts = explode($logKey . ' ', trim($line));
            if (count($parts) > 1) {
                return strtr($parts[1], [';' => '']);
            }
        }

        return '';
    }

    /**
     * @param Machine $project
     * @return string
     */
    private function getNginxConfig(Machine $project): string
    {
        $domainNginxResponse = $project->getMachine()->getSsh()->exec('grep "' . $project->getName() . '" /etc/nginx/sites-enabled/ -R');
        if (!$domainNginxResponse && ($dotsParts = explode('.', $project->getName())) && count($dotsParts) > 1) {
            $starDomainName = implode('.', [
                '*',
                $dotsParts[count($dotsParts) - 2],
                $dotsParts[count($dotsParts) - 1]
            ]);
            $domainNginxResponse = $project->getMachine()->getSsh()->exec('grep "' . $starDomainName . '" /etc/nginx/sites-enabled/ -R');
        }

        return explode(':', $domainNginxResponse)[0];
    }
}