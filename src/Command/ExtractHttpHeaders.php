<?php

namespace Atelier\Command;

use Atelier\Command;
use Atelier\Debug;
use Atelier\Logger;
use Atelier\Project;
use Atelier\ProjectCommand;
use Atelier\RotatorFragment;
use Atelier\RotatorFragments;

class ExtractHttpHeaders extends ProjectCommand
{
    public function run(Project $project): string
    {
//      Attempt for cache generate
        $this->download($project->getAddress());
        list($response, $info) = $this->download($project->getAddress());
        if ($info['http_code'] == 200) {
            $responseHeaders = substr($response, 0, $info['header_size']);
            $nginxCacheHeader = $this->parse($responseHeaders);
        }

        $project->setHttpHeaders($info['http_code'], $nginxCacheHeader ?? '', new \DateTime());
        Logger::info('Updated "' . $this->getName() . '" header code to ' . $info['http_code']);

        return $info['http_code'] . ', ' . ($nginxCacheHeader ?? '');
    }

    private function parse(string $responseHeaders)
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