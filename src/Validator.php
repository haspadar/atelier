<?php

namespace Atelier;

class Validator
{
    public static function validateMoveAd(
        int $categoryLevel1Id,
        string $newCategoryLevel1Title,
        string $newCategoryLevel2Title
    ): string {
        $error = '';
        if ($newCategoryLevel1Title && Categories::getByTitle($newCategoryLevel1Title)) {
            $error = 'Такая категория уже есть';
        } elseif ($newCategoryLevel2Title && Categories::getByTitle($newCategoryLevel2Title, $categoryLevel1Id)) {
            $error = 'Такая категория уже есть';
        }

        return $error;
    }

    public static function isUrlValid(string $url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }

    public static function isEmailValid(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function isDateValid(string $date, string $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }

    public static function validateSynonyms(array $synonyms): string
    {
        foreach ($synonyms as $synonym) {
            if ($found = Synonyms::getByTitle($synonym)) {
                return 'Синоним ' . $synonym . ' уже есть у категории ' . $found->getCategory()->getTitle();
            }
        }

        return '';
    }

    public static function validateMachine(string $host, string $ip, int $excludeId = 0): array
    {
        $errors = [];
        if (!$host) {
            $errors['host'] = 'Укажите название';
        } elseif (($found = Machines::getMachineByHost($host)) && $found->getId() != $excludeId) {
            $errors['host'] = 'Машина с таким названием уже есть';
        }

        if (!$ip) {
            $errors['ip'] = 'Укажите ip';
        } elseif (!filter_var($ip, FILTER_VALIDATE_IP)) {
            $errors['ip'] = 'Неверный формат ip';
        } elseif (($found = Machines::getMachineByIp($host)) && $found->getId() != $excludeId) {
            $errors['ip'] = 'Машина с таким ip уже есть';
        }

        return $errors;
    }
}