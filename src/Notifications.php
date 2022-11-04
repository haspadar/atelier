<?php

namespace Atelier;

use Atelier\Check\Type;
use DateTime;
use Longman\TelegramBot\DB;

class Notifications
{
    public static function generate(): void
    {
        $subscribers = Subscribers::getAll();
        $telegram = new Telegram();
        foreach ($subscribers as $subscriber) {
            $checks = (new Model\Notifications())->getActualChecks(
                $subscriber['chat_id'],
                (new \DateTime())->modify('-1 MONTH')->format('Y-m-d H:i:s')
            );
            if ($checks) {
                $types = explode(',', $subscriber['check_types']);
                foreach (self::groupByType($checks) as $type => $typeChecks) {
                    if (in_array($type, $types)) {
                        $message = self::generateMessage($type, $typeChecks);
                        Logger::info('Message ' . $message);
                        $response = $telegram->sendMessage($message, $subscriber['chat_id']);
                        Logger::info('Response: ' . var_export($response, true));
                        if ($response['ok']) {
                            $now = new DateTime();
                            foreach ($typeChecks as $typeCheck) {
                                (new Model\Notifications())->add([
                                    'check_id' => $typeCheck->getId(),
                                    'contact' => $subscriber['chat_id'],
                                    'create_time' => $now->format('Y-m-d H:i:s')
                                ]);
                            }

                            Logger::info('Sent ' . $type . ' telegram to ' . $subscriber['first_name']);
                        } else {
                            Logger::error($response['description']);
                        }
                    } else {
                        Logger::warning('Ignored ' . $type . ' type for ' . $subscriber['first_name']);
                    }

                }
            } else {
                Logger::debug('No actual checks for ' . $subscriber['first_name']);
            }

        }
    }

    private static function generateMessage(string $type, array $checks): string
    {
        $subject = self::generateSummarySubject($type, $checks);
        $url = sprintf('<a href="%s">Перейти на сайт</a>', self::generateUrl($type));
        $groupTitles = self::getGroupTitles($checks);
        $list = implode('', array_map(
            fn($name) => '<li>' . $name . '</li>',
            array_slice($groupTitles, 0, 5)
        ));

        return $subject . ':<br><ul>' . $list . (count($groupTitles) > 5 ? '<li>и др.</li>' : '') . '</ul>' . $url;
    }

    /**
     * @param array $checks
     * @return Check[]
     */
    private static function getGroupTitles(array $checks): array
    {
        $names = [];
        foreach ($checks as $check) {
            $names[] = $check->getGroupTitle();
        }

        return array_unique($names);
    }

    private static function generateUrl(string $type): string
    {
        if ($type == Type::CRITICAL->name) {
            return 'https://atelier.palto.name/checks#CRITICAL';
        }

        if ($type == Type::WARNING->name) {
            return 'https://atelier.palto.name/checks#WARNING';
        }

        if ($type == Type::INFO->name) {
            return 'https://atelier.palto.name/checks#INFO';
        }
    }

    private static function generateSummarySubject(string $type, array $checks): string
    {
        if ($type == Type::CRITICAL->name) {
            return count($checks)
                . ' '
                . Plural::get(
                    count($checks),
                    'важное сообщение',
                    'важных сообщения',
                    'важных сообщений'
                );
        }

        if ($type == Type::WARNING) {
            return count($checks)
                . ' '
                . Plural::get(
                    count($checks),
                    'уведомление',
                    'уведомления',
                    'уведомлений'
                );
        }

        return count($checks)
            . ' '
            . Plural::get(
                count($checks),
                'рекомендация',
                'рекомендации',
                'рекомендаций'
            );
    }

    private static function generateSummaryBody(array $checks): string
    {
        $summary = '<ol>';
        foreach ($checks as $check) {
            $summary .= '<li>' . $check['title'] . '<br>' . $check['text'] . '</li>';
        }

        return $summary . '</ol>';
    }

    private static function groupByType(array $checks): array
    {
        $grouped = [];
        foreach ($checks as $check) {
            $grouped[$check['type']][] = $check;
        }

        return $grouped;
    }
}