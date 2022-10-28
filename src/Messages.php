<?php

namespace Atelier;

use Atelier\Message\Type;
use Atelier\Model\HttpInfo;
use Atelier\Model\NginxTraffic;
use Atelier\Model\Parser;
use Atelier\Model\PhpFpmTraffic;
use Atelier\Model\Rotator;
use DateTime;

class Messages
{
    public static function generate(): void
    {
        self::generateCritical();
        self::generateWarning();
        self::generateInfo();
    }

    private static function generateCritical(): void
    {
        self::checkRotator(1, Type::CRITICAL);
        foreach (Machines::getMachines() as $machine) {
            self::checkFreeSpace($machine, 15, Type::CRITICAL);
        }

        foreach (Projects::getProjects() as $project) {
            self::checkHttpCode($project, (new DateTime())->modify('-1 DAY'), Type::CRITICAL);
            self::checkHttpSeconds($project, (new DateTime())->modify('-1 DAY'), 10, Type::CRITICAL);
        }
    }

    private static function generateWarning(): void
    {
        self::checkRotator(2, Type::WARNING);
        foreach (Machines::getMachines() as $machine) {
            self::checkFreeSpace($machine, 30, Type::WARNING);
            self::checkPhpFpmTraffic($machine, Type::WARNING);
        }

        foreach (Projects::getProjects() as $project) {
            self::checkHttpSeconds($project, (new DateTime())->modify('-1 DAY'), 5, Type::WARNING);
            self::checkMigrations($project, Type::WARNING);
            self::checkBranch($project, Type::WARNING);
            self::checkCommit($project, Type::WARNING);
            self::checkNginxTraffic($project, Type::WARNING);
            self::checkParserAds($project, (new DateTime())->modify('-1 DAY'), Type::WARNING);
            self::checkSmoke($project, Type::WARNING);
        }
    }

    private static function generateInfo(): void
    {
        foreach (Machines::getMachines() as $machine) {
            self::checkPhp($machine, Type::INFO);
            self::checkMysql($machine, Type::INFO);
        }

        foreach (Projects::getProjects() as $project) {
            self::checkCache($project, Type::INFO);
            self::checkNginxLogs($project, Type::INFO);
            self::checkHttpAccess($project, Type::INFO);
        }
    }

    private static function checkRotator(int $daysCount, Type $type): void
    {
        if ($rotatorInfo = (new Rotator())->getFirst()) {
            $expireTime = new DateTime($rotatorInfo['expire_time']);
            if ($expireTime->modify("-$daysCount day") < new DateTime()) {
                $project = Projects::getRotatorProject();
                $hoursCount = Time::getDiffHours(new DateTime(), $expireTime);
                self::add([
                    'title' => 'Заканчиваются прокси',
                    'text' => Plural::get($hoursCount, 'Остался ', 'Осталось ', 'Осталось ')
                        . $hoursCount
                        . 'ч для ' . $rotatorInfo['count']
                        . ' прокси',
                    'project_id' => $project->getId(),
                ], $type);
            } else {
                Logger::debug('Ignored expire time ' . $expireTime->format('Y-m-d H:i:s'));
            }
        }
    }

    private static function add(array $message, Type $type): void
    {
        $message['create_time'] = (new DateTime())->format('Y-m-d H:i:s');
        $message['type'] = $type->name;
        $now = new DateTime();
        if (!(new Model\Messages())->getBetween(
            ($type == Type::CRITICAL
                ? $now->modify('-1 DAY')
                : ($type == Type::WARNING
                    ? $now->modify('-1 WEEK')
                    : $now->modify('-1 MONTH')
                )
            )->format('Y-m-d H:i:s'),
            $now->format('Y-m-d H:i:s'),
            $message['machine_id'] ?? null,
            $message['project_id'] ?? null,
            $message['type'],
            $message['title']
        )) {
            (new Model\Messages())->add($message);
            Logger::info('Added ' . $type->name . ' message "' . $message['title'] . '"');
        } else {
            Logger::warning('Ignored exists ' . $type->name . ' message "' . $message['title'] . '"');
        }
    }

    private static function checkSmoke(Project $project, Type $type): void
    {
        if ($project->getSmokeLastReport() != 'OK') {
            self::add([
                'title' => 'Тесты отработали с ошибкой на ' . $project->getName(),
                'text' => 'На сайте <a href="' . $project->getAddress() . '" target="_blank">'
                    . $project->getName()
                    . '</a> ошибка: "' . $project->getSmokeLastReport() . '".',
                'project_id' => $project->getId(),
            ], $type);
        } else {
            Logger::debug('Smoke is OK');
        }
    }

