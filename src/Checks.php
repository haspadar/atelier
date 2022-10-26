<?php

namespace Atelier;

class Checks
{
    public static function run()
    {
        $criticalMessages = self::getCriticalMessages();
//        Critical

//        Не открывалась главная за последние сутки
//        Загрузка главной занимала больше 5 секунд за последние сутки
//        осталось меньше 20% свободного места
//        до окончания прокси осталось меньше суток

//        Warning
//        осталось меньше 30% свободного места
//        Запущены не все миграции
//        Включена не master-ветка
//        Траффик nginx сильно вырос за сутки
//        Траффик php сильно вырос за сутки
//        За сутки не было новых объявлений
//        Тесты не отработали

//        Info
//        Не включен кэш
//        Не совпадает версия php
//        Не совпадает версия mysql
//        Имя nginx log не совпадает с проектом
//        Доступ к сайту закрыт


        $notMasterProjects = self::getNotMasterProjects();
        $oldCommitProjects = self::getOldCommitProjects();
        $oldMigrationProjects = self::getOldMigrationProjects();
        $smokeErrorProjects = self::getSmokeErrorsProjects();

//        last_branch_name=master
//        last_commit_time-разный, сортировка по возрастанию, сравниваю с последним
//        last_migration_name-группировка,сортировка по количеству, сравниваю крайние
//        smoke_last_time-OK
    }

    private static function getCriticalMessages(): array
    {

    }

    /**
     * @param Model\Projects $projects
     * @return Machine[]
     */
    public static function getNotMasterProjects(): array
    {
        return array_map(fn($project) => new Machine($project), (new \Atelier\Model\Projects())->getNotMasterProjects());
    }

    /**
     * @return Machine[]
     */
    public static function getOldCommitProjects(): array
    {
        return array_map(fn($project) => new Machine($project), (new \Atelier\Model\Projects())->getOldCommitProjects());
    }

    /**
     * @return Machine[]
     */
    public static function getOldMigrationProjects(): array
    {
        return array_map(fn($project) => new Machine($project), (new \Atelier\Model\Projects())->getOldMigrationProjects());
    }

    /**
     * @return Machine[]
     */
    public static function getSmokeErrorsProjects(): array
    {
        return array_map(fn($project) => new Machine($project), (new \Atelier\Model\Projects())->getSmokeErrorProjects());
    }

    /**
     * @return Warning[]
     */
//    public static function getWarnings(): array
//    {
//        $types = (new Fittings())->getAll();
//        $warnings = [];
//        foreach ($types as $type) {
//            $class = '\Atelier\Warning\\' . Names::snakeToCamel($type['name']);
//            $warnings[] = new $class($type);
//        }
//
//        return $warnings;
//    }

//    public static function getFittingWarning(int $fittingId): Warning
//    {
//        $type = (new Fittings())->getById($fittingId);
//        $class = '\Atelier\Warning\\' . Names::snakeToCamel($type['name']);
//
//        return new $class($type);
//    }

//    public static function getFitting(int $id): array
//    {
//        return (new Fittings())->getById($id) ?: [];
//    }
}