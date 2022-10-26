<?php

namespace Atelier;

use DateTime;

class RotatorFragments
{
    const ROTATOR_KEY = 'ROTATOR_KEY';
    const ROTATOR_URL = 'ROTATOR_URL';

    public static function add(RotatorFragment $fragment, Machine $project)
    {
        Logger::info('Adding rotator fragment (' . $fragment->getPath() . ' for project ' . $project->getName());
        (new Model\RotatorFragments())->add([
            'project_id' => $project->getId(),
            'path' => $fragment->getPath(),
            'field' => $fragment->getField(),
            'fragment' => $fragment->getFragment(),
            'create_time' => (new DateTime())->format('Y-m-d H:i:s')
        ]);
    }

    /**
     * @param Ssh $ssh
     * @param string $directory
     * @return RotatorFragment[]
     */
    public static function findDirectoryFragments(Ssh $ssh, string $directory): array
    {
        $files = [];
        foreach ([self::ROTATOR_KEY, self::ROTATOR_URL] as $field) {
            $command = sprintf(
                "grep -R --include=*.{env,php,pylesos} --exclude-dir={vendor,logs,db,sphinx,\*sitemaps} \"%s\" " . $directory,
                $field
            );
            $response = $ssh->exec($command);
            foreach (explode(PHP_EOL, $response ?? '') as $responseLine) {
                $parts = explode(':', $responseLine);
                $file = $parts ? array_shift($parts) : '';
                $fragment = implode(':', $parts);
                if ($file && !array_key_exists($file, $files)) {
                    $files[] = new RotatorFragment(trim($fragment), $file, $field);
                }
            }
        }

        return $files;
    }

    public static function removeProject(Machine $project)
    {
        Logger::info('Removing project ' . $project->getName() . ' rotator fragments');
        (new Model\RotatorFragments())->removeByProjectId($project->getId());
    }

    /**
     * @param Machine $project
     * @return RotatorFragment[]
     * @throws \Exception
     */
    public static function getByProject(Machine $project): array
    {
        $fragments = [];
        foreach ((new Model\RotatorFragments())->getByProjectId($project->getId()) as $found) {
            $fragments[] = new RotatorFragment(
                $found['fragment'],
                $found['path'],
                $found['field'],
                new DateTime($found['create_time'])
            );
        }

        return $fragments;
    }
}