    private static function checkParserAds(Project $project, DateTime $fromTime, Type $type): void
    {
        $parsed = (new Parser())->getForPeriod($project->getId(), $fromTime->format('Y-m-d H:i:s'));
        $withAds = array_filter($parsed, fn($row) => $row['hour_ads_count'] > 0);
        if (!$withAds) {
            $withAdsLastTime = (new Parser())->getWithAdsLastTime($project->getId());
            $now = new DateTime();
            self::add([
                'title' => 'Не парсятся объявления на ' . $project->getName(),
                'text' => 'На сайте <a href="' . $project->getAddress() . '" target="_blank">'
                    . $project->getName()
                    . '</a> за последние '
                    . Time::getDiffHours($now, $fromTime)
                    . 'ч не было свежих объявлений. '
                    .  ($withAdsLastTime
                        ? 'Новые объявления были ' . Time::getDiffHours($now, new DateTime($withAdsLastTime)) . 'ч назад'
                        : ''
                    ),
                'project_id' => $project->getId(),
            ], $type);
        } else {
            Logger::debug('Has parsed ads for last day');
        }
    }

    private static function checkPhpFpmTraffic(Machine $machine, Type $type): void
    {
        $yesterdayTraffic = (new PhpFpmTraffic())->getForDate(
            $machine->getId(),
            (new DateTime())->modify('-1 DAY')->format('Y-m-d')
        );
        $todayTraffic = (new PhpFpmTraffic())->getForDate(
            $machine->getId(),
            (new DateTime())->format('Y-m-d')
        );
        $lastTraffic = (new PhpFpmTraffic())->getLastTraffic($machine->getId());
        $text = self::generateTrafficText($yesterdayTraffic, $todayTraffic, $lastTraffic);
        if ($text) {
            self::add([
                'title' => 'Вырос php-трафик на ' . $machine->getName(),
                'text' => sprintf(
                    $text,
                    'на сайте <a href="' . $machine->getAddress() . '" target="_blank">'
                    . $machine->getName()
                    . '</a>'
                ),
                'project_id' => $machine->getId(),
            ], $type);
        } else {
            Logger::debug('Nginx traffic is normal');
        }
    }

    private static function checkNginxTraffic(Project $project, Type $type): void
    {
        $yesterdayTraffic = (new NginxTraffic())->getForDate(
            $project->getId(),
            (new DateTime())->modify('-1 DAY')->format('Y-m-d')
        );
        $todayTraffic = (new NginxTraffic())->getForDate(
            $project->getId(),
            (new DateTime())->format('Y-m-d')
        );
        $lastTraffic = (new NginxTraffic())->getLastTraffic($project->getId());
        $text = self::generateTrafficText($yesterdayTraffic, $todayTraffic, $lastTraffic);
        if ($text) {
            self::add([
                'title' => 'Вырос nginx-трафик на ' . $project->getName(),
                'text' => sprintf(
                    $text,
                    'на сайте <a href="' . $project->getAddress() . '" target="_blank">'
                        . $project->getName()
                        . '</a>'
                ),
                'project_id' => $project->getId(),
            ], $type);
        } else {
            Logger::debug('Nginx traffic is normal');
        }
    }

    private static function checkNginxLogs(Project $project, Type $type): void
    {
        $generatedAccessLog = '/var/log/nginx/' . str_replace('.', '_', $project->getName()) . '_access.log';
        $generatedErrorLog = '/var/log/nginx/' . str_replace('.', '_', $project->getName()) . '_error.log';
        if ($project->getNginxAccessLog() != $generatedAccessLog
            || $project->getNginxErrorLog() != $generatedErrorLog
        ) {
            self::add([
                'title' => 'Неправильные имена nginx-логов на ' . $project->getName(),
                'text' => 'На сайте <a href="' . $project->getAddress() . '" target="_blank">'
                    . $project->getName()
                    . '</a> неправильные имена логов ('
                    . $project->getNginxAccessLog()
                    . ', '
                    . $project->getNginxErrorLog()
                    . ')',
                'project_id' => $project->getId(),
            ], $type);
        } else {
            Logger::debug('Nginx log names is correct');
        }
    }

