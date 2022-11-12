<?php

namespace Atelier\Command;

use Atelier\Cli;
use Atelier\Command;
use Atelier\Debug;
use Atelier\Logger;
use Atelier\Machine;
use Atelier\MachineCommand;
use Atelier\Project;
use Atelier\ProjectCommand;

class ExtractHttp extends ProjectCommand
{
    public function run(Project $project): string
    {
        list($response, $info) = $this->downloadHeaders($project->getWwwAddress());
        if ($info['http_code'] == 200) {
            $responseHeaders = substr($response, 0, $info['header_size']);
            $nginxCacheHeader = $this->parseCacheHeader($responseHeaders);
        }

        list($response, $info) = $this->download($project->getWwwAddress() . '?hash=' . md5(time()));
        $project->addHttp($info['total_time'], $info['http_code'], $nginxCacheHeader ?? '');
        Logger::info('Updated "' . $this->getName() . '" headers');

        return 'Code ' . $info['http_code'] . ' for ' . $info['total_time'] . ' secs';
    }

    private function parseCacheHeader(string $responseHeaders)
    {
        $lines = array_filter(explode(PHP_EOL, $responseHeaders));
        foreach ($lines as $line) {
            if (str_contains($line, 'x-fastcgi-cache')) {
                $parts = explode(': ', $line);

                return trim($parts[1]);
            }
        }

        return '';
    }
}