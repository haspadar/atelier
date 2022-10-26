<?php

namespace Atelier\Command;

use Atelier\Debug;
use Atelier\Logger;
use Atelier\Machine;
use Atelier\Project;
use Atelier\ProjectCommand;

class ExtractParserAds extends ProjectCommand
{
    public function run(Project $project): string
    {
        $dbCredentials = $this->extractDbCredentials($project);
        if ($dbCredentials->getDbName()) {
            $sql = "SELECT count(*) FROM ads WHERE create_time >= '"
                . (new \DateTime())->modify('-1 HOUR')->format('Y-m-d H:i:s')
                . "' ORDER BY id DESC";
            $result = $project->getMachine()->getSsh()->execMysql($sql, $dbCredentials);
            $count = intval(explode(PHP_EOL, $result)[1]);
            $project->addParserAdsCount($count);
            Logger::info('Added "' . $this->getName() . '" hour ads count to ' . $count);
        } else {
            Logger::warning('Ignored project ' . $project->getName() . ' without DB config');
        }


        return $count ?? 0;
    }

}