    private static function checkHttpAccess(Project $project, Type $type): void
    {
        $http = (new HttpInfo())->getLast($project->getId());
        if (!$http['http_code'] == 403) {
            self::add([
                'title' => 'Сайт ' . $project->getName() . ' закрыт паролем',
                'text' => 'Сайт <a href="' . $project->getAddress() . '" target="_blank">'
                    . $project->getName()
                    . '</a> закрыт паролем',
                'project_id' => $project->getId(),
            ], $type);
        } else {
            Logger::debug('Site is open');
        }
    }

    private static function checkCache(Project $project, Type $type): void
    {
        $http = (new HttpInfo())->getLast($project->getId());
        if (!$http['cache_header']) {
            self::add([
                'title' => 'Не включён кэш на ' . $project->getName(),
                'text' => 'На сайте <a href="' . $project->getAddress() . '" target="_blank">'
                    . $project->getName()
                    . '</a> не включён кэш',
                'project_id' => $project->getId(),
            ], $type);
        } else {
            Logger::debug('Cache header exists');
        }
    }

    private static function checkMysql(Machine $machine, Type $type): void
    {
        $lastPhpVersion = (new \Atelier\Model\Machines())->getLastMysqlVersion();
        if ($machine->getMysqlVersion() != $lastPhpVersion) {
            self::add([
                'title' => 'Обновите Mysql на ' . $machine->getHost(),
                'text' => 'На машине '
                    . $machine->getHost()
                    . ' ('
                    . $machine->getIp()
                    . ') старая версия Mysql: '
                    . $machine->getMysqlVersion(),
                'machine_id' => $machine->getId(),
            ], $type);
        } else {
            Logger::debug('Mysql version is last');
        }
    }

    private static function checkPhp(Machine $machine, Type $type): void
    {
        $lastPhpVersion = (new \Atelier\Model\Machines())->getLastPhpVersion();
        if ($machine->getPhpVersion() != $lastPhpVersion) {
            self::add([
                'title' => 'Обновите Php на ' . $machine->getHost(),
                'text' => 'На машине '
                    . $machine->getHost()
                    . ' ('
                    . $machine->getIp()
                    . ') старая версия Php: '
                    . $machine->getPhpVersion(),
                'machine_id' => $machine->getId(),
            ], $type);
        } else {
            Logger::debug('Php version is last');
        }
    }

    private static function checkFreeSpace(Machine $machine, int $percent, Type $type): void
    {
        if ($machine->getFreeSpace() <= $percent) {
            self::add([
                'title' => 'Заканчивается место на ' . $machine->getHost(),
                'text' => 'На машине '
                    . $machine->getHost()
                    . ' ('
                    . $machine->getIp()
                    . ') осталось '
                    . $machine->getFreeSpace()
                    . '% свободного места',
                'machine_id' => $machine->getId(),
            ], $type);
        } else {
            Logger::debug('Ignored free space ' . $machine->getFreeSpace());
        }
    }

    private static function checkCommit(Project $project, Type $type): void
    {
        $lastCommitTime = Projects::getLastCommitTime();
        if ($project->getLastCommitTime() != $lastCommitTime) {
            self::add([
                'title' => 'Не обновился код на ' . $project->getName(),
                'text' => 'На сайте <a href="' . $project->getAddress() . '" target="_blank">'
                    . $project->getName()
                    . '</a> код отстаёт от последнего коммита',
                'project_id' => $project->getId(),
            ], $type);
        } else {
            Logger::debug('Last commit time is actual');
        }
    }

    private static function checkBranch(Project $project, Type $type): void
    {
        if (!in_array($project->getLastBranchName(), ['master', 'main'])) {
            self::add([
                'title' => 'Включена ветка ' . $project->getLastBranchName() . ' на ' . $project->getName(),
                'text' => 'На сайте <a href="' . $project->getAddress() . '" target="_blank">'
                    . $project->getName()
                    . '</a> включена ветка ' . $project->getLastBranchName(),
                'project_id' => $project->getId(),
            ], $type);
        } else {
            Logger::debug('Branch is master');
        }
    }

