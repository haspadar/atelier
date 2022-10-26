<?php

namespace Atelier;

class Time
{
    public static function timeHuman(\DateTime $time): string
    {
        if ($time->format('d.m.Y') == (new \DateTime())->format('d.m.Y')) {
            return 'сегодня в ' . $time->format('H:i:s');
        } elseif ($time->format('d.m.Y') == (new \DateTime())->modify('1 DAY')->format('d.m.Y')) {
            return 'вчера в ' . $time->format('H:i:s');
        } elseif ($time->format('d.m.Y') == (new \DateTime())->modify('2 DAY')->format('d.m.Y')) {
            return 'позавчера в ' . $time->format('H:i:s');
        } elseif ($time->format('Y') == (new \DateTime())->format('Y')) {
            return $time->format('d')
                . ' ' . self::getRussianMonth($time->format('m'))
                . ' в ' . $time->format('H:i:s');
        } else {
            return $time->format('d')
                . ' ' . self::getRussianMonth($time->format('m'))
                . ' ' . $time->format('Y')
                . ' в ' . $time->format('H:i:s');
        }
    }

    /**
     * Родительный падеж: за 5 минут 3 секунды
     *
     * @param \DateTime $timeFrom
     * @param \DateTime $timeTo
     * @return string
     */
    public static function diffInGenitive(\DateTime $timeFrom, \DateTime $timeTo): string
    {
        return self::diffHuman($timeFrom, $timeTo, true);
    }

    /**
     * Именительный падеж: 2 минуты 5 секунд
     * @param \DateTime $timeFrom
     * @param \DateTime $timeTo
     * @return string
     */
    public static function diffInNominative(\DateTime $timeFrom, \DateTime $timeTo): string
    {
        return self::diffHuman($timeFrom, $timeTo, false);
    }

    private static function getRussianMonth(int $number): string
    {
        $titles = ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'];

        return $titles[intval($number) - 1] ?? '';
    }

    private static function diffHuman(\DateTime $timeFrom, \DateTime $timeTo, $isGenitive = false): string
    {
        $diff = $timeTo->diff($timeFrom);
        $humans = [];
        if ($diff->y) {
            $humans[] = $diff->y . ' ' . Plural::get($diff->y, 'год', 'года', 'лет');
        }

        if ($diff->m) {
            $humans[] = $diff->m . ' ' . Plural::get($diff->m, 'месяц', 'месяца', 'месяцев');
        }

        if ($diff->days) {
            $humans[] = $diff->days . ' ' . Plural::get($diff->days, 'день', 'дня', 'лет');
        }

        if ($diff->h) {
            $humans[] = $diff->h . ' ' . Plural::get($diff->h, 'час', 'часа', 'часов');
        }

        if ($diff->i) {
            $humans[] = $diff->i . ' ' . Plural::get($diff->i, $isGenitive ? 'минуту' : 'минута', 'минуты', 'минут');
        }

        if ($diff->s) {
            $humans[] = $diff->s . ' ' . Plural::get($diff->s, $isGenitive ? 'секунду' : 'секунда', 'секунды', 'секунд');
        }

        return implode(' ', $humans);
    }

    public static function getDiffHours(\DateTime $big, \DateTime $small): int
    {
        return intval($big->diff($small)->format('%h'));
    }
}