    private static function checkMigrations(Project $project, Type $type): void
    {
        $lastMigrationName = Projects::getLastMigrationName();
        if ($project->getLastMigrationName() != $lastMigrationName) {
            self::add([
                'title' => 'Не загрузилась последняя миграция на ' . $project->getName(),
                'text' => 'На сайте <a href="' . $project->getAddress() . '" target="_blank">'
                    . $project->getName()
                    . '</a> загрузились не все миграции',
                'project_id' => $project->getId(),
            ], $type);
        } else {
            Logger::debug('Last migration is actual');
        }
    }

    private static function checkHttpSeconds(Project $project, DateTime $fromTime, int $seconds, Type $type): void
    {
        $https = (new HttpInfo())->getForPeriod($project->getId(), $fromTime->format('Y-m-d H:i:s'));
        $longHttps = array_filter($https, fn($http) => $http['seconds'] >= $seconds);
        if ($longHttps) {
            $isLongHttp = $https[count($https) - 1]['seconds'] >= $seconds;
            self::add([
                'title' => $project->getName() . ($isLongHttp ? ' тормозит' : ' тормозил'),
                'text' => 'Сайт <a href="' . $project->getAddress() . '" target="_blank">'
                    . $project->getName()
                    . '</a> тормозил '
                    . self::groupTimes(array_column($longHttps, 'create_time')),
                'project_id' => $project->getId(),
            ], $type);
        } else {
            Logger::debug('Not found long http requests (more than ' . $seconds . ' seconds)');
        }
    }

    private static function checkHttpCode(Project $project, DateTime $fromTime, Type $type): void
    {
        $https = (new HttpInfo())->getForPeriod($project->getId(), $fromTime->format('Y-m-d H:i:s'));
        $notSuccessHttps = array_filter($https, fn($http) => $http['http_code'] != 200);
        if ($notSuccessHttps) {
            $isOffline = $https[count($https) - 1]['http_code'] != 200;
            self::add([
                'title' => $project->getName() . ($isOffline ? ' недоступен' : ' был недоступен'),
                'text' => 'Сайт <a href="' . $project->getAddress() . '" target="_blank">'
                    . $project->getName()
                    . '</a> был недоступен '
                    . self::groupTimes(array_column($notSuccessHttps, 'create_time')),
                'project_id' => $project->getId(),
            ], $type);
        } else {
            Logger::debug('Not found not success http requests');
        }
    }

    private static function groupTimes(array $times): string
    {
        $groupedTimes = [];
        foreach ($times as $time) {
            $isToday = (new DateTime($time))->format('Y-m-d') == (new DateTime())->format('Y-m-d');
            $isYesterday = (new DateTime($time))->format('Y-m-d')
                == (new DateTime())->modify('-1 DAY')->format('Y-m-d');
            $key = $isToday ? 'сегодня' : ($isYesterday ? 'вчера' : (new DateTime($time))->format('d.m.Y'));
            $groupedTimes[$key][] = (new DateTime())->format('H:i');
        }

        $groupedTimesLine = [];
        foreach ($groupedTimes as $day => $dayGroupedTimes) {
            $groupedTimesLine[] = $day . ' в ' . implode(', ', $dayGroupedTimes);
        }

        return implode(', ', $groupedTimesLine);
    }

    private static function generateTrafficText(array $yesterdayTraffic, array $todayTraffic, string $lastTraffic): string
    {
        if ($yesterdayTraffic['max_traffic'] > 0
            && $lastTraffic / $yesterdayTraffic['max_traffic'] >= 2
        ) {
            $text = 'Прямо сейчас %s трафик вырос в '
                . bcdiv($lastTraffic, $yesterdayTraffic['max_traffic'], 2)
                . ' по сравнению с максимальным вчерашним';
        } elseif ($yesterdayTraffic['max_traffic'] > 0
            && $todayTraffic['max_traffic'] / $yesterdayTraffic['max_traffic'] >= 2
        ) {
            $text = 'Сегодня %s максимальный трафик вырос в '
                . bcdiv($lastTraffic, $yesterdayTraffic['max_traffic'], 2)
                . ' по сравнению с максимальным вчерашним';
        } elseif ($yesterdayTraffic['avg_traffic'] > 0
            && $todayTraffic['avg_traffic'] / $yesterdayTraffic['avg_traffic'] >= 2
        ) {
            $text = 'Сегодня %s средний трафик вырос в '
                . bcdiv($lastTraffic, $yesterdayTraffic['max_traffic'], 2)
                . ' по сравнению со средним вчерашним';
        }

        return $text ?? '';
    }
}